<template>
    <!-- __ Тут именно -1, т.к. id = 0 - это заглушка для добавления нового элемента -->
    <div v-if="item.id > -1" class="flex">

        <!-- __ Клиент -->
        <AppLabelMultiLineTS
            v-if="render.client.show"
            :align="render.client.align"
            :color="item.order.order_type.color"
            :height="dataHeight"
            :text="globalSewingTaskTimesShow ? [render.client.data(), formattedLoadDate] : render.client.data()"
            :text-size="render.client.textSize"
            :type="render.client.type"
            :width="render.client.width"
            class="plan-item truncate"
            rounded="rounded-[4px]"
        />

        <!-- __ Номер заявки -->
        <AppLabelMultiLineTS
            v-if="render.orderNo.show"
            :align="render.orderNo.align"
            :color="item.order.order_type.color"
            :height="dataHeight"
            :text="globalSewingTaskTimesShow ? [render.orderNo.data(), ''] : render.orderNo.data()"
            :text-size="render.orderNo.textSize"
            :type="render.orderNo.type"
            :width="render.orderNo.width"
            class="plan-item"
            rounded="rounded-[4px]"
        />

        <!-- __ Количество + Трудозатраты Общие -->
        <ManageItemDataLabel
            v-if="render.amount.show"
            :align="render.amount.align"
            :amount="getTotalAmount"
            :color="item.order.order_type.color"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="getTotalTime"
            :time-show="globalSewingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
            class="plan-item"
        />


        <!-- __ Количество + Трудозатраты УШМ -->
        <ManageItemDataLabel
            v-if="globalSewingTaskFullDaysShow"
            :align="render.amount.align"
            :amount="amountAndTime[SEWING_MACHINES.UNIVERSAL].amount"
            :color="item.order.order_type.color"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="amountAndTime[SEWING_MACHINES.UNIVERSAL].time"
            :time-show="globalSewingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
            class="plan-item"
        />

        <!-- __ Количество + Трудозатраты АШМ -->
        <ManageItemDataLabel
            v-if="globalSewingTaskFullDaysShow"
            :align="render.amount.align"
            :amount="amountAndTime[SEWING_MACHINES.AUTO].amount"
            :color="item.order.order_type.color"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="amountAndTime[SEWING_MACHINES.AUTO].time"
            :time-show="globalSewingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
            class="plan-item"
        />

        <!-- __ Количество + Трудозатраты Solid Hard -->
        <ManageItemDataLabel
            v-if="globalSewingTaskFullDaysShow"
            :align="render.amount.align"
            :amount="amountAndTime[SEWING_MACHINES.SOLID_HARD].amount"
            :color="item.order.order_type.color"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="amountAndTime[SEWING_MACHINES.SOLID_HARD].time"
            :time-show="globalSewingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
            class="plan-item"
        />

        <!-- __ Количество + Трудозатраты Solid Lite -->
        <ManageItemDataLabel
            v-if="globalSewingTaskFullDaysShow"
            :align="render.amount.align"
            :amount="amountAndTime[SEWING_MACHINES.SOLID_LITE].amount"
            :color="item.order.order_type.color"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="amountAndTime[SEWING_MACHINES.SOLID_LITE].time"
            :time-show="globalSewingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
            class="plan-item"
        />

        <!-- __ Количество + Трудозатраты Неопознанные -->
        <ManageItemDataLabel
            v-if="globalSewingTaskFullDaysShow"
            :align="render.amount.align"
            :amount="amountAndTime[SEWING_MACHINES.UNDEFINED].amount"
            :color="amountAndTime[SEWING_MACHINES.UNDEFINED].amount === 0 ? item.order.order_type.color : 'red'"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="amountAndTime[SEWING_MACHINES.UNDEFINED].time"
            :time-show="globalSewingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
            class="plan-item"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed, reactive, } from 'vue'

import { storeToRefs } from 'pinia'

import { useSewingStore } from '@/stores/SewingStore.ts'

import type {
    IHorizontalAlign,
    ISewingTask,
    IFontsType,
    IColorTypes,
    IAmountAndTime
} from '@/types'

import { SEWING_MACHINES, SEWING_TASK_DRAFT, } from '@/app/constants/sewing.ts'
// import { DEBUG } from '@/app/constants/common.ts'

import { formatDateInFullFormat } from '@/app/helpers/helpers_date'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import ManageItemDataLabel
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageItemDataLabel.vue'

interface IProps {
    amountAndTime: IAmountAndTime
    item?: ISewingTask
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

    item:         () => SEWING_TASK_DRAFT,
    columnsWidth: () => ({
        client:  'w-[90px]',
        amount:  'w-[50px]',
        orderNo: 'w-[50px]',
        common:  'w-[164px]',
    }),
    index:        0,
})

// __ Данные из Хранилища
const sewingStore = useSewingStore()

const { globalSewingTaskTimesShow, globalSewingTaskFullDaysShow } = storeToRefs(sewingStore)

// __ Высота данных
const dataHeight = computed(() => globalSewingTaskTimesShow.value ? 'h-[60px]' : 'h-[30px]')

// __ Подготавливаем рендер
const render: IRender = reactive({
    client:  {
        show:     true,
        width:    props.columnsWidth.client,
        type:     'dark',
        align:    'left',
        data:     () => `${props.item.position}. ${props.item.order.client.short_name}`,
        textSize: 'micro',
    },
    orderNo: {
        show:     true,
        width:    props.columnsWidth.orderNo,
        type:     'dark',
        align:    'center',
        data:     () => props.item.order.order_no_str,
        textSize: 'micro',
    },
    amount:  {
        show:     true,
        width:    props.columnsWidth.amount,
        type:     'dark',
        align:    'center',
        data:     () => props.item.sewing_lines.reduce((acc, item) => acc + item.amount, 0).toString(),
        textSize: 'micro',
    },
})

// __ Общее Количество
const getTotalAmount = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.amount + acc, 0))

// __ Общее Трудозатраты
const getTotalTime = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.time + acc, 0))

// __ Подготавливаем дату отгрузки для отображения
const formattedLoadDate = computed(() => {
    return formatDateInFullFormat(props.item.order.load_at, true, false)
})



</script>

<style scoped>
.plan-item {
    @apply cursor-pointer;
}
</style>
