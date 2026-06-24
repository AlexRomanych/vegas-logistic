<template>

    <div>
        <div class="flex">

            <!-- __ Collapsed -->
            <AppLabelTS
                v-if="render.collapsed.show"
                :width="render.collapsed.width"
                align="center"
                text=""
                text-size="normal"
                type="light"
            />

            <AppLabelTS
                v-if="render.collapsed.show"
                :width="render.collapsed.width"
                align="center"
                text=""
                text-size="normal"
                type="light"
            />

            <!-- __ Collapsed Materials -->
            <AppLabelTSWrapper :render-object="render.collapsed_materials" header/>

            <!-- __ Collapsed CuttingDetails -->
            <AppLabelTSWrapper :render-object="render.collapsed_cutting_details" header/>

            <!-- __ Collapsed -->
            <!--<AppLabelTSWrapper :render-object="render.collapsed" header @click="toggleCollapsed"/>-->

            <!-- __ Тип изделия -->
            <AppLabelTSWrapper :render-object="render.modelType" header/>

            <!-- __ Размер -->
            <AppLabelTSWrapper :render-object="render.modelSize" header/>

            <!-- __ Название Модели -->
            <AppLabelTSWrapper :render-object="render.modelName" header/>

            <!-- __ Количество -->
            <AppLabelTSWrapper :render-object="render.modelAmount" header/>

            <!-- __ Ткань -->
            <AppLabelTSWrapper :render-object="render.textile" header/>

            <!-- __ Состава изделия -->
            <AppLabelTSWrapper :render-object="render.composition" header/>

            <!-- __ Примечание 1 -->
            <AppLabelTSWrapper :render-object="render.describe_1" header/>

            <!-- __ Примечание 2 -->
            <AppLabelTSWrapper :render-object="render.describe_2" header/>

            <!-- __ Примечание 3 -->
            <AppLabelTSWrapper :render-object="render.describe_3" header/>

            <!-- __ Спецификация -->
            <AppLabelTSWrapper :render-object="render.specification" header/>

            <!-- __ Дополнительная Спецификация -->
            <AppLabelTSWrapper :render-object="render.specification_add" header/>

            <!-- __ Кнопка удалить -->
            <AppLabelTSWrapper :render-object="render.deleteButton" header/>

        </div>
    </div>

    <!-- __ Данные -->
    <div v-for="orderLine of orderLinesRender" :key="orderLine.id">
        <div class="flex" @dblclick="showLineInfo(orderLine)">

            <!-- __ Collapsed -->
            <AppLabelTS
                v-if="render.collapsed.show"
                :width="render.collapsed.width"
                align="center"
                text=""
                text-size="normal"
                type="light"
            />

            <AppLabelTS
                v-if="render.collapsed.show"
                :width="render.collapsed.width"
                align="center"
                text=""
                text-size="normal"
                type="light"
            />

            <!-- __ Collapsed Materials -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.collapsed_materials" @click="render.collapsed_materials.click!(orderLine)"/>

            <!-- __ Collapsed CuttingDetails -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.collapsed_cutting_details" @click="render.collapsed_cutting_details.click!(orderLine)"/>

            <!-- __ Тип изделия -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelType"/>

            <!-- __ Размер -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelSize"/>

            <!-- __ Название -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelName"/>

            <!-- __ Количество -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelAmount"/>

            <!-- __ Ткань -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.textile"/>

            <!-- __ Состава изделия -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.composition"/>

            <!-- __ Примечание 1 -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.describe_1"/>

            <!-- __ Примечание 2 -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.describe_2"/>

            <!-- __ Примечание 3 -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.describe_3"/>

            <!-- __ Спецификация -->
            <AppLabelTSWrapper
                :arg="orderLine"
                :render-object="render.specification"
                @dblclick.stop="showSpecification(orderLine.specification?.code_1c)"
            />

            <!-- __ Дополнительная Спецификация -->
            <AppLabelTSWrapper
                :arg="orderLine"
                :render-object="render.specification_add"
                @dblclick.stop="showSpecification(orderLine.specification_add?.code_1c)"
            />

            <!-- __ Кнопка удалить -->
            <AppLabelTSWrapper
                :arg="orderLine"
                :render-object="render.deleteButton"
                class="cursor-pointer"
                @click="emits('deleteOrderLine', orderLine)"
            />

        </div>

        <!-- __ Расходы материалов -->
        <template v-if="showMaterials">
            <div v-if="!orderLine.collapsed_materials" class="ml-[74px]">
                <OrderLineMaterials :line="orderLine"/>
            </div>
        </template>

        <!-- __ Детальки СЗ для Раскроя -->
        <template v-if="showCuttingDetails">
            <div v-if="!orderLine.collapsed_cutting_details" class="ml-[74px]">
                <OrderLineCuttingDetail :line="orderLine"/>
            </div>
        </template>

    </div>

    <!-- __ Модальное окно для информации о записи -->
    <OrderItemInfo
        ref="orderItemInfo"
        :order-line="orderLine"
    />

    <!-- __ Карточка Спецификации -->
    <CardSpecification
        ref="cardSpecification"
        :construct="modelConstruct"
    />

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />

</template>


<script lang="ts" setup>
import { reactive, /*ref,*/ computed, ref } from 'vue'

import type { IColorTypes, IModelConstruct, IRenderData, IRenderOrderLine } from '@/types'

import { useModelsStore } from '@/stores/ModelsStore.ts'

// import { formatDateIntl } from '@/app/helpers/helpers_date.js'
// import AppInputDateTS from '@/components/ui/inputs/AppInputDateTS.vue'

import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import OrderItemInfo from '@/components/dashboard/orders/order_components/order_common/OrderItemInfo.vue'
import OrderLineMaterials from '@/components/dashboard/orders/order_components/order_render/OrderLineMaterials.vue'
import OrderLineCuttingDetail from '@/components/dashboard/orders/order_components/order_render/OrderLineCuttingDetail.vue'
import CardSpecification from '@/components/dashboard/models/components/CardSpecification.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'


interface IProps {
    orderLines: IRenderOrderLine[]
    showCollapsed?: boolean
    showDelete?: boolean
    showMaterials?: boolean
    showCuttingDetails?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    showCollapsed     : true,
    showDelete        : false,
    showMaterials     : false,
    showCuttingDetails: false,
})

const emits = defineEmits<{
    (e: 'deleteOrderLine', payload: IRenderOrderLine): void
}>()

const modelsStore = useModelsStore()

// __ Определяем переменные
const orderLinesRender = computed<IRenderOrderLine[]>(() => props.orderLines)


// __ Возможность редактирования
// TODO: Реализовать через систему ролей
// const canEdit = ref(false)


// __ Объект отображения данных
// const DEFAULT_WIDTH      = 'w-[100px]'
// const DEFAULT_WIDTH_DATE = 'w-[140px]'
const DEFAULT_HEIGHT   = 'h-[30px]'
const HEADER_TYPE      = 'indigo'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'micro'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
// const DATA_ALIGN_DEFAULT = 'center'
const DATA_ALIGN       = 'left'


const render: IRenderData = reactive({
    collapsed                : {
        header        : '▲',
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : props.showCollapsed,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : (/*orderLine: IRenderOrderLine*/) => '▲',
        // data: (orderLine: IRenderOrderLine) => orderLine.collapsed ? '▲' : '▼',
        // data: (dotLogistic: IDotLogistic) => console.log(dotLogistic),
        click: (orderLine: IRenderOrderLine) => console.log(orderLine),
        // click: (orderLine: IRenderOrderLine) => dotLogistic.collapsed = !dotLogistic.collapsed
        // click: (dotLogistic: IDotLogistic) => console.log(dotLogistic)
    },
    collapsed_materials      : {
        header        : '▲▼',
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : props.showMaterials,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : (orderLine: IRenderOrderLine) => orderLine.collapsed_materials ? '▲' : '▼',
        click         : (orderLine: IRenderOrderLine) => orderLine.collapsed_materials = !orderLine.collapsed_materials
    },
    collapsed_cutting_details: {
        header        : '▲▼',
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : props.showCuttingDetails,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : (orderLine: IRenderOrderLine) => orderLine.collapsed_cutting_details ? '▲' : '▼',
        click         : (orderLine: IRenderOrderLine) => orderLine.collapsed_cutting_details = !orderLine.collapsed_cutting_details
    },
    modelType                : {
        header        : 'Тип изделия',
        width         : 'w-[110px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (orderLine: IRenderOrderLine) => orderLine.model.model_type,
    },
    modelSize                : {
        header        : 'Размер',
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (orderLine: IRenderOrderLine) => orderLine.size,
    },
    modelName                : {
        header        : 'Модель',
        width         : 'w-[200px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (orderLine: IRenderOrderLine) => orderLine.model.name_report,
    },
    modelAmount              : {
        header        : 'Кол-во',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (orderLine: IRenderOrderLine) => orderLine.amount.toString(),
    },
    textile                  : {
        header        : 'Ткань',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (orderLine: IRenderOrderLine) => hasCuttingDetailsUndefinedTable(orderLine) ? 'danger' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (orderLine: IRenderOrderLine) => orderLine.textile,
    },
    composition              : {
        header        : 'Комментарий',
        width         : 'w-[248px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (orderLine: IRenderOrderLine) => orderLine.composition,
    },
    describe_1               : {
        header        : 'Примечание 1',
        width         : 'w-[174px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (orderLine: IRenderOrderLine) => orderLine.describe_1,
    },
    describe_2               : {
        header        : 'Примечание 2',
        width         : 'w-[175px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (orderLine: IRenderOrderLine) => orderLine.describe_2,
    },
    describe_3               : {
        header        : 'Примечание 3',
        width         : 'w-[175px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (orderLine: IRenderOrderLine) => orderLine.describe_3,
    },
    specification            : {
        header        : 'Спецификация',
        width         : 'w-[175px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (orderLine: IRenderOrderLine) => orderLine.specification ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        title         : 'Двойной клик ЛКМ - Показать Спецификацию',
        data          : (orderLine: IRenderOrderLine) => orderLine.spec_name ?? '',
        class         : (orderLine: IRenderOrderLine) => orderLine?.specification ? 'cursor-pointer' : '',
    },
    specification_add        : {
        header        : 'Доп. спецификация',
        width         : 'w-[175px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (orderLine: IRenderOrderLine) => orderLine.specification_add ? 'success' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        title         : 'Двойной клик ЛКМ - Показать Спецификацию',
        data          : (orderLine: IRenderOrderLine) => orderLine.spec_name_add ?? '',
        class         : (orderLine: IRenderOrderLine) => orderLine?.specification_add ? 'cursor-pointer' : '',
    },
    deleteButton             : {
        header        : '🗑️',
        width         : 'w-[40px]',
        height        : DEFAULT_HEIGHT,
        show          : props.showDelete,
        headerType    : () => 'orange',
        dataType      : () => DATA_TYPE,
        type          : () => 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (/*orderLine: IRenderOrderLine*/) => '🗑️',
    },
})

// __ Тип для модального окна информации о записи в Заявке
const orderLine     = ref<IRenderOrderLine | null>(null)
const orderItemInfo = ref<InstanceType<typeof OrderItemInfo> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Смотрим, есть ли в СЗ Раскроя неопределенный стол
const hasCuttingDetailsUndefinedTable = (line: IRenderOrderLine) => {
    if (!props.showCuttingDetails) {
        return false
    }
    let hasUndefinedTable = false
    line.cutting_lines?.forEach(cutting_line => {
        hasUndefinedTable ||= cutting_line.table === 'undefined'
    })

    return hasUndefinedTable
}

// __ Показать информацию о записи
const showLineInfo = async (line: IRenderOrderLine) => {
    orderLine.value = line
    await orderItemInfo.value!.show() // показываем модалку и ждем ответ
}

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null)


// __ Показываем сообщение об ошибке
// const showError = async (error: string | null = null) => {
//     modalInfoType.value = 'danger'
//     modalInfoMode.value = 'inform'
//     modalInfoText.value = error ? [error] : ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
//     await appModalAsyncMultilineTS.value!.show()
// }


// __ Карточка Спецификаций
const cardSpecification = ref<InstanceType<typeof CardSpecification> | null>(null)
const modelConstruct    = ref<IModelConstruct | null>(null)

// __ Показываем спецификацию
const showSpecification = async (code_1c: string | null | undefined) => {
    if (!code_1c) {
        return
    }

    const construct = await modelsStore.getConstructByCode1c(code_1c)
    if (!construct) {
        modalInfoType.value = 'danger'
        modalInfoText.value = [
            `Спецификация с кодом: ${code_1c}`,
            'не найдена!'
        ]
        await appModalAsyncMultilineTS.value?.show()
        return
    }

    modelConstruct.value = construct
    await cardSpecification.value?.show()
}

</script>

<style scoped>
/*@reference "@css/app.css";*/

/*    .input-date {
        @apply mr-0.5 ml-0.5 mt-[2px]
    }*/
</style>
