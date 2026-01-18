// Хранилище для ПЯ Швейки

import { defineStore, storeToRefs } from 'pinia'
import { ref, reactive, computed, watch } from 'vue'

import { jwtGet, jwtPost, jwtDelete, jwtPatch } from '@/app/utils/jwt_api'
import type { IPeriod, ISewingTask, ISewingTaskLine } from '@/types'
// import { usePlansStore } from '@/stores/PlansStore.ts'
import { DEBUG } from '@/app/constants/common.ts'



// Устанавливаем глобальные переменные
const API_PREFIX               = '/api/v1/' // Префикс API
const URL_SEWING_TASKS         = '/sewing/tasks' // URL для получения Сменных заданий
const URL_SEWING_TASK_STATUSES = '/sewing/task/statuses' // URL для получения Статуса Движения СЗ
const URL_SEWING_TASK_STATUSES_COLOR_PATCH = '/sewing/task/statuses/color/patch' // URL для получения Статуса Движения СЗ

export const useSewingStore = defineStore('sewing', () => {

    // --- ------------------------------------------------------------------------------------------
    // --- -- Этот функционал (Пошива) будем полностью реализовывать через Store API
    // --- ------------------------------------------------------------------------------------------

    // --- ------------------------------------------------------------------------------------------
    // --- -- Глобальные переменные
    // --- ------------------------------------------------------------------------------------------

    // __ Массив СЗ Пошива
    const globalSewingTasks = ref<ISewingTask[]>([])

    // __ Показывать ли Трудозатраты в календаре СЗ Пошива
    const globalSewingTaskTimesShow = ref(true)

    // __ Показывать ли Раскрытый день или нет в календаре СЗ Пошива
    const globalSewingTaskFullDaysShow = ref(true)

    // __ Раскрашивать заявки в календаре в цвет Типа Заявки или в цвет Статусов Движения Заявок
    const globalSewingTaskOrderTypeColor = ref(true)

    // __ Текущая Запись (SewingLine) в карточке СЗ в календаре СЗ Пошива
    const globalManageTaskCardActiveSewingLine = ref<ISewingTaskLine | null>(null)

    // --- ------------------------------------------------------------------------------------------


    // const planStore   = usePlansStore()
    // const {planPeriodGlobal} = storeToRefs(planStore)



    // __ Получение СЗ Пошива с сервера за период
    const getSewingTasks = async (period: IPeriod | null = null) => {
        let response
        if (period) {
            response = await jwtGet(URL_SEWING_TASKS, {period})
        } else {
            response = await jwtGet(URL_SEWING_TASKS)
        }
        const result = await response

        globalSewingTasks.value = result.data   // кэшируем

        if (DEBUG) console.log('SewingStore: getSewingTasks: ', result)
        return result.data
    }


    // __ Получение Статусов Движения СЗ
    const getSewingTaskStatuses = async () => {
        let response = await jwtGet(URL_SEWING_TASK_STATUSES)
        const result = await response
        if (DEBUG) console.log('SewingStore: getSewingTaskStatuses: ', result)
        return result.data
    }

    // __ Устанавливаем цвет ярлычка Типов заказов (серийная, гаррмем, прогнозная и т.д.)
    const patchSewingTaskStatusColor = async (sewingTaskStatusId: number, color: string) => {
        const result = await jwtPatch(URL_SEWING_TASK_STATUSES_COLOR_PATCH, {id: sewingTaskStatusId, color})
        if (DEBUG) console.log('SewingStore: patchSewingTaskStatusColor', result)
        return result.data
    }


    return {
        globalSewingTasks,

        globalSewingTaskTimesShow,
        globalSewingTaskFullDaysShow,
        globalSewingTaskOrderTypeColor,
        globalManageTaskCardActiveSewingLine,

        getSewingTasks,
        getSewingTaskStatuses,
        patchSewingTaskStatusColor,


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
