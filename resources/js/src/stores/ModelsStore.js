// Хранилище для моделей

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {jwtGet, jwtPost, jwtDelete} from "@/src/app/utils/jwt_api"
import {openNewTab} from "@/src/app/helpers/helpers_service"

// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                   // Префикс API
const URL_MODELS = 'models/'                    // URL для получения списка моделей
const URL_MODEL = 'model/'                      // URL для получения модели

const URL_MODELS_UPLOAD = 'models/upload/'      // URL для загрузки моделей с диска
const URL_MODELS_LOAD = 'models/load/'          // URL для загрузки моделей из хранилища
const URL_MODEL_DELETE = 'models/delete/'       // URL для загрузки модели


export const useModelsStore = defineStore('models', () => {

    const modelsLoad = async () => {
        const response = await jwtGet(URL_MODELS_LOAD)

        openNewTab(response)

        return response
    }

    return {
        modelsLoad,
    }

})








//
//
// export const useOrdersStore = defineStore('orders', () => {
//
//
//     // Список заказов, которые получили к отображению
//     let ordersShow = []
//     // const ordersShowTest = ref('123')
//     const ordersShowIsChanged = ref(false)
//
//     // Получаем с API список заказов
//     const getOrders = async (params) => {
//
//         // console.log(params)
//
//         const result = await jwtGet(URL_ORDERS, params)
//         ordersShow.value = result.data             // кэшируем
//         //
//         // console.log(ordersShow.value)
//
//         // openNewTab(result)
//         // console.log(data)
//         // console.log(result)
//
//         // console.log(result.data)
//
//         return result.data // все возвращается через Resource с ключем data
//
//     }
//
//     // Загрузка заказов на сервер
//     // fileData - данные файла, отправляем в RAW формате
//
//
//     const deleteOrders = async (delOrdersListIds = {}) => {
//         console.log(delOrdersListIds)
//         // const res = await axios.delete(API_PREFIX + URL_ORDER_DELETE, {data: delOrdersList})
//         const res = await jwtDelete(URL_ORDER_DELETE, delOrdersListIds)
//
//         console.log(res)
//     }
//
//
//     return {
//         ordersShow,
//         // ordersShowTest,
//         ordersShowIsChanged,
//         getOrders,
//         uploadOrders,
//         deleteOrders,
//     }
//
// })
