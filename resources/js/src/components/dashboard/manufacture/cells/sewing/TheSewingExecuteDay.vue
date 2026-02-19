<template>
    <template v-if="!isLoading">
        <div class="flex m-2">

            <template v-if="startGroupShow">

                <!-- __ Начать выполнение -->
                <AppLabelMultiLineTS
                    :text="startLabelTitle"
                    :type="startLabelType"
                    align="center"
                    class="start-group cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    width="w-[150px]"
                    @click="handleStartAction"
                />

                <!-- __ Начало выполнения -->
                <AppLabelMultiLineTS
                    v-if="sewingDay?.start_at"
                    :text="['Старт:', formatTimeInFullFormat(sewingDay?.start_at)]"
                    align="center"
                    class="start-group"
                    rounded="4"
                    text-size="mini"
                    type="primary"
                    width="w-[150px]"
                />

                <!-- __ Окончание выполнения -->
                <AppLabelMultiLineTS
                    v-if="sewingDay?.finish_at"
                    :text="['Финиш:', formatTimeInFullFormat(sewingDay?.finish_at)]"
                    align="center"
                    class="start-group"
                    rounded="4"
                    text-size="mini"
                    type="primary"
                    width="w-[150px]"
                />

                <!-- __ Длительность -->
                <AppLabelMultiLineTS
                    v-if="sewingDay?.start_at"
                    :text="['Длительность:', elapsedTime]"
                    align="center"
                    class="start-group"
                    rounded="4"
                    text-size="mini"
                    type="warning"
                    width="w-[150px]"
                />

                <!-- __ Прогресс общий -->
                <AppProgressBar
                    :progress="statistics.time.finished / statistics.time.total * 100"
                    :width="'w-[150px]'"
                    height="h-[50px]"
                />


                <DeviationBar
                    :deviation="-25"
                    :width="'w-[150px]'"
                    height="h-[50px]"
                />


                <!-- __ Опережение/Отставание  !!! Добавить type в зависимости от динамики -->
                <AppLabelMultiLineTS
                    :text="['Опережение:', 0 + '']"
                    align="center"
                    class="start-group"
                    rounded="4"
                    text-size="mini"
                    type="success"
                    width="w-[150px]"
                />

            </template>

            <!-- __ Комментарий к дню -->
            <AppLabelTS
                :text="sewingDay?.comment ?? ''"
                align="left"
                class="start-group"
                height="h-[50px]"
                rounded="4"
                text-size="mini"
                type="indigo"
                width="min-w-[400px]"
            />

        </div>

        <!-- __ Табы -->
        <div class="flex m-2">
            <div v-for="tab of tabs" :key="tab.position">

                <!-- __ Таб: TODO: !!! Доделать крестики и галочки на выполненных задачах !!!   -->
                <AppLabelMultiLineTS
                    v-if="tab.show"
                    :text="tab.label"
                    :type="activeTabPosition === tab.position ? tab.typeActive : tab.type"
                    align="center"
                    class="start-group cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    width="w-[150px]"
                    @click="activeTabPosition = tab.position"
                />

            </div>
        </div>

        <div class="m-2">
            <template v-if="activeTabPosition === infoTabPosition">

                <!-- __ Общая инфа -->
                <div class="ml-8">
                    <ExecuteDayInfo
                        :sewing-day="sewingDay!"
                    />
                </div>


            </template>
            <template v-else-if="activeTabPosition === personalTabPosition">

                <!-- __ Персонал -->
                <div class="ml-8">
                    <ExecutePersonal
                        :sewing-day="sewingDay!"
                        @add-worker="addWorker"
                        @remove-worker="removeWorker"
                        @add-responsible="addResponsible"
                    />
                </div>

            </template>
            <template v-else>

                <!-- __ Сами задачи -->
                <div class="">
                    <ExecuteDayTask
                        :sewing-task="tabs.find(tab => tab.position === activeTabPosition)!.task!"
                        @set-finish-status="setFinishStatus"
                        @set-false-status="setFalseStatus"
                        @reset-status="resetStatus"
                    />
                </div>

            </template>
        </div>


    </template>
</template>

<script lang="ts" setup>
import { useSewingStore } from '@/stores/SewingStore.ts'


import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { SEWING_TASK_DRAFT, SEWING_TASK_STATUSES } from '@/app/constants/sewing.ts'
import {
    getExecuteTaskStatustics, getSewingTaskAmountAndTime, unionDatesWithSewingTasks
} from '@/app/helpers/manufacture/helpers_sewing.ts'
import { useRouter, useRoute, onBeforeRouteLeave, onBeforeRouteUpdate } from 'vue-router'
import { storeToRefs } from 'pinia'
import type { IColorTypes, ISewingDay, ISewingDayWorker, ISewingTask, ISewingTaskLine } from '@/types'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import { formatDateInFullFormat, formatTimeInFullFormat, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'
import { round } from '@/app/helpers/helpers_lib.ts'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import ExecutePersonal
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecutePersonal.vue'
import ExecuteDayInfo
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute_day/ExecuteDayInfo.vue'
import ExecuteDayTask
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute_day/ExecuteDayTask.vue'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import DeviationBar from '@/components/ui/bars/DeviationBar.vue'

interface ITab {
    show: boolean
    label: string[]
    position: number
    type: IColorTypes
    typeActive: IColorTypes
    task: ISewingTask | null
}


const sewingStore = useSewingStore()
const router      = useRouter()
const route       = useRoute()

const { globalSewingTasks } = storeToRefs(sewingStore)


const DEBUG     = true
const isLoading = ref(false)

let executeDate: string

// __ Переменные
const sewingDay                = ref<ISewingDay | null>(null)
const tasksBeforeCurrentDay    = ref<ISewingTask[]>([])
const allSewingTasksLinesUnion = ref<ISewingTask>(SEWING_TASK_DRAFT) // __ Переменная для объединения всех SewingTaskLines

const statistics = computed(() => getExecuteTaskStatustics(allSewingTasksLinesUnion.value))

// const totalDayAmountAndTime = computed(() => getSewingTaskAmountAndTime(allSewingTasksLinesUnion.value))

// const totalDayAmountAndTimeObj = computed(() => {
//     if (!sewingDay.value) {
//         return {}
//     }
//     const tempArray: ISewingTaskLine[] = []
//     sewingDay.value!.sewing_tasks.forEach(task => task.sewing_lines.forEach(line => tempArray.push(line)))
//
//     console.log('1111')
//     return getSewingTaskAmountAndTime(tempArray)
// })
//
// const finishedDayAmountAndTimeObj


const now      = ref(0)
let timer: any = null

// __ Очистка таймера
const clearTimer = () => timer && clearInterval(timer)


const startGroupShow     = computed(() => !tasksBeforeCurrentDay.value.length)
const everyTaskIsPending = computed(() => sewingDay.value?.sewing_tasks.every(task => task.current_status.id === SEWING_TASK_STATUSES.PENDING.ID))
const startLabelTitle    = computed(() => everyTaskIsPending.value ? ['Начать', 'выполнение'] : ['Закончить', 'выполнение'])
const startLabelType     = computed(() => everyTaskIsPending.value ? 'warning' : 'orange')

// __ Время выполнения
const startTime   = computed(() => sewingDay.value?.start_at ? new Date(sewingDay.value.start_at).getTime() / 1000 : null)
const finishTime  = computed(() => sewingDay.value?.finish_at ? new Date(sewingDay.value.finish_at).getTime() / 1000 : null)
const elapsedTime = computed(() => {

    if (!now.value) return ''

    if (!startTime.value) {
        clearTimer()
        return formatTimeWithLeadingZeros(0)
    }

    if (startTime.value && !finishTime.value) {
        return formatTimeWithLeadingZeros(round(now.value - startTime.value))
    }

    if (startTime.value && finishTime.value) {
        clearTimer()
        return formatTimeWithLeadingZeros(round(finishTime.value - startTime.value))
    }

    return ''
})


// __ Организация Tabs
const infoTabPosition     = -2
const personalTabPosition = -1

const activeTabPosition = ref(infoTabPosition)

const tabs: ITab[] = []

const setTabs = () => {
    tabs.push({
        show:       true,
        label:      ['Инфо', ''],
        position:   infoTabPosition,
        type:       'dark',
        typeActive: 'info',
        task:       null,
    },)
    tabs.push({
        show:       true,
        label:      ['Персонал', ''],
        position:   personalTabPosition,
        type:       'dark',
        typeActive: 'warning',
        task:       null,
    },)

    sewingDay.value?.sewing_tasks.forEach(task => tabs.push({
        show:       true,
        label:      [`${task.position}. ${task.order.client.short_name} №${task.order.order_no_num}`, formatDateInFullFormat(task.order.load_at, true)],
        position:   task.position,
        type:       'dark',
        typeActive: 'primary',
        task,
    }))
}


// __ Обработка начала выполнения СЗ
const handleStartAction = async () => {
    // __ 1. Проверка, есть ли не выполненные или выполняемые задания за предыдущий период (до текущего дня)
    // __ 2. Если есть, то показываем предупреждение
    // __ 3. Если нет, то запускаем выполнение

    const startedDay          = await sewingStore.startSewingDay(sewingDay.value!.id)
    sewingDay.value!.start_at = startedDay.start_at

    console.log('returnedData: ', startedDay)

}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Персонал                       !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Добавляем работника
const addWorker = (worker: ISewingDayWorker) => {
    const existWorker = sewingDay.value!.workers.find(w => w.id === worker.id)
    if (!existWorker) {
        sewingDay.value!.workers.push(worker)
    }
}

// __ Удаляем работника
const removeWorker = (worker: ISewingDayWorker) => {
    const findIndex = sewingDay.value!.workers.findIndex(w => w.id === worker.id)
    if (findIndex !== -1) {
        sewingDay.value!.workers.splice(findIndex, 1)
    }
}

// __ Добавляем Ответственного
const addResponsible = (worker: ISewingDayWorker) => {
    sewingDay.value!.responsible = worker
}


// __ Устанавливаем статус Выполнено SewingLines
const setFinishStatus = async (sewingLinesIds: number[]) => {
    const result = (await sewingStore.setSewingTaskLinesDone(sewingLinesIds) as ISewingTaskLine[])

    console.log('result: ', result)
    if (checkCRUD(result)) {
        result.forEach(line => {
            const findLine = allSewingTasksLinesUnion.value.sewing_lines.find(l => l.id === line.id)
            if (findLine) {
                findLine.finished_at = line.finished_at
            }
        })
    }
}

// __ Устанавливаем статус Выполнено SewingLines
const setFalseStatus = async (sewingLinesIds: number[], falseReason: string) => {
    const result = (await sewingStore.setSewingTaskLinesFalse(sewingLinesIds, falseReason) as ISewingTaskLine[])

    console.log('result: ', result)
    if (checkCRUD(result)) {
        result.forEach(line => {
            const findLine = allSewingTasksLinesUnion.value.sewing_lines.find(l => l.id === line.id)
            if (findLine) {
                findLine.false_at     = line.false_at
                findLine.false_reason = line.false_reason
            }
        })
    }
}


// __ Сбрасываем статус SewingLines
const resetStatus = async (sewingLinesIds: number[]) => {
    const result = (await sewingStore.setSewingTaskLinesReset(sewingLinesIds) as ISewingTaskLine[])

    console.log('result: ', result)
    if (checkCRUD(result)) {
        result.forEach(line => {
            const findLine = allSewingTasksLinesUnion.value.sewing_lines.find(l => l.id === line.id)
            if (findLine) {
                findLine.finished_at  = line.finished_at
                findLine.false_at     = line.false_at
                findLine.false_reason = line.false_reason
            }
        })
    }
}


onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await router.isReady().then(() => {
                executeDate = route.params.date as unknown as string
                // editMode.value = route.meta.mode === 'edit' // определяем режим работы формы (редактирование или создание)
            })

            const [dayData, tasksBefore, _] = await Promise.all([
                sewingStore.getSewingDayByDateAndChange(executeDate),
                sewingStore.getSewingTasksByStatusBeforeDate(executeDate, [SEWING_TASK_STATUSES.PENDING.ID, SEWING_TASK_STATUSES.RUNNING.ID]),
                sewingStore.getSewingTasks({ start: executeDate, end: executeDate })
            ])

            sewingDay.value             = dayData
            tasksBeforeCurrentDay.value = tasksBefore

            // __ Фильтруем задания
            sewingDay.value!.sewing_tasks = globalSewingTasks.value
                .filter(task =>
                    task.current_status.id === SEWING_TASK_STATUSES.PENDING.ID ||
                    task.current_status.id === SEWING_TASK_STATUSES.RUNNING.ID)

            // __ Формируем объединение всех SewingTaskLines
            allSewingTasksLinesUnion.value.sewing_lines = []
            sewingDay.value!.sewing_tasks.forEach(task => task.sewing_lines.forEach(line => allSewingTasksLinesUnion.value.sewing_lines.push(line)))


            // __ Запускаем счетчик
            timer = setInterval(() => {
                now.value = new Date().getTime() / 1000
                // console.log(now.value)
            }, 1000)

            setTabs()

            const statistic = getExecuteTaskStatustics(allSewingTasksLinesUnion.value)
            console.log('statistic: ', statistic)


            console.log('executeDate: ', executeDate)
            console.log('globalSewingTasks: ', globalSewingTasks.value)
            console.log('sewingDay: ', sewingDay.value)
            console.log('tasksBeforeCurrentDay: ', tasksBeforeCurrentDay.value)

            // // __ Получаем SewingTasks по статусу и записываем в глобальную переменную в SewingStore
            // await sewingStore.getSewingTasksByStatus([
            //     SEWING_TASK_STATUSES.PENDING.ID,
            //     SEWING_TASK_STATUSES.RUNNING.ID,
            // ])
            //
            // // __ Получаем дни
            // await getSewingDays()
            //
            // // __ Объединяем задания с днями
            // unionDatesWithSewingTasks(sewingDays.value, globalSewingTasksPending.value)
            //
            // // __ Добавляем свернутость
            // addCollapsed()
            //
            // if (DEBUG) console.log('globalSewingTasksPending:', globalSewingTasksPending.value)
            // if (DEBUG) console.log('sewingDays:', sewingDays.value)
            // if (DEBUG) console.log('renderSewingDays:', renderSewingDays.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})

onUnmounted(() => {
    clearTimer()    // __ Очищаем таймер, чтобы не было утечек памяти
})

onBeforeRouteLeave(() => {
    clearTimer()
})
onBeforeRouteUpdate(() => {
    clearTimer()
})


</script>

<style scoped>
.start-group {
    @apply shadow-[0_0_15px_rgba(79,70,229,0.4)];
}
</style>
