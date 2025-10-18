<template>

    <div v-if="!isLoading" class="mt-2 ml-2">

        <!-- __ Выводим даты + статусы + сервисные кнопки   -->
        <div class="flex h-[165px] m-3">
            <div v-for="task in taskData" :key="task.date">

                <!-- __ Рамка выбора даты -->
                <div
                    :class="task.active ? 'bg-blue-200 border-2 border-blue-400 rounded-lg' : ''"
                    class="flex flex-col p-0.5  h-full">

                    <!-- __ Дата + день недели -->
                    <AppLabelMultiLine
                        :text="[formatDate(task.date), getDayOfWeek(task.date, false)]"
                        :type="getDayOfWeekStyle(task.date).toString()"
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

                    <!-- __ Выводим сервисные кнопки, только если дата больше или равна сегодняшней -->
                    <div v-if="taskDateConstraint(task.date)">

                        <!-- __ Первый ряд сервисных кнопок -->
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

                        <!-- __ Второй ряд сервисных кнопок - кнопка "Удалить" (Только для статуса "Создано") -->
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
        <div v-if="activeTask && activeTask.common.status !== FABRIC_TASK_STATUS.UNKNOWN.CODE">

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
                <!-- warning: key: - для реактивности -->
                <div v-for="tab in tabs" :key="tab.id">
                    <div v-if="tab.shown && tab.hasOwnProperty('machine') && !globalOrderContextIsLoading">
                        <TheTaskMachine
                            :key="rerender[tab.machine!.ID]"
                            :machine="tab.machine!"
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

            </div>

        </div>

    </div>


    <!-- __ Модальное окно отображения результатов оптимизации СЗ -->
    <KeepAlive>
    <TheTaskOptimize
        v-if="optimizedShow"
        ref="theTaskOptimize"
        :machine="optimizedMachine"
        :task="optimizedTask"
        :type="taskOptimizeType"
    />
    </KeepAlive>

    <AppModalAsyncMultilineTS
        ref="appModalAsyncTS"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />

    <AppCallout
        :show="modalSimpleShow"
        :text="modalSimpleText"
        :type="modalSimpleType"
    />

</template>

<script lang="ts" setup>
import { ref, reactive, onMounted, nextTick, } from 'vue'
import { storeToRefs } from 'pinia'

import type { IFabric, IFabricMachine, IGlobalProductivity, IRoll, ITaskItem, ITaskPeriod, ITimeContext } from '@/types'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    type IConstFabricMachine,
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
    FABRICS_NULLABLE,
    TASK_DRAFT, NEW_ROLL, FABRIC_DEFAULT_TUNING_TIME, type IMachineKey, FABRIC_ROLL_STATUS,
} from '@/app/constants/fabrics.js'

import { cloneDeep, cloneShallow } from '@/app/helpers/helpers_lib.js'

import {
    getTitleByFabricTaskStatusCode,
    getStyleTypeByFabricTaskStatusCode,
    getFabricTasksPeriod,
    addEmptyFabricTasks,
    getTuningTimeCasheKey,
    getTotalTunningProductivityGlobal,
} from '@/app/helpers/manufacture/helpers_fabric.js'

import {
    getDayOfWeek,
    formatDate,
    compareDatesLogic,
    getDayOfWeekStyle,
    formatTimeWithLeadingZeros,
} from '@/app/helpers/helpers_date.js'

import { catchErrorHandler } from '@/app/helpers/helpers_checks.ts'

import TheTaskCommonInfo
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskCommonInfo.vue'
import TheTaskOptimize
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_manage/TheTaskOptimize.vue'
import TheTaskMachine
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_manage/TheTaskMachine.vue'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'
// import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'


// __ End Loader

// Line -----------------------------------------------------------------------------
// Line ----------------------------- Интерфейсы-------------------------------------
// Line -----------------------------------------------------------------------------
type ITabKey = 'common' | 'american' | 'german' | 'china' | 'korean'

interface ITab {
    id: number,
    shown: boolean,
    enabled: boolean,
    name: string[],
    typePassive: string,
    machine?: IConstFabricMachine
}

// interface IInputData {
//     index: number
//     machine: IConstFabricMachine
//     roll: IRoll
//     task: ITaskItem
//     taskDescription: string
// }

interface ITempWorker {
    checked: boolean
    disabled: boolean
    id: number
    name: string
    record_id: number
    uniqID: string
}

interface IOptimizeResult {
    data: {
        minTime: number
        permutation: IRoll[] | []
        errors: string[] | null
    }
    statistic?: {
        uniquePics?: object[] | null
        keys?: number[]
        times?: any[]
        executionTime: string
        memory: string
    }
}

// Line -----------------------------------------------------------------------------

const fabricsStore = useFabricsStore()
const {tuningTimeCache} = fabricsStore
const {globalOrderContextIsLoading, globalOrderManageChangeFlag} = storeToRefs(fabricsStore)

const isLoading = ref(true)

const OPTIMIZE_PICS_LIMIT = 10      // ограничение на количество рисунков для оптимизации

// __ Подготавливаем данные
let fabrics: IFabric[] = []  // загружаем после монтирования
const fabricsMachines = ref<IFabricMachine[]>([])
let tasksPeriod: ITaskPeriod = {start: '', end: ''}
let tasks = []
let taskData: ITaskItem[] = []
const activeTask = ref<ITaskItem | undefined>(undefined)

const optimizedTask = ref<ITaskItem>(TASK_DRAFT) // СЗ для оптимизации
const optimizedMachine = ref<IConstFabricMachine>(FABRIC_MACHINES.UNKNOWN) // СМ для оптимизации
const optimizedShow = ref(false)

const getTasks = async () => {

    // __ Получаем период отображения сменного задания
    tasksPeriod = getFabricTasksPeriod() as ITaskPeriod

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
                roll.rolls_exec = roll.rolls_exec.map(item => ({...item, isTuning: false})) // добавляем поле isTuning (рулоны - физические)
            })
        })
    })

    // __ Ссылка на активное СЗ
    activeTask.value = taskData.find(t => t.active)

    console.log('tasksPeriod:', tasksPeriod)
    console.log('tasks:', tasks)
    // console.log('taskData: ', taskData)
    // console.log('activeTask', activeTask.value)
}

// __ Добавляем последний рулон в предыдущем СЗ
/*
const addLastExecRolls = async () => {
    for (let i = 0; i < taskData.length; i++) {

        if (taskData[i].common.status === FABRIC_TASK_STATUS.UNKNOWN.CODE ||
            taskData[i].common.status === FABRIC_TASK_STATUS.DONE.CODE ||
            taskData[i].common.status === FABRIC_TASK_STATUS.RUNNING.CODE) continue

        const workTask = taskData[i]

        for (const [machineKey, machine] of Object.entries(workTask.machines)) {

            const targetMachine = Object.keys(FABRIC_MACHINES).find(key => FABRIC_MACHINES[key as IMachineKey].TITLE === machineKey)
            if (targetMachine) {

                workTask.machines[machineKey].lastExecRoll =
                    await fabricsStore.getLastRoll(workTask.date, FABRIC_MACHINES[targetMachine as IMachineKey].ID)

            }

        }
    }
}
*/

// __ Добавляем переналадку по машине и СЗ
const addTuning = async (task: ITaskItem, machine: IConstFabricMachine) => {
    const rolls = task.machines[machine.TITLE].rolls
    if (!rolls.length) return

    let resultRolls
    let memRoll
    let tuningTime: ITimeContext

    memRoll = cloneDeep(NEW_ROLL)
    memRoll.fabric_id = 1
    // debugger
    if (task.machines[machine.TITLE].lastExecRoll) {
        memRoll = task.machines[machine.TITLE].lastExecRoll // Получаем последний рулон предыдущей смены
    }

    resultRolls = []

    let indexID = -100

    for (let j = 0; j < rolls.length; j++) {
        const roll = rolls[j]

        if (roll.fabric_id !== memRoll!.fabric_id) {

            // Получаем рисунки переналадки + время
            const picFromObj = fabrics.find(fabric => fabric.id === memRoll!.fabric_id)
            const picToObj = fabrics.find(fabric => fabric.id === roll.fabric_id)

            const picFrom = picFromObj?.picture.id ?? 0
            const picTo = picToObj?.picture.id ?? 0

            tuningTime = {from: picFrom, to: picTo, time: 0}
            if (picFrom !== picTo) {
                const key: string = getTuningTimeCasheKey(picFrom, picTo)
                if (tuningTimeCache[key]) {
                    tuningTime = tuningTimeCache[key]
                } else {
                    tuningTime = await fabricsStore.getFabricsPicturesBetweenTuningTime(picFrom, picTo)
                    tuningTimeCache[key] = tuningTime
                }
            }

            if (tuningTime.from !== tuningTime.to || tuningTime.time === null) {    // если есть время переналадки или если есть, но время не равно 0

                const tuningTimeRoll = cloneDeep(NEW_ROLL)
                // tuningTimeRoll.fabric = tuningTime.time
                //     ? `Переналадка с рис. ${picFromObj?.picture.name} на рис. ${picToObj?.picture.name}`
                //     : 'Переналадка (нет данных)'

                tuningTimeRoll.fabric =
                    `Переналадка с рис. ${picFromObj?.picture.name} на рис. ${picToObj?.picture.name}`
                    + (tuningTime.time ? '' : ' (нет данных)')

                tuningTimeRoll.roll_position = roll.roll_position - 0.5
                tuningTimeRoll.productivity = tuningTime.time ? tuningTime.time / 60 : FABRIC_DEFAULT_TUNING_TIME
                tuningTimeRoll.isTuning = true

                tuningTimeRoll.id = --indexID
                tuningTimeRoll.fabric_id = -1
                tuningTimeRoll.correct = true
                tuningTimeRoll.average_textile_length = 0
                tuningTimeRoll.average_fabric_length = 0
                tuningTimeRoll.fabric_rate = 1
                tuningTimeRoll.rolls_amount = 1
                tuningTimeRoll.average_textile_roll_length = tuningTimeRoll.productivity * tuningTimeRoll.productivity  // Чтобы получать время переналадки в часах после всех формул
                tuningTimeRoll.average_fabric_length = tuningTimeRoll.average_textile_roll_length                       // Чтобы получать время переналадки в часах после всех формул
                tuningTimeRoll.textile_layers_amount = 1

                resultRolls.push(tuningTimeRoll)
            }
        }

        resultRolls.push(roll)
        memRoll = cloneDeep(roll)
    }

    task.machines[machine.TITLE].rolls = resultRolls
}


// __ Добавляем переналадку
const addTuningRolls = async () => {
    for (let i = 0; i < taskData.length; i++) {

        if (taskData[i].common.status === FABRIC_TASK_STATUS.UNKNOWN.CODE) continue

        const workTask = taskData[i]

        for (const [machineKey, /*machine*/] of Object.entries(workTask.machines)) {

            const targetMachine = Object.keys(FABRIC_MACHINES).find(key => FABRIC_MACHINES[key as IMachineKey].TITLE === machineKey)
            if (targetMachine) {
                await addTuning(workTask, FABRIC_MACHINES[targetMachine as IMachineKey])
            }

        }
    }
}


// __ Устанавливаем активную вкладку даты по дате
const setActiveTaskByDate = (date: string) => {
    taskData.forEach(task => {
        if (date === task.date) {
            task.active = true
            activeTask.value = task
        } else {
            task.active = false
        }
    })
}

// __ Тип для модального окна
const modalType = ref<IColorTypes>('danger')
const modalText = ref<string[]>([])
const modalMode = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncTS = ref<any>(null)         // Получаем ссылку на модальное окно с асинхронной функцией

// __ Простое модальное окно для вывода ошибок и предупреждений
const modalSimpleType = ref('danger')
const modalSimpleText = ref('')
const modalSimpleShow = ref(false)
const modalSimpleClose = (delay = 5000) => setTimeout(() => modalSimpleShow.value = false, delay) // закрываем модалку

// __ Модальное окно для подтверждения оптимизации СЗ
const taskOptimizeType = ref<IColorTypes>('success')
const theTaskOptimize = ref<any>(null)         // Получаем ссылку на модальное окно с результатами оптимизации

// attract: Задаем отображение вкладок (Общие данные, Американец, Немец, Китаец, Кореец)
const tabs: Record<ITabKey, ITab> = reactive({
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
    (Object.keys(tabs) as ITabKey[]).forEach(tab => {

        if (tabs[tab].hasOwnProperty('machine')) {
            // if ('machine' in tabs[tab]) {
            const machine = fabricsMachines.value.find(m => m.id === tabs[tab].machine?.ID)
            if (machine) {
                tabs[tab].enabled = machine.active
            }
        }
    })
}
//__ --------------------------------


// attract: Условие на отображение сервисных кнопок под статусами
const serviceBtnShowCondition = (status: number) => {
    return !(status === FABRIC_TASK_STATUS.DONE.CODE || status === FABRIC_TASK_STATUS.RUNNING.CODE)
}

// attract: Получаем название сервисной кнопки в зависимости от статуса
const serviceBtnTitle = (status: number) => {
    const titles = ['Создать', 'На стежку', 'Вернуть статус', '', '']
    return titles[status]
}

// attract: Условие на отображение сервисной кнопки в зависимости от даты (больше или равна текущей)
const taskDateConstraint = (taskDate: string) => {
    const result = compareDatesLogic(new Date(), taskDate)
    return result || (result === undefined)
}


// attract Меняем статус СЗ по сервисной кнопке
const changeTaskStatus = async (task: ITaskItem, btnRow = 1) => {

    if (!task.active) return

    // attract: Удалить сменное задание. Обращаемся к API
    if (btnRow === 2 && task.common.status === FABRIC_TASK_STATUS.CREATED.CODE) {

        // attract: Проверяем, чтобы были только правильные статусы (не было не выполненных или переходящих)
        // считаем общее количество рулонов
        let canDelete = true

        console.log('machines: ', task.machines)

        Object.keys(task.machines).forEach((machine) => {
            canDelete &&= !task.machines[machine].rolls.some(roll => !roll.editable)
        })

        if (!canDelete) {
            modalSimpleType.value = 'danger'
            modalSimpleText.value = 'Задание не может быть удалено, потому присутствуют не выполненные или переходящие рулоны. Удалите их вручную.'
            modalSimpleShow.value = true
            modalSimpleClose()
            return
        }

        modalText.value = ['Сменное задания и все связанные с', 'ними данные будут удалены.', 'Продолжить?']
        modalType.value = 'danger'
        modalMode.value = 'confirm'

        const result = appModalAsyncTS.value.show()             // показываем модалку и ждем ответ
        if (result) {
            task.common.status = FABRIC_TASK_STATUS.UNKNOWN.CODE
            const res = await fabricsStore.changeFabricTaskDateStatus(task)
            console.log(res)

            const newTaskDay = cloneShallow(TASK_DRAFT)     // получаем копию нового сменного дня чтобы, почистить всю дату
            // console.log('newTaskDay: ', newTaskDay)
            task.common = newTaskDay.common
            task.machines = newTaskDay.machines
            task.workers = newTaskDay.workers

            globalOrderManageChangeFlag.value = false

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

        // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
        rerender.forEach((_, index, array) => array[index]++)

        return
    }

    // attract: Вернуть статус с "Готов к стежке" на "Создано". Обращаемся к API
    if (task.common.status === FABRIC_TASK_STATUS.PENDING.CODE) {

        modalText.value = ['Сменное задание будет доступно для редактирования', 'и будут удалены все связанные рулоны для производства.', 'Продолжить?']
        modalType.value = 'danger'
        modalMode.value = 'confirm'

        const result = await appModalAsyncTS.value.show()             // показываем модалку и ждем ответ
        if (result) {
            task.common.status = FABRIC_TASK_STATUS.CREATED.CODE
            const res = await fabricsStore.changeFabricTaskDateStatus(task)
            console.log(res)

            await getTasks()
            setActiveTaskByDate(task.date)

            // todo: сделать обработку ошибок + callout
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
        modalMode.value = 'confirm'

        const result = await appModalAsyncTS.value.show()             // показываем модалку и ждем ответ
        if (result) {
            task.common.status = FABRIC_TASK_STATUS.PENDING.CODE
            const res = await fabricsStore.changeFabricTaskDateStatus(task)
            console.log(res)

            await getTasks()
            setActiveTaskByDate(task.date)

            // todo: сделать обработку ошибок + callout
            // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
            rerender.forEach((_, index, array) => array[index]++)

        }
    }
}


// __ Меняем активный день по клику на нем
const changeActiveTask = (task: ITaskItem) => {
    taskData.forEach((t) => t.active = t.date === task.date)
    activeTask.value = taskData.find(t => t.active)
    // console.log('active_task: ', activeTask.value)

    setActiveTaskAndTab()

    // Сбрасываем флаг изменения порядка рулонов
    // globalOrderManageChangeFlag.value = false

    // __ Обновляем глобальную продуктивность для всех машин, чтобы исправить bug в отображении общей продуктивности
    fabricsStore.clearTaskGlobalProductivity()

    // __ Перерисовываем все табы, чтобы исправить баг с отображением продуктивности общей
    rerender.forEach((_, index, array) => array[index]++)

}

// __ Переключаем выбранную вкладку
const changeTab = (selectedTab: ITab) => {
    Object.values(tabs).forEach(tab => tab.shown = tab.id === selectedTab.id)
    setActiveTaskAndTab()
}

// __ Сбрасываем все вкладки в false, потому что в некоторых ситуациях появляется мультивыбор
const resetTabs = () => {
    Object.values(tabs).forEach(tab => tab.shown = false)
}

// __ Устанавливаем активное СЗ и вкладку в LocalStorage
const TASK_TAB_PREFIX = 'TASK_TAB'
const setActiveTaskAndTab = () => {
    try {
        const findTab = (Object.keys(tabs) as ITabKey[]).find(tab => tabs[tab].shown)
        localStorage.setItem(TASK_TAB_PREFIX, JSON.stringify({
            activeTaskDate: activeTask.value?.date,
            activeTabId: findTab ? tabs[findTab].id : tabs.common.id
        }))
    } catch (e) {
        catchErrorHandler('LocalStorage: ', e)
    }
}

// __ Получаем активное СЗ и вкладку из LocalStorage
const getActiveTaskAndTab = () => {
    try {
        const storedData = localStorage.getItem(TASK_TAB_PREFIX)
        let data: { activeTaskDate: string, activeTabId: number } | null = null
        if (storedData) {
            data = JSON.parse(storedData)
        }
        // const data: {activeTaskDate: string, activeTabId: string} | null = JSON.parse(localStorage.getItem(TASK_TAB_PREFIX))

        if (data) {
            const findTask = taskData.find(t => t.date === data.activeTaskDate)
            if (findTask) {
                changeActiveTask(findTask)
                resetTabs()
                const findTab = (Object.keys(tabs) as ITabKey[]).find(tab => tabs[tab].id === data.activeTabId)
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
const getTabType = (tab: ITab) => {

    // если вкладка активна
    // if (tab.shown) return 'primary'
    if (tab.hasOwnProperty('machine')) {

        // если неактивна, но есть СЗ
        if (activeTask.value && tab.machine && activeTask.value.machines[tab.machine.TITLE].rolls.length > 0) return 'success'
    }
    return tab.typePassive
}

// __ Пересчитывает позицию рулонов в массиве
const changeRollsPosition = (machine: IConstFabricMachine, task: ITaskItem) => {
    // убираем время переналадки
    task.machines[machine.TITLE].rolls = task.machines[machine.TITLE].rolls.filter(roll => !roll.isTuning)
    // переиндексируем
    task.machines[machine.TITLE].rolls.forEach((roll, index) => roll.roll_position = index + 1)



    // const findTask = taskData.find(t => t.date === task.date)     // Получаем ссылку на СЗ на дату контекста
    //
    // // если не находим СЗ или там нет рулонов, то выходим
    // if (!findTask || !findTask.machines[machine.TITLE].rolls) return
    //
    // // убираем время переналадки
    // findTask.machines[machine.TITLE].rolls = findTask.machines[machine.TITLE].rolls.filter(roll => !roll.isTuning)
    // // переиндексируем
    // findTask.machines[machine.TITLE].rolls.forEach((roll, index) => roll.roll_position = index + 1)

}

// __ Поднятое событие при клике на кнопку "Сохранить порядок рулонов"
const saveRollsPosition = async (machine: IConstFabricMachine, task: ITaskItem) => {
    // console.log('from saveRollsPosition!!!: ', machine, task)

    globalOrderManageChangeFlag.value = true
    globalOrderContextIsLoading.value = true

    // // const targetTask = taskData.find(t => t.date === task.date)     // Получаем ссылку на СЗ на дату контекста
    // await fabricsStore.changeContextOrder(task.id, machine.ID, task.machines[machine.TITLE].rolls)
    //
    // let orderContext = await fabricsStore.getOrderContext(task.id, machine.ID)
    // orderContext = orderContext.sort((a: IRoll, b: IRoll) => a.roll_position - b.roll_position)
    // // console.log('orderContext: ', orderContext)
    // // debugger
    //
    // task.machines[machine.TITLE].rolls = orderContext
    // // task.machines[machine.TITLE].rolls = orderContext.sort((a: IRoll, b: IRoll) => a.roll_position - b.roll_position)
    //
    // await addTuning(task, machine) // добавляем время переналадки
    //
    // console.log('rolls after tuning: ', task.machines[machine.TITLE].rolls)


    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await fabricsStore.changeContextOrder(task.id, machine.ID, task.machines[machine.TITLE].rolls) // сохраняем порядок рулонов
            const orderContext = await fabricsStore.getOrderContext(task.id, machine.ID) // получаем новый порядок рулонов
            task.machines[machine.TITLE].rolls = orderContext.sort((a: IRoll, b: IRoll) => a.roll_position - b.roll_position) // сортируем
            await addTuning(task, machine) // добавляем время переналадки
        },
        undefined,
        // false,
    )

    globalOrderContextIsLoading.value = false
    globalOrderManageChangeFlag.value = false
}

//line ----------------------


// __ Поднятое событие при клике на кнопку "Добавить рулон"
const addRoll = async (newRoll: IRoll, machine: IConstFabricMachine, task: ITaskItem) => {
    // const workTask = taskData.find(t => t.date === task.date)     // Получаем ссылку на СЗ на дату контекста
    // if (workTask) workTask.machines[machine.TITLE].rolls.push(newRoll)

    globalOrderContextIsLoading.value = true
    task.machines[machine.TITLE].rolls.push(newRoll)

    changeRollsPosition(machine, task)
    await addTuning(task, machine) // добавляем время переналадки
    globalOrderContextIsLoading.value = false
}


// __ Поднятое событие при клике на кнопку "Сохранить рулон"
const saveTasks = async (saveData: {
    index: number,
    roll: IRoll,
    machine: IConstFabricMachine,
    task: ITaskItem,
    taskDescription: string | null
}) => {
    /*const result =*/
    // await fabricsStore.addOrderContextRoll(saveData.task.id, saveData.machine.ID, saveData.roll)
    //
    // await getTasks()
    // await addTuningRolls()
    // setActiveTaskByDate(saveData.task.date)
    // rerender.forEach((_, index, array) => array[index]++)


    globalOrderManageChangeFlag.value = true
    globalOrderContextIsLoading.value = true

    // // const targetTask = taskData.find(t => t.date === task.date)     // Получаем ссылку на СЗ на дату контекста
    // await fabricsStore.changeContextOrder(task.id, machine.ID, task.machines[machine.TITLE].rolls)
    //
    // let orderContext = await fabricsStore.getOrderContext(task.id, machine.ID)
    // orderContext = orderContext.sort((a: IRoll, b: IRoll) => a.roll_position - b.roll_position)
    // // console.log('orderContext: ', orderContext)
    // // debugger
    //
    // task.machines[machine.TITLE].rolls = orderContext
    // // task.machines[machine.TITLE].rolls = orderContext.sort((a: IRoll, b: IRoll) => a.roll_position - b.roll_position)
    //
    // await addTuning(task, machine) // добавляем время переналадки
    //
    // console.log('rolls after tuning: ', task.machines[machine.TITLE].rolls)


    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await fabricsStore.addOrderContextRoll(saveData.task.id, saveData.machine.ID, saveData.roll)
            await getTasks()
            await addTuningRolls()
            setActiveTaskByDate(saveData.task.date)
        },
        undefined,
        // false,
    )
    rerender.forEach((_, index, array) => array[index]++)

    globalOrderContextIsLoading.value = false
    globalOrderManageChangeFlag.value = false
}


// __ Поднятое событие при клике на кнопку "Сохранить общее описание к СМ"
const saveMachineDescription = async (saveData: {
    machine: IConstFabricMachine,
    task: ITaskItem,
    taskDescription: string | null
}) => {

    const targetTask = taskData.find(t => t.date === saveData.task.date)
    if (targetTask) targetTask.machines[saveData.machine.TITLE].description = saveData.taskDescription      // общее описание

    await fabricsStore.createFabricTask(targetTask)

    rerender[saveData.machine.ID]++     // Нужно для обновления трудозатрат
}


// __ Поднятое событие при клике на кнопку "Удалить рулон"
const deleteTasksRecord = async (deleteData: { task: ITaskItem } & { machine: IConstFabricMachine } & IRoll) => {
    console.log('deleteTasksRecord: ', deleteData)

    // __ Если deleteData.id === 0 - это новый рулон, который еще не сохранился в БД
    // __ Иначе удаляем его из БД
    if (deleteData.id) {
        await fabricsStore.deleteFabricTaskRollById(deleteData.id)

        // Удаляем рулон из массива, чтобы верно пересчитать позицию
        const findTask = taskData.find(t => t.date === deleteData.task.date)     // Получаем ссылку на СЗ на дату контекста

        // если не находим СЗ или там нет рулонов, то выходим
        if (!findTask || !findTask.machines[deleteData.machine.TITLE].rolls) return

        findTask.machines[deleteData.machine.TITLE].rolls =
            findTask.machines[deleteData.machine.TITLE].rolls.filter(roll => roll.id !== deleteData.id)
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


// __ Поднятое событие при клике на кнопку "Оптимизировать трудозатраты"
const optimizeLabor = async (machine: IConstFabricMachine, task: ITaskItem) => {
    console.log('optimizeLabor')

    modalType.value = 'danger'
    modalMode.value = 'inform'

    // __ Ограничение: число рисунков переналадок не должно превышать 10
    // Оставляем только контекстные рулоны
    const contextRolls = task.machines[machine.TITLE].rolls.filter(roll => !roll.isTuning)
    // Получаем уникальные ID рисунков
    const uniquePics = new Set(contextRolls.map(roll => {
        const findFabric = fabrics.find(fabric => fabric.id === roll.fabric_id)
        if (findFabric) return findFabric.picture.id
    }))
    // Получаем последний рулон и добавляем уникальный рисунок
    const lastRoll = task.machines[machine.TITLE].lastExecRoll
    if (lastRoll) {
        const findFabric = fabrics.find(fabric => fabric.id === lastRoll.fabric_id)
        if (findFabric) uniquePics.add(findFabric.picture.id)
    }

    if (uniquePics.size > OPTIMIZE_PICS_LIMIT) {
        modalText.value = [
            'Количество уникальных рисунков превышает ' + OPTIMIZE_PICS_LIMIT + '.',
            'Оптимизация трудозатрат займет много времени.',
            'Уменьшите количество уникальных рисунков и',
            'повторите оптимизацию.'
        ]

        await appModalAsyncTS.value.show()             // показываем модалку и выходим
        return
    }

    // __ Ограничение. Не должно быть не выполненных рулонов
    let hasFalseContextRolls = false
    task.machines[machine.TITLE].rolls.forEach(roll => {
        roll.rolls_exec.forEach(rollExec => {
            hasFalseContextRolls ||= rollExec.status === FABRIC_ROLL_STATUS.FALSE.CODE
        })
    })

    if (hasFalseContextRolls) {
        modalText.value = [
            'В сменном задании есть',
            'не выполненные рулоны.',
            'Удалите и заново добавьте их и',
            'повторите оптимизацию.'
        ]

        await appModalAsyncTS.value.show()             // показываем модалку и выходим
        return
    }

    // __ Получаем результат оптимизации
    const optimizeResult: IOptimizeResult =
        await fabricsStore.optimizeOrderContext(task.id, machine.ID, true)

    console.log(optimizeResult)

    // __ Ошибка: не заданы все необходимые данные по перенастройкам
    if (optimizeResult.data.errors) {
        modalText.value = [
            'Не заданы данные по переналадкам:',
            ...optimizeResult.data.errors
        ]

        await appModalAsyncTS.value.show()             // показываем модалку и выходим
        return
    }

    // __ Проверяем, нужна ли оптимизация, или все уже оптимизировано
    const tuningTime = getTotalTunningProductivityGlobal(fabricsStore.globalTaskProductivity as unknown as IGlobalProductivity, machine)
    if (tuningTime === optimizeResult.data.minTime) {
        modalText.value = [
            'Оптимизация трудозатрат не требуется.',
            'Время переналадки: ' + formatTimeWithLeadingZeros(tuningTime, 'hour')
        ]

        await appModalAsyncTS.value.show()             // показываем модалку и выходим
        return
    }

    // __ Формируем объект отображения модалки с оптимизацией СЗ
    optimizedMachine.value = machine
    optimizedTask.value = cloneDeep(task)

    // __ Тут костыль, потому что возвращаем с сервера не совсем IRoll[]
    optimizeResult.data.permutation.forEach((roll: any) => roll.fabric = roll.fabric.name)

    optimizedTask.value.machines[optimizedMachine.value.TITLE].rolls = optimizeResult.data.permutation
    await addTuning(optimizedTask.value, optimizedMachine.value)    // добавляем время переналадки
    optimizedShow.value = true  // монтируем элемент

    await nextTick() // ждем монтирования элемента

    // console.log('opt rolls: ', optimizedTask.value.machines[optimizedMachine.value.TITLE].rolls)

    // __ Обрабатываем выбор
    const optimizeAnswer = await theTaskOptimize.value.show()
    if (!optimizeAnswer) return

    // console.log('start optimize')

    task.machines[machine.TITLE].rolls = optimizedTask.value.machines[optimizedMachine.value.TITLE].rolls
    changeRollsPosition(machine, task)        // меняем порядок рулонов
    await saveRollsPosition(machine, task)    // сохраняем порядок рулонов
}


// attract: Поднятое событие при клике на кнопку "Персонал", точнее, его сохранение
const selectWorkers = async (workersList: ITempWorker[]) => {

    workersList = workersList.filter(worker => worker.checked)
    console.log('selectWorkers: ', workersList)
    console.log('activeTask: ', activeTask.value)

    // Warning: Тут отправляем на сервер ключ-значение вида {"worker_id":1,"record_id":1}
    // warning: чтобы синхронизировать данные в таблице worker_records
    const workersIds = workersList.map(worker => ({worker_id: worker.id, record_id: worker.record_id}))
    // console.log(workersIds)

    if (activeTask.value) {
        /*const res =*/
        await fabricsStore.updateFabricTaskWorkers(activeTask.value.common.id, workersIds)
        // console.log(res)

        const newTaskDay = await fabricsStore.getTasksByPeriod({
            start: activeTask.value.date,
            end: activeTask.value.date
        })
        // console.log('newTaskDay: ', newTaskDay)

        activeTask.value.workers = newTaskDay[0].workers
        activeTask.value.common = newTaskDay[0].common
    }
    // увеличиваем счетчик рендеринга, чтобы обновить данные на странице
    rerender.forEach((_, index, array) => array[index]++)

}

// __ Получаем все ткани и запоминаем в хранилище
const getFabrics = async () => {
    fabrics = await fabricsStore.getFabrics(true)
    fabrics.unshift(FABRICS_NULLABLE)                   // добавляем пустой элемент в начало массива
    fabricsStore.fabricsMemory = fabrics
}

// __ Получаем список всех стегальных машин
const getFabricsMachines = async () => {
    const machines = await fabricsStore.getFabricsMachines() as IFabricMachine[]
    fabricsMachines.value = machines.filter(machine => machine.id !== 0).sort((a, b) => a.id - b.id) // сортируем по id, без id == 0
    console.log('fabricsMachines: ', fabricsMachines.value)
}


// attract: Сохраняет общий комментарий ко дню СЗ
const updateTaskDescription = async (description: string) => {
    if (activeTask.value) activeTask.value.common.description = description
    /*const res =*/
    await fabricsStore.changeFabricTaskDateStatus(activeTask.value)
    // console.log('description: ', description)
    // console.log(res)
}


// attract: Создаем реактивную переменную и вешаем ее ключом на компоненты для ререндеринга
const rerender = reactive([0, 0, 0, 0, 0])

// watch(() => taskData, async (newValue) => {
//     // rerender.value++
//     // console.log('taskData: changed: ', rerender)
// }, {deep: true})

// __ Загрузка данных
onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getFabricsMachines()      // Получаем список машин
            setEnabledTabs()                // Устанавливаем только активные машины
            await getFabrics()              // Получаем ПС
            await getTasks()                // Получаем список СЗ
            // await addLastExecRolls()        // Добавляем рулоны из последнего выполнения
            await addTuningRolls()          // Добавляем рулоны для переналадки
            resetTabs()                     // сбрасываем все табы
            tabs.common.shown = true        // делаем вкладку "общие данные" активной, чтобы запустить реактивность
            getActiveTaskAndTab()           // Получаем активное СЗ и вкладку из LocalStorage
            setActiveTaskAndTab()           // Устанавливаем активное СЗ и вкладку в LocalStorage

            // console.log('fabrics: ', fabrics)
        },
        undefined,
        // false,
    )

    // const time = await fabricsStore.getFabricsPicturesBetweenTuningTime(150, 150)
    // console.log('time: ', time)

    isLoading.value = false
})

</script>

<style scoped>

</style>

