<template>
    <main class="content">
        <div>The Main</div>

        <AppInputText
            :id = 'newInput'
            :placeholder = '"Search"'
            :label = '"Введите имя"'
            :type = '"error"'
        />

        <AppInputText
            :id = 'newInput_1'
            :placeholder = '"Enter..."'
            :label = '"Введите данные"'
            :error = 'workError'
            :type = '"warn"'
        />

        <AppInputText
            :label = '"Введите данные"'
            :id = 'newInput_2'
            :placeholder = '"Enter..."'
            :type = '"notice"'
        />

    </main>
</template>


<script setup>
import {ref} from "vue";
import AppInputText from "@/src/components/ui/inputs/AppInputText.vue";
import ErrorClass from "@/src/app/classes/ErrorClass.js";


const newInput = ref('123')
const newInput_1 = ref('123')
const newInput_2 = ref('123')
// const workError = new ErrorClass({message: 'Это ошибка'})
const workError = new ErrorClass()

const defError = new Error('Привет')
// console.log(workError)


</script>


<style scoped>
.content {
    flex: 1;
    overflow-y: auto;
    margin: var(--header-height) 0 var(--footer-height) var(--sidebar-width);
}
</style>
