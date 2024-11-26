// Хранилище для заказов

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {jwtGet} from "@/src/app/utils/jwt_api";


// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'
const URL_ORDERS = 'orders/'
const URL_ORDER = 'order/'

export const useOrdersStore = defineStore('orders', () => {


    // Список заказов
    const orders = ref([])


    // Получаем с API список заказов
    const getOrders = async (start, end) => {

        if (orders.length === 0) {

            const url = API_PREFIX + URL_ORDERS
            const params = {
                start,
                end,
            }

            result = await jwt_api.jwt_get(url, params)
            console.log(result)

        }





    }

    return {
        orders,
        getOrders,
    }

})
