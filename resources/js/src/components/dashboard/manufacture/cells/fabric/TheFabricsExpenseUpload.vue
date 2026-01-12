<template>

    <!-- attract: Блок загрузки файла -->
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


    <!-- attract: Пропущенные ПС   -->
    <div v-if="missingFabrics.length" class="m-5">


        <div class="mb-3">
            <AppLabelMultiLine
                align="center"
                height="h-[50px]"
                :text="['Следующие ПС отсутствуют в базе данных и будут недоступны для расчета.', 'Удалите их из файла или добавьте в базу данных.']"
                type="danger"
                width="w-[600px]"
            />

        </div>

        <div v-for="(item, index) in missingFabrics" :key="index">

            <AppLabel
                :text="item"
                text-size="mini"
                type="danger"
                width="w-[600px]"
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

import {useFabricsStore} from '@/stores/FabricsStore.js'

import {getFileContent} from '@/app/helpers/helpers_file_reader.js'
import {isJSON} from '@/app/helpers/helpers_checks.ts'

import AppInputFile from '@/components/ui/inputs/AppInputFile.vue'
import AppButton from '@/components/ui/buttons/AppButton.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'

const selectedFile = ref(null)
const isDataJson = ref(true)                // Проверка на тип файла для вызова Callout
const opResult = ref(false)                 // Проверка на результат выполнения операции
const opResultText = ref('')                // Сообщение результата операции
const opResultType = ref('danger')          // Тип результата операции

// attract: Пропущенные ПС
const missingFabrics = ref([])

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
            missingFabrics.value = [...Object.values(res['missing_fabrics'])].sort((str1, str2) => str1.localeCompare(str2))
            // console.log(missingFabrics, missingFabrics.length)

            // opResult.value = true
            // debugger

            if (missingFabrics.value.length === 0) {
                opResultText.value = 'Данные успешно загружены'
                opResultType.value = 'success'
                // setTimeout(() => {
                //     opResult.value = false
                // }, 5000)
            } else {
                // TODO: Доделать красивый многострочный Callout
                opResultText.value = 'В расходе присутствуют пропущенные ПС'
                // opResultText.value = 'Пропущены:&nl' + missingFabrics.value.join(',&nl')
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
