// info Хранилище Бизнес-процессов

import { ref } from 'vue'
import { defineStore } from 'pinia'
import { jwtGet, jwtPost, jwtDelete, jwtUpdate, jwtPut, jwtPatch } from '@/app/utils/jwt_api'
import type { IPeriod } from '@/types'

const DEBUG = true

const URL_PROCESSES = 'business-processes'                                                      // __ Получение Бизнес-процессов с сервера
const URL_PROCESS = 'business-processes'                                                        // __ Получение Бизнес-процессов с сервера
const URL_PROCESS_NODE = 'business-processes/node'                                              // __ Получение узла Бизнес-процесса с сервера = 'business-processes'                                                        // __ Получение Бизнес-процессов с сервера
const URL_PROCESSES_ADJACENCY_LIST = 'business-processes/adjacency-list'                        // __ Получение Списка смежности Бизнес-процессов с сервера = 'business-processes'                                                      // __ Получение Бизнес-процессов с сервера

export const useBusinessProcessesStore = defineStore('business-processes', () => {

    // // __ Declare variables
    const businessProcessesGlobal = ref<IPeriod | null>(null)

    // line ----------------------------------------------------------------

    // __ Получаем с API все Бизнесс-процессы
    const getBusinessProcesses = async (status = null) => {
        const params = status === true
            ? '1'
            : status === false
                ? '0'
                : ''
        const result = await jwtGet(`${URL_PROCESSES}/${params}`)
        businessProcessesGlobal.value = result.data     // кэшируем
        if (DEBUG) console.log('useBusinessProcessesStore: getBusinessProcesses: ', result)
        return result.data
    }

    // __ Получаем с API Бизнес-процесс по id
    const getBusinessProcessById = async (id: number) => {
        const result = await jwtGet(`${URL_PROCESS}/${id.toString()}`)
        if (DEBUG) console.log('useBusinessProcessesStore: getBusinessProcessesAdjacencyList: ', result)
        return result.data
    }

    // __ Получаем с API узел Бизнес-процесса по id
    const getBusinessProcessNodeById = async (id: number) => {
        const result = await jwtGet(`${URL_PROCESS_NODE}/${id.toString()}`)
        if (DEBUG) console.log('useBusinessProcessesStore: getBusinessProcessNodeById: ', result)
        return result.data
    }



    // __ Получаем с API Список Смежности (Adjacency List) Бизнес-процесса
    const getBusinessProcessAdjacencyList = async (id: number) => {
        const result = await jwtGet(`${URL_PROCESSES_ADJACENCY_LIST}/${id.toString()}`)
        if (DEBUG) console.log('useBusinessProcessesStore: getBusinessProcess: ', result)
        return result.data
    }

    // // ___ Загрузка Плана загрузок на сервер
    // // fileData - данные файла, отправляем в RAW формате
    // const uploadLoads = async (fileData: string) => {
    //
    //     const headers = {
    //         'Content-Type': 'application/json',
    //         // 'Content-Type': 'application/octet-stream',
    //         // 'Content-Type': 'multipart/form-data',
    //         // 'Content-Type': 'application/x-www-form-urlencoded',
    //     }
    //
    //     const response = await jwtPost(URL_PLAN_LOADS_UPLOAD, {data: fileData}, headers)
    //     // const response = await jwtPost(URL_PLAN_ULOADS_UPLOAD, fileData, headers)
    //     const result = await response
    //
    //     if (DEBUG) console.log('PlansStore: uploadLoads: ', result)
    //
    //     return result
    // }


    // // ___ Получение Плана загрузок с сервера за период
    // const getPlanLoads = async (period: IPeriod | null = null) => {
    //     let response
    //     if (period) {
    //         response = await jwtGet(URL_PLAN_LOADS, {period})
    //     } else {
    //         response = await jwtGet(URL_PLAN_LOADS)
    //     }
    //     const result = await response
    //     if (DEBUG) console.log('PlansStore: getPlanLoads: ', result)
    //     return result.data
    // }
    //
    // // ___ Получение периода по умолчанию для плана загрузок
    // const getPlanLoadsDefaultPeriod = async () => {
    //     const response = await jwtGet(URL_PLAN_LOADS_DEFAULT_PERIOD)
    //     const result = await response
    //     if (DEBUG) console.log('PlansStore: getPlanLoads: ', result)
    //     return result.period
    // }



    return {
        businessProcessesGlobal,

        getBusinessProcesses,
        getBusinessProcessById,
        getBusinessProcessNodeById,
        getBusinessProcessAdjacencyList,
    }

})



