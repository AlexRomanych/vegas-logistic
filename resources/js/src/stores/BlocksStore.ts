// Хранилище для ПЯ Швейки

import { defineStore } from 'pinia'

import { jwtGet, jwtPost, /*jwtDelete,*/ jwtPatch, jwtPut_, jwtPut, jwtPatch_, jwtDelete } from '@/app/utils/jwt_api'
import type {
    IBlock,
    IBlockCollection,
    IBlockDayWorker, IBlockLineSetData, IBlockTaskChangeKeys, IBlockTaskLine,
    IBlockTaskStatusEntity,
    IBlockTaskStatusesSet,
    IBlockTask,
    IPeriod, IRenderMatrixDiff,
} from '@/types'
import { ref } from 'vue'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import { isNumber } from '@/app/helpers/helpers_lib.ts'

import { additionDaysInStrFormat } from '@/app/helpers/helpers_date'
import {
    getBlockTasksDiff,
    mergeBlockTasks,
    repositionBlockTaskInDay,
    repositionBlockTaskLines,
    isAddItemsInDiffsPresents
} from '@/app/helpers/manufacture/helpers_blocks.ts'
import { CHANGES } from '@/app/constants/blocks.ts'


const DEBUG = true

// Устанавливаем глобальные переменные
// const API_PREFIX                           = '/api/v1/' // Префикс API
const URL_BLOCKS_COLLECTIONS = '/blocks/collections'                 // URL для получения Коллекций Блоков
const URL_BLOCKS             = '/blocks'                             // URL для получения Блоков
const URL_BLOCKS_TEST        = '/blocks/test'                        // URL для тестирования

const URL_BLOCK_TASKS_STATUS               = '/blocks/tasks/status'                // URL для получения Сменных заданий по статусу
const URL_BLOCK_TASKS_STATUS_PERIOD        = '/blocks/tasks/status/period'         // URL для получения Сменных заданий по статусу в периоде
const URL_BLOCK_TASKS_STATUS_BEFORE_DATE   = '/blocks/tasks/status/date/before'    // URL для получения Сменных заданий по статусу
const URL_BLOCK_TASKS_STATUS_ON_DATE       = '/blocks/tasks/status/date/on'        // URL для получения Сменных заданий по статусу в определенный день
const URL_BLOCK_TASKS_STATUS_ON_DATE_CHECK = '/blocks/tasks/status/date/on/check'  // URL для проверки наличия Сменных заданий по статусу в определенный день


const URL_BLOCKS_TASK_STATUSES             = '/blocks/task/statuses'               // URL для получения Статуса Движения СЗ
const URL_BLOCKS_TASK_STATUSES_SET         = '/blocks/task/statuses/set'           // URL для изменения/добавления Статуса Движения СЗ
const URL_BLOCKS_TASK_STATUSES_COLOR_PATCH = '/blocks/task/statuses/color/patch'   // URL для получения Статуса Движения СЗ

const URL_BLOCKS_TASKS                      = '/blocks/tasks'                       // URL для получения Сменных заданий
const URL_BLOCKS_TASKS_UPDATE               = '/blocks/tasks/update'                // URL для обновления Сменных заданий
const URL_BLOCKS_TASKS_ORDER_ID             = '/blocks/tasks/order'                 // URL для получения Сменных заданий по id Заявки
const URL_BLOCKS_TASKS_DELETE_BY_ORDER_ID   = '/blocks/tasks/delete/order'          // URL для удаления Сменных заданий по id Заявки
const URL_BLOCKS_TASKS_ADD_BY_ORDER_ID      = '/blocks/tasks/add/order'             // URL для добавления Сменных заданий по id Заявки
const URL_BLOCKS_TASKS_CALC_BY_ORDER_ID     = '/blocks/tasks/calc/order'            // URL для пересчета Кроя по id Заявки
const URL_BLOCKS_TASKS_STATUS_BEFORE_DATE   = '/blocks/tasks/status/date/before'    // URL для получения Сменных заданий по статусу
const URL_BLOCKS_TASKS_STATUS_ON_DATE       = '/blocks/tasks/status/date/on'        // URL для получения Сменных заданий по статусу в определенный день
const URL_BLOCKS_TASKS_STATUS_ON_DATE_CHECK = '/blocks/tasks/status/date/on/check'  // URL для проверки наличия Сменных заданий по статусу в определенный день
const URL_BLOCKS_TASKS_COMMENT              = '/blocks/tasks/comment'               // URL для изменения комментария к Сменному заданию
const URL_BLOCKS_TASKS_CHANGE               = '/blocks/tasks/change'                // URL для изменения смены Сменного задания
const URL_BLOCKS_TASKS_ACTION_AT_SET        = '/blocks/tasks/action/set'            // URL для установки даты выполнения (action_at) СЗ

const URL_BLOCKS_TASK_LINES_TABLE_SET = '/blocks/tasks/lines/line/set'        // URL для изменения раскройного стола для записи СЗ
const URL_BLOCKS_TASK_LINE_DONE       = '/blocks/tasks/line/done'             // URL для установки статуса "Выполнено" для записи СЗ
const URL_BLOCKS_TASK_LINE_FALSE      = '/blocks/tasks/line/false'            // URL для установки статуса "Не Выполнено" для записи СЗ
const URL_BLOCKS_TASK_LINE_RESET      = '/blocks/tasks/line/reset'            // URL для сброса статуса для записи СЗ


const URL_BLOCK_DAY                    = '/blocks/day'                         // URL для получения рабочего дня
const URL_BLOCK_DAY_DATES              = '/blocks/day/dates'                   // URL для получения рабочих дней по статусу
const URL_BLOCK_DAY_COMMENT            = '/blocks/day/comment'                 // URL для сохранения комментария к дню
const URL_BLOCK_DAY_WORKERS_ACTIVE     = '/workers/active'                      // URL для получения активных рабочих
const URL_BLOCK_DAY_WORKER_ADD         = '/blocks/day/worker/add'              // URL для добавления исполнителя к дню
const URL_BLOCK_DAY_WORKER_GROUP_ADD   = '/blocks/day/workers/add'             // URL для добавления группы исполнителей к дню
const URL_BLOCK_DAY_WORKER_REMOVE      = '/blocks/day/worker/remove'           // URL для удаления исполнителя к дню
const URL_BLOCK_DAY_RESPONSIBLE_ADD    = '/blocks/day/responsible/add'         // URL для добавления ответственного к дню
const URL_BLOCK_DAY_RESPONSIBLE_REMOVE = '/blocks/day/responsible/remove'      // URL для удаления ответственного к дню
const URL_BLOCK_DAY_START              = '/blocks/day/start'                   // URL для старта дня СЗ
const URL_BLOCK_DAY_FINISH             = '/blocks/day/finish'                  // URL для финиш дня СЗ
const URL_BLOCK_DAY_READY_GET          = '/blocks/day/ready/get'               // URL для получения маячка готовности дня с СЗ к добавлению новых СЗ
const URL_BLOCK_DAY_READY_SET          = '/blocks/day/ready/set'               // URL для установки маяка готовности к добавлению новых СЗ
const URL_BLOCK_DAY_READY_UNSET        = '/blocks/day/ready/unset'             // URL для снятия маяка готовности к добавлению новых СЗ


export const useBlocksStore = defineStore('blocks', () => {

    // __ Массив СЗ Раскроя
    const globalBlockTasks = ref<IBlockTask[]>([])

    // __ Копия массива СЗ Раскроя для отслеживания изменений
    let globalBlockTasksCopy: IBlockTask[] = []

    // __ Массив СЗ, готовых к выполнению
    const globalBlockTasksPending = ref<IBlockTask[]>([])

    // __ Копия массива СЗ Раскроя для отслеживания изменений
    let globalBlockTasksPendingCopy: IBlockTask[] = []

    // __ Показывать ли Трудозатраты в календаре СЗ Раскроя
    const globalBlockTaskTimesShow = ref(true)

    // __ Показывать ли Раскрытый день или нет в календаре СЗ Раскроя
    const globalBlockTaskFullDaysShow = ref(true)

    // __ Текущее Заявка, на которое ссылается кликнутое СЗ (для календаря для подсветки СЗ, которые ссылаются на одну заявку)
    const globalBlockTaskActiveOrderId = ref<number | null>(null)

    // __ Текущая Запись (BlockLine) в карточке СЗ в календаре СЗ Раскроя
    const globalManageTaskCardActiveBlockLine = ref<IBlockTaskLine | null>(null)

    // __ Раскрашивать заявки в календаре в цвет Типа Заявки или в цвет Статусов Движения Заявок
    const globalBlockTaskOrderTypeColor = ref(false)

    // __ Период рендеринга календаря
    const globalRenderPeriod = ref<IPeriod>(PERIOD_DRAFT)

    // __ Статусы Движения СЗ
    const globalBlockTaskStatuses = ref<IBlockTaskStatusEntity[]>([])

    // __ Массив Рабочих
    const globalWorkers = ref<IBlockDayWorker[]>([])


    // --- ------------------------------------------------------------------------------------------
    // --- ---------------- Тут вся логика по управлению и сохранению частей СЗ ---------------------
    // --- ------------------------------------------------------------------------------------------
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! --- Тут вся логика по управлению и сохранению частей СЗ !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    /**
     * __ Добавление новой части СЗ и изменение старой части СЗ, на основе которого была создана новая часть
     * @param addBlockTask     - __ СЗ, которое уже было сформировано на основе старой части СЗ (правая панель)__
     * @param leftPanel        - __ контент в новом СЗ (правая панель)__
     * @param oldBlockTask     - __ СЗ, на основе которого формируется новая часть СЗ (левая панель)__
     * @param rightPanel       - __ контент в старом СЗ (левая панель)__
     */
    const addBlockTaskToGlobal = async (
        oldBlockTask: IBlockTask,
        leftPanel: IBlockTaskLine[],
        addBlockTask: IBlockTask | null     = null,
        rightPanel: IBlockTaskLine[] | null = null,
    ) => {

        // console.log('leftPanel: ', leftPanel)
        // debugger

        leftPanel                = repositionBlockTaskLines(leftPanel)   // __ Пересчитываем позиции для строк СЗ (BlockLines[])
        oldBlockTask.block_lines = leftPanel              // __ oldBlockTask приходит по ссылке

        // __ Если есть правая панель, то добавляем ее в массив СЗ
        if (addBlockTask && rightPanel) {

            // console.log('passed')

            rightPanel               = repositionBlockTaskLines(rightPanel)  // __ Пересчитываем позиции для строк СЗ (BlockLines[])
            addBlockTask.block_lines = rightPanel             // __ addBlockTask приходит новым объектом

            // __ Добавляем новый объект в массив
            globalBlockTasks.value.push(addBlockTask)

            // __ Переопределяем порядок СЗ.
            // __ Находим все СЗ в глабальной переменной с датой созданного СЗ и меняем порядок
            globalBlockTasks.value = repositionBlockTaskInDay(globalBlockTasks.value, addBlockTask.action_at)

        }

        await saveChanges()   // __ Сохраняем изменения
    }

    // __ Устанавливаем содерживое СЗ
    const setBlockTasksLines = (blockTask: IBlockTask, blockTaskLines: IBlockTaskLine[]) => {
        blockTask.block_lines = blockTaskLines
    }

    // __ Устанавливаем комментарий в СЗ
    const applyBlockTaskComment = (blockTaskId: number, comment: string) => {
        const blockTask = globalBlockTasks.value.find((task: IBlockTask) => task.id === blockTaskId)

        console.log('blockTask: ', blockTask)
        console.log('comment: ', comment)
        if (blockTask) {
            blockTask.comment = comment
        }
    }

    // __ Применение изменений
    const applyChanges = async (diffs: IRenderMatrixDiff[] = []) => {

        // __ Если нет изменений, то выход
        if (diffs.length === 0) {
            return
        }

        // __ Если нет статусов, то получаем их с сервера
        if (globalBlockTaskStatuses.value.length === 0) {
            await getBlockTaskStatuses()
        }

        diffs.forEach(diff => {

            // __ Если изменилась позиция или дата производства или статус, то меняем ее в глобальном массиве
            if (diff.isPositionChanged || diff.isMoved || diff.statusId || diff.isChangeChanged) {
                const findTask = globalBlockTasks.value.find((task: IBlockTask) => task.id === diff.taskId)
                if (findTask) {

                    if (diff.newTaskPosition) {
                        findTask.position = diff.newTaskPosition
                    }

                    if (diff.newChange) {
                        findTask.change = diff.newChange as IBlockTaskChangeKeys
                    }

                    if (diff.isMoved) {
                        findTask.action_at = additionDaysInStrFormat(globalRenderPeriod.value.start, diff.dayToOffset ?? 0)
                    }

                    if (diff.statusId) {
                        const findStatus = globalBlockTaskStatuses.value.find((status: IBlockTaskStatusEntity) => status.id === diff.statusId)
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
    const mergeTasks = (blockTasks: IBlockTask[]) => {

        // __ Если массив СЗ меньше 2, то выход
        if (blockTasks.length < 2) {
            return
        }

        // __ Объединяем СЗ
        const mergedTasks = mergeBlockTasks(blockTasks)

        // __ Пересчитываем позиции !!! Важно выполнение после объединения СЗ
        mergedTasks[0].block_lines = repositionBlockTaskLines(mergedTasks[0].block_lines)

        // __ Заменяем СЗ в глобальном массиве
        const findTask = globalBlockTasks.value.find((task: IBlockTask) => task.id === mergedTasks[0].id)
        if (findTask) {
            findTask.block_lines = mergedTasks[0].block_lines
        }

        // __ Удаляем лишние СЗ
        for (let i = 1; i < blockTasks.length; i++) {

            // __ Находим то, что нужно удалить
            const workTask = globalBlockTasks.value.find((task: IBlockTask) => task.id === blockTasks[i].id)
            if (workTask) {

                // __ Удаляем
                globalBlockTasks.value = globalBlockTasks.value.filter((task: IBlockTask) => {
                    return task.id !== blockTasks[i].id
                })

                // __ Переопределяем порядок СЗ в дне, из которого удалили
                globalBlockTasks.value = repositionBlockTaskInDay(globalBlockTasks.value, workTask.action_at)
            }
        }

        // __ Переопределяем порядок СЗ в дне, в котором добавили
        globalBlockTasks.value = repositionBlockTaskInDay(globalBlockTasks.value, mergedTasks[0].action_at)

    }

    // __ Применение объединения СЗ для массива СЗ
    const applyMergeTasks = async (blockTasks: IBlockTask[]) => {

        mergeTasks(blockTasks)
        return await saveChanges()
    }

    // __ Применение объединения СЗ для массива массивов СЗ  [[...], [...]]
    const applyMergeTasksGroups = async (blockTasksGroups: IBlockTask[][]) => {
        blockTasksGroups.forEach(blockTasks => mergeTasks(blockTasks))
        return await saveChanges()
    }


    // __ Сохранение изменений (Синхронизация с сервером)
    const saveChanges = async (globalArray = globalBlockTasks.value, globalArrayCopy = globalBlockTasksCopy, period: IPeriod | null = null) => {
        const diffsInGlobalBlockTasks = getBlockTasksDiff(globalArray, globalArrayCopy)

        // __ Если нет изменений, то выход
        if (diffsInGlobalBlockTasks.length === 0) {
            return
        }

        console.log(diffsInGlobalBlockTasks)
        console.log('Сохраняем изменения')

        const result = await jwtPost(URL_BLOCKS_TASKS_UPDATE, { diffs: diffsInGlobalBlockTasks })
        if (DEBUG) console.log('saveChanges: ', result)


        // __ Если есть добавление новых элементов в БД, то обновляем данные, чтобы получить id
        // __ Если это изменение позиции, то просто пишем в базу
        if (isAddItemsInDiffsPresents(diffsInGlobalBlockTasks)) {

            // __ Получаем СЗ с сервера и реактивное обновление
            await getBlockTasks(period)
            console.log('Server data updated')
        } else {

            globalBlockTasksCopy = JSON.parse(JSON.stringify(globalArray))     // __ копия для отслеживания изменений
            // globalArrayCopy = JSON.parse(JSON.stringify(globalArray))     // __ копия для отслеживания изменений
        }

        return result
    }


    // __ Меняем динамически Столы раскроя в глобальном массиве, чтобы не перезагружать данные с сервера
    const setGlobalArrayChangeManufLines = (data: IBlockLineSetData[]) => {
        for (const item of data) {
            let isFind = false
            for (const task of globalBlockTasks.value) {
                for (const line of task.block_lines) {
                    if (item.id === line.id) {
                        line.manuf_line = item.line
                        isFind          = true
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
        globalBlockTasksCopy = JSON.parse(JSON.stringify(globalBlockTasks.value))
    }


    // --- ------------------------------------------------------------------------------------------


    // --- ----------------------------------------------------------
    // --- ------------------- Сменные задания ----------------------
    // --- ----------------------------------------------------------

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---               Сменные задания               !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    // __ Получение СЗ Блоков с сервера за период
    const getBlockTasks = async (period: IPeriod | null = null) => {
        let response
        if (period) {
            response = await jwtGet(URL_BLOCKS_TASKS, { period })
        } else {
            response = await jwtGet(URL_BLOCKS_TASKS)
        }
        const result = await response

        globalBlockTasks.value = result.data                                   // __ кэшируем
        globalBlockTasksCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений

        if (DEBUG) console.log('BlocksStore: getBlockTasks: ', result)
        return result.data
    }


    // __ Получение СЗ Блоков по ID Заявки
    const getBlockTasksByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtGet(`${URL_BLOCKS_TASKS_ORDER_ID}/${id}`)
        const result   = await response
        if (DEBUG) console.log('BlocksStore: getBlockTasksByOrderId: ', result)
        return result.data
    }


    // __ Удаление СЗ Блоков по ID Заявки
    const deleteBlockTasksByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtDelete(URL_BLOCKS_TASKS_DELETE_BY_ORDER_ID, { id })
        const result   = await response
        if (DEBUG) console.log('BlocksStore: deleteBlockTasksByOrderId: ', result)
        return result
    }

    // __ Добавление СЗ Блоков по ID Заявки
    const addBlockTasksByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtPost(URL_BLOCKS_TASKS_ADD_BY_ORDER_ID, { id })
        const result   = await response
        if (DEBUG) console.log('BlocksStore: addBlockTasksByOrderId: ', result)
        return result
    }

    // __ Пересчет СЗ Блоков по ID Заявки
    const calcBlockTasksCutByOrderId = async (id: number | null = null) => {
        if (!id) {
            return
        }
        const response = await jwtPost(URL_BLOCKS_TASKS_CALC_BY_ORDER_ID, { id })
        const result   = await response
        if (DEBUG) console.log('BlocksStore: calcBlockTasksCutByOrderId: ', result)
        return result
    }

    // __ Получение СЗ Блоков по статусу или массиву статусов до определенной даты
    const getBlockTasksByStatusBeforeDate = async (date: string, status: number[] | number | null = null) => {
        let response
        if (status) {
            if (isNumber(status)) {
                status = [status]
            }

            response = await jwtGet(URL_BLOCKS_TASKS_STATUS_BEFORE_DATE, { date, statuses: status })
        } else {
            response = await jwtGet(date, URL_BLOCKS_TASKS_STATUS_BEFORE_DATE)
        }
        const result = await response

        if (DEBUG) console.log('BlocksStore: getBlockTasksByStatusBeforeDate: ', result)
        return result.data
    }


    // __ Получение СЗ Блоков по статусу или массиву статусов в определенную дату
    const getBlockTasksByStatusOnDate = async (date: string, status: number[] | number | null = null) => {
        let response
        if (status) {
            if (isNumber(status)) {
                status = [status]
            }

            response = await jwtGet(URL_BLOCKS_TASKS_STATUS_ON_DATE, { date, statuses: status })
        } else {
            response = await jwtGet(date, URL_BLOCKS_TASKS_STATUS_ON_DATE)
        }
        const result = await response

        if (DEBUG) console.log('BlocksStore: getBlockTasksByStatusOnDate: ', result)
        return result.data
    }

    // __ Проверка на наличие СЗ Блоков по статусу или массиву статусов в определенную дату
    const checkBlockTasksByStatusOnDate = async (date: string, status: number[] | number | null = null) => {
        let response
        if (status) {
            if (isNumber(status)) {
                status = [status]
            }

            response = await jwtGet(URL_BLOCKS_TASKS_STATUS_ON_DATE_CHECK, { date, statuses: status })
        } else {
            response = await jwtGet(date, URL_BLOCKS_TASKS_STATUS_ON_DATE_CHECK)
        }
        const result = await response

        if (DEBUG) console.log('BlocksStore: checkBlockTasksByStatusOnDate: ', result)
        return result.data
    }

    // __ Сохранение Комментария к Сменному заданию - СЗ
    const setBlockTaskComment = async (id: number, comment: string | null = null) => {
        const response = await jwtPost(URL_BLOCKS_TASKS_COMMENT, { id, comment })
        const result   = await response
        if (DEBUG) console.log('BlocksStore: setBlockTaskComment: ', result)
        return result.data
    }


    // __ Изменяем смену СЗ
    // __ Сохранение Комментария к Сменному заданию - СЗ
    const modifyChange = async (id: number, change: IBlockTaskChangeKeys) => {
        const response = await jwtPost(URL_BLOCKS_TASKS_CHANGE, { id, change })
        const result   = await response
        if (DEBUG) console.log('BlocksStore: modifyChange: ', result)
        return result.data
    }

    // __ Установка даты выполнения СЗ
    const setBlockTaskActionAt = async (id: number, date: string) => {
        const response = await jwtPost(URL_BLOCKS_TASKS_ACTION_AT_SET, { id, date })
        const result   = await response
        if (DEBUG) console.log('BlocksStore: setBlockTaskActionAt: ', result)
        return result.data
    }


    // __ Получение СЗ Раскроя по статусу или массиву статусов
    const getBlockTasksByStatus = async (status: number[] | number | null = null) => {
        let response
        if (status) {
            if (isNumber(status)) {
                status = [status]
            }

            response = await jwtGet(URL_BLOCK_TASKS_STATUS, { statuses: status })
            // response = await jwtGet(`${URL_BLOCK_TASKS_STATUS}/${status}`)
        } else {
            response = await jwtGet(URL_BLOCK_TASKS_STATUS)
        }
        const result = await response

        globalBlockTasksPending.value = result.data                                   // __ кэшируем
        globalBlockTasksPendingCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений


        if (DEBUG) console.log('BlockStore: getBlockTasksByStatus: ', result)
        return result.data
    }


    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---        Коллекции (Группы) Блоков            !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    // __ Получение Коллекции Блоков
    const getBlockCollections = async () => {
        const response = await jwtGet(URL_BLOCKS_COLLECTIONS)
        const result   = await response
        if (DEBUG) console.log('BlocksStore: getBlockCollections: ', result)
        return result.data
    }

    // __ Получение Коллекции блоков по id
    const getBlockCollectionById = async (id: number) => {
        const response = await jwtGet(URL_BLOCKS_COLLECTIONS + '/' + id)
        const result   = await response
        if (DEBUG) console.log('BlocksStore: getBlockCollectionById: ', result)
        return result.data
    }

    // __ Создаем Коллекции блоков
    const createBlockCollection = async (blockCollection: IBlockCollection) => {
        const result = await jwtPost(URL_BLOCKS_COLLECTIONS, blockCollection)
        if (DEBUG) console.log('BlocksStore: createBlockCollection: ', result)
        return result
    }

    // __ Обновляем Коллекции блоков
    const updateBlockCollection = async (blockCollection: IBlockCollection) => {
        const result = await jwtPut_(URL_BLOCKS_COLLECTIONS, blockCollection)
        if (DEBUG) console.log('BlocksStore: updateBlockCollection: ', result)
        return result
    }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---                 Блоки                       !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // __ Получение Блока по id
    const getBlockById = async (id: number) => {
        const response = await jwtGet(URL_BLOCKS + '/' + id)
        const result   = await response
        if (DEBUG) console.log('BlocksStore: getBlockById: ', result)
        return result.data
    }

    // __ Создаем Блок
    const createBlock = async (block: IBlock) => {
        const result = await jwtPost(URL_BLOCKS, block)
        if (DEBUG) console.log('BlocksStore: createBlock: ', result)
        return result
    }

    // __ Обновляем Блок
    const updateBlock = async (block: IBlock) => {
        const result = await jwtPut_(URL_BLOCKS, block)
        if (DEBUG) console.log('BlocksStore: updateBlock: ', result)
        return result
    }

    // --- ----------------------------------------------------------
    // --- ------------------- Статусы СЗ ---------------------------
    // --- ----------------------------------------------------------

    // __ Получение Статусов Движения СЗ
    const getBlockTaskStatuses = async () => {
        const response = await jwtGet(URL_BLOCKS_TASK_STATUSES)
        const result   = await response
        if (DEBUG) console.log('BlockStore: getBlockTaskStatuses: ', result)
        globalBlockTaskStatuses.value = result.data    // __ кэшируем
        return result.data
    }

    // __ Устанавливаем цвет ярлычка Типов заказов (серийная, гаррмем, прогнозная и т.д.)
    const patchBlockTaskStatusColor = async (blockTaskStatusId: number, color: string) => {
        const result = await jwtPatch(URL_BLOCKS_TASK_STATUSES_COLOR_PATCH, { id: blockTaskStatusId, color })
        if (DEBUG) console.log('BlockStore: patchBlockTaskStatusColor', result)
        await getBlockTaskStatuses()   // __ Обновляем статусы, чтобы был актуальный цвет
        return result.data
    }

    // __ Устанавливаем статусы для СЗ.
    // __ data: [{ task: number, status: number }]
    const setBlockTasksStatuses = async (data: IBlockTaskStatusesSet[]) => {
        const response = await jwtPost(URL_BLOCKS_TASK_STATUSES_SET, data)
        const result   = await response
        if (DEBUG) console.log('BlockStore: setStatuses: ', result)
        return result.data
    }


    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---          Производственный день              !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // __ Получение производственного дня по дате и смене
    const getBlockDayByDateAndChange = async (date: string, change: IBlockTaskChangeKeys = CHANGES.CHANGE_1.NAME) => {
        const response = await jwtGet(`${URL_BLOCK_DAY}/${date}/${change}`)
        const result   = await response
        if (DEBUG) console.log('BlockStore: getBlockDayByDateAndChange: ', result)
        return result.data
    }

    // __ Сохранение Комментария к производственному дню
    const setBlockDayComment = async (id: number, comment: string | null = null) => {
        const response = await jwtPost(URL_BLOCK_DAY_COMMENT, { id, comment })
        const result   = await response
        if (DEBUG) console.log('BlockStore: setBlockDayComment: ', result)
        return result.data
    }

    // __ Получение производственных дней по массиву дат
    // __ Тут по хорошему надо прикрутить еще и смену, но оставим на потом
    const getBlockDaysByDates = async (dates: string[]) => {
        if (!dates.length) {
            return []
        }

        const response = await jwtGet(URL_BLOCK_DAY_DATES, { dates })
        const result   = await response
        if (DEBUG) console.log('BlockStore: getBlockDaysByDates: ', result)
        return result.data
    }

    // __ Старт СЗ
    const startBlockDay = async (id: number) => {
        const response = await jwtPatch_(URL_BLOCK_DAY_START, { id })
        const result   = await response
        if (DEBUG) console.log('BlockStore: startBlockDay: ', result)
        return result.data
    }

    // __ Стоп СЗ
    const finishBlockDay = async (id: number) => {
        const response = await jwtPatch_(URL_BLOCK_DAY_FINISH, { id })
        const result   = await response
        if (DEBUG) console.log('BlockStore: finishBlockDay: ', result)
        return result.data
    }

    // __ Получение маячка готовности дня с СЗ к добавлению новых СЗ
    const readyGetBlockDay = async (date: string, change: number = 1) => {
        const response = await jwtGet(`${URL_BLOCK_DAY_READY_GET}/${date}/${change}`)
        const result   = await response
        if (DEBUG) console.log('BlockStore: readyGetBlockDay: ', result)
        return result.data
    }

    // __ Установки маяка готовности к добавлению новых СЗ
    const readySetBlockDay = async (id: number) => {
        const response = await jwtPatch_(URL_BLOCK_DAY_READY_SET, { id })
        const result   = await response
        if (DEBUG) console.log('BlockStore: readySetBlockDay: ', result)
        return result.data
    }

    // __ Снятие маяка готовности к добавлению новых СЗ
    const readyUnsetBlockDay = async (id: number) => {
        const response = await jwtPatch_(URL_BLOCK_DAY_READY_UNSET, { id })
        const result   = await response
        if (DEBUG) console.log('BlockStore: readyUnsetBlockDay: ', result)
        return result.data
    }

    // __ Устанавливаем новые Производственные Линии
    const taskLinesManufLineSet = async (data: IBlockLineSetData[]) => {
        console.log('data: ', data)

        const response = await jwtPost(URL_BLOCKS_TASK_LINES_TABLE_SET, { data })
        const result   = await response
        if (DEBUG) console.log('BlockStore: taskLinesManufLineSet: ', result)
        return result.data
    }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---             Персонал                        !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    // __ Получение активных рабочих
    const getActiveWorkers = async () => {
        if (globalWorkers.value.length) {
            return globalWorkers
        }

        const response = await jwtGet(URL_BLOCK_DAY_WORKERS_ACTIVE)
        const result   = await response

        // __ кэшируем
        globalWorkers.value = result.data
            .filter((w: IBlockDayWorker) => w.id !== 0)
            .sort((a: IBlockDayWorker, b: IBlockDayWorker) => a.surname.localeCompare(b.surname))


        if (DEBUG) console.log('BlockStore: getActiveWorkers: ', result)
        return result.data
    }

    // __ Добавление Рабочего в Производственный день
    const addWorkerToBlockDay = async (day_id: number | null = null, worker_id: number | null = null) => {
        if (!day_id || !worker_id) {
            return
        }
        const response = await jwtPost(URL_BLOCK_DAY_WORKER_ADD, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('BlockStore: addWorkerToBlockDay: ', result)
        return result.data
    }

    // __ Добавление Группы Рабочих в Производственный день
    const addWorkersToBlockDay = async (day_id: number | null = null, worker_ids: number[] | null = null) => {
        if (!day_id || !worker_ids) {
            return
        }
        const response = await jwtPost(URL_BLOCK_DAY_WORKER_GROUP_ADD, { day_id, worker_ids })
        const result   = await response
        if (DEBUG) console.log('BlockStore: addWorkersGroupToBlockDay: ', result)
        return result.data
    }

    // __ Удаление Рабочего из Производственного дня
    const removeWorkerFromBlockDay = async (day_id: number, worker_id: number) => {
        const response = await jwtPost(URL_BLOCK_DAY_WORKER_REMOVE, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('BlockStore: removeWorkerToBlockDay: ', result)
        return result.data
    }

    // __ Добавление Ответственного в Производственный день
    const addResponsibleToBlockDay = async (day_id: number, worker_id: number) => {
        const response = await jwtPatch_(URL_BLOCK_DAY_RESPONSIBLE_ADD, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('BlockStore: addResponsibleToBlockDay: ', result)
        return result.data
    }

    // __ Удаление Ответственного из Производственного дня
    const removeResponsibleFromBlockDay = async (day_id: number, worker_id: number) => {
        const response = await jwtPatch_(URL_BLOCK_DAY_RESPONSIBLE_REMOVE, { day_id, worker_id })
        const result   = await response
        if (DEBUG) console.log('BlockStore: removeResponsibleFromBlockDay: ', result)
        return result.data
    }


    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---               Block Lines                   !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    const setBlockTaskLinesDone = async (blockTaskLinesIds: number[]) => {
        if (!blockTaskLinesIds.length) {
            return []
        }
        const result = await jwtPost(URL_BLOCKS_TASK_LINE_DONE, { ids: blockTaskLinesIds })
        if (DEBUG) console.log('BlockStore: setBlockTaskLinesDone: ', result)
        return result.data
    }

    // __ Устанавливаем "Не Выполнено" на BlockTaskLines
    const setBlockTaskLinesFalse = async (blockTaskLinesIds: number[], falseReason: string | null = null) => {
        if (!blockTaskLinesIds.length && !falseReason) {
            return []
        }
        const result = await jwtPost(URL_BLOCKS_TASK_LINE_FALSE, { ids: blockTaskLinesIds, reason: falseReason })
        if (DEBUG) console.log('BlockStore: setBlockTaskLinesFalse: ', result)
        return result.data
    }

    // __ Сбрасываем статусы на BlockTaskLines
    const setBlockTaskLinesReset = async (blockTaskLinesIds: number[]) => {
        if (!blockTaskLinesIds.length) {
            return []
        }
        const result = await jwtPost(URL_BLOCKS_TASK_LINE_RESET, { ids: blockTaskLinesIds })
        if (DEBUG) console.log('BlockStore: setBlockTaskLinesReset: ', result)
        return result.data
    }

    // __ Разделение линий СЗ при выполнении СЗ
    const divideLineInBlockTaskPending = async (blockTask: IBlockTask, period: IPeriod | null = null) => {

        const findTask = globalBlockTasks.value.find((task: IBlockTask) => task.id === blockTask.id)
        if (!findTask) {
            return
        }
        console.log('findTask: ', findTask)

        repositionBlockTaskLines(findTask)
        // const result = await saveChanges()
        return await saveChanges(globalBlockTasks.value, globalBlockTasksCopy, period)
        // return saveChanges(globalBlockTasksPending.value, globalBlockTasksPendingCopy)
    }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---                 Тесты                       !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    const test = async () => {
        const response = await jwtGet(URL_BLOCKS_TEST)
        const result   = await response
        if (DEBUG) console.log('BlocksStore: test: ', result)
        return result
    }


    return {
        globalBlockTasks,
        globalBlockTaskStatuses,
        globalRenderPeriod,
        globalBlockTaskTimesShow,
        globalBlockTaskFullDaysShow,
        globalBlockTaskActiveOrderId,
        globalBlockTaskOrderTypeColor,
        globalManageTaskCardActiveBlockLine,


        globalBlockTasksPending,

        globalWorkers,

        // globalBlockTaskPrintData,

        addBlockTaskToGlobal,
        applyBlockTaskComment,
        applyChanges,
        saveChanges,
        setBlockTasksLines,
        applyMergeTasks,
        applyMergeTasksGroups,
        setGlobalArrayChangeManufLines,

        setBlockTaskLinesDone,
        setBlockTaskLinesFalse,
        setBlockTaskLinesReset,
        divideLineInBlockTaskPending,

        taskLinesManufLineSet,
        getBlockDayByDateAndChange,
        setBlockDayComment,
        modifyChange,
        readyGetBlockDay,
        getBlockDaysByDates,

        getBlockTasks,
        getBlockTasksByOrderId,
        deleteBlockTasksByOrderId,
        addBlockTasksByOrderId,
        calcBlockTasksCutByOrderId,
        getBlockTasksByStatusBeforeDate,
        getBlockTasksByStatusOnDate,
        checkBlockTasksByStatusOnDate,
        setBlockTaskComment,
        setBlockTaskActionAt,
        readySetBlockDay,
        readyUnsetBlockDay,
        startBlockDay,
        finishBlockDay,

        getBlockCollections,
        getBlockCollectionById,
        createBlockCollection,
        updateBlockCollection,

        getBlockById,
        createBlock,
        updateBlock,

        getBlockTasksByStatus,

        getBlockTaskStatuses,
        patchBlockTaskStatusColor,
        setBlockTasksStatuses,

        getActiveWorkers,
        addWorkerToBlockDay,
        addWorkersToBlockDay,
        removeWorkerFromBlockDay,
        addResponsibleToBlockDay,
        removeResponsibleFromBlockDay,


        test,
    }
})
