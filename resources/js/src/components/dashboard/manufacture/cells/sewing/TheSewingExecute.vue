<template>
    <div
        v-if="!isLoading"
        class="ml-2 mt-2"
    >
        <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit z-50">
            <div>
                <div class="flex ml-0.5">
                    <!-- __ Collapsed -->
                    <div>
                        <AppLabelTSWrapper
                            :render-object="render.collapsedUp"
                            @click="collapseAll"
                        />
                        <AppLabelTSWrapper
                            :render-object="render.collapsedDown"
                            @click="expandAll"
                        />
                    </div>

                    <!-- __ id -->
                    <AppLabelMultilineTSWrapper :render-object="render.id"/>

                    <!-- __ Дата производства -->
                    <AppLabelMultilineTSWrapper :render-object="render.date"/>

                    <!-- __ Старт -->
                    <AppLabelMultilineTSWrapper :render-object="render.start_at"/>

                    <!-- __ Финиш -->
                    <AppLabelMultilineTSWrapper :render-object="render.finish_at"/>

                    <!-- __ Продолжительность -->
                    <AppLabelMultilineTSWrapper :render-object="render.duration"/>

                    <!-- __ Прогресс общий -->
                    <AppLabelMultilineTSWrapper :render-object="render.progressTotal"/>

                    <!-- __ Опережение / отставание -->
                    <AppLabelMultilineTSWrapper :render-object="render.progressDelta"/>

                    <!-- __ Комментарий -->
                    <AppLabelMultilineTSWrapper :render-object="render.comment"/>

                    <!-- __ Дополнительное СЗ -->
                    <template v-if="tasksAvailable.length !== 0 && getAddingCondition()">
                        <AppLabelMultilineTSWrapper :render-object="render.added_task" @click="addSewingTaskToCurrentDay"/>
                    </template>

                </div>
            </div>
        </div>

        <!-- __ Данные -->
        <div
            v-for="sewingDay of renderSewingDays"
            :key="sewingDay.id"
            class="ml-2 max-w-fit"
        >
            <TheDividerLineTS
                v-if="!sewingDay.collapsed"
                m-bottom="mb-4"
            />

            <div class="flex">
                <!-- __ collapsed -->
                <AppLabelTSWrapper
                    :arg="sewingDay"
                    :render-object="render.collapsed"
                    @click="sewingDay.collapsed = !sewingDay.collapsed"
                />

                <!-- __ id -->
                <AppLabelTSWrapper
                    :arg="sewingDay"
                    :render-object="render.id"
                    @dblclick="goToSewingDay(sewingDay)"
                />

                <!-- __ Дата пр-ва -->
                <AppLabelTSWrapper
                    :arg="sewingDay"
                    :render-object="render.date"
                    @click="goToSewingDay(sewingDay)"
                />

                <!-- __ Старт -->
                <AppLabelTSWrapper
                    :arg="sewingDay"
                    :render-object="render.start_at"
                    @dblclick="goToSewingDay(sewingDay)"
                />

                <!-- __ Финиш -->
                <AppLabelTSWrapper
                    :arg="sewingDay"
                    :render-object="render.finish_at"
                    @dblclick="goToSewingDay(sewingDay)"
                />

                <!-- __ Продолжительность -->
                <AppLabelTSWrapper
                    :arg="sewingDay"
                    :render-object="render.duration"
                    @dblclick="goToSewingDay(sewingDay)"
                />

                <!-- __ Прогресс общий -->
                <AppProgressBar
                    :height="DEFAULT_HEIGHT"
                    :progress="getProgressDayTotal(sewingDay)"
                    :text="getProgressDayTotalText(sewingDay)"
                    :width="render.progressTotal.width"
                    text-size="mini"
                />

                <!-- __ Опережение/Отставание -->
                <DeviationBar
                    :deviation="getDeviationDayTotal(sewingDay)"
                    :height="DEFAULT_HEIGHT"
                    :text="getDeviationDayTotalText(sewingDay)"
                    :width="render.progressTotal.width"
                    text-size="mini"
                />

                <!-- __ Комментарий -->
                <AppLabelTSWrapper
                    :arg="sewingDay"
                    :render-object="render.comment"
                />
            </div>

            <!-- __ Содержимое СЗ -->
            <div
                v-if="!sewingDay.collapsed"
                class="ml-[34px]"
            >
                <!-- __ Персонал -->
                <div class="mt-1">
                    <AppLabelTS
                        :text="sewingDay.personal_collapsed ? 'Персонал ▲' : 'Персонал ▼'"
                        align="center"
                        rounded="4"
                        text-size="mini"
                        type="warning"
                        width="w-[218px]"
                        @click="sewingDay.personal_collapsed = !sewingDay.personal_collapsed"
                    />
                </div>

                <!-- __ Персонал -->
                <template v-if="!sewingDay.personal_collapsed">
                    <div class="mt-2 mb-2">
                        <ExecutePersonal
                            :sewing-day="sewingDay"
                            @add-worker="addWorker(sewingDay, $event)"
                            @remove-worker="removeWorker(sewingDay, $event)"
                            @add-responsible="addResponsible(sewingDay, $event)"
                        />
                    </div>
                </template>

                <!-- __ СЗ -->
                <div class="mt-1">
                    <AppLabelTS
                        :text="sewingDay.tasks_collapsed ? 'Список СЗ ▲' : 'Список СЗ ▼'"
                        align="center"
                        rounded="4"
                        text-size="mini"
                        type="warning"
                        width="w-[218px]"
                        @click="sewingDay.tasks_collapsed = !sewingDay.tasks_collapsed"
                    />
                </div>

                <!-- __ СЗ -->
                <template v-if="!sewingDay.tasks_collapsed">
                    <div class="my-2">
                        <!-- __ Шапка СЗ -->
                        <ExecuteTaskHeader :fields-width="sewingTaskFieldsWidth"/>

                        <!-- __ Сами СЗ -->
                        <div
                            v-for="sewingTask of sewingDay.sewing_tasks"
                            :key="sewingTask.id"
                            class="bg-green-100"
                        >
                            <ExecuteTask
                                :fields-width="sewingTaskFieldsWidth"
                                :sewing-task="sewingTask"
                            />
                        </div>
                    </div>
                </template>

                <!-- __ Общие данные -->
                <div class="mt-1">
                    <AppLabelTS
                        :text="sewingDay.common_collapsed ? 'Общие данные ▲' : 'Общие данные ▼'"
                        align="center"
                        rounded="4"
                        text-size="mini"
                        type="warning"
                        width="w-[218px]"
                        @click="sewingDay.common_collapsed = !sewingDay.common_collapsed"
                    />
                </div>

                <!-- __ Общие данные -->
                <template v-if="!sewingDay.common_collapsed">
                    <div class="my-2">
                        <ExecuteTaskCommon :sewing-day="sewingDay"/>
                    </div>
                </template>
            </div>

            <TheDividerLineTS
                v-if="!sewingDay.collapsed"
                m-top="mt-4"
            />
        </div>
    </div>

    <!-- __ Выпадающий список для выбора СЗ -->
    <!--:func="(task: IEntity) => `${task.surname} ${task.name} ${task.patronymic}`"-->
    <AppModalAsyncSelectTSFunc
        ref="appModalAsyncSelectTS"
        v-model="selectedTaskId"
        :func="(surname, name, patronymic) => `${surname} №${name} ${patronymic}`"
        :items="tasksAvailable"
        title="Выберите СЗ для добавления"
        width="w-[600px]"/>

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />


</template>

<script lang="ts" setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'

import type { IColorTypes, IRenderData, ISewingDay, ISewingDayWorker, ISewingTask, ISewingTaskLine } from '@/types'

import { useSewingStore } from '@/stores/SewingStore.ts'

import { SEWING_TASK_STATUSES, START_SHIFT_TIME, TOTAL_SHIFT_DURATION } from '@/app/constants/sewing.ts'

import { getExecuteTaskStatistics, getSewingDates, unionDatesWithSewingTasks } from '@/app/helpers/manufacture/helpers_sewing.ts'
import {
    formatDateInFullFormat,
    formatTimeInFullFormat,
    formatTimeWithLeadingZeros,
    getDayOfWeek,
    isHoliday,
    isToday,
} from '@/app/helpers/helpers_date'

import { round } from '@/app/helpers/helpers_lib.ts'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'
import ExecuteTask from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecuteTask.vue'
import ExecuteTaskHeader from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecuteTaskHeader.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'
import ExecutePersonal from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecutePersonal.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import ExecuteTaskCommon from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecuteTaskCommon.vue'
import DeviationBar from '@/components/ui/bars/DeviationBar.vue'
import AppModalAsyncSelectTSFunc from '@/components/ui/modals/AppModalAsyncSelectTSFunc.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'


interface IEntity {
    id: number
    name: string
    surname: string
    patronymic: string
    description: string
}

const DEBUG     = true
const isLoading = ref(false)

const sewingStore = useSewingStore()
const router      = useRouter()

const {
          globalSewingTasksPending, // __ Все задания (Global State)
      } = storeToRefs(sewingStore)

// __ Определяем переменные
const sewingDays = ref<ISewingDay[]>([])

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна выбора СЗ
const selectedTaskId        = ref<number | null>(null)
const appModalAsyncSelectTS = ref<any>(null)

// __ Получаем объект рендера
const renderSewingDays = computed<ISewingDay[]>(() => {
    return sewingDays.value
})

// __ Получаем список доступных СЗ (Выбираем статус "Готово к выполнению" и дата больше текущей даты)
const tasksAvailable = computed<IEntity[]>(() => {

    // __ Получаем сегодняшнюю дату в формате Y-m-d
    const today = new Date().toISOString().split('T')[0] // Получится '2026-05-16'

    return globalSewingTasksPending.value
        .filter((task: ISewingTask) => task.current_status.id === SEWING_TASK_STATUSES.PENDING.ID)
        .filter((task: ISewingTask) => {
            const compare = task.action_at.split(' ')[0]
            return compare > today
        })
        .map((task: ISewingTask) => {
            return {
                id         : task.id,
                surname    : task.order.client.short_name,
                name       : task.order.order_no_str,
                patronymic : `(${formatDateInFullFormat(task.action_at)})`,
                description: `${task.order.order_no_origin}/${task.order.comment_1c ?? ''}`,

            }
        })
})

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
    await appModalAsyncMultiline.value!.show()
}


// __ Переменные для рендера
// const personalShow = ref(false)
// const tasksShow    = ref(false)
// const commonShow   = ref(false)

// __ Объект отображения данных
const DEFAULT_HEIGHT   = 'h-[50px]'
const COLLAPSED_WIDTH  = 'w-[30px]'
const PROGRESS_WIDTH   = 'w-[266px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'mini'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'

const render: IRenderData = reactive({
    collapsedUp  : {
        id            : () => 'collapsed-up-search',
        header        : ['▲', ''],
        width         : COLLAPSED_WIDTH,
        height        : 'h-[24px]',
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍collapsed...',
        data          : () => '▲',
        class         : 'cursor-pointer',
    },
    collapsedDown: {
        id            : () => 'collapsed-down-search',
        header        : ['▼', ''],
        width         : COLLAPSED_WIDTH,
        height        : 'h-[24px]',
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍collapsed...',
        data          : () => '▼',
        class         : 'cursor-pointer',
    },
    collapsed    : {
        id            : () => 'collapsed-down-search',
        header        : ['▲', ''],
        width         : COLLAPSED_WIDTH,
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
        data          : (sewingDay: ISewingDay) => (sewingDay.collapsed ? '▲' : '▼'),
        class         : 'cursor-pointer',
    },
    id           : {
        id            : () => 'id-search',
        header        : ['ID', ''],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (sewingDay: ISewingDay) => sewingDay.id.toString(),
        class         : 'cursor-pointer',
    },
    date         : {
        id            : () => 'date-search',
        header        : ['Дата', 'производства'],
        width         : 'w-[218px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Дата...',
        data          : (sewingDay: ISewingDay) => formatDateInFullFormat(sewingDay.action_at) + ` (${getDayOfWeek(sewingDay.action_at)})`,
        class         : 'cursor-pointer',
    },
    start_at     : {
        id            : () => 'start-at-search',
        header        : ['Старт', ''],
        width         : 'w-[90px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Старт...',
        data          : (sewingDay: ISewingDay) => (sewingDay.start_at ? formatTimeInFullFormat(sewingDay.start_at) : ''),
        class         : 'cursor-pointer',
    },
    finish_at    : {
        id            : () => 'finish-at-search',
        header        : ['Финиш', ''],
        width         : 'w-[90px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Финиш...',
        data          : (sewingDay: ISewingDay) => (sewingDay.finish_at ? formatTimeInFullFormat(sewingDay.finish_at) : ''),
        class         : 'cursor-pointer',
    },
    duration     : {
        id            : () => 'duration-search',
        header        : ['Продолжи-', 'тельность СЗ'],
        width         : 'w-[143px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Дата...',
        data          : (sewingDay: ISewingDay) => getDuration(sewingDay),
        class         : 'cursor-pointer',
    },
    progressTotal: {
        id            : () => 'progress-total-search',
        header        : ['Прогресс выполнения от', 'общего времени СЗ'],
        width         : PROGRESS_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Дата...',
        data          : (sewingDay: ISewingDay) => sewingDay.comment ?? '',
    },
    progressDelta: {
        id            : () => 'progress-delta-search',
        header        : ['Опережение или', 'отставание'], // __ (Темп выполнения СЗ, остаток смены - остаток задания) Опережение или отставание (отношение оставшегося времени смены к оставшемуся времени СЗ)
        width         : PROGRESS_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Дата...',
        data          : (sewingDay: ISewingDay) => sewingDay.comment ?? '',
    },
    comment      : {
        id            : () => 'comment-search',
        header        : ['Комментарий к', 'производственному дню'],
        width         : 'w-[312px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Комментарий...',
        data          : (sewingDay: ISewingDay) => sewingDay.comment ?? '',
    },
    added_task   : {
        id            : () => 'added-task-search',
        header        : ['➕', 'СЗ'],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍ДСЗ...',
        data          : (/*sewingDay: ISewingDay*/) => '',
        class         : 'cursor-pointer',
    },
})

// __ Ширина полей для вывода СЗ
const sewingTaskFieldsWidth = {
    collapsed    : COLLAPSED_WIDTH,
    id           : 'w-[30px]',
    position     : 'w-[30px]',
    client       : 'w-[190px]',
    order_no     : 'w-[50px]',
    status       : 'w-[90px]',
    progressTotal: PROGRESS_WIDTH,
    load_at      : 'w-[143px]',
    comment      : 'w-[578px]',
}

// __ Определяем тип календарного дня
const dateType = (sewingDay: ISewingDay) => {
    const workDate     = new Date(sewingDay.action_at)
    const isHolidayDay = isHoliday(workDate)
    const isTodayDay   = isToday(workDate)

    if (isTodayDay) return 'success'
    if (isHolidayDay) return 'danger'
    return 'primary'
}

const expandAll   = () => sewingDays.value.forEach(sewingDay => (sewingDay.collapsed = false))
const collapseAll = () => sewingDays.value.forEach(sewingDay => (sewingDay.collapsed = true))

// __ Добавляем свернутость
const addCollapsed = () => {
    sewingDays.value = sewingDays.value.map(day => {
        return {
            ...day,
            collapsed   : true,
            personal_collapsed: true,
            tasks_collapsed: true,
            common_collapsed: true,
            sewing_tasks: day.sewing_tasks.map(task => ({
                ...task,
                collapsed: true,
            })),
        }
    })
}

// __ Добавляем работника
const addWorker = (sewingDay: ISewingDay, worker: ISewingDayWorker) => {
    const existWorker = sewingDay.workers.find(w => w.id === worker.id)
    if (!existWorker) {
        sewingDay.workers.push(worker)
    }
}

// __ Удаляем работника
const removeWorker = (sewingDay: ISewingDay, worker: ISewingDayWorker) => {
    const findIndex = sewingDay.workers.findIndex(w => w.id === worker.id)
    if (findIndex !== -1) {
        sewingDay.workers.splice(findIndex, 1)
    }
}

// __ Добавляем Ответственного
const addResponsible = (sewingDay: ISewingDay, worker: ISewingDayWorker) => {
    sewingDay.responsible = worker
}

// __ Переходим на страницу непосредственного выполнения СЗ
const goToSewingDay = (sewingDay: ISewingDay) => {
    router.push({
        name  : 'manufacture.cell.sewing.tasks.execute.day',
        params: { date: sewingDay.action_at.split(' ')[0] }, // __ Делаем из 2026-02-09 00:00:00 => YYYY-MM-DD
    })
}

// __ Получаем продолжительность СЗ
const getDuration = (sewingDay: ISewingDay) => {
    if (!sewingDay.start_at) {
        return ''
    }

    const startSec  = new Date(sewingDay.start_at.replace(' ', 'T')).getTime() / 1000
    const finishSec = sewingDay.finish_at ? new Date(sewingDay.finish_at.replace(' ', 'T')).getTime() / 1000 : new Date().getTime() / 1000

    return formatTimeWithLeadingZeros(round(finishSec - startSec))
}

// __ Получаем объект статистики для дня
const getDayStatistics = (sewingDay: ISewingDay) => {
    const allSewingTasksLines: ISewingTaskLine[] = []
    sewingDay.sewing_tasks.forEach(task => task.sewing_lines.forEach(line => allSewingTasksLines.push(line)))
    return getExecuteTaskStatistics(allSewingTasksLines)
}

// __ Получаем прогресс выполнения СЗ по дню
const getProgressDayTotal = (sewingDay: ISewingDay) => {
    const statistics = getDayStatistics(sewingDay)
    return (statistics.time.finished / statistics.time.total) * 100
}

// __ Получаем текст прогресса выполнения СЗ по дню
const getProgressDayTotalText = (sewingDay: ISewingDay) => {
    const statistics = getDayStatistics(sewingDay)
    return `${formatTimeWithLeadingZeros(statistics.time.finished)} / ${formatTimeWithLeadingZeros(statistics.time.total)}`
}

// __ Получаем отклонение прогресса выполнения СЗ по дню в секундах
const getDeviationDay = (sewingDay: ISewingDay) => {
    const statistics = getDayStatistics(sewingDay)

    if (sewingDay.start_at && !sewingDay.finish_at) {
        // __ Находим время окончания смены
        const endShiftTime     = new Date(sewingDay.start_at.replace(' ', 'T'))
        const [hours, minutes] = START_SHIFT_TIME.split(':')
        endShiftTime.setHours(Number(hours), Number(minutes) + TOTAL_SHIFT_DURATION * 60, 0, 0)

        // __ Оставшееся время до окончания смены в секундах
        const remainingTime = (endShiftTime.getTime() - new Date().getTime()) / 1000

        // __ Опережение или отставание
        if (statistics.time.unfinished === 0) return 0
        return round(remainingTime - statistics.time.unfinished)
    }

    return 0
}

// __ Получаем отклонение прогресса выполнения СЗ по дню в %
const getDeviationDayTotal = (sewingDay: ISewingDay) => {
    const statistics = getDayStatistics(sewingDay)
    return statistics.time.unfinished !== 0 ? (getDeviationDay(sewingDay) / statistics.time.unfinished) * 100 : 0
}

// __ Текст для опережения/отставания
const getDeviationDayTotalText = (sewingDay: ISewingDay) => {
    const deviation = getDeviationDay(sewingDay)
    if (deviation === 0) {
        return 'В графике'
    }

    return deviation > 0 ? 'ОПЕРЕЖЕНИЕ' : 'ОТСТАВАНИЕ' + ' ' + formatTimeWithLeadingZeros(Math.abs(deviation))
}

// __ Получаем условие для добавления СЗ в текущий производственный день
// __ Не должно быть СЗ в процессе выполнения или ожидания к выполнению
const getAddingCondition = () => {

    // __ Получаем сегодняшнюю дату в формате Y-m-d
    const today = new Date().toISOString().split('T')[0] // Получится '2026-05-16'

    const filtered = globalSewingTasksPending.value
        // .filter((task: ISewingTask) => task.current_status.id === SEWING_TASK_STATUSES.RUNNING.ID)
        .filter((task: ISewingTask) => {
            const compare = task.action_at.split(' ')[0]
            return compare == today
        })

    return filtered.length === 0
}


// __ Добавление СЗ в текущий производственный день
const addSewingTaskToCurrentDay = async () => {

    if (tasksAvailable.value.length === 0) {
        return
    }

    const answerSelection = await appModalAsyncSelectTS.value!.show()
    if (answerSelection) {

        const selectedTask = appModalAsyncSelectTS.value!.selected

        modalInfoType.value = 'danger'
        modalInfoMode.value = 'confirm'
        modalInfoText.value = [
            `СЗ ${selectedTask.surname} №${selectedTask.name} ${selectedTask.patronymic}`,
            'будет добавлено в текущий производственный день.',
            'Действие нельзя будет отменить!',
            'Продолжить?',
        ]

        const answer = await appModalAsyncMultiline.value!.show()
        if (answer) {

            // __ Получаем сегодняшнюю дату в формате Y-m-d
            const today  = new Date().toISOString().split('T')[0] // Получится '2026-05-16'
            const result = await sewingStore.setSewingTaskActionAt(selectedTask.id, today)

            if (checkCRUD(result)) {
                modalInfoType.value = 'success'
                modalInfoMode.value = 'inform'
                modalInfoText.value = [
                    `СЗ ${selectedTask.surname} №${selectedTask.name} ${selectedTask.patronymic}`,
                    'добавлено в текущий производственный день.',
                    'Для актуализации данных, страница будет перезагружена.',
                ]
                await appModalAsyncMultiline.value!.show()
                window.location.reload()
            } else {
                await showError()
                return
            }
        }
    }
}


// __ Получаем производственные дни
const getSewingDays = async () => {
    const dates      = getSewingDates(globalSewingTasksPending.value) // __ Получаем даты из СЗ
    sewingDays.value = await sewingStore.getSewingDaysByDates(dates)  // __ Получаем дни по этим датам
}

onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            // __ Получаем SewingTasks по статусу и записываем в глобальную переменную в SewingStore
            await sewingStore.getSewingTasksByStatus([SEWING_TASK_STATUSES.PENDING.ID, SEWING_TASK_STATUSES.RUNNING.ID])

            // __ Получаем дни
            await getSewingDays()

            // __ Объединяем задания с днями
            unionDatesWithSewingTasks(sewingDays.value, globalSewingTasksPending.value)

            // __ Добавляем свернутость
            addCollapsed()

            if (DEBUG) console.log('globalSewingTasksPending:', globalSewingTasksPending.value)
            if (DEBUG) console.log('sewingDays:', sewingDays.value)
            if (DEBUG) console.log('renderSewingDays:', renderSewingDays.value)
        },
        undefined
        // false,
    )

    isLoading.value = false
})
</script>

<style scoped></style>
