<template>

    <!--__ Меню стегальной машины -->
    <div class="flex">

        <!-- __ Добавление рулона. Показываем только в режиме просмотра + если у СЗ соответсвующий статус-->
        <AppLabelMultiLineTS
            v-if="render.addRoll.show && !globalEditMode && getFunctionalByFabricTaskStatus(taskStatus)"
            :align="render.addRoll.headerAlign"
            :height="render.addRoll.height"
            :text="render.addRoll.header"
            :text-size="render.addRoll.headerTextSize"
            :type="typeof render.addRoll.type === 'function' ? render.addRoll.type() : render.addRoll.type"
            :width="render.addRoll.width"
            class="cursor-pointer"
            @click="render.addRoll.click"
        />

        <!-- __ Оптимизировать трудозатраты -->
        <AppLabelMultiLineTS
            v-if="render.optimizeLabor.show && !globalEditMode && getFunctionalByFabricTaskStatus(taskStatus)"
            :align="render.optimizeLabor.headerAlign"
            :height="render.optimizeLabor.height"
            :text="render.optimizeLabor.header"
            :text-size="render.optimizeLabor.headerTextSize"
            :type="typeof render.optimizeLabor.type === 'function' ? render.optimizeLabor.type() : render.optimizeLabor.type"
            :width="render.optimizeLabor.width"
            class="cursor-pointer"
            @click="render.optimizeLabor.click"
        />

        <!-- __ Текущий рисунок -->
        <AppLabelMultiLineTS
            v-if="render.currentPicture.show"
            :align="render.currentPicture.headerAlign"
            :height="render.currentPicture.height"
            :text="['Переходящий рис.:', currentPicture]"
            :text-size="render.currentPicture.headerTextSize"
            :type="typeof render.currentPicture.type === 'function' ? render.currentPicture.type() : render.currentPicture.type"
            :width="render.currentPicture.width"
        />

        <!-- __ Трудозатраты по рулонам -->
        <AppLabelMultiLineTS
            v-if="render.rollsProductivity.show"
            :align="render.rollsProductivity.headerAlign"
            :height="render.rollsProductivity.height"
            :text="['Время стежки:', '🕒' + formatTimeWithLeadingZeros(totalRollsProductivity, 'hour')]"
            :text-size="render.rollsProductivity.headerTextSize"
            :type="typeof render.rollsProductivity.type === 'function' ? render.rollsProductivity.type() : render.rollsProductivity.type"
            :width="render.rollsProductivity.width"
        />

        <!-- __ Трудозатраты по переналадкам -->
        <AppLabelMultiLineTS
            v-if="render.tuningProductivity.show"
            :align="render.tuningProductivity.headerAlign"
            :height="render.tuningProductivity.height"
            :text="['Время переналадки:','🕒' + formatTimeWithLeadingZeros(totalTuningProductivity, 'hour')]"
            :text-size="render.tuningProductivity.headerTextSize"
            :type="typeof render.tuningProductivity.type === 'function' ? render.tuningProductivity.type() : render.tuningProductivity.type"
            :width="render.tuningProductivity.width"
        />

        <!-- __ Общие трудозатраты -->
        <AppLabelMultiLineTS
            v-if="render.totalProductivity.show"
            :align="render.totalProductivity.headerAlign"
            :height="render.totalProductivity.height"
            :text="['Общее время:','🕒' + formatTimeWithLeadingZeros(totalProductivity, 'hour')]"
            :text-size="render.totalProductivity.headerTextSize"
            :type="typeof render.totalProductivity.type === 'function' ? render.totalProductivity.type() : render.totalProductivity.type"
            :width="render.totalProductivity.width"
        />

        <!-- __ Выбор режима ПС: Основной или Все доступные -->
        <AppCheckboxTS
            v-if="render.selectFabricMode.show && !globalEditMode && getFunctionalByFabricTaskStatus(taskStatus)"
            id="active"
            :checkboxData="checkboxData"
            :text-size="render.selectFabricMode.headerTextSize"
            :type="typeof render.selectFabricMode.type === 'function' ? render.selectFabricMode.type() : render.selectFabricMode.type"
            :width="render.selectFabricMode.width"
            dir="horizontal"
            inputType="radio"
            legend="Выбор ПС:"
            @checked="render.selectFabricMode.click"
        />

        <!-- __ Сохранить состояние порядка рулонов -->
        <AppLabelMultiLineTS
            v-if="render.saveOrder.show && fabricsStore.globalOrderManageChangeFlag"
            :align="render.saveOrder.headerAlign"
            :height="render.saveOrder.height"
            :text="render.saveOrder.header"
            :text-size="render.saveOrder.headerTextSize"
            :type="typeof render.saveOrder.type === 'function' ? render.saveOrder.type() : render.saveOrder.type"
            :width="render.saveOrder.width"
            class="cursor-pointer"
            @click="render.saveOrder.click"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed, reactive, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'

import type {
    ICheckboxData,
    ICheckboxDataItem, IGlobalProductivity, IHorizontalAlign, IRenderData,
    ITaskItem,
    TaskStatusUnionCodeType,
} from '@/types'
import type { IFontsType } from '@/app/constants/fontSizes.ts'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    FABRIC_DEFAULT_TUNING_TIME,
    FABRIC_MACHINES,
    FABRIC_WORKING_SHIFT_LENGTH, type IConstFabricMachine,
} from '@/app/constants/fabrics.js'

import {
    getFunctionalByFabricTaskStatus,
    getTotalProductivityGlobal,
    getTotalRollProductivityGlobal,
    getTotalTunningProductivityGlobal,
} from '@/app/helpers/manufacture/helpers_fabric.js'

import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date.js'

import AppCheckboxTS from '@/components/ui/checkboxes/AppCheckboxTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'


interface IProps {
    task: ITaskItem
    machine?: IConstFabricMachine
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

// __ Определяем глобальный статус СЗ
const taskStatus = computed<TaskStatusUnionCodeType>(() => props.task.common.status)

// __ Определяем объект с данными чекбокса (доступность тканей - основные или все доступные)
const checkboxData: ICheckboxData = reactive({
    name: 'availability',
    data: [
        {id: 1, name: 'Основные', checked: globalFabricsMode.value},
        {id: 2, name: 'Все доступные', checked: !globalFabricsMode.value},
    ]
})


// __ Трудозатраты по рулонам
const totalRollsProductivity = ref(getTotalRollProductivityGlobal(fabricsStore.globalTaskProductivity as unknown as IGlobalProductivity, props.machine))
/*
const totalRollsProductivity =
    computed(() => getTotalRollsProductivity(props.task.machines[props.machine.TITLE].rolls))
*/

// __ Трудозатраты по переналадкам
const totalTuningProductivity = ref(getTotalTunningProductivityGlobal(fabricsStore.globalTaskProductivity as unknown as IGlobalProductivity, props.machine))
/*
const totalTuningProductivity =
    computed(() => getTotalTuningProductivity(props.task.machines[props.machine.TITLE].rolls))
*/

// __ Общие трудозатраты
const totalProductivity = ref(getTotalProductivityGlobal(fabricsStore.globalTaskProductivity as unknown as IGlobalProductivity, props.machine))
/*
const totalProductivity =
    computed(() => getTotalProductivity(props.task.machines[props.machine.TITLE].rolls))
*/

// __ Ошибка в трудозатратах по переналадкам (отсутствует время переналадки)
const totalTuningProductivityHasError = computed(() => props.task.machines[props.machine.TITLE].rolls.some(roll => roll.productivity === FABRIC_DEFAULT_TUNING_TIME))


// __ Текущий рисунок
const currentPicture = computed(() => {
    if (props.task.machines[props.machine.TITLE].lastExecRoll) {
        const fabric = fabricsStore.fabricsMemory.find(fabric => fabric.id === props.task.machines[props.machine.TITLE].lastExecRoll!.fabric_id)
        if (fabric) return fabric.picture.name
    }
    return 'н/д'
})


// __ Определяем объект с данными для рендера кнопок
const WIDTH = 'w-[150px]'
const HEIGHT = 'h-[31px]'
const HEADER_TEXT_SIZE: IFontsType = 'small'
const HEADER_ALIGN: IHorizontalAlign = 'center'

const render: IRenderData = reactive({
    addRoll: {
        show: true,
        header: ['Добавить', 'рулон'],
        width: WIDTH,
        height: HEIGHT,
        type: 'success',
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        click: () => addRoll()
    },
    optimizeLabor: {
        show: true,
        header: ['Оптимизировать', 'трудозатраты'],
        width: WIDTH,
        height: HEIGHT,
        type: () => totalTuningProductivityHasError.value ? 'danger' : 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        click: () => optimizeLabor()
    },
    currentPicture: {
        show: true,
        header: ['Переходящий рис.:', 'Ж25'],
        width: WIDTH,
        height: HEIGHT,
        type: 'info',
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN
    },
    rollsProductivity: {
        show: true,
        header: ['Время стежки:', ''],
        // header: ['Время стежки:', formatTimeWithLeadingZeros(totalRollsProductivity.value, 'hour')],
        width: WIDTH,
        height: HEIGHT,
        type: () => totalRollsProductivity.value <= FABRIC_WORKING_SHIFT_LENGTH ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN
    },
    tuningProductivity: {
        show: true,
        header: ['Время переналадки:', ''],
        // header: ['Время переналадки:', formatTimeWithLeadingZeros(totalTuningProductivity.value, 'hour')],
        width: WIDTH,
        height: HEIGHT,
        type: () => totalTuningProductivityHasError.value ? 'danger' : 'stone',
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN
    },
    totalProductivity: {
        show: true,
        header: ['Общее время:', ''],
        // header: ['Общее время:', formatTimeWithLeadingZeros(totalProductivity.value, 'hour')],
        width: WIDTH,
        height: HEIGHT,
        type: () => totalProductivity.value <= FABRIC_WORKING_SHIFT_LENGTH ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN
    },
    selectFabricMode: {
        show: true,
        header: '',
        width: 'w-[270px]',
        height: HEIGHT,
        type: 'secondary',
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        click: (item) => changeFabricsMode(item)
    },
    saveOrder: {
        show: true,
        header: ['Сохранение', 'порядка рулонов'],
        width: WIDTH,
        height: HEIGHT,
        type: 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        click: () => saveRollsOrder()
    },
})


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
    console.log(globalRollsIndexes.value)
    if (globalRollsIndexes.value.includes(0)) return
    emits('addRoll')
}

// __ Обрабатываем клик по кнопке "Оптимизировать трудозатраты"
const optimizeLabor = () => {
    // let optimizeLaborError = null
    // if (totalTuningProductivityHasError.value) {
    //     optimizeLaborError = 'Не заданы все данные по переналадкам'
    // }
    emits('optimizeLabor')
}

// __ Обрабатываем клик по кнопке Сохранить порядок рулонов
const saveRollsOrder = () => {
    emits('saveRollsOrder')
}


// __ Отслеживаем изменения в хранилище по трудозатратам
watch(
    () => fabricsStore.globalTaskProductivity,
    () => {
        totalProductivity.value = getTotalProductivityGlobal(
            fabricsStore.globalTaskProductivity as unknown as IGlobalProductivity,
            props.machine
        )

        totalRollsProductivity.value = getTotalRollProductivityGlobal(
            fabricsStore.globalTaskProductivity as unknown as IGlobalProductivity,
            props.machine)

        totalTuningProductivity.value = getTotalTunningProductivityGlobal(
            fabricsStore.globalTaskProductivity as unknown as IGlobalProductivity,
            props.machine)
    },
    {deep: true, immediate: true}
)

</script>

<style scoped>

</style>
