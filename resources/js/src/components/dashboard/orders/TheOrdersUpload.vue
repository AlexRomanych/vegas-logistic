<template>
    <div class="flex justify-start items-center m-4">
        <div>
            <AppInputFile
                id="file"
                label="Файл JSON c заявками"
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

    <AppCallout v-if="!isDataJson" text="Ошибка данных!" type="danger" />

    <AppCallout v-if="opResult" :text="opResultText" :type="opResultType" />
</template>

<script setup>
import { ref } from 'vue'

import { useOrdersStore } from '@/stores/OrdersStore.ts'
import { getFileContent } from '@/app/helpers/helpers_file_reader.js'
import { isJSON } from '@/app/helpers/helpers_checks.js'

// import AppInputFileTS from '@/components/ui/inputs/AppInputFileTS.vue'
import AppInputFile from '@/components/ui/inputs/AppInputFile.vue'
import AppButton from '@/components/ui/buttons/AppButton.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'



const selectedFile = ref(null)
const isDataJson = ref(true) // Проверка на тип файла для вызова Callout
const opResult = ref(false) // Проверка на результат выполнения операции
const opResultText = ref('') // Сообщение результата операции
const opResultType = ref('') // Тип результата операции

// todo Сделать отображение данных файла и сделать проверку на тип файла(данных)
// Получаем данные файла
const onFileSelected = (formData) => (selectedFile.value = formData)

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

        isDataJson.value = true

        if (isJSON(fileData)) {
            // Отправляем в RAW формате и возвращаем результат операции
            // todo сделать проверку на существующие заявки
            const ordersStore = useOrdersStore()
            const res = await ordersStore.uploadOrders(fileData)
            // const res = await ordersStore.uploadOrders(fileData)

            // if (res.length === 0) {
            //     opResultText.value = 'Данные успешно загружены'
            //     opResultType.value = 'success'
            //     setTimeout(() => {
            //         opResult.value = false
            //     }, 5000)
            // } else {
            //     const dubsTextArray = res.map((item) => {
            //         return item['sh'] + ' ' + item['n']
            //     })
            //
            //     // console.log(dubsTextArray)
            //
            //     opResultText.value = 'Дубликат:' + dubsTextArray.join(', ')
            //     opResultType.value = 'danger'
            // }
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
