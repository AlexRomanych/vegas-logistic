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
            title="Double Click - Меню"
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
            title="Double Click - Меню"
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
            title="Double Click - Меню"
        />

        <!-- __ Данные по каждому участку -->
        <div v-for="dataItem of percentsRender" :key="dataItem.name" class="flex">
            <AppLabelMultiLineTS
                :color="dataItem.color"
                :height="dataHeight"
                :heightLimit="dataHeight"
                :text="dataItem.titleArr"
                :title="'Double Click - Меню\nCtrl+Click - Перейти на Участок'"
                align="center"
                class="cursor-pointer"
                rounded="4"
                text-size="micro"
                width="w-[40px]"
                @click.ctrl="goToSector(dataItem)"
            />
        </div>

    </div>

</template>

<script lang="ts" setup>
import { computed, reactive, } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'

import type {
    IHorizontalAlign,
    IAssemblyTask,
    IFontsType,
    IColorTypes,
    IAssemblySectorKeys,
    IStats,
    ITotalsDataType
} from '@/types'

import { ASSEMBLY_SECTORS, ASSEMBLY_TASK_DRAFT, DATA_TYPE_NOTHING, REDIRECT_KEY, } from '@/app/constants/assembly.ts'
// import { DEBUG } from '@/app/constants/common.ts'

import { formatDateInFullFormat } from '@/app/helpers/helpers_date'
import { getChangeByName, getDataArray } from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import ManageItemDataLabel
    from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageItemDataLabel.vue'

interface IProps {
    item?: IAssemblyTask
    columnsWidth?: Record<string, string>
    index?: number
    orderId?: number | null     // __ Для подсветки СЗ для Заявки
    dataType?: ITotalsDataType
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
    dataType    : DATA_TYPE_NOTHING,
})

const router = useRouter()

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

// __ Общее Трудозатраты
const getTotalTime = computed(() => props.item.assembly_lines.reduce((acc, line) => line.time + acc, 0))

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

// __ Вычисляем объект отображения
const percents = computed(() => getDataArray(props.item, props.dataType))

// __ Вычисление отображаемых участков
const percentsRender = computed(() => {
    let result = percents.value

    // __ Исключаем Заявку_Ф всегда
    const hiddenSectors = new Set<IAssemblySectorKeys>([
        ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_COMMON.NAME,
    ])

    result = result.filter(item => !hiddenSectors.has(item.name))


    if (!globalAssemblyTaskSectorsShow.value) {
        const hiddenSectors = new Set<IAssemblySectorKeys>([
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_COCONUT.NAME,
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_LATEX.NAME,
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_FOAM_LAYER.NAME,
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_FOAM_SIDE.NAME,
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_LAYER.NAME,
        ])

        result = result.filter(item => !hiddenSectors.has(item.name))
    }

    if (!globalAssemblyTaskFullDaysShow.value) {
        const hiddenSectors = new Set<IAssemblySectorKeys>([
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_LAMIT.NAME,
            ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_TABLE.NAME,
        ])

        result = result.filter(item => !hiddenSectors.has(item.name))
    }

    return result
})

// __ Переход на нужный Участок и нужное СЗ
const goToSector = (dataItem: IStats) => {
    localStorage.setItem(REDIRECT_KEY, JSON.stringify({
        task_id  : props.item.id,
        sector_id: dataItem.id,
    }))

    router.push({
        name  : 'manufacture.cell.assembly.manipulate.day',
        params: {
            date: props.item.action_at.split(' ')[0],
        },
    })

    // console.log('dataItem: ', dataItem)
}

</script>

<style scoped>
.plan-item {
    @apply cursor-pointer truncate;
}

</style>
