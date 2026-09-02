<template>
    <div v-if="!isLoading" class="ml-2 mt-2">
        <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit">
            <div>
                <div class="flex ml-0.5">

                    <!-- __ Collapsed -->
                    <div>
                        <AppLabelMultilineTSWrapper
                            :render-object="render.collapsed"
                            @click="toggleCollapsed"
                        />
                    </div>

                    <!-- __ id -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.id"/>
                        <AppInputTextTSWrapper v-model="idFilter" :render-object="render.id"/>
                    </div>

                    <!-- __ Position -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.position"/>
                    </div>

                    <!-- __ Клиент -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.client"/>
                        <AppInputTextTSWrapper v-model="clientFilter" :render-object="render.client"/>
                    </div>

                    <!-- __ Номер Заявки -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.orderNoStr"/>
                        <AppInputTextTSWrapper v-model="orderNoStrFilter" :render-object="render.orderNoStr"/>
                    </div>

                    <!-- __ Общее количество элементов (изделий) -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.taskAmount"/>
                    </div>

                    <!-- __ Active -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.taskActive"/>

                        <!-- __ Фильтр: Active -->
                        <AppSelectSimpleTS
                            v-if="render.taskActive.show"
                            id="active"
                            :select-data="orderActiveSelect"
                            :text-size="render.taskActive.headerTextSize"
                            :type="
                                orderActiveFilter === 0
                                ? 'primary'
                                : orderActiveFilter === 1
                                    ? 'success'
                                    : 'danger'
                            "
                            :width="render.taskActive.width"
                            align="center"
                            class="mt-[8px]"
                            height="h-[30px]"
                            @change="filterByOrderActive"
                        />
                    </div>

                    <!-- __ Прогнозный -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.isForecast"/>

                        <!-- __ Фильтр: Раскрытая -->
                        <AppSelectSimpleTS
                            v-if="render.isForecast.show"
                            id="is-forecast"
                            :select-data="orderForecastSelect"
                            :text-size="render.isForecast.headerTextSize"
                            :type="
                                orderForecastFilter === 0
                                ? 'primary'
                                : orderForecastFilter === 1
                                    ? 'success'
                                    : 'danger'
                            "
                            :width="render.isForecast.width"
                            align="center"
                            class="mt-[8px]"
                            height="h-[30px]"
                            @change="filterByOrderForecast"
                        />
                    </div>

                    <!-- __ Дата загрузки на складе Вегас -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.loadAt"/>
                        <AppInputTextTSWrapper
                            v-model="loadAtFilter"
                            :render-object="render.loadAt"
                            @input="handleLoadAtDate"
                        />
                    </div>

                    <!-- __ Дата разгрузки на складе клиента -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.unloadAt"/>
                        <AppInputTextTSWrapper
                            v-model="unloadAtFilter"
                            :render-object="render.unloadAt"
                            @input="handleUnloadAtDate"
                        />
                    </div>

                    <!-- __ Описание -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.description"/>
                        <AppInputTextTSWrapper v-model="descriptionFilter" :render-object="render.description"/>
                    </div>


                    <!-- __ Участки -->
                    <div v-for="sector of Object.values(ASSEMBLY_SECTORS)" :key="sector.ID">
                        <AppLabelMultiLineTS
                            :text="sector.LABEL"
                            :type="sector.TYPE"
                            :width="render.sector.width"
                            align="center"
                            rounded="4"
                            text-size="mini"
                        />

                    </div>

                    <!-- __ Выбор дат -->
                    <CellDatesSelectMiniTS
                        :period="renderPeriod"
                        @apply="loadTasks"
                    />

                </div>
            </div>
        </div>

        <!-- __ Даты СЗ -->
        <div v-for="day in renderDays" :key="day.action_at" class="ml-2">
            <div class="flex">
                <!-- __ Collapsed -->
                <AppLabelTS
                    :text="day.collapsed ? '▲' : '▼'"
                    align="center"
                    class="cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    type="warning"
                    width="w-[30px]"
                    @click="day.collapsed = !day.collapsed"
                />

                <!-- __ Дата -->
                <AppLabelTS
                    :text="`${formatDateIntl(day.action_at, true)} (${day.tasks.length})`"
                    :type="getDateType(day)"
                    align="left"
                    class="cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    title="Ctrl + Click - Перейти к Участкам Сборки, Double Click - Меню"
                    width="w-[672px]"
                    @dblclick="showMenu(day)"
                    @click.ctrl="goToManipulateDay(day)"
                />

                <!-- __ Комментарий -->
                <AppLabelTS
                    :text="day.day?.comment ?? ''"
                    :type="day.day?.comment ? 'warning' : getDateType(day)"
                    align="left"
                    class="cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    title="Ctrl + Click - Перейти к Участкам Сборки, Double Click - Изменить комментарий"
                    width="w-[250px]"
                    @dblclick="addDayComment(day)"
                    @click.ctrl="goToManipulateDay(day)"
                />

            </div>

            <!-- __ Сами СЗ -->
            <div v-if="!day.collapsed">


                <!-- __ Сами СЗ с возможностью перетаскивания -->
                <draggable
                    :="dragOptions"
                    :disabled="!isDragging"
                    :list="day.tasks as unknown as IAssemblyTask[]"
                    :move="checkMove"
                    class="min-h-[25px]"
                    item-key="id"
                    tag="div"
                    @end="finishDrag"
                    @start="startDrag"
                >
                    <template #item="{ element, index }">
                        <div
                            @click="() => ({}) /*selectAssemblyTask(element)*/"
                            @dblclick="showAssemblyTaskMenu(element)"
                        >
                            <ManipulateItem
                                :data-type="DATA_TYPE_PROGRESS_AMOUNT"
                                :index="index"
                                :item="element"
                                :render="render"
                            />
                        </div>
                    </template>

                </draggable>

                <!-- __ Разделительная линия -->
                <TheDividerLineTS m-bottom="mb-1" m-top="mt-1"/>

                <!--__ Всего: -->
                <div class="mb-2">
                    <ManipulateTotals
                        :data-type="DATA_TYPE_PROGRESS_AMOUNT"
                        :field-width="SECTOR_WIDTH"
                        :tasks="day.tasks"
                    />
                </div>

            </div>
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

    <!-- __ Модальное окно для изменения/добавления комментария -->
    <CommentEdit
        ref="commentEdit"
        :comment="comment"
        label="Комментарий к производственному дню"
    />

    <!-- __ Модальное Меню -->
    <AppModalMenuTS
        ref="appModalMenuTS"
        :menu="modalMenu"
        :type="modalMenuType"
    />

    <!-- __ Карточка СЗ -->
    <ManageTaskCard
        ref="manageTaskCard"
        :mode="modalMode"
        :task="taskCard"
        :text="modalText"
        :type="modalType"
    />

    <!-- __ Смена Производственной линии -->
    <ManageTaskManufLines
        ref="manageTaskManufLines"
        :mode="modalMode"
        :task="taskCard"
        :text="modalText"
        :type="modalType"
    />

</template>

<script lang="ts" setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import draggable from 'vuedraggable'

import type {
    DraggableHTMLElement,
    IAssemblyDay,
    IAssemblyLineSetData,
    IAssemblyManipulateDay,
    IAssemblyTask,
    IAssemblyTaskLine,
    IColorTypes,
    IDataInputObj,
    IModalAsyncMenu,
    IPeriod,
    IRenderData,
    ISelectData,
    ISelectDataItem,
} from '@/types'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'
import { usePlansStore } from '@/stores/PlansStore.ts'

// import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import { ASSEMBLY_SECTORS, ASSEMBLY_TASK_DRAFT, CHANGE_1, CHANGE_2, DATA_TYPE_PROGRESS_AMOUNT } from '@/app/constants/assembly.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import { formatDateIntl, formatToYMD, getDaysDifferenceFromDates, isHoliday, isToday, splitDate, validateInputDateHelper } from '@/app/helpers/helpers_date.js'
import {
    getAssemblyDayDiff,
    getAssemblyManipulationRenderTasks,
    getAssemblyTasksGroupedByOrder,
    getAssemblyTasksSameOrderInDay,
    isTaskStatusCreated,
    isTaskStatusRunning,
    repositionAssemblyTaskLines,
    setTaskPositionInRenderDays,
} from '@/app/helpers/manufacture/helpers_assembly.ts'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppLabelMultilineTSWrapper from '@/components/dashboard/manufacture/cells/assembly/components/AppLabelMultilineTSWrapper.vue'
import AppInputTextTSWrapper from '@/components/dashboard/manufacture/cells/assembly/components/AppInputTextTSWrapper.vue'
import CellDatesSelectMiniTS from '@/components/dashboard/manufacture/cells/assembly/components/CellDatesSelectMiniTS.vue'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'
import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'

import ManipulateItem from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate/ManipulateItem.vue'
import ManipulateTotals from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate/ManipulateTotals.vue'
import CommentEdit from '@/components/dashboard/manufacture/cells/assembly/common/CommentEdit.vue'
import AppModalMenuTS, { type IModalResponse } from '@/components/ui/modals/AppModalAsyncMenuTS.vue'
import ManageTaskCard from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskCard.vue'
import ManageTaskManufLines from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskManufLines.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'

const router = useRouter()                 // Определяем роутер

const isLoading = ref(false)

const assemblyStore = useAssemblyStore()
const planStore     = usePlansStore()

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

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

// __ Тип для модального окна изменения Комментария
const comment     = ref('')
const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null)

// __ Тип для модального Меню
const modalMenuType  = ref<IColorTypes>('primary')
const modalMenu      = ref<IModalAsyncMenu>({ data: [] })
const appModalMenuTS = ref<InstanceType<typeof AppModalMenuTS> | null>(null)

// __ Тип для Карточки и Изменения Производственной Линии
// __ Карточка СЗ
const taskCard             = ref<IAssemblyTask>(ASSEMBLY_TASK_DRAFT)
const modalType            = ref<IColorTypes>('primary')
const modalText            = ref<string>('')
const modalMode            = ref<'inform' | 'confirm'>('inform')
const manageTaskCard       = ref<InstanceType<typeof ManageTaskCard> | null>(null)
const manageTaskManufLines = ref<InstanceType<typeof ManageTaskManufLines> | null>(null)


const { planPeriodGlobal }                             = storeToRefs(planStore)
const { globalAssemblyTasks, globalAssemblyTasksCopy } = storeToRefs(assemblyStore)

// __ Глобальный Collapse
const collapseAll = ref(true)

// __ Определяем переменные
// let planPeriod: IPeriod = PERIOD_DRAFT // __ Период плана загрузок
const renderDays = ref<IAssemblyManipulateDay[]>([])
const days       = ref<IAssemblyDay[]>([])

const renderPeriod = ref<IPeriod | null>(null)
// const ordersRender = ref<IRenderOrder[]>([])


// __ Возможность редактирования
// TODO: Реализовать через систему ролей
// const canEdit = ref(false)

// __ Объект отображения данных
// const DEFAULT_WIDTH = 'w-[100px]'
const DEFAULT_WIDTH_BOOL = 'w-[70px]'
const DEFAULT_WIDTH_DATE = 'w-[100px]'
const DEFAULT_HEIGHT     = 'h-[30px]'
const HEADER_TYPE        = 'primary'
const DATA_TYPE          = 'primary'
const DEFAULT_TYPE       = 'dark'
const HEADER_TEXT_SIZE   = 'mini'
const DATA_TEXT_SIZE     = 'mini'
const HEADER_ALIGN       = 'center'
const DATA_ALIGN         = 'left'
const DATA_ALIGN_DEFAULT = 'center'
const SECTOR_WIDTH       = 'w-[80px]'

// __ Ширина колонок
const columnsWidth = {
    change : 'w-[30px]',
    client : 'w-[164px]',
    orderNo: 'w-[45px]',
    amount : 'w-[40px]',
    common : 'w-[139px]',
    line   : 'w-[40px]',
}


const render: IRenderData = reactive({
    collapsed  : {
        header        : ['▲', '▼'],
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : (day: IAssemblyManipulateDay) => day.collapsed ? '▲' : '▼',
        click         : (day: IAssemblyManipulateDay) => day.collapsed = !day.collapsed,
        class         : 'cursor-pointer',
        title         : 'Свернуть/Развернуть все'
    },
    plug       : {
        header        : ['', ''],
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'light',
        dataType      : () => 'light',
        type          : () => 'light',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : () => '',
    },
    id         : {
        id            : () => 'id-search',
        header        : ['ID', ''],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (task: IAssemblyTask) => task.id.toString(),
    },
    position   : {
        id            : () => 'position-search',
        header        : ['#', ''],
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => 'primary',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍#...',
        data          : (task: IAssemblyTask) => task.position.toString(),
    },
    client     : {
        id            : () => 'client-search',
        header        : ['Клиент', ''],
        width         : columnsWidth.client,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Клиент...',
        data          : (task: IAssemblyTask) => task.order.client.short_name,
        color         : (task: IAssemblyTask) => task.order.order_type.color,
        title         : (task: IAssemblyTask) => task.order.order_type.display_name,
    },
    orderNoStr : {
        id            : () => 'order-no-search',
        header        : ['№', 'Заявки'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍№...',
        data          : (task: IAssemblyTask) => task.order.order_no_str,
        color         : (task: IAssemblyTask) => task.order.order_type.color,
        title         : (task: IAssemblyTask) => task.order.order_type.display_name,
    },
    taskAmount : {
        id            : () => 'task-amount-search',
        header        : ['Общее', 'кол-во'],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Кол-во...',
        color         : (task: IAssemblyTask) => task.order.order_type.color,
        title         : (task: IAssemblyTask) => task.order.order_type.display_name,
        data          : (task: IAssemblyTask) => task.assembly_lines.reduce((acc: number, line: IAssemblyTaskLine) => acc + line.amount, 0).toString(),
    },
    taskActive : {
        id            : () => 'order-active',
        header        : ['Актуаль-', 'ная'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (task: IAssemblyTask) => task.active ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN_DEFAULT,
        data          : (task: IAssemblyTask) => task.active ? '✓' : '✗',
    },
    isForecast : {
        id            : () => 'is-forecast',
        header        : ['Раскры-', 'тая'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (task: IAssemblyTask) => !task.order.is_forecast ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN_DEFAULT,
        data          : (task: IAssemblyTask) => !task.order.is_forecast ? '✓' : '✗',
    },
    loadAt     : {
        id            : () => 'load-at-search',
        header        : ['Дата', 'загрузки'],
        width         : DEFAULT_WIDTH_DATE,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍дд.мм.гггг...',
        data          : (task: IAssemblyTask) => formatDateIntl(task.order.load_at),
    },
    unloadAt   : {
        id            : () => 'unload-at-search',
        header        : ['Дата', 'разгрузки'],
        width         : DEFAULT_WIDTH_DATE,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍дд.мм.гггг...',
        data          : (task: IAssemblyTask) => formatDateIntl(task.order.unload_at),
    },
    comment_1c : {
        id            : () => 'comment-1c-search',
        header        : ['Комментарий из 1С', ''],
        width         : 'w-[250px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Комментарий из 1С...',
        data          : (task: IAssemblyTask) => task.order.comment_1c ?? '',
    },
    description: {  // __ Описание Заявки
        id            : () => 'description-search',
        header        : ['Комментарий к', 'сменному заданию / дню'],
        width         : 'w-[250px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (task: IAssemblyTask) => task?.comment ? 'warning' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Комментарий...',
        data          : (task: IAssemblyTask) => task.comment ?? '',
    },
    sector     : {
        id            : () => 'sector-search',
        header        : ['Участки', ''],
        width         : SECTOR_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Участки...',
        data          : () => '',
    },
    order_print: {
        id            : () => 'order-print-search',
        header        : ['Печать', ''],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'dark',
        dataType      : () => DATA_TYPE,
        type          : () => 'info',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : 'large',
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Распечатать заявку...',
        class         : 'cursor-pointer',
        data          : (/*order: IRenderOrder*/) => '📄',
    },

})


// __ Фильтры
const idFilter            = ref('')
const clientFilter        = ref('')
const orderNoStrFilter    = ref('')
const descriptionFilter   = ref('')
const loadAtFilter        = ref('')
const unloadAtFilter      = ref('')
const orderActiveFilter   = ref(0)
const orderForecastFilter = ref(0)


const handleLoadAtDateObj: IDataInputObj = {   // Объект для манипуляции с вводом и выводом даты
    newValue: '',
    oldValue: '',
}

const handleLoadAtDate = (event: Event) => {
    const target                 = event.target as HTMLInputElement
    handleLoadAtDateObj.newValue = target.value

    const copy = { ...handleLoadAtDateObj }

    validateInputDateHelper(handleLoadAtDateObj)  // вся логика изменения объекта будет внутри функции
    loadAtFilter.value = handleLoadAtDateObj.newValue

    console.log('before: ', copy)
    console.log('after: ', handleLoadAtDateObj)
}

const handleUnloadAtDateObj: IDataInputObj = {   // Объект для манипуляции с вводом и выводом даты
    newValue: '',
    oldValue: '',
}

const handleUnloadAtDate = (event: Event) => {
    const target                   = event.target as HTMLInputElement
    handleUnloadAtDateObj.newValue = target.value
    validateInputDateHelper(handleUnloadAtDateObj)  // вся логика изменения объекта будет внутри функции
    unloadAtFilter.value = handleUnloadAtDateObj.newValue
}

// __ Подготавливаем селекты
const orderActiveSelect: ISelectData   = {
    name: 'order-active',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: '✓', selected: false, disabled: false },
        { id: 2, name: '✗', selected: false, disabled: false },
    ],
}
const orderForecastSelect: ISelectData = {
    name: 'order-forecast',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: '✓', selected: false, disabled: false },
        { id: 2, name: '✗', selected: false, disabled: false },
    ],
}


// __ Обрабатываем селекты
const filterByOrderActive   = (value: ISelectDataItem) => {
    orderActiveFilter.value = value.id
}
const filterByOrderForecast = (value: ISelectDataItem) => {
    orderForecastFilter.value = value.id
}

// __ Collapse/Expand all
const toggleCollapsed = () => {
    collapseAll.value = !collapseAll.value
    renderDays.value.forEach(day => day.collapsed = collapseAll.value)
}


// __ Переход к Участкам Сборки
const goToManipulateDay = (day: IAssemblyManipulateDay) => {
    router.push({
        name  : 'manufacture.cell.assembly.manipulate.day',
        params: { date: day.action_at },
    })
}

// __ Печать заявки
// const printOrder = async (task: IAssemblyTask) => {
//     // __ Получаем объект с путем и параметрами
//     const routeData = router.resolve({
//         name  : 'orders.print',
//         params: { id: task.id }
//         // query: { orderId: id }
//     })
//
//     // __ Открываем новое окно через стандартный JS
//     window.open(routeData.href, '_blank')
// }


// __ Сохранение комментария
const addDayComment = async (manipulateDay: IAssemblyManipulateDay) => {
    console.log('manipulateDay: ', manipulateDay)

    if (!manipulateDay.day) {
        if (manipulateDay.tasks.length === 0) {
            await showError(['Нет СЗ для сохранения комментария!'])
            return
        }

        // __ Получаем день
        manipulateDay.day = await assemblyStore.getAssemblyDayByDateAndChange(manipulateDay.action_at)
    }

    if (!manipulateDay.day) {
        throw new Error('Отсутствует целевой день. TheAssemblyManipulate.vue --> addDayComment')
    }

    comment.value = manipulateDay.day.comment ?? '' // __ Устанавливаем комментарий
    const answer  = await commentEdit.value!.show()
    if (answer) {
        const newComment = commentEdit.value!.comment.trim()
        const result     = await assemblyStore.setAssemblyDayComment(manipulateDay.day.id, newComment)
        if (checkCRUD(result)) {
            manipulateDay.day.comment = newComment
        } else {
            await showError()
        }
    }
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---    Табы для группировки отображения           !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип раскраски для даты группировки
const getDateType = (day: IAssemblyManipulateDay): IColorTypes => {
    if (isToday(new Date(day.action_at))) return 'success'
    if (isHoliday(new Date(day.action_at))) return 'danger'
    if (day.tasks.length === 0) return 'dark'
    return 'primary'
}


// __ Получаем период плана загрузок с сервера
const getDefaultPeriod = async (period: IPeriod | null) => {
    if (period) {
        planPeriodGlobal.value = renderPeriod.value
        return
    }
    renderPeriod.value     = await planStore.getPlanLoadsDefaultPeriod()
    planPeriodGlobal.value = renderPeriod.value
}


// __ Получаем Дни
const getDays = async () => {
    const actionAtSet = new Set<string>()
    globalAssemblyTasks.value.forEach(task => actionAtSet.add(task.action_at.split(' ')[0]))
    days.value = await assemblyStore.getAssemblyDaysByDates([...actionAtSet])
}


// __ Добавляем Дни к Массиву отображения
const marryDays = () => {
    const daysMap = new Map<string, IAssemblyDay>()
    days.value.forEach(day => daysMap.set(day.action_at, day))
    renderDays.value.forEach(renderDay => {
        const day = daysMap.get(renderDay.action_at)
        if (day) {
            renderDay.day = day
            renderDay.tasks.sort((a, b) => a.position - b.position)
        }
    })
}

// __ Подготавливаем массив отображения
const getRenderTasks = () => {
    renderDays.value = getAssemblyManipulationRenderTasks(
        globalAssemblyTasks.value,
        planPeriodGlobal.value!,
        {
            idFilter           : idFilter.value,
            clientFilter       : clientFilter.value,
            orderNoStrFilter   : orderNoStrFilter.value,
            descriptionFilter  : descriptionFilter.value,
            loadAtFilter       : loadAtFilter.value,
            unloadAtFilter     : unloadAtFilter.value,
            orderActiveFilter  : orderActiveFilter.value.toString(),
            orderForecastFilter: orderForecastFilter.value.toString(),
        },
    )
    marryDays()
}


// __ Тут следим за состоянием глобальных данных с сервера и обновляем локальные данные
watch(
    [
        () => globalAssemblyTasks.value,
        () => planPeriodGlobal.value,
        () => days.value,
    ],
    (/*[tasks, period]*/) => {
        if (!globalAssemblyTasks.value.length) {
            return
        }

        if (!planPeriodGlobal.value || planPeriodGlobal.value.start === '' || planPeriodGlobal.value.end === '') {
            return
        }

        if (days.value.length === 0) {
            return
        }

        console.log('globalAssemblyTasks.value!!!: ', globalAssemblyTasks.value)
        console.log('planPeriodGlobal.value!!!: ', planPeriodGlobal.value)

        getRenderTasks()
        console.log('renderDays.value: ', renderDays.value)

    },
    { immediate: true, deep: true }
)

// __ Реализация фильтров
watch([
    () => idFilter.value,
    () => clientFilter.value,
    () => orderNoStrFilter.value,
    () => descriptionFilter.value,
    () => loadAtFilter.value,
    () => unloadAtFilter.value,
    () => orderActiveFilter.value,
    () => orderForecastFilter.value,
], () => {
    console.log('filtered!!!')
    getRenderTasks()
})

// __ Загрузка Заявок
const loadTasks = async (period: IPeriod | null = null) => {
    renderPeriod.value = period

    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            // !!! Порядок важен

            // const answer = await assemblyStore.getAssemblyTasks([])
            // const answer = await assemblyStore.getAssemblyTasks(['coconut', 'latex'])

            // __ Получаем AssemblyTasks без всяких участков и записываем в глобальную переменную в AssemblyStore
            await Promise.all([
                assemblyStore.getAssemblyTasks([], renderPeriod.value),
                getDefaultPeriod(renderPeriod.value), // __ Получаем период плана загрузок
            ])


            // const daysData = assemblyStore.getAssemblyDaysByPeriod(renderPeriod.value),

            // const [daysData,] = await Promise.all([
            //     assemblyStore.getAssemblyDaysByPeriod(renderPeriod.value),
            //     assemblyStore.getAssemblyTasks([], renderPeriod.value),
            //     getDefaultPeriod(renderPeriod.value), // __ Получаем период плана загрузок
            // ])

            // days.value = daysData

            await getDays()
            console.log('days.value: ', days.value)
            // console.log('Assembly Tasks: ', globalAssemblyTasks.value)
            console.log('planPeriodGlobal.value: ', planPeriodGlobal.value)


            // __ Дальше все через watcher
            // if (DEBUG) console.log('renderMatrix:', renderMatrix.value)


        },
        undefined,
        // false,
    )

    isLoading.value = false
}


onMounted(async () => {
    await loadTasks()
})


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                 Меню Дня                      !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
const showMenu = async (day: IAssemblyManipulateDay) => {
    console.log('props.day: ', day)

    // const clearDay = clearRenderMatrixDay(props.day) as unknown as IAssemblyTask[][]  // __ Возвращаем новый массив без пустых элементов
    // const idx      = getIndexByChange(change)

    // __ Проверяем, есть ли СЗ в дне
    if (day.tasks.length === 0) {
        return
    }

    // __ Показываем модальное меню и обрабатываем результаты
    modalMenuType.value = 'success'
    modalMenu.value     = {
        data: [
            // { id: 1, title: 'Отправить на выполнение' },
            // { id: 2, title: 'Вернуть для редактирования' },
            { id: 3, title: 'Объединить СЗ для одной Заявки' },
            { id: 4, title: 'Добавить/изменить комментарий ко всем СЗ' },
            { id: 5, title: 'Перейти к Участкам Сборки' },
            { id: 6, title: 'Отмена' },
        ],
    }

    // let result = {menuItem: null, value: false} as IModalResponse

    // __ Показываем модальное меню
    const result = await appModalMenuTS.value!.show()

    // __ Отмена + terminate
    if (!result.value || result.menuItem === 6) {
        return
    }

    // __ Отправка на выполнение
    if (result.menuItem === 1) {
        return
    }

    // __ Возврат для редактирования
    if (result.menuItem === 2) {
        return
    }

    // __ Объединение СЗ для одной Заявки
    if (result.menuItem === 3) {

        // __ Получаем массив массивов СЗ по одинаковым Заявкам
        const grouped = getAssemblyTasksGroupedByOrder(day.tasks)
        // console.log('target: ', grouped)
        await assemblyStore.applyMergeTasksGroups(grouped)
        return
    }

    // __ Сохранение комментария
    if (result.menuItem === 4) {
        await addDayComment(day)
        return
    }

    // __ Перейти к Участкам Сборки
    if (result.menuItem === 5) {
        goToManipulateDay(day)
        return
    }

    throw new Error('Unknown menu item!')
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---       Логика Меню Сменного Задания            !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Показываем Меню на клике по самому СЗ
const showAssemblyTaskCard = async (assemblyTask: IAssemblyTask) => {
    taskCard.value = JSON.parse(JSON.stringify(assemblyTask)) // __ Копируем объект, чтобы не мутировал оригинал

    // __ Показываем модальное окно обработки СЗ
    const answer = await manageTaskCard.value!.show()
    if (!answer) {
        return
    }

    // __ Получаем ссылки на панели
    const leftPanel  = manageTaskCard.value!.leftPanel
    const rightPanel = manageTaskCard.value!.rightPanel

    // __ Если есть правая панель, то это создание нового СЗ
    if (rightPanel.length > 0) {
        // __ Создаем новое СЗ на основе копии
        const newAssemblyTask = JSON.parse(JSON.stringify(assemblyTask))

        // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
        newAssemblyTask.position += 0.1

        // __ Устанавливаем id
        // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
        newAssemblyTask.id = 0

        // __ Пересчитываем позиции для строк СЗ (AssemblyLines[])
        // leftPanel  = repositionAssemblyTaskLines(leftPanel)
        // rightPanel = repositionAssemblyTaskLines(rightPanel)

        // __ Обновляем глобальный state СЗ
        // assemblyTask.assembly_lines    = leftPanel              // __ Тут передача по ссылке, автоматическое изменение
        // newAssemblyTask.assembly_lines = rightPanel

        // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
        await assemblyStore.addAssemblyTaskToGlobal(assemblyTask, leftPanel, newAssemblyTask, rightPanel) // __ Тут реактивное перерисовывание

        // console.log(taskCard.value)
    } else {
        // __ Тут ситуация, когда изменился только левая панель (разделение количества и(или) порядка)

        // __ Пересчитываем позиции для строк СЗ (AssemblyLines[])
        // leftPanel = repositionAssemblyTaskLines(leftPanel)

        // __ Обновляем глобальный state СЗ
        await assemblyStore.addAssemblyTaskToGlobal(assemblyTask, leftPanel) // __ Тут реактивное перерисовывание
    }
}

// __ Изменение Производственной Линии
const showAssemblyTaskManufLines = async (assemblyTask: IAssemblyTask) => {
    // __ Копируем объект, чтобы не мутировал оригинал
    taskCard.value = JSON.parse(JSON.stringify(assemblyTask))
    // __ Добавляем метаданные Заявки в каждую строку
    taskCard.value.assembly_lines.forEach(line => line.order_meta = `${taskCard.value.order.client.short_name} №${taskCard.value.order.order_no_str}`)


    // __ Показываем модальное окно обработки СЗ
    const answer = await manageTaskManufLines.value!.show()
    if (!answer) {
        return
    }

    // __ Получаем ссылки на панели
    const mutations                               = manageTaskManufLines.value!.mutations
    const setAssemblyData: IAssemblyLineSetData[] = mutations.map(line => ({ id: line.id, line: line.assembly_line, }))

    console.log('mutations: ', setAssemblyData)

    const result = await assemblyStore.taskLinesAssemblyLineSet(setAssemblyData)
    if (checkCRUD(result)) {
        // __ Меняем глобальный стейт
        assemblyStore.setGlobalArrayChangeAssemblyLines(setAssemblyData)
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'
        modalInfoText.value = 'Данные успешно обновлены'
        await appModalAsyncMultilineTS.value!.show()

    } else {
        await showError()
    }


    // // __ Если есть правая панель, то это создание нового СЗ
    // if (rightPanel.length > 0) {
    //     // __ Создаем новое СЗ на основе копии
    //     const newAssemblyTask = JSON.parse(JSON.stringify(assemblyTask))
    //
    //     // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
    //     newAssemblyTask.position += 0.1
    //
    //     // __ Устанавливаем id
    //     // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
    //     newAssemblyTask.id = 0
    //
    //     // __ Пересчитываем позиции для строк СЗ (AssemblyLines[])
    //     // leftPanel  = repositionAssemblyTaskLines(leftPanel)
    //     // rightPanel = repositionAssemblyTaskLines(rightPanel)
    //
    //     // __ Обновляем глобальный state СЗ
    //     // assemblyTask.assembly_lines    = leftPanel              // __ Тут передача по ссылке, автоматическое изменение
    //     // newAssemblyTask.assembly_lines = rightPanel
    //
    //     // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
    //     await assemblyStore.addAssemblyTaskToGlobal(assemblyTask, leftPanel, newAssemblyTask, rightPanel) // __ Тут реактивное перерисовывание
    //
    //     // console.log(taskCard.value)
    // } else {
    //     // __ Тут ситуация, когда изменился только левая панель (разделение количества и(или) порядка)
    //
    //     // __ Пересчитываем позиции для строк СЗ (AssemblyLines[])
    //     // leftPanel = repositionAssemblyTaskLines(leftPanel)
    //
    //     // __ Обновляем глобальный state СЗ
    //     await assemblyStore.addAssemblyTaskToGlobal(assemblyTask, leftPanel) // __ Тут реактивное перерисовывание
    // }
}


// __ Добавить комментарий к СЗ
const addTaskComment = async (task: IAssemblyTask) => {

    comment.value = task.comment ?? '' // __ Устанавливаем комментарий

    const answer = await commentEdit.value!.show()
    if (answer) {

        const newComment = commentEdit.value!.comment.trim()

        const result = await assemblyStore.setAssemblyTaskComment(task.id, newComment)

        if (!checkCRUD(result)) {

            modalInfoText.value = [
                'Упс! Что-то пошло не так.',
                'Попробуйте повторить операцию позже.',
            ]
            modalInfoType.value = 'danger'
            modalInfoMode.value = 'inform'
            await appModalAsyncMultilineTS.value!.show()

            return
        }

        // __ Обновляем комментарий в глобальном массиве
        assemblyStore.applyAssemblyTaskComment(task.id, newComment)

        // __ Обновляем комментарий в СЗ
        task.comment = newComment
    }
}

// __ Меняем смену
const modifyChange = async (task: IAssemblyTask) => {
    console.log('modifyChange: ', task)

    //
    // // __ Проверяем статус СЗ, если не Создано, то выходим
    // if (!isTaskStatusCreated(task)) {
    //     await showError([
    //         'Изменить смену можно только у СЗ',
    //         'со статусом "Создано" или ',
    //         'со статусом "Создано при закрытии СЗ"!',
    //     ])
    //
    //     return
    // }
    //
    // const targetChange = task.change === CHANGES.CHANGE_1.NAME ? CHANGES.CHANGE_2 : CHANGES.CHANGE_1
    //
    // // __ Показываем предупреждение
    // modalInfoType.value = 'primary'
    // modalInfoMode.value = 'confirm'
    // modalInfoText.value = [
    //     'Смена СЗ',
    //     getOrderTitle(task),
    //     `будет изменена на ${targetChange.TITLE}.`,
    //     'Продолжить?'
    // ]
    //
    // const answer = await appModalAsyncMultilineTS.value!.show()
    // if (!answer) {
    //     return
    // }
    //
    // // __ Устанавливаем реактивно смену
    // task.change = targetChange.NAME
    //
    // // __ Выясняем, что перетаскивали и куда перемещали и что устанавливали (смена)
    // let renderMatrixCloned = JSON.parse(JSON.stringify(renderMatrix.value))
    // renderMatrixCloned     = clearRenderMatrix(renderMatrixCloned)
    // renderMatrixCloned     = setTaskPositionInRenderMatrix(renderMatrixCloned)
    //
    // // console.log('renderMatrixCleared: ', renderMatrixCloned)
    // // console.log('renderMatrixCopy: ', renderMatrixCopy.value)
    //
    // debugger
    //
    // // // __ Получаем разницу между матрицами
    // const diffs = getDiffsWithPositions(renderMatrixCloned, renderMatrixCopy.value)
    // console.log('diffs: ', diffs)
    //
    // // __ Если нет изменений - выходим, чтобы не было лишних телодвижений
    // if (!diffs.length) {
    //     // __ Откатываем изменения
    //     renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
    //     return
    // }
    //
    // // __ Перемещаем СЗ без вывода дополнительной информации
    // await assemblyStore.applyChanges(diffs) // __ Применяем изменения

    // const answer = await appModalAsyncMultiline.value!.show()
    // if (answer) {
    //
    //     const result = await assemblyStore.modifyChange(task.id, targetChange.NAME) // __ Применяем изменения
    //     if (!checkCRUD(result)) {
    //         await showError()
    //         return
    //     }
    //
    //     // __ Устанавливаем реактивно смену
    //     task.change = targetChange.NAME
    //     return
    // }
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---          Меню Сменного Задания                !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Меню при двойном клике на Заявке (Разделить количество + Изменить стол)
const showAssemblyTaskMenu = async (assemblyTask: IAssemblyTask) => {
    // __ Показываем модальное меню при двойном клике на Заявке обрабатываем результаты
    modalMenuType.value = 'indigo'

    const CANCEL_ID = 6
    modalMenu.value = {
        data: [
            { id: 1, title: 'Разделить количество' },
            // { id: 2, title: 'Изменить смену СЗ' },
            { id: 3, title: 'Изменить Линию Сборки' },
            { id: 4, title: 'Добавить / Изменить комментарий к СЗ' },
            { id: 5, title: 'Перейти в Карточку Заявки' },
            { id: CANCEL_ID, title: 'Отмена' },
        ],
    }

    const result = await appModalMenuTS.value!.show()
    // let result = { menuItem: 3, value: true } as IModalResponse

    // __ Отмена
    if (result.menuItem === CANCEL_ID && result.value) {
        return
    }

    // __ Разделить количество
    if (result.menuItem === 1 && result.value) {
        await showAssemblyTaskCard(assemblyTask)
        return
    }

    // __ Изменить Смену
    if (result.menuItem === 2 && result.value) {
        await modifyChange(assemblyTask)
        return
    }

    // __ Изменить Линию Сборки
    if (result.menuItem === 3 && result.value) {
        await showAssemblyTaskManufLines(assemblyTask)
        return
    }

    // __ Добавить комментарий к СЗ
    if (result.menuItem === 4 && result.value) {
        await addTaskComment(assemblyTask)
        return
    }

    // __ Перейти в карточку Заявки
    if (result.menuItem === 5 && result.value) {
        router.push({ name: 'orders.card', params: { id: assemblyTask.order.id } })
        return
    }

}


// --- -----------------------------------------------------
// --- ------------- Управление Druggable ------------------
// --- -----------------------------------------------------
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---           Управление Druggable                !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Опции для draggable
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const dragOptions = computed(() => {
    return {
        animation  : 300,
        group      : 'orders',
        ghostClass : 'ghost',
        dragClass  : 'drag',
        chosenClass: 'chosen',
        // sort: true,
        // disabled: false, // Выносим в отдельное свойство
    }
})

const isDragging = ref(true)

const checkMove = (evt: DraggableHTMLElement) => {
    // return true
    // console.log('checkMove: ', evt)
    const movedElement = evt.draggedContext.element as IAssemblyTask
    // console.log(movedElement)
    // return true
    // __ Проверяем, что перемещаемый элемент со статусом 'Создано' или 'Выполняется' но внутри одного дня
    if (!isTaskStatusCreated(movedElement) && !isTaskStatusRunning(movedElement)) {
        return false
    }

    // __ Проверяем, что перемещаемый элемент не в прошлом
    const nowDate  = formatToYMD(new Date())
    const dateDiff = getDaysDifferenceFromDates(movedElement.action_at, nowDate)

    // console.log('movedElement.action_at: ', movedElement.action_at)
    // console.log('nowDate: ', nowDate)
    // console.log('dateDiff: ', dateDiff)

    if (dateDiff < 0) {
        // await showError(['Ошибка!', 'Прошлое не ворошим!'])
        // renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
        return false
    }

    return true
}


// __ Начало перетаскивания СЗ
const startDrag = (/*evt: any*/) => {
    // const element = evt.item._underlying_vm_
    // console.log('startDrag: ', evt.oldIndex)
    // console.log('element: ', element)
}

// __ Окончание перетаскивания СЗ
const finishDrag = async (evt: DraggableHTMLElement) => {
    // // const element = evt.item._underlying_vm_
    // // console.log('evt: ', evt)
    //
    //
    // __ Выясняем, что перетаскивали и куда перемещали
    const renderDaysCloned = JSON.parse(JSON.stringify(renderDays.value))
    renderDays.value       = setTaskPositionInRenderDays(renderDays.value)

    // console.log('renderDaysCloned: ', renderDaysCloned)

    // __ Получаем разницу между матрицами
    // __ Именно renderDaysCloned, renderDays.value, потому
    const { diffs, dayFrom, dayTo } = getAssemblyDayDiff(renderDays.value, renderDaysCloned)
    console.log('matrix diffs: ', diffs, dayFrom, dayTo)

    // __ Если нет изменений - выходим, чтобы не было лишних телодвижений
    if (!diffs.length) {
        // __ Откатываем изменения
        assemblyStore.backUpChanges()
        return
    }

    // __ Проверяем, переместились ли СЗ в рамках одного дня или нет
    const isOneDayAction = !(dayFrom && dayTo)

    // __ Проверяем, переместились ли СЗ в рамках смены
    const isChangeModify = false
    // const isChangeModify = diffs.some(diff => diff.isChangeChanged)

    // __ Находим целевую смену (куда перемещаем)
    const targetChange = CHANGE_1
    // const targetChange = diffs.find(diff => diff.isChangeChanged)?.newChange


    console.log('isOneDayAction: ', isOneDayAction)
    // console.log('isChangeModify: ', isChangeModify)

    // __ Получаем сам перемещаемый элемент
    const movedElement = evt.item._underlying_vm_ as IAssemblyTask

    if (isOneDayAction && !isChangeModify) {

        console.log('movedElement: ', movedElement)

        // __ Если перемещаемый элемент со статусом 'Выполняется', проверяем маячок,
        // __ который указывает на готовность к добавлению СЗ
        if (isTaskStatusRunning(movedElement)) {

            // __ Получаем флаг готовности к добавлению новых СЗ
            // const isReady: IAssemblyDay = await assemblyStore.readyGetAssemblyDay(splitDate(movedElement.action_at))

            // if (!isReady) {
            await showError([
                'Ошибка!',
                'Для перемещения СЗ со статусом "Выполняется"',
                'необходимо приостановить выполнение СЗ',
                'для добавления новых СЗ!',
            ])

            // __ Откатываем изменения
            assemblyStore.backUpChanges()
            return
            // }
        }

        // __ Перемещаем СЗ без вывода дополнительной информации
        await assemblyStore.applyChangesManipulate(diffs) // __ Применяем изменения

    } else {

        // __ Проверяем, что перемещаемый элемент не со статусом 'Выполняется'
        // __ потому что здесь уже перемещение между днями, а с этим статусом только в рамках дня
        if (isTaskStatusRunning(movedElement)) {
            await showError([
                'Ошибка!',
                'Нельзя переместить СЗ со статусом "Выполняется"',
                'на другой день!',
            ])

            // __ Откатываем изменения
            assemblyStore.backUpChanges()
            return
        }

        // __ Находим те изменения, которые относятся к перемещаемому СЗ
        const diffsForAssemblyTask = diffs.find(diff => diff.taskChanges?.action_at || diff.taskChanges?.change)
        if (!diffsForAssemblyTask) {
            console.error('Не найдено изменений для перемещения СЗ')

            // __ Откатываем изменения
            assemblyStore.backUpChanges()
            return
        }

        // __ Получаем СЗ, которое перемещаем, здесь не мутируем
        const assemblyTask = movedElement

        // __ Возвращаем дату и позицию из копии, потому что здесь она уже мутирована
        const findTask = globalAssemblyTasksCopy.value.find(task => task.id === assemblyTask.id)
        if (findTask) {
            assemblyTask.action_at = findTask.action_at
            assemblyTask.position = findTask.position
        }

        // __ Получаем дату, на которую нужно переместить СЗ
        const targetDate = dayTo

        // __ Проверяем, на даты СЗ и отгрузки
        let dateDiff = getDaysDifferenceFromDates(assemblyTask.order.load_at ?? targetDate!, targetDate!)

        // console.log('targetDate: ', targetDate)
        // console.log('assemblyTask.order.load_at: ', assemblyTask.order.load_at)
        // console.log('dateDiff: ', dateDiff)

        if (dateDiff < 0) {
            await showError([
                'Ошибка!',
                'Дата СЗ не может быть позднее даты загрузки',
                'на складе!',
                `Дата загрузки на складе: ${formatDateIntl(splitDate(assemblyTask.order.load_at ?? targetDate), true)}`,
            ])

            // __ Откатываем изменения
            assemblyStore.backUpChanges()
            return
        }

        // __ Проверяем, на даты СЗ и текущую дату (чтобы не было в прошлом)
        const nowDate = formatToYMD(new Date())
        dateDiff      = getDaysDifferenceFromDates(targetDate!, nowDate)

        // console.log('targetDate: ', targetDate)
        // console.log('nowDate: ', nowDate)
        // console.log('dateDiff: ', dateDiff)

        if (dateDiff < 0) {
            await showError(['Ошибка!', 'Дата СЗ не может быть в прошлом!'])

            // __ Откатываем изменения
            assemblyStore.backUpChanges()
            return
        }

        // __ Получаем все СЗ в целевом дне с тем же Заказом, что и у перемещаемого СЗ для проверки на объединение
        // __ Проверяем также соответствие статусов. Если одинаковые статусы, то объединяем
        const existingAssemblyTasks = getAssemblyTasksSameOrderInDay(
            assemblyTask,
            globalAssemblyTasksCopy.value,
            targetDate,
            targetChange || assemblyTask.change,
            true
        )

        // __ Формируем текст для модального окна
        const orderInfo = `${assemblyTask.order.client.short_name} №${assemblyTask.order.order_no_str}`

        // __ Находим количество для формирования динамического меню
        const totalAmount = assemblyTask.assembly_lines.reduce((acc, item) => acc + item.amount, 0)

        // __ Показываем модальное меню и обрабатываем результаты
        modalMenuType.value = 'primary'
        modalMenu.value     = {
            data: [
                { id: 1, title: 'Переместить все' },
                { id: 2, title: 'Переместить часть' },
                { id: 3, title: 'Отмена' },
            ],
        }

        let result = { menuItem: 1, value: true } as IModalResponse

        // __ Если количество СЗ больше 1, то показываем меню, иначе сразу перемещаем
        if (totalAmount > 1) {
            // __ Показываем модальное меню
            result = await appModalMenuTS.value!.show()
        }

        // __ 'Отмена'
        if (!result.value || result.menuItem === 3) {

            // __ Откатываем изменения
            assemblyStore.backUpChanges()
            return

        } else if (result.menuItem === 1 || totalAmount === 1) {

            // __ Перемещаем все СЗ
            // !!! Логика для доработки TODO: Тут проверка на даты на возможность перемещения СЗ

            // __ Проверяем, есть ли уже СЗ в целевом дне с тем же Заказом, что и у перемещаемого СЗ
            if (existingAssemblyTasks.length) {
                // __ Тут ситуация, когда в целевом дне есть уже СЗ для той же Заявки
                modalInfoType.value = 'success'
                modalInfoText.value = ['Объединить СЗ для', orderInfo, 'в одно?']
                modalInfoMode.value = 'confirm'

                const result = await appModalAsyncMultilineTS.value!.show()

                if (result) {
                    // __ С объединением
                    // console.warn('Union AssemblyTasks')

                    // !!! Важен порядок параметров в функции. Основное СЗ - Куда перемещаем
                    await assemblyStore.applyMergeTasks([existingAssemblyTasks[0], assemblyTask]) // __ Объединяем СЗ с первой
                    // assemblyStore.applyMergeTasks([assemblyTask, ...existingAssemblyTasks])   // __ Объединяем все остальные
                    return
                }
            }

            // __ Применяем изменения
            await assemblyStore.applyChangesManipulate(diffs)

        } else if (result.menuItem === 2) {
            // __ Перемещаем часть СЗ в другой день
            // !!! Логика для доработки TODO: Тут проверка на даты на возможность перемещения СЗ

            taskCard.value = JSON.parse(JSON.stringify(assemblyTask)) // __ Копируем объект, чтобы не мутировал оригинал

            // __ Показываем модальное окно обработки СЗ
            const answer = await manageTaskCard.value!.show()
            if (!answer) {

                // __ Откатываем изменения
                assemblyStore.backUpChanges()
                return
            }

            // __ Получаем правую и левую панели
            let leftPanel  = manageTaskCard.value!.leftPanel
            let rightPanel = manageTaskCard.value!.rightPanel

            // __ Если есть правая панель, то это создание нового СЗ
            if (rightPanel.length > 0) {
                // __ Создаем новое СЗ на основе копии
                const newAssemblyTask = JSON.parse(JSON.stringify(assemblyTask))

                // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
                // __ Тут такой код. Чтобы правильная была нумерация
                // __ Баг возникает если переносить часть со первой смены на самое начало второй
                // __ или если переносить часть со второй смены на самый конец первой
                //@ts-expect-error Для бобавления в будушем смен
                if (targetChange === CHANGE_2) {
                    newAssemblyTask.position = (diffsForAssemblyTask.taskChanges?.position?.new ?? 1) + 0.1
                } else if (targetChange === CHANGE_1) {
                    newAssemblyTask.position = (diffsForAssemblyTask.taskChanges?.position?.new ?? 1) - 0.1
                }

                // __ Устанавливаем новую дату, высчитываем новую дату по смещению
                newAssemblyTask.action_at = dayTo

                // __ Устанавливаем новую смену, если перемещаем в другую
                newAssemblyTask.change = targetChange

                // __ Устанавливаем id
                // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
                newAssemblyTask.id = 0

                // __ Проверяем, есть ли уже СЗ в целевом дне с тем же Заказом, что и у перемещаемого СЗ
                if (existingAssemblyTasks.length) {
                    // __ Тут ситуация, когда в целевом дне есть уже СЗ для той же Заявки
                    modalInfoType.value = 'success'
                    modalInfoText.value = ['Объединить СЗ для', orderInfo, 'в одно?']
                    modalInfoMode.value = 'confirm'

                    const result = await appModalAsyncMultilineTS.value!.show()

                    if (result) {
                        // __ С объединением
                        console.warn('Union AssemblyTasks')

                        // __ Переносим правую панель в новый СЗ
                        rightPanel                     = repositionAssemblyTaskLines(rightPanel)
                        newAssemblyTask.assembly_lines = rightPanel

                        // __ Изменяем содержимое в СЗ
                        leftPanel = repositionAssemblyTaskLines(leftPanel)
                        assemblyStore.setAssemblyTasksLines(assemblyTask, leftPanel) // __ Делаем это в родителе

                        // !!! Важен порядок параметров в функции. Основное СЗ - Куда перемещаем
                        await assemblyStore.applyMergeTasks([existingAssemblyTasks[0], newAssemblyTask]) // __ Объединяем СЗ с первой
                        // assemblyStore.applyMergeTasks([assemblyTask, ...existingAssemblyTasks])   // __ Объединяем все остальные
                        return
                    }
                }

                // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
                await assemblyStore.addAssemblyTaskToGlobal(assemblyTask, leftPanel, newAssemblyTask, rightPanel) // __ Тут реактивное перерисовывание
            } else {
                // __ Тут ситуация, когда изменился только левая панель (разделение количества и(или) порядка)
                // __ Игнорируем это поведение и просто показываем сообщение об ошибке
                await showError(['Ошибка!', 'Правая часть не может быть пустой!'])

                // __ Откатываем изменения
                assemblyStore.backUpChanges()
                return
            }
        }
    }
}



// assemblyStore.$subscribe((mutation, state) => {
//     console.log('Тип изменения:', mutation.type);
//     console.log('Новое состояние:', state);
// })


// watch(() => assemblyStore.globalAssemblyTasksCopy, (oldData, newData) => {
//     console.log('--------------------------------')
//     console.log('old: ', oldData)
//     console.log('newData: ', newData)
//     console.log('--------------------------------')
// }, { deep: true, immediate: true })

</script>

<style scoped>
/* Стили для draggable */
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}

.chosen {
    background: #e1f5fe;
}

.drag {
    opacity: 0.8;
    cursor: grabbing;
}

/* Убедимся, что draggable контейнер занимает пространство даже когда пустой */
:deep(.sortable-chosen) {
    background-color: rgba(226, 232, 240, 0.5);
}

:deep(.sortable-ghost) {
    opacity: 0.5;
}

</style>

