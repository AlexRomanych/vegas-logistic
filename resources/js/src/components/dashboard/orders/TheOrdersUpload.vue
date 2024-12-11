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

    <!--    <div>-->
    <!--        <input ref="fileInput" type="file" @change="onFileSelected">-->
    <!--        <button @click="uploadFile">Загрузить</button>-->
    <!--    </div>-->
</template>

<script setup>
// import axios from 'axios';
import {ref} from 'vue';
import AppInputFile from '@/src/components/ui/inputs/AppInputFile.vue'
import AppButton from '@/src/components/ui/buttons/AppButton.vue'
import {useOrdersStore} from '@/src/stores/OrdersStore.js'
import {getFileContent} from '@/src/app/helpers/helpers_file_reader.js'
import {isJSON} from '@/src/app/helpers/helpers_checks.js'

const selectedFile = ref(null)

// todo Сделать отображение данных файла и сделать проверку на тип файла(данных)
// Получаем данные файла
const onFileSelected = (formData) => selectedFile.value = formData

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

        if (isJSON(fileData)) {
            // Отправляем в RAW формате и возвращаем результат операции
            // todo сделать проверку на существующие заявки
            const ordersStore = useOrdersStore()
            const res = await ordersStore.uploadOrders(fileData)
            // const res = await ordersStore.uploadOrders(fileData)

            console.log(res)
        }
    }
}

</script>
