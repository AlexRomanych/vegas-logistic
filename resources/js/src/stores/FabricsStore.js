// Хранилище для заказов

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {FABRIC_MACHINES} from '/resources/js/src/app/constants/fabrics.js'

import {jwtGet, jwtPost, jwtDelete, jwtUpdate, jwtPut, jwtPatch} from '/resources/js/src/app/utils/jwt_api'
import {openNewTab} from '/resources/js/src/app/helpers/helpers_service'

import axios from 'axios'


// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                                           // Префикс API
const URL_FABRICS = 'fabrics/'                                          // URL для получения списка ПС
const URL_FABRIC = 'fabric/'                                            // URL для получения ПС

const URL_FABRICS_MACHINES = 'fabrics/machines/'                        // URL для получения списка Стегальных Машин
const URL_FABRICS_MACHINE = 'fabrics/machine/'                          // URL для получения Стегальной Машины
const URL_FABRICS_MACHINE_SET_ACTIVE = 'fabrics/machine/set/active/'    // URL для переключения активности Стегальной Машины

const URL_FABRICS_UPLOAD = 'fabrics/upload/'                            // URL для загрузки ПС с диска
const URL_FABRIC_DELETE = 'fabrics/delete/'                             // URL для удаления ПС

const URL_FABRICS_PICTURES_UPLOAD = 'fabrics/pictures/upload/'          // URL для загрузки рисунков ПС с диска

const URL_FABRICS_ORDERS = 'fabrics/orders/'                            // URL для получения списка заказов для ПС
const URL_FABRICS_ORDERS_UPLOAD = 'fabrics/orders/upload/'              // URL для загрузки расхода ПС с диска из отчета 1С СВПМ
const URL_FABRICS_ORDERS_CLOSE = 'fabrics/orders/close/'                // URL для закрытия заказа
const URL_FABRICS_ORDERS_SET_ACTIVE = 'fabrics/orders/set/active/'      // URL для переключения активности заказа
// const URL_FABRIC_ORDERS_DELETE = 'fabrics/order/delete/'                // URL для удаления заказа

const URL_FABRIC_TASKS = 'fabrics/tasks/'                               // URL для получения списка СЗ для ПС
const URL_FABRIC_TASKS_LAST_DONE = 'fabrics/tasks/last/done/'           // URL для получения последнего выполненного СЗ
const URL_FABRIC_TASKS_CREATE = 'fabrics/tasks/create/'                 // URL для получения создания или обновления СЗ для ПС
const URL_FABRIC_TASKS_STATUS_CHANGE = 'fabrics/tasks/status/change/'   // URL для получения создания или обновления СЗ для ПС
const URL_FABRIC_TASKS_CONTEXT_DELETE = 'fabrics/tasks/context/delete/' // URL для удаления рулона из СЗ, созданного ОПП (FabricTaskContext)
const URL_FABRIC_TASKS_CONTEXT_EXPENSE_CREATE =
    'fabrics/tasks/context/expense/create/'                             // URL для создания СЗ, для ОПП (FabricTaskContext)
const URL_FABRIC_TASKS_CONTEXT_GET_NOT_DONE =
    'fabrics/tasks/context/not-done/'                                   // URL для получения СЗ, созданного ОПП (FabricTaskContext), где статус СЗ у FabricTask - не "Выполнен"

const URL_FABRIC_TASKS_WORKERS_UPDATE = 'fabrics/tasks/workers/update/' // URL для обновления списка сотрудников на СЗ

const URL_FABRIC_TASKS_EXECUTING_TASKS = 'fabrics/tasks/executing/'     // URL для получения списка выполняемых СЗ
const URL_FABRIC_TASKS_NOT_DONE_TASKS = 'fabrics/tasks/not-done/'       // URL для получения списка невыполненных СЗ'
const URL_FABRIC_TASKS_CLOSE = 'fabrics/tasks/close/'                   // URL для закрытия СЗ

const URL_FABRIC_TASKS_EXECUTE_ROLL_UPDATE =
    'fabrics/tasks/execute/roll/update/'                                // URL для обновления выполняемого рулона (FabricTaskContext)
const URL_FABRIC_TASKS_EXECUTE_ROLL_SET_REGISTERED =
    'fabrics/tasks/execute/roll/registered/'                            // URL для регистрации рулона в 1С
const URL_FABRIC_TASKS_EXECUTE_ROLL_SET_MOVED =
    'fabrics/tasks/execute/roll/moved/'                                 // URL для перемещения рулона на закрой
const URL_FABRIC_TASKS_ROLLS_GET_DONE = 'fabrics/tasks/rolls/done/'     // URL для получения всех выполненных рулонов
const URL_FABRIC_TASKS_ROLLS_GET_NOT_MOVED_TO_CUT  =
    'fabrics/tasks/rolls/done/'                                         // URL для получения всех выполненных рулонов

const URL_FABRIC_TEAM_NUMBER = 'fabrics/tasks/team/number/'             // URL для получения номера смены

export const useFabricsStore = defineStore('fabrics', () => {


    // attract: Список ПС, которые получили к отображению
    let fabricsCashe = []
    let fabricsMemory = []
    // const ordersShowTest = ref('123')
    const fabricsCasheIsChanged = ref(false)

    // attract: Маячок того, что в данный момент происходит редактирование какого-то ПС рулона
    const globalEditMode = ref(false)       // Начальное значение - режим просмотра

    // attract: Маячок выбора режима доступности ПС - Основные или Все доступные
    const globalFabricsMode = ref(true)    // Начальное значение - основные

    // attract: Массив с трудозатратами по СЗ
    const globalTaskProductivity = reactive({
        [FABRIC_MACHINES.AMERICAN.TITLE]: [],
        [FABRIC_MACHINES.GERMAN.TITLE]: [],
        [FABRIC_MACHINES.CHINA.TITLE]: [],
        [FABRIC_MACHINES.KOREAN.TITLE]: [],
    })

    // console.log(globalTaskProductivity)

    // attract: Сброс глобального массива трудозатрат СЗ
    const clearTaskGlobalProductivity = () => {
        globalTaskProductivity[FABRIC_MACHINES.AMERICAN.TITLE] = []
        globalTaskProductivity[FABRIC_MACHINES.GERMAN.TITLE] = []
        globalTaskProductivity[FABRIC_MACHINES.CHINA.TITLE] = []
        globalTaskProductivity[FABRIC_MACHINES.KOREAN.TITLE] = []
    }

    // info: Блок для обмена информацией между модулями с элементами управления СЗ и самим данными по СЗ

    // attract: Массив с активными рулонами на каждой стегальной машине
    const globalActiveRolls = reactive({
        [FABRIC_MACHINES.AMERICAN.TITLE]: null,
        [FABRIC_MACHINES.GERMAN.TITLE]: null,
        [FABRIC_MACHINES.CHINA.TITLE]: null,
        [FABRIC_MACHINES.KOREAN.TITLE]: null,
    })


    // attract: Переменная-флаг нажатия кнопки "Начать выполнение" рулона
    const globalStartExecuteRoll = ref(false)

    // attract: Переменная-флаг нажатия кнопки "Приостановить выполнение" рулона
    const globalPauseExecuteRoll = ref(false)

    // attract: Переменная-флаг нажатия кнопки "Возобновить выполнение" рулона
    const globalResumeExecuteRoll = ref(false)

    // attract: Переменная-флаг нажатия кнопки "Закончить выполнение" рулона
    const globalFinishExecuteRoll = ref(false)

    // attract: Переменная-флаг, которая определяет, какая инфа отображается в элементе выполнения рулонов (полная или сокращенная)
    const globalExecuteRollsInfo = ref(true)

    // attract: Переменная-флаг нажатия кнопки "Переходящий рулон"
    const globalExecuteMarkRollRolling = ref(false)

    // attract: Переменная-флаг нажатия кнопки "Невыполнено"
    const globalExecuteMarkRollFalse = ref(false)               // маяк кнопки
    const globalExecuteMarkRollFalseReason = ref('')            // причина невыполненного рулона

    // attract: Переменная-флаг нажатия кнопки "Отменено"
    const globalExecuteMarkRollCancel = ref(false)              // маяк кнопки
    const globalExecuteMarkRollCancelReason = ref('')           // причина отмененного рулона

    // attract: Переменная-флаг нажатия кнопки "Изменить метраж ткани" рулона
    const globalExecuteRollChangeTextile = ref(false)
    const globalExecuteRollChangeTextileLength = ref(0)         // новая длина рулона

    // attract: Переменная-флаг нажатия кнопки "Изменить комментарий" рулона
    const globalExecuteRollChangeDescription = ref(false)
    const globalExecuteRollChangeDescriptionText = ref('')      // текст комментария

    // attract: Переменная для хранения данных для select для учета ответственного за стегание рулона
    const globalSelectWorkers = reactive({})                   // для отображения рабочих в самом выполняемом рулоне
    const globalSelectWorkerId = ref(0)                         // для записи id ответственного
    const globalSelectWorkerFlag = ref(false)                   // маяк селекта

    // attract: Переменная-флаг, которая определяет, были ли изменения в календаре СЗ
    const globalCalendarChangeFlag = ref(false)
    // info----------------------------------------------------------------------------------------


    // attract: Массив с индексами рулонов, которые уже есть в СЗ, для того, чтобы исключить их из выбора при создании СЗ
    let globalRollsIndexes = reactive([])


    // Attract: Получаем с API список ПС
    const getFabrics = async (active = null) => {

        const result = await jwtGet(URL_FABRICS, {active})
        fabricsCashe.value = result.fabrics             // кэшируем
        console.log('store', result)

        return result.fabrics // все возвращается через Resource с ключем data
    }

    // Attract: Получаем с API ПС по id
    const getFabricById = async (id) => {
        const result = await jwtGet(URL_FABRIC + id)
        console.log('store: getFabricById: ', result)
        return result.fabric
    }


    // Attract: Обновление ПС
    const updateFabric = async (fabric) => {
        const result = await jwtUpdate(URL_FABRIC, fabric)
        console.log('store: updateFabric: ', result)
        fabricsCashe = []
        return result
    }

    // Attract: Создание ПС
    const createFabric = async (fabric) => {
        const result = await jwtPost(URL_FABRIC, {data: fabric})
        console.log('store: createFabric: ', result)
        fabricsCashe = []
        return result
    }

    // Attract: Загрузка ПС на сервер
    // fileData - данные файла, отправляем в RAW формате
    const uploadFabrics = async (fileData) => {

        // console.log(fileData)

        const headers = {
            'Content-Type': 'application/json',
            // 'Content-Type': 'application/octet-stream',
            // 'Content-Type': 'multipart/form-data',
            // 'Content-Type': 'application/x-www-form-urlencoded',
        }

        const response = await jwtPost(URL_FABRICS_UPLOAD, fileData, headers)
        const result = await response

        // openNewTab(result)

        // console.log(result)

        return result
    }

    //  Attract: Удаление ПС
    const deleteFabric = async (delFabricId = 0) => {
        if (delFabricId === 0) {
            return
        }

        const result = await jwtDelete(URL_FABRIC, {id: delFabricId})
        fabricsCashe = []
        return result
        // console.log(result)
    }

    // Attract: Загрузка ПС на сервер
    // fileData - данные файла, отправляем в RAW формате
    const uploadFabricsPictures = async (fileData) => {

        // console.log(fileData)

        const headers = {
            'Content-Type': 'application/json',
            // 'Content-Type': 'application/octet-stream',
            // 'Content-Type': 'multipart/form-data',
            // 'Content-Type': 'application/x-www-form-urlencoded',
        }

        const response = await jwtPost(URL_FABRICS_PICTURES_UPLOAD, fileData, headers)
        const result = await response

        // openNewTab(result)

        // console.log(result)

        return result
    }


    // Attract: Получаем с API список Стегальных машин
    const getFabricsMachines = async () => {
        const result = await jwtGet(URL_FABRICS_MACHINES)
        return result.machines
    }

    // Attract: Получаем с API Стегальную машину по id
    const getFabricsMachineById = async (id) => {
        const result = await jwtGet(URL_FABRICS_MACHINE + id)
        return result.machine
    }

    // Attract: Меняем статус стегальной машины по id
    const setFabricsMachineStatusById = async (id, active) => {
        const result = await jwtPatch(URL_FABRICS_MACHINE_SET_ACTIVE, {id, active})

        console.log('store: setFabricsMachineStatusById: ', result)

        return result.machine
    }

    // Attract: Загрузка расхода ПС на сервер из отчета 1С - СВПМ
    // fileData - данные файла, отправляем в RAW формате
    const uploadFabricsOrders = async (fileData) => {

        // console.log(fileData)

        const headers = {
            'Content-Type': 'application/json',
            // 'Content-Type': 'application/octet-stream',
            // 'Content-Type': 'multipart/form-data',
            // 'Content-Type': 'application/x-www-form-urlencoded',
        }

        const response = await jwtPost(URL_FABRICS_ORDERS_UPLOAD, fileData, headers)
        const result = await response

        // openNewTab(result)

        // console.log(result)

        return result
    }

    // attract: Получаем с API список ПС
    const getTasksByPeriod = async (params) => {

        const result = await jwtGet(URL_FABRIC_TASKS, params)
        // console.log('store', result)
        // debugger
        return result.data                                  // все возвращается через Resource с ключем data
    }

    // attract: Получаем с API последнее закрытое (выполненное) СЗ
    const getLastDoneFabricTask = async () => {
        const result = await jwtGet(URL_FABRIC_TASKS_LAST_DONE)
        console.log('store', result)
        return result.data                                  // все возвращается через Resource с ключем data
    }

    // attract: Создание СЗ
    const createFabricTask = async (task) => {
        const result = await jwtPut(URL_FABRIC_TASKS_CREATE, {data: task})
        console.log('store', result)
        return result
        debugger
    }

    // attract: Изменение статуса СЗ (именно для конкретной СМ)
    const changeFabricTaskStatus = async (task) => {
        const result = await jwtPatch(URL_FABRIC_TASKS_STATUS_CHANGE, {data: task})
        console.log('store', result)
        return result
        debugger
    }

    // attract: Изменение статуса СЗ (для всех СМ - для дня)
    const changeFabricTaskDateStatus = async (task) => {
        // console.log('debug')
        const result = await jwtPatch(URL_FABRIC_TASKS_STATUS_CHANGE, {data: task})
        console.log('store', result)
        return result
        debugger
    }

    // attract: Удаление рулона из СЗ по id созданного ОПП (FabricTaskContext)
    const deleteFabricTaskRollById = async (fabricTaskContextId = 0) => {
        if (fabricTaskContextId === 0) {
            return
        }

        const result = await jwtDelete(URL_FABRIC_TASKS_CONTEXT_DELETE, {id: fabricTaskContextId})
        console.log('store', result)
        return result
        // console.log(result)
    }

    // attract: Получение СЗ, созданного ОПП (FabricTaskContext), где статус СЗ у FabricTask - не "Выполнен"
    const getFabricTaskContextNotDone = async () => {
        const result = await jwtGet(URL_FABRIC_TASKS_CONTEXT_GET_NOT_DONE)
        console.log('store: getFabricTaskContextNotDone:', result)
        return result.data
        // console.log(result)
    }


    // attract: Создание СЗ из расхода ПС для ОПП (FabricTaskContext)
    const createContextExpense = async (taskContextData) => {
        const result = await jwtPut(URL_FABRIC_TASKS_CONTEXT_EXPENSE_CREATE, {data: taskContextData})
        console.log('store: createContextExpense:', result)
        return result.data
        // console.log(result)
    }


    // attract: Получаем с API номер смены по дате
    const getFabricTeamNumberByDate = async (date = null) => {
        const result = await jwtGet(URL_FABRIC_TEAM_NUMBER, {date})
        // console.log('store', result)
        return result.data                                  // все возвращается через Resource с ключем data
    }

    // attract: Обновление данных выполняемого рулона
    const updateExecuteRoll = async (rollData) => {
        // if (rollId === null) return
        // console.log('st_at', rollData.start_at.toISOString())
        // rollData.start_at = rollData.start_at.toLocaleString()
        const result = await jwtPut(URL_FABRIC_TASKS_EXECUTE_ROLL_UPDATE, {data: rollData})
        console.log('store', result)
        return result.data                                  // все возвращается через Resource с ключем data
    }

    // attract: Обновление списка сотрудников для дня СЗ
    const updateFabricTaskWorkers = async (taskId = 0, workerIds = []) => {
        console.log('debug: ', taskId, workerIds)
        const result = await jwtPut(URL_FABRIC_TASKS_WORKERS_UPDATE, {data: {task: taskId, workers: workerIds}})
        console.log('store: workers: ', result)
        return result.data                                  // все возвращается через Resource с ключем data
    }

    // Attract: Получаем с API список выполняемых СЗ до определенной даты
    // Descr: Если date = null, получаем весь список
    // Descr: Если date = '', получаем список до текущей даты
    // Descr: Если date = '05.05.2025', получаем список до даты '05.05.2025'
    const getFabricExecutingTasks = async (date = null) => {
        const result = await jwtGet(URL_FABRIC_TASKS_EXECUTING_TASKS, {date})
        console.log('store: executing: ', result)
        return result.data                                  // все возвращается через Resource с ключем data
    }

    // Attract: Получаем с API список СЗ, у которых статус не "Выполнено" до определенной даты
    // Descr: Если date = null, получаем весь список
    // Descr: Если date = '', получаем список до текущей даты
    // Descr: Если date = '05.05.2025', получаем список до даты '05.05.2025'
    const getFabricNotDoneTasks = async (date = null) => {
        const result = await jwtGet(URL_FABRIC_TASKS_NOT_DONE_TASKS, {date})
        console.log('store: not-done: ', result)
        return result.data                                  // все возвращается через Resource с ключем data
    }

    // Attract: Закрываем СЗ по дате
    const closeFabricTasks = async (date = null) => {
        const result = await jwtGet(URL_FABRIC_TASKS_CLOSE, {date})
        console.log('store: task-close: ', result)
        return result.data                                  // все возвращается через Resource с ключем data
    }

    // Attract: Получаем с API список заказов ПС (расход ПС)
    const getFabricsOrders = async () => {
        const result = await jwtGet(URL_FABRICS_ORDERS)
        console.log('store: fabric-orders: ', result)
        return result.data
    }

    // Attract: Меняем статус заказа с расходом ПС для отображения в расчетах
    const setFabricOrderActive = async (id = 0, active = true) => {
        const result = await jwtPatch(URL_FABRICS_ORDERS_SET_ACTIVE, {id, active})
        console.log('store: toggle-active-fabric-order: ', result)
        return result.data
    }

    // Attract: Закрываем заказ с расходом ПС
    const closeFabricOrder = async (id = 0) => {
        const result = await jwtPatch(URL_FABRICS_ORDERS_CLOSE, {id})
        console.log('store: close-fabric-order: ', result)
        return result.data
    }

    // Attract: Получаем с API список всех выполненных рулонов
    const getNotAcceptedToCutRolls = async () => {
        const result = await jwtGet(URL_FABRIC_TASKS_ROLLS_GET_DONE)
        console.log('store: getDoneRolls: ', result)
        return result.data
    }


    // Attract: Изменить статус регистрации рулона в 1С
    const setRollRegisteredStatus = async (id = 0, status = 0) => {
        const result = await jwtPatch(URL_FABRIC_TASKS_EXECUTE_ROLL_SET_REGISTERED, {id, status})
        console.log('store: set-roll-registered-status: ', result)
        return result.data
    }


    // Attract: Изменить статус перемещения рулона на закрой
    const setRollMovedStatus = async (id = 0, status = 0) => {
        const result = await jwtPatch(URL_FABRIC_TASKS_EXECUTE_ROLL_SET_MOVED, {id, status})
        console.log('store: set-roll-moved-status: ', result)
        return result.data
    }



    return {
        fabricsCashe,
        fabricsMemory,
        fabricsCasheIsChanged,
        globalEditMode,
        globalFabricsMode,
        globalTaskProductivity, clearTaskGlobalProductivity,
        globalRollsIndexes,
        globalActiveRolls,
        globalExecuteRollsInfo, globalExecuteMarkRollRolling, globalExecuteMarkRollFalse, globalExecuteMarkRollFalseReason,
        globalExecuteMarkRollCancel, globalExecuteMarkRollCancelReason,
        globalExecuteRollChangeTextile, globalExecuteRollChangeDescription,
        globalExecuteRollChangeTextileLength, globalExecuteRollChangeDescriptionText,
        globalStartExecuteRoll, globalPauseExecuteRoll, globalResumeExecuteRoll, globalFinishExecuteRoll,
        globalSelectWorkers, globalSelectWorkerId, globalSelectWorkerFlag,
        globalCalendarChangeFlag,
        getFabrics,
        getFabricById,
        updateFabric,
        createFabric,
        uploadFabrics,
        deleteFabric,
        uploadFabricsPictures,
        getFabricsMachines, getFabricsMachineById, setFabricsMachineStatusById,
        uploadFabricsOrders,
        getTasksByPeriod,
        getLastDoneFabricTask,
        createFabricTask,
        changeFabricTaskStatus,
        changeFabricTaskDateStatus,
        deleteFabricTaskRollById,
        getFabricTeamNumberByDate,
        updateExecuteRoll,
        updateFabricTaskWorkers,
        getFabricExecutingTasks, getFabricNotDoneTasks,
        closeFabricTasks,
        getFabricsOrders,
        closeFabricOrder, setFabricOrderActive,
        getFabricTaskContextNotDone,
        createContextExpense,
        getNotAcceptedToCutRolls,
        setRollRegisteredStatus, setRollMovedStatus,
    }

})

