<template>

    <div v-if="!isLoading" class="mt-2 ml-2">

        <!-- __ Выводим даты + статусы + сервисные кнопки   -->
        <div class="flex h-[165px] m-3">
            <div v-for="task in taskData" :key="task.date">

                <!-- __ Рамка выбора даты -->
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

        <!-- __ Выводим табы, если есть СЗ -->
        <div v-if="activeTask.common.status !== FABRIC_TASK_STATUS.UNKNOWN.CODE">

            <!-- __ Выводим табы, если СМ активна или статус СЗ - "Выполнено" или "Выполняется" -->
            <div class="flex flex-row justify-start items-center m-3">
                <div v-for="tab in tabs" :key="tab.id">
                    <div
                        v-if="tab.enabled || activeTask.common.status === FABRIC_TASK_STATUS.RUNNING.CODE || activeTask.common.status === FABRIC_TASK_STATUS.DONE.CODE"
                        :class="tab.shown ? 'bg-blue-200 border-2 border-blue-400 rounded-lg' : ''">
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

                <!-- __ Общая вкладка -->
                <div v-if="tabs.common.shown">
                    <TheTaskCommonInfo
                        :key="rerender[0]"
                        :task="activeTask"
                        @saveTaskDescription="updateTaskDescription"
                        @select-workers="selectWorkers"
                    />
                </div>

                <!-- __ Содержимое Стегальной машины -->
                <div v-for="tab in tabs" :key="tab.id">
                    <div v-if="tab.shown && tab.hasOwnProperty('machine')">
                        <TheTaskMachine
                            :key="rerender[tab.machine.ID]"
                            :machine="tab.machine"
                            :task="activeTask"
                            @add-roll="addRoll"
                            @optimize-labor="optimizeLabor"
                            @save-task-record="saveTasks"
                            @delete-task-record="deleteTasksRecord"
                            @save-machine-description="saveMachineDescription"
                            @change-rolls-position="changeRollsPosition"
                            @save-rolls-position="saveRollsPosition"
                        />
                    </div>
                </div>


                <!--&lt;!&ndash; TODO: Убрать дублирование кода &ndash;&gt;-->
                <!--&lt;!&ndash; attract: Американец&ndash;&gt;-->
                <!--&lt;!&ndash; warning: key: - для реактивности &ndash;&gt;-->
                <!--&lt;!&ndash; todo: доработать, потому, что task будем получать в самом компоненте &ndash;&gt;-->
                <!--<div v-if="tabs.american.shown">-->
                <!--    <TheTaskMachine-->
                <!--        :key="rerender[FABRIC_MACHINES.AMERICAN.ID]"-->
                <!--        :machine="FABRIC_MACHINES.AMERICAN"-->
                <!--        :task="activeTask"-->
                <!--        @add-roll="addRoll"-->
                <!--        @optimize-labor="optimizeLabor"-->
                <!--        @save-task-record="saveTasks"-->
                <!--        @delete-task-record="deleteTasksRecord"-->
                <!--        @save-machine-description="saveMachineDescription"-->
                <!--        @change-rolls-position="changeRollsPosition"-->
                <!--        @save-rolls-order="saveRollsOrder"-->
                <!--    />-->

                <!--</div>-->

                <!--&lt;!&ndash;attract: Немец&ndash;&gt;-->
                <!--<div v-if="tabs.german.shown">-->
                <!--    <TheTaskMachine-->
                <!--        :key="rerender[FABRIC_MACHINES.GERMAN.ID]"-->
                <!--        :machine="FABRIC_MACHINES.GERMAN"-->
                <!--        :task="activeTask"-->
                <!--        @add-roll="addRoll"-->
                <!--        @optimize-labor="optimizeLabor"-->
                <!--        @save-task-record="saveTasks"-->
                <!--        @delete-task-record="deleteTasksRecord"-->
                <!--        @save-machine-description="saveMachineDescription"-->
                <!--        @change-rolls-position="changeRollsPosition"-->
                <!--        @save-rolls-order="saveRollsOrder"-->
                <!--    />-->
                <!--</div>-->

                <!--&lt;!&ndash;attract: Китаец&ndash;&gt;-->
                <!--<div v-if="tabs.china.shown">-->
                <!--    <TheTaskMachine-->
                <!--        :key="rerender[FABRIC_MACHINES.CHINA.ID]"-->
                <!--        :machine="FABRIC_MACHINES.CHINA"-->
                <!--        :task="activeTask"-->
                <!--        @add-roll="addRoll"-->
                <!--        @optimize-labor="optimizeLabor"-->
                <!--        @save-task-record="saveTasks"-->
                <!--        @delete-task-record="deleteTasksRecord"-->
                <!--        @save-machine-description="saveMachineDescription"-->
                <!--        @change-rolls-position="changeRollsPosition"-->
                <!--        @save-rolls-order="saveRollsOrder"-->
                <!--    />-->
                <!--</div>-->

                <!--&lt;!&ndash;attract: Китаец&ndash;&gt;-->
                <!--<div v-if="tabs.korean.shown">-->
                <!--    <TheTaskMachine-->
                <!--        :key="rerender[FABRIC_MACHINES.KOREAN.ID]"-->
                <!--        :machine="FABRIC_MACHINES.KOREAN"-->
                <!--        :task="activeTask"-->
                <!--        @add-roll="addRoll"-->
                <!--        @optimize-labor="optimizeLabor"-->
                <!--        @save-task-record="saveTasks"-->
                <!--        @delete-task-record="deleteTasksRecord"-->
                <!--        @save-machine-description="saveMachineDescription"-->
                <!--        @change-rolls-position="changeRollsPosition"-->
                <!--        @save-rolls-order="saveRollsOrder"-->
                <!--    />-->
                <!--</div>-->

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
import { ref, reactive, watch, onMounted } from 'vue'
// import { useRoute, useRouter } from 'vue-router'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
    FABRICS_NULLABLE,
    TASK_DRAFT,
    // TEST_FABRICS,
} from '@/app/constants/fabrics.js'

import { cloneShallow } from '@/app/helpers/helpers_lib.js'
import { log } from '@/app/helpers/helpers.js'

import {
    getTitleByFabricTaskStatusCode,
    getStyleTypeByFabricTaskStatusCode,
    getFabricTasksPeriod,
    addEmptyFabricTasks,
    // fillFabricsDisplayNames
} from '@/app/helpers/manufacture/helpers_fabric.js'

import {
    getDayOfWeek,
    formatDate,
    compareDatesLogic,
    getDayOfWeekStyle,
    // isToday,
    // isWorkingDay,
    // getISOFromLocaleDate,
} from '@/app/helpers/helpers_date.js'

import TheTaskCommonInfo
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskCommonInfo.vue'

import TheTaskMachine
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_manage/TheTaskMachine.vue'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'
import { catchErrorHandler } from '@/app/helpers/helpers_checks.ts'

const isLoading = ref(true)
// __ End Loader

// const route = useRoute()
// const router = useRouter()

const fabricsStore = useFabricsStore()

// __ Получаем список всех стегальных машин
const fabricsMachines = ref([])
const getFabricsMachines = async () => {
    const machines = await fabricsStore.getFabricsMachines()
    fabricsMachines.value = machines.filter(machine => machine.id !== 0).sort((a, b) => a.id - b.id) // сортируем по id, без id == 0
}

// __ Получаем все ткани и запоминаем в хранилище
let fabrics = []  // загружаем после монтирования
const getFabrics = async () => {
    fabrics = await fabricsStore.getFabrics(true)
    fabrics.unshift(FABRICS_NULLABLE)                   // добавляем пустой элемент в начало массива
    fabricsStore.fabricsMemory = fabrics
    // log(fabrics)
}


// __ Подготавливаем данные
let tasksPeriod = null
let tasks = []
let taskData = null
const activeTask = ref(null)

const getTasks = async () => {

    // __ Получаем период отображения сменного задания
    tasksPeriod = getFabricTasksPeriod()

    // __ Получаем сами сменные задания
    tasks = await fabricsStore.getTasksByPeriod(tasksPeriod)

    // __ формируем полный (дополненный) массив сменных заданий
    taskData = reactive(addEmptyFabricTasks(tasks, tasksPeriod))

    // __ Сортируем массив рулонов ОПП + сортируем массив физических рулонов СМ по позиции
    taskData.forEach(task => {
        Object.keys(task.machines).forEach(machine => {
            // по ОПП
            task.machines[machine].rolls = task.machines[machine].rolls.sort((a, b) => a.roll_position - b.roll_position)
            // по позиции
            task.machines[machine].rolls.forEach(roll => {
                roll.rolls_exec = roll.rolls_exec.sort((a, b) => a.position - b.position)
            })
        })
    })

    // __ Ссылка на активное СЗ
    activeTask.value = taskData.find(t => t.active)

    console.log('tasksPeriod:', tasksPeriod)
    console.log('tasks:', tasks)
    console.log('taskData: ', taskData)
    console.log('activeTask', activeTask.value)
}

// __ Устанавливаем активную вкладку даты по дате
const setActiveTaskByDate = (date) => {
    taskData.forEach(task => {
        if (date === task.date) {
            task.active = true
            activeTask.value = task
        } else {
            task.active = false
        }
    })
}


// attract: Тип для модального окна
const modalType = ref('danger')

// attract: Задаем отображение вкладок (Общие данные, Американец, Немец, Китаец, Кореец)
const tabs = reactive({
    common: {
        id: 1,
        shown: false,
        enabled: true,
        name: ['Общие', 'данные'],
        typePassive: 'warning'
    },
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

// __ Устанавливаем только активные машины
const setEnabledTabs = () => {
    Object.keys(tabs).forEach(tab => {

        if (tabs[tab].hasOwnProperty('machine')) {
            const machine = fabricsMachines.value.find(m => m.id === tabs[tab].machine.ID)
            tabs[tab].enabled = machine.active
            // log(machine.active)
        }

    })
}
// setEnabledTabs()
// log(tabs)
//__ --------------------------------


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
        let canDelete = true

        console.log('machines: ', task.machines)

        Object.keys(task.machines).forEach((machine) => {

            // let i = 0
            // canDelete &&= !task.machines[machine].rolls.some(roll => {
            //     console.log(i, roll.editable)
            //     i++
            //     return !roll.editable
            // })
            canDelete &&= !task.machines[machine].rolls.some(roll => !roll.editable)
            // task.machines[machine].rolls.forEach(roll => {canDelete &&= roll.editable})
        })

        // console.log('canDelete: ', canDelete)
        // return

        if (!canDelete) {
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

            fabricsStore.globalOrderManageChangeFlag = false

            // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
            rerender.forEach((_, index, array) => array[index]++)
        }
        return
    }

    // __ Сменить статус c "Не создано" на "Создано". Обращаемся к API
    if (task.common.status === FABRIC_TASK_STATUS.UNKNOWN.CODE) {
        task.common.status = FABRIC_TASK_STATUS.CREATED.CODE
        const res = await fabricsStore.changeFabricTaskDateStatus(task)
        console.log(res)

        await getTasks()
        setActiveTaskByDate(task.date)
        // const newTaskDay = await fabricsStore.getTasksByPeriod({start: task.date, end: task.date})
        // console.log('created: newTaskDay: ', newTaskDay)
        //
        // task.machines = newTaskDay[0].machines
        // task.common = newTaskDay[0].common

        // fabricsStore.globalOrderManageChangeFlag = false

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

            await getTasks()
            setActiveTaskByDate(task.date)

            // const newTaskDay = await fabricsStore.getTasksByPeriod({start: task.date, end: task.date})
            // console.log('newTaskDay: ', newTaskDay)
            //
            // task.machines = newTaskDay[0].machines
            // task.common = newTaskDay[0].common
            //
            // fabricsStore.globalOrderManageChangeFlag = false
            // // sortTaskByPosition()
            //
            // // todo: сделать обработку ошибок + callout
        }

        return
    }

    // __ Сменить статус с "Создано" на "Готов стежке". Обращаемся к API
    if (task.common.status === FABRIC_TASK_STATUS.CREATED.CODE) {

        // __ Проверяем, что у нас есть хотя бы один рулон в СЗ
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

            await getTasks()
            setActiveTaskByDate(task.date)

            // // todo: сделать обработку ошибок + callout
            //
            // // console.log('pending: ', task)
            //
            // const newTaskDay = await fabricsStore.getTasksByPeriod({start: task.date, end: task.date})
            // // console.log('newTaskDay: ', newTaskDay)
            //
            // task.machines = newTaskDay[0].machines
            // task.common = newTaskDay[0].common
            //
            // // fabricsStore.globalOrderManageChangeFlag = false
            // // sortTaskByPosition()
            // // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
            rerender.forEach((_, index, array) => array[index]++)

        }
    }

}


// __ Меняем активный день по клику на нем
const changeActiveTask = (task) => {
    taskData.forEach((t) => t.active = t.date === task.date)
    activeTask.value = taskData.find(t => t.active)
    // console.log('active_task: ', activeTask.value)

    setActiveTaskAndTab()

    // Сбрасываем флаг изменения порядка рулонов
    // fabricsStore.globalOrderManageChangeFlag = false

    // __ Обновляем глобальную продуктивность для всех машин, чтобы исправить bug в отображении общей продуктивности
    fabricsStore.clearTaskGlobalProductivity()

    // __ Перерисовываем все табы, чтобы исправить баг с отображением продуктивности общей
    rerender.forEach((_, index, array) => array[index]++)

}


// __ Переключаем выбранную вкладку
const changeTab = (selectedTab) => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = tabs[tab].id === selectedTab.id
        }
    }
    log('tabs: ', tabs)
    setActiveTaskAndTab()
}

// __ Сбрасываем все вкладки в false, потому что в некоторых ситуациях появляется мультивыбор
const resetTabs = () => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = false
        }
    }
    // console.log(tabs)
}

// resetTabs()                                 // сбрасываем все табы
// tabs.common.shown = true                    // делаем вкладку "общие данные" активной, чтобы запустить реактивность


const TASK_TAB_PREFIX = 'TASK_TAB'
// __ Устанавливаем активное СЗ и вкладку в LocalStorage
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
            const findTask = taskData.find(t => t.date === data.activeTaskDate)
            if (findTask) {
                changeActiveTask(findTask)
                resetTabs()
                const findTab = Object.keys(tabs).find(tab => tabs[tab].id === data.activeTabId)
                if (findTab) {
                    tabs[findTab].shown = true
                }
            }
        }
    } catch (e) {
        catchErrorHandler('LocalStorage: ', e)
    }
}


// __ Определяем тип таба (цвет) в зависимости от наличия СЗ
const getTabType = (tab) => {

    // если вкладка активна
    // if (tab.shown) return 'primary'
    if (tab.hasOwnProperty('machine')) {

        // если неактивна, но есть СЗ
        if (activeTask.value.machines[tab.machine.TITLE].rolls.length > 0) return 'success'
    }
    return tab.typePassive
}

// __ Пересчитывает позицию рулонов в массиве + сохраняет по необходимости
const changeRollsPosition = (machine, task) => {
    console.log('from changeRollsPosition!!!')
    const findTask = taskData.find(t => t.date === task.date)     // Получаем ссылку на СЗ на дату контекста

    // если не находим СЗ или там нет рулонов, то выходим
    if (!findTask || !findTask.machines[machine.TITLE].rolls) return

    findTask.machines[machine.TITLE].rolls.forEach((roll, index) => roll.roll_position = index + 1)

    // console.log('workTask: ', workTask)
}

// __ Поднятое событие при клике на кнопку "Сохранить порядок рулонов"
const saveRollsPosition = async (machine, task) => {
    console.log('from saveRollsPosition!!!: ', machine, task)

    // debugger
    fabricsStore.globalOrderManageChangeFlag = true

    const targetTask = taskData.find(t => t.date === task.date)     // Получаем ссылку на СЗ на дату контекста

    const result = await fabricsStore.changeContextOrder(task.id, machine.ID, task.machines[machine.TITLE].rolls)

    const orderContext = await fabricsStore.getOrderContext(task.id, machine.ID)
    console.log('orderContext: ', orderContext)

    task.machines[machine.TITLE].rolls = orderContext.sort((a, b) => a.roll_position - b.roll_position)

    fabricsStore.globalOrderManageChangeFlag = false
    // log('workTask: ', targetTask)
}

const saveRollsPosition_Old = async (machine, task) => {
    const targetTask = taskData.find(t => t.date === task.date)     // Получаем ссылку на СЗ на дату контекста
    const result = await fabricsStore.createFabricTask(targetTask)
    fabricsStore.globalOrderManageChangeFlag = false
}
//line ----------------------


// attract: Поднятое событие при клике на кнопку "Добавить рулон"
const addRoll = (newRoll, machine, task) => {

    const workTask = taskData.find(t => t.date === task.date)     // Получаем ссылку на СЗ на дату контекста
    workTask.machines[machine.TITLE].rolls.push(newRoll)
    changeRollsPosition(machine, task)

    // console.log('workTask addRoll: ', workTask)
}


// __ Поднятое событие при клике на кнопку "Сохранить рулон"
const saveTasks = async (saveData) => {
    const result = await fabricsStore.addOrderContextRoll(saveData.task.id, saveData.machine.ID, saveData.roll)
    // console.log('from saveTask: ', saveData)
    // console.log(result)

    // const targetTask = taskData.find(t => t.date === saveData.task.date)
    // targetTask.machines[saveData.machine.TITLE].rolls[saveData.index] = saveData.roll       // рулоны
    // targetTask.machines[saveData.machine.TITLE].description = saveData.taskDescription      // общее описание

    // const result = await fabricsStore.createFabricTask(targetTask)
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


// __ Поднятое событие при клике на кнопку "Удалить рулон"
const deleteTasksRecord = async (deleteData) => {
    console.log('deleteTasksRecord: ', deleteData)

    // __ Если deleteData.id === 0 - это новый рулон, который еще не сохранился в БД
    // __ Иначе удаляем его из БД
    if (deleteData.id) {
        const result = await fabricsStore.deleteFabricTaskRollById(deleteData.id)
    }

    // __ Пересчитываем позиции рулонов
    changeRollsPosition(deleteData.machine, deleteData.task)

    // __ Сохраняем в БД
    await saveRollsPosition(deleteData.machine, deleteData.task)

    // Удаляем рулон из массива
    // targetTask.machines[deleteData.machine.TITLE].rolls
    //     = targetTask.machines[deleteData.machine.TITLE].rolls.filter(r => r.id !== deleteData.id)

    rerender[deleteData.machine.ID]++
}


// attract: Поднятое событие при клике на кнопку "Оптимизировать трудозатраты"
const optimizeLabor = (machine, task) => {

}

// attract: Поднятое событие при клике на кнопку "Персонал", точнее, его сохранение
const selectWorkers = async (workersList) => {

    workersList = workersList.filter(worker => worker.checked)
    console.log('selectWorkers: ', workersList)
    console.log('activeTask: ', activeTask.value)

    // Warning: Тут отправляем на сервер ключ-значение вида {"worker_id":1,"record_id":1}
    // warning: чтобы синхронизировать данные в таблице worker_records
    const workersIds = workersList.map(worker => ({worker_id: worker.id, record_id: worker.record_id}))
    // console.log(workersIds)

    const res = await fabricsStore.updateFabricTaskWorkers(activeTask.value.common.id, workersIds)
    // console.log(res)

    const newTaskDay = await fabricsStore.getTasksByPeriod({start: activeTask.value.date, end: activeTask.value.date})
    // console.log('newTaskDay: ', newTaskDay)

    activeTask.value.workers = newTaskDay[0].workers
    activeTask.value.common = newTaskDay[0].common
    // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
    rerender.forEach((_, index, array) => array[index]++)

}

// attract: Сохраняет общий комментарий ко дню СЗ
const updateTaskDescription = async (description) => {
    activeTask.value.common.description = description
    const res = await fabricsStore.changeFabricTaskDateStatus(activeTask.value)
    // console.log('description: ', description)
    // console.log(res)
}


// attract: Создаем реактивную переменную и вешаем ее ключом на компоненты для ререндеринга
const rerender = reactive([0, 0, 0, 0, 0])

watch(() => taskData, async (newValue) => {

    // rerender.value++
    // console.log('taskData: changed: ', rerender)

}, {deep: true})

// __ Загрузка данных
onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getFabricsMachines()      // Получаем список машин
            setEnabledTabs()                // Устанавливаем только активные машины
            await getFabrics()              // Получаем список СЗ
            await getTasks()                // Получаем список СЗ
            resetTabs()                     // сбрасываем все табы
            tabs.common.shown = true        // делаем вкладку "общие данные" активной, чтобы запустить реактивность
            getActiveTaskAndTab()           // Получаем активное СЗ и вкладку из LocalStorage
            setActiveTaskAndTab()           // Устанавливаем активное СЗ и вкладку в LocalStorage
        },
        undefined,
        // false,
    )
    isLoading.value = false
})

</script>

<style scoped>

</style>





