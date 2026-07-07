<template>
    <template v-if="!isLoading">
        <div class="flex m-2">


            <template v-if="isStartAvailable">
                <!-- __ Начать/Закончить выполнение -->
                <AppLabelMultiLineTS
                    :text="startLabelTitle"
                    :type="startLabelType"
                    :width="MENU_LABEL_WIDTH"
                    align="center"
                    class="start-group cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    @click="handleStartAction"
                />


                <!-- __ Остановить для добавления новых СЗ -->
                <AppLabelMultiLineTS
                    :text="addNewTasksLabelTitle"
                    :type="addNewTasksLabelType"
                    :width="MENU_LABEL_WIDTH"
                    align="center"
                    class="start-group cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    @click="handleReadyAction"
                />


                <!-- __ Начало выполнения -->
                <AppLabelMultiLineTS
                    v-if="cuttingDay?.start_at"
                    :text="['🕑Старт:', formatTimeInFullFormat(cuttingDay?.start_at)]"
                    :width="MENU_LABEL_WIDTH"
                    align="center"
                    class="start-group"
                    rounded="4"
                    text-size="mini"
                    type="primary"
                />

                <!-- __ Окончание выполнения -->
                <AppLabelMultiLineTS
                    v-if="cuttingDay?.finish_at"
                    :text="['🕑Финиш:', formatTimeInFullFormat(cuttingDay?.finish_at)]"
                    :width="MENU_LABEL_WIDTH"
                    align="center"
                    class="start-group"
                    rounded="4"
                    text-size="mini"
                    type="primary"
                />

                <!-- __ Длительность -->
                <AppLabelMultiLineTS
                    v-if="cuttingDay?.start_at"
                    :text="['🕑Длительность:', elapsedTime === -1 ? '' : formatTimeWithLeadingZeros(elapsedTime)]"
                    :width="MENU_LABEL_WIDTH"
                    align="center"
                    class="start-group"
                    rounded="4"
                    text-size="mini"
                    type="warning"
                />

                <!-- __ Прогресс общий -->
                <AppProgressBar
                    :progress="(statistics.time.finished / statistics.time.total) * 100"
                    :text="`${formatTimeWithLeadingZeros(statistics.time.finished)} / ${formatTimeWithLeadingZeros(statistics.time.total)}`"
                    height="h-[50px]"
                    text-size="mini"
                    width="w-[200px]"
                />

                <!-- __ Опережение/Отставание -->
                <DeviationBar
                    :deviation="statistics.time.unfinished !== 0 ? (deviation / statistics.time.unfinished) * 100 : 0"
                    :text="deviationText"
                    height="h-[50px]"
                    text-size="mini"
                    width="w-[200px]"
                />
            </template>

            <!-- __ Комментарий к дню -->
            <AppLabelTS
                :text="cuttingDay?.comment ?? ''"
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
            <div
                v-for="tab of tabs"
                :key="tab.position"
            >
                <!-- __ Таб: TODO: !!! Доделать крестики и галочки на выполненных задачах !!!   -->
                <AppLabelMultiLineTS
                    v-if="tab.show"
                    :text="tab.label"
                    :type="activeTabPosition === tab.position ? tab.typeActive : tab.type"
                    :width="MENU_LABEL_WIDTH"
                    align="center"
                    class="start-group cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    @click="activeTabPosition = tab.position"
                />
            </div>
        </div>

        <div class="m-2">
            <template v-if="activeTabPosition === infoTabPosition">
                <!-- __ Общая инфа -->
                <div class="ml-8">
                    <ExecuteDayInfo :cutting-day="cuttingDay!"/>
                </div>
            </template>
            <template v-else-if="activeTabPosition === personalTabPosition">
                <!-- __ Персонал -->
                <div class="ml-8">
                    <ExecutePersonal
                        :can-edit="isStartAvailable "
                        :cutting-day="cuttingDay!"
                        @add-worker="addWorker"
                        @add-workers="addWorkers"
                        @remove-worker="removeWorker"
                        @add-responsible="addResponsible"
                    />
                </div>
            </template>
            <template v-else>
                <!-- __ Сами СЗ -->
                <div class="">
                    <ExecuteDayTask
                        :is-running="isCuttingDayStarted  && !isCuttingDayReadyForNewTasks"
                        :cutting-task="tabs.find(tab => tab.position === activeTabPosition)!.task!"
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
import type { IColorTypes, ICuttingDay, ICuttingDayWorker, ICuttingTask, ICuttingTaskLine } from '@/types'

import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter, useRoute, onBeforeRouteLeave, onBeforeRouteUpdate } from 'vue-router'

import { CUTTING_TASK_DRAFT, CUTTING_TASK_STATUSES, START_SHIFT_TIME, TOTAL_SHIFT_DURATION } from '@/app/constants/cutting.ts'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import { getCoverSizeString, getExecuteTaskStatistics, getCuttingTaskModelCoverName, getCuttingTaskTitle } from '@/app/helpers/manufacture/helpers_cutting.ts'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import { round } from '@/app/helpers/helpers_lib.ts'
import { formatDateInFullFormat, formatTimeInFullFormat, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import ExecutePersonal from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute/ExecutePersonal.vue'
import ExecuteDayInfo from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute_day/ExecuteDayInfo.vue'
import ExecuteDayTask from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute_day/ExecuteDayTask.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import DeviationBar from '@/components/ui/bars/DeviationBar.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'

interface ITab {
    show: boolean
    label: string[]
    position: number
    type: IColorTypes
    typeActive: IColorTypes
    task: ICuttingTask | null
}

const cuttingStore = useCuttingStore()
const router      = useRouter()
const route       = useRoute()

const { globalCuttingTasks } = storeToRefs(cuttingStore)

// __ Константы
// const DEBUG = true
const MENU_LABEL_WIDTH = 'w-[160px]'

const isLoading = ref(false)

let executeDate: string

// __ Переменные
const cuttingDay                = ref<ICuttingDay | null>(null)
const tasksBeforeCurrentDay    = ref<ICuttingTask[]>([])
const allCuttingTasksLinesUnion = ref<ICuttingTask>(CUTTING_TASK_DRAFT) // __ Переменная для объединения всех CuttingTaskLines

const statistics = computed(() => getExecuteTaskStatistics(allCuttingTasksLinesUnion.value))

const now                = ref(0)
let timer: number | null = null
// let timer: ReturnType<typeof setInterval> | undefined

// __ Очистка таймера
const clearTimer = () => timer && clearInterval(timer)

// __ Группа Старта СЗ
const isStartAvailable = computed(() => {
    // return true // __ Заглушка!!! !!!!!!!! TODO WARNING УБРАТЬ!
    // __ Проверяем, что дата выполнения меньше текущей
    // const today = new Date().toISOString().split('T')[0] // Получится '2026-05-16'
    const today   = new Date().toLocaleDateString('en-CA')// ().split('T')[0] // Получится '2026-05-16'
    const compare = cuttingDay.value ? cuttingDay.value.action_at.split(' ')[0] : today

    // console.log('today: ', today)
    // console.log('compare: ', compare)

    if (compare > today) {
        return false
    }


    return tasksBeforeCurrentDay.value.length === 0 && cuttingDay.value?.cutting_tasks.length !== 0
})
// const everyTaskIsPending = computed(() => cuttingDay.value?.cutting_tasks.every(task => task.current_status.id === CUTTING_TASK_STATUSES.PENDING.ID))


// __ #############################################
// __ Маяк запуска СЗ
const isCuttingDayStarted = computed(() => !!(cuttingDay.value?.start_at && !cuttingDay.value?.finish_at))

// __ Кнопка Начать/Закончить выполнение
const startLabelTitle = computed(() => {
    // console.log('cuttingDay: ', cuttingDay.value)
    // console.log('pendingTasksPresents: ', pendingTasksPresents.value)
    // console.log('runningTasksPresents: ', runningTasksPresents.value)

    // __ Есть Готовые к выполнению СЗ
    if (pendingTasksPresents.value) {
        if (!cuttingDay.value?.start_at) {
            return ['🚀 Начать', 'выполнение']
        } else if (cuttingDay.value?.start_at && cuttingDay.value?.finish_at) {
            return ['Продолжить', 'выполнение']
        }
    }
    return ['🏁 Закончить', 'выполнение']
})

const startLabelType = computed(() => (!isCuttingDayStarted.value ? 'warning' : 'orange'))
// __ #############################################

// __ #############################################
// __ Маяк остановки СЗ для добавления новых СЗ
const isCuttingDayReadyForNewTasks = computed(() => cuttingDay.value?.ready)

// __ Кнопка Остановить для добавления новых СЗ
const addNewTasksLabelTitle = computed(() => {
    return !isCuttingDayReadyForNewTasks.value ? ['Остановить для', 'добавления новых СЗ'] : ['Продолжить', 'выполнение']
})

const addNewTasksLabelType = computed(() => (!isCuttingDayReadyForNewTasks.value ? 'stone' : 'danger'))
// __ #############################################


// __ Есть ли СЗ со статусом Готово к выполнению
const pendingTasksPresents = computed(() =>
    cuttingDay.value?.cutting_tasks.some(task => task.current_status.id === CUTTING_TASK_STATUSES.PENDING.ID)
)

// __ Есть ли СЗ со статусом Выполняется
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const runningTasksPresents = computed(() =>
    cuttingDay.value?.cutting_tasks.some(task => task.current_status.id === CUTTING_TASK_STATUSES.RUNNING.ID)
)

// __ Время выполнения
const startTime  = computed(() => (cuttingDay.value?.start_at ? new Date(cuttingDay.value.start_at).getTime() / 1000 : null))
const finishTime = computed(() => (cuttingDay.value?.finish_at ? new Date(cuttingDay.value.finish_at).getTime() / 1000 : null))

const startPointTime = computed(() => {
    console.log(cuttingDay.value)

    if (!cuttingDay.value?.start_at) return null
    if (cuttingDay.value?.resume_at) return new Date(cuttingDay.value.resume_at).getTime() / 1000
    return new Date(cuttingDay.value.start_at).getTime() / 1000
})


const elapsedTime = computed(() => {
    // console.log(now.value)
    // console.log(startPointTime.value)
    // console.log(startPointTime.value)

    if (!now.value) return -1
    // if (!cuttingDay.value?.duration) return -1

    if (!startPointTime.value) {
        clearTimer()
        return 0
    }

    if (startPointTime.value && !finishTime.value) {
        return round(now.value - startPointTime.value) + (cuttingDay.value?.duration ?? 0)
    }

    if (startPointTime.value && finishTime.value) {
        clearTimer()
        return cuttingDay.value?.duration ?? 0
        // return round(finishTime.value - startPointTime.value) + (cuttingDay.value?.duration ?? 0)
    }


    // if (!startTime.value) {
    //     clearTimer()
    //     return 0
    // }
    //
    // if (startTime.value && !finishTime.value) {
    //     return round(now.value - startTime.value)
    // }
    //
    // if (startTime.value && finishTime.value) {
    //     clearTimer()
    //     return round(finishTime.value - startTime.value)
    // }

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
const UNION_TASKS_POSITION = 0

const activeTabPosition = ref(infoTabPosition)

const tabs = ref<ITab[]>([])

const setTabs = () => {
    tabs.value = []
    tabs.value.push({
        show      : true,
        label     : ['Инфо', ''],
        position  : infoTabPosition,
        type      : 'dark',
        typeActive: 'info',
        task      : null,
    })
    tabs.value.push({
        show      : true,
        label     : ['Персонал', ''],
        position  : personalTabPosition,
        type      : 'dark',
        typeActive: 'warning',
        task      : null,
    })
    cuttingDay.value?.cutting_tasks.forEach(task =>
        tabs.value.push({
            show      : true,
            label     : [
                `${task.position}. ${task.order.client.short_name} №${task.order.order_no_num}`,
                formatDateInFullFormat(task.order.load_at, true),
            ],
            position  : task.position,
            type      : 'dark',
            typeActive: 'primary',
            task,
        })
    )
    // console.log(allCuttingTasksLinesUnion.value)
    if (allCuttingTasksLinesUnion.value.cutting_lines.length !== 0) {
        tabs.value.push({
            show      : true,
            label     : ['Объединение', 'СЗ'],
            position  : UNION_TASKS_POSITION,
            type      : 'dark',
            typeActive: 'orange',
            task      : allCuttingTasksLinesUnion.value,
        })
    }
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
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

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
// !!! ---      Пауза для добавления новых СЗ            !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
const handleReadyAction = async () => {
    if (!isCuttingDayStarted.value) {
        return
    }

    if (!isCuttingDayReadyForNewTasks.value) {
        modalInfoText.value = ['Смена будет приостановлена', 'для добавления новых СЗ!']
        modalInfoType.value = 'primary'
        modalInfoMode.value = 'confirm'
        const answer        = await appModalAsyncMultiline.value!.show()

        if (!answer) return

        const readyDay = await cuttingStore.readySetCuttingDay(cuttingDay.value!.id)
        if (checkCRUD(readyDay)) {
            cuttingDay.value!.ready = readyDay.ready

        } else {
            await showError()
            return
        }
    } else {
        modalInfoText.value = ['Смена будет продолжена.', 'Для корректной работы приложения', 'страница будет перезагружена!']
        modalInfoType.value = 'danger'
        modalInfoMode.value = 'inform'
        await appModalAsyncMultiline.value!.show()

        const readyDay = await cuttingStore.readyUnsetCuttingDay(cuttingDay.value!.id)
        if (checkCRUD(readyDay)) {
            cuttingDay.value!.ready = readyDay.ready
            window.location.reload()
        } else {
            await showError()
            return
        }
    }
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Старт / Стоп                   !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Обработка начала выполнения СЗ
const handleStartAction = async () => {
    if (!isCuttingDayStarted.value) {
        // __ Старт.

        // __ 1. Проверка, есть ли не выполненные или выполняемые задания за предыдущий период (до текущего дня)
        // __ (Избыточно при переносе невыполненных или выполняемых заданий на следующий день на сервере)
        // __ 2. Если есть, то показываем предупреждение
        // __ 3. Если нет, то запускаем выполнение

        const startedDay = await cuttingStore.startCuttingDay(cuttingDay.value!.id)
        // console.log('Start: returnedData: ', startedDay)

        if (checkCRUD(startedDay)) {
            cuttingDay.value!.start_at  = startedDay.start_at
            cuttingDay.value!.resume_at = startedDay.resume_at
            cuttingDay.value!.finish_at = null
            // console.log('start')
            startTimer()
        } else {
            await showError()
            return
        }
    } else {
        // __ Стоп.

        // __    Проверяем, не находится ли СЗ в процессе паузы для добавления новых СЗ
        // __ 1. Проверяем, заполнен ли персонал
        // __ 2. Проверяем, есть ли ответственный
        // __ 3. Проверяем по каждому СЗ, выполнены ли все задания
        // __ 4. Закрываем выполнение

        if (isCuttingDayReadyForNewTasks.value) {
            await showError([
                'Выполнение СЗ находится в процессе паузы',
                'для добавления новых СЗ!',
                'Для закрытия смены необходимо возобновить выполнение.'
            ])
            return
        }

        if (cuttingDay.value!.workers.length === 0) {
            await showError('Необходимо заполнить персонал!')
            return
        }

        if (!cuttingDay.value!.responsible || !cuttingDay.value!.responsible.id) {
            await showError(['Не выбран ответственный сотрудник', 'за выполнение смены!'])
            return
        }

        for (const task of cuttingDay.value!.cutting_tasks) {
            for (const line of task.cutting_lines) {
                if (!line.finished_at && !line.false_at) {
                    console.log('line: ', line)

                    await showError([
                        'Нет отметки выполнения!',
                        `СЗ: ${task.order.client.short_name} №${task.order.order_no_num}`,
                        `Строка: ${getCoverSizeString(line)} ${getCuttingTaskModelCoverName(line)} ${line.amount} шт.`,
                    ])
                    return
                }
            }
        }

        const finishedDay = await cuttingStore.finishCuttingDay(cuttingDay.value!.id)
        console.log('Finish: returnedData: ', finishedDay)

        if (checkCRUD(finishedDay)) {
            modalInfoType.value = 'success'
            modalInfoMode.value = 'inform'
            modalInfoText.value = ['Смена успешно закрыта!']

            // !!! TODO: Дописать время закрытия смены
            // modalInfoText.value = ['Смена успешно закрыта!']

            await appModalAsyncMultiline.value!.show()

            // __ Переходим на страницу с выполнением СЗ
            await router.push({ name: 'manufacture.cell.cutting.tasks.execute' })
        } else {
            await showError()
        }
    }
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Персонал                       !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Добавляем работника
const addWorker = (worker: ICuttingDayWorker) => {
    const existWorker = cuttingDay.value!.workers.find(w => w.id === worker.id)
    if (!existWorker) {
        cuttingDay.value!.workers.push(worker)
    }
}


// __ Добавляем список работников
const addWorkers = (workers: ICuttingDayWorker[]) => {
    workers.forEach(worker => {
        const existWorker = cuttingDay.value!.workers.find(w => w.id === worker.id)
        if (!existWorker) {
            console.log('push: ', worker)
            cuttingDay.value!.workers.push(worker)
        }
    })
}


// __ Удаляем работника
const removeWorker = (worker: ICuttingDayWorker) => {
    const findIndex = cuttingDay.value!.workers.findIndex(w => w.id === worker.id)
    if (findIndex !== -1) {
        cuttingDay.value!.workers.splice(findIndex, 1)
        if (cuttingDay.value!.responsible && cuttingDay.value!.responsible.id === worker.id) {
            cuttingDay.value!.responsible = null
        }
    }
}

// __ Добавляем Ответственного
const addResponsible = (worker: ICuttingDayWorker) => {
    cuttingDay.value!.responsible = worker
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---   Функционал для выполнения дня Записи        !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Устанавливаем статус Выполнено CuttingLines
const setFinishStatus = async (cuttingLinesIds: number[]) => {
    const result = (await cuttingStore.setCuttingTaskLinesDone(cuttingLinesIds)) as ICuttingTaskLine[]

    if (checkCRUD(result)) {
        result.forEach(line => {
            const findLine = allCuttingTasksLinesUnion.value.cutting_lines.find(l => l.id === line.id)
            if (findLine) {
                findLine.finished_at = line.finished_at
            }
        })
    } else {
        await showError()
    }
}

// __ Устанавливаем статус Не Выполнено CuttingLines
const setFalseStatus = async (cuttingLinesIds: number[], falseReason: string) => {
    const result = (await cuttingStore.setCuttingTaskLinesFalse(cuttingLinesIds, falseReason)) as ICuttingTaskLine[]

    if (checkCRUD(result)) {
        result.forEach(line => {
            const findLine = allCuttingTasksLinesUnion.value.cutting_lines.find(l => l.id === line.id)
            if (findLine) {
                findLine.false_at     = line.false_at
                findLine.false_reason = line.false_reason
            }
        })
    } else {
        await showError()
    }
}

// __ Сбрасываем статус CuttingLines
const resetStatus = async (cuttingLinesIds: number[]) => {
    const result = (await cuttingStore.setCuttingTaskLinesReset(cuttingLinesIds)) as ICuttingTaskLine[]

    if (checkCRUD(result)) {
        result.forEach(line => {
            const findLine = allCuttingTasksLinesUnion.value.cutting_lines.find(l => l.id === line.id)
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
const divideLine = async (taskId: number, cuttingLineId: number, range: { take: number; keep: number }) => {

    // __ Старый вариант, когда нельзя было разбить в Объединении СЗ
    // const findTask = cuttingDay.value!.cutting_tasks.find(task => task.id === taskId)

    // __ Новый вариант, когда можно разбить в Объединении СЗ, в принципе taskId не нужен
    let findTask: ICuttingTask | undefined = undefined
    for (const task of cuttingDay.value!.cutting_tasks) {
        for (const line of task.cutting_lines) {
            if (line.id === cuttingLineId) {
                findTask = task
                break
            }
        }
        if (findTask) {
            break
        }
    }

    if (!findTask) {
        return // страховка
    }

    const dividerElementIndex = findTask.cutting_lines.findIndex(line => line.id === cuttingLineId)
    const newCuttingLine       = { ...findTask.cutting_lines[dividerElementIndex] } // __ Копируем объект
    newCuttingLine.id          = 0 // __ Устанавливаем новый ID
    newCuttingLine.position    = round(newCuttingLine.position + 0.1, 1) // __ Делаем новую строку ниже текущей позицию с шагом 0.1 (всего 9 разбиений)

    newCuttingLine.amount                              = range.take
    findTask.cutting_lines[dividerElementIndex].amount = range.keep

    // __ Вставляем новую строку
    findTask.cutting_lines.splice(dividerElementIndex + 1, 0, newCuttingLine)
    findTask.cutting_lines.sort((a, b) => a.position - b.position) // !!! Обязательно

    const result = await cuttingStore.divideLineInCuttingTaskPending(findTask, { start: executeDate, end: executeDate })

    // await cuttingStore.getCuttingTasks({ start: executeDate, end: executeDate })
    // await nextTick() // __ Ждем, пока все отрендерится
    // prepareData()
    // setTabs()
    // await nextTick() // __ Ждем, пока все отрендерится

    // console.log('result: ', result)
    // console.log('tabs: ', tabs.value)
    // console.log('activeTabPosition: ', activeTabPosition)

    if (!checkCRUD(result)) {
        await showError()
    } else {
        return
    }
}

// __ Подготавливаем данные для отображения
const prepareData = () => {
    if (!cuttingDay.value) return

    // __ Фильтруем задания
    cuttingDay.value!.cutting_tasks = globalCuttingTasks.value
        .filter(task => task.current_status.id === CUTTING_TASK_STATUSES.PENDING.ID || task.current_status.id === CUTTING_TASK_STATUSES.RUNNING.ID)
        .sort((a, b) => a.position - b.position)

    // console.log('prepareData: ', cuttingDay.value)

    // __ Формируем объединение всех CuttingTaskLines и добавляем признак группировки для "Объединения СЗ"
    allCuttingTasksLinesUnion.value.cutting_lines = []

    // !!! Тут важно, в allCuttingTasksLinesUnion.value кладем ссылку на объект, а не его копию, чтобы сокранять реактивность при Выполнено/Не выполнено/Сброс и тд
    cuttingDay.value!.cutting_tasks.forEach(task => task.cutting_lines.forEach(line => {
        line.groupAttr = getCuttingTaskTitle(task)
        allCuttingTasksLinesUnion.value.cutting_lines.push(line)
    }))
    // cuttingDay.value!.cutting_tasks.forEach(task => task.cutting_lines.forEach(line => allCuttingTasksLinesUnion.value.cutting_lines.push({ ...line, groupAttr: getCuttingTaskTitle(task) })))

    allCuttingTasksLinesUnion.value.position  = UNION_TASKS_POSITION // __ Позиция объединения всех строк
    allCuttingTasksLinesUnion.value.action_at = cuttingDay.value.action_at
}

const startTimer = () => {
    // __ Запускаем счетчик
    timer = setInterval(() => {
        now.value = new Date().getTime() / 1000
        // console.log(now.value)
    }, 1000)
}

watch(
    () => globalCuttingTasks.value,
    () => {
        // console.log('globalCuttingTasks updated')
        prepareData()
        setTabs()
    },
    { deep: true }
)

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
            const [dayData, tasksBefore, /*_*/] = await Promise.all([
                cuttingStore.getCuttingDayByDateAndChange(executeDate),
                cuttingStore.getCuttingTasksByStatusBeforeDate(executeDate, [
                    CUTTING_TASK_STATUSES.PENDING.ID,
                    CUTTING_TASK_STATUSES.RUNNING.ID,
                    CUTTING_TASK_STATUSES.CREATED.ID,
                    CUTTING_TASK_STATUSES.ROLLING.ID,
                ]),
                cuttingStore.getCuttingTasks({ start: executeDate, end: executeDate }),
            ])

            tasksBeforeCurrentDay.value = tasksBefore
            cuttingDay.value             = dayData

            prepareData()

            // __ Запускаем счетчик
            startTimer()
            // timer = setInterval(() => {
            //     now.value = new Date().getTime() / 1000
            //     // console.log(now.value)
            // }, 1000)

            setTabs()

            console.log('executeDate: ', executeDate)
            console.log('globalCuttingTasks: ', globalCuttingTasks.value)
            console.log('cuttingDay: ', cuttingDay.value)
            console.log('tasksBeforeCurrentDay: ', tasksBeforeCurrentDay.value)

            // // __ Получаем CuttingTasks по статусу и записываем в глобальную переменную в CuttingStore
            // await cuttingStore.getCuttingTasksByStatus([
            //     CUTTING_TASK_STATUSES.PENDING.ID,
            //     CUTTING_TASK_STATUSES.RUNNING.ID,
            // ])
            //
            // // __ Получаем дни
            // await getCuttingDays()
            //
            // // __ Объединяем задания с днями
            // unionDatesWithCuttingTasks(cuttingDays.value, globalCuttingTasksPending.value)
            //
            // // __ Добавляем свернутость
            // addCollapsed()
            //
            // if (DEBUG) console.log('globalCuttingTasksPending:', globalCuttingTasksPending.value)
            // if (DEBUG) console.log('cuttingDays:', cuttingDays.value)
            // if (DEBUG) console.log('renderCuttingDays:', renderCuttingDays.value)
        },
        undefined
        // false,
    )

    isLoading.value = false
})

onUnmounted(() => {
    clearTimer() // __ Очищаем таймер, чтобы не было утечек памяти
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
