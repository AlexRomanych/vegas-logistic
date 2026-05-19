<template>
    <!-- __ Тут именно -1, т.к. id = 0 - это заглушка для добавления нового элемента -->
    <div v-if="item.id > -1" class="flex">

        <!-- __ Клиент -->
        <AppLabelMultiLineTS
            v-if="render.client.show"
            :align="render.client.align"
            :class="animatedClass"
            :color="color"
            :height="dataHeight"
            :text="globalCuttingTaskTimesShow ? [render.client.data(), formattedLoadDate] : render.client.data()"
            :text-size="render.client.textSize"
            :type="render.client.type"
            :width="render.client.width"
            rounded="rounded-[4px]"
        />

        <!-- __ Номер заявки -->
        <AppLabelMultiLineTS
            v-if="render.orderNo.show"
            :align="render.orderNo.align"
            :color="color"
            :height="dataHeight"
            :text="globalCuttingTaskTimesShow ? [render.orderNo.data(), ''] : render.orderNo.data()"
            :text-size="render.orderNo.textSize"
            :type="render.orderNo.type"
            :width="render.orderNo.width"
            :class="animatedClass"
            rounded="rounded-[4px]"
        />

        <!-- __ Количество + Трудозатраты Общие -->
        <ManageItemDataLabel
            v-if="render.amount.show"
            :align="render.amount.align"
            :amount="getTotalAmount"
            :class_="animatedClass"
            :color="color"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="getTotalTime"
            :time-show="globalCuttingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
        />

        <!-- __ Количество + Трудозатраты УШМ -->
        <ManageItemDataLabel
            v-if="globalCuttingTaskFullDaysShow"
            :align="render.amount.align"
            :amount="amountAndTime[CUTTING_MACHINES.UNIVERSAL].amount"
            :class="animatedClass"
            :color="color"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="amountAndTime[CUTTING_MACHINES.UNIVERSAL].time"
            :time-show="globalCuttingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
        />

        <!-- __ Количество + Трудозатраты АШМ -->
        <ManageItemDataLabel
            v-if="globalCuttingTaskFullDaysShow"
            :align="render.amount.align"
            :amount="amountAndTime[CUTTING_MACHINES.AUTO].amount"
            :class="animatedClass"
            :color="color"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="amountAndTime[CUTTING_MACHINES.AUTO].time"
            :time-show="globalCuttingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
        />

        <!-- __ Количество + Трудозатраты Solid Hard -->
        <ManageItemDataLabel
            v-if="globalCuttingTaskFullDaysShow"
            :align="render.amount.align"
            :amount="amountAndTime[CUTTING_MACHINES.SOLID_HARD].amount"
            :class="animatedClass"
            :color="color"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="amountAndTime[CUTTING_MACHINES.SOLID_HARD].time"
            :time-show="globalCuttingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
        />

        <!-- __ Количество + Трудозатраты Solid Lite -->
        <ManageItemDataLabel
            v-if="globalCuttingTaskFullDaysShow"
            :align="render.amount.align"
            :amount="amountAndTime[CUTTING_MACHINES.SOLID_LITE].amount"
            :class="animatedClass"
            :color="color"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="amountAndTime[CUTTING_MACHINES.SOLID_LITE].time"
            :time-show="globalCuttingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
        />

        <!-- __ Количество + Трудозатраты Неопознанные -->
        <ManageItemDataLabel
            v-if="globalCuttingTaskFullDaysShow"
            :align="render.amount.align"
            :amount="amountAndTime[CUTTING_MACHINES.UNDEFINED].amount"
            :class="animatedClass"
            :color="amountAndTime[CUTTING_MACHINES.UNDEFINED].amount === 0 ? color : 'red'"
            :height="dataHeight"
            :text-size="render.amount.textSize"
            :time="amountAndTime[CUTTING_MACHINES.UNDEFINED].time"
            :time-show="globalCuttingTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed, reactive, } from 'vue'

import { storeToRefs } from 'pinia'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import type {
    IHorizontalAlign,
    ICuttingTask,
    IFontsType,
    IColorTypes,
    IAmountAndTime
} from '@/types'

import { CUTTING_MACHINES, CUTTING_TASK_DRAFT, } from '@/app/constants/cutting.ts'
// import { DEBUG } from '@/app/constants/common.ts'

import { formatDateInFullFormat } from '@/app/helpers/helpers_date'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import ManageItemDataLabel
    from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageItemDataLabel.vue'

interface IProps {
    amountAndTime: IAmountAndTime
    item?: ICuttingTask
    columnsWidth?: Record<string, string>
    index?: number
    orderId?: number | null     // __ Для подсветки СЗ для Заявки
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

    item:         () => CUTTING_TASK_DRAFT,
    columnsWidth: () => ({
        client:  'w-[90px]',
        amount:  'w-[50px]',
        orderNo: 'w-[50px]',
        common:  'w-[164px]',
    }),
    index:        0,
    orderId:      null,
})

// __ Данные из Хранилища
const cuttingStore = useCuttingStore()

const { globalCuttingTaskTimesShow, globalCuttingTaskFullDaysShow, globalCuttingTaskOrderTypeColor } = storeToRefs(cuttingStore)

// __ Высота данных
const dataHeight = computed(() => globalCuttingTaskTimesShow.value ? 'h-[60px]' : 'h-[30px]')

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
        data:     () => props.item.cutting_lines.reduce((acc, item) => acc + item.amount, 0).toString(),
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

// __ Цвет
const color = computed(() => {
    // if (props.item.order.id === props.orderId) {
    //     return 'red'
    // }

    // __ Если цвет по типу заявки, то берем его, или по статусу движения
    return globalCuttingTaskOrderTypeColor.value ? props.item.order.order_type.color : props.item.current_status.color
})

// __ Анимация, СЗ для текущей Заявки
const animatedClass = computed(() => {
    if (props.item.order.id === props.orderId) {
        return 'plan-item  animate-pulse'
    }
    return 'plan-item'
})


</script>

<style scoped>
.plan-item {
    @apply cursor-pointer truncate;
}


</style>
