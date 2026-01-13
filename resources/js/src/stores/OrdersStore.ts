// Хранилище для заказов
import { DEBUG } from '@/app/constants/common.ts'

import { ref, /*reactive, computed, watch*/ } from 'vue'
import { defineStore } from 'pinia'

import { jwtGet, jwtPost, jwtDelete, jwtPatch } from '@/app/utils/jwt_api'
// import { openNewTab } from '@/app/helpers/helpers_service'


// __ Устанавливаем глобальные переменные
const API_PREFIX                    = '/api/v1/'     // Префикс API
const URL_ORDERS                    = 'orders/'      // URL для получения списка заказов
const URL_ORDER                     = 'order/'       // URL для получения заказа
const URL_ORDERS_TYPES              = 'orders/types' // URL для получения типа заказов
const URL_ORDERS_TYPES_COLOR_UPDATE = 'orders/types/color/patch' // URL для обновления цвета типа заказов


const URL_ORDERS_UPLOAD   = 'orders/upload/'      // URL для загрузки заказов с диска
const URL_ORDERS_VALIDATE = 'orders/validate/'  // URL для проверки заказов с диска
const URL_ORDER_DELETE    = 'orders/delete/'       // URL для удаления заказов


export const useOrdersStore = defineStore('orders', () => {


    // Список заказов, которые получили к отображению
    let ordersShow: any       = []
    // const ordersShowTest = ref('123')
    const ordersShowIsChanged = ref(false)

    // __ Получаем с API список Заказов
    const getOrders = async (/*period: any*/) => {

        // console.log(period)
        const result     = await jwtGet(URL_ORDERS,/* params*/)
        ordersShow.value = result.data             // кэшируем

        return result.data // все возвращается через Resource с ключем data
    }


    // __ Проверка заказов на сервере
    const validateOrders = async (fileData: string) => {
        const headers = {
            'Content-Type': 'application/json',
        }

        const response = await jwtPost(URL_ORDERS_VALIDATE, {data: fileData}, headers)
        const result   = await response

        if (DEBUG) console.log('OrdersStore: validateOrders', result)

        return result.data
    }


    // Загрузка заказов на сервер
    // fileData - данные файла, отправляем в RAW формате
    const uploadOrders = async (fileData: string) => {
        const headers = {
            'Content-Type': 'application/json',
            // 'Content-Type': 'application/octet-stream',
            // 'Content-Type': 'multipart/form-data',
            // 'Content-Type': 'application/x-www-form-urlencoded',
        }

        const response = await jwtPost(URL_ORDERS_UPLOAD, {data: fileData}, headers)
        const result   = await response

        if (DEBUG) console.log('OrdersStore: uploadOrders', result)

        return result
    }

    const deleteOrders = async (delOrdersListIds = {}) => {
        console.log(delOrdersListIds)
        // const res = await axios.delete(API_PREFIX + URL_ORDER_DELETE, {data: delOrdersList})
        const res = await jwtDelete(URL_ORDER_DELETE, delOrdersListIds)

        console.log(res)
    }


    // __ Получаем с API список Типов заказов (серийная, гаррмем, прогнозная и т.д.)
    const getOrderTypes = async () => {
        const result = await jwtGet(URL_ORDERS_TYPES)

        if (DEBUG) console.log('OrdersStore: getOrderTypes', result)

        return result.data
    }

    // __ Устанавливаем цвет ярлычка Типов заказов (серийная, гаррмем, прогнозная и т.д.)
    const patchOrderTypeColor = async (orderTypeId: number, color: string) => {
        const result = await jwtPatch(URL_ORDERS_TYPES_COLOR_UPDATE, {id: orderTypeId, color})
        if (DEBUG) console.log('OrdersStore: updateOrderTypeColor', result)
        return result.data
    }

    return {
        ordersShow,
        ordersShowIsChanged,

        getOrders,
        uploadOrders,
        validateOrders,
        deleteOrders,

        getOrderTypes,
        patchOrderTypeColor,
    }

})
