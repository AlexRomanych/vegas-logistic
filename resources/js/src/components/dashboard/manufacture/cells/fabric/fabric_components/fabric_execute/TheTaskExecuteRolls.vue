<template>

    <!-- attract: Заголовок таблицы выполняемых рулонов -->
    <TheTaskExecuteRollsHeader
        :rollsRender="rollsRender"
    />

    <div v-for="(roll, index) in rolls" :key="index">

        <div v-for="(roll_exec) in roll.rolls_exec" :key="roll_exec.id">

            <div
                class="cursor-pointer"
                :class="roll_exec.active ? 'pt-0.5 pb-0.5 bg-blue-400 rounded-lg' : ''">

                <!-- attract: Сам рулон  -->
                <TheTaskExecuteRoll

                    :roll_exec="roll_exec"
                    :rollsRender="rollsRender"
                    @click="changeActiveRoll(roll_exec)"
                />

            </div>

        </div>

    </div>

</template>

<script setup>
import {reactive, watch} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_ROLL_STATUS, FABRIC_ROLL_STATUS_LIST} from '/resources/js/src/app/constants/fabrics.js'

import {formatTimeWithLeadingZeros} from '/resources/js/src/app/helpers/helpers_date.js'

import TheTaskExecuteRollsHeader
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteRollsHeader.vue'
import TheTaskExecuteRoll
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteRoll.vue'


import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'


const props = defineProps({
    rolls: {
        type: Array,
        required: false,
        default: []
    },
    execute: {                      // Режим отображения (в режиме демонстрации или в режиме выполнения)
        type: Boolean,
        required: false,
        default: true
    }
})



// attract: Сортируем рулоны по позиции
const sortExecRollsByPosition = () => {
    props.rolls.forEach((roll) => {
        roll.rolls_exec.sort((a, b) => a.position - b.position)
    })
}
sortExecRollsByPosition()

// console.log(props.rolls)
// attract: Добавляем флаг активности
const resetActiveFlag = () => {
    props.rolls.forEach((roll) => {
        roll.rolls_exec.forEach((roll_exec) => {
            roll_exec['active'] = false
        })
    })
}
resetActiveFlag()

// attract: Устанавливаем активным первый элемент - активный рулон
let activeRoll = props.rolls[0].rolls_exec[0]
activeRoll['active'] = true // устанавливаем активным первый элемент

console.log('props.rolls: ', props.rolls)


const FABRIC_ROLL_STATUS_ARRAY = Object.values(FABRIC_ROLL_STATUS_LIST);
// console.log(FABRIC_ROLL_STATUS_ARRAY)

// attract: Получаем данные из хранилища по ПС
const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory

// attract: Определяем, что в рулонах будет полная инфа
fabricsStore.globalExecuteRollsInfo = true

// attract: Получаем ПС по ID
// const getFabricById = (fabric_id) => fabrics.find((fabric) => fabric.id === fabric_id)

// attract: Получаем тип раскраски в зависимости от статуса выполнения рулона
const getTypeByStatus = (roll_exec) => {
    return 'dark'
}

// attract: Получаем тип шапки таблицы
const getHeaderType = () => 'primary'

// attract: Задаем глобальный объект для унификации отображения рулонов
const rollsRender = reactive({
    rollNumber: {width: 'w-[60px]', show: true, title: '№ рулона', data: (roll_exec) => roll_exec.id.toString()},
    fabricName: {
        width: 'w-[270px]',
        show: true,
        title: 'Название ПС',
        data: (roll_exec) => fabrics.find((fabric) => fabric.id === roll_exec.fabric_id).display_name
    },
    textileLength: {
        width: 'w-[50px]',
        show: true,
        title: 'Средняя длина ткани, м.п.',
        data: (roll_exec) => roll_exec.textile_length.toFixed(2)
    },
    rollsAmount: {width: 'w-[30px]', show: false, title: 'Кол-во рулонов, шт.', data: () => '1'},
    productivity: {
        width: 'w-[90px]',
        show: true,
        title: 'Средние трудозатраты, ч.',
        data: (roll_exec) => formatTimeWithLeadingZeros(roll_exec.textile_length / roll_exec.productivity, 'hour')
    },
    description: {
        width: props.execute ? 'w-[200px]' : 'w-[100px]',
        show: true,
        title: 'Комментарий',
        data: (roll_exec) => roll_exec.descr
    },
    status: {
        width: 'w-[70px]',
        show: true,
        title: 'Статус',
        data: (roll_exec) => FABRIC_ROLL_STATUS_ARRAY[roll_exec.status].TITLE
    },
    startAt: {
        width: 'w-[110px]',
        show: props.execute,
        title: 'Начало стегания рулона',
        data: (roll_exec) => '16.04.2023 10:59'
    },
    finishAt: {
        width: 'w-[110px]',
        show: props.execute,
        title: 'Окончание стегания рулона',
        data: (roll_exec) => '16.04.2023 12:38'
    },
    rollTime: {width: 'w-[90px]', show: props.execute, title: 'Время стегания', data: (roll_exec) => '01ч. 59м. 59с.'},
    finishBy: {width: 'w-[110px]', show: props.execute, title: 'Ответственный', data: (roll_exec) => 'Сидорук И.И.'},
})

// attract: Обработчик активного рулона (подсвечиваем активный рулон)
const changeActiveRoll = (roll_exec) => {
    resetActiveFlag()
    activeRoll = roll_exec
    activeRoll.active = true
}

const toggleFabricExecuteInfo = () => {
    rollsRender.description.show = !rollsRender.description.show
    rollsRender.finishBy.show = !rollsRender.finishBy.show
}

watch(()  => fabricsStore.globalExecuteRollsInfo, (newValue) => {
    toggleFabricExecuteInfo(newValue)
})

</script>

<style scoped>

</style>
