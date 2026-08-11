<template>

    <div :class="[!assemblyTask.collapsed ? 'mt-1' : '']" class="flex">

        <!-- __ Collapsed -->
        <!--eslint-disable vue/no-mutating-props-->
        <AppLabelTSWrapper
            :render-object="render.collapsed"
            @click="assemblyTask.collapsed = !assemblyTask.collapsed"
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

        <!--&lt;!&ndash; __ Прогресс общий &ndash;&gt;-->
        <!--<AppProgressBar-->
        <!--    :progress="statistics.time.finished / statistics.time.total * 100"-->
        <!--    :text="`${formatTimeWithLeadingZeros(statistics.time.finished, 'hour')} / ${formatTimeWithLeadingZeros(statistics.time.total, 'hour')}`"-->
        <!--    :width="render.progressTotal.width"-->
        <!--    text-size="micro"-->
        <!--/>-->

        <!--<AppLabelTSWrapper :render-object="render.progressTotal"/>-->

        <!-- __ Комментарий  -->
        <AppLabelTSWrapper :render-object="render.comment"/>

    </div>

    <!--<div v-if="true">-->
    <div v-if="!assemblyTask.collapsed">
        <div class="ml-[34px] mb-2">

            <!-- __ Заголовок -->
            <ExecuteTaskLineHeader
                :fields-width="assemblyLineFieldsWidth"
            />

            <!-- __ Данные -->
            <div v-for="assemblyLine of assemblyTask.assembly_lines" :key="assemblyLine.id">
                <ExecuteTaskLine
                    :assembly-line="assemblyLine"
                    :fields-width="assemblyLineFieldsWidth"
                    @change-description="changeDescription"
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
import type { IRenderData, IAssemblyTask, IColorTypes, IAssemblyTaskLineSector, } from '@/types'

import { computed, reactive, ref, } from 'vue'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'

import {
    getAssemblyTaskAmountAndTimeTotal,
    getTaskStatusById,
    // getExecuteTaskStatistics,
    // getAssemblyTaskAmountAndTime,
} from '@/app/helpers/manufacture/helpers_assembly.ts'
import { formatDateInFullFormat, /*formatTimeWithLeadingZeros */} from '@/app/helpers/helpers_date'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'
// import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import ExecuteTaskLine
    from '@/components/dashboard/manufacture/cells/assembly/assembly_execute/ExecuteTaskLine.vue'
import ExecuteTaskLineHeader
    from '@/components/dashboard/manufacture/cells/assembly/assembly_execute/ExecuteTaskLineHeader.vue'
import ExecuteTaskTotals
    from '@/components/dashboard/manufacture/cells/assembly/assembly_execute/ExecuteTaskTotals.vue'


// import OrderItemInfo from '@/components/dashboard/manufacture/cells/assembly/assembly_components/common/OrderItemInfo.vue'
// import CommentEdit from '@/components/dashboard/manufacture/cells/assembly/assembly_components/common/CommentEdit.vue'
// import AppLabelMultilineTSWrapper
//     from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'


interface IProps {
    assemblyTask: IAssemblyTask
    fieldsWidth: Record<string, string>
    clientShow?: boolean
    orderInfo?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    clientShow: true,
    orderInfo : true,
})

const assemblyStore = useAssemblyStore()

// console.log('task: ', props.assemblyTask)

// __ Тип для модального окна информации о записи в Заявке
// const orderLine     = ref<IAssemblyTaskOrderLine | null>(null)
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
        data          : (/*assemblyTask: IAssemblyTask*/) => props.assemblyTask.collapsed ? '▲' : '▼',
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
        data          : (/*assemblyTask: IAssemblyTask*/) => props.assemblyTask.id.toString(),
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
        data          : (/*assemblyTask: IAssemblyTask*/) => props.assemblyTask.position.toString(),
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
        data          : (/*assemblyTask: IAssemblyTask*/) => props.assemblyTask.order.client.short_name,
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
        data          : (/*assemblyTask: IAssemblyTask*/) => props.assemblyTask.order.order_no_num.toString(),
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
        data          : (/*assemblyTask: IAssemblyTask*/) => {
            const status = getTaskStatusById(props.assemblyTask.current_status.id)
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
        data          : (/*assemblyTask: IAssemblyTask*/) => formatDateInFullFormat(props.assemblyTask.order.load_at, true, false),
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
        data          : (/*assemblyTask: IAssemblyTask*/) => formatDateInFullFormat(props.assemblyTask.action_at, true, false),
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
        data          : (/*assemblyTask: IAssemblyTask*/) => props.assemblyTask.comment ?? '',
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
        data          : (/*assemblyTask: IAssemblyTask*/) => props.assemblyTask.comment ?? '',
        class         : 'truncate',
    },

})


// __ Ширина полей для вывода СЗ
const assemblyLineFieldsWidth = {
    checker     : 'w-[30px]',
    id          : 'w-[30px]',
    name        : 'w-[305px]',
    amount      : 'w-[50px]',
    finished_at : 'w-[80px]',
    time        : 'w-[100px]',
    description : 'w-[300px]',
    size        : 'w-[105px]',
    expense     : 'w-[100px]',
    code_1c     : 'w-[100px]',
    false_reason: 'w-[229px]',
}

// __ Пересчитываем Итого
const calculateTotals = computed(() => getAssemblyTaskAmountAndTimeTotal(props.assemblyTask.assembly_lines))

// __ Цвет от статуса СЗ
const color = computed<string>(() => props.assemblyTask?.current_status?.color)

// __ Объект статистики
// const statistics = computed(() => getExecuteTaskStatistics(props.assemblyTask))

// // __ Показать информацию о записи
// const showLineInfo = async (assemblyLine: IAssemblyTaskLine) => {
//     orderLine.value = assemblyLine.order_line
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
const changeDescription = async (sectorLine: IAssemblyTaskLineSector, description: string) => {

    const result = await assemblyStore.setAssemblyTaskLineSectorDescription(sectorLine.id, description)
    if (checkCRUD(result)) {
        sectorLine.description = description
        return
    } else {
        await showError()
        return
    }
}

</script>

<style scoped>

</style>
