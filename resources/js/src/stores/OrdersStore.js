// Хранилище для заказов

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {jwtGet, jwtPost} from "@/src/app/utils/jwt_api";


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

    const uploadOrders = async (formData) => {

        const headers = {
            'Content-Type': 'multipart/form-data'
        }

        // jwtPost(URL_ORDER_UPLOAD, formData, headers)
            // .then(data => {
            //     console.log(data)
            // })
            // .catch(error => {
            //     console.log(error)
            // })
        const result = await jwtPost(URL_ORDER_UPLOAD, formData, headers)
        const data = await result
        return data

        // console.log(data)
        // debugger
    }

    return {
        orders,
        getOrders,
        uploadOrders,
    }

})
