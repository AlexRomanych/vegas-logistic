// Хранилище для ПЯ Швейки

import { defineStore } from 'pinia'
import { ref } from 'vue'

import { jwtGet, jwtPost, /*jwtDelete,*/ jwtPatch, jwtPut_, jwtPut, jwtPatch_, jwtDelete } from '@/app/utils/jwt_api'
import type {
    IPeriod, IRenderMatrixDiff, ISewingDayWorker, ISewingOperation, ISewingOperationSchema,
    ISewingOperationUpdateObject, ISewingTask,
    ISewingTaskLine, ISewingTaskLinesSubgroup, ISewingTaskStatusEntity, ISewingTaskStatusesSet,
} from '@/types'


// import { DEBUG } from '@/app/constants/common.ts'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import { additionDaysInStrFormat } from '@/app/helpers/helpers_date'
import {
    getSewingTasksDiff, isAddItemsInDiffsPresents, mergeSewingTasks, repositionSewingTaskInDay,
    repositionSewingTaskLines,
} from '@/app/helpers/manufacture/helpers_sewing.ts'
import { isNumber } from '@/app/helpers/helpers_lib.ts'

const DEBUG = true

// Устанавливаем глобальные переменные
// const API_PREFIX                           = '/api/v1/' // Префикс API
const URL_CUTTING_TASKS                      = '/cutting/tasks'                       // URL для получения Сменных заданий
const URL_CUTTING_TASKS_ADD_BY_ORDER_ID      = '/cutting/tasks/add/order'             // URL для добавления Сменных заданий по id Заявки
const URL_CUTTING_TASKS_DELETE_BY_ORDER_ID   = '/cutting/tasks/delete/order'          // URL для удаления Сменных заданий по id Заявки
const URL_CUTTING_TASKS_ORDER_ID             = '/cutting/tasks/order'                 // URL для получения Сменных заданий по id Заявки
const URL_CUTTING_TASKS_STATUS               = '/cutting/tasks/status'                // URL для получения Сменных заданий по статусу
const URL_CUTTING_TASKS_STATUS_PERIOD        = '/cutting/tasks/status/period'         // URL для получения Сменных заданий по статусу в периоде
const URL_CUTTING_TASKS_STATUS_BEFORE_DATE   = '/cutting/tasks/status/date/before'    // URL для получения Сменных заданий по статусу
const URL_CUTTING_TASKS_STATUS_ON_DATE       = '/cutting/tasks/status/date/on'        // URL для получения Сменных заданий по статусу в определенный день
const URL_CUTTING_TASKS_STATUS_ON_DATE_CHECK = '/cutting/tasks/status/date/on/check'  // URL для проверки наличия Сменных заданий по статусу в определенный день
const URL_CUTTING_TASKS_COMMENT              = '/cutting/tasks/comment'               // URL для изменения комментария к Сменному заданию
const URL_CUTTING_TASKS_UPDATE               = '/cutting/tasks/update'                // URL для обновления Сменных заданий
const URL_CUTTING_TASKS_ACTION_AT_SET        = '/cutting/tasks/action/set'            // URL для установки даты выполнения (action_at) СЗ
const URL_CUTTING_TASK_LINE_DONE             = '/cutting/tasks/line/done'             // URL для установки статуса "Выполнено" для записи СЗ
const URL_CUTTING_TASK_LINE_FALSE            = '/cutting/tasks/line/false'            // URL для установки статуса "Не Выполнено" для записи СЗ
const URL_CUTTING_TASK_LINE_RESET            = '/cutting/tasks/line/reset'            // URL для сброса статуса для записи СЗ
const URL_CUTTING_TASK_STATUSES              = '/cutting/task/statuses'               // URL для получения Статуса Движения СЗ
const URL_CUTTING_TASK_STATUSES_SET          = '/cutting/task/statuses/set'           // URL для изменения/добавления Статуса Движения СЗ
const URL_CUTTING_TASK_STATUSES_COLOR_PATCH  = '/cutting/task/statuses/color/patch'   // URL для получения Статуса Движения СЗ
const URL_CUTTING_OPERATIONS                 = '/cutting/operations'                  // URL для получения Типовых операций швейки
const URL_CUTTING_OPERATION                  = '/cutting/operations'                  // URL для получения Типовой операции
const URL_CUTTING_OPERATION_SCHEMAS          = '/cutting/operation/schemas'           // URL для получения Схем Типовых операций швейки
const URL_CUTTING_OPERATION_SCHEMAS_DELETE   = '/cutting/operation/schemas/delete'    // URL для удаления Типовой операции из Схемы Типовых операций
const URL_CUTTING_OPERATION_SCHEMAS_ADD      = '/cutting/operation/schemas/add'       // URL для добавления/изменения Типовой операции в Схеме Типовых операций
const URL_CUTTING_OPERATION_SCHEMAS_CREATE   = '/cutting/operation/schemas/create'    // URL для создания новой Схемы Типовых операций
const URL_CUTTING_OPERATION_SCHEMAS_UPDATE   = '/cutting/operation/schemas/update'    // URL для обновления Схемы Типовых операций
const URL_CUTTING_OPERATION_SCHEMAS_CHECK    = '/cutting/operation/schemas/check'     // URL для проверки суммарного времени Схемы Типовых операций
const URL_CUTTING_OPERATION_SCHEMAS_MODEL    = '/cutting/operation/schemas/models'    // URL для обновления Схемы ТО для модели
const URL_CUTTING_OPERATION_MODELS           = '/cutting/operation/models'            // URL для получения моделей для Типовых операций швейки
const URL_CUTTING_OPERATION_MODELS_DELETE    = '/cutting/operation/models/delete'     // URL для удаления Типовой операции из Модели
const URL_CUTTING_OPERATION_MODELS_ADD       = '/cutting/operation/models/add'        // URL для добавления ТО для моделей
const URL_CUTTING_DAY                        = '/cutting/day'                         // URL для получения рабочего дня
const URL_CUTTING_DAY_DATES                  = '/cutting/day/dates'                   // URL для получения рабочих дней по статусу
const URL_CUTTING_DAY_COMMENT                = '/cutting/day/comment'                 // URL для сохранения комментария к дню
const URL_CUTTING_DAY_WORKERS_ACTIVE         = '/workers/active'                     // URL для получения активных рабочих
const URL_CUTTING_DAY_WORKER_ADD             = '/cutting/day/worker/add'              // URL для добавления исполнителя к дню
const URL_CUTTING_DAY_WORKER_GROUP_ADD       = '/cutting/day/workers/add'             // URL для добавления группы исполнителей к дню
const URL_CUTTING_DAY_WORKER_REMOVE          = '/cutting/day/worker/remove'           // URL для удаления исполнителя к дню
const URL_CUTTING_DAY_RESPONSIBLE_ADD        = '/cutting/day/responsible/add'         // URL для добавления ответственного к дню
const URL_CUTTING_DAY_RESPONSIBLE_REMOVE     = '/cutting/day/responsible/remove'      // URL для удаления ответственного к дню
const URL_CUTTING_DAY_START                  = '/cutting/day/start'                   // URL для старта дня СЗ
const URL_CUTTING_DAY_FINISH                 = '/cutting/day/finish'                  // URL для финиш дня СЗ
const URL_CUTTING_DAY_READY_GET              = '/cutting/day/ready/get'               // URL для получения маячка готовности дня с СЗ к добавлению новых СЗ
const URL_CUTTING_DAY_READY_SET              = '/cutting/day/ready/set'               // URL для установки маяка готовности к добавлению новых СЗ
const URL_CUTTING_DAY_READY_UNSET            = '/cutting/day/ready/unset'             // URL для снятия маяка готовности к добавлению новых СЗ

export const useCuttingStore = defineStore('cutting', () => {

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

    // __ Массив СЗ, готовых к выполнению
    const globalSewingTasksPending = ref<ISewingTask[]>([])

    // __ Копия массива СЗ Пошива для отслеживания изменений
    let globalSewingTasksPendingCopy: ISewingTask[] = []

    // __ Период рендеринга календаря
    const globalRenderPeriod = ref<IPeriod>(PERIOD_DRAFT)

    // __ Показывать ли Трудозатраты в календаре СЗ Пошива
    const globalSewingTaskTimesShow = ref(true)

    // __ Показывать ли Раскрытый день или нет в календаре СЗ Пошива
    const globalSewingTaskFullDaysShow = ref(true)

    // __ Раскрашивать заявки в календаре в цвет Типа Заявки или в цвет Статусов Движения Заявок
    const globalSewingTaskOrderTypeColor = ref(false)

    // __ Текущая Запись (SewingLine) в карточке СЗ в календаре СЗ Пошива
    const globalManageTaskCardActiveSewingLine = ref<ISewingTaskLine | null>(null)

    // __ Текущее Заявка, на которое ссылается кликнутое СЗ (для календаря для подсветки СЗ, которые ссылаются на одну заявку)
    const globalSewingTaskActiveOrderId = ref<number | null>(null)

    // __ Глобальное состояние разницы состояний до и после редактирования в карточке СЗ или перетаскивания в календаре
    // const globalDiffs = ref<IRenderMatrixDiff[]>([])

    // __ Статусы Движения СЗ
    const globalSewingTaskStatuses = ref<ISewingTaskStatusEntity[]>([])

    // __ Массив Рабочих
    const globalWorkers = ref<ISewingDayWorker[]>([])

    // --- ------------------------------------------------------------------------------------------

    // __ Объект печати СЗ
    const globalSewingTaskPrintData = ref<ISewingTaskLinesSubgroup[]>([])


    // const planStore   = usePlansStore()
    // const {planPeriodGlobal} = storeToRefs(planStore)


    // --- ------------------------------------------------------------------------------------------
    // --- ---------------- Тут вся логика по управлению и сохранению частей СЗ ---------------------
    // --- ------------------------------------------------------------------------------------------

    /**
     * __ Добавление новой части СЗ и изменение старой части СЗ, на основе которого была создана новая часть
     * @param addSewingTask     - __ СЗ, которое уже было сформировано на основе старой части СЗ (правая панель)__
     * @param leftPanel         - __ контент в новом СЗ (правая панель)__
     * @param oldSewingTask     - __ СЗ, на основе которого формируется новая часть СЗ (левая панель)__
     * @param rightPanel        - __ контент в старом СЗ (левая панель)__
     */
    const addSewingTaskToGlobal = async (
        oldSewingTask: ISewingTask,
        leftPanel: ISewingTaskLine[],
        addSewingTask: ISewingTask | null    = null,
        rightPanel: ISewingTaskLine[] | null = null,
    ) => {

        leftPanel                  = repositionSewingTaskLines(leftPanel)   // __ Пересчитываем позиции для строк СЗ (SewingLines[])
        oldSewingTask.sewing_lines = leftPanel              // __ oldSewingTask приходит по ссылке

        // __ Если есть правая панель, то добавляем ее в массив СЗ
        if (addSewingTask && rightPanel) {

            // console.log('passed')

            rightPanel                 = repositionSewingTaskLines(rightPanel)  // __ Пересчитываем позиции для строк СЗ (SewingLines[])
            addSewingTask.sewing_lines = rightPanel             // __ addSewingTask приходит новым объектом

            // __ Добавляем новый объект в массив
            globalSewingTasks.value.push(addSewingTask)

            // __ Переопределяем порядок СЗ.
            // __ Находим все СЗ в глабальной переменной с датой созданного СЗ и меняем порядок
            globalSewingTasks.value = repositionSewingTaskInDay(globalSewingTasks.value, addSewingTask.action_at)

        }

        await saveChanges()   // __ Сохраняем изменения
    }


    // __ Устанавливаем содерживое СЗ
    const setSewingTasksLines = (sewingTask: ISewingTask, sewingTaskLines: ISewingTaskLine[]) => {
        sewingTask.sewing_lines = sewingTaskLines
    }


    // __ Устанавливаем комментарий в СЗ
    const applySewingTaskComment = (sewingTaskId: number, comment: string) => {
        const sewingTask = globalSewingTasks.value.find((task: ISewingTask) => task.id === sewingTaskId)

        console.log('sewingTask: ', sewingTask)
        console.log('comment: ', comment)
        if (sewingTask) {
            sewingTask.comment = comment
        }
    }

    // __ Применение изменений
    const applyChanges = async (diffs: IRenderMatrixDiff[] = []) => {

        // __ Если нет изменений, то выход
        if (diffs.length === 0) {
            return
        }

        // __ Если нет статусов, то получаем их с сервера
        if (globalSewingTaskStatuses.value.length === 0) {
            await getSewingTaskStatuses()
        }

        diffs.forEach(diff => {

            // __ Если изменилась позиция или дата производства или статус, то меняем ее в глобальном массиве
            if (diff.isPositionChanged || diff.isMoved || diff.statusId) {
                const findTask = globalSewingTasks.value.find((task: ISewingTask) => task.id === diff.taskId)
                if (findTask) {

                    if (diff.newTaskPosition) {
                        findTask.position = diff.newTaskPosition
                    }

                    if (diff.isMoved) {
                        findTask.action_at = additionDaysInStrFormat(globalRenderPeriod.value.start, diff.dayToOffset ?? 0)
                    }

                    if (diff.statusId) {
                        const findStatus = globalSewingTaskStatuses.value.find((status: ISewingTaskStatusEntity) => status.id === diff.statusId)
                        if (findStatus) {
                            findTask.current_status.id = findStatus.id
                            findTask.current_status.name = findStatus.name
                            findTask.current_status.color = findStatus.color
                        }

                    }
                }
            }
        })

        return await saveChanges()   // __ Сохраняем изменения
    }

    // __ Объединение СЗ для одинаковых Заявок в одном календарном дне
    const mergeTasks = (sewingTasks: ISewingTask[]) => {

        // __ Если массив СЗ меньше 2, то выход
        if (sewingTasks.length < 2) {
            return
        }

        // __ Объединяем СЗ
        const mergedTasks = mergeSewingTasks(sewingTasks)

        // __ Пересчитываем позиции !!! Важно выполнение после объединения СЗ
        mergedTasks[0].sewing_lines = repositionSewingTaskLines(mergedTasks[0].sewing_lines)

        // __ Заменяем СЗ в глобальном массиве
        const findTask = globalSewingTasks.value.find((task: ISewingTask) => task.id === mergedTasks[0].id)
        if (findTask) {
            findTask.sewing_lines = mergedTasks[0].sewing_lines
        }

        // __ Удаляем лишние СЗ
        for (let i = 1; i < sewingTasks.length; i++) {

            // __ Находим то, что нужно удалить
            const workTask = globalSewingTasks.value.find((task: ISewingTask) => task.id === sewingTasks[i].id)
            if (workTask) {

                // __ Удаляем
                globalSewingTasks.value = globalSewingTasks.value.filter((task: ISewingTask) => {
                    return task.id !== sewingTasks[i].id
                })

                // __ Переопределяем порядок СЗ в дне, из которого удалили
                globalSewingTasks.value = repositionSewingTaskInDay(globalSewingTasks.value, workTask.action_at)
            }
        }

        // __ Переопределяем порядок СЗ в дне, в котором добавили
        globalSewingTasks.value = repositionSewingTaskInDay(globalSewingTasks.value, mergedTasks[0].action_at)

    }

    // __ Применение объединения СЗ для массива СЗ
    const applyMergeTasks = async (sewingTasks: ISewingTask[]) => {

        mergeTasks(sewingTasks)
        return await saveChanges()
    }

    // __ Применение объединения СЗ для массива массивов СЗ  [[...], [...]]
    const applyMergeTasksGroups = async (sewingTasksGroups: ISewingTask[][]) => {
        sewingTasksGroups.forEach(sewingTasks => mergeTasks(sewingTasks))
        return await saveChanges()
    }


    // __ Сохранение изменений (Синхронизация с сервером)
    const saveChanges = async (globalArray = globalSewingTasks.value, globalArrayCopy = globalSewingTasksCopy, period: IPeriod | null = null) => {
        const diffsInGlobalSewingTasks = getSewingTasksDiff(globalArray, globalArrayCopy)

        // __ Если нет изменений, то выход
        if (diffsInGlobalSewingTasks.length === 0) {
            return
        }

        console.log(diffsInGlobalSewingTasks)
        console.log('Сохраняем изменения')

        const result = await jwtPost(URL_CUTTING_TASKS_UPDATE, { diffs: diffsInGlobalSewingTasks })
        if (DEBUG) console.log('saveChanges: ', result)


        // __ Если есть добавление новых элементов в БД, то обновляем данные, чтобы получить id
        // __ Если это изменение позиции, то просто пишем в базу
        if (isAddItemsInDiffsPresents(diffsInGlobalSewingTasks)) {

            // __ Получаем СЗ с сервера и реактивное обновление
            await getSewingTasks(period)
            console.log('Server data updated')
        } else {

            globalSewingTasksCopy = JSON.parse(JSON.stringify(globalArray))     // __ копия для отслеживания изменений
            // globalArrayCopy = JSON.parse(JSON.stringify(globalArray))     // __ копия для отслеживания изменений
        }

        return result
    }

    // __ Сохранение изменений (Синхронизация с сервером)
    const saveChanges_Old = async () => {
        const diffsInGlobalSewingTasks = getSewingTasksDiff(globalSewingTasks.value, globalSewingTasksCopy)

        // __ Если нет изменений, то выход
        if (diffsInGlobalSewingTasks.length === 0) {
            return
        }

        console.log(diffsInGlobalSewingTasks)
        console.log('Сохраняем изменения')

        const result = await jwtPost(URL_CUTTING_TASKS_UPDATE, { diffs: diffsInGlobalSewingTasks })
        if (DEBUG) console.log('saveChanges: ', result)


        // __ Если есть добавление новых элементов в БД, то обновляем данные, чтобы получить id
        // __ Если это изменение позиции, то просто пишем в базу
        if (isAddItemsInDiffsPresents(diffsInGlobalSewingTasks)) {

            // __ Получаем СЗ с сервера и реактивное обновление
            await getSewingTasks()
            console.log('Server data updated')
        } else {

            globalSewingTasksCopy = JSON.parse(JSON.stringify(globalSewingTasks.value))     // __ копия для отслеживания изменений
        }

        return result.data
    }
    // --- ------------------------------------------------------------------------------------------


    // --- ----------------------------------------------------------
    // --- ------------------- Сменные задания ----------------------
    // --- ----------------------------------------------------------

    // __ Получение СЗ Пошива с сервера за период
    const getSewingTasks = async (period: IPeriod | null = null) => {
        let response
        if (period) {
            response = await jwtGet(URL_CUTTING_TASKS, { period })
        } else {
            response = await jwtGet(URL_CUTTING_TASKS)
        }
        const result = await response

        globalSewingTasks.value = result.data                                   // __ кэшируем
        globalSewingTasksCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений

        if (DEBUG) console.log('SewingStore: getSewingTasks: ', result)
        return result.data
    }


    // __ Получение СЗ Пошива по ID Заявки
    const getSewingTasksByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtGet(`${URL_CUTTING_TASKS_ORDER_ID}/${id}`)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getSewingTasksByOrderId: ', result)
        return result.data
    }


    // __ Удаление СЗ Пошива по ID Заявки
    const deleteSewingTasksByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtDelete(URL_CUTTING_TASKS_DELETE_BY_ORDER_ID, { id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: deleteSewingTasksByOrderId: ', result)
        return result
    }

    // __ Добавление СЗ Пошива по ID Заявки
    const addSewingTasksByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtPost(URL_CUTTING_TASKS_ADD_BY_ORDER_ID, { id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: addSewingTasksByOrderId: ', result)
        return result
    }

    // __ Получение СЗ Пошива по статусу или массиву статусов до определенной даты
    const getSewingTasksByStatusBeforeDate = async (date: string, status: number[] | number | null = null) => {
        let response
        if (status) {
            if (isNumber(status)) {
                status = [status]
            }

            response = await jwtGet(URL_CUTTING_TASKS_STATUS_BEFORE_DATE, { date, statuses: status })
        } else {
            response = await jwtGet(date, URL_CUTTING_TASKS_STATUS_BEFORE_DATE)
        }
        const result = await response

        if (DEBUG) console.log('SewingStore: getSewingTasksByStatusBeforeDate: ', result)
        return result.data
    }


    // __ Получение СЗ Пошива по статусу или массиву статусов в определенную дату
    const getSewingTasksByStatusOnDate = async (date: string, status: number[] | number | null = null) => {
        let response
        if (status) {
            if (isNumber(status)) {
                status = [status]
            }

            response = await jwtGet(URL_CUTTING_TASKS_STATUS_ON_DATE, { date, statuses: status })
        } else {
            response = await jwtGet(date, URL_CUTTING_TASKS_STATUS_ON_DATE)
        }
        const result = await response

        if (DEBUG) console.log('SewingStore: getSewingTasksByStatusOnDate: ', result)
        return result.data
    }

    // __ Проверка на наличие СЗ Пошива по статусу или массиву статусов в определенную дату
    const checkSewingTasksByStatusOnDate = async (date: string, status: number[] | number | null = null) => {
        let response
        if (status) {
            if (isNumber(status)) {
                status = [status]
            }

            response = await jwtGet(URL_CUTTING_TASKS_STATUS_ON_DATE_CHECK, { date, statuses: status })
        } else {
            response = await jwtGet(date, URL_CUTTING_TASKS_STATUS_ON_DATE_CHECK)
        }
        const result = await response

        if (DEBUG) console.log('SewingStore: checkSewingTasksByStatusOnDate: ', result)
        return result.data
    }

    // __ Сохранение Комментария к Сменному заданию - СЗ
    const setSewingTaskComment = async (id: number, comment: string | null = null) => {
        const response = await jwtPost(URL_CUTTING_TASKS_COMMENT, { id, comment })
        const result   = await response
        if (DEBUG) console.log('SewingStore: setSewingTaskComment: ', result)
        return result.data
    }

    // __ Установка даты выполнения СЗ
    const setSewingTaskActionAt = async (id: number, date: string) => {
        const response = await jwtPost(URL_CUTTING_TASKS_ACTION_AT_SET, { id, date })
        const result   = await response
        if (DEBUG) console.log('SewingStore: setSewingTaskActionAt: ', result)
        return result.data
    }

    // --- ----------------------------------------------------------
    // --- ------ Сменные задания к выполнению (Pending) ------------
    // --- ----------------------------------------------------------

    // __ Разделение линий СЗ при выполнении СЗ
    const divideLineInSewingTaskPending = async (sewingTask: ISewingTask, period: IPeriod | null = null) => {

        const findTask = globalSewingTasks.value.find((task: ISewingTask) => task.id === sewingTask.id)
        if (!findTask) {
            return
        }
        console.log('findTask: ', findTask)

        repositionSewingTaskLines(findTask)
        // const result = await saveChanges()
        return await saveChanges(globalSewingTasks.value, globalSewingTasksCopy, period)
        // return saveChanges(globalSewingTasksPending.value, globalSewingTasksPendingCopy)
    }

    // __ Получение СЗ Пошива по статусу или массиву статусов
    const getSewingTasksByStatus = async (status: number[] | number | null = null) => {
        let response
        if (status) {
            if (isNumber(status)) {
                status = [status]
            }

            response = await jwtGet(URL_CUTTING_TASKS_STATUS, { statuses: status })
            // response = await jwtGet(`${URL_CUTTING_TASKS_STATUS}/${status}`)
        } else {
            response = await jwtGet(URL_CUTTING_TASKS_STATUS)
        }
        const result = await response

        globalSewingTasksPending.value = result.data                                   // __ кэшируем
        globalSewingTasksPendingCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений


        if (DEBUG) console.log('SewingStore: getSewingTasksByStatus: ', result)
        return result.data
    }

    // __ Получение СЗ Пошива за период
    const getSewingTasksByStatusAndPeriod = async (period: IPeriod | null = null, statuses: number[] | number | null = null) => {
        let response

        if (statuses && isNumber(statuses)) {
            statuses = [statuses]
        }

        // __ Тут именно такая проверка, потому что если status === 0, то он не передается в запросе
        if (period && statuses !== null) {
            response = await jwtGet(URL_CUTTING_TASKS_STATUS_PERIOD, { period, statuses })
        } else if (period) {
            response = await jwtGet(URL_CUTTING_TASKS_STATUS_PERIOD, { period })
        } else if (statuses !== null) {
            response = await jwtGet(URL_CUTTING_TASKS_STATUS_PERIOD, { statuses })
        } else {
            response = await jwtGet(URL_CUTTING_TASKS_STATUS_PERIOD)
        }

        const result = await response

        globalSewingTasksPending.value = result.data                                   // __ кэшируем
        globalSewingTasksPendingCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений

        if (DEBUG) console.log('SewingStore: getSewingTasksByStatusAndPeriod: ', result)
        return result.data
    }


    // __ Проверка на наличие СЗ с определенным статусом на определенную дату
    // const checkSewingTasksByStatusAndDate = async (date: string, status: number[] | number | null = null) => {
    //     let response
    //     if (status) {
    //         if (isNumber(status)) {
    //             status = [status]
    //         }
    //
    //         response = await jwtGet(URL_CUTTING_TASKS_STATUS, { statuses: status })
    //         // response = await jwtGet(`${URL_CUTTING_TASKS_STATUS}/${status}`)
    //     } else {
    //         response = await jwtGet(URL_CUTTING_TASKS_STATUS)
    //     }
    //     const result = await response
    //
    //     globalSewingTasksPending.value = result.data                                   // __ кэшируем
    //     globalSewingTasksPendingCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений
    //
    //
    //     if (DEBUG) console.log('SewingStore: getSewingTasksByStatus: ', result)
    //     return result.data
    // }


    // __ Один статус
    const getSewingTasksByStatusOneStatus = async (status: number | null = null) => {
        let response
        if (status) {
            response = await jwtGet(`${URL_CUTTING_TASKS_STATUS}/${status}`)
            // response = await jwtGet(URL_CUTTING_TASKS_STATUS, { period })
        } else {
            response = await jwtGet(URL_CUTTING_TASKS_STATUS)
        }
        const result = await response

        globalSewingTasksPending.value = result.data                                   // __ кэшируем
        // globalSewingTasksCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений

        if (DEBUG) console.log('SewingStore: getSewingTasksByStatusOneStatus: ', result)
        return result.data
    }

    // __ Устанавливаем "Выполнено" на SewingTaskLines
    const setSewingTaskLinesDone = async (sewingTaskLinesIds: number[]) => {
        if (!sewingTaskLinesIds.length) {
            return []
        }
        const result = await jwtPost(URL_CUTTING_TASK_LINE_DONE, { ids: sewingTaskLinesIds })
        if (DEBUG) console.log('SewingStore: setSewingTaskLinesDone: ', result)
        return result.data
    }


    // __ Устанавливаем "Не Выполнено" на SewingTaskLines
    const setSewingTaskLinesFalse = async (sewingTaskLinesIds: number[], falseReason: string | null = null) => {
        if (!sewingTaskLinesIds.length && !falseReason) {
            return []
        }
        const result = await jwtPost(URL_CUTTING_TASK_LINE_FALSE, { ids: sewingTaskLinesIds, reason: falseReason })
        if (DEBUG) console.log('SewingStore: setSewingTaskLinesFalse: ', result)
        return result.data
    }

    // __ Сбрасываем статусы на SewingTaskLines
    const setSewingTaskLinesReset = async (sewingTaskLinesIds: number[]) => {
        if (!sewingTaskLinesIds.length) {
            return []
        }
        const result = await jwtPost(URL_CUTTING_TASK_LINE_RESET, { ids: sewingTaskLinesIds })
        if (DEBUG) console.log('SewingStore: setSewingTaskLinesReset: ', result)
        return result.data
    }

    // --- ----------------------------------------------------------
    // --- ------------------ Типовые операции ----------------------
    // --- ----------------------------------------------------------
    // __ Получение Типовых операций
    const getSewingOperations = async () => {
        const response = await jwtGet(URL_CUTTING_OPERATIONS)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getSewingOperations: ', result)
        return result.data
    }

    // __ Получение Типовой операции
    const getSewingOperation = async (id: string | number) => {
        const response = await jwtGet(URL_CUTTING_OPERATION + '/' + id)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getSewingOperation: ', result)
        return result.data
    }

    // __ Создаем Типовую операцию
    const createSewingOperation = async (sewingOperation: ISewingOperation) => {
        const result = await jwtPost(URL_CUTTING_OPERATION, sewingOperation)
        if (DEBUG) console.log('SewingStore: createSewingOperation: ', result)
        return result
    }

    // __ Обновляем Типовую операцию
    const updateSewingOperation = async (sewingOperation: ISewingOperation) => {
        const result = await jwtPut_(URL_CUTTING_OPERATION, sewingOperation)
        if (DEBUG) console.log('SewingStore: updateSewingOperation: ', result)
        return result
    }


    // __ Получение Схем Типовых операций
    const getSewingOperationSchemas = async () => {
        const response = await jwtGet(URL_CUTTING_OPERATION_SCHEMAS)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getSewingOperationSchemas: ', result)
        return result.data
    }

    // __ Получение Схемы Типовой операции
    const getSewingOperationSchema = async (id: string | number) => {
        const response = await jwtGet(URL_CUTTING_OPERATION_SCHEMAS + '/' + id)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getSewingOperationSchema: ', result)
        return result.data
    }

    // __ Создание Схемы Типовой операции
    const createSewingOperationSchema = async (schema: ISewingOperationSchema) => {
        const response = await jwtPost(URL_CUTTING_OPERATION_SCHEMAS_CREATE, schema)
        const result   = await response
        if (DEBUG) console.log('SewingStore: createSewingOperationSchema: ', result)
        return result
    }

    // __ Обновление Схемы Типовой операции
    const updateSewingOperationSchema = async (schema: ISewingOperationSchema) => {
        const response = await jwtPut(URL_CUTTING_OPERATION_SCHEMAS_UPDATE, schema)
        const result   = await response
        if (DEBUG) console.log('SewingStore: updateSewingOperationSchema: ', result)
        return result
    }

    // __ Удаление Типовой операции из схемы
    const deleteSewingOperationFromSchema = async (deleteObject: ISewingOperationUpdateObject) => {
        const response = await jwtDelete(URL_CUTTING_OPERATION_SCHEMAS_DELETE, deleteObject)
        const result   = await response
        if (DEBUG) console.log('SewingStore: deleteSewingOperationFromSchema: ', result)
        return result.data
    }

    // __ Обновление Типовой операции в схеме
    const addSewingOperationToSchema = async (addObject: ISewingOperationUpdateObject) => {
        const response = await jwtPost(URL_CUTTING_OPERATION_SCHEMAS_ADD, addObject)
        const result   = await response
        if (DEBUG) console.log('SewingStore: addSewingOperationToSchema: ', result)
        return result.data
    }

    // __ Проверка Схемы Типовых операций на суммарное время
    const checkSewingOperationSchemaForSummaryTime = async (schemaId: number | null = null) => {
        if (!schemaId) {
            return null
        }
        const response = await jwtGet(`${URL_CUTTING_OPERATION_SCHEMAS_CHECK}/${schemaId}`)
        const result   = await response
        if (DEBUG) console.log('SewingStore: checkSewingOperationSchemaForSummaryTime: ', result)
        return result.data
    }

    // --- ----------------------------------------------------------
    // --- --------- Типовые операции + Модели ----------------------
    // --- ----------------------------------------------------------
    // __ Получение Моделей для Типовых операций
    const getModelsForLabor = async () => {
        const response = await jwtGet(URL_CUTTING_OPERATION_MODELS)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getModelsForLabor: ', result)
        return result.data
    }

    // __ Обновление Схемы Типовых операций для модели
    const updateModelSewingOperationSchema = async (code_1c: string, schema_id: number) => {
        const response = await jwtPatch(URL_CUTTING_OPERATION_SCHEMAS_MODEL, { code_1c, schema_id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: updateModelSewingOperationSchema: ', result)
        return result.data
    }

    // __ Удаление Типовой опрерации из схемы
    const deleteSewingOperationFromModel = async (deleteObject: ISewingOperationUpdateObject) => {
        const response = await jwtPost(URL_CUTTING_OPERATION_MODELS_DELETE, deleteObject)
        const result   = await response
        if (DEBUG) console.log('SewingStore: deleteSewingOperationFromModel: ', result)
        return result.data
    }

    // __ Обновление Типовой операции в схеме
    const addSewingOperationToModel = async (addObject: ISewingOperationUpdateObject) => {
        const response = await jwtPost(URL_CUTTING_OPERATION_MODELS_ADD, addObject)
        const result   = await response
        if (DEBUG) console.log('SewingStore: addSewingOperationToModel: ', result)
        return result.data
    }

    // --- ----------------------------------------------------------
    // --- ------------------- Статусы СЗ ---------------------------
    // --- ----------------------------------------------------------

    // __ Получение Статусов Движения СЗ
    const getSewingTaskStatuses = async () => {
        const response = await jwtGet(URL_CUTTING_TASK_STATUSES)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getSewingTaskStatuses: ', result)
        globalSewingTaskStatuses.value = result.data    // __ кэшируем
        return result.data
    }

    // __ Устанавливаем цвет ярлычка Типов заказов (серийная, гаррмем, прогнозная и т.д.)
    const patchSewingTaskStatusColor = async (sewingTaskStatusId: number, color: string) => {
        const result = await jwtPatch(URL_CUTTING_TASK_STATUSES_COLOR_PATCH, { id: sewingTaskStatusId, color })
        if (DEBUG) console.log('SewingStore: patchSewingTaskStatusColor', result)
        await getSewingTaskStatuses()   // __ Обновляем статусы, чтобы был актуальный цвет
        return result.data
    }

    // __ Устанавливаем статусы для СЗ.
    // __ data: [{ task: number, status: number }]
    const setSewingTasksStatuses = async (data: ISewingTaskStatusesSet[]) => {
        const response = await jwtPost(URL_CUTTING_TASK_STATUSES_SET, data)
        const result   = await response
        if (DEBUG) console.log('SewingStore: setStatuses: ', result)
        return result.data
    }


    // --- ----------------------------------------------------------
    // --- ------------------- Производственный день ----------------
    // --- ----------------------------------------------------------

    // __ Получение производственного дня по дате и смене
    const getSewingDayByDateAndChange = async (date: string, change: number = 1) => {
        const response = await jwtGet(`${URL_CUTTING_DAY}/${date}/${change}`)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getSewingDayByDateAndChange: ', result)
        return result.data
    }

    // __ Сохранение Комментария к производственному дню
    const setSewingDayComment = async (id: number, comment: string | null = null) => {
        const response = await jwtPost(URL_CUTTING_DAY_COMMENT, { id, comment })
        const result   = await response
        if (DEBUG) console.log('SewingStore: setSewingDayComment: ', result)
        return result.data
    }

    // __ Получение производственных дней по массиву дат
    // __ Тут по хорошему надо прикрутить еще и смену, но оставим на потом
    const getSewingDaysByDates = async (dates: string[]) => {
        if (!dates.length) {
            return []
        }

        const response = await jwtGet(URL_CUTTING_DAY_DATES, { dates })
        const result   = await response
        if (DEBUG) console.log('SewingStore: getSewingDaysByDates: ', result)
        return result.data
    }

    // __ Получение активных рабочих
    const getActiveWorkers = async () => {
        if (globalWorkers.value.length) {
            return globalWorkers
        }

        const response = await jwtGet(URL_CUTTING_DAY_WORKERS_ACTIVE)
        const result   = await response

        // __ кэшируем
        globalWorkers.value = result.data
            .filter((w: ISewingDayWorker) => w.id !== 0)
            .sort((a: ISewingDayWorker, b: ISewingDayWorker) => a.surname.localeCompare(b.surname))


        if (DEBUG) console.log('SewingStore: getActiveWorkers: ', result)
        return result.data
    }

    // __ Добавление Рабочего в Производственный день
    const addWorkerToSewingDay = async (day_id: number | null = null, worker_id: number | null = null) => {
        if (!day_id || !worker_id) {
            return
        }
        const response = await jwtPost(URL_CUTTING_DAY_WORKER_ADD, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: addWorkerToSewingDay: ', result)
        return result.data
    }

    // __ Добавление Группы Рабочих в Производственный день
    const addWorkersToSewingDay = async (day_id: number | null = null, worker_ids: number[] | null = null) => {
        if (!day_id || !worker_ids) {
            return
        }
        const response = await jwtPost(URL_CUTTING_DAY_WORKER_GROUP_ADD, { day_id, worker_ids })
        const result   = await response
        if (DEBUG) console.log('SewingStore: addWorkersGroupToSewingDay: ', result)
        return result.data
    }

    // __ Удаление Рабочего из Производственного дня
    const removeWorkerFromSewingDay = async (day_id: number, worker_id: number) => {
        const response = await jwtPost(URL_CUTTING_DAY_WORKER_REMOVE, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: removeWorkerToSewingDay: ', result)
        return result.data
    }

    // __ Добавление Ответственного в Производственный день
    const addResponsibleToSewingDay = async (day_id: number, worker_id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_RESPONSIBLE_ADD, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: addResponsibleToSewingDay: ', result)
        return result.data
    }

    // __ Удаление Ответственного из Производственного дня
    const removeResponsibleFromSewingDay = async (day_id: number, worker_id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_RESPONSIBLE_REMOVE, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: removeResponsibleFromSewingDay: ', result)
        return result.data
    }

    // __ Старт СЗ
    const startSewingDay = async (id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_START, { id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: startSewingDay: ', result)
        return result.data
    }

    // __ Стоп СЗ
    const finishSewingDay = async (id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_FINISH, { id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: finishSewingDay: ', result)
        return result.data
    }

    // __ Получение маячка готовности дня с СЗ к добавлению новых СЗ
    const readyGetSewingDay = async (date: string, change: number = 1) => {
        const response = await jwtGet(`${URL_CUTTING_DAY_READY_GET}/${date}/${change}`)
        const result   = await response
        if (DEBUG) console.log('SewingStore: readyGetSewingDay: ', result)
        return result.data
    }

    // __ Установки маяка готовности к добавлению новых СЗ
    const readySetSewingDay = async (id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_READY_SET, { id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: readySetSewingDay: ', result)
        return result.data
    }

    // __ Снятие маяка готовности к добавлению новых СЗ
    const readyUnsetSewingDay = async (id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_READY_UNSET, { id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: readyUnsetSewingDay: ', result)
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
        globalSewingTaskStatuses,
        globalRenderPeriod,

        globalSewingTaskTimesShow,
        globalSewingTaskFullDaysShow,
        globalSewingTaskOrderTypeColor,
        globalManageTaskCardActiveSewingLine,
        globalSewingTaskActiveOrderId,

        globalSewingTasksPending,

        globalWorkers,

        globalSewingTaskPrintData,

        getSewingTasks,
        getSewingTasksByOrderId,
        getSewingTasksByStatus,
        getSewingTasksByStatusAndPeriod,
        getSewingTasksByStatusBeforeDate,
        getSewingTasksByStatusOnDate,
        checkSewingTasksByStatusOnDate,
        deleteSewingTasksByOrderId,
        addSewingTasksByOrderId,
        setSewingTaskComment,
        setSewingTaskActionAt,
        getSewingTaskStatuses,
        patchSewingTaskStatusColor,
        setSewingTaskLinesDone,
        setSewingTaskLinesFalse,
        setSewingTaskLinesReset,

        getSewingOperations,
        getSewingOperation,
        createSewingOperation,
        updateSewingOperation,

        getSewingOperationSchemas,
        getSewingOperationSchema,
        createSewingOperationSchema,
        updateSewingOperationSchema,
        deleteSewingOperationFromSchema,
        addSewingOperationToSchema,
        checkSewingOperationSchemaForSummaryTime,

        getModelsForLabor,
        updateModelSewingOperationSchema,
        deleteSewingOperationFromModel,
        addSewingOperationToModel,

        setSewingTasksStatuses,

        getSewingDayByDateAndChange,
        setSewingDayComment,
        getSewingDaysByDates,
        getActiveWorkers,
        addWorkerToSewingDay, addWorkersToSewingDay,
        removeWorkerFromSewingDay,
        addResponsibleToSewingDay,
        removeResponsibleFromSewingDay,
        startSewingDay, finishSewingDay, readyGetSewingDay, readySetSewingDay, readyUnsetSewingDay,
        divideLineInSewingTaskPending,

        addSewingTaskToGlobal,
        saveChanges,
        applyChanges,
        applyMergeTasks,
        applyMergeTasksGroups,
        setSewingTasksLines,
        applySewingTaskComment,
    }
})
