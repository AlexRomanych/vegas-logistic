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
import {getFileText} from '@/src/app/helpers/helpers_file_reader.js'


const selectedFile = ref(null)

// todo Сделать отображение данных файла и сделать проверку на тип файла(данных)
const onFileSelected = (formData) => selectedFile.value = formData


const uploadFile = async () => {

    // Создаем объект FormData и пихаем туда ссылку на файл
    const formData = new FormData()
    // console.log(selectedFile.value)
    formData.append('file', selectedFile.value)
    formData.append('_method', 'PATCH');

    const ordersStore = useOrdersStore()

    // Возвращаем результат операции todo сделать проверку на существующие заявки
    const res = await ordersStore.uploadOrders(formData)
    // const res = await ordersStore.uploadOrders(selectedFile.value)
    debugger
    // console.log(res)

    const fileData = await getFileText(selectedFile.value)
    console.log(fileData)


}

</script>
