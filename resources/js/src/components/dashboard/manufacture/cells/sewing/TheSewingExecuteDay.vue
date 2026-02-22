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
                    :text="['Длительность:', elapsedTime === -1 ? '' : formatTimeWithLeadingZeros(elapsedTime)]"
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
                    :text="`${formatTimeWithLeadingZeros(statistics.time.finished)} / ${formatTimeWithLeadingZeros(statistics.time.total)}`"
                    height="h-[50px]"
                    text-size="mini"
                    width="w-[200px]"
                />

                <!-- __ Опережение/Отставание -->
                <DeviationBar
                    :deviation="statistics.time.unfinished !== 0 ? deviation/statistics.time.unfinished * 100 : 0"
                    :text="deviationText"
                    height="h-[50px]"
                    text-size="mini"
                    width="w-[200px]"
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
                        @divide-line="divideLine"
                    />
                </div>

            </template>
        </div>


    </template>

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
import { useSewingStore } from '@/stores/SewingStore.ts'


import { computed, onMounted, onUnmounted, reactive, ref, watch } from 'vue'
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import {
    SEWING_TASK_DRAFT, SEWING_TASK_STATUSES, START_SHIFT_TIME, TOTAL_SHIFT_DURATION
} from '@/app/constants/sewing.ts'
import {
    getCoverSizeString, getExecuteTaskStatustics, getSewingTaskModelCoverName,
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
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'

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

const now      = ref(0)
let timer: any = null

// __ Очистка таймера
const clearTimer = () => timer && clearInterval(timer)

// __ Группа Старта СЗ
const startGroupShow     = computed(() => !tasksBeforeCurrentDay.value.length)
// const everyTaskIsPending = computed(() => sewingDay.value?.sewing_tasks.every(task => task.current_status.id === SEWING_TASK_STATUSES.PENDING.ID))
const isSewingDayStarted = computed(() => sewingDay.value?.start_at && !sewingDay.value?.finish_at)
const startLabelTitle    = computed(() => !isSewingDayStarted.value ? ['Начать', 'выполнение'] : ['Закончить', 'выполнение'])
const startLabelType     = computed(() => !isSewingDayStarted.value ? 'warning' : 'orange')

// __ Время выполнения
const startTime   = computed(() => sewingDay.value?.start_at ? new Date(sewingDay.value.start_at).getTime() / 1000 : null)
const finishTime  = computed(() => sewingDay.value?.finish_at ? new Date(sewingDay.value.finish_at).getTime() / 1000 : null)
const elapsedTime = computed(() => {

    if (!now.value) return -1

    if (!startTime.value) {
        clearTimer()
        return 0
    }

    if (startTime.value && !finishTime.value) {
        return round(now.value - startTime.value)
    }

    if (startTime.value && finishTime.value) {
        clearTimer()
        return round(finishTime.value - startTime.value)
    }

    return -1
})


// __ Опережение / Отставание
const deviation = computed(() => {

    if (now.value && startTime.value && !finishTime.value) {

        // __ Находим время окончания смены
        const endShiftTime     = new Date(startTime.value * 1000)
        const [hours, minutes] = START_SHIFT_TIME.split(':')
        endShiftTime.setHours(Number(hours), Number(minutes) + TOTAL_SHIFT_DURATION * 60, 0, 0)

        // __ Оставшееся время до окончания смены в секундах
        const remainingTime = endShiftTime.getTime() / 1000 - now.value
        //
        // console.log('unfinished: ', formatTimeWithLeadingZeros(statistics.value.time.unfinished))
        // console.log('remainingTime: ', formatTimeWithLeadingZeros(round(remainingTime)))
        // console.log('deviation: ', formatTimeWithLeadingZeros(round(remainingTime - statistics.value.time.unfinished)))

        // __ Опережение или отставание
        if (statistics.value.time.unfinished === 0) return 0
        return round(remainingTime - statistics.value.time.unfinished)
    }

    return 0
})

// __ Текст для опережения/отставания
const deviationText = computed(() => {
    if (deviation.value === 0) {
        return 'В графике'
    }
    return deviation.value > 0 ? 'ОПЕРЕЖЕНИЕ' : 'ОТСТАВАНИЕ' + ' ' + formatTimeWithLeadingZeros(Math.abs(deviation.value))
})


// __ Организация Tabs
const infoTabPosition      = -2
const personalTabPosition  = -1
const UNION_TASKS_POSITION = 1000

const activeTabPosition = ref(infoTabPosition)

const tabs = ref<ITab[]>([])

const setTabs = () => {
    tabs.value = []
    tabs.value.push({
        show:       true,
        label:      ['Инфо', ''],
        position:   infoTabPosition,
        type:       'dark',
        typeActive: 'info',
        task:       null,
    },)
    tabs.value.push({
        show:       true,
        label:      ['Персонал', ''],
        position:   personalTabPosition,
        type:       'dark',
        typeActive: 'warning',
        task:       null,
    },)
    sewingDay.value?.sewing_tasks.forEach(task => tabs.value.push({
        show:       true,
        label:      [`${task.position}. ${task.order.client.short_name} №${task.order.order_no_num}`, formatDateInFullFormat(task.order.load_at, true)],
        position:   task.position,
        type:       'dark',
        typeActive: 'primary',
        task,
    }))
    tabs.value.push({
        show:       true,
        label:      ['Объединение', 'СЗ'],
        position:   UNION_TASKS_POSITION,
        type:       'dark',
        typeActive: 'orange',
        task:       allSewingTasksLinesUnion.value,
    })
    tabs.value.sort((a, b) => a.position - b.position)
}

// console.log('tabs: ', tabs.value)


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

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

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Старт / Стоп                   !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Обработка начала выполнения СЗ
const handleStartAction = async () => {

    if (!isSewingDayStarted.value) {
        // __ Старт.

        // __ 1. Проверка, есть ли не выполненные или выполняемые задания за предыдущий период (до текущего дня)
        // __ (Избыточно при переносе невыполненных или выполняемых заданий на следующий день на сервере)
        // __ 2. Если есть, то показываем предупреждение
        // __ 3. Если нет, то запускаем выполнение

        const startedDay          = await sewingStore.startSewingDay(sewingDay.value!.id)
        console.log('Start: returnedData: ', startedDay)

        if (checkCRUD(startedDay)) {
            sewingDay.value!.start_at = startedDay.start_at
        } else {
            await showError()
            return
        }

    } else {
        // __ Стоп.

        // __ 1. Проверяем, заполнен ли персонал
        // __ 2. Проверяем, есть ли ответственный
        // __ 3. Проверяем по каждому СЗ, выполнены ли все задания
        // __ 4. Закрываем выполнение

        if (sewingDay.value!.workers.length === 0) {
            await showError('Необходимо заполнить персонал!')
            return
        }

        if (!sewingDay.value!.responsible || !sewingDay.value!.responsible.id) {
            await showError(['Не выбран ответственный сотрудник', 'за выполнение смены!'])
            return
        }

        for (const task of sewingDay.value!.sewing_tasks) {
            for (const line of task.sewing_lines) {
                if (!line.finished_at || !line.false_at) {
                    await showError([
                        'Нет отметки выполнения!',
                        `СЗ: ${task.order.client.short_name} №${task.order.order_no_num}`,
                        `Строка: ${getCoverSizeString(line)} ${getSewingTaskModelCoverName(line)} ${line.amount} шт.`,
                    ])
                    return
                }
            }
        }

        const finishedDay = await sewingStore.finishSewingDay(sewingDay.value!.id)
        console.log('Finish: returnedData: ', finishedDay)

    }


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
        if (sewingDay.value!.responsible && sewingDay.value!.responsible.id === worker.id) {
            sewingDay.value!.responsible = null
        }
    }
}

// __ Добавляем Ответственного
const addResponsible = (worker: ISewingDayWorker) => {
    sewingDay.value!.responsible = worker
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---   Функционал для выполнения дня Записи        !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Устанавливаем статус Выполнено SewingLines
const setFinishStatus = async (sewingLinesIds: number[]) => {
    const result = (await sewingStore.setSewingTaskLinesDone(sewingLinesIds) as ISewingTaskLine[])

    if (checkCRUD(result)) {
        result.forEach(line => {
            const findLine = allSewingTasksLinesUnion.value.sewing_lines.find(l => l.id === line.id)
            if (findLine) {
                findLine.finished_at = line.finished_at
            }
        })
    } else {
        await showError()
    }
}

// __ Устанавливаем статус Выполнено SewingLines
const setFalseStatus = async (sewingLinesIds: number[], falseReason: string) => {
    const result = (await sewingStore.setSewingTaskLinesFalse(sewingLinesIds, falseReason) as ISewingTaskLine[])

    if (checkCRUD(result)) {
        result.forEach(line => {
            const findLine = allSewingTasksLinesUnion.value.sewing_lines.find(l => l.id === line.id)
            if (findLine) {
                findLine.false_at     = line.false_at
                findLine.false_reason = line.false_reason
            }
        })
    } else {
        await showError()
    }
}

// __ Сбрасываем статус SewingLines
const resetStatus = async (sewingLinesIds: number[]) => {
    const result = (await sewingStore.setSewingTaskLinesReset(sewingLinesIds) as ISewingTaskLine[])

    if (checkCRUD(result)) {
        result.forEach(line => {
            const findLine = allSewingTasksLinesUnion.value.sewing_lines.find(l => l.id === line.id)
            if (findLine) {
                findLine.finished_at  = line.finished_at
                findLine.false_at     = line.false_at
                findLine.false_reason = line.false_reason
            }
        })
    } else {
        await showError()
    }
}

// __ Разделяем строку
const divideLine = async (taskId: number, sewingLineId: number, range: { take: number, keep: number }) => {

    const findTask = sewingDay.value!.sewing_tasks.find(task => task.id === taskId)
    if (!findTask) {
        return // страховка
    }

    const dividerElementIndex = findTask.sewing_lines.findIndex(line => line.id === sewingLineId)
    let newSewingLine         = { ...findTask.sewing_lines[dividerElementIndex] }           // __ Копируем объект
    newSewingLine.id          = 0                                                           // __ Устанавливаем новый ID
    newSewingLine.position    = round(newSewingLine.position + 0.1, 1)    // __ Делаем новую строку ниже текущей позицию с шагом 0.1 (всего 9 разбиений)

    newSewingLine.amount                              = range.take
    findTask.sewing_lines[dividerElementIndex].amount = range.keep

    // __ Вставляем новую строку
    findTask.sewing_lines.splice(dividerElementIndex + 1, 0, newSewingLine)
    findTask.sewing_lines.sort((a, b) => a.position - b.position) // !!! Обязательно

    const result = await sewingStore.divideLineInSewingTaskPending(findTask)
    if (!checkCRUD(result)) {
        await showError()
    } else {
        return
    }
}

// __ Подготавливаем данные для отображения
const prepareData = () => {
    if (!sewingDay.value) return

    // __ Фильтруем задания
    sewingDay.value!.sewing_tasks = globalSewingTasks.value
        .filter(task =>
            task.current_status.id === SEWING_TASK_STATUSES.PENDING.ID ||
            task.current_status.id === SEWING_TASK_STATUSES.RUNNING.ID)
        .sort((a, b) => a.position - b.position)

    // __ Формируем объединение всех SewingTaskLines
    allSewingTasksLinesUnion.value.sewing_lines = []
    sewingDay.value!.sewing_tasks.forEach(task => task.sewing_lines.forEach(line => allSewingTasksLinesUnion.value.sewing_lines.push(line)))

    allSewingTasksLinesUnion.value.position = UNION_TASKS_POSITION  // __ Позиция объединения всех строк
}


watch(() => globalSewingTasks.value, () => {
    prepareData()
    setTabs()
}, { deep: true })


onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await router.isReady().then(() => {
                executeDate = route.params.date as unknown as string
            })


            // __ Здесь получаем данные по СЗ, которые есть со статусом "Выполняется" или "Выполнено" до текущего дня
            // __ По идее эта логика избыточна, потому что все незавершенные или выполняющиеся сменные задания должны
            // __ автоматом через middleware переноситься на следующий день
            const [dayData, tasksBefore, _] = await Promise.all([
                sewingStore.getSewingDayByDateAndChange(executeDate),
                sewingStore.getSewingTasksByStatusBeforeDate(executeDate, [SEWING_TASK_STATUSES.PENDING.ID, SEWING_TASK_STATUSES.RUNNING.ID]),
                sewingStore.getSewingTasks({ start: executeDate, end: executeDate })
            ])

            tasksBeforeCurrentDay.value = tasksBefore
            sewingDay.value             = dayData

            prepareData()

            // __ Запускаем счетчик
            timer = setInterval(() => {
                now.value = new Date().getTime() / 1000
                // console.log(now.value)
            }, 1000)

            setTabs()


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
