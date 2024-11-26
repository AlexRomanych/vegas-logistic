<template>
    <div>
        <AppInputFile
            id="file"
            label="Файл JSON c заявками"
            type="dark"
        />
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

const selectedFile = ref(null)

const onFileSelected = (event) => selectedFile.value = event.target.files[0]

const uploadFile = () => {

    const formData = new FormData();
    formData.append('file', selectedFile.value);


    axios.post('/upload', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
        .then(response => {
            console.log(response);
        })
        .catch(error => {
            console.error(error);

        });
}

</script>


<!--<template>-->
<!--    I am Upload-->
<!--</template>-->

<!--<script setup>-->

<!--</script>-->

<!--<style scoped>-->

<!--</style>-->

