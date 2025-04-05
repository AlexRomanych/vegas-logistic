<template>

    <!--attract: Меню стегальной машины -->
    <div class="flex">

        <AppLabelMultiLine
            :text="['Добавить', 'рулон']"
            align="center"
            class="cursor-pointer"
            height="h-[31px]"
            type="success"
            width="w-[170px]"
            @click="addRoll"

        />

        <AppLabelMultiLine
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

        <AppCheckbox
            id="active"
            :checkboxData="checkboxData"
            dir="horizontal"
            inputType="radio"
            legend="Выбор ПС:"
            type="secondary"
            width="w-[270px]"
            @checked="checkedHandler"
        />

    </div>

</template>

<script setup>

import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'

// attract: Определяем объект с данными чекбокса (доступность тканей - основные или все доступные)
const checkboxData = {
    name: 'Availability',
    data: [
        {id: 1, name: 'Основные', checked: true},
        {id: 2, name: 'Все доступные', checked: false},
    ]
}

const emits = defineEmits(['addRoll', 'optimizeLabor'])

// Обрабатываем клик по чек боксу
const checkedHandler = (item) => console.log(item)

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
