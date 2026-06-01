<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal" class="dark-container">
                <div :class="[width, height, borderColor, 'modal-container p-4']">

                    <div class="flex justify-between items-center w-full mb-2 h-auto">
                        <h3 :class="[currentTextColor, 'font-semibold text-lg']">
                            Просмотр документации КДЧ №{{ doc?.kdch || '' }}
                        </h3>
                        <AppInputButton
                            id="close"
                            :type="type"
                            height="w-5"
                            title="x"
                            width="w-[30px]"
                            @buttonClick="select(false)"
                        />
                    </div>

                    <div class="w-full flex-grow relative bg-slate-900 rounded-lg overflow-hidden flex justify-center items-center">

                        <div v-if="isLoading" class="flex flex-col items-center gap-2 animate-pulse">
                            <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" fill="currentColor"></path>
                            </svg>
                            <span :class="[currentTextColor]">Загрузка документа...</span>
                        </div>

                        <div v-else-if="hasError || !pdfUrl" class="flex flex-col items-center p-4 text-center gap-2">
                            <span class="text-3xl">⚠️</span>
                            <span class="text-white font-medium">{{ errorMessage }}</span>
                        </div>

                        <iframe
                            v-else
                            :src="pdfUrl"
                            class="w-full h-full border-none"
                            type="application/pdf"
                            @load="onIframeLoad"
                        />
                    </div>

                    <div class="w-full h-auto flex justify-end gap-2 mt-4">
                        <div v-if="mode === 'confirm'">
                            <AppInputButton
                                id="confirm"
                                :type="type"
                                title="Да"
                                @buttonClick="select(true)"
                            />
                        </div>

                        <div>
                            <AppInputButton
                                id="cancel"
                                :title="mode === 'confirm' ? 'Отмена' : okWord"
                                :type="type"
                                @buttonClick="select(false)"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />
</template>

<script lang="ts" setup>
import { computed, ref, watch, onUnmounted } from 'vue'

import type { IColorTypes, ITextileDocument } from '@/types'
import { getColorClassByType, getTextColorClassByType } from '@/app/helpers/helpers.js'
import { useSharedStore } from '@/stores/SharedStore.ts'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'

interface IProps {
    type?: IColorTypes
    width?: string
    height?: string
    mode?: 'inform' | 'confirm'
    okWord?: string
    doc: ITextileDocument | null | undefined
}

const props = withDefaults(defineProps<IProps>(), {
    type  : 'dark',
    width : 'w-[95vw]',
    height: 'h-[95vh]',
    mode  : 'inform',
    okWord: 'Закрыть',
})

const emits = defineEmits<{
    (e: 'loaded', url: string): void
    (e: 'error', error: string): void
}>()

const sharedStore = useSharedStore()

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Показываем сообщение об ошибке
async function showError(error: string | string[] | null = null) {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'

    let renderError = ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    if (typeof error === 'string' && error.length > 0) {
        renderError = [error]
    } else if (Array.isArray(error) && error.length > 0) {
        renderError = error
    }

    modalInfoText.value = renderError
    await appModalAsyncMultilineTS.value!.show()
}

const currentColorIndex = 500
const borderColor       = computed(() => getColorClassByType(props.type, 'border', currentColorIndex))
const currentTextColor  = computed(() => getTextColorClassByType(props.type))

// __ Асинхронные состояния
const pdfUrl       = ref<string | null>(null)
const isLoading    = ref<boolean>(false)
const hasError     = ref<boolean>(false)
const errorMessage = ref<string>('Документ отсутствует')

const cleanUpPdfUrl = () => {
    if (pdfUrl.value) {
        URL.revokeObjectURL(pdfUrl.value)
        pdfUrl.value = null
    }
}

// __ Основная функция асинхронной загрузки PDF в Blob
const loadPdfDocument = async () => {
    const docData = props.doc

    // КРИТИЧЕСКИЙ ШАГ: Сначала полностью очищаем старый URL и ошибки
    cleanUpPdfUrl()
    hasError.value  = false

    // Включаем лоадер. Так как pdfUrl теперь равен null,
    // iframe скроется, и отрендерится блок "Загрузка документа..."
    isLoading.value = true

    const result = await sharedStore.getTextileDesignDocumentByIdBlob(docData!.id)

    if (checkCRUD(result)) {

        const binaryData = result.data || result
        const blob       = new Blob([binaryData], { type: 'application/pdf' })

        pdfUrl.value     = URL.createObjectURL(blob)
        emits('loaded', pdfUrl.value)
    } else {
        cleanUpPdfUrl()
        // await showError()
    }

    isLoading.value = false
}

const onIframeLoad = () => {
    // PDF успешно отрисовался в iframe
}

// __ Логика Promises для управления вызовом модалки
const showModal = ref(false)
let resolvePromise: ((value: boolean) => void) | null = null

const show = () => {
    // Сбрасываем флаги в дефолт, чтобы старые состояния не всплывали при открытии
    isLoading.value = false
    hasError.value  = false

    showModal.value = true
    return new Promise<boolean>((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: boolean) => {
    if (resolvePromise) {
        resolvePromise(value)
        showModal.value = false
        resolvePromise  = null
        cleanUpPdfUrl() // Чистим память при закрытии окна
    }
}


defineExpose({
    show,
})

watch(() => props.doc?.id, async () => {
    console.log(props.doc)
    const docData = props.doc
    if (!docData || !docData.id /*|| !docData.file_path*/) {
        cleanUpPdfUrl()
        // await showError('КДЧ не привязана к этой строке')
        // hasError.value     = true
        // errorMessage.value = 'Чертеж КД не привязан к этой строке'

        // __ Выходим
        select(false)
        return
    }

    await loadPdfDocument()
})

onUnmounted(() => {
    cleanUpPdfUrl()
})


</script>

<style scoped>
.dark-container {
    @apply z-[999] bg-slate-900 bg-opacity-80 fixed w-screen h-screen top-0 left-0 flex justify-center items-center backdrop-blur-sm;
}

.modal-container {
    @apply bg-slate-800 rounded-xl flex flex-col justify-between border-l-8;
}
</style>
