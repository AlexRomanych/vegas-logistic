<template>
    <div v-for="[key, value] of Object.entries(calculateTotals)" :key="key">
        <template v-if="!([UNDEFINED] as ICuttingTableKeys[]).includes(key as ICuttingTableKeys)">

            <div class="flex">

                <!-- __ Стол -->
                <AppLabelTS
                    :text="getTableTitle(key) + ':'"
                    :width="TABLE_WIDTH"
                    rounded="4"
                    text-size="mini"
                    type="dark"
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

                <!-- __ Трудозатраты -->
                <AppLabelTS
                    :text="formatTimeWithLeadingZeros(value.time)"
                    :width="TIME_WIDTH"
                    align="center"
                    rounded="4"
                    text-size="mini"
                    type="indigo"
                />

                <!-- __ Прогресс общий -->
                <AppProgressBar
                    :progress="getTablePercent(key as ICuttingTableKeys)"
                    :width="PROGRESS_WIDTH"
                />
            </div>

        </template>
    </div>

    <TheDividerLineTS/>

    <!--__ Итого-->
    <div class="flex">
        <AppLabelTS
            :width="TABLE_WIDTH"
            rounded="4"
            text="Всего:"
            text-size="mini"
            type="orange"
        />

        <!-- __ Штуки -->
        <AppLabelTS
            :text="totalAmount + ' шт.'"
            :width="PICS_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="primary"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :text="formatTimeWithLeadingZeros(totalTime)"
            :width="TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="success"
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

import type { ICuttingDay, ICuttingTableKeys, ICuttingTaskLine } from '@/types'

import { CUTTING_TABLES, TABLE_1_TITLE, TABLE_2_TITLE, TABLE_3_TITLE, TABLE_0_TITLE } from '@/app/constants/cutting.ts'
import { UNDEFINED } from '@/app/constants/textile_common.ts'

import {
    getExecuteTaskStatistics, getCuttingTaskAmountAndTime
} from '@/app/helpers/manufacture/helpers_cutting.ts'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'

interface IProps {
    cuttingDay: ICuttingDay
}

const props = defineProps<IProps>()

const TABLE_WIDTH    = 'w-[120px]'
const PICS_WIDTH     = 'w-[120px]'
const TIME_WIDTH     = 'w-[120px]'
const PROGRESS_WIDTH = 'w-[300px]'


// __ Собираем все Записи в один массив
const commonCuttingLines = computed(() => {
    const result: ICuttingTaskLine[] = []
    props.cuttingDay.cutting_tasks.forEach(task => task.cutting_lines.forEach(line => result.push(line)))
    return result
})

// __ Пересчитываем Итого
const calculateTotals = computed(() => getCuttingTaskAmountAndTime(commonCuttingLines.value))

// __ Получаем процент выполнения по каждой машине
const getTablePercent = (key: ICuttingTableKeys) => {
    const cuttingLinesByMachine = commonCuttingLines.value.filter(item => item.table === key)

    // __ Получаем объект статистики
    const statistics = getExecuteTaskStatistics(cuttingLinesByMachine)
    // console.log('statistics: ', statistics)
    return statistics.time.total !== 0 ? statistics.time.finished / statistics.time.total * 100 : 0
}

// __ Общее Количество
const totalAmount = computed(() => Object.values(calculateTotals.value).reduce((acc, item) => item.amount + acc, 0))

// __ Общее Трудозатраты
const totalTime = computed(() => Object.values(calculateTotals.value).reduce((acc, item) => item.time + acc, 0))

// __ Название Стола
const getTableTitle = (key: string) => {
    console.log(key)

    if (key === CUTTING_TABLES.TABLE_1) {
        return TABLE_1_TITLE
    }
    if (key === CUTTING_TABLES.TABLE_2) {
        return TABLE_2_TITLE
    }
    if (key === CUTTING_TABLES.TABLE_3) {
        return TABLE_3_TITLE
    }
    if (key === CUTTING_TABLES.TABLE_0) {
        return TABLE_0_TITLE
    }

    throw new Error('Неизвестная машина')
}

</script>

<style scoped>

</style>
