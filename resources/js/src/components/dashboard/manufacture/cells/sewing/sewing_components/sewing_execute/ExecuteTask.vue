<template>

    <div :class="[!sewingTask.collapsed ? 'mt-1' : '']" class="flex">

        <!-- __ Collapsed -->
        <AppLabelTSWrapper
            :render-object="render.collapsed"
            @click="sewingTask.collapsed = !sewingTask.collapsed"
        />

        <!-- __ id -->
        <AppLabelTSWrapper :render-object="render.id" />

        <!-- __ position -->
        <AppLabelTSWrapper :render-object="render.position" />

        <!-- __ Клиент -->
        <AppLabelTSWrapper :render-object="render.client" />

        <!-- __ № Заявки -->
        <AppLabelTSWrapper :render-object="render.order_no" />

        <!-- __ Статус  -->
        <AppLabelTSWrapper :render-object="render.status" />

        <!-- __ Дата загрузки  -->
        <AppLabelTSWrapper :render-object="render.load_at" />

        <!-- __ Action_at  -->
        <AppLabelTSWrapper :render-object="render.action_at" />

        <!-- __ Прогресс общий -->
        <AppProgressBar
            :progress="statistics.time.finished / statistics.time.total * 100"
            :text="`${formatTimeWithLeadingZeros(statistics.time.finished)} / ${formatTimeWithLeadingZeros(statistics.time.total)}`"
            :width="render.progressTotal.width"
            text-size="micro"
        />

        <!--<AppLabelTSWrapper :render-object="render.progressTotal"/>-->

        <!-- __ Комментарий  -->
        <AppLabelTSWrapper :render-object="render.comment" />

    </div>

    <div v-if="!sewingTask.collapsed">
        <div class="ml-[34px] mb-2">

            <!-- __ Заголовок -->
            <ExecuteTaskLineHeader
                :fields-width="sewingLineFieldsWidth"
            />

            <!-- __ Данные -->
            <div v-for="sewingLine of sewingTask.sewing_lines" :key="sewingLine.id">
                <ExecuteTaskLine
                    :fields-width="sewingLineFieldsWidth"
                    :sewing-line="sewingLine"
                    @dblclick="showLineInfo(sewingLine)"
                />
            </div>

            <!-- __ Разделительная линия -->
            <TheDividerLineTS
                m-bottom="mb-0.5"
                m-top="mt-1"
            />

            <!-- __ Итого -->
            <ExecuteTaskTotals
                :amount-and-time="calculateTotals"
            />

        </div>

    </div>


    <!-- __ Модальное окно для информации о записи -->
    <OrderItemInfo
        ref="orderItemInfo"
        :order-line="orderLine"
    />

    <!-- __ Модальное окно для изменения/добавления комментария к СЗ -->
    <!--<CommentEdit-->
    <!--    ref="commentEdit"-->
    <!--    :comment="comment"-->
    <!--    label="Комментарий к Сменному Заданию"-->
    <!--/>-->

</template>

<script lang="ts" setup>
import { computed, reactive, ref } from 'vue'

import type { IRenderData, ISewingTask, ISewingTaskLine, ISewingTaskOrderLine } from '@/types'

import {
    getExecuteTaskStatustics, getSewingTaskAmountAndTime, getTaskStatusById,
} from '@/app/helpers/manufacture/helpers_sewing.ts'
import { formatDateInFullFormat, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import ExecuteTaskLine
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecuteTaskLine.vue'
import ExecuteTaskLineHeader
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecuteTaskLineHeader.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import ExecuteTaskTotals
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecuteTaskTotals.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'
import OrderItemInfo from '@/components/dashboard/manufacture/cells/sewing/sewing_components/common/OrderItemInfo.vue'
// import CommentEdit from '@/components/dashboard/manufacture/cells/sewing/sewing_components/common/CommentEdit.vue'
// import AppLabelMultilineTSWrapper
//     from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'


interface IProps {
    sewingTask: ISewingTask
    fieldsWidth: Record<string, string>
    clientShow?: boolean
    orderInfo?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    clientShow: true,
    orderInfo: true,
})


// __ Тип для модального окна информации о записи в Заявке
const orderLine     = ref<ISewingTaskOrderLine | null>(null)
const orderItemInfo = ref<InstanceType<typeof OrderItemInfo> | null>(null)


// __ Тип для модального окна изменения Комментария
// const comment = ref('')
// const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null)



// __ Объект отображения данных
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const COLLAPSED_WIDTH    = 'w-[30px]'
const DEFAULT_HEIGHT   = 'h-[30px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'mini'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'

const render: IRenderData = reactive({
    collapsed:     {
        id:             () => 'collapsed-search',
        header:         ['', ''],
        width:          props.fieldsWidth.collapsed,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍collapsed...',
        data:           (/*sewingTask: ISewingTask*/) => props.sewingTask.collapsed ? '▲' : '▼',
        class:          'cursor-pointer',
    },
    id:            {
        id:             () => 'id-search',
        header:         ['', ''],
        width:          props.fieldsWidth.id,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍id...',
        data:           (/*sewingTask: ISewingTask*/) => props.sewingTask.id.toString(),
    },
    position:      {
        id:             () => 'position-search',
        header:         ['', ''],
        width:          props.fieldsWidth.position,
        height:         DEFAULT_HEIGHT,
        show:           props.orderInfo,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍№ п/п...',
        data:           (/*sewingTask: ISewingTask*/) => props.sewingTask.position.toString(),
    },
    client:        {
        id:             () => 'client-search',
        header:         ['', ''],
        width:          props.fieldsWidth.client,
        height:         DEFAULT_HEIGHT,
        show:           props.clientShow,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Клиент...',
        data:           (/*sewingTask: ISewingTask*/) => props.sewingTask.order.client.short_name,
    },
    order_no:      {
        id:             () => 'order-no-search',
        header:         ['', ''],
        width:          props.fieldsWidth.order_no,
        height:         DEFAULT_HEIGHT,
        show:           props.orderInfo,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍№ ...',
        data:           (/*sewingTask: ISewingTask*/) => props.sewingTask.order.order_no_num.toString(),
    },
    status:        {
        id:             () => 'status-search',
        header:         ['', ''],
        width:          props.fieldsWidth.status,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   'micro',
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Статус...',
        data:           (/*sewingTask: ISewingTask*/) => {
            const status = getTaskStatusById(props.sewingTask.current_status.id)
            return status?.TITLE ?? ''
        },
    },
    load_at:       {
        id:             () => 'load-at-search',
        header:         ['', ''],
        width:          props.fieldsWidth.load_at,
        height:         DEFAULT_HEIGHT,
        show:           props.orderInfo,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍 Дата загрузки...',
        data:           (/*sewingTask: ISewingTask*/) => formatDateInFullFormat(props.sewingTask.order.load_at, true, false),
    },
    action_at:       {
        id:             () => 'action-at-search',
        header:         ['', ''],
        width:          props.fieldsWidth.load_at,
        height:         DEFAULT_HEIGHT,
        show:           !props.orderInfo,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Прогресс...',
        data:           (/*sewingTask: ISewingTask*/) => formatDateInFullFormat(props.sewingTask.action_at, true, false),
    },
    progressTotal: {
        id:             () => 'progress-total-search',
        header:         ['Процесс выполнения от', 'общего времени СЗ'],
        width:          props.fieldsWidth.progressTotal,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Прогресс...',
        data:           (/*sewingTask: ISewingTask*/) => props.sewingTask.comment ?? '',
    },
    comment:       {
        id:             () => 'comment-search',
        header:         ['', ''],
        width:          props.fieldsWidth.comment,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   'micro',
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Комментарий...',
        data:           (/*sewingTask: ISewingTask*/) => props.sewingTask.comment ?? '',
        class:          'truncate',
    },

})


// __ Ширина полей для вывода СЗ
const sewingLineFieldsWidth = {
    checker:      'w-[30px]',
    id:           'w-[30px]',
    position:     'w-[30px]',
    size:         'w-[70px]',
    cover:        'w-[230px]',
    amount:       'w-[30px]',
    textile:      'w-[80px]',
    composition:  'w-[100px]',
    describe_1:   'w-[90px]',
    describe_2:   'w-[90px]',
    describe_3:   'w-[90px]',
    finished_at:  'w-[80px]',
    machine:      'w-[25px]',
    time:         'w-[50px]',
    tkch:         'w-[50px]',
    kant:         'w-[70px]',
    false_reason: 'w-[110px]',
}

// __ Пересчитываем Итого
const calculateTotals = computed(() => getSewingTaskAmountAndTime(props.sewingTask.sewing_lines))

// __ Цвет от статуса СЗ
const color = computed<string>(() => props.sewingTask.current_status.color)

// __ Объект статистики
const statistics = computed(() => getExecuteTaskStatustics(props.sewingTask))

// __ Показать информацию о записи
const showLineInfo = async (sewingLine: ISewingTaskLine) => {
    orderLine.value = sewingLine.order_line
    await orderItemInfo.value!.show()             // показываем модалку и ждем ответ
}


</script>

<style scoped>

</style>
