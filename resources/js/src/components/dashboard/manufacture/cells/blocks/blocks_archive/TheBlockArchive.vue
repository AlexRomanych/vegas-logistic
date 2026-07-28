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

                    <!-- __ Смена производства -->
                    <AppLabelMultilineTSWrapper :render-object="render.change"/>

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
                        @apply="getBlockTasks"
                    />

                </div>
            </div>
        </div>

        <!-- __ Данные -->
        <div
            v-for="blockDay of renderBlockDays"
            :key="blockDay.id"
            class="ml-2 max-w-fit"
        >
            <TheDividerLineTS
                v-if="!blockDay.collapsed"
                m-bottom="mb-4"
            />

            <div class="flex">
                <!-- __ collapsed -->
                <AppLabelTSWrapper
                    :arg="blockDay"
                    :render-object="render.collapsed"
                    @click="blockDay.collapsed = !blockDay.collapsed"
                />

                <!-- __ id -->
                <AppLabelTSWrapper
                    :arg="blockDay"
                    :render-object="render.id"
                />

                <!-- __ Дата пр-ва -->
                <AppLabelTSWrapper
                    :arg="blockDay"
                    :render-object="render.date"
                />

                <!-- __ Смена пр-ва -->
                <AppLabelTSWrapper
                    :arg="blockDay"
                    :render-object="render.change"
                    @click="goToBlockDay(blockDay)"
                />

                <!-- __ Старт -->
                <AppLabelTSWrapper
                    :arg="blockDay"
                    :render-object="render.start_at"
                />

                <!-- __ Финиш -->
                <AppLabelTSWrapper
                    :arg="blockDay"
                    :render-object="render.finish_at"
                />

                <!-- __ Продолжительность -->
                <AppLabelTSWrapper
                    :arg="blockDay"
                    :render-object="render.duration"
                />

                <!-- __ Прогресс общий -->
                <AppProgressBar
                    :height="DEFAULT_HEIGHT"
                    :progress="getProgressDayTotal(blockDay)"
                    :text="getProgressDayTotalText(blockDay)"
                    :width="render.progressTotal.width"
                    text-size="mini"
                />

                <!-- __ Опережение/Отставание -->
                <DeviationBar
                    :deviation="getDeviationDayTotal(blockDay)"
                    :height="DEFAULT_HEIGHT"
                    :text="getDeviationDayTotalText(blockDay)"
                    :width="render.progressTotal.width"
                    text-size="mini"
                />

                <!-- __ Комментарий -->
                <AppLabelTSWrapper
                    :arg="blockDay"
                    :render-object="render.comment"
                />
            </div>

            <!-- __ Содержимое СЗ -->
            <div
                v-if="!blockDay.collapsed"
                class="ml-[34px]"
            >
                <!-- __ Персонал -->
                <div class="mt-1">
                    <AppLabelTS
                        :text="blockDay.personal_collapsed ? 'Персонал ▲' : 'Персонал ▼'"
                        align="center"
                        rounded="4"
                        text-size="mini"
                        type="warning"
                        width="w-[218px]"
                        @click="blockDay.personal_collapsed = !blockDay.personal_collapsed"
                    />
                </div>

                <!-- __ Персонал -->
                <template v-if="!blockDay.personal_collapsed">
                    <div class="mt-2 mb-2">
                        <ExecutePersonal
                            :block-day="blockDay"
                            :can-edit="false"
                        />
                    </div>
                </template>

                <!-- __ СЗ -->
                <div class="mt-1">
                    <AppLabelTS
                        :text="blockDay.tasks_collapsed ? 'Список СЗ ▲' : 'Список СЗ ▼'"
                        align="center"
                        rounded="4"
                        text-size="mini"
                        type="warning"
                        width="w-[218px]"
                        @click="blockDay.tasks_collapsed = !blockDay.tasks_collapsed"
                    />
                </div>

                <!-- __ СЗ -->
                <template v-if="!blockDay.tasks_collapsed">
                    <div class="my-2">
                        <!-- __ Шапка СЗ -->
                        <ExecuteTaskHeader :fields-width="blockTaskFieldsWidth"/>

                        <!-- __ Сами СЗ -->
                        <div
                            v-for="blockTask of blockDay.block_tasks"
                            :key="blockTask.id"
                            class="bg-green-100"
                        >
                            <ExecuteTask
                                :block-task="blockTask"
                                :fields-width="blockTaskFieldsWidth"
                            />
                        </div>
                    </div>
                </template>

                <!-- __ Общие данные -->
                <div class="mt-1">
                    <AppLabelTS
                        :text="blockDay.common_collapsed ? 'Общие данные ▲' : 'Общие данные ▼'"
                        align="center"
                        rounded="4"
                        text-size="mini"
                        type="warning"
                        width="w-[218px]"
                        @click="blockDay.common_collapsed = !blockDay.common_collapsed"
                    />
                </div>

                <!-- __ Общие данные -->
                <template v-if="!blockDay.common_collapsed">
                    <div class="my-2">
                        <ExecuteTaskCommon :block-day="blockDay"/>
                    </div>
                </template>

                <!-- __ События -->
                <div class="mt-1">
                    <AppLabelTS
                        :text="blockDay.cell_events_collapsed ? 'События ▲' : 'События ▼'"
                        align="center"
                        rounded="4"
                        text-size="mini"
                        type="warning"
                        width="w-[218px]"
                        @click="blockDay.cell_events_collapsed = !blockDay.cell_events_collapsed"
                    />
                </div>

                <!-- __ События -->
                <template v-if="!blockDay.cell_events_collapsed">
                    <div class="mt-2 mb-2">
                        <ExecuteCellEvents
                            :block-day="blockDay"
                        />
                    </div>
                </template>

            </div>

            <TheDividerLineTS
                v-if="!blockDay.collapsed"
                m-top="mt-4"
            />
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'

import type { IPeriod, IRenderData, IBlockDay, IBlockTaskLine } from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

import { START_SHIFT_TIME, TOTAL_SHIFT_DURATION, } from '@/app/constants/blocks.ts'

import { getExecuteTaskStatistics, getBlockDates, unionDatesWithBlockTasks, getChangeByName } from '@/app/helpers/manufacture/helpers_blocks.ts'
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

import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import DeviationBar from '@/components/ui/bars/DeviationBar.vue'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'

import ExecuteTask from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteTask.vue'
import ExecuteTaskHeader from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteTaskHeader.vue'
import ExecutePersonal from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecutePersonal.vue'
import ExecuteTaskCommon from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteTaskCommon.vue'

import CellDatesSelectMiniTS from '@/components/dashboard/manufacture/components/CellDatesSelectMiniTS.vue'
import ExecuteCellEvents from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteCellEvents.vue'

const DEBUG     = true
const isLoading = ref(false)

const blockStore = useBlocksStore()
const router = useRouter()

const {
          globalBlockTasksPending, // __ Все задания (Global State)
      } = storeToRefs(blockStore)

// __ Определяем переменные
const blockDays    = ref<IBlockDay[]>([])
const renderPeriod = ref<IPeriod | null>(null)

// __ Получаем объект рендера
const renderBlockDays = computed<IBlockDay[]>(() => {
    return blockDays.value
})

// __ Получаем подсветку для Смены
const dateTypeChange = (blockDay: IBlockDay) => {
    const change = getChangeByName(blockDay.change)
    return change ? change.TYPE : dateType(blockDay)
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
        data          : (blockDay: IBlockDay) => (blockDay.collapsed ? '▲' : '▼'),
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
        type          : (blockDay: IBlockDay) => dateType(blockDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (blockDay: IBlockDay) => blockDay.id.toString(),
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
        type          : (blockDay: IBlockDay) => dateType(blockDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Дата...',
        data          : (blockDay: IBlockDay) => formatDateInFullFormat(blockDay.action_at) + ` (${getDayOfWeek(blockDay.action_at)})`,
        class         : 'cursor-pointer',
    },
    change       : {
        id            : () => 'change-search',
        header        : ['Смена', ''],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockDay: IBlockDay) => dateTypeChange(blockDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Смена...',
        data          : (blockDay: IBlockDay) => blockDay.change,
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
        type          : (blockDay: IBlockDay) => dateType(blockDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Старт...',
        data          : (blockDay: IBlockDay) => (blockDay.start_at ? formatTimeInFullFormat(blockDay.start_at) : ''),
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
        type          : (blockDay: IBlockDay) => dateType(blockDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Финиш...',
        data          : (blockDay: IBlockDay) => (blockDay.finish_at ? formatTimeInFullFormat(blockDay.finish_at) : ''),
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
        type          : (blockDay: IBlockDay) => dateType(blockDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Дата...',
        data          : (blockDay: IBlockDay) => getDuration(blockDay),
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
        data          : (blockDay: IBlockDay) => blockDay.comment ?? '',
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
        data          : (blockDay: IBlockDay) => blockDay.comment ?? '',
    },
    comment      : {
        id            : () => 'comment-search',
        header        : ['Комментарий к', 'производственному дню'],
        width         : 'w-[352px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockDay: IBlockDay) => dateType(blockDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Комментарий...',
        data          : (blockDay: IBlockDay) => blockDay.comment ?? '',
    },
})

// __ Ширина полей для вывода СЗ
const blockTaskFieldsWidth = {
    collapsed    : COLLAPSED_WIDTH,
    id           : 'w-[30px]',
    position     : 'w-[30px]',
    client       : 'w-[285px]',
    order_no     : 'w-[50px]',
    status       : 'w-[148px]',
    progressTotal: PROGRESS_WIDTH,
    load_at      : 'w-[155px]',
    comment      : 'w-[511px]',
}

// __ Определяем тип календарного дня
const dateType = (blockDay: IBlockDay) => {
    const workDate     = new Date(blockDay.action_at)
    const isHolidayDay = isHoliday(workDate)
    const isTodayDay   = isToday(workDate)

    if (isTodayDay) return 'success'
    if (isHolidayDay) return 'danger'
    return 'primary'
}

const expandAll   = () => blockDays.value.forEach(blockDay => (blockDay.collapsed = false))
const collapseAll = () => blockDays.value.forEach(blockDay => (blockDay.collapsed = true))

// __ Добавляем свернутость
const addCollapsed = () => {
    blockDays.value = blockDays.value.map(day => {
        return {
            ...day,
            collapsed            : true,
            personal_collapsed   : true,
            tasks_collapsed      : true,
            common_collapsed     : true,
            cell_events_collapsed: true,
            block_tasks          : day.block_tasks.map(task => ({
                ...task,
                collapsed: true,
            })),
        }
    })
}


// __ Получаем продолжительность СЗ
const getDuration = (blockDay: IBlockDay) => {
    if (!blockDay.start_at) {
        return ''
    }

    const startSec  = new Date(blockDay.start_at.replace(' ', 'T')).getTime() / 1000
    const finishSec = blockDay.finish_at ? new Date(blockDay.finish_at.replace(' ', 'T')).getTime() / 1000 : new Date().getTime() / 1000

    return formatTimeWithLeadingZeros(round(finishSec - startSec))
}

// __ Получаем объект статистики для дня
const getDayStatistics = (blockDay: IBlockDay) => {
    const allBlockTasksLines: IBlockTaskLine[] = []
    blockDay.block_tasks.forEach(task => task.block_lines.forEach(line => allBlockTasksLines.push(line)))
    return getExecuteTaskStatistics(allBlockTasksLines)
}

// __ Получаем прогресс выполнения СЗ по дню
const getProgressDayTotal = (blockDay: IBlockDay) => {
    const statistics = getDayStatistics(blockDay)
    return (statistics.time.finished / statistics.time.total) * 100
}

// __ Получаем текст прогресса выполнения СЗ по дню
const getProgressDayTotalText = (blockDay: IBlockDay) => {
    const statistics = getDayStatistics(blockDay)
    return `${formatTimeWithLeadingZeros(statistics.time.finished, 'hour')} / ${formatTimeWithLeadingZeros(statistics.time.total, 'hour')}`
}

// __ Получаем отклонение прогресса выполнения СЗ по дню в секундах
const getDeviationDay = (blockDay: IBlockDay) => {
    const statistics = getDayStatistics(blockDay)

    if (blockDay.start_at && !blockDay.finish_at) {
        // __ Находим время окончания смены
        const endShiftTime     = new Date(blockDay.start_at.replace(' ', 'T'))
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
const getDeviationDayTotal = (blockDay: IBlockDay) => {
    const statistics = getDayStatistics(blockDay)
    return statistics.time.unfinished !== 0 ? (getDeviationDay(blockDay) / statistics.time.unfinished) * 100 : 0
}

// __ Текст для опережения/отставания
const getDeviationDayTotalText = (blockDay: IBlockDay) => {
    const deviation = getDeviationDay(blockDay)
    if (deviation === 0) {
        return 'В графике'
    }

    return deviation > 0 ? 'ОПЕРЕЖЕНИЕ' : 'ОТСТАВАНИЕ' + ' ' + formatTimeWithLeadingZeros(Math.abs(deviation), 'hour')
}

// __ Получаем производственные дни
const getBlockDays = async () => {
    const dates     = getBlockDates(globalBlockTasksPending.value) // __ Получаем даты из СЗ
    blockDays.value = await blockStore.getBlockDaysByDates(dates) // __ Получаем дни по этим датам
}

// __ Переходим на страницу непосредственного выполнения СЗ
const goToBlockDay = (blockDay: IBlockDay) => {
    router.push({
        name  : 'manufacture.cell.blocks.tasks.execute.day',
        params: {
            // __ Делаем из 2026-02-09 00:00:00 => YYYY-MM-DD
            date  : blockDay.action_at.split(' ')[0],
            change: blockDay.change
        },
    })
}

// __ Получаем BlockTasks
const getBlockTasks = async (period: IPeriod | null = null) => {
    renderPeriod.value = period


    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            // __ Получаем BlockTasks по статусу и записываем в глобальную переменную в BlockStore
            await blockStore.getBlockTasksByStatusAndPeriod(null, renderPeriod.value)

            // __ Получаем дни
            await getBlockDays()

            // __ Объединяем задания с днями
            unionDatesWithBlockTasks(blockDays.value, globalBlockTasksPending.value)

            // __ Удаляем дни без заданий
            blockDays.value = blockDays.value.filter((day) => day.block_tasks.length > 0)

            // __ Добавляем свернутость
            addCollapsed()

            if (DEBUG) console.log('globalBlockTasksPending:', globalBlockTasksPending.value)
            if (DEBUG) console.log('blockDays:', blockDays.value)
            if (DEBUG) console.log('renderBlockDays:', renderBlockDays.value)
        },
        undefined
        // false,
    )

    isLoading.value = false
}


onMounted(async () => {
    await getBlockTasks()
})
</script>

<style scoped></style>
