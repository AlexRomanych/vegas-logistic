<template>
    <div v-if="!isLoading" class="ml-2 mt-2">
        <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit">
            <div>
                <div class="flex ml-0.5">

                    <!-- __ Collapsed -->
                    <div>
                        <AppLabelTSWrapper :render-object="render.collapsedUp" @click="collapseAll"/>
                        <AppLabelTSWrapper :render-object="render.collapsedDown" @click="expandAll"/>
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

                </div>
            </div>
        </div>

        <!-- __ Данные -->
        <div v-for="sewingDay of renderSewingDays" :key="sewingDay.id" class="ml-2 max-w-fit">
            <div class="flex ">

                <!-- __ collapsed -->
                <AppLabelTSWrapper
                    :arg="sewingDay"
                    :render-object="render.collapsed"
                    @click="sewingDay.collapsed = !sewingDay.collapsed"
                />

                <!-- __ id -->
                <AppLabelTSWrapper :arg="sewingDay" :render-object="render.id"/>

                <!-- __ Дата пр-ва -->
                <AppLabelTSWrapper :arg="sewingDay" :render-object="render.date"/>

                <!-- __ Старт -->
                <AppLabelTSWrapper :arg="sewingDay" :render-object="render.start_at"/>

                <!-- __ Финиш -->
                <AppLabelTSWrapper :arg="sewingDay" :render-object="render.finish_at"/>

                <!-- __ Продолжительность -->
                <AppLabelTSWrapper :arg="sewingDay" :render-object="render.duration"/>

                <!-- __ Прогресс общий -->
                <AppProgressBar
                    :progress="40"
                    :width="render.progressTotal.width"
                />
                <!--<AppLabelTSWrapper :arg="sewingDay" :render-object="render.progressTotal"/>-->

                <!-- __ Опережение / отставание -->
                <AppLabelTSWrapper :arg="sewingDay" :render-object="render.progressDelta"/>

                <!-- __ Комментарий -->
                <AppLabelTSWrapper :arg="sewingDay" :render-object="render.comment"/>

            </div>

            <!-- __ Содержимое СЗ -->
            <div v-if="!sewingDay.collapsed" class="ml-[34px]">

                <!-- __ Персонал -->

                <!-- __ СЗ -->
                <div class="my-2">
                    <!-- __ Шапка СЗ -->
                    <ExecuteTaskHeader
                        :fields-width="sewingTaskFieldsWidth"
                    />

                    <!-- __ Сами СЗ -->
                    <div v-for="sewingTask of sewingDay.sewing_tasks" :key="sewingTask.id" class=" bg-green-100">
                        <ExecuteTask
                            :fields-width="sewingTaskFieldsWidth"
                            :sewing-task="sewingTask"
                        />
                    </div>
                </div>

            </div>

        </div>
    </div>
</template>


<script lang="ts" setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { storeToRefs } from 'pinia'

import type { IRenderData, ISewingDay } from '@/types'

import { useSewingStore } from '@/stores/SewingStore.ts'

import { SEWING_TASK_STATUSES } from '@/app/constants/sewing.ts'

import {
    getSewingDates, unionDatesWithSewingTasks
} from '@/app/helpers/manufacture/helpers_sewing.ts'
import { formatDateInFullFormat, getDayOfWeek, isHoliday, isToday } from '@/app/helpers/helpers_date'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import AppLabelMultilineTSWrapper
    from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'
import ExecuteTask
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecuteTask.vue'
import ExecuteTaskHeader
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecuteTaskHeader.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'

// import AppInputTextTSWrapper from '@/components/dashboard/manufacture/cells/components/AppInputTextTSWrapper.vue'
// import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
// import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
// import AppRGBPickerModalTS from '@/components/ui/pickers/AppRGBPickerModalTS.vue'
// import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'


const DEBUG     = true
const isLoading = ref(false)

const sewingStore = useSewingStore()

const {
          globalSewingTasksPending,        // __ Все задания (Global State)
      } = storeToRefs(sewingStore)

// __ Определяем переменные
const sewingDays = ref<ISewingDay[]>([])


// __ Получаем объект рендера
const renderSewingDays = computed<ISewingDay[]>(() => {
    return sewingDays.value
})


// __ Объект отображения данных
const DEFAULT_HEIGHT   = 'h-[30px]'
const COLLAPSED_WIDTH  = 'w-[30px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'mini'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'

const render: IRenderData = reactive({
    collapsedUp:   {
        id:             () => 'collapsed-up-search',
        header:         ['▲', ''],
        width:          COLLAPSED_WIDTH,
        height:         'h-[24px]',
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍collapsed...',
        data:           () => '▲',
        class:          'cursor-pointer',
    },
    collapsedDown: {
        id:             () => 'collapsed-down-search',
        header:         ['▼', ''],
        width:          COLLAPSED_WIDTH,
        height:         'h-[24px]',
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍collapsed...',
        data:           () => '▼',
        class:          'cursor-pointer',
    },
    collapsed:     {
        id:             () => 'collapsed-down-search',
        header:         ['▲', ''],
        width:          COLLAPSED_WIDTH,
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
        data:           (sewingDay: ISewingDay) => sewingDay.collapsed ? '▲' : '▼',
    },
    id:            {
        id:             () => 'id-search',
        header:         ['ID', ''],
        width:          'w-[50px]',
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍id...',
        data:           (sewingDay: ISewingDay) => sewingDay.id.toString(),
    },
    date:          {
        id:             () => 'date-search',
        header:         ['Дата', 'производства'],
        width:          'w-[218px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Дата...',
        data:           (sewingDay: ISewingDay) => formatDateInFullFormat(sewingDay.action_at) + ` (${getDayOfWeek(sewingDay.action_at)})`
    },
    start_at:      {
        id:             () => 'start-at-search',
        header:         ['Старт', ''],
        width:          'w-[90px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Старт...',
        data:           (sewingDay: ISewingDay) => '07ч. 00м. 00с.',
    },
    finish_at:     {
        id:             () => 'finish-at-search',
        header:         ['Финиш', ''],
        width:          'w-[90px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Финиш...',
        data:           (sewingDay: ISewingDay) => '16ч. 00м. 00с.',
    },
    duration:      {
        id:             () => 'duration-search',
        header:         ['Продолжи-', 'тельность СЗ'],
        width:          'w-[143px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Дата...',
        data:           (sewingDay: ISewingDay) => '07ч. 59м. 59с.',
    },

    progressTotal: {
        id:             () => 'progress-total-search',
        header:         ['Прогресс выполнения от', 'общего времени СЗ'],
        width:          'w-[265px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Дата...',
        data:           (sewingDay: ISewingDay) => sewingDay.comment ?? '',
    },
    progressDelta: {
        id:             () => 'progress-delta-search',
        header:         ['Опережение или', 'отставание'],  // __ (Темп выполнения СЗ, остаток смены - остаток задания) Опережение или отставание (отношение оставшегося времени смены к оставшемуся времени СЗ)
        width:          'w-[150px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Дата...',
        data:           (sewingDay: ISewingDay) => sewingDay.comment ?? '',
    },

    comment: {
        id:             () => 'comment-search',
        header:         ['Комментарий к', 'производственному дню'],
        width:          'w-[312px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (sewingDay: ISewingDay) => dateType(sewingDay),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Комментарий...',
        data:           (sewingDay: ISewingDay) => sewingDay.comment ?? '',
    },
})

// __ Ширина полей для вывода СЗ
const sewingTaskFieldsWidth = {
    collapsed:     COLLAPSED_WIDTH,
    id:            'w-[30px]',
    position:      'w-[30px]',
    client:        'w-[190px]',
    order_no:      'w-[50px]',
    status:        'w-[90px]',
    progressTotal: 'w-[265px]',
    load_at:       'w-[143px]',
    comment:       'w-[466px]',
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


const expandAll   = () => sewingDays.value.forEach(sewingDay => sewingDay.collapsed = false)
const collapseAll = () => sewingDays.value.forEach(sewingDay => sewingDay.collapsed = true)

// __ Получаем производственные дни
const getSewingDays = async () => {
    const dates      = getSewingDates(globalSewingTasksPending.value)                    // __ Получаем даты из СЗ
    sewingDays.value = await sewingStore.getSewingDaysByDates(dates)                // __ Получаем дни по этим датам
}

// __ Добавляем свернутость
const addCollapsed = () => {
    sewingDays.value = sewingDays.value.map(day => {
        return {
            ...day,
            collapsed: true,
            // Проверяем, есть ли задачи, и проходим по ним
            sewing_tasks: day.sewing_tasks.map(task => ({
                ...task,
                collapsed: true
            }))
        }
    })
}


onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            // __ Получаем SewingTasks по статусу и записываем в глобальную переменную в SewingStore
            await sewingStore.getSewingTasksByStatus([
                SEWING_TASK_STATUSES.PENDING.ID,
                SEWING_TASK_STATUSES.RUNNING.ID,
            ])

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
        undefined,
        // false,
    )

    isLoading.value = false
})
</script>


<style scoped>

</style>
