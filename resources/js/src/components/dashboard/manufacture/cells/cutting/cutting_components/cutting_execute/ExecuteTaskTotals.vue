<template>
    <div class="flex ">

        <!-- __ Надпись Всего: -->
        <AppLabelTS
            align="center"
            height="h-[60px]"
            rounded="4"
            text="Всего:"
            text-size="mini"
            type="stone"
            :width="totalFieldWidth"
        />

        <!-- __ Количество в штуках -->
        <AppLabelTS
            :text="getTotalAmount === 0 ? '' : `${getTotalAmount.toFixed(0)} шт.`"
            align="center"
            height="h-[60px]"
            rounded="4"
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            type="stone"
            width="w-[30px]"
        />

        <!-- __ Количество + Трудозатраты Общие -->
        <ManageItemDataLabel
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            :align="TOTAL_ITEMS_ALIGN"
            :amount="getTotalAmount"
            :height="TOTAL_ITEMS_HEIGHT"
            :time="getTotalTime"
            type="danger"
            width="w-[50px]"
        />

        <!-- __ Количество + Трудозатраты УШМ -->
        <ManageItemDataLabel
            :align="TOTAL_ITEMS_ALIGN"
            :amount="amountAndTime[SEWING_MACHINES.UNIVERSAL].amount"
            :height="TOTAL_ITEMS_HEIGHT"
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            :time="amountAndTime[SEWING_MACHINES.UNIVERSAL].time"
            :type="TOTAL_ITEMS_TYPE"
            :width="TOTAL_ITEMS_WIDTH"
        />

        <!-- __ Количество + Трудозатраты АШМ -->
        <ManageItemDataLabel
            :align="TOTAL_ITEMS_ALIGN"
            :amount="amountAndTime[SEWING_MACHINES.AUTO].amount"
            :height="TOTAL_ITEMS_HEIGHT"
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            :time="amountAndTime[SEWING_MACHINES.AUTO].time"
            :type="TOTAL_ITEMS_TYPE"
            :width="TOTAL_ITEMS_WIDTH"
        />

        <!-- __ Количество + Трудозатраты Solid Hard -->
        <ManageItemDataLabel
            :align="TOTAL_ITEMS_ALIGN"
            :amount="amountAndTime[SEWING_MACHINES.SOLID_HARD].amount"
            :height="TOTAL_ITEMS_HEIGHT"
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            :time="amountAndTime[SEWING_MACHINES.SOLID_HARD].time"
            :type="TOTAL_ITEMS_TYPE"
            :width="TOTAL_ITEMS_WIDTH"
        />

        <!-- __ Количество + Трудозатраты Solid Lite -->
        <ManageItemDataLabel
            :align="TOTAL_ITEMS_ALIGN"
            :amount="amountAndTime[SEWING_MACHINES.SOLID_LITE].amount"
            :height="TOTAL_ITEMS_HEIGHT"
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            :time="amountAndTime[SEWING_MACHINES.SOLID_LITE].time"
            :type="TOTAL_ITEMS_TYPE"
            :width="TOTAL_ITEMS_WIDTH"
        />

        <!-- __ Количество + Трудозатраты Неопознанные -->
        <ManageItemDataLabel
            v-if="amountAndTime[SEWING_MACHINES.UNKNOWN].time"
            :align="TOTAL_ITEMS_ALIGN"
            :amount="amountAndTime[SEWING_MACHINES.UNKNOWN].amount"
            :height="TOTAL_ITEMS_HEIGHT"
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            :time="amountAndTime[SEWING_MACHINES.UNKNOWN].time"
            type="danger"
            :width="TOTAL_ITEMS_WIDTH"
        />


    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IAmountAndTime } from '@/types'

import { SEWING_MACHINES } from '@/app/constants/sewing.ts'

import ManageItemDataLabel
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageItemDataLabel.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    amountAndTime?: IAmountAndTime
    totalFieldWidth?: string,
}

const props = withDefaults(defineProps<IProps>(), {
    amountAndTime: () => ({}),
    totalFieldWidth: 'w-[372px]'
})

// __ Константы полей агрегаторов
const TOTAL_ITEMS_WIDTH     = 'w-[25px]'
const TOTAL_ITEMS_HEIGHT    = 'h-[60px]'
const TOTAL_ITEMS_TYPE      = 'stone'
const TOTAL_ITEMS_ALIGN     = 'center'
const TOTAL_ITEMS_TEXT_SIZE = 'micro'


// __ Общее Количество
const getTotalAmount = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.amount + acc, 0))

// __ Общее Трудозатраты
const getTotalTime = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.time + acc, 0))


</script>

<style scoped>

</style>
