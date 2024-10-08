<template>
    <main class="content">
        <div>The Main</div>

        <AppInputText
            :id="newInput"
            :disabled="true"
            :type='"danger"'
            label='Введите имя'
            placeholder='Search'
        />

        <AppInputText
            :id="newInput_1"
            :error="workError"
            label="Введите данные"
            placeholder="Enter..."
            type="warning"
            width="w-[300px]"
        />

        <AppInputText
            :id="newInput_2"
            label="Введите данные"
            placeholder="Enter..."
            type="success"
            @getInputText="showText"
        />


        <AppInputButton
            id="newButton"
            title="Ошибка"
            type="primary"
            @buttonClick="buttonClickHandler"
        />

        <AppInputButton
            id="newButton"
            title="Ошибка"
            type="secondary"
            @buttonClick="buttonClickHandler"
        />

        <AppInputButton
            id="newButton"
            title="Ошибка"
            type="success"
            @buttonClick="buttonClickHandler"
        />

        <AppInputButton
            id="newButton"
            title="Ошибка"
            type="danger"
            @buttonClick="buttonClickHandler"
        />


        <div>
            <AppInputButton
                id="newButton"
                title="Ошибка"
                type="info"
                @buttonClick="buttonClickHandler"
            />

            <AppInputButton
                id="newButton"
                title="Warning"
                type="warning"
                @buttonClick="buttonClickHandler"
            />

            <AppInputButton
                id="newButton"
                title="Ошибка"
                type="light"
                @buttonClick="buttonClickHandler"
            />

            <AppInputButton
                id="newButton"
                title="Ошибка"
                type="dark"
                @buttonClick="buttonClickHandler"
            />
        </div>

        <AppCallout
            type="dark"

        />

        <AppModal
            :show="false"
            text="A am modal"
            type="info"

        />

<!--        <AppSelect-->
<!--            :selectData="selectData"-->
<!--        />-->


    </main>
</template>


<script setup>
import {reactive, ref} from "vue";
import AppInputText from "@/src/components/ui/inputs/AppInputText.vue";
import ErrorClass from "@/src/app/classes/ErrorClass.js";
import AppInputButton from "@/src/components/ui/inputs/AppInputButton.vue";
import AppCallout from "@/src/components/ui/callouts/AppCallout.vue";
import AppModal from "@/src/components/ui/modals/AppModal.vue";
import AppSelect from "@/src/components/ui/selects/AppSelect.vue";

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


const buttonClickHandler = function (args) {
    console.log(args)
}


const selectData = {
    name: 'numbers',
    data: [
        {id: 1, name: 'One'},
        {id: 2, name: 'Two'},
        {id: 3, name: 'Three', selected: true},
        {id: 4, name: 'Four'},
        {id: 5, name: 'Five'},
    ]
}


// console.log(selectData)

// const selectData = reactive({})

</script>


<style scoped>
.content {
    flex: 1;
    overflow-y: auto;
    margin: var(--header-height) 0 var(--footer-height) var(--sidebar-width);
    /*   position: relative; */
}
</style>
