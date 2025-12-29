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

            <!-- __ Размер -->
            <AppLabelTSWrapper :render-object="render.modelSize" header/>

            <!-- __ Название Модели -->
            <AppLabelTSWrapper :render-object="render.modelName" header/>

            <!-- __ Количество -->
            <AppLabelTSWrapper :render-object="render.modelAmount" header/>

            <!-- __ Состава изделия -->
            <AppLabelTSWrapper :render-object="render.composition" header/>

            <!-- __ Примечание 1 -->
            <AppLabelTSWrapper :render-object="render.describe_1" header/>

            <!-- __ Примечание 2 -->
            <AppLabelTSWrapper :render-object="render.describe_2" header/>

            <!-- __ Примечание 3 -->
            <AppLabelTSWrapper :render-object="render.describe_3" header/>

            <!-- __ Результат проверки -->
            <AppLabelTSWrapper :render-object="render.validateCheck" header/>

            <!-- __ Действие -->
            <AppLabelTSWrapper :render-object="render.validateAction" header/>

            <!-- __ Описание -->
            <AppLabelTSWrapper :render-object="render.validateAdvice" header/>

        </div>
    </div>

    <!-- __ Данные -->
    <div v-for="(orderItem, index) of orderItemsRender" :key="index">
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

            <AppLabelTSWrapper :arg="orderItem" :render-object="render.collapsed"/>

            <!--<AppLabelTS-->
            <!--    v-if="render.collapsed.show"-->
            <!--    :width="render.collapsed.width"-->
            <!--    align="center"-->
            <!--    text=""-->
            <!--    text-size="normal"-->
            <!--    type="light"-->
            <!--/>-->

            <!-- __ Размер -->
            <AppLabelTSWrapper :arg="orderItem" :render-object="render.modelSize"/>

            <!-- __ Название -->
            <AppLabelTSWrapper :arg="orderItem" :render-object="render.modelName"/>

            <!-- __ Количество -->
            <AppLabelTSWrapper :arg="orderItem" :render-object="render.modelAmount"/>

            <!-- __ Состав изделия -->
            <AppLabelTSWrapper :arg="orderItem" :render-object="render.composition"/>

            <!-- __ Примечание 1 -->
            <AppLabelTSWrapper :arg="orderItem" :render-object="render.describe_1"/>

            <!-- __ Примечание 2 -->
            <AppLabelTSWrapper :arg="orderItem" :render-object="render.describe_2"/>

            <!-- __ Примечание 3 -->
            <AppLabelTSWrapper :arg="orderItem" :render-object="render.describe_3"/>

            <!-- __ Результат проверки -->
            <AppLabelTSWrapper :arg="orderItem" :render-object="render.validateCheck"/>

            <!-- __ Действие -->
            <AppLabelTSWrapper :arg="orderItem" :render-object="render.validateAction"/>

            <!-- __ Описание -->
            <AppLabelTSWrapper :arg="orderItem" :render-object="render.validateAdvice"/>

        </div>


    </div>

</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue'

import type { IRenderData, IValidatedOrderItem } from '@/types'

import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    orderItems: IValidatedOrderItem[]
    okWord?: string
}

const props = withDefaults(defineProps<IProps>(), {
    okWord: 'ok',
})


// __ Определяем переменные
const orderItemsRender = ref<IValidatedOrderItem[]>(props.orderItems)


// __ Возможность редактирования
// TODO: Реализовать через систему ролей
const canEdit = ref(false)


// __ Объект отображения данных
const DEFAULT_WIDTH    = 'w-[89px]'
// const WIDTH = 'w-[300px]'
// const DEFAULT_WIDTH_DATE = 'w-[140px]'
const DEFAULT_HEIGHT   = 'h-[30px]'
const HEADER_TYPE      = 'indigo'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'micro'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'

const OK_WORD = props.okWord

const render: IRenderData = reactive({
    collapsed: {
        header:         '▲',
        width:          'w-[30px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => 'warning',
        dataType:       () => DATA_TYPE,
        type:           (orderItem: IValidatedOrderItem) => orderItem.validate.check === OK_WORD ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        data:           (orderItem: IValidatedOrderItem) => orderItem.validate.check === OK_WORD ? '✓' : '✗'
    },

    modelSize:      {
        header:         'Размер',
        width:          'w-[65px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (orderItem: IValidatedOrderItem) => orderItem.validate.check === OK_WORD ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      'center',
        data:           (orderItem: IValidatedOrderItem) => orderItem.s
    },
    modelName:      {
        header:         'Модель',
        width:          'w-[106px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (orderItem: IValidatedOrderItem) => orderItem.validate.check === OK_WORD ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        data:           (orderItem: IValidatedOrderItem) => orderItem.n
    },
    modelAmount:    {
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
        data:           (orderItem: IValidatedOrderItem) => orderItem.a.toString()
    },
    composition:    {
        header:         'Комментарий',
        width:          DEFAULT_WIDTH,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        class:          'truncate',
        data:           (orderItem: IValidatedOrderItem) => orderItem.d
    },
    describe_1:     {
        header:         'Примечание 1',
        width:          DEFAULT_WIDTH,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        class:          'truncate',
        data:           (orderItem: IValidatedOrderItem) => orderItem.d1
    },
    describe_2:     {
        header:         'Примечание 2',
        width:          DEFAULT_WIDTH,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        class:          'truncate',
        data:           (orderItem: IValidatedOrderItem) => orderItem.d2
    },
    describe_3:     {
        header:         'Примечание 3',
        width:          DEFAULT_WIDTH,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        class:          'truncate',
        data:           (orderItem: IValidatedOrderItem) => orderItem.d3
    },
    validateCheck:  {
        header:         'Результат проверки',
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
        data:           (orderItem: IValidatedOrderItem) => orderItem.validate.check
    },
    validateAction: {
        header:         'Действие',
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
        data:           (orderItem: IValidatedOrderItem) => orderItem.validate.action
    },
    validateAdvice: {
        header:         'Описание',
        width:          'w-[350px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    'center',
        dataAlign:      DATA_ALIGN,
        data:           (orderItem: IValidatedOrderItem) => orderItem.validate.advice
    },

})


</script>

<style scoped>
/*@reference "@css/app.css";*/

.input-date {
    @apply mr-0.5 ml-0.5 mt-[2px]
}
</style>
