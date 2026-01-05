// info Хранилище для Планов

import { ref } from 'vue'
import { defineStore } from 'pinia'
import { jwtGet, jwtPost, jwtDelete, jwtUpdate, jwtPut, jwtPatch } from '@/app/utils/jwt_api'
import type { IPeriod } from '@/types'
import { BUSINESS_PROCESS_NODES, BUSINESS_PROCESSES } from '@/app/constants/business_processes.ts'

const DEBUG = true

const URL_PLAN_BUSINESS_PROCESS_NODE = 'plan/business-process-node'    // Получение Плана Узла бизнес-процесса с сервера

const URL_PLAN_LOADS_UPLOAD = 'plan/loads/upload'                      // Загрузка Плана загрузок на сервер
const URL_PLAN_LOADS = 'plan/loads'                                    // Получение Плана загрузок с сервера
const URL_PLAN_LOADS_VALIDATE = 'plan/loads/validate'                  // Проверка Плана загрузок на сервере

const URL_PLAN_LOADS_DEFAULT_PERIOD = 'plan/loads/default/period'      // Получение периода по умолчанию для плана загрузок

export const usePlansStore = defineStore('plans', () => {

    // __ Declare variables
    const planPeriodGlobal = ref<IPeriod | null>(null)

    // __ ________________________________________________________________

    // __ Проверка заказов на сервере
    const validatePlanLoads = async (fileData: string) => {
        const headers = {
            'Content-Type': 'application/json',
        }
        const response = await jwtPost(URL_PLAN_LOADS_VALIDATE, {data: fileData}, headers)
        const result   = await response

        if (DEBUG) console.log('PlansStore: validatePlanLoads', result)

        return result.data
    }

    // __ Загрузка Плана загрузок на сервер
    const uploadLoads = async (fileData: string) => {
        const headers = {
            'Content-Type': 'application/json',
        }

        const response = await jwtPost(URL_PLAN_LOADS_UPLOAD, {data: fileData}, headers)
        const result = await response

        if (DEBUG) console.log('PlansStore: uploadLoads: ', result)

        return result
    }


    // ___ Получение Плана узла бизнес-процесса с сервера за период
    const getPlanBusinessProcessNode = async (
        businessProcessId: number | null = BUSINESS_PROCESSES.ORDER_MOVING.ID,
        businessProcessNodeId: number | null = BUSINESS_PROCESS_NODES.LOADS.ID,
        period: IPeriod | null = null
    ) => {
        let response
        if (period) {
            response = await jwtGet(URL_PLAN_BUSINESS_PROCESS_NODE, {process: businessProcessId, node: businessProcessNodeId, period})
        } else {
            response = await jwtGet(URL_PLAN_BUSINESS_PROCESS_NODE, {process: businessProcessId, node: businessProcessNodeId})
        }
        const result = await response
        if (DEBUG) console.log('PlansStore: getPlanBusinessProcessNode: ', result)
        return result.data
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
        validatePlanLoads,
        getPlanLoadsDefaultPeriod,


        getPlanBusinessProcessNode,
    }

})



