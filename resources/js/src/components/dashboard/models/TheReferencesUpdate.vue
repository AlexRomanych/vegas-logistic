<template>
    <div>
        <div class="flex w-full justify-center">

            <div :class="COLUMN_WIDTH">
                <div class="group">
                    <div class="label">
                        <AppLabelMultiLineTS
                            :align="LABEL_ALIGN"
                            :height="LABEL_HEIGHT"
                            :text-size="LABEL_TEXT_SIZE"
                            :type="LABEL_TYPE"
                            :width="LABEL_WIDTH"
                            rounded="4"
                            text="Отчет 1С по материалам"
                        />
                    </div>

                    <div class="file">
                        <AppInputFileTS
                            :file-extension="FILE_EXTENSIONS"
                            :height="FILE_HEIGHT"
                            :width="FILE_WIDTH"
                            upload-title="Файл материалов готов к импорту"
                            @select-file="selectFileMaterials"
                        />
                    </div>
                </div>

                <div class="group">
                    <div class="label">
                        <AppLabelMultiLineTS
                            :align="LABEL_ALIGN"
                            :height="LABEL_HEIGHT"
                            :text-size="LABEL_TEXT_SIZE"
                            :type="LABEL_TYPE"
                            :width="LABEL_WIDTH"
                            rounded="4"
                            text="Отчет 1С по моделям"
                        />
                    </div>

                    <div class="file">
                        <AppInputFileTS
                            :file-extension="FILE_EXTENSIONS"
                            :height="FILE_HEIGHT"
                            :width="FILE_WIDTH"
                            upload-title="Файл моделей готов к импорту"
                            @select-file="selectFileModels"
                        />
                    </div>
                </div>
            </div>

            <div :class="COLUMN_WIDTH">
                <div class="group">
                    <div class="label">
                        <AppLabelMultiLineTS
                            :align="LABEL_ALIGN"
                            :height="LABEL_HEIGHT"
                            :text-size="LABEL_TEXT_SIZE"
                            :type="LABEL_TYPE"
                            :width="LABEL_WIDTH"
                            rounded="4"
                            text="Отчет 1С по процедурам расчета"
                        />
                    </div>

                    <div class="file">
                        <AppInputFileTS
                            :file-extension="FILE_EXTENSIONS"
                            :height="FILE_HEIGHT"
                            :width="FILE_WIDTH"
                            upload-title="Файл процедур готов к импорту"
                            @select-file="selectFileProcedures"
                        />
                    </div>
                </div>

                <div class="group">
                    <div class="label">
                        <AppLabelMultiLineTS
                            :align="LABEL_ALIGN"
                            :height="LABEL_HEIGHT"
                            :text-size="LABEL_TEXT_SIZE"
                            :type="LABEL_TYPE"
                            :width="LABEL_WIDTH"
                            rounded="4"
                            text="Отчет 1С по спецификациям"
                        />
                    </div>

                    <div class="file">
                        <AppInputFileTS
                            :file-extension="FILE_EXTENSIONS"
                            :height="FILE_HEIGHT"
                            :width="FILE_WIDTH"
                            upload-title="Файл спецификаций готов к импорту"
                            @select-file="selectFileSpecifications"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div v-if="fileMaterials && fileModels && fileProcedures && fileSpecifications"
             class="flex justify-center cursor-pointer uppercase">
            <AppLabelMultiLineTS
                :align="LABEL_ALIGN"
                :text="isUploading ? uploadProgress.toString() + '%' : 'Начать импорт'"
                :text-size="LABEL_TEXT_SIZE"
                :type="LABEL_TYPE"
                :width="LABEL_WIDTH"
                height="h-[75px]"
                rounded="4"
                @click="startImport"
            />
        </div>

    </div>

    <!-- __ Для модальных сообщений -->
    <AppModalAsyncTS
        ref="appModalAsyncTS"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
        ok-word="Понятно"
    />

</template>

<script lang="ts" setup>
import type { IColorTypes } from '@/types'

import { ref } from 'vue'
import { useRouter } from 'vue-router'

import AppInputFileTS from '@/components/ui/inputs/AppInputFileTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppModalAsyncTS from '@/components/ui/modals/AppModalAsyncTS.vue'
import axios from 'axios'

const router = useRouter()

// __ URL для загрузки файлов
const URL = '/api/v1/models/update'

const FILE_EXTENSIONS = ['.xlsx']

const COLUMN_WIDTH    = 'w-[500px]'
const LABEL_HEIGHT    = 'h-[50px]'
const LABEL_WIDTH     = 'w-[464px]'
const LABEL_TYPE      = 'indigo'
const LABEL_ALIGN     = 'center'
const LABEL_TEXT_SIZE = 'normal'
const FILE_WIDTH      = 'w-[500px]'
const FILE_HEIGHT     = 'min-h-[100px]'

const fileMaterials      = ref<File>()
const fileModels         = ref<File>()
const fileProcedures     = ref<File>()
const fileSpecifications = ref<File>()

// __ Тип для модального окна
const modalType       = ref<IColorTypes>('danger')
const modalText       = ref<string>('')
const modalMode       = ref<'inform' | 'confirm'>('inform')
const appModalAsyncTS = ref<InstanceType<typeof AppModalAsyncTS> | null>(null)


const isUploading    = ref(false)
const uploadProgress = ref(0) // Если захочешь добавить прогресс-бар


const startImport = async () => {
    if (!fileMaterials.value || !fileModels.value || !fileProcedures.value || !fileSpecifications.value) {
        modalText.value = 'Необходимо выбрать все файлы перед началом импорта!'
        appModalAsyncTS.value?.show()
        return
    }

    isUploading.value    = true
    uploadProgress.value = 0

    // __ Получаем токен
    const token = localStorage.getItem('token')

    const formData = new FormData()
    formData.append('materials', fileMaterials.value)
    formData.append('models', fileModels.value)
    formData.append('procedures', fileProcedures.value)
    formData.append('specifications', fileSpecifications.value)

    try {
        const response = await axios.post(URL, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',

                // __ Добавляем JWT токен в заголовки
                'Authorization': `Bearer ${token}`
            },
            // __ Отслеживание прогресса загрузки
            onUploadProgress: (progressEvent) => {
                if (progressEvent.total) {
                    uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
                }
            }
        })

        // console.log('response: ', response)

        if (response.status === 200) {

            modalType.value = 'success'
            modalText.value = response.data.payload || 'Справочники успешно обновлены'
            await appModalAsyncTS.value?.show()

            // __ Очищаем файлы после успешной загрузки
            cancelAllSelections()

            // __ Перезагружаем страницу
            await router.push({
                path : router.currentRoute.value.path,
                query: { ...router.currentRoute.value.query, t: Date.now() }
            })
            // router.go(0)
        }
    } catch (error) {

        // console.log('Error object:', JSON.parse(JSON.stringify(error.response)));

        modalType.value = 'danger'

        if (axios.isAxiosError(error)) {
            // Теперь TS знает, что это AxiosError
            // Ты можешь даже указать тип того, что возвращает твой Laravel (например, { message: string })
            modalText.value = error.response?.data?.error || 'Упс! Что-то пошло не так.'
        } else {
            // Это на случай, если ошибка не связана с сетевым запросом
            modalText.value = 'Упс! Что-то пошло не так.'
        }

        await appModalAsyncTS.value?.show()
    } finally {
        isUploading.value = false
    }
}

// __ Вспомогательная функция для очистки
const cancelAllSelections = () => {
    fileMaterials.value      = undefined
    fileModels.value         = undefined
    fileProcedures.value     = undefined
    fileSpecifications.value = undefined
}


const selectFileMaterials = (file: File) => {
    fileMaterials.value = file
}

const selectFileModels = (file: File) => {
    fileModels.value = file
}

const selectFileProcedures = (file: File) => {
    fileProcedures.value = file
}

const selectFileSpecifications = (file: File) => {
    fileSpecifications.value = file
}

</script>

<style scoped>
.group {
    @apply m-2 p-2 pb-0 border-2 rounded-lg border-indigo-200 border-dashed
}

.file {
    @apply flex justify-center min-h-[288px]
}

.label {
    @apply flex justify-center
}
</style>
