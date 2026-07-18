<template>

    <div :class="[!blockTask.collapsed ? 'mt-1' : '']" class="flex">

        <!-- __ Collapsed -->
        <!--eslint-disable vue/no-mutating-props-->
        <AppLabelTSWrapper
            :render-object="render.collapsed"
            @click="blockTask.collapsed = !blockTask.collapsed"
        />

        <!-- __ id -->
        <AppLabelTSWrapper :render-object="render.id"/>

        <!-- __ position -->
        <AppLabelTSWrapper :render-object="render.position"/>

        <!-- __ Клиент -->
        <AppLabelTSWrapper :render-object="render.client"/>

        <!-- __ № Заявки -->
        <AppLabelTSWrapper :render-object="render.order_no"/>

        <!-- __ Статус  -->
        <AppLabelTSWrapper :render-object="render.status"/>

        <!-- __ Дата загрузки  -->
        <AppLabelTSWrapper :render-object="render.load_at"/>

        <!-- __ Action_at  -->
        <AppLabelTSWrapper :render-object="render.action_at"/>

        <!-- __ Прогресс общий -->
        <AppProgressBar
            :progress="statistics.time.finished / statistics.time.total * 100"
            :text="`${formatTimeWithLeadingZeros(statistics.time.finished, 'hour')} / ${formatTimeWithLeadingZeros(statistics.time.total, 'hour')}`"
            :width="render.progressTotal.width"
            text-size="micro"
        />

        <!--<AppLabelTSWrapper :render-object="render.progressTotal"/>-->

        <!-- __ Комментарий  -->
        <AppLabelTSWrapper :render-object="render.comment"/>

    </div>

    <div v-if="!blockTask.collapsed">
        <div class="ml-[34px] mb-2">

            <!-- __ Заголовок -->
            <ExecuteTaskLineHeader
                :fields-width="blockLineFieldsWidth"
            />

            <!-- __ Данные -->
            <div v-for="blockLine of blockTask.block_lines" :key="blockLine.id">
                <ExecuteTaskLine
                    :block-line="blockLine"
                    :fields-width="blockLineFieldsWidth"
                    @change-description="changeDescription(blockLine, $event)"
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

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />

    <!-- __ Модальное окно для информации о записи -->
    <!--<OrderItemInfo-->
    <!--    ref="orderItemInfo"-->
    <!--    :order-line="orderLine"-->
    <!--/>-->

    <!-- __ Модальное окно для изменения/добавления комментария к СЗ -->
    <!--<CommentEdit-->
    <!--    ref="commentEdit"-->
    <!--    :comment="comment"-->
    <!--    label="Комментарий к Сменному Заданию"-->
    <!--/>-->

</template>

<script lang="ts" setup>
import type { IRenderData, IBlockTask, IBlockTaskLine, IColorTypes, } from '@/types'

import { computed, reactive, ref, } from 'vue'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

import {
    getExecuteTaskStatistics, getBlockTaskAmountAndTime, getTaskStatusById,
} from '@/app/helpers/manufacture/helpers_blocks.ts'
import { formatDateInFullFormat, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import ExecuteTaskLine
    from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteTaskLine.vue'
import ExecuteTaskLineHeader
    from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteTaskLineHeader.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import ExecuteTaskTotals
    from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteTaskTotals.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'

// import OrderItemInfo from '@/components/dashboard/manufacture/cells/block/block_components/common/OrderItemInfo.vue'
// import CommentEdit from '@/components/dashboard/manufacture/cells/block/block_components/common/CommentEdit.vue'
// import AppLabelMultilineTSWrapper
//     from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'


interface IProps {
    blockTask: IBlockTask
    fieldsWidth: Record<string, string>
    clientShow?: boolean
    orderInfo?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    clientShow: true,
    orderInfo : true,
})

const blockStore = useBlocksStore()

// console.log('task: ', props.blockTask)

// __ Тип для модального окна информации о записи в Заявке
// const orderLine     = ref<IBlockTaskOrderLine | null>(null)
// const orderItemInfo = ref<InstanceType<typeof OrderItemInfo> | null>(null)


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
    collapsed    : {
        id            : () => 'collapsed-search',
        header        : ['', ''],
        width         : props.fieldsWidth.collapsed,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍collapsed...',
        data          : (/*blockTask: IBlockTask*/) => props.blockTask.collapsed ? '▲' : '▼',
        class         : 'cursor-pointer',
    },
    id           : {
        id            : () => 'id-search',
        header        : ['', ''],
        width         : props.fieldsWidth.id,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        color         : () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (/*blockTask: IBlockTask*/) => props.blockTask.id.toString(),
    },
    position     : {
        id            : () => 'position-search',
        header        : ['', ''],
        width         : props.fieldsWidth.position,
        height        : DEFAULT_HEIGHT,
        show          : props.orderInfo,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        color         : () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍№ п/п...',
        data          : (/*blockTask: IBlockTask*/) => props.blockTask.position.toString(),
    },
    client       : {
        id            : () => 'client-search',
        header        : ['', ''],
        width         : props.fieldsWidth.client,
        height        : DEFAULT_HEIGHT,
        show          : props.clientShow,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        color         : () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Клиент...',
        data          : (/*blockTask: IBlockTask*/) => props.blockTask.order.client.short_name,
    },
    order_no     : {
        id            : () => 'order-no-search',
        header        : ['', ''],
        width         : props.fieldsWidth.order_no,
        height        : DEFAULT_HEIGHT,
        show          : props.orderInfo,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        color         : () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍№ ...',
        data          : (/*blockTask: IBlockTask*/) => props.blockTask.order.order_no_num.toString(),
    },
    status       : {
        id            : () => 'status-search',
        header        : ['', ''],
        width         : props.fieldsWidth.status,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        color         : () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : 'mini',
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Статус...',
        data          : (/*blockTask: IBlockTask*/) => {
            const status = getTaskStatusById(props.blockTask.current_status.id)
            return status?.TITLE ?? ''
        },
    },
    load_at      : {
        id            : () => 'load-at-search',
        header        : ['', ''],
        width         : props.fieldsWidth.load_at,
        height        : DEFAULT_HEIGHT,
        show          : props.orderInfo,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        color         : () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍 Дата загрузки...',
        data          : (/*blockTask: IBlockTask*/) => formatDateInFullFormat(props.blockTask.order.load_at, true, false),
    },
    action_at    : {
        id            : () => 'action-at-search',
        header        : ['', ''],
        width         : props.fieldsWidth.load_at,
        height        : DEFAULT_HEIGHT,
        show          : !props.orderInfo,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        color         : () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Прогресс...',
        data          : (/*blockTask: IBlockTask*/) => formatDateInFullFormat(props.blockTask.action_at, true, false),
    },
    progressTotal: {
        id            : () => 'progress-total-search',
        header        : ['Процесс выполнения от', 'общего времени СЗ'],
        width         : props.fieldsWidth.progressTotal,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Прогресс...',
        data          : (/*blockTask: IBlockTask*/) => props.blockTask.comment ?? '',
    },
    comment      : {
        id            : () => 'comment-search',
        header        : ['', ''],
        width         : props.fieldsWidth.comment,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        color         : () => color.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : 'micro',
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Комментарий...',
        data          : (/*blockTask: IBlockTask*/) => props.blockTask.comment ?? '',
        class         : 'truncate',
    },

})


// __ Ширина полей для вывода СЗ
const blockLineFieldsWidth = {
    checker     : 'w-[30px]',
    id          : 'w-[30px]',
    position    : 'w-[30px]',
    name        : 'w-[305px]',
    amount      : 'w-[30px]',
    finished_at : 'w-[80px]',
    line        : 'w-[25px]',
    time        : 'w-[70px]',
    kdb         : 'w-[68px]',
    false_reason: 'w-[250px]',
    description : 'w-[266px]',
}

// __ Пересчитываем Итого
const calculateTotals = computed(() => getBlockTaskAmountAndTime(props.blockTask.block_lines))

// __ Цвет от статуса СЗ
const color = computed<string>(() => props.blockTask?.current_status?.color)

// __ Объект статистики
const statistics = computed(() => getExecuteTaskStatistics(props.blockTask))

// // __ Показать информацию о записи
// const showLineInfo = async (blockLine: IBlockTaskLine) => {
//     orderLine.value = blockLine.order_line
//     await orderItemInfo.value!.show()             // показываем модалку и ждем ответ
// }


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Показываем сообщение об ошибке
async function showError(error: string | string[] | null = null) {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'

    let renderError = ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    if (typeof error === 'string' && error.length > 0) {
        renderError = [error]
    } else if (Array.isArray(error) && error.length > 0) {
        renderError = error
    }

    modalInfoText.value = renderError
    await appModalAsyncMultilineTS.value!.show()
}


// __ Меняем Комментарий
const changeDescription = async (blockLine: IBlockTaskLine, description: string) => {

    const result = await blockStore.setBlockTaskLineDescription(blockLine.id, description)
    if (checkCRUD(result)) {
        blockLine.description = description
        return
    } else {
        await showError()
        return
    }
}

</script>

<style scoped>

</style>
