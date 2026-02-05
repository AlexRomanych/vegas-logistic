// Хранилище для ПЯ Швейки

import { defineStore } from 'pinia'
import { ref } from 'vue'

import { jwtGet, jwtPost, /*jwtDelete,*/ jwtPatch, jwtPut_, jwtPut } from '@/app/utils/jwt_api'
import type {
    IPeriod, IRenderMatrixDiff, ISewingOperation, ISewingOperationSchema, ISewingOperationUpdateObject, ISewingTask,
    ISewingTaskLine
} from '@/types'

// import { usePlansStore } from '@/stores/PlansStore.ts'

import { DEBUG } from '@/app/constants/common.ts'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import { additionDaysInStrFormat } from '@/app/helpers/helpers_date'
import {
    getSewingTasksDiff, isAddItemsInDiffsPresents, mergeSewingTasks, repositionSewingTaskInDay,
    repositionSewingTaskLines
} from '@/app/helpers/manufacture/helpers_sewing.ts'


// Устанавливаем глобальные переменные
// const API_PREFIX                           = '/api/v1/' // Префикс API
const URL_SEWING_TASKS                     = '/sewing/tasks'                     // URL для получения Сменных заданий
const URL_SEWING_TASKS_UPDATE              = '/sewing/tasks/update'              // URL для обновления Сменных заданий
const URL_SEWING_TASK_STATUSES             = '/sewing/task/statuses'             // URL для получения Статуса Движения СЗ
const URL_SEWING_TASK_STATUSES_COLOR_PATCH = '/sewing/task/statuses/color/patch' // URL для получения Статуса Движения СЗ
const URL_SEWING_OPERATIONS                = '/sewing/operations'                // URL для получения Типовых операций швейки
const URL_SEWING_OPERATION                 = '/sewing/operations'                // URL для получения Типовой операции
const URL_SEWING_OPERATION_SCHEMAS         = '/sewing/operation/schemas'         // URL для получения Схем Типовых операций швейки
const URL_SEWING_OPERATION_SCHEMAS_DELETE  = '/sewing/operation/schemas/delete'  // URL для удаления Типовой операции из Схемы Типовых операций
const URL_SEWING_OPERATION_SCHEMAS_ADD     = '/sewing/operation/schemas/add'     // URL для добавления/изменения Типовой операции в Схеме Типовых операций
const URL_SEWING_OPERATION_SCHEMAS_CREATE  = '/sewing/operation/schemas/create'  // URL для создания новой Схемы Типовых операций
const URL_SEWING_OPERATION_SCHEMAS_UPDATE  = '/sewing/operation/schemas/update'  // URL для обновления Схемы Типовых операций
const URL_SEWING_OPERATION_SCHEMAS_MODEL   = '/sewing/operation/schemas/models'  // URL для обновления Схемы ТО для модели
const URL_SEWING_OPERATION_MODELS          = '/sewing/operation/models'          // URL для получения моделей для Типовых операций швейки
const URL_SEWING_OPERATION_MODELS_DELETE   = '/sewing/operation/models/delete'   // URL для удаления Типовой операции из Модели
const URL_SEWING_OPERATION_MODELS_ADD      = '/sewing/operation/models/add'      // URL для добавления ТО для моделей

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

        saveChanges()   // __ Сохраняем изменения
    }


    // __ Устанавливаем содерживое СЗ
    const setSewingTasksLines = (sewingTask: ISewingTask, sewingTaskLines: ISewingTaskLine[]) => {
        sewingTask.sewing_lines = sewingTaskLines
    }


    // __ Применение изменений
    const applyChanges = (diffs: IRenderMatrixDiff[] = []) => {

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

    // __ Объединение СЗ для одинаковых Заявок в одном календарном дне
    const applyMergeTasks = (sewingTasks: ISewingTask[]) => {

        // __ Объединяем СЗ
        let mergedTasks = mergeSewingTasks(sewingTasks)

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

        saveChanges()
    }


    // __ Сохранение изменений (Синхронизация с сервером)
    const saveChanges = async () => {
        const diffsInClobalSewingTasks = getSewingTasksDiff(globalSewingTasks.value, globalSewingTasksCopy)

        console.log(diffsInClobalSewingTasks)
        console.log('Сохраняем изменения')

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

        globalSewingTasks.value = result.data                                   // __ кэшируем
        globalSewingTasksCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений

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

    // __ Получение Типовых опрераций
    const getSewingOperations = async () => {
        let response = await jwtGet(URL_SEWING_OPERATIONS)
        const result = await response
        if (DEBUG) console.log('SewingStore: getSewingOperations: ', result)
        return result.data
    }

    // __ Получение Типовой опрерации
    const getSewingOperation = async (id: string | number) => {
        let response = await jwtGet(URL_SEWING_OPERATION + '/' + id)
        const result = await response
        if (DEBUG) console.log('SewingStore: getSewingOperation: ', result)
        return result.data
    }

    // __ Создаем Типовую операцию
    const createSewingOperation = async (sewingoperation: ISewingOperation) => {
        const result = await jwtPost(URL_SEWING_OPERATION, sewingoperation)
        if (DEBUG) console.log('SewingStore: createSewingOperation: ', result)
        return result
    }

    // __ Обновляем Типовую операцию
    const updateSewingOperation = async (sewingoperation: ISewingOperation) => {
        const result = await jwtPut_(URL_SEWING_OPERATION, sewingoperation)
        if (DEBUG) console.log('SewingStore: updateSewingOperation: ', result)
        return result
    }


    // __ Получение Схем Типовых опрераций
    const getSewingOperationSchemas = async () => {
        const response = await jwtGet(URL_SEWING_OPERATION_SCHEMAS)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getSewingOperationSchemas: ', result)
        return result.data
    }

    // __ Получение Схемы Типовой опрерации
    const getSewingOperationSchema = async (id: string | number) => {
        const response = await jwtGet(URL_SEWING_OPERATION_SCHEMAS + '/' + id)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getSewingOperationSchema: ', result)
        return result.data
    }

    // __ Создание Схемы Типовой опрерации
    const createSewingOperationSchema = async (schema: ISewingOperationSchema) => {
        const response = await jwtPost(URL_SEWING_OPERATION_SCHEMAS_CREATE, schema)
        const result   = await response
        if (DEBUG) console.log('SewingStore: createSewingOperationSchema: ', result)
        return result
    }

    // __ Обновление Схемы Типовой опрерации
    const updateSewingOperationSchema = async (schema: ISewingOperationSchema) => {
        const response = await jwtPut(URL_SEWING_OPERATION_SCHEMAS_UPDATE, schema)
        const result   = await response
        if (DEBUG) console.log('SewingStore: updateSewingOperationSchema: ', result)
        return result
    }

    // __ Удаление Типовой опрерации из схемы
    const deleteSewingOperationFromSchema = async (deleteObject: ISewingOperationUpdateObject) => {
        const response = await jwtPost(URL_SEWING_OPERATION_SCHEMAS_DELETE, deleteObject)
        const result   = await response
        if (DEBUG) console.log('SewingStore: deleteSewingOperationFromSchema: ', result)
        return result.data
    }

    // __ Обновление Типовой опрерации в схеме
    const addSewingOperationToSchema = async (addObject: ISewingOperationUpdateObject) => {
        const response = await jwtPost(URL_SEWING_OPERATION_SCHEMAS_ADD, addObject)
        const result   = await response
        if (DEBUG) console.log('SewingStore: addSewingOperationToSchema: ', result)
        return result.data
    }

    // --- ----------------------------------------------------------
    // --- --------- Типовые операции + Модели ----------------------
    // --- ----------------------------------------------------------
    // __ Получение Моделей для Типовых операций
    const getModelsForLabor = async () => {
        const response = await jwtGet(URL_SEWING_OPERATION_MODELS)
        const result   = await response
        if (DEBUG) console.log('SewingStore: getModelsForLabor: ', result)
        return result.data
    }

    // __ Обновление Схемы Типовых операций для модели
        const updateModelSewingOperationSchema = async (code_1c: string, schema_id: number) => {
        const response = await jwtPatch(URL_SEWING_OPERATION_SCHEMAS_MODEL, { code_1c, schema_id })
        const result   = await response
        if (DEBUG) console.log('SewingStore: updateModelSewingOperationSchema: ', result)
        return result.data
    }

    // __ Удаление Типовой опрерации из схемы
    const deleteSewingOperationFromModel = async (deleteObject: ISewingOperationUpdateObject) => {
        const response = await jwtPost(URL_SEWING_OPERATION_MODELS_DELETE, deleteObject)
        const result   = await response
        if (DEBUG) console.log('SewingStore: deleteSewingOperationFromModel: ', result)
        return result.data
    }

    // __ Обновление Типовой опрерации в схеме
    const addSewingOperationToModel = async (addObject: ISewingOperationUpdateObject) => {
        const response = await jwtPost(URL_SEWING_OPERATION_MODELS_ADD, addObject)
        const result   = await response
        if (DEBUG) console.log('SewingStore: addSewingOperationToModel: ', result)
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

        globalSewingTaskTimesShow,
        globalSewingTaskFullDaysShow,
        globalSewingTaskOrderTypeColor,
        globalManageTaskCardActiveSewingLine,
        globalSewingTaskActiveOrderId,

        getSewingTasks,
        getSewingTaskStatuses,
        patchSewingTaskStatusColor,

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

        getModelsForLabor,
        updateModelSewingOperationSchema,
        deleteSewingOperationFromModel,
        addSewingOperationToModel,

        addSewingTaskToGlobal,
        applyChanges,
        applyMergeTasks,
        setSewingTasksLines,
    }
})
