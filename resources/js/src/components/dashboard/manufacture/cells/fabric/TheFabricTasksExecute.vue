<template>

    <div v-if="!isLoading" class="mt-2 ml-2">

        <!-- __ Выводим даты + статусы + сервисные кнопки   -->
        <div class="flex h-[130px] m-3">
            <div v-for="task in taskData" :key="task.date">

                <!-- __ Рамка выбора даты -->
                <div
                    :class="task.active ? 'bg-blue-200 border-2 border-blue-400 rounded-lg' : ''"
                    class="flex flex-col p-0.5 h-full">

                    <!-- __ Дата + день недели -->
                    <AppLabelMultiLine
                        :text="[formatDate(task.date), getDayOfWeek(task.date, false)]"
                        :type="dayOfWeekStyle(task.date)"
                        align="center"
                        class="cursor-pointer"
                        width="w-[150px]"
                        @click="changeActiveTask(task)"
                    />

                    <!-- __ Статусы -->
                    <AppLabel
                        :text="getTitleByFabricTaskStatusCode(task.common.status)"
                        :type="getStyleTypeByFabricTaskStatusCode(task.common.status)"
                        align="center"
                        class="cursor-pointer"
                        width="w-[150px]"
                    />

                    <!-- __ Кнопка "Начать СЗ + Закончить СЗ" (Только для текущего СЗ) -->
                    <AppLabel
                        v-if="getStartStopButtonShowCondition(task)"
                        :text="getExecuteButtonTitle(task.common.status)"
                        align="center"
                        class="cursor-pointer"
                        textSize="small"
                        type="info"
                        width="w-[150px]"
                        @click="changeTaskExecute(task)"
                    />

                </div>

            </div>
        </div>

        <!-- __ Выводим табы, если есть СЗ -->
        <div v-if="activeTask.common.status !== FABRIC_TASK_STATUS.UNKNOWN.CODE">

            <!-- __ Выводим табы -->
            <div class="flex flex-row justify-start items-center m-3">

                <div v-for="tab in tabs" :key="tab.id">

                    <!-- __ Показываем только те СМ, для которых есть СЗ -->
                    <div v-if="getMachineShowCondition(activeTask, tab)">
                        <AppLabelMultiLine
                            :bold="true"
                            :text="tab.name"
                            :type="getTabType(tab)"
                            align="center"
                            class="cursor-pointer"
                            width="w-[150px]"
                            @click="changeTab(tab)"
                        />
                    </div>

                </div>

            </div>

            <!-- __ Выводим содержимое табов -->
            <div class="ml-3 mt-3">

                <!-- __ Общее-->
                <div v-if="tabs.common.shown">
                    <TheTaskCommonInfo
                        :key="rerender[0]"
                        :task="activeTask"
                        @select-workers="selectWorkers"

                    />
                </div>

                <!-- __ Содержимое каждой СМ-->
                <div v-for="tab in tabs" :key="tab.id">

                    <div v-if="tab.hasOwnProperty('machine')">
                        <TheTaskExecuteMachine
                            v-if="tab.shown"
                            :key="rerender[tab.machine.ID]"
                            :machine="tab.machine"
                            :task="activeTask"
                            @add-execute-roll="addExecuteRoll"
                            @calculator-action-handler="calculatorActionHandler"
                            @change-calc-status="changeCalcStatus"
                            @save-exec-rolls-order="saveExecRollsOrder"
                        />
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- __ Асинхронное модальное окно -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="confirm"
    />

    <!-- __ Callout -->
    <AppCallout
        :show="calloutShow"
        :text="calloutText"
        :type="calloutType"
    />

</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { onBeforeRouteLeave, onBeforeRouteUpdate } from 'vue-router'
import { storeToRefs } from 'pinia'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
    FABRICS_NULLABLE,
    FABRIC_TASKS_EXECUTE, FABRIC_ROLL_STATUS,
} from '@/app/constants/fabrics.js'

import {
    getTitleByFabricTaskStatusCode,
    getStyleTypeByFabricTaskStatusCode,
    getFabricTasksPeriod,
} from '@/app/helpers/manufacture/helpers_fabric.js'

import {
    getDayOfWeek,
    formatDate,
    isToday,
    isWorkingDay,
} from '@/app/helpers/helpers_date.js'

import { catchErrorHandler } from '@/app/helpers/helpers_checks.ts'

import TheTaskCommonInfo
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskCommonInfo.vue'
import TheTaskExecuteMachine
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteMachine.vue'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'


const isLoading = ref(true)
// __ End Loader

const fabricsStore = useFabricsStore()
const {globalOrderExecuteChangeFlag} = storeToRefs(fabricsStore)    // __ флаг изменения порядка рулонов


// __ Асинхронная модальное окно
const appModalAsync = ref(null)         // Получаем ссылку на модальное окно
const modalText = ref([])
const modalType = ref('danger')


// __ Callout для вывода ошибок и предупреждений
const calloutType = ref('danger')
const calloutText = ref('')
const calloutShow = ref(false)
const calloutClose = (delay = 5000) => setTimeout(() => calloutShow.value = false, delay) // закрываем callout


// __ Создаем реактивную переменную и вешаем ее ключом на компоненты для ререндеринга
const rerender = reactive([0, 0, 0, 0, 0])


// __ Подготавливаем переменные
let fabrics = []  // загружаем после монтирования
let tasksPeriod = null
let tasks = []
const taskData = ref(null)
const activeTask = ref(null)


// __ Задаем отображение вкладок (Общие данные, Американец, Немец, Китаец, Кореец)
const tabs = reactive({
    common: {id: 1, shown: false, name: ['Общие', 'данные'], typePassive: 'warning'},
    american: {
        id: 2,
        shown: false,
        name: ['Американец', 'LEGACY-4'],
        typePassive: 'dark',
        machine: FABRIC_MACHINES.AMERICAN
    },
    german: {
        id: 3,
        shown: false,
        name: ['Немец', 'CHAINTRONIC'],
        typePassive: 'dark',
        machine: FABRIC_MACHINES.GERMAN
    },
    china: {
        id: 4,
        shown: false,
        name: ['Китаец', 'HY-W-DGW'],
        typePassive: 'dark',
        machine: FABRIC_MACHINES.CHINA
    },
    korean: {
        id: 5,
        shown: false,
        name: ['Кореец', 'МТ-94'],
        typePassive: 'dark',
        machine: FABRIC_MACHINES.KOREAN
    },
    // oneNeedle: {id: 6, shown: false, name: ['Одноиголка', '']},
    // test: {id: 6, shown: false, name: ['Machine', 'Test']},
})


// __ Получаем все ткани и запоминаем в хранилище
const getFabrics = async () => {
    fabrics = await fabricsStore.getFabrics(true)
    fabrics.unshift(FABRICS_NULLABLE)                   // добавляем пустой элемент в начало массива
    fabricsStore.fabricsMemory = fabrics
}


// __ Подготавливаем данные
const prepareTasksData = async () => {

    // __ Получаем период отображения сменного задания
    tasksPeriod = getFabricTasksPeriod()

    // __ Получаем сами сменные задания
    tasks = await fabricsStore.getTasksByPeriod(tasksPeriod)

    // __ Выбираем все СЗ, которые имеют статус "Готов к стежке", "Выполняется" и "Выполнено"
    tasks = tasks.filter(
        t =>
            t.common.status === FABRIC_TASK_STATUS.PENDING.CODE ||
            t.common.status === FABRIC_TASK_STATUS.RUNNING.CODE ||
            t.common.status === FABRIC_TASK_STATUS.DONE.CODE
    )

    // __ Проверяем на последнее СЗ со статусом "Выполнено".
    // __ Если его нет - получаем последнее СЗ, которое имеет статус "Выполнено"
    if (tasks[0] === undefined || (tasks[0] !== undefined && tasks[0].common.status !== FABRIC_TASK_STATUS.DONE.CODE)) {
        const lastDoneTask = await fabricsStore.getLastDoneFabricTask()
        if (lastDoneTask) tasks.unshift(lastDoneTask)       // добавляем последнее СЗ в начало массива, если оно есть
        // console.log('lastDoneTask:', lastDoneTask)
    }

    // __ Сортируем массив рулонов ОПП + сортируем массив физических рулонов СМ по позиции
    tasks.forEach(task => {
        Object.keys(task.machines).forEach(machine => {
            // по ОПП
            task.machines[machine].rolls = task.machines[machine].rolls.sort((a, b) => a.roll_position - b.roll_position)
            // по позиции
            task.machines[machine].rolls.forEach(roll => {
                roll.rolls_exec = roll.rolls_exec.sort((a, b) => a.position - b.position)
            })
            // Добавляем маяк "Калькулятора"
            task.machines[machine].rolls.forEach(roll => {
                roll.rolls_exec = roll.rolls_exec.map(rollExec => ({...rollExec, isCalc: false}))
            })
        })
    })

    // __ Формируем данные для отображения
    taskData.value = tasks

    // __ Ссылка на активное СЗ (по умолчанию сегодняшнее СЗ, если его нет - первый в массиве)
    activeTask.value = taskData.value.find(t => isToday(t.date)) || tasks[0]
    activeTask.value.active = true

    console.log('taskData: ', taskData.value)
    // console.log('tasksPeriod:', tasksPeriod)
    // console.log('execute tasks: ', tasks)
    // console.log('activeTask', activeTask.value)
}


// __ Обновляем данные с сервера
const updateTaskData = async () => {
    // isLoading.value = true
    const activeTaskDate = activeTask.value.date
    let activeTabId = 0

    for (const tab in tabs) {
        if (tabs[tab].shown) {
            activeTabId = tabs[tab].id
            break
        }
    }

    await prepareTasksData()

    activeTask.value = taskData.value.find(t => t.date === activeTaskDate)
    resetTabs()

    for (const tab in tabs) {
        if (tabs[tab].id === activeTabId) {
            tabs[tab].shown = true
            break
        }
    }
    // isLoading.value = false
}


// __ Получаем тип метки в зависимости от типа дня недели (выходной или рабочий)
const dayOfWeekStyle = (date) => {
    if (isToday(date)) return 'success'
    if (isWorkingDay(date)) return 'dark'
    return 'danger'
}



// __ Переключаем выбранную вкладку
const changeTab = (selectedTab) => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = tabs[tab].id === selectedTab.id
        }
    }

    globalOrderExecuteChangeFlag.value = false // сбрасываем флаг изменения порядка рулонов

    setActiveTaskAndTab()
}

// __ Сбрасываем все вкладки в false, потому что в некоторых ситуациях появляется мультивыбор
const resetTabs = () => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = false
        }
    }
}


// __ Меняем активный день по клику на нем
const changeActiveTask = (task) => {

    globalOrderExecuteChangeFlag.value = false // сбрасываем флаг изменения порядка рулонов

    taskData.value.forEach((t) => t.active = t.date === task.date)
    activeTask.value = taskData.value.find(t => t.active)

    setActiveTaskAndTab()

    // console.log('active_task: ', activeTask.value)
    // descr: Обновляем глобальную продуктивность для всех машин, чтобы исправить bug в отображении продуктивности общей
    fabricsStore.clearTaskGlobalProductivity()

    // // __ Перерисовываем все табы, чтобы исправить баг с отображением продуктивности общей
    // rerender.forEach((_, index, array) => array[index]++)

    resetTabs()                                 // сбрасываем все табы
    tabs.common.shown = true                    // делаем вкладку "общие данные" активной, чтобы запустить реактивность
}

// __ Определяем тип таба (цвет) в зависимости от наличия СЗ
const getTabType = (tab) => {

    // если вкладка активна
    if (tab.shown) return 'primary'
    if (tab.hasOwnProperty('machine')) {

        // если неактивна, но есть СЗ
        if (activeTask.value.machines[tab.machine.TITLE].rolls.length > 0) return 'success'
    }
    return tab.typePassive
}


// __ Поднятое событие при клике на кнопку "Персонал", точнее, его сохранение
const selectWorkers = async (workersList) => {
    workersList = workersList.filter(worker => worker.checked)
    // console.log('selectWorkers: ', workersList)
    // console.log(workersList)

    // Warning: Тут отправляем на сервер ключ-значение вида {"worker_id":1,"record_id":1}
    // warning: чтобы синхронизировать данные в таблице worker_records
    const workersIds = workersList.map(worker => ({worker_id: worker.id, record_id: worker.record_id}))
    // console.log(workersIds)

    const res = await fabricsStore.updateFabricTaskWorkers(activeTask.value.common.id, workersIds)
    // console.log(res)

    const newTaskDay = await fabricsStore.getTasksByPeriod({start: activeTask.value.date, end: activeTask.value.date})
    // console.log('newTaskDay: ', newTaskDay)

    activeTask.value.workers = newTaskDay[0].workers

    // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
    rerender.forEach((_, index, array) => array[index]++)
}


// warning: ------------------------------------------------------------------------

// attract: Начало выполнения сменного задания
const changeTaskExecute = async (task) => {

    // attract: Изменить статус с "Готов к стежке" на "Выполняется". Обращаемся к API
    if (task.common.status === FABRIC_TASK_STATUS.PENDING.CODE) {

        // attract: Проверки на наличие всех необходимых условий
        // attract: 1. Не должно быть ни одного задания в процессе выполнения
        // attract: 2. Не должно быть ни одного задания не со статусом "Выполнено" до текущей даты
        // attract: 3. В СЗ должен быть хотя бы один рулон

        // attract: 1.
        const runningTasks = await fabricsStore.getFabricExecutingTasks()
        // console.log('runningTasks: ', runningTasks)

        if (runningTasks.length > 0) {
            calloutType.value = 'danger'
            calloutText.value = 'Присутствуют СЗ на стадии выполнения. Необходимо завершить их выполнение.'
            calloutShow.value = true
            calloutClose()
            return
        }

        // attract: 2.
        const notDoneTasks = await fabricsStore.getFabricNotDoneTasks(task.date)
        // console.log('notDoneTasks: ', notDoneTasks)

        if (notDoneTasks.length > 0) {
            calloutType.value = 'danger'
            calloutText.value = 'Присутствуют ранние СЗ с некорректным статусом. Необходимо завершить их или удалить.'
            calloutShow.value = true
            calloutClose()
            return
        }

        // attract: 3.
        // считаем общее количество рулонов
        let execRollsAmountTotal = 0
        Object.keys(task.machines).forEach((machine) => {
            let execRollsAmount = task.machines[machine].rolls.reduce((rollsAmount, roll) => rollsAmount + roll?.rolls_exec.length, 0)
            execRollsAmountTotal += execRollsAmount
        })
        // console.log(execRollsAmountTotal)

        if (execRollsAmountTotal === 0) {
            calloutType.value = 'danger'
            calloutText.value = 'В СЗ не найдено ни одного рулона.'
            calloutShow.value = true
            calloutClose()
            return
        }

        modalText.value = ['Начать выполнение сменного задания?', '']
        modalType.value = 'success'
        const result = await appModalAsync.value.show()             // показываем модалку и ждем ответ
        if (result) {
            task.common.status = FABRIC_TASK_STATUS.RUNNING.CODE
            const res = await fabricsStore.changeFabricTaskDateStatus(task)
            console.log(res)

            const newTaskDay = await fabricsStore.getTasksByPeriod({start: task.date, end: task.date})
            console.log('newTaskDay: ', newTaskDay)

            // task.machines = newTaskDay[0].machines
            task.common = newTaskDay[0].common

            // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
            rerender.forEach((_, index, array) => array[index]++)

            // todo: сделать обработку ошибок + callout
        }

        return
    }

    // attract: Изменить статус с "Выполняется" на "Выполнено". Обращаемся к API
    if (task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE) {

        // __ Проверки на наличие всех необходимых условий
        // __ 1. Должен быть заполнен персонал общий к дню СЗ
        // __ 2. Должны быть проставлены правильные статусы для всех рулонов по каждой СМ:
        // __     "Выполнено", "Не выполнено", "Переходящий"
        // __ 3. Должен быть заполнен персонал на каждый рулон по каждой СМ
        // __ 4. Не должно быть ни одного рулона, который не может быть перемещен. Пока не реализовано


        // __ 1.
        // console.log(task.workers)
        if (task.workers.length === 0) {
            calloutType.value = 'danger'
            calloutText.value = 'Не заполнен персонал.'
            calloutShow.value = true
            calloutClose()
            return
        }

        // attract: 2.
        let isError = false
        Object.keys(task.machines).forEach((machine) => {
            task.machines[machine].rolls.forEach((roll) => {
                isError ||= roll.rolls_exec.some((roll_exec) => {
                    // return !(roll_exec.status === FABRIC_ROLL_STATUS.DONE.CODE ||
                    //     roll_exec.status === FABRIC_ROLL_STATUS.ROLLING.CODE ||
                    //     roll_exec.status === FABRIC_ROLL_STATUS.FALSE.CODE ||
                    //     roll_exec.status === FABRIC_ROLL_STATUS.CANCELLED.CODE)

                    return (roll_exec.status === FABRIC_ROLL_STATUS.CREATED.CODE ||
                        roll_exec.status === FABRIC_ROLL_STATUS.RUNNING.CODE ||
                        roll_exec.status === FABRIC_ROLL_STATUS.PAUSED.CODE)

                })
            })
        })

        // console.log('statusError: ', isError)
        if (isError) {
            calloutType.value = 'danger'
            calloutText.value = 'Некорректный статус одного из рулонов.'
            calloutShow.value = true
            calloutClose()
            return
        }

        // attract: 3.
        isError = false
        Object.keys(task.machines).forEach((machine) => {
            task.machines[machine].rolls.forEach((roll) => {
                isError ||= roll.rolls_exec.some((roll_exec) => roll_exec.finish_by === 0)
            })
        })
        // console.log('workerIdError: ', isError)

        if (isError) {
            calloutType.value = 'danger'
            calloutText.value = 'Не заполнен ответственный за выпуск одного из рулонов.'
            calloutShow.value = true
            calloutClose()
            return
        }

        modalText.value = ['Закончить выполнение сменного задания?', '']
        modalType.value = 'warning'
        const result = await appModalAsync.value.show()             // показываем модалку и ждем ответ
        if (result) {
            task.common.status = FABRIC_TASK_STATUS.DONE.CODE
            const res = await fabricsStore.closeFabricTasks(task.date)

            const newTaskDay = await fabricsStore.getTasksByPeriod({start: task.date, end: task.date})
            // console.log('created: newTaskDay: ', newTaskDay)

            task.machines = newTaskDay[0].machines
            task.common = newTaskDay[0].common
            task.workers = newTaskDay[0].workers

            rerender.forEach((_, index, array) => array[index]++)
        }

        // return
    }
}


// __ Добавить рулон в СЗ во время выполнения
const addExecuteRoll = async (addingRollData) => {

    console.log('TaskExec: addExecuteRoll:', addingRollData)
    const res = await fabricsStore.addExecuteRoll({...addingRollData, taskId: activeTask.value.common.id})
    await updateTaskData() // обновляем данные с сервера

    // const newTaskDay = await fabricsStore.getTasksByPeriod({start: activeTask.value.date, end: activeTask.value.date})
    // // console.log('created: newTaskDay: ', newTaskDay)
    //
    // // taskData.value = await prepareTasksData()
    //
    //
    // newTaskDay.forEach(task => {
    //     Object.keys(task.machines).forEach(machine => {
    //         task.machines[machine].rolls = task.machines[machine].rolls.sort((a, b) => a.roll_position - b.roll_position)
    //     })
    // })
    //
    // activeTask.value.machines = newTaskDay[0].machines
    //
    rerender.forEach((_, index, array) => array[index]++)
}


// attract: Возвращает текст для кнопки управления "Старт" или "Стоп" или "Выполнено"
const getExecuteButtonTitle = (taskStatus) => {
    if (taskStatus === FABRIC_TASK_STATUS.PENDING.CODE) return FABRIC_TASKS_EXECUTE.START.TITLE
    if (taskStatus === FABRIC_TASK_STATUS.RUNNING.CODE) return FABRIC_TASKS_EXECUTE.STOP.TITLE
    return ''
}


// attract: Возвращаем условие отображения машины (присутствуют рулоны в СЗ)
const getMachineShowCondition = (task, tab) => {

    if (tab.hasOwnProperty('machine')) return task.machines[tab.machine.TITLE].rolls.length > 0
    return true

}


// attract: Возвращаем условие начала/окончания выполнения
const getStartStopButtonShowCondition = (task) => {

    // если "Готов к стежке"
    if (task.common.status === FABRIC_TASK_STATUS.PENDING.CODE) {
        return isToday(task.date)
    }

    // если "Выполняется"
    if (task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE) {
        // TODO!!! warning - включить проверку на дату текущего дня окончания выполнения СЗ
        return true
        // return isToday(task.date)
    }

    // Было изначально
    // return isToday(task.date) && task.common.status !== FABRIC_TASK_STATUS.DONE.CODE
}


// __ Калькулятор
const calculatorActionHandler = (handler, machine) => {
    activeTask.value.machines[machine.TITLE].rolls.forEach(roll => {
        roll.rolls_exec.forEach(roll_exec => handler(roll_exec))
    })
}


// __ Изменение статуса калькулятора
const changeCalcStatus = (rollExec, machine) => {
    activeTask.value.machines[machine.TITLE].rolls.forEach(roll => {
        roll.rolls_exec.forEach(roll_exec => {
            if (roll_exec.id === rollExec.id) {
                roll_exec.isCalc = !roll_exec.isCalc
            }
        })
    })
}


// __ Сохранение порядка рулонов
const saveExecRollsOrder = async (rollsExec, machine) => {
    rollsExec.value.forEach((rollExec, index) => rollExec.position = index + 1)
    const res = await fabricsStore.saveExecuteRollsOrder(rollsExec.value, fabricsStore.globalOrderExecuteChangeReason)
    // console.log('saveExecRollsOrder: ', rollsExec.value)
    // console.log('machine: ', machine)
}


// __ Устанавливаем активное СЗ и вкладку в LocalStorage
const TASK_TAB_PREFIX = 'TASK_EXECUTE_TAB'
const setActiveTaskAndTab = () => {
    try {
        const findTab = Object.keys(tabs).find(tab => tabs[tab].shown)
        localStorage.setItem(TASK_TAB_PREFIX, JSON.stringify({
            activeTaskDate: activeTask.value.date,
            activeTabId: findTab ? tabs[findTab].id : tabs.common.id
        }))
    } catch (e) {
        catchErrorHandler('LocalStorage: ', e)
    }
}


// __ Получаем активное СЗ и вкладку из LocalStorage
const getActiveTaskAndTab = () => {
    try {
        const data = JSON.parse(localStorage.getItem(TASK_TAB_PREFIX))
        if (data) {
            const findTask = taskData.value.find(t => t.date === data.activeTaskDate)
            if (findTask) {
                changeActiveTask(findTask)
                resetTabs()
                // const findTab = Object.keys(tabs).find(tab => tabs[tab].id === data.activeTabId)
                // if (findTab) {
                //     tabs[findTab].shown = true
                // }
                tabs.common.shown = true  // __ делаем вкладку "общие данные" активной, чтобы получить данные по работягам
            }
        }
    } catch (e) {
        catchErrorHandler('LocalStorage: ', e)
    }
}


onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getFabrics()                              // Получаем список ПС
            await prepareTasksData()                        // Получаем список СЗ
            resetTabs()                                     // сбрасываем все табы
            tabs.common.shown = true                        // делаем вкладку "общие данные" активной, чтобы запустить реактивность
            getActiveTaskAndTab()                           // Получаем активное СЗ и вкладку из LocalStorage
            setActiveTaskAndTab()                           // Устанавливаем активное СЗ и вкладку в LocalStorage
            fabrics.globalOrderExecuteChangeFlag = false    // сбрасываем флаг изменения порядка рулонов
        },
        undefined,
        // false,
    )
    isLoading.value = false
})


// __ Сбрасываем состояние флага изменения порядка рулонов
onBeforeRouteLeave((to, from) => {
    globalOrderExecuteChangeFlag.value = false
})

onBeforeRouteUpdate((to, from) => {
    globalOrderExecuteChangeFlag.value = false
})

</script>

<style scoped>

</style>





