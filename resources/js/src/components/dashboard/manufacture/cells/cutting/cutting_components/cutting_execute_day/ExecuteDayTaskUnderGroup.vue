<template>
    <div class="flex">

        <!-- __ Collapsed -->
        <AppLabelTS
            :text="collapsed ? '▲' : '▼'"
            align="center"
            rounded="4"
            text-size="micro"
            type="warning"
            width="w-[30px]"
            @click="emits('toggleCollapse')"
        />

        <!-- __ Название SubCategory -->
        <AppLabelTS
            :text="undergroup.undergroupName"
            rounded="4"
            text-size="mini"
            :type="DEFAULT_TYPE"
            width="w-[331px]"
            @click="emits('toggleCollapse')"
        />

        <!-- __ Длина м.п. -->
        <AppLabelTS
            :text="`${undergroup.cutLengthTotal.toFixed(3)} м.п.`"
            :type="undergroup.cutLengthTotal === 0 ? 'danger' : DEFAULT_TYPE"
            align="center"
            rounded="4"
            text-size="mini"
            width="w-[100px]"
        />

        <!-- __ Всего, шт. -->
        <AppLabelTS
            :text="`Σ = ${undergroup.amount.total.toFixed(0)} шт. (${formatTimeWithLeadingZeros(undergroup.time.total)})`"
            :type="undergroup.amount.total === 0 ? 'danger' : 'stone'"
            :width="FIELDS_AMOUNT_TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
        />

        <!-- __ Выполнено, шт. -->
        <AppLabelTS
            :text="`✓ = ${undergroup.amount.done.toFixed(0)} шт. (${formatTimeWithLeadingZeros(undergroup.time.done)})`"
            :width="FIELDS_AMOUNT_TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="success"
        />

        <!-- __ Не Выполнено, шт. -->
        <AppLabelTS
            :text="`✗ = ${undergroup.amount.incomplete.toFixed(0)} шт. (${formatTimeWithLeadingZeros(undergroup.time.incomplete)})`"
            :width="FIELDS_AMOUNT_TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="danger"
        />


    </div>

</template>

<script lang="ts" setup>

import type { ICuttingTaskLinesUnderGroup } from '@/types'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

interface IProps {
    undergroup: ICuttingTaskLinesUnderGroup
    collapsed?: boolean
}

/*const props =*/ defineProps<IProps>()

const emits = defineEmits<{
    (e: 'toggleCollapse'): void
}>()

const FIELDS_AMOUNT_TIME_WIDTH = 'w-[172px]'
const DEFAULT_TYPE = 'indigo'

</script>

<style scoped>

</style>
