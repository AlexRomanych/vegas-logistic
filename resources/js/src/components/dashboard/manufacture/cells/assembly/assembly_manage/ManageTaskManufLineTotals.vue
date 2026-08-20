<template>
    <div class="flex ">

        <!-- __ Надпись Всего: -->
        <AppLabelTS
            align="center"
            height="h-[60px]"
            rounded="4"
            text="Всего:"
            text-size="mini"
            type="primary"
            :width="short ? 'w-[227px]' : 'w-[457px]'"
        />

        <!-- __ Количество в штуках -->
        <AppLabelTS
            :text="getTotalAmount === 0 ? '' : `${getTotalAmount.toFixed(0)} шт.`"
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            align="center"
            height="h-[60px]"
            rounded="4"
            type="primary"
            width="w-[30px]"
        />

        <!-- __ Количество + Трудозатраты Общие -->
        <ManageItemDataLabel
            :align="TOTAL_ITEMS_ALIGN"
            :amount="getTotalAmount"
            :height="TOTAL_ITEMS_HEIGHT"
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            :time="getTotalTime"
            type="danger"
            width="w-[50px]"
        />

        <template v-if="!short">
            <!-- __ Количество + Трудозатраты Ламит -->
            <ManageItemDataLabel
                :align="TOTAL_ITEMS_ALIGN"
                :amount="amountAndTime[ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT].amount"
                :height="TOTAL_ITEMS_HEIGHT"
                :text-size="TOTAL_ITEMS_TEXT_SIZE"
                :time="amountAndTime[ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT].time"
                :type="TOTAL_ITEMS_TYPE"
                :width="TOTAL_ITEMS_WIDTH"
            />

            <!-- __ Количество + Трудозатраты Столы -->
            <ManageItemDataLabel
                :align="TOTAL_ITEMS_ALIGN"
                :amount="amountAndTime[ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE].amount"
                :height="TOTAL_ITEMS_HEIGHT"
                :text-size="TOTAL_ITEMS_TEXT_SIZE"
                :time="amountAndTime[ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE].time"
                :type="TOTAL_ITEMS_TYPE"
                :width="TOTAL_ITEMS_WIDTH"
            />

        </template>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IAmountAndTimeAssemblyLines } from '@/types'

import { ASSEMBLY_LINES } from '@/app/constants/assembly.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import ManageItemDataLabel
    from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageItemDataLabel.vue'

interface IProps {
    amountAndTime?: IAmountAndTimeAssemblyLines
    short?: boolean
    showDetails?: boolean,
}

const props = withDefaults(defineProps<IProps>(), {
    amountAndTime: () => ({
        [ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT]: {
            amount: 0,
            time: 0,
        },
        [ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE]: {
            amount: 0,
            time: 0,
        },
        [ASSEMBLY_LINES.ASSEMBLY_LINE_UNDEFINED]: {
            amount: 0,
            time: 0,
        },
    }),
    short: false,
    showDetails: false,
})

// __ Константы полей агрегаторов
const TOTAL_ITEMS_WIDTH     = 'w-[25px]'
const TOTAL_ITEMS_HEIGHT    = 'h-[60px]'
const TOTAL_ITEMS_TYPE      = 'success'
const TOTAL_ITEMS_ALIGN     = 'center'
const TOTAL_ITEMS_TEXT_SIZE = 'micro'


// __ Общее Количество
const getTotalAmount = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.amount + acc, 0))

// __ Общее Трудозатраты
const getTotalTime = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.time + acc, 0))


</script>

<style scoped>

</style>
