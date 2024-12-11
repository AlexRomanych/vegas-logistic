// Хранилище для заказов

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {jwtGet, jwtPost} from "@/src/app/utils/jwt_api"
import {openNewTab} from "@/src/app/helpers/helpers_service"



// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                   // Префикс API
const URL_ORDERS = 'orders/'                    // URL для получения списка заказов
const URL_ORDER = 'order/'                      // URL для получения заказа

const URL_ORDER_UPLOAD = 'orders/upload/'       // URL для загрузки заказов


export const useOrdersStore = defineStore('orders', () => {


    // Список заказов
    const orders = ref([])


    // Получаем с API список заказов
    const getOrders = async (start, end) => {

        if (orders.length === 0) {

            const params = {
                start,
                end,
            }

            const result = await jwt_get(URL_ORDERS, params)
            console.log(result)

        }
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

        openNewTab(result)


        return result
    }

    return {
        orders,
        getOrders,
        uploadOrders,
    }

})
