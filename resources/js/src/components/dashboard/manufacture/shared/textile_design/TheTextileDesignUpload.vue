<template>
    <div class="max-w-[750px] mx-auto my-8 flex flex-col gap-6 font-sans">
        <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-50 border-b border-slate-200">
                <h2 class="m-0 text-slate-800 text-xl font-bold">Загрузка конструкторской документации</h2>
                <p class="m-0 mt-1 text-slate-500 text-sm">Введите номер КД и выберите или перетащите PDF-файл для привязки</p>
            </div>

            <div class="p-6">
                <div class="mb-5">
                    <label for="kdch-input" class="block mb-2 font-semibold text-slate-700 text-sm">
                        Номер документации (КД):
                    </label>
                    <input
                        id="kdch-input"
                        v-model.trim="kdch"
                        :disabled="isUploading"
                        class="w-full压 w-full padding-4 px-4 py-3 text-base rounded-md border border-slate-300 text-slate-800 box-border transition-colors focus:outline-none focus:border-blue-500 focus:ring-3 focus:ring-blue-500/10 disabled:opacity-60 disabled:cursor-not-allowed"
                        placeholder="Например: 11033"
                        type="text"
                    />
                </div>

                <div class="mb-5">
                    <label class="block mb-2 font-semibold text-slate-700 text-sm">Файл чертежа (PDF):</label>

                    <div
                        :class="[
                            isUploading ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer',
                            selectedFile
                                ? 'border-solid border-emerald-500 bg-emerald-50/50'
                                : isDragOver
                                    ? 'border-solid border-blue-500 bg-blue-50'
                                    : 'border-dashed border-slate-300 bg-slate-50/50 hover:border-blue-500 hover:bg-blue-50/50'
                        ]"
                        class="border-2 rounded-lg p-8 text-center transition-all duration-200 ease-in-out"
                        @click="triggerFileInput"
                        @dragover.prevent="onDragOver"
                        @dragenter.prevent="onDragEnter"
                        @dragleave.prevent="onDragLeave"
                        @drop.prevent="onFileDrop"
                    >
                        <input
                            ref="fileInputRef"
                            :disabled="isUploading"
                            accept="application/pdf"
                            class="hidden"
                            type="file"
                            @change="onFileSelect"
                        />

                        <div v-if="!selectedFile" class="select-none">
                            <span class="text-4xl block mb-2">📂</span>
                            <p class="text-slate-600 font-medium">
                                {{ isDragOver ? 'Бросайте файл сюда!' : 'Нажмите или перетащите PDF-файл сюда' }}
                            </p>
                        </div>

                        <div v-else class="flex flex-col items-center">
                            <span class="text-4xl block mb-2">📄</span>
                            <p class="font-semibold text-slate-900 my-1 break-all">{{ selectedFile.name }}</p>
                            <p class="text-xs text-slate-500 m-0 mb-4">{{ formatBytes(selectedFile.size) }}</p>
                            <button
                                :disabled="isUploading"
                                class="bg-red-500 hover:bg-red-600 text-white border-none px-4 py-1.5 rounded text-xs font-medium cursor-pointer transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                type="button"
                                @click.stop="resetFile"
                            >
                                Удалить выбор
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        class="w-full p-3.5 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-md border-none text-base cursor-pointer transition-colors"
                        @click="$router.push({name: 'manufacture.textile.design.show'})"
                    >
                        <span>К списку КДЧ</span>
                    </button>
                    <button
                        :disabled="!isValid || isUploading"
                        class="w-full p-3.5 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-md border-none text-base cursor-pointer transition-colors disabled:bg-slate-400 disabled:cursor-not-allowed"
                        @click="uploadDocument"
                    >
                        <span v-if="isUploading">⏳ Загрузка на сервер...</span>
                        <span v-else>🚀 Привязать документ</span>
                    </button>
                </div>
            </div>
        </div>

        <div v-if="previewUrl" class="bg-white rounded-xl shadow-md border border-blue-300 overflow-hidden">
            <div class="p-6 bg-blue-50/50 border-b border-blue-200 flex justify-between items-center">
                <h3 class="m-0 text-slate-800 text-lg font-bold">Предпросмотр выбранного файла</h3>
                <span class="bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full text-xs font-semibold">Локальный плеер</span>
            </div>
            <div class="p-0">
                <div class="bg-[#525659] flex justify-center">
                    <embed
                        :src="previewUrl"
                        height="550px"
                        type="application/pdf"
                        width="100%"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- __ Вывод сообщений -->
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />
</template>

<script lang="ts" setup>
import { ref, computed, onUnmounted } from 'vue'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import { useSharedStore } from '@/stores/SharedStore.ts'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'

const sharedStore = useSharedStore()

// Состояние формы
const kdch         = ref('')
const selectedFile = ref<File | null>(null)
const previewUrl   = ref<string | null>(null)
const isUploading  = ref(false)
const isDragOver   = ref(false) // Состояние перетаскивания

const fileInputRef = ref<HTMLInputElement | null>(null)

const isValid = computed(() => {
    return kdch.value.length > 0 && selectedFile.value !== null
})

const triggerFileInput = () => {
    if (!isUploading.value && fileInputRef.value) {
        fileInputRef.value.click()
    }
}

// Универсальный валидатор файла
const processFile = async (file: File | undefined) => {
    if (!file) return

    if (file.type !== 'application/pdf') {
        await showError(['Пожалуйста, выберите файл в формате PDF'])
        resetFile()
        return
    }

    selectedFile.value = file

    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value)
    }
    previewUrl.value = URL.createObjectURL(file)
}

// Обработка клика на инпут
const onFileSelect = (event: Event) => {
    if (!event.target) return
    const target = event.target as HTMLInputElement
    processFile(target.files?.[0])
}

// Логика Drag and Drop событий
const onDragOver = () => {
    if (!isUploading.value) isDragOver.value = true
}

const onDragEnter = () => {
    if (!isUploading.value) isDragOver.value = true
}

const onDragLeave = () => {
    isDragOver.value = false
}

const onFileDrop = (event: DragEvent) => {
    isDragOver.value = false
    if (isUploading.value) return

    const file = event.dataTransfer?.files?.[0]
    processFile(file)
}

const resetFile = () => {
    selectedFile.value = null
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value)
        previewUrl.value = null
    }
    if (fileInputRef.value) {
        fileInputRef.value.value = ''
    }
}

const uploadDocument = async () => {
    if (!isValid.value || isUploading.value) return

    if (!selectedFile.value) {
        await showError(['Файл не выбран!'])
        return
    }

    const findExistItem = await sharedStore.getTextileDesignDocumentByKdch(kdch.value)
    if (findExistItem) {
        await showError([
            `КДЧ ${kdch.value} уже загружено.`,
            'Для обновления этой документации сначала',
            'нужно его удалить!'
        ])
        return
    }

    isUploading.value = true

    const formData = new FormData()
    formData.append('kdch', kdch.value)
    formData.append('file', selectedFile.value)

    const result = await sharedStore.uploadTextileDesignDocument(formData)

    if (!checkCRUD(result)) {
        await showError()
    } else {
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'
        modalInfoText.value = result.payload
        await appModalAsyncMultilineTS.value!.show()
    }

    kdch.value = ''
    resetFile()
    isUploading.value = false
}

const formatBytes = (bytes: number, decimals = 2) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const dm = decimals < 0 ? 0 : decimals
    const sizes = ['Bytes', 'KB', 'MB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i]
}

const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null)

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

onUnmounted(() => {
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value)
    }
})
</script>
