<template>


    <div class="flex">

        <!-- __ Общая длина ткани -->
        <AppLabelMultiLine
            :text="['Длина ткани:', totalLength().toFixed(2)]"
            :text-size="HEADER_TEXT_SIZE"
            :width="WIDTH"
            align="center"
            type="primary"
        />

        <!-- __ Общая длина ПС -->
        <AppLabelMultiLine
            :text="['Длина ПС:', totalFabricLength().toFixed(2)]"
            :text-size="HEADER_TEXT_SIZE"
            :width="WIDTH"
            align="center"
            type="primary"
        />

        <!-- __ Общие трудозатраты -->
        <AppLabelMultiLine
            :text="['Трудозатраты:', formatTimeWithLeadingZeros(totalProductivityAmount(), 'hour')]"
            :text-size="HEADER_TEXT_SIZE"
            :width="WIDTH"
            align="center"
            type="primary"
        />


        <!-- __ Кнопки калькулятора -->
        <div class="flex">
            <div v-for="(item, index) in render" :key="index">
                <AppLabelMultiLine
                    v-if="item.show"
                    :align="item.align"
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


    <!--&lt;!&ndash; __ Позиция (№ по порядку) &ndash;&gt;-->
    <!--<AppLabelMultiLine-->
    <!--    v-if="render.selectAll.show"-->
    <!--    :align="render.selectAll.align"-->
    <!--    :class="render.selectAll.class"-->
    <!--    :text="render.selectAll.header"-->
    <!--    :text-size="render.selectAll.headerTextSize"-->
    <!--    :title="render.selectAll.title"-->
    <!--    :type="render.selectAll.type()"-->
    <!--    :width="render.selectAll.width"-->
    <!--    @click="render.selectAll.click()"-->
    <!--/>-->

</template>

<script setup>
import { reactive, ref } from 'vue'
import { FABRIC_MACHINES, FABRIC_ROLL_STATUS } from '@/app/constants/fabrics.ts'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date.js'

const props = defineProps({
    rolls: {
        type: Array,
        required: false,
        default: []
    },
    machine: {
        type: Object,
        required: false,
        default: () => FABRIC_MACHINES.AMERICAN,
        validator: (machine) => [
            FABRIC_MACHINES.AMERICAN.ID,
            FABRIC_MACHINES.GERMAN.ID,
            FABRIC_MACHINES.CHINA.ID,
            FABRIC_MACHINES.KOREAN.ID,
        ].includes(machine.ID)
    },
})

console.log('props.rolls: ', props.rolls)

// __ Передача событий
const emit = defineEmits(['calculatorActionHandler'])

// __ Обертка над кликом
const clickHandler = (handler) => {
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

const render = ref({
    // totalLength: {
    //     header: ['Длина ткани:', totalLength().toString()],
    //     title: 'Выделить все',
    //     show: true,
    //     width: WIDTH,
    //     headerTextSize: HEADER_TEXT_SIZE,
    //     align: ALIGN,
    //     class: CLASS,
    //     type: (roll) => 'primary',
    //     data: (roll) => 'data',
    //     click: () => {},
    // },
    selectAll: {
        header: ['Выделить', 'все'],
        title: 'Выделить все',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        align: ALIGN,
        class: CLASS,
        type: (roll) => 'success',
        data: (roll) => 'data',
        click: () => clickHandler(item => item.isCalc = true),
    },
    unSelectAll: {
        header: ['Снять', 'выделение'],
        title: 'Снять выделение',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        align: ALIGN,
        type: (roll) => 'danger',
        data: (roll) => 'data',
        click: () => clickHandler(item => item.isCalc = false),
    },
    invertSelection: {
        header: ['Инвертировать', 'выделение'],
        title: 'Инвертировать выделение',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        align: ALIGN,
        type: (roll) => 'orange',
        data: (roll) => 'data',
        click: () => clickHandler(item => item.isCalc = !item.isCalc),
    },
    selectCreated: {
        header: ['Статус', 'Создано'],
        title: 'Статус Создано',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        align: ALIGN,
        type: (roll) => FABRIC_ROLL_STATUS.CREATED.TYPE,
        data: (roll) => 'data',
        click: () => clickHandler(item => item.isCalc = item.status === FABRIC_ROLL_STATUS.CREATED.CODE),
    },
    selectDone: {
        header: ['Статус', 'Выполнено'],
        title: 'Статус Выполнено',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        align: ALIGN,
        type: (roll) => FABRIC_ROLL_STATUS.DONE.TYPE,
        data: (roll) => 'data',
        click: () => clickHandler(item => item.isCalc = item.status === FABRIC_ROLL_STATUS.DONE.CODE),
    },
    selectFalse: {
        header: ['Статус', 'Не выполнено'],
        title: 'Статус Не выполнено',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        align: ALIGN,
        type: (roll) => FABRIC_ROLL_STATUS.FALSE.TYPE,
        data: (roll) => 'data',
        click: () => clickHandler(item => item.isCalc = item.status === FABRIC_ROLL_STATUS.FALSE.CODE),
    },
    selectCancelled: {
        header: ['Статус', 'Отменено'],
        title: 'Статус Отменено',
        show: true,
        width: WIDTH,
        headerTextSize: HEADER_TEXT_SIZE,
        align: ALIGN,
        type: (roll) => FABRIC_ROLL_STATUS.CANCELLED.TYPE,
        data: (roll) => 'data',
        click: () => clickHandler(item => item.isCalc = item.status === FABRIC_ROLL_STATUS.CANCELLED.CODE),
    },
})


</script>

<style scoped>

</style>
