// Хранилище для заказов

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {jwtGet, jwtPost, jwtDelete} from "@/src/app/utils/jwt_api"
import {openNewTab} from "@/src/app/helpers/helpers_service"

import axios from 'axios'


// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                   // Префикс API
const URL_ORDERS = 'orders/'                    // URL для получения списка заказов
const URL_ORDER = 'order/'                      // URL для получения заказа

const URL_ORDER_UPLOAD = 'orders/upload/'       // URL для загрузки заказов
const URL_ORDER_DELETE = 'orders/delete/'       // URL для загрузки заказов


export const useOrdersStore = defineStore('orders', () => {


    // Список заказов, которые получили к отображению
    let ordersShow = []
    // const ordersShowTest = ref('123')
    const ordersShowIsChanged = ref(false)

    // Получаем с API список заказов
    const getOrders = async (params) => {

        // console.log(params)

        const result = await jwtGet(URL_ORDERS, params)
        ordersShow.value = result.data             // кэшируем
        //
        // console.log(ordersShow.value)

        // openNewTab(result)
        // console.log(data)
        // console.log(result)

        // console.log(result.data)

        return result.data // все возвращается через Resource с ключем data

    }

    // Загрузка заказов на сервер
    // fileData - данные файла, отправляем в RAW формате
    const uploadOrders = async (fileData) => {

        console.log(fileData)


        const headers = {
            'Content-Type': 'application/json',
            // 'Content-Type': 'application/octet-stream',
            // 'Content-Type': 'multipart/form-data',
            // 'Content-Type': 'application/x-www-form-urlencoded',
        }

        const response = await jwtPost(URL_ORDER_UPLOAD, fileData, headers)
        const result = await response

        // openNewTab(result)
        // ordersShowIsChanged = false

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
        // ordersShowTest,
        ordersShowIsChanged,
        getOrders,
        uploadOrders,
        deleteOrders,
    }

})
