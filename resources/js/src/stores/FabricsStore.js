// Хранилище для заказов

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {jwtGet, jwtPost, jwtDelete} from "/resources/js/src/app/utils/jwt_api"
import {openNewTab} from "/resources/js/src/app/helpers/helpers_service"

import axios from 'axios'


// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                       // Префикс API
const URL_FABRICS = 'fabrics/'                      // URL для получения списка ПС
const URL_FABRIC = 'fabric/'                        // URL для получения ПС

const URL_FABRICS_UPLOAD = 'fabrics/upload/'        // URL для загрузки ПС с диска
const URL_FABRIC_DELETE = 'fabrics/delete/'         // URL для удаления ПС


export const useFabricsStore = defineStore('fabrics', () => {


    // Список ПС, которые получили к отображению
    let fabricsShow = []
    // const ordersShowTest = ref('123')
    const fabricsShowIsChanged = ref(false)

    // Получаем с API список ПС
    const getFabrics = async () => {

        const result = await jwtGet(URL_FABRICS)
        fabricsShow.value = result.fabrics             // кэшируем
        //
        // console.log(ordersShow.value)

        // openNewTab(result)
        // console.log(data)
        // console.log(result)

        console.log('store', result)

        return result.fabrics // все возвращается через Resource с ключем data

    }

    // Загрузка заказов на сервер
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

    const deleteFabric = async (delOrdersListIds = {}) => {
        console.log(delOrdersListIds)
        // const res = await axios.delete(API_PREFIX + URL_ORDER_DELETE, {data: delOrdersList})
        const res = await jwtDelete(URL_FABRIC_DELETE, delOrdersListIds)

        console.log(res)
    }


    return {
        fabricsShow,
        // ordersShowTest,
        fabricsShowIsChanged,
        getFabrics,
        uploadFabrics,
        deleteFabric,
    }

})
