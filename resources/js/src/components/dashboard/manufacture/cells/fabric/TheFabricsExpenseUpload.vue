<template>
    <div class="flex justify-start items-center m-4">
        <div>
            <AppInputFile
                id="file"
                label="Файл JSON c расходом ПС (из отчета 1С -СВПМ)"
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
        :show="opResult"
        :text="opResultText"
        :type="opResultType"
    />

</template>

<script setup>

import {ref} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {getFileContent} from '/resources/js/src/app/helpers/helpers_file_reader.js'
import {isJSON} from '/resources/js/src/app/helpers/helpers_checks.js'

import AppInputFile from '/resources/js/src/components/ui/inputs/AppInputFile.vue'
import AppButton from '/resources/js/src/components/ui/buttons/AppButton.vue'
import AppCallout from '/resources/js/src/components/ui/callouts/AppCallout.vue'

const selectedFile = ref(null)
const isDataJson = ref(true)                // Проверка на тип файла для вызова Callout
const opResult = ref(false)                 // Проверка на результат выполнения операции
const opResultText = ref('')                // Сообщение результата операции
const opResultType = ref('danger')          // Тип результата операции

// todo Сделать отображение данных файла и сделать проверку на тип файла(данных)
// Получаем данные файла
const onFileSelected = (formData) => selectedFile.value = formData

const uploadFile = async (buttonId) => {

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
            const fabricsStore = useFabricsStore()
            const res = await fabricsStore.uploadFabricsOrders(fileData)
            // const res = await ordersStore.uploadOrders(fileData)

            // console.log('uploadOrders', res)
            const missingFabrics = [...Object.values(res['missing_fabrics'])].sort((str1, str2) => str1.localeCompare(str2))
            // console.log(missingFabrics, missingFabrics.length)

            // opResult.value = true
            // debugger

            if (missingFabrics.length === 0) {
                opResultText.value = 'Данные успешно загружены'
                opResultType.value = 'success'
                // setTimeout(() => {
                //     opResult.value = false
                // }, 5000)
            } else {
                // TODO: Доделать красивый многострочный Callout
                opResultText.value = 'Пропущены:&nl' + missingFabrics.join(',&nl')
                opResultType.value = 'danger'
            }

            opResult.value = true
            setTimeout(() => {
                opResult.value = false
            }, 5000)
        }

    } else {

        isDataJson.value = false

        opResultText.value = 'Ошибка в Json файле'
        opResultType.value = 'danger'

        opResult.value = true
        setTimeout(() => {
            opResult.value = false
            isDataJson.value = true
        }, 5000)
    }

}

</script>
