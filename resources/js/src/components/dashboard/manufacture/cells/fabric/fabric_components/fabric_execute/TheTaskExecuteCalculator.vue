<template>

    <div class="flex">

        <!-- __ Общая длина ткани -->
        <AppLabelMultiLine
            :align="ALIGN"
            :text="['Длина ткани:', totalLength().toFixed(2)]"
            :text-size="HEADER_TEXT_SIZE"
            :type="TYPE"
            :width="WIDTH"
        />

        <!-- __ Общая длина ПС -->
        <AppLabelMultiLine
            :align="ALIGN"
            :text="['Длина ПС:', totalFabricLength().toFixed(2)]"
            :text-size="HEADER_TEXT_SIZE"
            :type="TYPE"
            :width="WIDTH"
        />

        <!-- __ Общие трудозатраты -->
        <AppLabelMultiLine
            :align="ALIGN"
            :text="['Трудозатраты:', formatTimeWithLeadingZeros(totalProductivityAmount(), 'hour')]"
            :text-size="HEADER_TEXT_SIZE"
            :type="TYPE"
            :width="WIDTH"
        />

        <!-- __ Кнопки калькулятора -->
        <div class="flex">
            <div v-for="(item, index) in render" :key="index">
                <AppLabelMultiLine
                    v-if="item.show && item.click"
                    :align="item.headerAlign"
                    :class="item.class"
                    :text="item.header"
                    :text-size="item.headerTextSize"
                    :title="item.title"
                    :type="item.type()"
                    :width="item.width"
                    @click="item.click()"
                />
            </div>
        </div>
    </div>

</template>

<script lang="ts" setup>
// import { ref, /*reactive*/ } from 'vue'

import type { IRenderData, MachineUnionType, /*IFabricMachine*/ } from '@/types'

import { FABRIC_MACHINES, FABRIC_ROLL_STATUS } from '@/app/constants/fabrics.ts'

//@ts-ignore
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date.js'

import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'


interface IProps {
    rolls?: {
        rolls_exec: {
            isCalc: boolean
            status: string
            textile_length: number
            rate: number
            productivity: number
        }[]
    }[],
    machine?: MachineUnionType,
}

const props = withDefaults(defineProps<IProps>(), {
    rolls: () => ([]),
    machine: () => FABRIC_MACHINES.AMERICAN
})

/*
// const props = defineProps({
//     rolls: {
//         type: Array,
//         required: false,
//         default: () => ([])
//     },
//     machine: {
//         type: Object,
//         required: false,
//         default: () => FABRIC_MACHINES.AMERICAN,
//         validator: (machine) => [
//             FABRIC_MACHINES.AMERICAN.ID,
//             FABRIC_MACHINES.GERMAN.ID,
//             FABRIC_MACHINES.CHINA.ID,
//             FABRIC_MACHINES.KOREAN.ID,
//         ].includes(machine.ID)
//     },
// })
*/

// console.log('props.rolls: ', props.rolls)

// __ Передача событий

const emit = defineEmits<{
    (e: 'calculatorActionHandler', handler: (...args: any[]) => {}, machine: MachineUnionType): void
}>()
// const emit = defineEmits(['calculatorActionHandler'])


// __ Обертка над кликом
const clickHandler = (handler: (...args: any[]) => {}) => {
    emit('calculatorActionHandler', handler, props.machine)
}

// __ Общая длина длина ткани
const totalLength = () => {
    let totalLength = 0
    props.rolls.forEach(roll => {
        totalLength += roll.rolls_exec.reduce((acc, roll_exec) => roll_exec.isCalc ? acc + roll_exec.textile_length : acc, 0)
    })
    return totalLength
}

// __ Общая длина длина ПС
const totalFabricLength = () => {
    let totalFabricLength = 0
    props.rolls.forEach(roll => {
        totalFabricLength += roll.rolls_exec.reduce((acc, roll_exec) => roll_exec.isCalc ? acc + roll_exec.textile_length / roll_exec.rate : acc, 0)
    })
    return totalFabricLength
}

// __ Общие трудозатраты
const totalProductivityAmount = () => {
    let totalProductivityAmount = 0
    props.rolls.forEach(roll => {
        totalProductivityAmount += roll.rolls_exec.reduce((acc, roll_exec) => roll_exec.isCalc ? acc + roll_exec.textile_length / roll_exec.productivity : acc, 0)
    })
    return totalProductivityAmount
}


// __ Рендер
const WIDTH = 'w-[100px]'
const HEADER_TEXT_SIZE = 'mini'
const ALIGN = 'center'
const CLASS = 'cursor-pointer'
const TYPE = 'primary'

const render: IRenderData = {
    selectAll: {
        header: ['Выделить', 'все'],
        title: 'Выделить все',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: ALIGN,
        class: CLASS,
        type: () => 'success',
        data: () => 'data',
        click: () => clickHandler(item => item.isCalc = true),
    },
    unSelectAll: {
        header: ['Снять', 'выделение'],
        title: 'Снять выделение',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: ALIGN,
        type: () => 'danger',
        data: () => 'data',
        click: () => clickHandler(item => item.isCalc = false),
    },
    invertSelection: {
        header: ['Инвертировать', 'выделение'],
        title: 'Инвертировать выделение',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: ALIGN,
        type: () => 'orange',
        data: () => 'data',
        click: () => clickHandler(item => item.isCalc = !item.isCalc),
    },
    selectCreated: {
        header: ['Статус', 'Создано'],
        title: 'Статус Создано',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: ALIGN,
        type: () => FABRIC_ROLL_STATUS.CREATED.TYPE,
        data: () => 'data',
        click: () => clickHandler(item => item.isCalc = item.status === FABRIC_ROLL_STATUS.CREATED.CODE),
    },
    selectDone: {
        header: ['Статус', 'Выполнено'],
        title: 'Статус Выполнено',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: ALIGN,
        type: () => FABRIC_ROLL_STATUS.DONE.TYPE,
        data: () => 'data',
        click: () => clickHandler(item => item.isCalc = item.status === FABRIC_ROLL_STATUS.DONE.CODE),
    },
    selectFalse: {
        header: ['Статус', 'Не выполнено'],
        title: 'Статус Не выполнено',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: ALIGN,
        type: () => FABRIC_ROLL_STATUS.FALSE.TYPE,
        data: () => 'data',
        click: () => clickHandler(item => item.isCalc = item.status === FABRIC_ROLL_STATUS.FALSE.CODE),
    },
    selectCancelled: {
        header: ['Статус', 'Отменено'],
        title: 'Статус Отменено',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        headerAlign: ALIGN,
        type: () => FABRIC_ROLL_STATUS.CANCELLED.TYPE,
        data: () => 'data',
        click: () => clickHandler(item => item.isCalc = item.status === FABRIC_ROLL_STATUS.CANCELLED.CODE),
    },
}


</script>

<style scoped>

</style>
