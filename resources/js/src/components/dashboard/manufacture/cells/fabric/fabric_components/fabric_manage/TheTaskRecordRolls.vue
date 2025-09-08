<template>

    <!-- hr----------------------------------------------------------------------------------------- -->
    <div class="flex">

        <!-- __ Позиция (№ по порядку) -->
        <AppLabelMultiLine
            v-if="rollsRender.position.show"
            :text="['№', 'п/п']"
            :type="getHeaderType()"
            :width="rollsRender.position.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Номер рулона (id записи) -->
        <AppLabelMultiLine
            v-if="rollsRender.rollNumber.show"
            :text="['Номер', 'рулона']"
            :type="getHeaderType()"
            :width="rollsRender.rollNumber.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Название ПС -->
        <AppLabelMultiLine
            v-if="rollsRender.fabricName.show"
            :text="['Полотно', 'стеганное']"
            :type="getHeaderType()"
            :width="rollsRender.fabricName.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Средняя длина ткани -->
        <AppLabelMultiLine
            v-if="rollsRender.textileLength.show"
            :text="['Длина', 'ткани']"
            :type="getHeaderType()"
            :width="rollsRender.textileLength.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Средняя длина ПС -->
        <AppLabelMultiLine
            v-if="rollsRender.fabricLength.show"
            :text="['Длина', 'ПС']"
            :type="getHeaderType()"
            :width="rollsRender.fabricLength.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Количество рулонов (всегда = 1) -->
        <AppLabelMultiLine
            v-if="rollsRender.rollsAmount.show"
            :text="['1', '']"
            :type="getHeaderType()"
            :width="rollsRender.rollsAmount.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Средние трудозатраты на рулон -->
        <AppLabelMultiLine
            v-if="rollsRender.productivity.show"
            :text="['Плановые', 'трудозатраты']"
            :type="getHeaderType()"
            :width="rollsRender.productivity.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Комментарий -->
        <AppLabelMultiLine
            v-if="rollsRender.description?.show"
            :text="['Комментарий', '']"
            :title="(typeof rollsRender.description?.title == 'function') ? rollsRender.description.title() : rollsRender.description.title"
            :type="getHeaderType()"
            :width="rollsRender.description.width"
            align="center"
            class="truncate"
            text-size="mini"
        />

        <!-- __ Статус -->
        <AppLabelMultiLine
            v-if="rollsRender.status.show"
            :text="['Статус', '']"
            :type="getHeaderType()"
            :width="rollsRender.status.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Начало -->
        <AppLabelMultiLine
            v-if="rollsRender.startAt.show"
            :text="['Начало', 'стегания']"
            :type="getHeaderType()"
            :width="rollsRender.startAt.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Окончание -->
        <AppLabelMultiLine
            v-if="rollsRender.finishAt.show"
            :text="['Окончание', 'стегания']"
            :type="getHeaderType()"
            :width="rollsRender.finishAt.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Время стегания -->
        <AppLabelMultiLine
            v-if="rollsRender.rollTime.show"
            :text="['Время', 'стегания']"
            :type="getHeaderType()"
            :width="rollsRender.rollTime.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Ответственный -->
        <AppLabelMultiLine
            v-if="rollsRender.finishBy.show"
            :text="['Ответственный', '']"
            :type="getHeaderType()"
            :width="rollsRender.finishBy.width"
            align="center"
            text-size="mini"
        />
    </div>
    <!-- hr----------------------------------------------------------------------------------------- -->


    <div v-for="(roll_exec) in rollsData" :key="roll_exec.id">
        <div class="flex">

            <!-- __ Позиция (№ по порядку) -->
            <AppLabel
                v-if="rollsRender.position.show"
                :text="rollsRender.position.data ? rollsRender.position.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.position.width"
                align="center"
                text-size="mini"
            />

            <!-- __ Номер рулона (id записи) -->
            <AppLabel
                v-if="rollsRender.rollNumber.show"
                :text="rollsRender.rollNumber.data ? rollsRender.rollNumber.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.rollNumber.width"
                align="center"
                text-size="mini"
            />

            <!-- __ Название ПС -->
            <AppLabel
                v-if="rollsRender.fabricName.show"
                :text="rollsRender.fabricName.data ? rollsRender.fabricName.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.fabricName.width"
                text-size="mini"
            />

            <!-- __ Средняя длина ткани -->
            <AppLabel
                v-if="rollsRender.textileLength.show"
                :text="rollsRender.textileLength.data ? rollsRender.textileLength.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.textileLength.width"
                align="center"
                text-size="mini"
            />

            <!-- __ Средняя длина ПС -->
            <AppLabel
                v-if="rollsRender.fabricLength.show"
                :text="rollsRender.fabricLength.data ? rollsRender.fabricLength.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.fabricLength.width"
                align="center"
                text-size="mini"
            />

            <!-- __ Количество рулонов (всегда = 1) -->
            <AppLabel
                v-if="rollsRender.rollsAmount.show"
                :text="rollsRender.rollsAmount.data ? rollsRender.rollsAmount.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.rollsAmount.width"
                align="center"
                text-size="mini"
            />

            <!-- __ Средние трудозатраты на рулон -->
            <AppLabel
                v-if="rollsRender.productivity.show"
                :text="rollsRender.productivity.data ? rollsRender.productivity.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.productivity.width"
                align="center"
                text-size="mini"
            />

            <!-- __ Комментарий -->
            <AppLabel
                v-if="rollsRender.description.show"
                :text="rollsRender.description.data ? rollsRender.description.data(roll_exec) : ''"
                :title="(typeof rollsRender.description?.title == 'function') ? rollsRender.description.title() : rollsRender.description.title"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.description.width"
                class="truncate"
                text-size="mini"
            />

            <!-- __ Статус -->
            <AppLabel
                v-if="rollsRender.status.show"
                :text="rollsRender.status.data ? rollsRender.status.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.status.width"
                align="center"
                text-size="mini"
            />

            <!-- __ Начало -->
            <AppLabel
                v-if="rollsRender.startAt.show"
                :text="rollsRender.startAt.data ? rollsRender.startAt.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.startAt.width"
                align="center"
                text-size="mini"
            />

            <!-- __ Окончание -->
            <AppLabel
                v-if="rollsRender.finishAt.show"
                :text="rollsRender.finishAt.data ? rollsRender.finishAt.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.finishAt.width"
                align="center"
                text-size="mini"
            />

            <!-- __ Время стегания -->
            <AppLabel
                v-if="rollsRender.rollTime.show"
                :text="rollsRender.rollTime.data ? rollsRender.rollTime.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.rollTime.width"
                align="center"
                text-size="mini"
            />

            <!-- __ Ответственный -->
            <AppLabel
                v-if="rollsRender.finishBy.show"
                :text="rollsRender.finishBy.data ? rollsRender.finishBy.data(roll_exec) : ''"
                :type="getTypeByStatus(roll_exec)"
                :width="rollsRender.finishBy.width"
                text-size="mini"
            />

        </div>
    </div>


</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'

import type { IRenderDataItem, IRollExec } from '@/types'
//@ts-ignore
import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    FABRIC_ROLL_STATUS_LIST,
    // FABRIC_ROLL_STATUS
} from '@/app/constants/fabrics.js'

//@ts-ignore
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date.js'
//@ts-ignore
import { getTypeByRollStatus } from '@/app/helpers/manufacture/helpers_fabric.js'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'


type IRenderDataItemOptional = Omit<IRenderDataItem, 'header' | 'type'>;

interface IRenderData {
    [key: string]: IRenderDataItemOptional;
}

interface IProps {
    rolls?: {
        rolls_exec: IRollExec[]
    }[]
    execute?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
        rolls: () => [],
        execute: false
    }
)

// const props = defineProps({
//     rollsData: {
//         type: Array,
//         required: false,
//         default: []
//     },
//     execute: {                      // Режим отображения (в режиме демонстрации или в режиме выполнения)
//         type: Boolean,
//         required: false,
//         default: false
//     }
// })

const PRECISION = 2

// __ Определяем массив для отображение
const rollsData = ref<IRollExec[]>([])

const prepareRenderData = () => {
    rollsData.value = []
    props.rolls.forEach(roll => roll.rolls_exec.forEach(roll_exec => rollsData.value.push(roll_exec)))
    rollsData.value.sort((a, b) => a.position - b.position)
}

prepareRenderData()
// console.log('execute rolls: ', rollsData.value)

const FABRIC_ROLL_STATUS_ARRAY = Object.values(FABRIC_ROLL_STATUS_LIST)
// console.log(FABRIC_ROLL_STATUS_ARRAY)

// __ Получаем данные из хранилища по ПС
const fabricsStore = useFabricsStore()
const fabrics: Array<{id: number, display_name: string}> = fabricsStore.fabricsMemory

// __ Получаем ПС по ID
// const getFabricById = (fabric_id) => fabrics.find((fabric) => fabric.id === fabric_id)

// __ Получаем тип раскраски в зависимости от статуса выполнения рулона
const getTypeByStatus = (roll_exec: IRollExec) => {
    return getTypeByRollStatus(roll_exec.status)
}

// __ Получаем тип шапки таблицы
const getHeaderType = () => 'primary'

// __ Задаем глобальный объект для унификации отображения рулонов
const rollsRender: IRenderData = {
    position: {
        width: 'w-[60px]',
        show: true,
        title: '№ п/п',
        data: (roll_exec) => roll_exec.position.toString()
    },
    rollNumber: {
        width: 'w-[60px]',
        show: true,
        title: '№ рулона',
        data: (roll_exec) => roll_exec.id.toString()
    },
    fabricName: {
        width: 'w-[280px]',
        show: true,
        title: 'Название ПС',
        data: (roll_exec) => fabrics.find(fabric => fabric.id === roll_exec.fabric_id)!.display_name
    },
    textileLength: {
        width: 'w-[50px]',
        show: true,
        title: 'Средняя длина ткани, м.п.',
        data: (roll_exec) => roll_exec.textile_length.toFixed(PRECISION)
    },
    fabricLength: {
        width: 'w-[50px]',
        show: true,
        title: 'Средняя длина ПС, м.п.',
        data: (roll_exec) => roll_exec.fabric_length.toFixed(PRECISION)
        // data: (roll_exec) => (roll_exec.textile_length / roll_exec.rate).toFixed(PRECISION)
    },
    rollsAmount: {width: 'w-[30px]', show: false, title: 'Кол-во рулонов, шт.', data: () => '1'},
    productivity: {
        width: 'w-[90px]',
        show: true,
        title: 'Средние трудозатраты, ч.',
        data: (roll_exec) => formatTimeWithLeadingZeros(roll_exec.fabric_length / roll_exec.productivity, 'hour')
    },
    description: {
        width: props.execute ? 'w-[200px]' : 'w-[300px]',
        show: true,
        title: (roll_exec) => roll_exec?.descr ?? 'Комментарий',
        data: (roll_exec) => roll_exec.descr,
    },
    status: {
        width: 'w-[100px]',
        show: true,
        title: 'Статус',
        data: (roll_exec) => FABRIC_ROLL_STATUS_ARRAY.find(fabricRollStatus => fabricRollStatus.CODE === roll_exec.status)!.TITLE
    },
    startAt: {
        width: 'w-[110px]',
        show: props.execute,
        title: 'Начало стегания рулона',
        data: (/*roll_exec*/) => '16.04.2023 10:59'
    },
    finishAt: {
        width: 'w-[110px]',
        show: props.execute,
        title: 'Окончание стегания рулона',
        data: (/*roll_exec*/) => '16.04.2023 12:38'
    },
    rollTime: {
        width: 'w-[90px]',
        show: props.execute,
        title: 'Время стегания',
        data: (/*roll_exec*/) => '01ч. 59м. 59с.'
    },
    finishBy: {
        width: 'w-[110px]',
        show: props.execute,
        title: 'Ответственный',
        data: (/*roll_exec*/) => 'Сидорук И.И.'
    },
}

watch(() => props.rolls, () => prepareRenderData(), {immediate: true, deep: true})

</script>

<style scoped>

</style>
