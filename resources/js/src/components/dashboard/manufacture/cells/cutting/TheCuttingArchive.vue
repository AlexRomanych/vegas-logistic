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

                    <!-- __ Выбор дат -->
                    <CellDatesSelectMiniTS
                        :period="renderPeriod"
                        @apply="getCuttingTasks"
                    />

                </div>
            </div>
        </div>

        <!-- __ Данные -->
        <div
            v-for="cuttingDay of renderCuttingDays"
            :key="cuttingDay.id"
            class="ml-2 max-w-fit"
        >
            <TheDividerLineTS
                v-if="!cuttingDay.collapsed"
                m-bottom="mb-4"
            />

            <div class="flex">
                <!-- __ collapsed -->
                <AppLabelTSWrapper
                    :arg="cuttingDay"
                    :render-object="render.collapsed"
                    @click="cuttingDay.collapsed = !cuttingDay.collapsed"
                />

                <!-- __ id -->
                <AppLabelTSWrapper
                    :arg="cuttingDay"
                    :render-object="render.id"
                />

                <!-- __ Дата пр-ва -->
                <AppLabelTSWrapper
                    :arg="cuttingDay"
                    :render-object="render.date"
                />

                <!-- __ Старт -->
                <AppLabelTSWrapper
                    :arg="cuttingDay"
                    :render-object="render.start_at"
                />

                <!-- __ Финиш -->
                <AppLabelTSWrapper
                    :arg="cuttingDay"
                    :render-object="render.finish_at"
                />

                <!-- __ Продолжительность -->
                <AppLabelTSWrapper
                    :arg="cuttingDay"
                    :render-object="render.duration"
                />

                <!-- __ Прогресс общий -->
                <AppProgressBar
                    :height="DEFAULT_HEIGHT"
                    :progress="getProgressDayTotal(cuttingDay)"
                    :text="getProgressDayTotalText(cuttingDay)"
                    :width="render.progressTotal.width"
                    text-size="mini"
                />

                <!-- __ Опережение/Отставание -->
                <DeviationBar
                    :deviation="getDeviationDayTotal(cuttingDay)"
                    :height="DEFAULT_HEIGHT"
                    :text="getDeviationDayTotalText(cuttingDay)"
                    :width="render.progressTotal.width"
                    text-size="mini"
                />

                <!-- __ Комментарий -->
                <AppLabelTSWrapper
                    :arg="cuttingDay"
                    :render-object="render.comment"
                />
            </div>

            <!-- __ Содержимое СЗ -->
            <div
                v-if="!cuttingDay.collapsed"
                class="ml-[34px]"
            >
                <!-- __ Персонал -->
                <div class="mt-1">
                    <AppLabelTS
                        :text="cuttingDay.personal_collapsed ? 'Персонал ▲' : 'Персонал ▼'"
                        align="center"
                        rounded="4"
                        text-size="mini"
                        type="warning"
                        width="w-[218px]"
                        @click="cuttingDay.personal_collapsed = !cuttingDay.personal_collapsed"
                    />
                </div>

                <!-- __ Персонал -->
                <template v-if="!cuttingDay.personal_collapsed">
                    <div class="mt-2 mb-2">
                        <ExecutePersonal
                            :can-edit="false"
                            :cutting-day="cuttingDay"
                        />
                    </div>
                </template>

                <!-- __ СЗ -->
                <div class="mt-1">
                    <AppLabelTS
                        :text="cuttingDay.tasks_collapsed ? 'Список СЗ ▲' : 'Список СЗ ▼'"
                        align="center"
                        rounded="4"
                        text-size="mini"
                        type="warning"
                        width="w-[218px]"
                        @click="cuttingDay.tasks_collapsed = !cuttingDay.tasks_collapsed"
                    />
                </div>

                <!-- __ СЗ -->
                <template v-if="!cuttingDay.tasks_collapsed">
                    <div class="my-2">
                        <!-- __ Шапка СЗ -->
                        <ExecuteTaskHeader :fields-width="cuttingTaskFieldsWidth"/>

                        <!-- __ Сами СЗ -->
                        <div
                            v-for="cuttingTask of cuttingDay.cutting_tasks"
                            :key="cuttingTask.id"
                            class="bg-green-100"
                        >
                            <ExecuteTask
                                :fields-width="cuttingTaskFieldsWidth"
                                :cutting-task="cuttingTask"
                            />
                        </div>
                    </div>
                </template>

                <!-- __ Общие данные -->
                <div class="mt-1">
                    <AppLabelTS
                        :text="cuttingDay.common_collapsed ? 'Общие данные ▲' : 'Общие данные ▼'"
                        align="center"
                        rounded="4"
                        text-size="mini"
                        type="warning"
                        width="w-[218px]"
                        @click="cuttingDay.common_collapsed = !cuttingDay.common_collapsed"
                    />
                </div>

                <!-- __ Общие данные -->
                <template v-if="!cuttingDay.common_collapsed">
                    <div class="my-2">
                        <ExecuteTaskCommon :cutting-day="cuttingDay"/>
                    </div>
                </template>
            </div>

            <TheDividerLineTS
                v-if="!cuttingDay.collapsed"
                m-top="mt-4"
            />
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { storeToRefs } from 'pinia'
// import { useRouter } from 'vue-router'

import type { IPeriod, IRenderData, ICuttingDay, ICuttingTaskLine } from '@/types'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import { START_SHIFT_TIME, TOTAL_SHIFT_DURATION, /*CUTTING_TASK_STATUSES,*/ } from '@/app/constants/cutting.ts'

import { getExecuteTaskStatistics, getCuttingDates, unionDatesWithCuttingTasks } from '@/app/helpers/manufacture/helpers_cutting.ts'
import {
    formatDateInFullFormat,
    formatTimeInFullFormat,
    formatTimeWithLeadingZeros,
    getDayOfWeek,
    isHoliday,
    isToday,
} from '@/app/helpers/helpers_date'

import { round } from '@/app/helpers/helpers_lib.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'
import ExecuteTask from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute/ExecuteTask.vue'
import ExecuteTaskHeader from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute/ExecuteTaskHeader.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'
import ExecutePersonal from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute/ExecutePersonal.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import ExecuteTaskCommon from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute/ExecuteTaskCommon.vue'
import DeviationBar from '@/components/ui/bars/DeviationBar.vue'
import CellDatesSelectMiniTS from '@/components/dashboard/manufacture/components/CellDatesSelectMiniTS.vue'

const DEBUG     = true
const isLoading = ref(false)

const cuttingStore = useCuttingStore()
// const router = useRouter()

const {
          globalCuttingTasksPending, // __ Все задания (Global State)
      } = storeToRefs(cuttingStore)

// __ Определяем переменные
const cuttingDays = ref<ICuttingDay[]>([])
const renderPeriod = ref<IPeriod | null>(null)

// __ Получаем объект рендера
const renderCuttingDays = computed<ICuttingDay[]>(() => {
    return cuttingDays.value
})

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
        data          : (cuttingDay: ICuttingDay) => (cuttingDay.collapsed ? '▲' : '▼'),
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
        type          : (cuttingDay: ICuttingDay) => dateType(cuttingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (cuttingDay: ICuttingDay) => cuttingDay.id.toString(),
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
        type          : (cuttingDay: ICuttingDay) => dateType(cuttingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Дата...',
        data          : (cuttingDay: ICuttingDay) => formatDateInFullFormat(cuttingDay.action_at) + ` (${getDayOfWeek(cuttingDay.action_at)})`,
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
        type          : (cuttingDay: ICuttingDay) => dateType(cuttingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Старт...',
        data          : (cuttingDay: ICuttingDay) => (cuttingDay.start_at ? formatTimeInFullFormat(cuttingDay.start_at) : ''),
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
        type          : (cuttingDay: ICuttingDay) => dateType(cuttingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Финиш...',
        data          : (cuttingDay: ICuttingDay) => (cuttingDay.finish_at ? formatTimeInFullFormat(cuttingDay.finish_at) : ''),
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
        type          : (cuttingDay: ICuttingDay) => dateType(cuttingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Дата...',
        data          : (cuttingDay: ICuttingDay) => getDuration(cuttingDay),
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
        data          : (cuttingDay: ICuttingDay) => cuttingDay.comment ?? '',
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
        data          : (cuttingDay: ICuttingDay) => cuttingDay.comment ?? '',
    },
    comment      : {
        id            : () => 'comment-search',
        header        : ['Комментарий к', 'производственному дню'],
        width         : 'w-[312px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (cuttingDay: ICuttingDay) => dateType(cuttingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Комментарий...',
        data          : (cuttingDay: ICuttingDay) => cuttingDay.comment ?? '',
    },
})

// __ Ширина полей для вывода СЗ
const cuttingTaskFieldsWidth = {
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
const dateType = (cuttingDay: ICuttingDay) => {
    const workDate     = new Date(cuttingDay.action_at)
    const isHolidayDay = isHoliday(workDate)
    const isTodayDay   = isToday(workDate)

    if (isTodayDay) return 'success'
    if (isHolidayDay) return 'danger'
    return 'primary'
}

const expandAll   = () => cuttingDays.value.forEach(cuttingDay => (cuttingDay.collapsed = false))
const collapseAll = () => cuttingDays.value.forEach(cuttingDay => (cuttingDay.collapsed = true))

// __ Добавляем свернутость
const addCollapsed = () => {
    cuttingDays.value = cuttingDays.value.map(day => {
        return {
            ...day,
            collapsed   : true,
            personal_collapsed: true,
            tasks_collapsed: true,
            common_collapsed: true,
            cutting_tasks: day.cutting_tasks.map(task => ({
                ...task,
                collapsed: true,
            })),
        }
    })
}


// __ Получаем продолжительность СЗ
const getDuration = (cuttingDay: ICuttingDay) => {
    if (!cuttingDay.start_at) {
        return ''
    }

    const startSec  = new Date(cuttingDay.start_at.replace(' ', 'T')).getTime() / 1000
    const finishSec = cuttingDay.finish_at ? new Date(cuttingDay.finish_at.replace(' ', 'T')).getTime() / 1000 : new Date().getTime() / 1000

    return formatTimeWithLeadingZeros(round(finishSec - startSec))
}

// __ Получаем объект статистики для дня
const getDayStatistics = (cuttingDay: ICuttingDay) => {
    const allCuttingTasksLines: ICuttingTaskLine[] = []
    cuttingDay.cutting_tasks.forEach(task => task.cutting_lines.forEach(line => allCuttingTasksLines.push(line)))
    return getExecuteTaskStatistics(allCuttingTasksLines)
}

// __ Получаем прогресс выполнения СЗ по дню
const getProgressDayTotal = (cuttingDay: ICuttingDay) => {
    const statistics = getDayStatistics(cuttingDay)
    return (statistics.time.finished / statistics.time.total) * 100
}

// __ Получаем текст прогресса выполнения СЗ по дню
const getProgressDayTotalText = (cuttingDay: ICuttingDay) => {
    const statistics = getDayStatistics(cuttingDay)
    return `${formatTimeWithLeadingZeros(statistics.time.finished)} / ${formatTimeWithLeadingZeros(statistics.time.total)}`
}

// __ Получаем отклонение прогресса выполнения СЗ по дню в секундах
const getDeviationDay = (cuttingDay: ICuttingDay) => {
    const statistics = getDayStatistics(cuttingDay)

    if (cuttingDay.start_at && !cuttingDay.finish_at) {
        // __ Находим время окончания смены
        const endShiftTime     = new Date(cuttingDay.start_at.replace(' ', 'T'))
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
const getDeviationDayTotal = (cuttingDay: ICuttingDay) => {
    const statistics = getDayStatistics(cuttingDay)
    return statistics.time.unfinished !== 0 ? (getDeviationDay(cuttingDay) / statistics.time.unfinished) * 100 : 0
}

// __ Текст для опережения/отставания
const getDeviationDayTotalText = (cuttingDay: ICuttingDay) => {
    const deviation = getDeviationDay(cuttingDay)
    if (deviation === 0) {
        return 'В графике'
    }

    return deviation > 0 ? 'ОПЕРЕЖЕНИЕ' : 'ОТСТАВАНИЕ' + ' ' + formatTimeWithLeadingZeros(Math.abs(deviation))
}

// __ Получаем производственные дни
const getCuttingDays = async () => {
    const dates      = getCuttingDates(globalCuttingTasksPending.value) // __ Получаем даты из СЗ
    cuttingDays.value = await cuttingStore.getCuttingDaysByDates(dates) // __ Получаем дни по этим датам
}

// __ Получаем CuttingTasks
const getCuttingTasks = async (period: IPeriod | null = null) => {
    renderPeriod.value = period

    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            // __ Получаем CuttingTasks и записываем в глобальную переменную в CuttingStore
            await cuttingStore.getCuttingTasksByStatusAndPeriod(period)
            // await cuttingStore.getCuttingTasksByStatus()
            // await cuttingStore.getCuttingTasksByStatusAndPeriod(
            //     { start: '2026-04-01', end: '2026-04-17' },
            //     [CUTTING_TASK_STATUSES.PENDING.ID, CUTTING_TASK_STATUSES.RUNNING.ID]
            // )
            // await cuttingStore.getCuttingTasksByStatusAndPeriod(null, [CUTTING_TASK_STATUSES.RUNNING.ID])
            // await cuttingStore.getCuttingTasksByStatus([CUTTING_TASK_STATUSES.PENDING.ID, CUTTING_TASK_STATUSES.RUNNING.ID])

            // __ Получаем дни
            await getCuttingDays()

            // __ Объединяем задания с днями
            unionDatesWithCuttingTasks(cuttingDays.value, globalCuttingTasksPending.value)

            // __ Добавляем свернутость
            addCollapsed()

            if (DEBUG) console.log('globalCuttingTasks:', globalCuttingTasksPending.value)
            if (DEBUG) console.log('cuttingDays:', cuttingDays.value)
            if (DEBUG) console.log('renderCuttingDays:', renderCuttingDays.value)
        },
        undefined
        // false,
    )

    isLoading.value = false
}



onMounted(async () => {
    await getCuttingTasks()

    // isLoading.value = true
    //
    // const loadingService = useLoading()
    // await loaderHandler(
    //     loadingService,
    //     async () => {
    //
    //         // __ Получаем CuttingTasks и записываем в глобальную переменную в CuttingStore
    //         await getCuttingTasks()
    //         // await cuttingStore.getCuttingTasksByStatus()
    //         // await cuttingStore.getCuttingTasksByStatusAndPeriod(
    //         //     { start: '2026-04-01', end: '2026-04-17' },
    //         //     [CUTTING_TASK_STATUSES.PENDING.ID, CUTTING_TASK_STATUSES.RUNNING.ID]
    //         // )
    //         // await cuttingStore.getCuttingTasksByStatusAndPeriod(null, [CUTTING_TASK_STATUSES.RUNNING.ID])
    //         // await cuttingStore.getCuttingTasksByStatus([CUTTING_TASK_STATUSES.PENDING.ID, CUTTING_TASK_STATUSES.RUNNING.ID])
    //
    //         // // __ Получаем дни
    //         // await getCuttingDays()
    //         //
    //         // // __ Объединяем задания с днями
    //         // unionDatesWithCuttingTasks(cuttingDays.value, globalCuttingTasksPending.value)
    //         //
    //         // // __ Добавляем свернутость
    //         // addCollapsed()
    //
    //         if (DEBUG) console.log('globalCuttingTasks:', globalCuttingTasksPending.value)
    //         if (DEBUG) console.log('cuttingDays:', cuttingDays.value)
    //         if (DEBUG) console.log('renderCuttingDays:', renderCuttingDays.value)
    //     },
    //     undefined
    //     // false,
    // )
    //
    // isLoading.value = false
})
</script>

<style scoped></style>
