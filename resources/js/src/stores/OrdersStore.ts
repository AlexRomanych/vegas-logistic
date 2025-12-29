// Хранилище для заказов
import { DEBUG } from '@/app/constants/common.ts'

import { ref, /*reactive, computed, watch*/ } from 'vue'
import { defineStore } from 'pinia'

import { jwtGet, jwtPost, jwtDelete } from '@/app/utils/jwt_api'
// import { openNewTab } from '@/app/helpers/helpers_service'


// __ Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                   // Префикс API
const URL_ORDERS = 'orders/'                    // URL для получения списка заказов
const URL_ORDER  = 'order/'                      // URL для получения заказа

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


    return {
        ordersShow,
        ordersShowIsChanged,

        getOrders,
        uploadOrders,
        validateOrders,
        deleteOrders,
    }

})
