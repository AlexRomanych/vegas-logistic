// Хранилище для ПЯ Швейки

import { defineStore } from 'pinia'
import { ref } from 'vue'

import { jwtGet, jwtPost, /*jwtDelete,*/ jwtPatch, jwtPut_, jwtPut, jwtPatch_, jwtDelete } from '@/app/utils/jwt_api'
import type {
    IPeriod,
    IRenderMatrixDiff,
    ICuttingDayWorker,
    ICuttingOperation,
    ICuttingOperationSchema,
    ICuttingOperationUpdateObject,
    ICuttingTask,
    ICuttingTaskLine,
    ICuttingTaskLinesSubgroup,
    ICuttingTaskStatusEntity,
    ICuttingTaskStatusesSet,
    ICuttingLineTableSetData,
    ICuttingProcedure,
    ICuttingDetailType,
} from '@/types'


// import { DEBUG } from '@/app/constants/common.ts'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import { additionDaysInStrFormat } from '@/app/helpers/helpers_date'
import {
    getCuttingTasksDiff, isAddItemsInDiffsPresents, mergeCuttingTasks, repositionCuttingTaskInDay,
    repositionCuttingTaskLines,
} from '@/app/helpers/manufacture/helpers_cutting.ts'
import { isNumber } from '@/app/helpers/helpers_lib.ts'

const DEBUG = true

// Устанавливаем глобальные переменные
// const API_PREFIX                           = '/api/v1/' // Префикс API
const URL_CUTTING_TASKS                      = '/cutting/tasks'                       // URL для получения Сменных заданий
const URL_CUTTING_TASKS_ADD_BY_ORDER_ID      = '/cutting/tasks/add/order'             // URL для добавления Сменных заданий по id Заявки
const URL_CUTTING_TASKS_CALC_BY_ORDER_ID     = '/cutting/tasks/calc/order'            // URL для пересчета Кроя по id Заявки
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
const URL_CUTTING_TASK_LINES_TABLE_SET       = '/cutting/tasks/lines/table/set'       // URL для изменения раскройного стола для записи СЗ
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
const URL_CUTTING_DAY_WORKERS_ACTIVE         = '/workers/active'                      // URL для получения активных рабочих
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
const URL_CUTTING_PROCEDURES                 = '/cutting/procedures'                  // URL для получения процедур расчета Раскроя
const URL_CUTTING_PROCEDURES_MODEL           = '/cutting/models/procedures'           // URL для обновления процедуры раскроя для модели
const URL_CUTTING_ANGLE_MODEL                = '/cutting/models/angle'                // URL для обновления Угла Раскроя для Модели
const URL_CUTTING_TEST                       = '/cutting/test'                        // URL для тестирования

export const useCuttingStore = defineStore('cutting', () => {

    // --- ------------------------------------------------------------------------------------------
    // --- -- Этот функционал (Раскроя) будем полностью реализовывать через Store API
    // --- ------------------------------------------------------------------------------------------

    // --- ------------------------------------------------------------------------------------------
    // --- -- Глобальные переменные
    // --- ------------------------------------------------------------------------------------------

    // __ Массив СЗ Раскроя
    const globalCuttingTasks = ref<ICuttingTask[]>([])

    // __ Копия массива СЗ Раскроя для отслеживания изменений
    let globalCuttingTasksCopy: ICuttingTask[] = []

    // __ Массив СЗ, готовых к выполнению
    const globalCuttingTasksPending = ref<ICuttingTask[]>([])

    // __ Копия массива СЗ Раскроя для отслеживания изменений
    let globalCuttingTasksPendingCopy: ICuttingTask[] = []

    // __ Период рендеринга календаря
    const globalRenderPeriod = ref<IPeriod>(PERIOD_DRAFT)

    // __ Показывать ли Трудозатраты в календаре СЗ Раскроя
    const globalCuttingTaskTimesShow = ref(true)

    // __ Показывать ли Раскрытый день или нет в календаре СЗ Раскроя
    const globalCuttingTaskFullDaysShow = ref(true)

    // __ Раскрашивать заявки в календаре в цвет Типа Заявки или в цвет Статусов Движения Заявок
    const globalCuttingTaskOrderTypeColor = ref(false)

    // __ Текущая Запись (CuttingLine) в карточке СЗ в календаре СЗ Раскроя
    const globalManageTaskCardActiveCuttingLine = ref<ICuttingTaskLine | null>(null)

    // __ Текущее Заявка, на которое ссылается кликнутое СЗ (для календаря для подсветки СЗ, которые ссылаются на одну заявку)
    const globalCuttingTaskActiveOrderId = ref<number | null>(null)

    // __ Глобальное состояние разницы состояний до и после редактирования в карточке СЗ или перетаскивания в календаре
    // const globalDiffs = ref<IRenderMatrixDiff[]>([])

    // __ Статусы Движения СЗ
    const globalCuttingTaskStatuses = ref<ICuttingTaskStatusEntity[]>([])

    // __ Массив Рабочих
    const globalWorkers = ref<ICuttingDayWorker[]>([])

    // --- ------------------------------------------------------------------------------------------

    // __ Объект печати СЗ
    const globalCuttingTaskPrintData = ref<ICuttingTaskLinesSubgroup[]>([])


    // const planStore   = usePlansStore()
    // const {planPeriodGlobal} = storeToRefs(planStore)


    // --- ------------------------------------------------------------------------------------------
    // --- ---------------- Тут вся логика по управлению и сохранению частей СЗ ---------------------
    // --- ------------------------------------------------------------------------------------------

    /**
     * __ Добавление новой части СЗ и изменение старой части СЗ, на основе которого была создана новая часть
     * @param addCuttingTask     - __ СЗ, которое уже было сформировано на основе старой части СЗ (правая панель)__
     * @param leftPanel         - __ контент в новом СЗ (правая панель)__
     * @param oldCuttingTask     - __ СЗ, на основе которого формируется новая часть СЗ (левая панель)__
     * @param rightPanel        - __ контент в старом СЗ (левая панель)__
     */
    const addCuttingTaskToGlobal = async (
        oldCuttingTask: ICuttingTask,
        leftPanel: ICuttingTaskLine[],
        addCuttingTask: ICuttingTask | null   = null,
        rightPanel: ICuttingTaskLine[] | null = null,
    ) => {

        leftPanel                    = repositionCuttingTaskLines(leftPanel)   // __ Пересчитываем позиции для строк СЗ (CuttingLines[])
        oldCuttingTask.cutting_lines = leftPanel              // __ oldCuttingTask приходит по ссылке

        // __ Если есть правая панель, то добавляем ее в массив СЗ
        if (addCuttingTask && rightPanel) {

            // console.log('passed')

            rightPanel                   = repositionCuttingTaskLines(rightPanel)  // __ Пересчитываем позиции для строк СЗ (CuttingLines[])
            addCuttingTask.cutting_lines = rightPanel             // __ addCuttingTask приходит новым объектом

            // __ Добавляем новый объект в массив
            globalCuttingTasks.value.push(addCuttingTask)

            // __ Переопределяем порядок СЗ.
            // __ Находим все СЗ в глабальной переменной с датой созданного СЗ и меняем порядок
            globalCuttingTasks.value = repositionCuttingTaskInDay(globalCuttingTasks.value, addCuttingTask.action_at)

        }

        await saveChanges()   // __ Сохраняем изменения
    }


    // __ Устанавливаем содерживое СЗ
    const setCuttingTasksLines = (cuttingTask: ICuttingTask, cuttingTaskLines: ICuttingTaskLine[]) => {
        cuttingTask.cutting_lines = cuttingTaskLines
    }


    // __ Устанавливаем комментарий в СЗ
    const applyCuttingTaskComment = (cuttingTaskId: number, comment: string) => {
        const cuttingTask = globalCuttingTasks.value.find((task: ICuttingTask) => task.id === cuttingTaskId)

        console.log('cuttingTask: ', cuttingTask)
        console.log('comment: ', comment)
        if (cuttingTask) {
            cuttingTask.comment = comment
        }
    }

    // __ Применение изменений
    const applyChanges = async (diffs: IRenderMatrixDiff[] = []) => {

        // __ Если нет изменений, то выход
        if (diffs.length === 0) {
            return
        }

        // __ Если нет статусов, то получаем их с сервера
        if (globalCuttingTaskStatuses.value.length === 0) {
            await getCuttingTaskStatuses()
        }

        diffs.forEach(diff => {

            // __ Если изменилась позиция или дата производства или статус, то меняем ее в глобальном массиве
            if (diff.isPositionChanged || diff.isMoved || diff.statusId) {
                const findTask = globalCuttingTasks.value.find((task: ICuttingTask) => task.id === diff.taskId)
                if (findTask) {

                    if (diff.newTaskPosition) {
                        findTask.position = diff.newTaskPosition
                    }

                    if (diff.isMoved) {
                        findTask.action_at = additionDaysInStrFormat(globalRenderPeriod.value.start, diff.dayToOffset ?? 0)
                    }

                    if (diff.statusId) {
                        const findStatus = globalCuttingTaskStatuses.value.find((status: ICuttingTaskStatusEntity) => status.id === diff.statusId)
                        if (findStatus) {
                            findTask.current_status.id    = findStatus.id
                            findTask.current_status.name  = findStatus.name
                            findTask.current_status.color = findStatus.color
                        }

                    }
                }
            }
        })

        return await saveChanges()   // __ Сохраняем изменения
    }

    // __ Объединение СЗ для одинаковых Заявок в одном календарном дне
    const mergeTasks = (cuttingTasks: ICuttingTask[]) => {

        // __ Если массив СЗ меньше 2, то выход
        if (cuttingTasks.length < 2) {
            return
        }

        // __ Объединяем СЗ
        const mergedTasks = mergeCuttingTasks(cuttingTasks)

        // __ Пересчитываем позиции !!! Важно выполнение после объединения СЗ
        mergedTasks[0].cutting_lines = repositionCuttingTaskLines(mergedTasks[0].cutting_lines)

        // __ Заменяем СЗ в глобальном массиве
        const findTask = globalCuttingTasks.value.find((task: ICuttingTask) => task.id === mergedTasks[0].id)
        if (findTask) {
            findTask.cutting_lines = mergedTasks[0].cutting_lines
        }

        // __ Удаляем лишние СЗ
        for (let i = 1; i < cuttingTasks.length; i++) {

            // __ Находим то, что нужно удалить
            const workTask = globalCuttingTasks.value.find((task: ICuttingTask) => task.id === cuttingTasks[i].id)
            if (workTask) {

                // __ Удаляем
                globalCuttingTasks.value = globalCuttingTasks.value.filter((task: ICuttingTask) => {
                    return task.id !== cuttingTasks[i].id
                })

                // __ Переопределяем порядок СЗ в дне, из которого удалили
                globalCuttingTasks.value = repositionCuttingTaskInDay(globalCuttingTasks.value, workTask.action_at)
            }
        }

        // __ Переопределяем порядок СЗ в дне, в котором добавили
        globalCuttingTasks.value = repositionCuttingTaskInDay(globalCuttingTasks.value, mergedTasks[0].action_at)

    }

    // __ Применение объединения СЗ для массива СЗ
    const applyMergeTasks = async (cuttingTasks: ICuttingTask[]) => {

        mergeTasks(cuttingTasks)
        return await saveChanges()
    }

    // __ Применение объединения СЗ для массива массивов СЗ  [[...], [...]]
    const applyMergeTasksGroups = async (cuttingTasksGroups: ICuttingTask[][]) => {
        cuttingTasksGroups.forEach(cuttingTasks => mergeTasks(cuttingTasks))
        return await saveChanges()
    }


    // __ Сохранение изменений (Синхронизация с сервером)
    const saveChanges = async (globalArray = globalCuttingTasks.value, globalArrayCopy = globalCuttingTasksCopy, period: IPeriod | null = null) => {
        const diffsInGlobalCuttingTasks = getCuttingTasksDiff(globalArray, globalArrayCopy)

        // __ Если нет изменений, то выход
        if (diffsInGlobalCuttingTasks.length === 0) {
            return
        }

        console.log(diffsInGlobalCuttingTasks)
        console.log('Сохраняем изменения')

        const result = await jwtPost(URL_CUTTING_TASKS_UPDATE, { diffs: diffsInGlobalCuttingTasks })
        if (DEBUG) console.log('saveChanges: ', result)


        // __ Если есть добавление новых элементов в БД, то обновляем данные, чтобы получить id
        // __ Если это изменение позиции, то просто пишем в базу
        if (isAddItemsInDiffsPresents(diffsInGlobalCuttingTasks)) {

            // __ Получаем СЗ с сервера и реактивное обновление
            await getCuttingTasks(period)
            console.log('Server data updated')
        } else {

            globalCuttingTasksCopy = JSON.parse(JSON.stringify(globalArray))     // __ копия для отслеживания изменений
            // globalArrayCopy = JSON.parse(JSON.stringify(globalArray))     // __ копия для отслеживания изменений
        }

        return result
    }


    // __ Меняем динамически Столы раскроя в глобальном массиве, чтобы не перезагружать данные с сервера
    const setGlobalArrayChangeTables = (data: ICuttingLineTableSetData[]) => {
        for (const item of data) {
            let isFind = false
            for (const task of globalCuttingTasks.value) {
                for (const line of task.cutting_lines) {
                    if (item.id === line.id) {
                        line.table = item.table
                        isFind     = true
                        break
                    }
                    if (isFind) {
                        break
                    }
                }
                if (isFind) {
                    break
                }
            }
        }

        // __ Копия для отслеживания изменений
        globalCuttingTasksCopy = JSON.parse(JSON.stringify(globalCuttingTasks.value))
    }


    // --- ------------------------------------------------------------------------------------------


    // --- ----------------------------------------------------------
    // --- ------------------- Сменные задания ----------------------
    // --- ----------------------------------------------------------

    // __ Получение СЗ Раскроя с сервера за период
    const getCuttingTasks = async (period: IPeriod | null = null) => {
        let response
        if (period) {
            response = await jwtGet(URL_CUTTING_TASKS, { period })
        } else {
            response = await jwtGet(URL_CUTTING_TASKS)
        }
        const result = await response

        globalCuttingTasks.value = result.data                                   // __ кэшируем
        globalCuttingTasksCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений

        if (DEBUG) console.log('CuttingStore: getCuttingTasks: ', result)
        return result.data
    }


    // __ Получение СЗ Раскроя по ID Заявки
    const getCuttingTasksByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtGet(`${URL_CUTTING_TASKS_ORDER_ID}/${id}`)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: getCuttingTasksByOrderId: ', result)
        return result.data
    }


    // __ Удаление СЗ Раскроя по ID Заявки
    const deleteCuttingTasksByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtDelete(URL_CUTTING_TASKS_DELETE_BY_ORDER_ID, { id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: deleteCuttingTasksByOrderId: ', result)
        return result
    }

    // __ Добавление СЗ Раскроя по ID Заявки
    const addCuttingTasksByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtPost(URL_CUTTING_TASKS_ADD_BY_ORDER_ID, { id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: addCuttingTasksByOrderId: ', result)
        return result
    }

    // __ Пересчет СЗ Раскроя по ID Заявки
    const calcCuttingTasksCutByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtPost(URL_CUTTING_TASKS_CALC_BY_ORDER_ID, { id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: calcCuttingTasksCutByOrderId: ', result)
        return result
    }

    // __ Получение СЗ Раскроя по статусу или массиву статусов до определенной даты
    const getCuttingTasksByStatusBeforeDate = async (date: string, status: number[] | number | null = null) => {
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

        if (DEBUG) console.log('CuttingStore: getCuttingTasksByStatusBeforeDate: ', result)
        return result.data
    }


    // __ Получение СЗ Раскроя по статусу или массиву статусов в определенную дату
    const getCuttingTasksByStatusOnDate = async (date: string, status: number[] | number | null = null) => {
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

        if (DEBUG) console.log('CuttingStore: getCuttingTasksByStatusOnDate: ', result)
        return result.data
    }

    // __ Проверка на наличие СЗ Раскроя по статусу или массиву статусов в определенную дату
    const checkCuttingTasksByStatusOnDate = async (date: string, status: number[] | number | null = null) => {
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

        if (DEBUG) console.log('CuttingStore: checkCuttingTasksByStatusOnDate: ', result)
        return result.data
    }

    // __ Сохранение Комментария к Сменному заданию - СЗ
    const setCuttingTaskComment = async (id: number, comment: string | null = null) => {
        const response = await jwtPost(URL_CUTTING_TASKS_COMMENT, { id, comment })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: setCuttingTaskComment: ', result)
        return result.data
    }

    // __ Установка даты выполнения СЗ
    const setCuttingTaskActionAt = async (id: number, date: string) => {
        const response = await jwtPost(URL_CUTTING_TASKS_ACTION_AT_SET, { id, date })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: setCuttingTaskActionAt: ', result)
        return result.data
    }

    // --- ----------------------------------------------------------
    // --- ------ Сменные задания к выполнению (Pending) ------------
    // --- ----------------------------------------------------------

    // __ Разделение линий СЗ при выполнении СЗ
    const divideLineInCuttingTaskPending = async (cuttingTask: ICuttingTask, period: IPeriod | null = null) => {

        const findTask = globalCuttingTasks.value.find((task: ICuttingTask) => task.id === cuttingTask.id)
        if (!findTask) {
            return
        }
        console.log('findTask: ', findTask)

        repositionCuttingTaskLines(findTask)
        // const result = await saveChanges()
        return await saveChanges(globalCuttingTasks.value, globalCuttingTasksCopy, period)
        // return saveChanges(globalCuttingTasksPending.value, globalCuttingTasksPendingCopy)
    }

    // __ Получение СЗ Раскроя по статусу или массиву статусов
    const getCuttingTasksByStatus = async (status: number[] | number | null = null) => {
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

        globalCuttingTasksPending.value = result.data                                   // __ кэшируем
        globalCuttingTasksPendingCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений


        if (DEBUG) console.log('CuttingStore: getCuttingTasksByStatus: ', result)
        return result.data
    }

    // __ Получение СЗ Раскроя за период
    const getCuttingTasksByStatusAndPeriod = async (period: IPeriod | null = null, statuses: number[] | number | null = null) => {
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

        globalCuttingTasksPending.value = result.data                                   // __ кэшируем
        globalCuttingTasksPendingCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений

        if (DEBUG) console.log('CuttingStore: getCuttingTasksByStatusAndPeriod: ', result)
        return result.data
    }


    // __ Проверка на наличие СЗ с определенным статусом на определенную дату
    // const checkCuttingTasksByStatusAndDate = async (date: string, status: number[] | number | null = null) => {
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
    //     globalCuttingTasksPending.value = result.data                                   // __ кэшируем
    //     globalCuttingTasksPendingCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений
    //
    //
    //     if (DEBUG) console.log('CuttingStore: getCuttingTasksByStatus: ', result)
    //     return result.data
    // }


    // __ Один статус
    const getCuttingTasksByStatusOneStatus = async (status: number | null = null) => {
        let response
        if (status) {
            response = await jwtGet(`${URL_CUTTING_TASKS_STATUS}/${status}`)
            // response = await jwtGet(URL_CUTTING_TASKS_STATUS, { period })
        } else {
            response = await jwtGet(URL_CUTTING_TASKS_STATUS)
        }
        const result = await response

        globalCuttingTasksPending.value = result.data                                   // __ кэшируем
        // globalCuttingTasksCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений

        if (DEBUG) console.log('CuttingStore: getCuttingTasksByStatusOneStatus: ', result)
        return result.data
    }

    // __ Устанавливаем "Выполнено" на CuttingTaskLines
    const setCuttingTaskLinesDone = async (cuttingTaskLinesIds: number[]) => {
        if (!cuttingTaskLinesIds.length) {
            return []
        }
        const result = await jwtPost(URL_CUTTING_TASK_LINE_DONE, { ids: cuttingTaskLinesIds })
        if (DEBUG) console.log('CuttingStore: setCuttingTaskLinesDone: ', result)
        return result.data
    }


    // __ Устанавливаем "Не Выполнено" на CuttingTaskLines
    const setCuttingTaskLinesFalse = async (cuttingTaskLinesIds: number[], falseReason: string | null = null) => {
        if (!cuttingTaskLinesIds.length && !falseReason) {
            return []
        }
        const result = await jwtPost(URL_CUTTING_TASK_LINE_FALSE, { ids: cuttingTaskLinesIds, reason: falseReason })
        if (DEBUG) console.log('CuttingStore: setCuttingTaskLinesFalse: ', result)
        return result.data
    }

    // __ Сбрасываем статусы на CuttingTaskLines
    const setCuttingTaskLinesReset = async (cuttingTaskLinesIds: number[]) => {
        if (!cuttingTaskLinesIds.length) {
            return []
        }
        const result = await jwtPost(URL_CUTTING_TASK_LINE_RESET, { ids: cuttingTaskLinesIds })
        if (DEBUG) console.log('CuttingStore: setCuttingTaskLinesReset: ', result)
        return result.data
    }

    // --- ----------------------------------------------------------
    // --- ------------------ Типовые операции ----------------------
    // --- ----------------------------------------------------------
    // __ Получение Типовых операций
    const getCuttingOperations = async () => {
        const response = await jwtGet(URL_CUTTING_OPERATIONS)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: getCuttingOperations: ', result)
        return result.data
    }

    // __ Получение Типовой операции
    const getCuttingOperation = async (id: string | number) => {
        const response = await jwtGet(URL_CUTTING_OPERATION + '/' + id)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: getCuttingOperation: ', result)
        return result.data
    }

    // __ Создаем Типовую операцию
    const createCuttingOperation = async (cuttingOperation: ICuttingOperation) => {
        const result = await jwtPost(URL_CUTTING_OPERATION, cuttingOperation)
        if (DEBUG) console.log('CuttingStore: createCuttingOperation: ', result)
        return result
    }

    // __ Обновляем Типовую операцию
    const updateCuttingOperation = async (cuttingOperation: ICuttingOperation) => {
        const result = await jwtPut_(URL_CUTTING_OPERATION, cuttingOperation)
        if (DEBUG) console.log('CuttingStore: updateCuttingOperation: ', result)
        return result
    }


    // __ Получение Схем Типовых операций
    const getCuttingOperationSchemas = async () => {
        const response = await jwtGet(URL_CUTTING_OPERATION_SCHEMAS)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: getCuttingOperationSchemas: ', result)
        return result.data
    }

    // __ Получение Схемы Типовой операции
    const getCuttingOperationSchema = async (id: string | number) => {
        const response = await jwtGet(URL_CUTTING_OPERATION_SCHEMAS + '/' + id)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: getCuttingOperationSchema: ', result)
        return result.data
    }

    // __ Создание Схемы Типовой операции
    const createCuttingOperationSchema = async (schema: ICuttingOperationSchema) => {
        const response = await jwtPost(URL_CUTTING_OPERATION_SCHEMAS_CREATE, schema)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: createCuttingOperationSchema: ', result)
        return result
    }

    // __ Обновление Схемы Типовой операции
    const updateCuttingOperationSchema = async (schema: ICuttingOperationSchema) => {
        const response = await jwtPut(URL_CUTTING_OPERATION_SCHEMAS_UPDATE, schema)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: updateCuttingOperationSchema: ', result)
        return result
    }

    // __ Удаление Типовой операции из схемы
    const deleteCuttingOperationFromSchema = async (deleteObject: ICuttingOperationUpdateObject) => {
        const response = await jwtDelete(URL_CUTTING_OPERATION_SCHEMAS_DELETE, deleteObject)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: deleteCuttingOperationFromSchema: ', result)
        return result.data
    }

    // __ Обновление Типовой операции в схеме
    const addCuttingOperationToSchema = async (addObject: ICuttingOperationUpdateObject) => {
        const response = await jwtPost(URL_CUTTING_OPERATION_SCHEMAS_ADD, addObject)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: addCuttingOperationToSchema: ', result)
        return result.data
    }

    // __ Проверка Схемы Типовых операций на суммарное время
    const checkCuttingOperationSchemaForSummaryTime = async (schemaId: number | null = null) => {
        if (!schemaId) {
            return null
        }
        const response = await jwtGet(`${URL_CUTTING_OPERATION_SCHEMAS_CHECK}/${schemaId}`)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: checkCuttingOperationSchemaForSummaryTime: ', result)
        return result.data
    }

    // --- ----------------------------------------------------------
    // --- --------- Типовые операции + Модели ----------------------
    // --- ----------------------------------------------------------
    // __ Получение Моделей для Типовых операций
    const getModelsForLabor = async () => {
        const response = await jwtGet(URL_CUTTING_OPERATION_MODELS)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: getModelsForLabor: ', result)
        return result.data
    }

    // __ Обновление Схемы Типовых операций для модели
    const updateModelCuttingOperationSchema = async (code_1c: string, schema_id: number) => {
        const response = await jwtPatch(URL_CUTTING_OPERATION_SCHEMAS_MODEL, { code_1c, schema_id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: updateModelCuttingOperationSchema: ', result)
        return result.data
    }

    // __ Удаление Типовой опрерации из схемы
    const deleteCuttingOperationFromModel = async (deleteObject: ICuttingOperationUpdateObject) => {
        const response = await jwtPost(URL_CUTTING_OPERATION_MODELS_DELETE, deleteObject)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: deleteCuttingOperationFromModel: ', result)
        return result.data
    }

    // __ Обновление Типовой операции в схеме
    const addCuttingOperationToModel = async (addObject: ICuttingOperationUpdateObject) => {
        const response = await jwtPost(URL_CUTTING_OPERATION_MODELS_ADD, addObject)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: addCuttingOperationToModel: ', result)
        return result.data
    }


    // --- ----------------------------------------------------------
    // --- ------------------- Статусы СЗ ---------------------------
    // --- ----------------------------------------------------------

    // __ Получение Статусов Движения СЗ
    const getCuttingTaskStatuses = async () => {
        const response = await jwtGet(URL_CUTTING_TASK_STATUSES)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: getCuttingTaskStatuses: ', result)
        globalCuttingTaskStatuses.value = result.data    // __ кэшируем
        return result.data
    }

    // __ Устанавливаем цвет ярлычка Типов заказов (серийная, гаррмем, прогнозная и т.д.)
    const patchCuttingTaskStatusColor = async (cuttingTaskStatusId: number, color: string) => {
        const result = await jwtPatch(URL_CUTTING_TASK_STATUSES_COLOR_PATCH, { id: cuttingTaskStatusId, color })
        if (DEBUG) console.log('CuttingStore: patchCuttingTaskStatusColor', result)
        await getCuttingTaskStatuses()   // __ Обновляем статусы, чтобы был актуальный цвет
        return result.data
    }

    // __ Устанавливаем статусы для СЗ.
    // __ data: [{ task: number, status: number }]
    const setCuttingTasksStatuses = async (data: ICuttingTaskStatusesSet[]) => {
        const response = await jwtPost(URL_CUTTING_TASK_STATUSES_SET, data)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: setStatuses: ', result)
        return result.data
    }


    // --- ----------------------------------------------------------
    // --- ------------------- Производственный день ----------------
    // --- ----------------------------------------------------------

    // __ Получение производственного дня по дате и смене
    const getCuttingDayByDateAndChange = async (date: string, change: number = 1) => {
        const response = await jwtGet(`${URL_CUTTING_DAY}/${date}/${change}`)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: getCuttingDayByDateAndChange: ', result)
        return result.data
    }

    // __ Сохранение Комментария к производственному дню
    const setCuttingDayComment = async (id: number, comment: string | null = null) => {
        const response = await jwtPost(URL_CUTTING_DAY_COMMENT, { id, comment })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: setCuttingDayComment: ', result)
        return result.data
    }

    // __ Получение производственных дней по массиву дат
    // __ Тут по хорошему надо прикрутить еще и смену, но оставим на потом
    const getCuttingDaysByDates = async (dates: string[]) => {
        if (!dates.length) {
            return []
        }

        const response = await jwtGet(URL_CUTTING_DAY_DATES, { dates })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: getCuttingDaysByDates: ', result)
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
            .filter((w: ICuttingDayWorker) => w.id !== 0)
            .sort((a: ICuttingDayWorker, b: ICuttingDayWorker) => a.surname.localeCompare(b.surname))


        if (DEBUG) console.log('CuttingStore: getActiveWorkers: ', result)
        return result.data
    }

    // __ Добавление Рабочего в Производственный день
    const addWorkerToCuttingDay = async (day_id: number | null = null, worker_id: number | null = null) => {
        if (!day_id || !worker_id) {
            return
        }
        const response = await jwtPost(URL_CUTTING_DAY_WORKER_ADD, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: addWorkerToCuttingDay: ', result)
        return result.data
    }

    // __ Добавление Группы Рабочих в Производственный день
    const addWorkersToCuttingDay = async (day_id: number | null = null, worker_ids: number[] | null = null) => {
        if (!day_id || !worker_ids) {
            return
        }
        const response = await jwtPost(URL_CUTTING_DAY_WORKER_GROUP_ADD, { day_id, worker_ids })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: addWorkersGroupToCuttingDay: ', result)
        return result.data
    }

    // __ Удаление Рабочего из Производственного дня
    const removeWorkerFromCuttingDay = async (day_id: number, worker_id: number) => {
        const response = await jwtPost(URL_CUTTING_DAY_WORKER_REMOVE, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: removeWorkerToCuttingDay: ', result)
        return result.data
    }

    // __ Добавление Ответственного в Производственный день
    const addResponsibleToCuttingDay = async (day_id: number, worker_id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_RESPONSIBLE_ADD, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: addResponsibleToCuttingDay: ', result)
        return result.data
    }

    // __ Удаление Ответственного из Производственного дня
    const removeResponsibleFromCuttingDay = async (day_id: number, worker_id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_RESPONSIBLE_REMOVE, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: removeResponsibleFromCuttingDay: ', result)
        return result.data
    }

    // __ Старт СЗ
    const startCuttingDay = async (id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_START, { id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: startCuttingDay: ', result)
        return result.data
    }

    // __ Стоп СЗ
    const finishCuttingDay = async (id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_FINISH, { id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: finishCuttingDay: ', result)
        return result.data
    }

    // __ Получение маячка готовности дня с СЗ к добавлению новых СЗ
    const readyGetCuttingDay = async (date: string, change: number = 1) => {
        const response = await jwtGet(`${URL_CUTTING_DAY_READY_GET}/${date}/${change}`)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: readyGetCuttingDay: ', result)
        return result.data
    }

    // __ Установки маяка готовности к добавлению новых СЗ
    const readySetCuttingDay = async (id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_READY_SET, { id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: readySetCuttingDay: ', result)
        return result.data
    }

    // __ Снятие маяка готовности к добавлению новых СЗ
    const readyUnsetCuttingDay = async (id: number) => {
        const response = await jwtPatch_(URL_CUTTING_DAY_READY_UNSET, { id })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: readyUnsetCuttingDay: ', result)
        return result.data
    }

    // __ Устанавливаем новые Раскройные столы
    const taskLinesTableSet = async (data: ICuttingLineTableSetData[]) => {
        const response = await jwtPost(URL_CUTTING_TASK_LINES_TABLE_SET, { data })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: taskLinesTableSet: ', result)
        return result.data
    }

    // --- ----------------------------------------------------------
    // --- ------------------ Процедуры Раскроя ---------------------
    // --- ----------------------------------------------------------

    // __ Получаем процедуры расчета Раскроя
    const getCuttingProcedures = async () => {
        const response = await jwtGet(URL_CUTTING_PROCEDURES)
        const result   = await response

        if (DEBUG) console.log('CuttingStore: getCuttingProcedures: ', result)
        return result.data
    }

    // __ Получаем процедуру расчета Раскроя по ID
    const getCuttingProcedure = async (id: number | null = null) => {
        if (id === null) {
            return
        }
        const response = await jwtGet(`${URL_CUTTING_PROCEDURES}/${id}`)
        const result   = await response

        if (DEBUG) console.log('CuttingStore: getCuttingProcedure: ', result)
        return result.data
    }

    // __ Создаем Процедуру Раскроя
    const createCuttingProcedure = async (cuttingProcedure: ICuttingProcedure) => {
        const result = await jwtPost(URL_CUTTING_PROCEDURES, cuttingProcedure)
        if (DEBUG) console.log('CuttingStore: createCuttingProcedure: ', result)
        return result
    }

    // __ Обновляем Процедуру Раскроя
    const updateCuttingProcedure = async (cuttingProcedure: ICuttingProcedure) => {
        const result = await jwtPut_(URL_CUTTING_PROCEDURES, cuttingProcedure)
        if (DEBUG) console.log('CuttingStore: updateCuttingProcedure: ', result)
        return result
    }


    // __ Обновление Процедуры раскроя для модели
    const updateModelCuttingProcedure = async (code_1c: string, procedure_id: number, detail_type: ICuttingDetailType) => {
        const response = await jwtPatch(URL_CUTTING_PROCEDURES_MODEL, { code_1c, procedure_id, detail_type })
        const result   = await response
        if (DEBUG) console.log('CuttingStore: updateModelCuttingProcedure: ', result)
        return result.data
    }

    // --- ----------------------------------------------------------

    // __ Устанавливаем Угол для Раскроя
    const setCuttingAngle = async (code_1c: string | null = null, angle: string | null) => {
        if (!code_1c || !angle) {
            return
        }
        const result = await jwtPatch(URL_CUTTING_ANGLE_MODEL, {code_1c, angle})
        if (DEBUG) console.log('CuttingStore: setCuttingAngle: ', result)
        return result
    }


    // __ Тут следим за состоянием глобальных данных с сервера и обновляем локальные данные
    // watch(() => globalCuttingTasks.value, () => {
    //
    //     console.log('save task')
    //
    //
    // }, { immediate: true, deep: true })


    const test = async () => {
        const response = await jwtGet(URL_CUTTING_TEST)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: test: ', result)
        return result
    }


    return {
        globalCuttingTasks,
        globalCuttingTaskStatuses,
        globalRenderPeriod,

        globalCuttingTaskTimesShow,
        globalCuttingTaskFullDaysShow,
        globalCuttingTaskOrderTypeColor,
        globalManageTaskCardActiveCuttingLine,
        globalCuttingTaskActiveOrderId,

        globalCuttingTasksPending,

        globalWorkers,

        globalCuttingTaskPrintData,

        getCuttingTasks,
        getCuttingTasksByOrderId,
        getCuttingTasksByStatus,
        getCuttingTasksByStatusAndPeriod,
        getCuttingTasksByStatusBeforeDate,
        getCuttingTasksByStatusOnDate,
        checkCuttingTasksByStatusOnDate,
        deleteCuttingTasksByOrderId,
        addCuttingTasksByOrderId,
        calcCuttingTasksCutByOrderId,
        setCuttingTaskComment,
        setCuttingTaskActionAt,
        getCuttingTaskStatuses,
        patchCuttingTaskStatusColor,
        setCuttingTaskLinesDone,
        setCuttingTaskLinesFalse,
        setCuttingTaskLinesReset,

        getCuttingOperations,
        getCuttingOperation,
        createCuttingOperation,
        updateCuttingOperation,

        getCuttingOperationSchemas,
        getCuttingOperationSchema,
        createCuttingOperationSchema,
        updateCuttingOperationSchema,
        deleteCuttingOperationFromSchema,
        addCuttingOperationToSchema,
        checkCuttingOperationSchemaForSummaryTime,

        getModelsForLabor,
        updateModelCuttingOperationSchema,
        deleteCuttingOperationFromModel,
        addCuttingOperationToModel,

        setCuttingTasksStatuses,

        getCuttingDayByDateAndChange,
        setCuttingDayComment,
        getCuttingDaysByDates,
        getActiveWorkers,
        addWorkerToCuttingDay, addWorkersToCuttingDay,
        removeWorkerFromCuttingDay,
        addResponsibleToCuttingDay,
        removeResponsibleFromCuttingDay,
        startCuttingDay, finishCuttingDay, readyGetCuttingDay, readySetCuttingDay, readyUnsetCuttingDay,
        divideLineInCuttingTaskPending,

        addCuttingTaskToGlobal,
        saveChanges,
        applyChanges,
        applyMergeTasks,
        applyMergeTasksGroups,
        setCuttingTasksLines,
        applyCuttingTaskComment,

        taskLinesTableSet,
        setGlobalArrayChangeTables,

        getCuttingProcedures,
        getCuttingProcedure,
        updateCuttingProcedure,
        createCuttingProcedure,
        updateModelCuttingProcedure,

        setCuttingAngle,

        test,
    }
})
