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
            type="slate"
            title="Загрузить"
            width="w-[200px]"
            height="min-h-[64px]"
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
import axios from 'axios';
import {ref} from 'vue';
import AppInputFile from '@/src/components/ui/inputs/AppInputFile.vue'
import AppButton from '@/src/components/ui/buttons/AppButton.vue'

import {useOrdersStore} from '@/src/stores/OrdersStore.js'

const selectedFile = ref(null)

const onFileSelected = (formData) => selectedFile.value = formData

// todo Сделать отображение данных файла и сделать проверку на тип данных

const uploadFile = async () => {

    // Создаем объект FormData и пихаем туда ссылку на файл
    const formData = new FormData()
    formData.append('file', selectedFile.value, 'orders.json')

    const ordersStore = useOrdersStore()

    console.log(selectedFile.value)
    console.log(formData)

    const res = await ordersStore.uploadOrders(formData)

    console.log(res)

    // axios.post('api/v1/orders/upload', formData, {
    //     headers: {
    //         'Content-Type': 'multipart/form-data'
    //     }
    // })
    //     .then(response => {
    //         console.log(response);
    //     })
    //     .catch(error => {
    //         console.error(error);
    //
    //     });
}

</script>


<!--<template>-->
<!--    I am Upload-->
<!--</template>-->

<!--<script setup>-->

<!--</script>-->

<!--<style scoped>-->

<!--</style>-->

