<template>
    <div
        class="min-h-[800px] w-full"
        @dragover.prevent
        @drop.prevent
    >


        <div class="w-[800px] mx-auto p-4">

            <div
                v-if="!selectedFile || !checkFile  /*!selectedFile && !isUploading*/"
                class="relative"
                @dragover.prevent="isDragging = true"
                @dragenter.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="onFileDrop"


            >
                <label
                    :class="[
                        'flex flex-col items-center justify-center w-full h-64 border-2 border-dashed rounded-2xl transition-all duration-300 cursor-pointer',
                        isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-slate-300 bg-slate-50 hover:bg-slate-100'
                    ]"
                >
                    <div class="flex flex-col items-center justify-center pt-5 pb-6 pointer-events-none">
                        <div
                            class="p-4 bg-indigo-100 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg
                                class="w-8 h-8 text-indigo-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                />
                            </svg>
                        </div>
                        <p class="mb-2 text-sm text-slate-700 font-semibold">
                            {{
                                !isDragging ? `Кликните для выбора ${extensions} или перетащите файл сюда` : 'Отпустите файл здесь'
                            }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ !isDragging ? `${extensions.toUpperCase()} (до 10 MB)` : '' }}
                        </p>
                    </div>

                    <input :accept="extensions" class="hidden" type="file" @change="onFileSelect"/>
                </label>
            </div>

            <div v-if="selectedFile && checkFile /*selectedFile && !isUploading*/"
                 class="bg-white border-2 border-indigo-100 rounded-2xl p-6 shadow-xl shadow-indigo-50/50">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-indigo-600 rounded-xl text-white">
                        <svg class="w-8 h-8"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24"
                        >
                            <path
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                            />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-base font-bold text-slate-900 truncate">
                            {{ selectedFile.name }}
                        </p>
                        <p class="text-xs text-slate-500 font-medium tracking-wide">
                            {{ (selectedFile.size / 1024).toFixed(1) }} KB • Готов к импорту
                        </p>
                    </div>
                    <button class="p-2 hover:bg-slate-100 rounded-full text-slate-400 hover:text-red-500 transition-all"
                            @click="cancelSelection">
                        <svg class="w-6 h-6"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24"
                        >
                            <path d="M6 18L18 6M6 6l12 12"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                            />
                        </svg>
                    </button>
                </div>

                <button
                    class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-4 rounded-xl transition-all flex items-center justify-center space-x-2 active:scale-[0.98]"
                    @click="uploadFile"
                >
                    <span>{{ uploadTitle }}</span>
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path d="M13 5l7 7-7 7M5 5l7 7-7 7"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                        />
                    </svg>
                </button>
            </div>

        </div>

        <AppModalAsyncTS
            ref="appModalAsyncTS"
            :mode="modalMode"
            :text="modalText"
            :type="modalType"
        />
    </div>
</template>


<script lang="ts" setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import AppModalAsyncTS from '@/components/ui/modals/AppModalAsyncTS.vue'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

interface IProps {
    fileExtension?: string[],   // Расширение файла, которое нужно выбрать
    checkFile?: boolean         // Не позволяет переходить ко второму этапу - "Начать импорт" если файл не прошел валидацию
    uploadTitle?: string,       // Заголовок для второго этапа загрузки
}

const props = withDefaults(defineProps<IProps>(), {
    checkFile:     true,
    fileExtension: () => ['.json'],
    uploadTitle:   'Начать импорт',
})

// __ Эмитируем 2 события для совместимости с предыдущим API
const emits = defineEmits<{
    (e: 'selectFile', file: File): void
    (e: 'uploadFile', file: File): void
}>()

// __ Определяем расширения файла через строку
const extensions = computed(() => props.fileExtension.map(ext => ext.toLowerCase()).join(','))

const selectedFile = ref<File | null>(null)
const isDragging   = ref(false) // Для визуального эффекта при наведении
// const isUploading  = ref(false)
// const progress     = ref(0)


// __ Тип для модального окна
const modalType       = ref<IColorTypes>('danger')
const modalText       = ref<string>('')
const modalMode       = ref<'inform' | 'confirm'>('inform')
const appModalAsyncTS = ref<InstanceType<typeof AppModalAsyncTS> | null>(null)         // Получаем ссылку на модальное окно с асинхронной функцией

// __ Обработка перетаскивания (Drop)
const onFileDrop = (e: DragEvent) => {
    isDragging.value = false

    const file = e.dataTransfer?.files[0]

    if (file) {
        validateAndSetFile(file)
    }
}

// __ Обработка выбора через клик (Change)
const onFileSelect = (e: Event) => {
    // Приводим target к типу HTMLInputElement, чтобы TS знал о наличии .files
    const target = e.target as HTMLInputElement
    const file   = target.files?.[0]

    if (file) {
        validateAndSetFile(file)
    }
}

// __ Общая валидация
const validateAndSetFile = (file: File) => {
    const name: string = file.name.toLowerCase()
    const extension    = name.slice(((name.lastIndexOf('.') - 1) >>> 0) + 1).toLowerCase()

    if (extensions.value.includes(extension)) {
        selectedFile.value = file
        emits('selectFile', selectedFile.value)     // __ Здесь только выбор файла
    } else {
        modalText.value = `Расширение Файла не соответствует шаблону ${extensions.value}!!!`
        appModalAsyncTS.value!.show()             // показываем модалку и ждем ответ
        // alert('Файл должен быть в формате JSON (.json)')
    }

}

// __ Запуск импорта (2 этап загрузки)
const uploadFile = () => {
    // isUploading.value = true
    if (selectedFile.value) {
        emits('uploadFile', selectedFile.value)         // __ Здесь выбор файла + начало импорта
    }
}

// __ Отмена выбора файла
const cancelSelection = () => {
    selectedFile.value = null
}

watch(() => props.checkFile, (newVal) => {
    if (!newVal) {
        cancelSelection()
    }
}, {immediate: true, deep: true})


// __ Запрещаем открытие файла во ВСЕМ окне браузера (Чтобы не открывался в новой вкладке при промахе мимо drop зоны)
const preventDefault = (e: DragEvent) => e.preventDefault()

onMounted(() => {
    // Запрещаем открытие файла во ВСЕМ окне браузера
    window.addEventListener('dragover', preventDefault)
    window.addEventListener('drop', preventDefault)
})

onUnmounted(() => {
    window.removeEventListener('dragover', preventDefault)
    window.removeEventListener('drop', preventDefault)
})
</script>
