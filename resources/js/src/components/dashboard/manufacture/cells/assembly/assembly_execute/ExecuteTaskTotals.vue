<template>
    <div class="flex ">

        <!-- __ Надпись Всего: -->
        <AppLabelTS
            :width="totalFieldWidth"
            align="center"
            :height="TOTAL_ITEMS_HEIGHT"
            rounded="4"
            text="Всего:"
            text-size="mini"
            type="stone"
        />

        <!-- __ Количество в штуках -->
        <AppLabelTS
            :height="TOTAL_ITEMS_HEIGHT"
            :text="`${amountAndTime.amount.toFixed(0)} шт.`"
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            :type="amountAndTime.amount === 0 ? 'danger' : 'stone'"
            :width="TOTAL_AMOUNT_WIDTH"
            align="center"
            rounded="4"
        />


        <!-- __ Трудозатраты -->
        <AppLabelTS
            :height="TOTAL_ITEMS_HEIGHT"
            :text="`${amountAndTime.time.toFixed(0)}`"
            :text-size="TOTAL_ITEMS_TEXT_SIZE"
            :type="amountAndTime.time === 0 ? 'danger' : 'stone'"
            :width="TOTAL_TIME_WIDTH"
            align="center"
            rounded="4"
        />


    </div>
</template>

<script lang="ts" setup>
import type { IStatItemAssembly } from '@/types'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
// import ManageItemDataLabel
//     from '@/components/dashboard/manufacture/cells/blocks/blocks_manage/ManageItemDataLabel.vue'

interface IProps {
    amountAndTime?: IStatItemAssembly
    totalFieldWidth?: string,
}

/*const props = */withDefaults(defineProps<IProps>(), {
    amountAndTime  : () => ({ amount: 0, time: 0 }),
    totalFieldWidth: 'w-[552px]'
})

// console.log('amountAndTime: ', props.amountAndTime)

// __ Константы полей агрегаторов
const TOTAL_AMOUNT_WIDTH    = 'w-[50px]'
const TOTAL_TIME_WIDTH      = 'w-[100px]'
const TOTAL_ITEMS_HEIGHT    = 'h-[30px]'
const TOTAL_ITEMS_TEXT_SIZE = 'micro'


// // __ Общее Количество
// const getTotalAmount = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.amount + acc, 0))
//
// // __ Общая Площадь
// const getTotalSquare = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.square + acc, 0))
//
// // __ Общее Трудозатраты
// const getTotalTime = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.time + acc, 0))


</script>

<style scoped>

</style>
