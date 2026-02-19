<template>
    <div v-for="[key, value] of Object.entries(calculateTotals)" :key="key">
        <template v-if="!([AVERAGE, UNDEFINED] as ISewingMachineKeys[]).includes(key as ISewingMachineKeys)">

            <div class="flex">

                <!-- __ ШМ -->
                <AppLabelTS
                    :text="getMachineTitle(key) + ':'"
                    :width="MACHINE_WIDTH"
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
                    :progress="90"
                    :width="PROGRESS_WIDTH"
                />
            </div>

        </template>
    </div>

    <TheDividerLineTS/>

    <!--__ Итого-->
    <div class="flex">
        <AppLabelTS
            :width="MACHINE_WIDTH"
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

        <!-- __ Прогресс общий -->
        <AppProgressBar
            :progress="90"
            :width="PROGRESS_WIDTH"
        />
    </div>


</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { ISewingDay, ISewingMachineKeys, ISewingTaskLine } from '@/types'

import { AVERAGE, SEWING_MACHINES, UNDEFINED } from '@/app/constants/sewing.ts'

import { getSewingTaskAmountAndTime } from '@/app/helpers/manufacture/helpers_sewing.ts'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'

interface IProps {
    sewingDay: ISewingDay
}

const props = defineProps<IProps>()

const MACHINE_WIDTH  = 'w-[120px]'
const PICS_WIDTH     = 'w-[120px]'
const TIME_WIDTH     = 'w-[120px]'
const PROGRESS_WIDTH = 'w-[300px]'


// __ Собираем все Записи в один массив
const commonSewingLines = computed(() => {
    const result: ISewingTaskLine[] = []
    props.sewingDay.sewing_tasks.forEach(task => task.sewing_lines.forEach(line => result.push(line)))
    return result
})

// __ Пересчитываем Итого
const calculateTotals = computed(() => getSewingTaskAmountAndTime(commonSewingLines.value))

// __ Общее Количество
const totalAmount = computed(() => Object.values(calculateTotals.value).reduce((acc, item) => item.amount + acc, 0))

// __ Общее Трудозатраты
const totalTime = computed(() => Object.values(calculateTotals.value).reduce((acc, item) => item.time + acc, 0))

// __ Название машины
const getMachineTitle = (key: string) => {
    console.log(key)

    if (key === SEWING_MACHINES.UNIVERSAL) {
        return 'УШМ'
    }
    if (key === SEWING_MACHINES.AUTO) {
        return 'АШМ'
    }
    if (key === SEWING_MACHINES.SOLID_HARD) {
        return 'Глухие Сложные'
    }
    if (key === SEWING_MACHINES.SOLID_LITE) {
        return 'Глухие Простые'
    }

    throw new Error('Неизвестная машина')
}

</script>

<style scoped>

</style>
