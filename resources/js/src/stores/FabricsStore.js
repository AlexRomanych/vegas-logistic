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

const URL_FABRICS_UPLOAD = 'fabrics/upload/'                            // URL для загрузки ПС с диска
const URL_FABRIC_DELETE = 'fabrics/delete/'                             // URL для удаления ПС

const URL_FABRICS_PICTURES_UPLOAD = 'fabrics/pictures/upload/'          // URL для загрузки hрисунков ПС с диска

const URL_FABRICS_ORDERS_UPLOAD = 'fabrics/orders/upload/'              // URL для загрузки расхода ПС с диска из отчета 1С СВПМ
const URL_FABRIC_ORDERS_DELETE = 'fabrics/order/delete/'                // URL для удаления заказа

const URL_FABRIC_TASKS = 'fabrics/tasks/'                               // URL для получения списка СЗ для ПС
const URL_FABRIC_TASKS_CREATE = 'fabrics/tasks/create/'                 // URL для получения создания или обновления СЗ для ПС
const URL_FABRIC_TASKS_STATUS_CHANGE = 'fabrics/tasks/status/change/'   // URL для получения создания или обновления СЗ для ПС
const URL_FABRIC_TASKS_CONTEXT_DELETE = 'fabrics/tasks/context/delete/' // URL для удаления рулона из СЗ, созданного ОПП (FabricTaskContext)

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


    // attract: Массив с индексами рулонов, которые уже есть в СЗ, для того, чтобы исключить их из выбора при создании СЗ
    let globalRollsIndexes = reactive([])


    // Attract: Получаем с API список ПС
    const getFabrics = async () => {

        const result = await jwtGet(URL_FABRICS)
        fabricsCashe.value = result.fabrics             // кэшируем
        // console.log('store', result)

        return result.fabrics // все возвращается через Resource с ключем data
    }

    // Attract: Получаем с API ПС по id
    const getFabricById = async (id) => {
        const result = await jwtGet(URL_FABRIC + id)
        return result.fabric
    }


    // Attract: Обновление ПС
    const updateFabric = async (fabric) => {
        const result = await jwtUpdate(URL_FABRIC, fabric)
        fabricsCashe = []
        return result
        // console.log(result)
    }

    // Attract: Создание ПС
    const createFabric = async (fabric) => {
        const result = await jwtPost(URL_FABRIC, {data: fabric})
        fabricsCashe = []
        return result
        // console.log(result)
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

    return {
        fabricsCashe,
        fabricsMemory,
        fabricsCasheIsChanged,
        globalEditMode,
        globalFabricsMode,
        globalTaskProductivity, clearTaskGlobalProductivity,
        globalRollsIndexes,
        getFabrics,
        getFabricById,
        updateFabric,
        createFabric,
        uploadFabrics,
        deleteFabric,
        uploadFabricsPictures,
        getFabricsMachines,
        getFabricsMachineById,
        uploadFabricsOrders,
        getTasksByPeriod,
        createFabricTask,
        changeFabricTaskStatus,
        changeFabricTaskDateStatus,
        deleteFabricTaskRollById
    }

})
