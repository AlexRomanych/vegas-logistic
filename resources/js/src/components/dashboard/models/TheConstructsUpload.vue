<template>
    <div class="flex justify-start items-center m-4">
        <div>
            <AppInputFile
                id="file"
                label="Файл JSON cо Спецификациями изделий (КД)"
                type="slate"
                @selectFile="onFileSelected"
            />
        </div>
        <div>
            <AppButton
                id="upload"
                height="min-h-[64px]"
                title="Загрузить"
                type="slate"
                width="w-[200px]"
                @buttonClick="uploadFile"
            />
        </div>
    </div>

    <AppCallout
        v-if="!isDataJson"
        text="Ошибка данных!"
        type="danger"
    />

    <AppCallout
        v-if="opResult"
        :text="opResultText"
        :type="opResultType"
    />

</template>

<script setup lang="ts">

import { ref } from 'vue'

import { useModelsStore } from '@/stores/ModelsStore.ts'

import { getFileContent } from '@/app/helpers/helpers_file_reader.js'
import { isJSON } from '@/app/helpers/helpers_checks.ts'

import AppInputFile from '@/components/ui/inputs/AppInputFile.vue'

import AppButton from '@/components/ui/buttons/AppButton.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'



const selectedFile = ref<object | null>(null)
const isDataJson = ref(true)                // Проверка на тип файла для вызова Callout
const opResult = ref(false)                 // Проверка на результат выполнения операции
const opResultText = ref('')                // Сообщение результата операции
const opResultType = ref('')                // Тип результата операции

// todo Сделать отображение данных файла и сделать проверку на тип файла(данных)
// Получаем данные файла
const onFileSelected = (formData: object) => {
    selectedFile.value = formData
    console.log('formData: ', formData)
}

const uploadFile = async () => {

    // warning Не связываемся с FormData - танцы с бубнами
    // Создаем объект FormData и пихаем туда ссылку на файл
    // const formData = new FormData()
    // formData.append('file', selectedFile.value)
    // formData.append('_method', 'PATCH');


    // Получаем данные файла
    // todo Сделать отображение данных файла и сделать проверку на тип файла(данных)
    // если что-то загружено


    if (selectedFile.value) {
        const fileData = await getFileContent(selectedFile.value)

        // console.log('fileData: ', fileData)

        // isDataJson.value = true
        isDataJson.value = isJSON(fileData)     // Проверка на Json формат

        if (isDataJson.value) {

            // Отправляем в RAW формате и возвращаем результат операции
            // todo сделать проверку на существующие ПС

            const modelsStore = useModelsStore()
            const res = await modelsStore.uploadConstructs(fileData)
            // const res = await ordersStore.uploadOrders(fileData)

            console.log('uploadConstructs: ', res)

            // if (res === 'success') {
            //     opResultText.value = 'Данные успешно загружены'
            //     opResultType.value = 'success'
            //     setTimeout(() => {
            //         opResult.value = false
            //     }, 5000)
            // } else {
            //     opResultText.value = 'Дубликат:' + res.join(', ')
            //     opResultType.value = 'danger'
            // }
            //
            // opResult.value = true
            // setTimeout(() => {
            //     opResult.value = false
            // }, 5000)

        } else {
            isDataJson.value = false
            setTimeout(() => {
                isDataJson.value = true
            }, 5000)
        }
    }
}

</script>
