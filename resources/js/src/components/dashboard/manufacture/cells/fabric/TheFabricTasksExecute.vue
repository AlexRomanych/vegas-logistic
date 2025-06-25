<template>

    <div class="mt-2 ml-2">

        <!-- attract: Выводим даты + статусы + сервисные кнопки   -->
        <div class="flex h-[130px] m-3">
            <div v-for="task in taskData" :key="task.date">

                <!-- attract: Рамка выбора даты -->
                <div
                    :class="task.active ? 'bg-blue-200 border-2 border-blue-400 rounded-lg' : ''"
                    class="flex flex-col p-0.5 h-full">

                    <!-- attract: Дата + день недели -->
                    <AppLabelMultiLine
                        :text="[formatDate(task.date), getDayOfWeek(task.date, false)]"
                        :type="dayOfWeekStyle(task.date)"
                        align="center"
                        class="cursor-pointer"
                        width="w-[150px]"
                        @click="changeActiveTask(task)"
                    />

                    <!-- attract: Статусы -->
                    <AppLabel
                        :text="getTitleByFabricTaskStatusCode(task.common.status)"
                        :type="getStyleTypeByFabricTaskStatusCode(task.common.status)"
                        align="center"
                        class="cursor-pointer"
                        width="w-[150px]"
                    />

                    <!-- attract: Кнопка "Начать СЗ + Закончить СЗ" (Только для текущего СЗ) -->
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

        <!-- attract: Выводим табы, если есть СЗ -->
        <div v-if="activeTask.common.status !== FABRIC_TASK_STATUS.UNKNOWN.CODE">


            <!--            :type="tab.shown ? 'primary' : tab.typePassive"-->
            <!-- attract Выводим табы -->
            <div class="flex flex-row justify-start items-center m-3">

                <div v-for="tab in tabs" :key="tab.id">

                    <!-- attract: Показываем только те СМ, для которых есть СЗ -->
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


            <!-- attract: Выводим содержимое табов -->
            <div class="ml-3 mt-3">

                <!--attract: Общее-->
                <div v-if="tabs.common.shown">
                    <TheTaskCommonInfo
                        :key="rerender[0]"
                        :task="activeTask"
                        @select-workers="selectWorkers"

                    />
                </div>

                <!--attract: Содержимое каждой СМ-->
                <div v-for="tab in tabs" :key="tab.id">

                    <div v-if="tab.hasOwnProperty('machine')">
                        <TheTaskExecuteMachine
                            v-if="tab.shown"
                            :key="rerender[tab.machine.ID]"
                            :machine="{...tab.machine}"
                            :task="activeTask"
                            @add-execute-roll="addExecuteRoll"
                        />
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- attract: Асинхронное модальное окно -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="confirm"
    />

    <!-- attract: Callout -->
    <AppCallout
        :show="calloutShow"
        :text="calloutText"
        :type="calloutType"
    />

</template>

<script setup>
import {ref, reactive, watch} from 'vue'

import {useFabricsStore} from '@/stores/FabricsStore.js'

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

import TheTaskCommonInfo
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskCommonInfo.vue'
import TheTaskExecuteMachine
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteMachine.vue'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'


const fabricsStore = useFabricsStore()

// attract: Получаем период отображения сменного задания
const tasksPeriod = getFabricTasksPeriod()
console.log('tasksPeriod:', tasksPeriod)

const prepareTasksData = async () => {
    // attract: Получаем сами сменные задания
    let tasks = await fabricsStore.getTasksByPeriod(tasksPeriod)

// attract: Выбираем все СЗ, которые имеют статус "Готов к стежке", "Выполняется" и "Выполнено"
    tasks = tasks.filter(
        t =>
            t.common.status === FABRIC_TASK_STATUS.PENDING.CODE ||
            t.common.status === FABRIC_TASK_STATUS.RUNNING.CODE ||
            t.common.status === FABRIC_TASK_STATUS.DONE.CODE
    )

// attract: Проверяем на последнее СЗ со статусом "Выполнено".
// attract: Если его нет - получаем последнее СЗ, которое имеет статус "Выполнено"
    if (tasks[0] === undefined || (tasks[0] !== undefined && tasks[0].common.status !== FABRIC_TASK_STATUS.DONE.CODE)) {
        const lastDoneTask = await fabricsStore.getLastDoneFabricTask()
        if (lastDoneTask) tasks.unshift(lastDoneTask)       // добавляем последнее СЗ в начало массива, если оно есть
        // console.log('lastDoneTask:', lastDoneTask)
    }

    return tasks
}

let tasks = await prepareTasksData()


// attract: Формируем данные для отображения
let taskData = reactive(tasks)

// attract: Ссылка на активное СЗ (по умолчанию сегодняшнее СЗ, если его нет - первый в массиве)
let activeTask = reactive(taskData.find(t => isToday(t.date)) || tasks[0])
activeTask.active = true

console.log('tasks:', tasks)
console.log('taskData: ', taskData)
console.log('activeTask', activeTask)


// attract: Получаем все ткани и запоминаем в хранилище
const fabrics = await fabricsStore.getFabrics()
fabrics.unshift(FABRICS_NULLABLE)                   // добавляем пустой элемент в начало массива
fabricsStore.fabricsMemory = fabrics
// console.log(fabrics)

// attract: Тип для модального окна
const modalType = ref('danger')

// attract: Задаем отображение вкладок (Общие данные, Американец, Немец, Китаец, Кореец)
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
    china: {id: 4, shown: false, name: ['Китаец', 'HY-W-DGW'], typePassive: 'dark', machine: FABRIC_MACHINES.CHINA},
    korean: {id: 5, shown: false, name: ['Кореец', 'МТ-94'], typePassive: 'dark', machine: FABRIC_MACHINES.KOREAN},
    // oneNeedle: {id: 6, shown: false, name: ['Одноиголка', '']},
    // test: {id: 6, shown: false, name: ['Machine', 'Test']},
})

// переключаем выбранную вкладку
const changeTab = (selectedTab) => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = tabs[tab].id === selectedTab.id
        }
    }
    // console.log(tabs)
}

// сбрасываем все вкладки в false, потому что в некоторых ситуациях появляется мультивыбор
const resetTabs = () => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = false
        }
    }
    // console.log(tabs)
}

resetTabs()                                 // сбрасываем все табы
tabs.common.shown = true                    // делаем вкладку "общие данные" активной, чтобы запустить реактивность


// attract Получаем тип метки в зависимости от типа дня недели (выходной или рабочий)
const dayOfWeekStyle = (date) => {
    if (isToday(date)) return 'success'
    if (isWorkingDay(date)) return 'dark'
    return 'danger'
}


// attract: Асинхронная модальное окно
const appModalAsync = ref(null)         // Получаем ссылку на модальное окно
const modalText = ref([])

// attract: Callout для вывода ошибок и предупреждений
const calloutType = ref('danger')
const calloutText = ref('')
const calloutShow = ref(false)
const calloutClose = (delay = 5000) => setTimeout(() => calloutShow.value = false, delay) // закрываем callout


// attract: Меняем активный день по клику на нем
const changeActiveTask = (task) => {
    taskData.forEach((t) => t.active = t.date === task.date)
    activeTask = taskData.find(t => t.active)
    // console.log('active_task: ', activeTask)
    // descr: Обновляем глобальную продуктивность для всех машин, чтобы исправить bug в отображении продуктивности общей
    fabricsStore.clearTaskGlobalProductivity()

    // // attract: Перерисовываем все табы, чтобы исправить баг с отображением продуктивности общей
    // rerender.forEach((_, index, array) => array[index]++)

    resetTabs()                                 // сбрасываем все табы
    tabs.common.shown = true                    // делаем вкладку "общие данные" активной, чтобы запустить реактивность
}

// attract: Определяем тип таба (цвет) в зависимости от наличия СЗ
const getTabType = (tab) => {

    // если вкладка активна
    if (tab.shown) return 'primary'
    if (tab.hasOwnProperty('machine')) {

        // если неактивна, но есть СЗ
        if (activeTask.machines[tab.machine.TITLE].rolls.length > 0) return 'success'
    }
    return tab.typePassive
}


// attract: Поднятое событие при клике на кнопку "Персонал", точнее, его сохранение
const selectWorkers = async (workersList) => {
    workersList = workersList.filter(worker => worker.checked)
    // console.log('selectWorkers: ', workersList)
    // console.log(workersList)

    // Warning: Тут отправляем на сервер ключ-значение вида {"worker_id":1,"record_id":1}
    // warning: чтобы синхронизировать данные в таблице worker_records
    const workersIds = workersList.map(worker => ({worker_id: worker.id, record_id: worker.record_id}))
    // console.log(workersIds)

    const res = await fabricsStore.updateFabricTaskWorkers(activeTask.common.id, workersIds)
    // console.log(res)

    const newTaskDay = await fabricsStore.getTasksByPeriod({start: activeTask.date, end: activeTask.date})
    // console.log('newTaskDay: ', newTaskDay)

    activeTask.workers = newTaskDay[0].workers

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

        // attract: Проверки на наличие всех необходимых условий
        // attract: 1. Должен быть заполнен персонал общий к дню СЗ
        // attract: 2. Должны быть проставлены правильные статусы для всех рулонов по каждой СМ:
        // attract:     "Выполнено", "Не выполнено", "Переходящий"
        // attract: 3. Должен быть заполнен персонал на каждый рулон по каждой СМ
        // attract: 4. Не должно быть ни одного рулона, который не может быть перемещен. Пока не реализовано

        // attract: 1.
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


// attract: Добавить рулон в СЗ во время выполнения
const addExecuteRoll = async () => {
    const newTaskDay = await fabricsStore.getTasksByPeriod({start: activeTask.date, end: activeTask.date})
    // console.log('created: newTaskDay: ', newTaskDay)

    activeTask.machines = newTaskDay[0].machines

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
// getMachineShowCondition(activeTask, FABRIC_MACHINES.AMERICAN.ID)


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



// attract: Создаем реактивную переменную и вешаем ее ключом на компоненты для ререндеринга
const rerender = reactive([0, 0, 0, 0, 0])

watch(() => taskData, async (newValue) => {

    // rerender.value++
    // console.log('taskData: changed: ', rerender)


}, {deep: true})

</script>

<style scoped>

</style>





