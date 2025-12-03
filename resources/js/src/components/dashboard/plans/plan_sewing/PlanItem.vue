<template>
    <div v-if="item.id > 0" class="flex" >

        <!-- __ Клиент -->
        <AppLabelTS
            v-if="render.client.show"
            :align="render.client.align"
            :color="item.order_type.color"
            :height="DEFAULT_HEIGHT"
            :text="render.client.data()"
            :text-size="render.client.textSize"
            :type="render.client.type"
            :width="render.client.width"
            class="plan-item truncate"
            rounded="rounded-[4px]"
        />

        <!-- __ Номер заявки -->
        <AppLabelTS
            v-if="render.orderNo.show"
            :align="render.orderNo.align"
            :color="item.order_type.color"
            :height="DEFAULT_HEIGHT"
            :text="render.orderNo.data()"
            :text-size="render.orderNo.textSize"
            :type="render.orderNo.type"
            :width="render.orderNo.width"
            rounded="rounded-[4px]"
            class="plan-item"
        />

        <!-- __ Количество -->
        <AppLabelTS
            v-if="render.amount.show"
            :align="render.amount.align"
            :color="item.order_type.color"
            :height="DEFAULT_HEIGHT"
            :text="render.amount.data()"
            :text-size="render.amount.textSize"
            :type="render.amount.type"
            :width="render.amount.width"
            rounded="rounded-[4px]"
            class="plan-item"
        />

    </div>
</template>

<script lang="ts" setup>
import { reactive } from 'vue'

import type { IHorizontalAlign, IPlan } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import type { IFontsType } from '@/app/constants/fontSizes.ts'

import { PLAN_DRAFT } from '@/app/constants/plans.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    item?: IPlan
    columnsWidth?: Record<string, string>
    index?: number
}

interface IRenderItem {
    show: boolean
    width: string
    type: IColorTypes
    align: IHorizontalAlign
    data: () => string
    textSize: IFontsType
}

type IRender = Record<string, IRenderItem>

const props = withDefaults(defineProps<IProps>(), {
    item: () => PLAN_DRAFT,
    columnsWidth: () => ({
        client: 'w-[110px]',
        amount: 'w-[50px]',
        orderNo: 'w-[50px]',
        common: 'w-[164px]',
    }),
    index: 0,
})

const DEFAULT_HEIGHT = 'h-[25px]'

// __ Подготавливаем рендер
const render: IRender = reactive({
    client: {
        show: true,
        width: props.columnsWidth.client,
        type: 'dark',
        align: 'left',
        data: () => props.item.client.short_name,
        textSize: 'micro',
    },
    orderNo: {
        show: true,
        width: props.columnsWidth.orderNo,
        type: 'dark',
        align: 'center',
        data: () => props.item.order_no,
        textSize: 'micro',
    },
    amount: {
        show: true,
        width: props.columnsWidth.amount,
        type: 'dark',
        align: 'center',
        data: () => props.item.amounts.totals ? props.item.amounts.totals.toString() : '',
        textSize: 'micro',
    },
})

// console.log('index: ', props.index)


</script>

<style scoped>
.plan-item {
    @apply cursor-pointer;
}
</style>
