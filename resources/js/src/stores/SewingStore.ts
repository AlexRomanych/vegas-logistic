// Хранилище для ПЯ Швейки

import { defineStore, storeToRefs } from 'pinia'
import { ref, reactive, computed, watch } from 'vue'

import { jwtGet, jwtPost, jwtDelete, jwtPatch } from '@/app/utils/jwt_api'
import type { IPeriod, IRenderMatrixDiff, ISewingTask, ISewingTaskLine, ISewingTaskOrder } from '@/types'
// import { usePlansStore } from '@/stores/PlansStore.ts'
import { DEBUG } from '@/app/constants/common.ts'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import { additionDaysInStrFormat } from '@/app/helpers/helpers_date'
import {
    getSewingTasksDiff, isAddItemsInDiffsPresents, repositionSewingTaskLines
} from '@/app/helpers/manufacture/helpers_sewing.ts'


// Устанавливаем глобальные переменные
const API_PREFIX                           = '/api/v1/' // Префикс API
const URL_SEWING_TASKS                     = '/sewing/tasks' // URL для получения Сменных заданий
const URL_SEWING_TASKS_UPDATE              = '/sewing/tasks/update' // URL для обновления Сменных заданий
const URL_SEWING_TASK_STATUSES             = '/sewing/task/statuses' // URL для получения Статуса Движения СЗ
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

    // __ Копия массива СЗ Пошива для отслеживания изменений
    let globalSewingTasksCopy: ISewingTask[] = []

    // __ Период рендеринга календаря
    const globalRenderPeriod = ref<IPeriod>(PERIOD_DRAFT)

    // __ Показывать ли Трудозатраты в календаре СЗ Пошива
    const globalSewingTaskTimesShow = ref(true)

    // __ Показывать ли Раскрытый день или нет в календаре СЗ Пошива
    const globalSewingTaskFullDaysShow = ref(true)

    // __ Раскрашивать заявки в календаре в цвет Типа Заявки или в цвет Статусов Движения Заявок
    const globalSewingTaskOrderTypeColor = ref(true)

    // __ Текущая Запись (SewingLine) в карточке СЗ в календаре СЗ Пошива
    const globalManageTaskCardActiveSewingLine = ref<ISewingTaskLine | null>(null)

    // __ Текущее Заявка, на которое ссылается кликнутое СЗ (для календаря для подсветки СЗ, которые ссылаются на одну заявку)
    const globalSewingTaskActiveOrderId = ref<number | null>(null)

    // __ Глобальное состояние разницы состояний до и после редактирования в карточке СЗ или перетаскивания в календаре
    // const globalDiffs = ref<IRenderMatrixDiff[]>([])

    // --- ------------------------------------------------------------------------------------------


    // const planStore   = usePlansStore()
    // const {planPeriodGlobal} = storeToRefs(planStore)


    // --- ------------------------------------------------------------------------------------------
    // --- ---------------- Тут вся логика по управлению и сохранению частей СЗ ---------------------
    // --- ------------------------------------------------------------------------------------------
    const processSewingTaskManageActions = (
        dateFrom: string, leftPanel: ISewingTaskLine[]) => {

        // __ Создание новой части СЗ + изменение существующего порядка
        // __   1. Создание новой части СЗ (правая панель)
        // __   2. Перенумеровка позиций (правая панель)
        // __   3. Сохранение изменений (правая панель)
        // __   4. Перенумеровка позиций (левая панель) (с учетом разбитых линий)
        // __   5. Сохранение изменений (левая панель)
    }


    /**
     * __ Добавление новой части СЗ и изменение старой части СЗ, на основе которого была создана новая часть
     * @param addSewingTask     - __ СЗ, которое уже было сформировано на основе старой части СЗ (правая панель)__
     * @param leftPanel         - __ контент в новом СЗ (правая панель)__
     * @param oldSewingTask     - __ СЗ, на основе которого формируется новая часть СЗ (левая панель)__
     * @param rightPanel        - __ контент в старом СЗ (левая панель)__
     */
    const addSewingTaskToGlobal = (
        oldSewingTask: ISewingTask,
        leftPanel: ISewingTaskLine[],
        addSewingTask: ISewingTask | null    = null,
        rightPanel: ISewingTaskLine[] | null = null,
    ) => {

        leftPanel                  = repositionSewingTaskLines(leftPanel)   // __ Пересчитываем позиции для строк СЗ (SewingLines[])
        oldSewingTask.sewing_lines = leftPanel              // __ oldSewingTask приходит по ссылке

        // console.log('addSewingTask: ', addSewingTask)
        // console.log('rightPanel: ', rightPanel)
        //
        // console.log(!addSewingTask)
        // console.log(!rightPanel)

        // __ Если есть правая панель, то добавляем ее в массив СЗ
        if (addSewingTask && rightPanel) {

            // console.log('passed')

            rightPanel                 = repositionSewingTaskLines(rightPanel)  // __ Пересчитываем позиции для строк СЗ (SewingLines[])
            addSewingTask.sewing_lines = rightPanel             // __ addSewingTask приходит новым объектом


            // __ Переопределяем порядок СЗ.
            // __ Находим все СЗ в глабальной переменной с датой созданного СЗ и меняем порядок


            globalSewingTasks.value.push(addSewingTask)
            globalSewingTasks.value
                // __ Отбираем только объекты на нужную дату
                .filter(item => item.action_at === addSewingTask.action_at)
                // __ Сортируем их по возрастанию текущей позиции (включая x.1)
                .sort((a, b) => a.position - b.position)
                // __ Мутируем каждый объект, присваивая новый порядковый номер
                .forEach((item, index) => {
                    item.position = index + 1
                })

            // console.log(globalSewingTasks.value)
        }

        saveChanges()   // __ Сохраняем изменения


        /*
        // !!! Через блокировку watcher, но лучше работает напрямую
        // __ Копируем массив, чтобы не вызывать watcher
        // const copyData = JSON.parse(JSON.stringify(globalSewingTasks.value))

        // __ Добавляем новый объект в массив
        //  copyData.push(sewingTask)

        // const changedData = (copyData as ISewingTask[])
        //     // __ Отбираем только объекты на нужную дату
        //     .filter(item => item.action_at === sewingTask.action_at)
        //     // __ Сортируем их по возрастанию текущей позиции (включая x.1)
        //     .sort((a, b) => a.position - b.position)

        // __ Мутируем каждый объект, присваивая новый порядковый номер
        // changedData
        //     .forEach((item, index) => {
        //         item.position = index + 1
        //     })

        // __ Мутируем каждый объект в copyData, присваивая новый порядковый номер
        // changedData.forEach(item => {
        //     const findTask = copyData.find((el: ISewingTask) => el.id === item.id)
        //     if (findTask) {
        //         findTask.position = item.position
        //     }
        // })

        // __ Запускаем реактивность
        // globalSewingTasks.value = copyData
        */

    }


    // __ Применение изменений
    const applyChanges = (diffs: IRenderMatrixDiff[] = []) => {

        console.log(diffs)
        // console.log('sewingStore: globalRenderPeriod.value: ', globalRenderPeriod.value)

        // console.log(globalSewingTasks.value, globalSewingTasksCopy)


        // __ Если нет изменений, то выход
        if (diffs.length === 0) {
            return
        }

        diffs.forEach(diff => {

            // __ Если изменилась позиция или дата производства, то меняем ее в глобальном массиве
            if (diff.isPositionChanged || diff.isMoved) {
                const findTask = globalSewingTasks.value.find((task: ISewingTask) => task.id === diff.taskId)
                if (findTask) {

                    if (diff.newTaskPosition) {
                        findTask.position = diff.newTaskPosition
                    }

                    if (diff.isMoved) {
                        findTask.action_at = additionDaysInStrFormat(globalRenderPeriod.value.start, diff.dayToOffset ?? 0)

                    }
                }
            }

        })


        saveChanges()   // __ Сохраняем изменения

    }


    // __ Сохранение изменений (Синхронизация с сервером)
    const saveChanges = async () => {
        const diffsInClobalSewingTasks = getSewingTasksDiff(globalSewingTasks.value, globalSewingTasksCopy)

        console.log(diffsInClobalSewingTasks)
        console.log('Сохраняем изменения')

        // return

        const result = await jwtPost(URL_SEWING_TASKS_UPDATE, { diffs: diffsInClobalSewingTasks })
        if (DEBUG) console.log('saveChanges: ', result)


        // __ Если есть добавление новых элементов в БД, то обновляем данные, чтобы получить id
        // __ Если это изменение позиции, то просто пишем в базу
        if (isAddItemsInDiffsPresents(diffsInClobalSewingTasks)) {

            // __ Получаем СЗ с сервера и реактивное обновление
            getSewingTasks()
            console.log('Server data updated')
        } else {

            globalSewingTasksCopy = JSON.parse(JSON.stringify(globalSewingTasks.value))     // __ копия для отслеживания изменений
        }


        return result.data


    }


    // --- ------------------------------------------------------------------------------------------


    // __ Получение СЗ Пошива с сервера за период
    const getSewingTasks = async (period: IPeriod | null = null) => {
        let response
        if (period) {
            response = await jwtGet(URL_SEWING_TASKS, { period })
        } else {
            response = await jwtGet(URL_SEWING_TASKS)
        }
        const result = await response

        globalSewingTasks.value = result.data                               // __ кэшируем
        globalSewingTasksCopy   = JSON.parse(JSON.stringify(result.data))     // __ копия для отслеживания изменений

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
        const result = await jwtPatch(URL_SEWING_TASK_STATUSES_COLOR_PATCH, { id: sewingTaskStatusId, color })
        if (DEBUG) console.log('SewingStore: patchSewingTaskStatusColor', result)
        return result.data
    }


    // __ Тут следим за состоянием глобальных данных с сервера и обновляем локальные данные
    // watch(() => globalSewingTasks.value, () => {
    //
    //     console.log('save task')
    //
    //
    // }, { immediate: true, deep: true })


    return {
        globalSewingTasks,
        globalRenderPeriod,

        // globalDiffs,

        globalSewingTaskTimesShow,
        globalSewingTaskFullDaysShow,
        globalSewingTaskOrderTypeColor,
        globalManageTaskCardActiveSewingLine,
        globalSewingTaskActiveOrderId,

        getSewingTasks,
        getSewingTaskStatuses,
        patchSewingTaskStatusColor,

        addSewingTaskToGlobal,
        applyChanges,
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
