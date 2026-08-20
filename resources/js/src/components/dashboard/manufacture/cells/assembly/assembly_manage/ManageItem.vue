<template>
    <!-- __ Тут именно -1, т.к. id = 0 - это заглушка для добавления нового элемента -->
    <div v-if="item.id > -1" class="flex">

        <!-- __ Смена -->
        <!--<AppLabelMultiLineTS-->
        <!--    v-if="render.change.show"-->
        <!--    :align="render.change.align"-->
        <!--    :class="animatedClass"-->
        <!--    :color="color"-->
        <!--    :height="dataHeight"-->
        <!--    :text="render.change.data()"-->
        <!--    :text-size="render.change.textSize"-->
        <!--    :type="render.change.type"-->
        <!--    :width="render.change.width"-->
        <!--    rounded="rounded-[4px]"-->
        <!--/>-->

        <!-- __ Клиент -->
        <AppLabelMultiLineTS
            v-if="render.client.show"
            :align="render.client.align"
            :class="animatedClass"
            :color="color"
            :height="dataHeight"
            :text="globalAssemblyTaskTimesShow ? [render.client.data(), formattedLoadDate] : render.client.data()"
            :text-size="render.client.textSize"
            :type="render.client.type"
            :width="render.client.width"
            rounded="rounded-[4px]"
        />

        <!-- __ Номер заявки -->
        <AppLabelMultiLineTS
            v-if="render.orderNo.show"
            :align="render.orderNo.align"
            :class="animatedClass"
            :color="color"
            :height="dataHeight"
            :text="globalAssemblyTaskTimesShow ? [render.orderNo.data(), ''] : render.orderNo.data()"
            :text-size="render.orderNo.textSize"
            :type="render.orderNo.type"
            :width="render.orderNo.width"
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
            :time-show="globalAssemblyTaskTimesShow"
            :type="render.amount.type"
            :width="render.amount.width"
        />

        <!-- __ Данные по каждому участку -->
        <div v-for="dataItem of percentsRender" :key="dataItem.name" class="flex">
            <AppLabelTS
                :color="dataItem.color"
                :text="dataItem.title"
                align="center"
                rounded="4"
                text-size="micro"
                width="w-[40px]"
                :height="globalAssemblyTaskTimesShow ? 'h-[60px]' : 'h-[25px]'"
            />
        </div>


        <!-- __ Количество + Трудозатраты Линия 1 -->
        <!--<ManageItemDataLabel-->
        <!--    v-if="globalAssemblyTaskFullDaysShow"-->
        <!--    :align="render.amount.align"-->
        <!--    :amount="amountAndTime[ASSEMBLY_MANUF_LINES.LINE_1].amount"-->
        <!--    :square="amountAndTime[ASSEMBLY_MANUF_LINES.LINE_1].square"-->
        <!--    :class="animatedClass"-->
        <!--    :color="color"-->
        <!--    :height="dataHeight"-->
        <!--    :text-size="render.amount.textSize"-->
        <!--    :time="amountAndTime[ASSEMBLY_MANUF_LINES.LINE_1].time"-->
        <!--    :time-show="globalAssemblyTaskTimesShow"-->
        <!--    :square-show="globalAssemblyTaskAssemblyInSquare"-->
        <!--    :type="render.amount.type"-->
        <!--    :width="render.amount.width"-->
        <!--/>-->

        <!-- __ Количество + Трудозатраты Линия 2 -->
        <!--<ManageItemDataLabel-->
        <!--    v-if="globalAssemblyTaskFullDaysShow"-->
        <!--    :align="render.amount.align"-->
        <!--    :amount="amountAndTime[ASSEMBLY_MANUF_LINES.LINE_2].amount"-->
        <!--    :square="amountAndTime[ASSEMBLY_MANUF_LINES.LINE_2].square"-->
        <!--    :class="animatedClass"-->
        <!--    :color="color"-->
        <!--    :height="dataHeight"-->
        <!--    :text-size="render.amount.textSize"-->
        <!--    :time="amountAndTime[ASSEMBLY_MANUF_LINES.LINE_2].time"-->
        <!--    :time-show="globalAssemblyTaskTimesShow"-->
        <!--    :square-show="globalAssemblyTaskAssemblyInSquare"-->
        <!--    :type="render.amount.type"-->
        <!--    :width="render.amount.width"-->
        <!--/>-->

        <!-- __ Количество + Трудозатраты Неопознанные -->
        <!--<ManageItemDataLabel-->
        <!--    v-if="globalAssemblyTaskFullDaysShow"-->
        <!--    :align="render.amount.align"-->
        <!--    :amount="amountAndTime[ASSEMBLY_MANUF_LINES.LINE_0].amount"-->
        <!--    :square="amountAndTime[ASSEMBLY_MANUF_LINES.LINE_0].square"-->
        <!--    :class="animatedClass"-->
        <!--    :color="amountAndTime[ASSEMBLY_MANUF_LINES.LINE_0].amount === 0 ? color : 'red'"-->
        <!--    :height="dataHeight"-->
        <!--    :text-size="render.amount.textSize"-->
        <!--    :time="amountAndTime[ASSEMBLY_MANUF_LINES.LINE_0].time"-->
        <!--    :time-show="globalAssemblyTaskTimesShow"-->
        <!--    :square-show="globalAssemblyTaskAssemblyInSquare"-->
        <!--    :type="render.amount.type"-->
        <!--    :width="render.amount.width"-->
        <!--/>-->

    </div>

</template>

<script lang="ts" setup>
import { computed, reactive, } from 'vue'

import { storeToRefs } from 'pinia'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'

import type {
    IHorizontalAlign,
    IAssemblyTask,
    IFontsType,
    IColorTypes,
    IAmountAndTimeAssembly, IAssemblySectorKeys
} from '@/types'

import { ASSEMBLY_SECTORS, ASSEMBLY_TASK_DRAFT, } from '@/app/constants/assembly.ts'
// import { DEBUG } from '@/app/constants/common.ts'

import { formatDateInFullFormat } from '@/app/helpers/helpers_date'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import ManageItemDataLabel
    from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageItemDataLabel.vue'
import { getChangeByName } from '@/app/helpers/manufacture/helpers_assembly.ts'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import type { IStats } from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageDay.vue'

interface IProps {
    amountAndTime: IAmountAndTimeAssembly
    percents: IStats[]
    item?: IAssemblyTask
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

    item        : () => ASSEMBLY_TASK_DRAFT,
    columnsWidth: () => ({
        client : 'w-[90px]',
        amount : 'w-[50px]',
        orderNo: 'w-[50px]',
        common : 'w-[164px]',
    }),
    index       : 0,
    orderId     : null,
})

// __ Данные из Хранилища
const assemblyStore = useAssemblyStore()

const {
          globalAssemblyTaskOrderTypeColor,
          globalAssemblyTaskTimesShow,
          globalAssemblyTaskFullDaysShow,
          globalAssemblyTaskSectorsShow,
      } = storeToRefs(assemblyStore)

// __ Высота данных
const dataHeight = computed(() => globalAssemblyTaskTimesShow.value ? 'h-[60px]' : 'h-[30px]')

// __ Получаем объект Смены
const change = computed(() => getChangeByName(props.item))

// __ Подготавливаем рендер
const render: IRender = reactive({
    change : {
        show    : true,
        width   : props.columnsWidth.change,
        type    : change.value ? change.value.TYPE : 'dark',
        align   : 'center',
        data    : () => change.value ? change.value.TITLE : '',
        textSize: 'huge',
    },
    client : {
        show    : true,
        width   : props.columnsWidth.client,
        type    : 'dark',
        align   : 'left',
        data    : () => `${props.item.position}. ${props.item.order.client.short_name}`,
        textSize: 'micro',
    },
    orderNo: {
        show    : true,
        width   : props.columnsWidth.orderNo,
        type    : 'dark',
        align   : 'center',
        data    : () => props.item.order.order_no_str,
        textSize: 'micro',
    },
    amount : {
        show    : true,
        width   : props.columnsWidth.amount,
        type    : 'dark',
        align   : 'center',
        data    : () => props.item.assembly_lines.reduce((acc, item) => acc + item.amount, 0).toString(),
        textSize: 'micro',
    },
})

// __ Общее Количество
const getTotalAmount = computed(() => props.item.assembly_lines.reduce((acc, line) => line.amount + acc, 0))
// const getTotalAmount = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.amount + acc, 0))


// __ Общее Трудозатраты
const getTotalTime = computed(() => props.item.assembly_lines.reduce((acc, line) => line.time + acc, 0))
// const getTotalTime = computed(() => Object.values(props.amountAndTime).reduce((acc, item) => item.time + acc, 0))

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
    return globalAssemblyTaskOrderTypeColor.value ? props.item.order.order_type.color : props.item.current_status.color
})

// __ Анимация, СЗ для текущей Заявки
const animatedClass = computed(() => {
    if (props.item.order.id === props.orderId) {
        return 'plan-item  animate-pulse'
    }
    return 'plan-item'
})


// __ Вычисление отображаемых участков
const percentsRender = computed(() => {
    let result = props.percents
    if (!globalAssemblyTaskSectorsShow.value) {
        const hiddenSectors = new Set<IAssemblySectorKeys>([
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_COCONUT.NAME,
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_LATEX.NAME,
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_FOAM_LAYER.NAME,
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_FOAM_SIDE.NAME,
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_LAYER.NAME,
        ]);

        result = result.filter(item => !hiddenSectors.has(item.name));
    }

    if (!globalAssemblyTaskFullDaysShow.value) {
        const hiddenSectors = new Set<IAssemblySectorKeys>([
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_LAMIT.NAME,
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_TABLE.NAME,
        ]);

        result = result.filter(item => !hiddenSectors.has(item.name));
    }

    return result
})

</script>

<style scoped>
.plan-item {
    @apply cursor-pointer truncate;
}


</style>
