<template>

    <div class="mt-2 ml-2">

        <!-- attract: Выводим даты + статусы + сервисные кнопки   -->
        <div class="flex h-[165px] m-3">
            <div v-for="task in taskData" :key="task.date">

                <!-- attract: Рамка выбора даты -->
                <div
                    :class="task.active ? 'bg-blue-200 border-2 border-blue-400 rounded-lg' : ''"
                    class="flex flex-col p-0.5  h-full">

                    <!-- attract: Дата + день недели -->
                    <AppLabelMultiLine
                        :text="[formatDate(task.date), getDayOfWeek(task.date, false)]"
                        :type="getDayOfWeekStyle(task.date)"
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

                    <!-- attract: Выводим сервисные кнопки, только если дата больше или равна сегодняшней -->
                    <div v-if="taskDateConstraint(task.date)">

                        <!-- attract: Первый ряд сервисных кнопок -->
                        <AppLabel
                            v-if="serviceBtnShowCondition(task.common.status)"
                            :text="serviceBtnTitle(task.common.status)"
                            align="center"
                            class="cursor-pointer"
                            textSize="small"
                            type="info"
                            width="w-[150px]"
                            @click="changeTaskStatus(task)"
                        />

                        <!-- attract: Второй ряд сервисных кнопок - кнопка "Удалить" (Только для статуса "Создано") -->
                        <AppLabel
                            v-if="task.common.status === FABRIC_TASK_STATUS.CREATED.CODE"
                            align="center"
                            class="cursor-pointer"
                            text="Удалить"
                            textSize="small"
                            type="danger"
                            width="w-[150px]"
                            @click="changeTaskStatus(task, 2)"
                        />

                    </div>

                </div>

            </div>
        </div>

        <!-- attract: Выводим табы, если есть СЗ -->
        <div v-if="activeTask.common.status !== FABRIC_TASK_STATUS.UNKNOWN.CODE">

            <!-- attract Выводим табы, если СМ активна или статус СЗ - "Выполнено" или "Выполняется" -->
            <div class="flex flex-row justify-start items-center m-3">
                <div v-for="tab in tabs" :key="tab.id">
                    <div v-if="tab.enabled || activeTask.common.status === FABRIC_TASK_STATUS.RUNNING.CODE || activeTask.common.status === FABRIC_TASK_STATUS.DONE.CODE">
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


            <div class="ml-3 mt-3">

                <!--attract: Общее-->
                <div v-if="tabs.common.shown">
                    <TheTaskCommonInfo
                        :key="rerender[0]"
                        :task="activeTask"
                        @saveTaskDescription="updateTaskDescription"
                        @select-workers="selectWorkers"
                    />
                </div>


                <!-- TODO: Убрать дублирование кода -->
                <!--attract: Американец-->
                <!-- warning: key: - для реактивности -->
                <!-- todo: доработать, потому, что task будем получать в самом компоненте -->
                <div v-if="tabs.american.shown">
                    <TheTaskMachine
                        :key="rerender[FABRIC_MACHINES.AMERICAN.ID]"
                        :machine="FABRIC_MACHINES.AMERICAN"
                        :task="activeTask"
                        @add-roll="addRoll"
                        @optimize-labor="optimizeLabor"
                        @save-task-record="saveTasks"
                        @delete-task-record="deleteTasks"
                        @save-machine-description="saveMachineDescription"
                    />

                </div>

                <!--attract: Немец-->
                <div v-if="tabs.german.shown">
                    <TheTaskMachine
                        :key="rerender[FABRIC_MACHINES.GERMAN.ID]"
                        :machine="FABRIC_MACHINES.GERMAN"
                        :task="activeTask"
                        @add-roll="addRoll"
                        @optimize-labor="optimizeLabor"
                        @save-task-record="saveTasks"
                        @delete-task-record="deleteTasks"
                        @save-machine-description="saveMachineDescription"
                    />
                </div>

                <!--attract: Китаец-->
                <div v-if="tabs.china.shown">
                    <TheTaskMachine
                        :key="rerender[FABRIC_MACHINES.CHINA.ID]"
                        :machine="FABRIC_MACHINES.CHINA"
                        :task="activeTask"
                        @add-roll="addRoll"
                        @optimize-labor="optimizeLabor"
                        @save-task-record="saveTasks"
                        @delete-task-record="deleteTasks"
                        @save-machine-description="saveMachineDescription"
                    />
                </div>

                <!--attract: Китаец-->
                <div v-if="tabs.korean.shown">
                    <TheTaskMachine
                        :key="rerender[FABRIC_MACHINES.KOREAN.ID]"
                        :machine="FABRIC_MACHINES.KOREAN"
                        :task="activeTask"
                        @add-roll="addRoll"
                        @optimize-labor="optimizeLabor"
                        @save-task-record="saveTasks"
                        @delete-task-record="deleteTasks"
                        @save-machine-description="saveMachineDescription"
                    />
                </div>

            </div>

        </div>

    </div>

    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="confirm"
    />

    <!--    <AppModal-->
    <!--        :show="modalSimpleShow"-->
    <!--        :text="modalSimpleText"-->
    <!--        :type="modalSimpleType"-->
    <!--    />-->

    <AppCallout
        :show="modalSimpleShow"
        :text="modalSimpleText"
        :type="modalSimpleType"
    />

</template>

<script setup>
import {ref, reactive, watch} from 'vue'
import {useRoute, useRouter} from 'vue-router'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
    FABRICS_NULLABLE,
    TASK_DRAFT,
    TEST_FABRICS,
} from '/resources/js/src/app/constants/fabrics.js'

import {cloneShallow} from '/resources/js/src/app/helpers/helpers_lib.js'

import {
    getTitleByFabricTaskStatusCode,
    getStyleTypeByFabricTaskStatusCode,
    getFabricTasksPeriod,
    addEmptyFabricTasks, fillFabricsDisplayNames
} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import {
    getDayOfWeek,
    formatDate,
    compareDatesLogic,
    getDayOfWeekStyle,
    // isToday,
    // isWorkingDay,
    // getISOFromLocaleDate,
} from '/resources/js/src/app/helpers/helpers_date.js'

import TheTaskCommonInfo
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskCommonInfo.vue'

import TheTaskMachine
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_manage/TheTaskMachine.vue'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '/resources/js/src/components/ui/modals/AppModalAsyncMultiline.vue'
import AppCallout from '/resources/js/src/components/ui/callouts/AppCallout.vue'


const route = useRoute()
const router = useRouter()

const fabricsStore = useFabricsStore()

// attract: Получаем список всех стегальных машин
const getFabricsMachines = async () => {
    const machines = await fabricsStore.getFabricsMachines()
    return machines.filter(machine => machine.id !== 0).sort((a, b) => a.id - b.id) // сортируем по id, без id == 0
}
const fabricsMachines = ref(await getFabricsMachines())


// attract: Получаем все ткани и запоминаем в хранилище
const fabrics = await fabricsStore.getFabrics(true)
fabrics.unshift(FABRICS_NULLABLE)                   // добавляем пустой элемент в начало массива
fabricsStore.fabricsMemory = fabrics
// console.log(fabrics)

// attract: Получаем период отображения сменного задания
const tasksPeriod = getFabricTasksPeriod()
console.log('tasksPeriod:', tasksPeriod)

// attract: Получаем сами сменные задания
let tasks = await fabricsStore.getTasksByPeriod(tasksPeriod)

// console.log('tasks:', tasks)
// debugger

// // attract: Заполняем поле 'fabric' - название ткани в сменных заданиях
// tasks = fillFabricsDisplayNames(fabrics, tasks)

console.log('tasks:', tasks)


// attract: формируем полный (дополненный) массив сменных заданий
// let taskData = reactive(tasks)
// // let taskData = reactive(TEST_FABRICS)
// taskData = reactive(addEmptyFabricTasks(taskData, tasksPeriod))

// let taskData = reactive(tasks)
// let taskData = reactive(TEST_FABRICS)
const taskData = reactive(addEmptyFabricTasks(tasks, tasksPeriod))


// attract: Ссылка на активное СЗ
let activeTask = reactive(taskData.find(t => t.active))

console.log('taskData: ', taskData)
console.log('activeTask', activeTask)


// attract: Тип для модального окна
const modalType = ref('danger')

// attract: Задаем отображение вкладок (Общие данные, Американец, Немец, Китаец, Кореец)
const tabs = reactive({
    common: {id: 1, shown: false, enabled: true, name: ['Общие', 'данные'], typePassive: 'warning'},
    american: {
        id: 2,
        shown: false,
        enabled: true,
        name: ['Американец', 'LEGACY-4'],
        typePassive: 'dark',
        machine: FABRIC_MACHINES.AMERICAN
    },
    german: {
        id: 3,
        shown: false,
        enabled: true,
        name: ['Немец', 'CHAINTRONIC'],
        typePassive: 'dark',
        machine: FABRIC_MACHINES.GERMAN
    },
    china: {
        id: 4,
        shown: false,
        enabled: true,
        name: ['Китаец', 'HY-W-DGW'],
        typePassive: 'dark',
        machine: FABRIC_MACHINES.CHINA
    },
    korean: {
        id: 5,
        shown: false,
        enabled: true,
        name: ['Кореец', 'МТ-94'],
        typePassive: 'dark',
        machine: FABRIC_MACHINES.KOREAN
    },
    // oneNeedle: {id: 6, shown: false, name: ['Одноиголка', '']},
    // test: {id: 6, shown: false, name: ['Machine', 'Test']},
})


const setEnabledTabs = () => {
    Object.keys(tabs).forEach(tab => {

        if (tabs[tab].hasOwnProperty('machine')) {
            const machine = fabricsMachines.value.find(m => m.id === tabs[tab].machine.ID)
            // console.log(machine.active)
            tabs[tab].enabled = machine.active
        }

    })
}
setEnabledTabs()
console.log(tabs)

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


// // attract Получаем тип метки в зависимости от типа дня недели (выходной или рабочий)
// const dayOfWeekStyle = (date) => {
//     if (isToday(date)) return 'success'
//     if (isWorkingDay(date)) return 'dark'
//     return 'danger'
// }

// attract: Условие на отображение сервисных кнопок под статусами
const serviceBtnShowCondition = (status) => {
    return !(status === FABRIC_TASK_STATUS.DONE.CODE || status === FABRIC_TASK_STATUS.RUNNING.CODE)
}

// attract: Получаем название сервисной кнопки в зависимости от статуса
const serviceBtnTitle = (status) => {
    const titles = ['Создать', 'На стежку', 'Вернуть статус', '', '']
    return titles[status]
}

// attract: Условие на отображение сервисной кнопки в зависимости от даты (больше или равна текущей)
const taskDateConstraint = (taskDate) => {
    const result = compareDatesLogic(new Date(), taskDate)
    return result || (result === undefined)
}

// attract: Простое модальное окно для вывода ошибок и предупреждений
const modalSimpleType = ref('danger')
const modalSimpleText = ref('')
const modalSimpleShow = ref(false)
const modalSimpleClose = (delay = 5000) => setTimeout(() => modalSimpleShow.value = false, delay) // закрываем модалку

const appModalAsync = ref(null)         // Получаем ссылку на модальное окно с асинхронной функцией
const modalText = ref([])


// attract Меняем статус СЗ по сервисной кнопке
const changeTaskStatus = async (task, btnRow = 1) => {

    if (!task.active) return

    // attract: Удалить сменное задание. Обращаемся к API
    if (btnRow === 2 && task.common.status === FABRIC_TASK_STATUS.CREATED.CODE) {

        // attract: Проверяем, чтобы были только правильные статусы (не было не выполненных или переходящих)
        // считаем общее количество рулонов
        let isFind = false
        Object.keys(task.machines).forEach((machine) => {
            isFind ||= task.machines[machine].rolls.some(roll => roll.editable)
        })

        if (!isFind) {
            modalSimpleType.value = 'danger'
            modalSimpleText.value = 'Задание не может быть удалено, потому присутствуют не выполненные или переходящие рулоны. Удалите их вручную.'
            modalSimpleShow.value = true
            modalSimpleClose()
            return
        }

        modalText.value = ['Сменное задания и все связанные с', 'ними данные будут удалены.', 'Продолжить?']
        modalType.value = 'danger'
        const result = await appModalAsync.value.show()             // показываем модалку и ждем ответ
        if (result) {
            task.common.status = FABRIC_TASK_STATUS.UNKNOWN.CODE
            const res = await fabricsStore.changeFabricTaskDateStatus(task)
            console.log(res)

            const newTaskDay = cloneShallow(TASK_DRAFT)     // получаем копию нового сменного дня чтобы, почистить всю дату
            // console.log('newTaskDay: ', newTaskDay)
            task.common = newTaskDay.common
            task.machines = newTaskDay.machines
            task.workers = newTaskDay.workers
            // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
            rerender.forEach((_, index, array) => array[index]++)
        }
        return
    }

    // attract: Сменить статус c "Не создано" на "Создано". Обращаемся к API
    if (task.common.status === FABRIC_TASK_STATUS.UNKNOWN.CODE) {
        task.common.status = FABRIC_TASK_STATUS.CREATED.CODE
        const res = await fabricsStore.changeFabricTaskDateStatus(task)
        console.log(res)

        const newTaskDay = await fabricsStore.getTasksByPeriod({start: task.date, end: task.date})
        console.log('created: newTaskDay: ', newTaskDay)

        task.machines = newTaskDay[0].machines
        task.common = newTaskDay[0].common

        // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
        rerender.forEach((_, index, array) => array[index]++)

        return
    }

    // attract: Вернуть статус с "Готов к стежке" на "Создано". Обращаемся к API
    if (task.common.status === FABRIC_TASK_STATUS.PENDING.CODE) {
        // task.common.status = FABRIC_TASK_STATUS.CREATED.CODE

        modalText.value = ['Сменное задание будет доступно для редактирования', 'и будут удалены все связанные рулоны для производства.', 'Продолжить?']
        modalType.value = 'danger'
        const result = await appModalAsync.value.show()             // показываем модалку и ждем ответ
        if (result) {
            task.common.status = FABRIC_TASK_STATUS.CREATED.CODE
            const res = await fabricsStore.changeFabricTaskDateStatus(task)
            console.log(res)

            const newTaskDay = await fabricsStore.getTasksByPeriod({start: task.date, end: task.date})
            console.log('newTaskDay: ', newTaskDay)

            task.machines = newTaskDay[0].machines
            task.common = newTaskDay[0].common

            // todo: сделать обработку ошибок + callout
        }

        return
    }

    // attract: Сменить статус с "Создано" на "Готов стежке". Обращаемся к API
    if (task.common.status === FABRIC_TASK_STATUS.CREATED.CODE) {

        // attract: проверяем, что у нас есть хотя бы один рулон в СЗ
        let taskContextRolls = 0
        for (const machine of Object.keys(task.machines)) {
            // console.log(machine)
            taskContextRolls += task.machines[machine].rolls.length
        }

        if (!taskContextRolls) {
            modalSimpleType.value = 'danger'
            modalSimpleText.value = 'В СЗ нет рулонов. Добавьте хотя бы один, чтобы создать задание для стежки.'
            modalSimpleShow.value = true
            modalSimpleClose()
            return
        }

        modalText.value = ['Сменное задание будет закрыто для редактирования', 'и будут сформированы рулоны для производства.', 'Продолжить?']
        modalType.value = 'primary'
        const result = await appModalAsync.value.show()             // показываем модалку и ждем ответ
        if (result) {
            task.common.status = FABRIC_TASK_STATUS.PENDING.CODE
            const res = await fabricsStore.changeFabricTaskDateStatus(task)
            console.log(res)
            // todo: сделать обработку ошибок + callout

            // console.log('pending: ', task)

            const newTaskDay = await fabricsStore.getTasksByPeriod({start: task.date, end: task.date})
            // console.log('newTaskDay: ', newTaskDay)

            task.machines = newTaskDay[0].machines
            task.common = newTaskDay[0].common

            // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
            rerender.forEach((_, index, array) => array[index]++)

        }
    }

}

// attract: Меняем активный день по клику на нем
const changeActiveTask = (task) => {
    taskData.forEach((t) => t.active = t.date === task.date)
    activeTask = taskData.find(t => t.active)
    // console.log('active_task: ', activeTask)

    // descr: Обновляем глобальную продуктивность для всех машин, чтобы исправить bug в отображении продуктивности общей
    fabricsStore.clearTaskGlobalProductivity()
    // attract: Перерисовываем все табы, чтобы исправить баг с отображением продуктивности общей
    rerender.forEach((_, index, array) => array[index]++)

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

// attract: Поднятое событие при клике на кнопку "Добавить рулон"
const addRoll = (newRoll, machine, task) => {

    const workTask = taskData.find(t => t.date === task.date)     // Получаем ссылку на СЗ на дату контекста
    workTask.machines[machine.TITLE].rolls.push(newRoll)
}


// attract: Поднятое событие при клике на кнопку "Сохранить рулон"
const saveTasks = async (saveData) => {

    const targetTask = taskData.find(t => t.date === saveData.task.date)

    targetTask.machines[saveData.machine.TITLE].rolls[saveData.index] = saveData.roll       // рулоны
    targetTask.machines[saveData.machine.TITLE].description = saveData.taskDescription      // общее описание

    // console.log('from saveTask: ', targetTask)
    // console.log('from taskData: ', taskData)

    const result = await fabricsStore.createFabricTask(targetTask)
    // console.log('SFC: ', result)

    // rerender[saveData.machine.ID]++
}

// attract: Поднятое событие при клике на кнопку "Сохранить общее описание к СМ"
const saveMachineDescription = async (saveData) => {

    const targetTask = taskData.find(t => t.date === saveData.task.date)
    targetTask.machines[saveData.machine.TITLE].description = saveData.taskDescription      // общее описание

    // console.log('from updateDescription: ', targetTask)
    // console.log('from updateDescription: ', taskData)

    const result = await fabricsStore.createFabricTask(targetTask)
    console.log('SFC: ', result)

    rerender[saveData.machine.ID]++     // Нужно для обновления трудозатрат
}


// attract: Поднятое событие при клике на кнопку "Удалить рулон"
const deleteTasks = async (deleteData) => {
    console.log('deleteTasks: ', deleteData)

    // Находим ссылку на СЗ на дату контекста
    const targetTask = taskData.find(t => t.date === deleteData.task.date)
    // Удаляем рулон из массива
    targetTask.machines[deleteData.machine.TITLE].rolls
        = targetTask.machines[deleteData.machine.TITLE].rolls.filter(r => r.id !== deleteData.id)

    // console.log(targetTask)

    // activeTask.machines[deleteData.machine.TITLE].rolls = targetTask.machines[deleteData.machine.TITLE].rolls

    // attract: Если deleteData.id === 0 - это новый рулон, который еще не сохранился в БД
    // attract: Иначе удаляем его из БД
    if (deleteData.id) {
        // console.log('НеПустой рулон')

        const result = await fabricsStore.deleteFabricTaskRollById(deleteData.id)
        // console.log('SFC: ', result)
    }

    rerender[deleteData.machine.ID]++
    // console.log(deleteData.machine.ID)
    // console.log(rerender)
}


// attract: Поднятое событие при клике на кнопку "Оптимизировать трудозатраты"
const optimizeLabor = (machine, task) => {

}

// attract: Поднятое событие при клике на кнопку "Персонал", точнее, его сохранение
const selectWorkers = async (workersList) => {

    workersList = workersList.filter(worker => worker.checked)
    console.log('selectWorkers: ', workersList)
    console.log('activeTask: ', activeTask)

    // Warning: Тут отправляем на сервер ключ-значение вида {"worker_id":1,"record_id":1}
    // warning: чтобы синхронизировать данные в таблице worker_records
    const workersIds = workersList.map(worker => ({worker_id: worker.id, record_id: worker.record_id}))
    // console.log(workersIds)

    const res = await fabricsStore.updateFabricTaskWorkers(activeTask.common.id, workersIds)
    // console.log(res)

    const newTaskDay = await fabricsStore.getTasksByPeriod({start: activeTask.date, end: activeTask.date})
    // console.log('newTaskDay: ', newTaskDay)

    activeTask.workers = newTaskDay[0].workers
    activeTask.common = newTaskDay[0].common
    // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
    rerender.forEach((_, index, array) => array[index]++)

}

// attract: Сохраняет общий комментарий ко дню СЗ
const updateTaskDescription = async (description) => {
    activeTask.common.description = description
    const res = await fabricsStore.changeFabricTaskDateStatus(activeTask)
    // console.log('description: ', description)
    // console.log(res)
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





