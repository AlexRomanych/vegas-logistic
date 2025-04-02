// Хранилище для заказов

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {jwtGet, jwtPost, jwtDelete, jwtUpdate} from "/resources/js/src/app/utils/jwt_api"
import {openNewTab} from "/resources/js/src/app/helpers/helpers_service"

import axios from 'axios'


// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                                   // Префикс API
const URL_FABRICS = 'fabrics/'                                  // URL для получения списка ПС
const URL_FABRIC = 'fabric/'                                    // URL для получения ПС

const URL_FABRICS_MACHINES = 'fabrics/machines/'                // URL для получения списка Стегальных Машин
const URL_FABRICS_MACHINE = 'fabrics/machine/'                  // URL для получения Стегальной Машины

const URL_FABRICS_UPLOAD = 'fabrics/upload/'                    // URL для загрузки ПС с диска
const URL_FABRIC_DELETE = 'fabrics/delete/'                     // URL для удаления ПС

const URL_FABRICS_PICTURES_UPLOAD = 'fabrics/pictures/upload/'  // URL для загрузки hрисунков ПС с диска

const URL_FABRICS_ORDERS_UPLOAD = 'fabrics/orders/upload/'      // URL для загрузки расхода ПС с диска из отчета 1С СВПМ
const URL_FABRIC_ORDERS_DELETE = 'fabrics/order/delete/'        // URL для удаления заказа

const URL_FABRIC_TASKS = 'fabrics/tasks/'                       // URL для получения списка СЗ для ПС

export const useFabricsStore = defineStore('fabrics', () => {


    // Список ПС, которые получили к отображению
    let fabricsCashe = []
    // const ordersShowTest = ref('123')
    const fabricsCasheIsChanged = ref(false)

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

    // Attract: Получаем с API список ПС
    const getTasksByPeriod = async (params) => {

        const result = await jwtGet(URL_FABRIC_TASKS, params)
        console.log('store', result)
        return result.data                                  // все возвращается через Resource с ключем data
    }

    return {
        fabricsCashe,
        fabricsCasheIsChanged,
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
    }

})
