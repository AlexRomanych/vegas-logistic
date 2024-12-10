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

        console.log(formData)

        for (const [key, value] of formData.entries()) {
            console.log(key, value);
        }

        fetch('/api/v1/orders/upload', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                // 'Content-Type': 'multipart/form-data',
                // 'Content-Type': 'application/json',
                // 'Content-Type': 'multipart/form-data; charset=utf-8; boundary=' + Math.random().toString().substr(2),
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: formData
        })
            .then(response => console.log(response.data))
            .catch(error => {
                debugger
                console.error('Error:', error);
            });


        // debugger

        // const headers = {
        //     // 'Content-Type': 'application/json',
        //     'Content-Type': 'multipart/form-data',
        //
        // }
        //
        // const result = await jwtPost(URL_ORDER_UPLOAD, formData, headers)
        // const data = await result
        //
        // // return data
        // try {
        //     // const a = JSON.stringify(data)
        //     console.log(a)
        // } catch (e) {
        //     console.log(e)
        // }
        //
        // console.log(typeof data)
        // console.log(data)
        // debugger
    }

    return {
        orders,
        getOrders,
        uploadOrders,
    }

})
