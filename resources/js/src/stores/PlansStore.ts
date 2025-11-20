// info Хранилище для Планов

import { ref } from 'vue'
import { defineStore } from 'pinia'
import { jwtGet, jwtPost, jwtDelete, jwtUpdate, jwtPut, jwtPatch } from '@/app/utils/jwt_api'
import type { IPeriod } from '@/types'

const DEBUG = true

const URL_PLAN_LOADS_UPLOAD = 'plan/loads/upload'                      // Загрузка Плана загрузок на сервер
const URL_PLAN_LOADS = 'plan/loads'                                    // Получение Плана загрузок с сервера
const URL_PLAN_LOADS_DEFAULT_PERIOD = 'plan/loads/default/period'      // Получение периода по умолчанию для плана загрузок

export const usePlansStore = defineStore('plans', () => {

    // __ Declare variables
    const planPeriodGlobal = ref<IPeriod | null>(null)

    // __ ________________________________________________________________


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


    // ___ Получение Плана загрузок с сервера за период
    const getPlanLoads = async (period: IPeriod | null = null) => {
        let response
        if (period) {
            response = await jwtGet(URL_PLAN_LOADS, {period})
        } else {
            response = await jwtGet(URL_PLAN_LOADS)
        }
        const result = await response
        if (DEBUG) console.log('PlansStore: getPlanLoads: ', result)
        return result.data
    }

    // ___ Получение периода по умолчанию для плана загрузок
    const getPlanLoadsDefaultPeriod = async () => {
        const response = await jwtGet(URL_PLAN_LOADS_DEFAULT_PERIOD)
        const result = await response
        if (DEBUG) console.log('PlansStore: getPlanLoads: ', result)
        return result.period
    }



    return {
        planPeriodGlobal,

        uploadLoads,
        getPlanLoads,
        getPlanLoadsDefaultPeriod,
    }

})



