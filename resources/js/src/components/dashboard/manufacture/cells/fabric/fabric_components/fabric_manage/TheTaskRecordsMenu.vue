<template>

    <!--attract: Меню стегальной машины -->
    <div class="flex">

        <!-- attract: Добавление рулона. Показываем только в режиме просмотра + если у СЗ соответсвующий статус-->
        <AppLabelMultiLine
            v-if="!fabricsStore.globalEditMode && getFunctionalByFabricTaskStatus(taskStatus)"
            :text="['Добавить', 'рулон']"
            align="center"
            class="cursor-pointer"
            height="h-[31px]"
            type="success"
            width="w-[170px]"
            @click="addRoll"

        />

        <!-- attract: Оптимизировать трудозатраты -->
        <AppLabelMultiLine
            v-if="!fabricsStore.globalEditMode && getFunctionalByFabricTaskStatus(taskStatus)"
            :text="['Оптимизировать', 'трудозатраты']"
            align="center"
            class="cursor-pointer"
            height="h-[31px]"
            type="warning"
            width="w-[170px]"
            @click="optimizeLabor"
        />

        <!-- attract: Текущий рисунок -->
        <AppLabelMultiLine
            :text="['Текущий рисунок:', 'Ж25']"
            align="center"
            height="h-[31px]"
            type="info"
            width="w-[170px]"
        />

        <!-- attract: Общие трудозатраты -->
        <AppLabelMultiLine
            :text="['Общие трудозатраты:', formatTimeWithLeadingZeros(totalProductivityAmount, 'hour')]"
            :type="totalProductivityAmount <= FABRIC_WORKING_SHIFT_LENGTH ? 'success' : 'danger'"
            align="center"
            height="h-[31px]"
            width="w-[200px]"
        />

        <!-- __ Выбор режима ПС: Основной или Все доступные -->
        <AppCheckbox
            v-if="!fabricsStore.globalEditMode && getFunctionalByFabricTaskStatus(taskStatus)"
            id="active"
            :checkboxData="checkboxData"
            dir="horizontal"
            inputType="radio"
            legend="Выбор ПС:"
            type="secondary"
            width="w-[270px]"
            @checked="changeFabricsMode"
        />

        <!-- __ Сохранить состояние порядка рулонов -->
        <AppLabelMultiLine
            v-if="fabricsStore.globalOrderManageChangeFlag"
            :text="['Сохранить', 'порядок рулонов']"
            align="center"
            class="cursor-pointer"
            height="h-[31px]"
            type="danger"
            width="w-[170px]"
            @click="saveRollsOrder"
        />



    </div>

</template>

<script setup>
import {reactive, ref, watch} from 'vue'

import {useFabricsStore} from '@/stores/FabricsStore.js'

import {
    FABRIC_MACHINES,
    FABRIC_TASK_STATUS,
    FABRIC_WORKING_SHIFT_LENGTH
} from '@/app/constants/fabrics.js'

import {getFunctionalByFabricTaskStatus,} from '@/app/helpers/manufacture/helpers_fabric.js'

import {formatTimeWithLeadingZeros} from '@/app/helpers/helpers_date.js'

import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppCheckbox from '@/components/ui/checkboxes/AppCheckbox.vue'

const props = defineProps({
    machine: {
        type: Object,
        required: false,
        default: () => FABRIC_MACHINES.AMERICAN,
    //     validator: (machine) => [
    //         FABRIC_MACHINES.AMERICAN,
    //         FABRIC_MACHINES.GERMAN,
    //         FABRIC_MACHINES.CHINA,
    //         FABRIC_MACHINES.KOREAN,
    //     ].includes(machine)
    },
    taskStatus: {
        type: Number,
        required: true,
        validator: (taskStatus) => [
            FABRIC_TASK_STATUS.UNKNOWN.CODE,
            FABRIC_TASK_STATUS.CREATED.CODE,
            FABRIC_TASK_STATUS.PENDING.CODE,
            FABRIC_TASK_STATUS.RUNNING.CODE,
            FABRIC_TASK_STATUS.DONE.CODE,
        ].includes(taskStatus)
    }
})

// console.log('menu: ', props.machine)

const emits = defineEmits(['addRoll', 'optimizeLabor', 'saveRollsOrder'])

// attract: Получаем данные из хранилища
const fabricsStore = useFabricsStore()
// глобальный режим редактирования + глобальный режим выбора ПС
// let {globalEditMode, globalFabricsMode} = storeToRefs(fabricsStore)

// console.log(globalFabricsMode.value)
// const fabricsMode

// attract: Общие трудозатраты
const getTotalProductivityAmount = () => {
    const productivityAmounts = fabricsStore.globalTaskProductivity[props.machine.TITLE]
    // console.log(productivityAmounts)

    return productivityAmounts.reduce((acc, cur) => acc + cur, 0)
}
const totalProductivityAmount = ref(getTotalProductivityAmount())

// attract: Отслеживаем изменения в хранилище по трудозатратам
watch(
    () => fabricsStore.globalTaskProductivity,
    () => {
        totalProductivityAmount.value = getTotalProductivityAmount()
        // console.log(totalProductivityAmount.value)
    },
    {deep: true}
)

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
    console.log(fabricsStore.globalRollsIndexes)
    if (fabricsStore.globalRollsIndexes.includes(0)) return
    // console.log('add roll')
    emits('addRoll')
}

// attract: Обрабатываем клик по кнопке "Оптимизировать трудозатраты"
const optimizeLabor = () => {
    emits('optimizeLabor')
    // console.log('optimize labor')
}


// __ Обрабатываем клик по кнопке Сохранить порядок рулонов
const saveRollsOrder = () => {
    emits('saveRollsOrder')
    // console.log('save Rolls Order')
}


// watch(() => fabricsStore.globalFabricsMode, () => {})



</script>

<style scoped>

</style>
