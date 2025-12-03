<template>

    <div
        :class="shadowColor"
        class="m-1 pb-0.5 border-[1px] rounded border-slate-600 bg-slate-200 w-fit min-w-[224px] min-h-[129px] shadow-xl"
    >

        <!-- __ День недели -->
        <div class="mr-1">

            <AppLabelTS
                :text="renderDate"
                :type="dateType"
                align="center"
                rounded="rounded-[4px]"
                text-size="small"
                width="w-full"
                :height="DEFAULT_HEIGHT"
            />
        </div>

        <!-- __ Шапка -->
        <div v-if="totalAmountInDAy" class="flex">
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :type="TOTALS_TYPE"
                :width="columnsWidth.client"
                align="center"
                rounded="rounded-[4px]"
                text="Клиент:"
                text-size="mini"
            />

            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :type="TOTALS_TYPE"
                :width="columnsWidth.orderNo"
                align="center"
                rounded="rounded-[4px]"
                text="№"
                text-size="mini"
            />

            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                align="center"
                rounded="rounded-[4px]"
                text="Кол-во"
                text-size="mini"
            />
        </div>

        <!-- __ Загрузки -->
        <div v-for="(load, idx) of day" :key="idx">
            <PlanItem
                :columns-width="columnsWidth"
                :load="load"
            />
        </div>

        <!-- __ Разделительная линия -->
        <TheDividerLine/>

        <!-- __ Итого -->
        <div v-if="totalAmountInDAy" class="flex">
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :type="TOTALS_TYPE"
                :width="columnsWidth.common"
                align="right"
                rounded="rounded-[4px]"
                text="Всего:"
                text-size="mini"
            />

            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text="totalAmountInDAy.toString()"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                align="center"
                rounded="rounded-[4px]"
                text-size="mini"
            />
        </div>


    </div>
</template>

<script lang="ts" setup>

import { computed } from 'vue'

import type { IPlanMatrixDay } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

import { formatDateInFullFormat, isHoliday, isToday } from '@/app/helpers/helpers_date'
import { usePlansStore } from '@/stores/PlansStore.ts'
import { storeToRefs } from 'pinia'
import { ifDateInPeriod } from '@/app/helpers/plan/helpers_plan.ts'

import PlanItem from '@/components/dashboard/plans/plan_business_process/PlanItem.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import TheDividerLine from '@/components/ui/dividers/TheDividerLine.vue'

interface IProps {
    date: Date,
    day?: IPlanMatrixDay
}

const props = withDefaults(defineProps<IProps>(), {
    day: () => [],
})

const planStore = usePlansStore()
const {planPeriodGlobal} = storeToRefs(planStore)

const DEFAULT_HEIGHT = 'h-[25px]'
const TOTALS_TYPE: IColorTypes = 'stone'

const renderDate = computed(() => formatDateInFullFormat(props.date))
// const renderDate = computed(() => getDateFromDateTimeString(props.date))

// TODO: Переписать все в dateType()
const isHolidayDay = computed(() => isHoliday(props.date))
const isTodayDay = computed(() => isToday(props.date))
const isDayInRange = computed(() => ifDateInPeriod(props.date, planPeriodGlobal.value))
// const isActiveDay = computed(() => false)
const dateType = computed((): IColorTypes => {
    if (isTodayDay.value) return 'success'
    if (!isDayInRange.value) return 'dark'
    if (isHolidayDay.value) return 'danger'
    return 'primary'
})
// ----------------------------------------

// __ Итого
const totalAmountInDAy = computed(() => props.day.reduce((acc, load) => load.amounts.totals ? load.amounts.totals + acc : acc, 0))

// __ Ширина колонок
const columnsWidth = {
    client: 'w-[110px]',
    amount: 'w-[50px]',
    orderNo: 'w-[50px]',
    common: 'w-[164px]',
}

// __ Цвет тени
const shadowColor = computed(() => {
    switch (dateType.value) {
        case 'success':
            return 'shadow-green-300'
        case 'danger':
            return 'shadow-red-300'
        case 'primary':
            return 'shadow-blue-300'
        case 'dark':
            return 'shadow-slate-400'
        default:
            return 'shadow-slate-400'
    }
})


</script>

<style scoped>

</style>
