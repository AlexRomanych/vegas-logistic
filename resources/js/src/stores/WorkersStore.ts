// Хранилище для заказов

import { defineStore } from 'pinia'
import { ref } from 'vue'

import { jwtGet, jwtPost, jwtDelete, jwtPatch_, jwtUpdate,  } from '@/app/utils/jwt_api'

import type { IWorker } from '@/types'
import { WORKER_DRAFT } from '@/app/constants/workers.ts'

const DEBUG = true

// Устанавливаем глобальные переменные
const API_PREFIX  = '/api/v1/'                       // Префикс API
const URL_WORKERS = 'workers'                      // URL для получения списка рабочих
// const URL_WORKER  = 'workers/'                        // URL для crud с рабочим

// const URL_WORKER_UPLOAD = 'orders/upload/'      // URL для загрузки заказов с диска
// const URL_ORDER_DELETE = 'orders/delete/'       // URL для удаления заказов


export const useWorkersStore = defineStore('workers', () => {

    let workersCashe = ref<IWorker[]>([])       // Список рабочих


    // __ Получаем список рабочих
    const getWorkers = async () => {
        const result       = await jwtGet(URL_WORKERS)
        workersCashe.value = result.data
        if (DEBUG) console.log('workersStore: getWorkers', result.data)
        return result.data
    }


    // __ Получаем рабочего по id
    const getWorkerById = async (id: number = 0) => {

        if (id === 0) return WORKER_DRAFT
        const result = await jwtGet(`${URL_WORKERS}/${id}`)
        if (DEBUG) console.log('workersStore: getWorkerById', result.data)
        return result.data
    }

    // __ Обновление рабочего
    const updateWorker = async (worker: IWorker | null = null) => {
        if (worker === null) {
            return
        }
        const result = await jwtPatch_(URL_WORKERS, worker)
        if (DEBUG) console.log('workersStore: updateWorker', result.data)
        return result
    }

    // __ Создание рабочего
    const createWorker = async (worker: IWorker | null = null) => {
        if (worker === null) {
            return
        }
        const result = await jwtPost(URL_WORKERS, worker)
        return result
    }

    // __ Удаление рабочего
    const deleteWorker = async (delWorkerId: number = 0) => {
        if (delWorkerId === 0) {
            return
        }
        const result = await jwtDelete(URL_WORKERS, { id: delWorkerId })
        return result
    }



    return {
        getWorkers,
        deleteWorker,
        getWorkerById,
        updateWorker,
        createWorker,

    }

})
