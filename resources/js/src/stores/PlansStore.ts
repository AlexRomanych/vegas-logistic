// info Хранилище для Планов

import { defineStore } from 'pinia'
import { jwtGet, jwtPost, jwtDelete, jwtUpdate, jwtPut, jwtPatch } from '@/app/utils/jwt_api'

const DEBUG = true

const URL_PLAN_LOADS_UPLOAD = 'plan/loads/upload'                      // Загрузка Плана загрузок на сервер
const URL_PLAN_LOADS = 'plan/loads'                                    // Получение Плана загрузок с сервера

export const usePlansStore = defineStore('plans', () => {

    // ___ Загрузка Плана загрузок на сервер
    // fileData - данные файла, отправляем в RAW формате
    const uploadLoads = async (fileData: string) => {

        const headers = {
            'Content-Type': 'application/json',
            // 'Content-Type': 'application/octet-stream',
            // 'Content-Type': 'multipart/form-data',
            // 'Content-Type': 'application/x-www-form-urlencoded',
        }

        const response = await jwtPost(URL_PLAN_LOADS_UPLOAD, {data: fileData}, headers)
        // const response = await jwtPost(URL_PLAN_ULOADS_UPLOAD, fileData, headers)
        const result = await response

        if (DEBUG) console.log('PlansStore: uploadLoads: ', result)

        return result
    }


    // ___ Получение Плана загрузок с сервера
    const getPlanLoads = async () => {
        const response = await jwtGet(URL_PLAN_LOADS)
        const result = await response

        if (DEBUG) console.log('PlansStore: getPlanLoads: ', result)

        return result.data
    }


    return {
        uploadLoads,
        getPlanLoads,
    }

})



