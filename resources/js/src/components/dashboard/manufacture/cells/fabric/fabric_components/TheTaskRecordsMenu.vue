<template>

    <!--attract: Меню стегальной машины -->
    <div class="flex">

        <!-- attract: Добавление рулона. Показываем только в режиме просмотра -->
        <AppLabelMultiLine
            v-if="!fabricsStore.globalEditMode"
            :text="['Добавить', 'рулон']"
            align="center"
            class="cursor-pointer"
            height="h-[31px]"
            type="success"
            width="w-[170px]"
            @click="addRoll"

        />

        <!-- attract: Оптимизация трудозатрат. Показываем только в режиме просмотра -->
        <AppLabelMultiLine
            v-if="!fabricsStore.globalEditMode"
            :text="['Оптимизировать', 'трудозатраты']"
            align="center"
            class="cursor-pointer"
            height="h-[31px]"
            type="warning"
            width="w-[170px]"
            @click="optimizeLabor"
        />

        <AppLabelMultiLine
            :text="['Текущий рисунок:', 'Ж25']"
            align="center"
            height="h-[31px]"
            type="info"
            width="w-[170px]"
        />

        <AppLabelMultiLine
            :text="['Общие трудозатраты', '08ч. 59м. 59с.']"
            align="center"
            height="h-[31px]"
            type="danger"
            width="w-[200px]"
        />

        <!-- attract: Режим выбора ПС. Показываем только в режиме просмотра -->
        <AppCheckbox
            v-if="!fabricsStore.globalEditMode"
            id="active"
            :checkboxData="checkboxData"
            dir="horizontal"
            inputType="radio"
            legend="Выбор ПС:"
            type="secondary"
            width="w-[270px]"
            @checked="changeFabricsMode"
        />

    </div>

</template>

<script setup>
import {reactive} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {storeToRefs} from 'pinia';
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'

const emits = defineEmits(['addRoll', 'optimizeLabor'])

// attract: Получаем данные из хранилища
const fabricsStore = useFabricsStore()
// глобальный режим редактирования + глобальный режим выбора ПС
// let {globalEditMode, globalFabricsMode} = storeToRefs(fabricsStore)

// console.log(globalFabricsMode.value)
// const fabricsMode



// attract: Определяем объект с данными чекбокса (доступность тканей - основные или все доступные)
const checkboxData = reactive({
    name: 'Availability',
    data: [
        {id: 1, name: 'Основные', checked: fabricsStore.globalFabricsMode},
        {id: 2, name: 'Все доступные', checked: !fabricsStore.globalFabricsMode},
    ]
})



// attract: Обрабатываем клик по чек боксу (Основные или все доступные)
const changeFabricsMode = (item) => {
    fabricsStore.globalFabricsMode = item.id === 1
    // console.log('menu: ', fabricsStore.globalFabricsMode)
    checkboxData.data[0].checked = fabricsStore.globalFabricsMode
    checkboxData.data[1].checked = !fabricsStore.globalFabricsMode
    // console.log(item)
}

// attract: Обрабатываем клик по кнопке "Добавить рулон"
const addRoll = () => {
    console.log('add roll')
    emits('addRoll')
}

// attract: Обрабатываем клик по кнопке "Оптимизировать трудозатраты"
const optimizeLabor = () => {
    emits('optimizeLabor')
    console.log('optimize labor')
}


</script>

<style scoped>

</style>
