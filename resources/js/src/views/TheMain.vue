<template>
    <main class="content">
        <div>The Main</div>

        <AppInputText
            :id = 'newInput'
            placeholder = 'Search'
            label = 'Введите имя'
            :type = '"danger"'
            :disabled="true"
        />

        <AppInputText
            :id = 'newInput_1'
            :placeholder = '"Enter..."'
            :label = '"Введите данные"'
            :error = 'workError'
            :type = '"warning"'
        />

        <AppInputText
            :label = '"Введите данные"'
            :id = 'newInput_2'
            :placeholder = '"Enter..."'
            :type = '"success"'
            @getInputText = 'showText'
        />

<!--        <AppButtonSubmit-->

<!--        />-->

<!--        <AppInputButton-->
<!--            id="newButton"-->
<!--            :type = '"error"'-->
<!--        />-->

    </main>
</template>


<script setup>
import {ref} from "vue";
import AppInputText from "@/src/components/ui/inputs/AppInputText.vue";
import AppButtonSubmit from "@/src/components/ui/buttons/AppButtonSubmit.vue";
import ErrorClass from "@/src/app/classes/ErrorClass.js";
import AppInputButton from "@/src/components/ui/inputs/AppInputButton.vue";


const newInput = ref('123')
const newInput_1 = ref('123')
const newInput_2 = ref('123')
// const workError = new ErrorClass({message: 'Это ошибка'})
const workError = new ErrorClass()

const defError = new Error('Привет')
// console.log(workError)
const showText = function (data) {
    console.log(data)
}

</script>


<style scoped>
.content {
    flex: 1;
    overflow-y: auto;
    margin: var(--header-height) 0 var(--footer-height) var(--sidebar-width);
}
</style>
