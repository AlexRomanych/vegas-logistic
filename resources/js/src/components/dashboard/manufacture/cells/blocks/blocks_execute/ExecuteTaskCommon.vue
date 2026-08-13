<template>
    <div v-for="[key, value] of Object.entries(calculateTotals)" :key="key">
        <template v-if="key !== BLOCK_MANUF_LINES.LINE_0">

            <div class="flex">

                <!-- __ Линия -->
                <AppLabelTS
                    :text="getLineTitle(key) + ':'"
                    :width="LINE_WIDTH"
                    rounded="4"
                    text-size="mini"
                    :type="getLineTitle(key) === 'Линия 1' ? 'indigo' : 'orange'"
                />

                <!-- __ Штуки -->
                <AppLabelTS
                    :text="value.amount + ' шт.'"
                    :width="PICS_WIDTH"
                    align="center"
                    rounded="4"
                    text-size="mini"
                    type="info"
                />

                <!-- __ Площадь -->
                <AppLabelTS
                    :text="value.square.toFixed(3) + ' кв.м.'"
                    :width="PICS_WIDTH"
                    align="center"
                    rounded="4"
                    text-size="mini"
                    type="warning"
                />

                <!-- __ Трудозатраты -->
                <AppLabelTS
                    :text="formatTimeWithLeadingZeros(value.time, 'hour')"
                    :width="TIME_WIDTH"
                    align="center"
                    rounded="4"
                    text-size="mini"
                    type="indigo"
                />

                <!-- __ Прогресс общий -->
                <AppProgressBar
                    :progress="getLinePercent(key as IBlockManufLine)"
                    :width="PROGRESS_WIDTH"
                />
            </div>

        </template>
    </div>

    <TheDividerLineTS/>

    <!--__ Итого-->
    <div class="flex">
        <AppLabelTS
            :width="LINE_WIDTH"
            rounded="4"
            text="Всего:"
            text-size="mini"
            type="primary"
        />

        <!-- __ Штуки -->
        <AppLabelTS
            :text="totalAmount + ' шт.'"
            :width="PICS_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="info"
        />

        <!-- __ Площадь -->
        <AppLabelTS
            :text="totalSquare.toFixed(3) + ' кв.м.'"
            :width="PICS_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="warning"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :text="formatTimeWithLeadingZeros(totalTime, 'hour')"
            :width="TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="indigo"
        />

        <!--&lt;!&ndash; __ Прогресс общий &ndash;&gt;-->
        <!--<AppProgressBar-->
        <!--    :progress="dayStatistics.time.finished / dayStatistics.time.total * 100"-->
        <!--    :width="PROGRESS_WIDTH"-->
        <!--/>-->
    </div>


</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IBlockDay, IBlockManufLine, IBlockTaskLine } from '@/types'

import { BLOCK_MANUF_LINES, LINE_0_NAME, LINE_1_NAME, LINE_2_NAME } from '@/app/constants/blocks.ts'

import {
    getExecuteTaskStatistics, getBlockTaskAmountAndTime
} from '@/app/helpers/manufacture/helpers_blocks.ts'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'

interface IProps {
    blockDay: IBlockDay
}

const props = defineProps<IProps>()

const LINE_WIDTH     = 'w-[120px]'
const PICS_WIDTH     = 'w-[120px]'
const TIME_WIDTH     = 'w-[120px]'
const PROGRESS_WIDTH = 'w-[300px]'


// __ Собираем все Записи в один массив
const commonBlockLines = computed(() => {
    const result: IBlockTaskLine[] = []
    props.blockDay.block_tasks.forEach(task => task.block_lines.forEach(line => result.push(line)))
    return result
})

// __ Пересчитываем Итого
const calculateTotals = computed(() => getBlockTaskAmountAndTime(commonBlockLines.value))

// __ Получаем процент выполнения по каждой машине
const getLinePercent = (key: IBlockManufLine) => {
    const blockLinesByMachine = commonBlockLines.value.filter(item => item.manuf_line === key)

    // __ Получаем объект статистики
    const statistics = getExecuteTaskStatistics(blockLinesByMachine)
    // console.log('statistics: ', statistics)
    return statistics.time.total !== 0 ? statistics.time.finished / statistics.time.total * 100 : 0
}

// __ Общее Количество
const totalAmount = computed(() => Object.values(calculateTotals.value).reduce((acc, item) => item.amount + acc, 0))

// __ Общая Площадь
const totalSquare = computed(() => Object.values(calculateTotals.value).reduce((acc, item) => item.square + acc, 0))


// __ Общее Трудозатраты
const totalTime = computed(() => Object.values(calculateTotals.value).reduce((acc, item) => item.time + acc, 0))

// __ Название Стола
const getLineTitle = (key: string) => {
    // console.log(key)

    if (key === BLOCK_MANUF_LINES.LINE_1) {
        return LINE_1_NAME
    }
    if (key === BLOCK_MANUF_LINES.LINE_2) {
        return LINE_2_NAME
    }
    if (key === BLOCK_MANUF_LINES.LINE_0) {
        return LINE_0_NAME
    }

    throw new Error('Неизвестная линия')
}

</script>

<style scoped>

</style>
