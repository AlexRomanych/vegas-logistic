<template>

    <!--__ Меню стегальной машины -->
    <div class="flex">

        <!-- __ Добавление рулона. Показываем только в режиме просмотра + если у СЗ соответсвующий статус-->
        <AppLabelMultiLine
            v-if="!globalEditMode && getFunctionalByFabricTaskStatus(taskStatus)"
            :text="['Добавить', 'рулон']"
            align="center"
            class="cursor-pointer"
            height="h-[31px]"
            type="success"
            width="w-[170px]"
            @click="addRoll"

        />

        <!-- __ Оптимизировать трудозатраты -->
        <AppLabelMultiLine
            v-if="!globalEditMode && getFunctionalByFabricTaskStatus(taskStatus)"
            :text="['Оптимизировать', 'трудозатраты']"
            align="center"
            class="cursor-pointer"
            height="h-[31px]"
            type="warning"
            width="w-[170px]"
            @click="optimizeLabor"
        />

        <!-- __ Текущий рисунок -->
        <AppLabelMultiLine
            :text="['Текущий рисунок:', 'Ж25']"
            align="center"
            height="h-[31px]"
            type="info"
            width="w-[170px]"
        />

        <!-- __ Общие трудозатраты -->
        <AppLabelMultiLine
            :text="['Общие трудозатраты:', formatTimeWithLeadingZeros(totalProductivityAmount, 'hour')]"
            :type="totalProductivityAmount <= FABRIC_WORKING_SHIFT_LENGTH ? 'success' : 'danger'"
            align="center"
            height="h-[31px]"
            width="w-[200px]"
        />

        <!-- __ Выбор режима ПС: Основной или Все доступные -->
        <AppCheckboxTS
            v-if="!globalEditMode && getFunctionalByFabricTaskStatus(taskStatus)"
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
            :text="['Сохранение', 'порядка рулонов']"
            align="center"
            class="cursor-pointer"
            height="h-[31px]"
            type="danger"
            width="w-[170px]"
            @click="saveRollsOrder"
        />


    </div>

</template>

<script lang="ts" setup>
import { reactive, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'

import type { ICheckboxDataItem, MachineUnionType, TaskStatusUnionType } from '@/types'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    FABRIC_MACHINES,
    FABRIC_WORKING_SHIFT_LENGTH,
    // FABRIC_TASK_STATUS
} from '@/app/constants/fabrics.js'

import { getFunctionalByFabricTaskStatus, } from '@/app/helpers/manufacture/helpers_fabric.js'

import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date.js'

import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppCheckboxTS from '@/components/ui/checkboxes/AppCheckboxTS.vue'


interface IProps {
    taskStatus: TaskStatusUnionType
    machine?: MachineUnionType
}

const props = withDefaults(defineProps<IProps>(), {
    machine: () => FABRIC_MACHINES.AMERICAN
})

const emits = defineEmits<{
    (e: 'addRoll'): void,
    (e: 'optimizeLabor'): void,
    (e: 'saveRollsOrder'): void
}>()


// __ Получаем данные из хранилища
const fabricsStore = useFabricsStore()

// __ Глобальный режим редактирования + глобальный режим выбора ПС
const {globalEditMode, globalFabricsMode, globalRollsIndexes} = storeToRefs(fabricsStore)

// __ Определяем объект с данными чекбокса (доступность тканей - основные или все доступные)
const checkboxData = reactive({
    name: 'availability',
    data: [
        {id: 1, name: 'Основные', checked: globalEditMode.value},
        {id: 2, name: 'Все доступные', checked: !globalEditMode.value},
    ]
})

// __ Общие трудозатраты
const getTotalProductivityAmount = () => {
    const productivityAmounts = fabricsStore.globalTaskProductivity[props.machine.TITLE]
    // console.log(productivityAmounts)
    return productivityAmounts.reduce((acc, cur) => acc + cur, 0)
}
const totalProductivityAmount = ref(getTotalProductivityAmount())


// __ Обрабатываем клик по чек боксу (Основные или все доступные)
const changeFabricsMode = (item: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(item)) {
        globalFabricsMode.value = item.id === 1
        checkboxData.data[0].checked = globalFabricsMode.value
        checkboxData.data[1].checked = !globalFabricsMode.value
        // console.log(item)
        // console.log('menu: ', globalEditMode.value)
    }
}

// __ Обрабатываем клик по кнопке "Добавить рулон"
const addRoll = () => {
    // console.log(globalRollsIndexes.value)
    if (globalRollsIndexes.value.includes(0)) return
    // console.log('add roll')
    emits('addRoll')
}

// __ Обрабатываем клик по кнопке "Оптимизировать трудозатраты"
const optimizeLabor = () => {
    // console.log('optimize labor')
    emits('optimizeLabor')
}

// __ Обрабатываем клик по кнопке Сохранить порядок рулонов
const saveRollsOrder = () => {
    // console.log('save Rolls Order')
    emits('saveRollsOrder')
}


// __ Отслеживаем изменения в хранилище по трудозатратам
watch(
    () => fabricsStore.globalTaskProductivity,
    () => {
        totalProductivityAmount.value = getTotalProductivityAmount()
        // console.log(totalProductivityAmount.value)
    },
    {deep: true}
)

</script>

<style scoped>

</style>
