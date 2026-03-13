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

            <!-- __ Collapsed -->
            <!--<AppLabelTSWrapper :render-object="render.collapsed" header @click="toggleCollapsed"/>-->

            <!-- __ Тип изделия -->
            <AppLabelTSWrapper :render-object="render.modelType" header />

            <!-- __ Размер -->
            <AppLabelTSWrapper :render-object="render.modelSize" header />

            <!-- __ Название Модели -->
            <AppLabelTSWrapper :render-object="render.modelName" header />

            <!-- __ Количество -->
            <AppLabelTSWrapper :render-object="render.modelAmount" header />

            <!-- __ Состава изделия -->
            <AppLabelTSWrapper :render-object="render.composition" header />

            <!-- __ Примечание 1 -->
            <AppLabelTSWrapper :render-object="render.describe_1" header />

            <!-- __ Примечание 2 -->
            <AppLabelTSWrapper :render-object="render.describe_2" header />

            <!-- __ Примечание 3 -->
            <AppLabelTSWrapper :render-object="render.describe_3" header />

            <!-- __ Кнопка удалить -->
            <AppLabelTSWrapper :render-object="render.deleteButton" header />

        </div>
    </div>

    <!-- __ Данные -->
    <div v-for="orderLine of orderLinesRender" :key="orderLine.id">
        <div class="flex ">

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

            <!-- __ Collapsed -->
            <!--<AppLabelTSWrapper :arg="orderLine" :render-object="render.collapsed" @click="render.collapsed.click!(orderLine)"/>-->

            <!-- __ Тип изделия -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelType" />

            <!-- __ Размер -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelSize" />

            <!-- __ Название -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelName" />

            <!-- __ Количество -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelAmount" />

            <!-- __ Состава изделия -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.composition" />

            <!-- __ Примечание 1 -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.describe_1" />

            <!-- __ Примечание 2 -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.describe_2" />

            <!-- __ Примечание 3 -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.describe_3" />

            <!-- __ Кнопка удалить -->
            <AppLabelTSWrapper
                :arg="orderLine"
                :render-object="render.deleteButton"
                @click="emits('deleteLine', orderLine.id)"
            />

        </div>

    </div>

</template>

<script lang="ts" setup>
import { reactive, /*ref,*/ computed } from 'vue'

import type { IRenderData, IRenderOrderLine } from '@/types'

// import { formatDateIntl } from '@/app/helpers/helpers_date.js'
// import AppInputDateTS from '@/components/ui/inputs/AppInputDateTS.vue'

import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    orderLines: IRenderOrderLine[]
    showCollapsed?: boolean
    showDelete?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    showCollapsed: true,
    showDelete:    false,
})

const emits = defineEmits<{
    (e: 'deleteLine', id: number): void
}>()

// __ Определяем переменные
const orderLinesRender = computed<IRenderOrderLine[]>(() => props.orderLines)


// __ Возможность редактирования
// TODO: Реализовать через систему ролей
// const canEdit = ref(false)


// __ Объект отображения данных
// const DEFAULT_WIDTH      = 'w-[100px]'
// const DEFAULT_WIDTH_DATE = 'w-[140px]'
const DEFAULT_HEIGHT     = 'h-[30px]'
const HEADER_TYPE        = 'indigo'
const DATA_TYPE          = 'primary'
const DEFAULT_TYPE       = 'dark'
const HEADER_TEXT_SIZE   = 'micro'
const DATA_TEXT_SIZE     = 'micro'
const HEADER_ALIGN       = 'center'
// const DATA_ALIGN_DEFAULT = 'center'
const DATA_ALIGN         = 'left'


const render: IRenderData = reactive({
    collapsed: {
        header:         '▲',
        width:          'w-[30px]',
        height:         DEFAULT_HEIGHT,
        show:           props.showCollapsed,
        headerType:     () => 'warning',
        dataType:       () => DATA_TYPE,
        type:           () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        data:           (/*orderLine: IRenderOrderLine*/) => '▲',
        // data: (orderLine: IRenderOrderLine) => orderLine.collapsed ? '▲' : '▼',
        // data: (dotLogistic: IDotLogistic) => console.log(dotLogistic),
        click: (orderLine: IRenderOrderLine) => console.log(orderLine),
        // click: (orderLine: IRenderOrderLine) => dotLogistic.collapsed = !dotLogistic.collapsed
        // click: (dotLogistic: IDotLogistic) => console.log(dotLogistic)
    },


    modelType:   {
        header:         'Тип изделия',
        width:          'w-[110px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      'center',
        data:           (orderLine: IRenderOrderLine) => orderLine.model.model_type,
    },
    modelSize:   {
        header:         'Размер',
        width:          'w-[80px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      'center',
        data:           (orderLine: IRenderOrderLine) => orderLine.size,
    },
    modelName:   {
        header:         'Модель',
        width:          'w-[200px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        data:           (orderLine: IRenderOrderLine) => orderLine.model.name_report,
    },
    modelAmount: {
        header:         'Кол-во',
        width:          'w-[50px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      'center',
        data:           (orderLine: IRenderOrderLine) => orderLine.amount.toString(),
    },
    composition: {
        header:         'Комментарий',
        width:          'w-[248px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        data:           (orderLine: IRenderOrderLine) => orderLine.composition,
    },
    describe_1:  {
        header:         'Примечание 1',
        width:          'w-[174px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        data:           (orderLine: IRenderOrderLine) => orderLine.describe_1,
    },
    describe_2:  {
        header:         'Примечание 2',
        width:          'w-[175px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        data:           (orderLine: IRenderOrderLine) => orderLine.describe_2,
    },
    describe_3:  {
        header:         'Примечание 3',
        width:          'w-[175px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        data:           (orderLine: IRenderOrderLine) => orderLine.describe_3,
    },
    deleteButton:  {
        header:         '🗑️',
        width:          'w-[40px]',
        height:         DEFAULT_HEIGHT,
        show:           props.showDelete,
        headerType:     () => 'orange',
        dataType:       () => DATA_TYPE,
        type:           () => 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      'center',
        data:           (/*orderLine: IRenderOrderLine*/) => '🗑️',
    },
})

</script>

<style scoped>
    /*@reference "@css/app.css";*/

/*    .input-date {
        @apply mr-0.5 ml-0.5 mt-[2px]
    }*/
</style>
