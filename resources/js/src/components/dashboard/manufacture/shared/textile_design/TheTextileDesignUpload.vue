<template>
    <div class="uploader-container">
        <div class="card">
            <div class="card-header">
                <h2>Загрузка конструкторской документации</h2>
                <p class="subtitle">Введите номер КД и выберите PDF-файл для привязки</p>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label for="kdch-input">Номер документации (КД):</label>
                    <input
                        id="kdch-input"
                        v-model.trim="kdch"
                        :disabled="isUploading"
                        class="form-control"
                        placeholder="Например: 11033"
                        type="text"
                    />
                </div>

                <div class="form-group">
                    <label>Файл чертежа (PDF):</label>
                    <div
                        :class="{ 'has-file': selectedFile, 'disabled': isUploading }"
                        class="dropzone"
                        @click="triggerFileInput"
                    >
                        <input
                            ref="fileInputRef"
                            :disabled="isUploading"
                            accept="application/pdf"
                            class="hidden-file-input"
                            type="file"
                            @change="onFileSelect"
                        />

                        <div v-if="!selectedFile" class="dropzone-placeholder">
                            <span class="icon">📂</span>
                            <p>Нажмите, чтобы выбрать PDF-файл</p>
                        </div>

                        <div v-else class="dropzone-file-info">
                            <span class="icon">📄</span>
                            <p class="file-name">{{ selectedFile.name }}</p>
                            <p class="file-size">{{ formatBytes(selectedFile.size) }}</p>
                            <button
                                :disabled="isUploading"
                                class="btn-reset"
                                type="button"
                                @click.stop="resetFile"
                            >
                                Удалить выбор
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex gap-1">
                    <button
                        class="btn-submit"
                        @click="$router.push({name: 'manufacture.textile.design.show'})"
                    >
                        <span>К списку КДЧ</span>
                    </button>
                    <button
                        :disabled="!isValid || isUploading"
                        class="btn-submit"
                        @click="uploadDocument"
                    >
                        <span v-if="isUploading">⏳ Загрузка на сервер...</span>
                        <span v-else>🚀 Привязать документ</span>
                    </button>
                </div>
            </div>
        </div>

        <div v-if="previewUrl" class="card preview-card">
            <div class="card-header preview-header">
                <h3>Предпросмотр выбранного файла</h3>
                <span class="badge">Локальный плеер</span>
            </div>
            <div class="card-body no-padding">
                <div class="pdf-viewer-wrapper">
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
import { ref, computed, onUnmounted } from 'vue'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import { useSharedStore } from '@/stores/SharedStore.ts'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

const sharedStore = useSharedStore()

// Состояние формы
const kdch         = ref('')
const selectedFile = ref<File | null>(null)
const previewUrl   = ref<string | null>(null)
const isUploading  = ref(false)

const fileInputRef = ref<HTMLInputElement | null>(null)

// Валидация формы (кнопка активна только если заполнен номер и выбран файл)
const isValid = computed(() => {
    return kdch.value.length > 0 && selectedFile.value !== null
})

// Клик по кастомной зоне выбора файла триггерит стандартный скрытый input
const triggerFileInput = () => {
    if (!isUploading.value && fileInputRef.value) {
        fileInputRef.value.click()
    }
}

// Обработка выбора файла на компьютере
const onFileSelect = (event: Event) => {
    // 1. Проверяем, что target вообще существует
    if (!event.target) return

    // 2. Принудительно объясняем TypeScript, что этот target — HTML-инпут.
    // После этого у переменной target ПОЯВИТСЯ свойство files.
    const target = event.target as HTMLInputElement

    // 3. Безопасно забираем файл из массива через опциональную цепочку
    const file = target.files?.[0]
    if (!file) return

    // Проверяем тип файла на всякий случай
    if (file.type !== 'application/pdf') {
        alert('Пожалуйста, выберите файл в формате PDF')
        resetFile()
        return
    }

    selectedFile.value = file

    // Генерируем локальную blob-ссылку для тега <embed> (файл ещё не улетел на бэкенд)
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value) // Чистим старую ссылку, если была
    }
    previewUrl.value = URL.createObjectURL(file)
}

// Сброс выбранного файла
const resetFile = () => {
    selectedFile.value = null
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value)
        previewUrl.value = null
    }
    if (fileInputRef.value) {
        fileInputRef.value.value = '' // Сбрасываем значение инпута
    }
}

// __ Отправка данных на Сервер
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

    // Формируем multipart/form-data
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

// Хелпер для красивого отображения размера файла
const formatBytes = (bytes: number, decimals = 2) => {
    if (bytes === 0) return '0 Bytes'
    const k     = 1024
    const dm    = decimals < 0 ? 0 : decimals
    const sizes = ['Bytes', 'KB', 'MB']
    const i     = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i]
}


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


// Защита от утечек памяти: удаляем blob-ссылку при уничтожении компонента
onUnmounted(() => {
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value)
    }
})
</script>

<style scoped>
.uploader-container {
    max-width: 750px;
    margin: 2rem auto;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-header {
    padding: 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.card-header h2, .card-header h3 {
    margin: 0;
    color: #1e293b;
    font-size: 1.35rem;
}

.subtitle {
    margin: 0.25rem 0 0 0;
    color: #64748b;
    font-size: 0.9rem;
}

.card-body {
    padding: 1.5rem;
}

.no-padding {
    padding: 0 !important;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #334155;
    font-size: 0.95rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    color: #1e293b;
    box-sizing: border-box;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Стилизация Дропзоны для файла */
.dropzone {
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    background: #f8fafc;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

.dropzone:hover:not(.disabled) {
    border-color: #3b82f6;
    background: #eff6ff;
}

.dropzone.has-file {
    border-style: solid;
    border-color: #10b981;
    background: #ecfdf5;
}

.dropzone.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.hidden-file-input {
    display: none;
}

.icon {
    font-size: 2.5rem;
    display: block;
    margin-bottom: 0.5rem;
}

.file-name {
    font-weight: 600;
    color: #0f172a;
    margin: 0.25rem 0;
    word-break: break-all;
}

.file-size {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0 0 1rem 0;
}

.btn-reset {
    background: #ef4444;
    color: white;
    border: none;
    padding: 0.4rem 1rem;
    border-radius: 4px;
    font-size: 0.85rem;
    cursor: pointer;
}

.btn-reset:hover {
    background: #dc2626;
}

/* Кнопка отправки */
.btn-submit {
    width: 100%;
    padding: 0.85rem;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-submit:hover:not(:disabled) {
    background: #2563eb;
}

.btn-submit:disabled {
    background: #94a3b8;
    cursor: not-allowed;
}

/* Блок предпросмотра */
.preview-card {
    border-color: #93c5fd;
}

.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f0f7ff;
}

.badge {
    background: #dbeafe;
    color: #1e40af;
    padding: 0.25rem 0.6rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.pdf-viewer-wrapper {
    background: #525659;
    display: flex;
    justify-content: center;
}
</style>
