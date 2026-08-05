// Хранилище для ПЯ Швейки

import { defineStore } from 'pinia'

import { jwtGet, jwtPost, /*jwtDelete,*/ jwtPatch, jwtPut_, jwtPut, jwtPatch_, jwtDelete } from '@/app/utils/jwt_api'
import type {
    IPeriod, IRenderMatrixDiff,

} from '@/types'
import { ref } from 'vue'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import { isNumber } from '@/app/helpers/helpers_lib.ts'

import { additionDaysInStrFormat } from '@/app/helpers/helpers_date'


const DEBUG = true

const URL_ASSEMBLY_MODEL_MANUFACTURE_GROUPS = '/assembly/model/manufacture/groups'  // URL для Групп Моделей Сортировки

const URL_ASSEMBLY      = '/assembly'                             // URL для получения Блоков
const URL_ASSEMBLY_TEST = '/assembly/test'                        // URL для тестирования

// const URL_ASSEMBLY_COLLECTIONS_TUNING_TIME           = '/assembly/collections/tuning/time/'           // URL для получения времени переналадки Коллекций Блоков
// const URL_ASSEMBLY_COLLECTIONS_TUNING_TIME_LIST      = '/assembly/collections/tuning/time/list'       // URL для получения времени переналадки Коллекций Блоков для Группы Коллекций
// const URL_ASSEMBLY_COLLECTIONS_TUNING_TIME_OPTIMIZED = '/assembly/collections/tuning/time/optimized'  // URL для получения оптимизированного времени переналадки Коллекций Блоков для Группы Коллекций
// const URL_ASSEMBLY_COLLECTIONS_TUNING_TIME_BETWEEN   = '/assembly/collections/tuning/time/between'    // URL для получения времени переналадки между двумя Коллекциями Блоков
//
//
//
// const URL_ASSEMBLY_TASKS_STATUS               = '/assembly/tasks/status'                // URL для получения Сменных заданий по статусу
// const URL_ASSEMBLY_TASKS_STATUS_PERIOD        = '/assembly/tasks/status/period'         // URL для получения Сменных заданий по статусу в периоде
// const URL_ASSEMBLY_TASKS_STATUS_BEFORE_DATE   = '/assembly/tasks/status/date/before'    // URL для получения Сменных заданий по статусу
// const URL_ASSEMBLY_TASKS_STATUS_ON_DATE       = '/assembly/tasks/status/date/on'        // URL для получения Сменных заданий по статусу в определенный день
// const URL_ASSEMBLY_TASKS_STATUS_ON_DATE_CHECK = '/assembly/tasks/status/date/on/check'  // URL для проверки наличия Сменных заданий по статусу в определенный день
//
//
// const URL_ASSEMBLY_TASK_STATUSES             = '/assembly/task/statuses'               // URL для получения Статуса Движения СЗ
// const URL_ASSEMBLY_TASK_STATUSES_SET         = '/assembly/task/statuses/set'           // URL для изменения/добавления Статуса Движения СЗ
// const URL_ASSEMBLY_TASK_STATUSES_COLOR_PATCH = '/assembly/task/statuses/color/patch'   // URL для получения Статуса Движения СЗ
//
// const URL_ASSEMBLY_TASKS                      = '/assembly/tasks'                       // URL для получения Сменных заданий
// const URL_ASSEMBLY_TASKS_UPDATE               = '/assembly/tasks/update'                // URL для обновления Сменных заданий
// const URL_ASSEMBLY_TASKS_ORDER_ID             = '/assembly/tasks/order'                 // URL для получения Сменных заданий по id Заявки
// const URL_ASSEMBLY_TASKS_DELETE_BY_ORDER_ID   = '/assembly/tasks/delete/order'          // URL для удаления Сменных заданий по id Заявки
// const URL_ASSEMBLY_TASKS_ADD_BY_ORDER_ID      = '/assembly/tasks/add/order'             // URL для добавления Сменных заданий по id Заявки
// const URL_ASSEMBLY_TASKS_CALC_BY_ORDER_ID     = '/assembly/tasks/calc/order'            // URL для пересчета Кроя по id Заявки
// const URL_ASSEMBLY_TASKS_STATUS_BEFORE_DATE   = '/assembly/tasks/status/date/before'    // URL для получения Сменных заданий по статусу
// const URL_ASSEMBLY_TASKS_STATUS_ON_DATE       = '/assembly/tasks/status/date/on'        // URL для получения Сменных заданий по статусу в определенный день
// const URL_ASSEMBLY_TASKS_STATUS_ON_DATE_CHECK = '/assembly/tasks/status/date/on/check'  // URL для проверки наличия Сменных заданий по статусу в определенный день
// const URL_ASSEMBLY_TASKS_COMMENT              = '/assembly/tasks/comment'               // URL для изменения комментария к Сменному заданию
// const URL_ASSEMBLY_TASKS_CHANGE               = '/assembly/tasks/change'                // URL для изменения смены Сменного задания
// const URL_ASSEMBLY_TASKS_ACTION_AT_SET        = '/assembly/tasks/action/set'            // URL для установки даты выполнения (action_at) СЗ
//
// const URL_ASSEMBLY_TASK_LINES_TABLE_SET  = '/assembly/tasks/lines/line/set'        // URL для изменения раскройного стола для записи СЗ
// const URL_ASSEMBLY_TASK_LINE_DONE        = '/assembly/tasks/line/done'             // URL для установки статуса "Выполнено" для записи СЗ
// const URL_ASSEMBLY_TASK_LINE_FALSE       = '/assembly/tasks/line/false'            // URL для установки статуса "Не Выполнено" для записи СЗ
// const URL_ASSEMBLY_TASK_LINE_RESET       = '/assembly/tasks/line/reset'            // URL для сброса статуса для записи СЗ
// const URL_ASSEMBLY_TASK_LINE_DESCRIPTION = '/assembly/tasks/line/description'      // URL для изменения описания для записи СЗ
//
// const URL_ASSEMBLY_DAY                    = '/assembly/day'                         // URL для получения рабочего дня
// const URL_ASSEMBLY_DAY_PERIOD             = '/assembly/days/period'                 // URL для получения рабочих дней за период
// const URL_ASSEMBLY_DAY_DATES              = '/assembly/day/dates'                   // URL для получения рабочих дней по статусу
// const URL_ASSEMBLY_DAY_COMMENT            = '/assembly/day/comment'                 // URL для сохранения комментария к дню
// const URL_ASSEMBLY_DAY_WORKERS_ACTIVE     = '/workers/active'                      // URL для получения активных рабочих
// const URL_ASSEMBLY_DAY_WORKER_ADD         = '/assembly/day/worker/add'              // URL для добавления исполнителя к дню
// const URL_ASSEMBLY_DAY_WORKER_GROUP_ADD   = '/assembly/day/workers/add'             // URL для добавления группы исполнителей к дню
// const URL_ASSEMBLY_DAY_WORKER_REMOVE      = '/assembly/day/worker/remove'           // URL для удаления исполнителя к дню
// const URL_ASSEMBLY_DAY_RESPONSIBLE_ADD    = '/assembly/day/responsible/add'         // URL для добавления ответственного к дню
// const URL_ASSEMBLY_DAY_RESPONSIBLE_REMOVE = '/assembly/day/responsible/remove'      // URL для удаления ответственного к дню
// const URL_ASSEMBLY_DAY_START              = '/assembly/day/start'                   // URL для старта дня СЗ
// const URL_ASSEMBLY_DAY_FINISH             = '/assembly/day/finish'                  // URL для финиш дня СЗ
// const URL_ASSEMBLY_DAY_READY_GET          = '/assembly/day/ready/get'               // URL для получения маячка готовности дня с СЗ к добавлению новых СЗ
// const URL_ASSEMBLY_DAY_READY_SET          = '/assembly/day/ready/set'               // URL для установки маяка готовности к добавлению новых СЗ
// const URL_ASSEMBLY_DAY_READY_UNSET        = '/assembly/day/ready/unset'             // URL для снятия маяка готовности к добавлению новых СЗ


export const useAssemblyStore = defineStore('assembly', () => {
    //
    // // __ Массив СЗ Раскроя
    // const globalAssemblyTasks = ref<IAssemblyTask[]>([])
    //
    // // __ Копия массива СЗ Раскроя для отслеживания изменений
    // let globalAssemblyTasksCopy: IAssemblyTask[] = []
    //
    // // __ Массив СЗ, готовых к выполнению
    // const globalAssemblyTasksPending = ref<IAssemblyTask[]>([])
    //
    // // __ Копия массива СЗ Раскроя для отслеживания изменений
    // let globalAssemblyTasksPendingCopy: IAssemblyTask[] = []
    //
    // // __ Показывать ли Трудозатраты в календаре СЗ Раскроя
    // const globalAssemblyTaskTimesShow = ref(true)
    //
    // // __ Показывать ли Раскрытый день или нет в календаре СЗ Раскроя
    // const globalAssemblyTaskFullDaysShow = ref(true)
    //
    // // __ Показывать единицу измерения в Метрах Квадратных или Штуках
    // const globalAssemblyTaskAssemblyInSquare = ref(true)
    //
    // // __ Текущее Заявка, на которое ссылается кликнутое СЗ (для календаря для подсветки СЗ, которые ссылаются на одну заявку)
    // const globalAssemblyTaskActiveOrderId = ref<number | null>(null)
    //
    // // __ Текущая Запись (AssemblyLine) в карточке СЗ в календаре СЗ Раскроя
    // const globalManageTaskCardActiveAssemblyLine = ref<IAssemblyTaskLine | null>(null)
    //
    // // __ Раскрашивать заявки в календаре в цвет Типа Заявки или в цвет Статусов Движения Заявок
    // const globalAssemblyTaskOrderTypeColor = ref(false)
    //
    // // __ Период рендеринга календаря
    // const globalRenderPeriod = ref<IPeriod>(PERIOD_DRAFT)
    //
    // // __ Статусы Движения СЗ
    // const globalAssemblyTaskStatuses = ref<IAssemblyTaskStatusEntity[]>([])
    //
    // // __ Массив Рабочих
    // const globalWorkers = ref<IAssemblyDayWorker[]>([])
    //
    //
    // // --- ------------------------------------------------------------------------------------------
    // // --- ---------------- Тут вся логика по управлению и сохранению частей СЗ ---------------------
    // // --- ------------------------------------------------------------------------------------------
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // // !!! --- Тут вся логика по управлению и сохранению частей СЗ !!!
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //
    // /**
    //  * __ Добавление новой части СЗ и изменение старой части СЗ, на основе которого была создана новая часть
    //  * @param addAssemblyTask     - __ СЗ, которое уже было сформировано на основе старой части СЗ (правая панель)__
    //  * @param leftPanel        - __ контент в новом СЗ (правая панель)__
    //  * @param oldAssemblyTask     - __ СЗ, на основе которого формируется новая часть СЗ (левая панель)__
    //  * @param rightPanel       - __ контент в старом СЗ (левая панель)__
    //  */
    // const addAssemblyTaskToGlobal = async (
    //     oldAssemblyTask: IAssemblyTask,
    //     leftPanel: IAssemblyTaskLine[],
    //     addAssemblyTask: IAssemblyTask | null     = null,
    //     rightPanel: IAssemblyTaskLine[] | null = null,
    // ) => {
    //
    //     // console.log('leftPanel: ', leftPanel)
    //     // debugger
    //
    //     leftPanel                = repositionAssemblyTaskLines(leftPanel)   // __ Пересчитываем позиции для строк СЗ (AssemblyLines[])
    //     oldAssemblyTask.assembly_lines = leftPanel              // __ oldAssemblyTask приходит по ссылке
    //
    //     // __ Если есть правая панель, то добавляем ее в массив СЗ
    //     if (addAssemblyTask && rightPanel) {
    //
    //         // console.log('passed')
    //
    //         rightPanel               = repositionAssemblyTaskLines(rightPanel)  // __ Пересчитываем позиции для строк СЗ (AssemblyLines[])
    //         addAssemblyTask.assembly_lines = rightPanel             // __ addAssemblyTask приходит новым объектом
    //
    //         // __ Добавляем новый объект в массив
    //         globalAssemblyTasks.value.push(addAssemblyTask)
    //
    //         // __ Переопределяем порядок СЗ.
    //         // __ Находим все СЗ в глабальной переменной с датой созданного СЗ и меняем порядок
    //         globalAssemblyTasks.value = repositionAssemblyTaskInDay(globalAssemblyTasks.value, addAssemblyTask.action_at)
    //
    //     }
    //
    //     await saveChanges()   // __ Сохраняем изменения
    // }
    //
    // // __ Устанавливаем содерживое СЗ
    // const setAssemblyTasksLines = (assemblyTask: IAssemblyTask, assemblyTaskLines: IAssemblyTaskLine[]) => {
    //     assemblyTask.assembly_lines = assemblyTaskLines
    // }
    //
    // // __ Устанавливаем комментарий в СЗ
    // const applyAssemblyTaskComment = (assemblyTaskId: number, comment: string) => {
    //     const assemblyTask = globalAssemblyTasks.value.find((task: IAssemblyTask) => task.id === assemblyTaskId)
    //
    //     console.log('assemblyTask: ', assemblyTask)
    //     console.log('comment: ', comment)
    //     if (assemblyTask) {
    //         assemblyTask.comment = comment
    //     }
    // }
    //
    // // __ Применение изменений
    // const applyChanges = async (diffs: IRenderMatrixDiff[] = []) => {
    //
    //     // __ Если нет изменений, то выход
    //     if (diffs.length === 0) {
    //         return
    //     }
    //
    //     // __ Если нет статусов, то получаем их с сервера
    //     if (globalAssemblyTaskStatuses.value.length === 0) {
    //         await getAssemblyTaskStatuses()
    //     }
    //
    //     diffs.forEach(diff => {
    //
    //         // __ Если изменилась позиция или дата производства или статус, то меняем ее в глобальном массиве
    //         if (diff.isPositionChanged || diff.isMoved || diff.statusId || diff.isChangeChanged) {
    //             const findTask = globalAssemblyTasks.value.find((task: IAssemblyTask) => task.id === diff.taskId)
    //             if (findTask) {
    //
    //                 if (diff.newTaskPosition) {
    //                     findTask.position = diff.newTaskPosition
    //                 }
    //
    //                 if (diff.newChange) {
    //                     findTask.change = diff.newChange as IAssemblyTaskChangeKeys
    //                 }
    //
    //                 if (diff.isMoved) {
    //                     findTask.action_at = additionDaysInStrFormat(globalRenderPeriod.value.start, diff.dayToOffset ?? 0)
    //                 }
    //
    //                 if (diff.statusId) {
    //                     const findStatus = globalAssemblyTaskStatuses.value.find((status: IAssemblyTaskStatusEntity) => status.id === diff.statusId)
    //                     if (findStatus) {
    //                         findTask.current_status.id    = findStatus.id
    //                         findTask.current_status.name  = findStatus.name
    //                         findTask.current_status.color = findStatus.color
    //                     }
    //
    //                 }
    //             }
    //         }
    //     })
    //
    //     return await saveChanges()   // __ Сохраняем изменения
    // }
    //
    // // __ Объединение СЗ для одинаковых Заявок в одном календарном дне
    // const mergeTasks = (assemblyTasks: IAssemblyTask[]) => {
    //
    //     // __ Если массив СЗ меньше 2, то выход
    //     if (assemblyTasks.length < 2) {
    //         return
    //     }
    //
    //     // __ Объединяем СЗ
    //     const mergedTasks = mergeAssemblyTasks(assemblyTasks)
    //
    //     // __ Пересчитываем позиции !!! Важно выполнение после объединения СЗ
    //     mergedTasks[0].assembly_lines = repositionAssemblyTaskLines(mergedTasks[0].assembly_lines)
    //
    //     // __ Заменяем СЗ в глобальном массиве
    //     const findTask = globalAssemblyTasks.value.find((task: IAssemblyTask) => task.id === mergedTasks[0].id)
    //     if (findTask) {
    //         findTask.assembly_lines = mergedTasks[0].assembly_lines
    //     }
    //
    //     // __ Удаляем лишние СЗ
    //     for (let i = 1; i < assemblyTasks.length; i++) {
    //
    //         // __ Находим то, что нужно удалить
    //         const workTask = globalAssemblyTasks.value.find((task: IAssemblyTask) => task.id === assemblyTasks[i].id)
    //         if (workTask) {
    //
    //             // __ Удаляем
    //             globalAssemblyTasks.value = globalAssemblyTasks.value.filter((task: IAssemblyTask) => {
    //                 return task.id !== assemblyTasks[i].id
    //             })
    //
    //             // __ Переопределяем порядок СЗ в дне, из которого удалили
    //             globalAssemblyTasks.value = repositionAssemblyTaskInDay(globalAssemblyTasks.value, workTask.action_at)
    //         }
    //     }
    //
    //     // __ Переопределяем порядок СЗ в дне, в котором добавили
    //     globalAssemblyTasks.value = repositionAssemblyTaskInDay(globalAssemblyTasks.value, mergedTasks[0].action_at)
    //
    // }
    //
    // // __ Применение объединения СЗ для массива СЗ
    // const applyMergeTasks = async (assemblyTasks: IAssemblyTask[]) => {
    //
    //     mergeTasks(assemblyTasks)
    //     return await saveChanges()
    // }
    //
    // // __ Применение объединения СЗ для массива массивов СЗ  [[...], [...]]
    // const applyMergeTasksGroups = async (assemblyTasksGroups: IAssemblyTask[][]) => {
    //     assemblyTasksGroups.forEach(assemblyTasks => mergeTasks(assemblyTasks))
    //     return await saveChanges()
    // }
    //
    //
    // // __ Сохранение изменений (Синхронизация с сервером)
    // const saveChanges = async (globalArray = globalAssemblyTasks.value, globalArrayCopy = globalAssemblyTasksCopy, period: IPeriod | null = null) => {
    //     const diffsInGlobalAssemblyTasks = getAssemblyTasksDiff(globalArray, globalArrayCopy)
    //
    //     // __ Если нет изменений, то выход
    //     if (diffsInGlobalAssemblyTasks.length === 0) {
    //         return
    //     }
    //
    //     console.log(diffsInGlobalAssemblyTasks)
    //     console.log('Сохраняем изменения')
    //
    //     const result = await jwtPost(URL_ASSEMBLY_TASKS_UPDATE, { diffs: diffsInGlobalAssemblyTasks })
    //     if (DEBUG) console.log('saveChanges: ', result)
    //
    //
    //     // __ Если есть добавление новых элементов в БД, то обновляем данные, чтобы получить id
    //     // __ Если это изменение позиции, то просто пишем в базу
    //     if (isAddItemsInDiffsPresents(diffsInGlobalAssemblyTasks)) {
    //
    //         // __ Получаем СЗ с сервера и реактивное обновление
    //         await getAssemblyTasks(period)
    //         console.log('Server data updated')
    //     } else {
    //
    //         globalAssemblyTasksCopy = JSON.parse(JSON.stringify(globalArray))     // __ копия для отслеживания изменений
    //         // globalArrayCopy = JSON.parse(JSON.stringify(globalArray))     // __ копия для отслеживания изменений
    //     }
    //
    //     return result
    // }
    //
    //
    // // __ Меняем динамически Столы раскроя в глобальном массиве, чтобы не перезагружать данные с сервера
    // const setGlobalArrayChangeManufLines = (data: IAssemblyLineSetData[]) => {
    //     for (const item of data) {
    //         let isFind = false
    //         for (const task of globalAssemblyTasks.value) {
    //             for (const line of task.assembly_lines) {
    //                 if (item.id === line.id) {
    //                     line.manuf_line = item.line
    //                     isFind          = true
    //                     break
    //                 }
    //                 if (isFind) {
    //                     break
    //                 }
    //             }
    //             if (isFind) {
    //                 break
    //             }
    //         }
    //     }
    //
    //     // __ Копия для отслеживания изменений
    //     globalAssemblyTasksCopy = JSON.parse(JSON.stringify(globalAssemblyTasks.value))
    // }
    //
    //
    // // --- ------------------------------------------------------------------------------------------
    //
    //
    // // --- ----------------------------------------------------------
    // // --- ------------------- Сменные задания ----------------------
    // // --- ----------------------------------------------------------
    //
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // // !!! ---               Сменные задания               !!!
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //
    // // __ Получение СЗ Блоков с сервера за период
    // const getAssemblyTasks = async (period: IPeriod | null = null) => {
    //     let response
    //     if (period) {
    //         response = await jwtGet(URL_ASSEMBLY_TASKS, { period })
    //     } else {
    //         response = await jwtGet(URL_ASSEMBLY_TASKS)
    //     }
    //     const result = await response
    //
    //     globalAssemblyTasks.value = result.data                                   // __ кэшируем
    //     globalAssemblyTasksCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений
    //
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyTasks: ', result)
    //     return result.data
    // }
    //
    //
    // // __ Получение СЗ Блоков по ID Заявки
    // const getAssemblyTasksByOrderId = async (id: number | null = null) => {
    //     if (!id) {
    //         return
    //     }
    //     const response = await jwtGet(`${URL_ASSEMBLY_TASKS_ORDER_ID}/${id}`)
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyTasksByOrderId: ', result)
    //     return result.data
    // }
    //
    //
    // // __ Удаление СЗ Блоков по ID Заявки
    // const deleteAssemblyTasksByOrderId = async (id: number | null = null) => {
    //     if (!id) {
    //         return
    //     }
    //     const response = await jwtDelete(URL_ASSEMBLY_TASKS_DELETE_BY_ORDER_ID, { id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: deleteAssemblyTasksByOrderId: ', result)
    //     return result
    // }
    //
    // // __ Добавление СЗ Блоков по ID Заявки
    // const addAssemblyTasksByOrderId = async (id: number | null = null) => {
    //     if (!id) {
    //         return
    //     }
    //     const response = await jwtPost(URL_ASSEMBLY_TASKS_ADD_BY_ORDER_ID, { id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: addAssemblyTasksByOrderId: ', result)
    //     return result
    // }
    //
    // // __ Пересчет СЗ Блоков по ID Заявки
    // const calcAssemblyTasksCutByOrderId = async (id: number | null = null) => {
    //     if (!id) {
    //         return
    //     }
    //     const response = await jwtPost(URL_ASSEMBLY_TASKS_CALC_BY_ORDER_ID, { id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: calcAssemblyTasksCutByOrderId: ', result)
    //     return result
    // }
    //
    // // __ Получение СЗ Блоков по статусу или массиву статусов до определенной даты
    // const getAssemblyTasksByStatusBeforeDateAndChange = async (date: string, change: IAssemblyTaskChangeKeys, status: number[] | number | null = null) => {
    //     let response
    //     if (status) {
    //         if (isNumber(status)) {
    //             status = [status]
    //         }
    //
    //         response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS_BEFORE_DATE, { date, change, statuses: status })
    //     } else {
    //         response = await jwtGet(date, URL_ASSEMBLY_TASKS_STATUS_BEFORE_DATE)
    //     }
    //     const result = await response
    //
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyTasksByStatusBeforeDate: ', result)
    //     return result.data
    // }
    //
    //
    // // __ Получение СЗ Блоков по статусу или массиву статусов в определенную дату
    // const getAssemblyTasksByStatusOnDate = async (date: string, status: number[] | number | null = null) => {
    //     let response
    //     if (status) {
    //         if (isNumber(status)) {
    //             status = [status]
    //         }
    //
    //         response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS_ON_DATE, { date, statuses: status })
    //     } else {
    //         response = await jwtGet(date, URL_ASSEMBLY_TASKS_STATUS_ON_DATE)
    //     }
    //     const result = await response
    //
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyTasksByStatusOnDate: ', result)
    //     return result.data
    // }
    //
    // // __ Проверка на наличие СЗ Блоков по статусу или массиву статусов в определенную дату
    // const checkAssemblyTasksByStatusOnDate = async (date: string, change: string, status: number[] | number | null = null) => {
    //
    //     console.log('change: ', change)
    //     let response
    //     if (status) {
    //         if (isNumber(status)) {
    //             status = [status]
    //         }
    //
    //         response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS_ON_DATE_CHECK, { date, change, statuses: status })
    //     } else {
    //         response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS_ON_DATE_CHECK, { date, change })
    //     }
    //     const result = await response
    //
    //     if (DEBUG) console.log('AssemblyStore: checkAssemblyTasksByStatusOnDate: ', result)
    //     return result.data
    // }
    //
    // // __ Сохранение Комментария к Сменному заданию - СЗ
    // const setAssemblyTaskComment = async (id: number, comment: string | null = null) => {
    //     const response = await jwtPost(URL_ASSEMBLY_TASKS_COMMENT, { id, comment })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: setAssemblyTaskComment: ', result)
    //     return result.data
    // }
    //
    //
    // // __ Изменяем смену СЗ
    // // __ Сохранение Комментария к Сменному заданию - СЗ
    // const modifyChange = async (id: number, change: IAssemblyTaskChangeKeys) => {
    //     const response = await jwtPost(URL_ASSEMBLY_TASKS_CHANGE, { id, change })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: modifyChange: ', result)
    //     return result.data
    // }
    //
    // // __ Установка даты выполнения СЗ
    // const setAssemblyTaskActionAt = async (id: number, date: string) => {
    //     const response = await jwtPost(URL_ASSEMBLY_TASKS_ACTION_AT_SET, { id, date })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: setAssemblyTaskActionAt: ', result)
    //     return result.data
    // }
    //
    //
    // // __ Получение СЗ Блоков по статусу или массиву статусов
    // const getAssemblyTasksByStatus = async (status: number[] | number | null = null) => {
    //     let response
    //     if (status) {
    //         if (isNumber(status)) {
    //             status = [status]
    //         }
    //
    //         response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS, { statuses: status })
    //         // response = await jwtGet(`${URL_ASSEMBLY_TASKS_STATUS}/${status}`)
    //     } else {
    //         response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS)
    //     }
    //     const result = await response
    //
    //     globalAssemblyTasksPending.value = result.data                                   // __ кэшируем
    //     globalAssemblyTasksPendingCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений
    //
    //
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyTasksByStatus: ', result)
    //     return result.data
    // }
    //
    //
    // // __ Получение СЗ Блоков по статусу или массиву статусов и Периоду
    // const getAssemblyTasksByStatusAndPeriod = async (status: number[] | number | null | undefined = null, period: IPeriod | null = null) => {
    //     let response
    //     if (status) {
    //         if (isNumber(status)) {
    //             status = [status]
    //         }
    //
    //         if (period) {
    //             response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS, { statuses: status, period })
    //         } else {
    //             response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS, { statuses: status })
    //         }
    //     } else {
    //         if (period) {
    //             response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS, { period })
    //         } else {
    //             response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS)
    //         }
    //         response = await jwtGet(URL_ASSEMBLY_TASKS_STATUS)
    //     }
    //     const result = await response
    //
    //     globalAssemblyTasksPending.value = result.data                                   // __ кэшируем
    //     globalAssemblyTasksPendingCopy   = JSON.parse(JSON.stringify(result.data))       // __ копия для отслеживания изменений
    //
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyTasksByStatusAndPeriod: ', result)
    //     return result.data
    // }
    //
    //
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // // !!! ---        Коллекции (Группы) Блоков            !!!
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //
    // // __ Получение Коллекции Блоков
    // const getAssemblyCollections = async () => {
    //     const response = await jwtGet(URL_ASSEMBLY_COLLECTIONS)
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyCollections: ', result)
    //     return result.data
    // }
    //
    // // __ Получение Коллекции блоков по id
    // const getAssemblyCollectionById = async (id: number) => {
    //     const response = await jwtGet(URL_ASSEMBLY_COLLECTIONS + '/' + id)
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyCollectionById: ', result)
    //     return result.data
    // }
    //
    // // __ Создаем Коллекции блоков
    // const createAssemblyCollection = async (assemblyCollection: IAssemblyCollection) => {
    //     const result = await jwtPost(URL_ASSEMBLY_COLLECTIONS, assemblyCollection)
    //     if (DEBUG) console.log('AssemblyStore: createAssemblyCollection: ', result)
    //     return result
    // }
    //
    // // __ Обновляем Коллекции блоков
    // const updateAssemblyCollection = async (assemblyCollection: IAssemblyCollection) => {
    //     const result = await jwtPut_(URL_ASSEMBLY_COLLECTIONS, assemblyCollection)
    //     if (DEBUG) console.log('AssemblyStore: updateAssemblyCollection: ', result)
    //     return result
    // }
    //
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // // !!! ---                 Блоки                       !!!
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // // __ Получение Блока по id
    // const getAssemblyById = async (id: number) => {
    //     const response = await jwtGet(URL_ASSEMBLY + '/' + id)
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyById: ', result)
    //     return result.data
    // }
    //
    // // __ Создаем Блок
    // const createAssembly = async (assembly: IAssembly) => {
    //     const result = await jwtPost(URL_ASSEMBLY, assembly)
    //     if (DEBUG) console.log('AssemblyStore: createAssembly: ', result)
    //     return result
    // }
    //
    // // __ Обновляем Блок
    // const updateAssembly = async (assembly: IAssembly) => {
    //     const result = await jwtPut_(URL_ASSEMBLY, assembly)
    //     if (DEBUG) console.log('AssemblyStore: updateAssembly: ', result)
    //     return result
    // }
    //
    // // --- ----------------------------------------------------------
    // // --- ------------------- Статусы СЗ ---------------------------
    // // --- ----------------------------------------------------------
    //
    // // __ Получение Статусов Движения СЗ
    // const getAssemblyTaskStatuses = async () => {
    //     const response = await jwtGet(URL_ASSEMBLY_TASK_STATUSES)
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyTaskStatuses: ', result)
    //     globalAssemblyTaskStatuses.value = result.data    // __ кэшируем
    //     return result.data
    // }
    //
    // // __ Устанавливаем цвет ярлычка Типов заказов (серийная, гаррмем, прогнозная и т.д.)
    // const patchAssemblyTaskStatusColor = async (assemblyTaskStatusId: number, color: string) => {
    //     const result = await jwtPatch(URL_ASSEMBLY_TASK_STATUSES_COLOR_PATCH, { id: assemblyTaskStatusId, color })
    //     if (DEBUG) console.log('AssemblyStore: patchAssemblyTaskStatusColor', result)
    //     await getAssemblyTaskStatuses()   // __ Обновляем статусы, чтобы был актуальный цвет
    //     return result.data
    // }
    //
    // // __ Устанавливаем статусы для СЗ.
    // // __ data: [{ task: number, status: number }]
    // const setAssemblyTasksStatuses = async (data: IAssemblyTaskStatusesSet[]) => {
    //     const response = await jwtPost(URL_ASSEMBLY_TASK_STATUSES_SET, data)
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: setStatuses: ', result)
    //     return result.data
    // }
    //
    //
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // // !!! ---          Производственный день              !!!
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //
    // // __ Получение производственных дней за период
    // const getAssemblyDayByPeriod = async (period: IPeriod | null = null) => {
    //     console.log(period)
    //
    //     let response
    //     if (period) {
    //         response = await jwtGet(URL_ASSEMBLY_DAY_PERIOD, { period })
    //     } else {
    //         response = await jwtGet(URL_ASSEMBLY_DAY_PERIOD)
    //     }
    //
    //     const result = await response
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyDayByPeriod: ', result)
    //     return result.data
    // }
    //
    // // __ Получение производственного дня по дате и смене
    // const getAssemblyDayByDateAndChange = async (date: string, change: IAssemblyTaskChangeKeys = CHANGES.CHANGE_1.NAME) => {
    //     const response = await jwtGet(`${URL_ASSEMBLY_DAY}/${date}/${change}`)
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyDayByDateAndChange: ', result)
    //     return result.data
    // }
    //
    // // __ Сохранение Комментария к производственному дню
    // const setAssemblyDayComment = async (id: number, comment: string | null = null) => {
    //     const response = await jwtPost(URL_ASSEMBLY_DAY_COMMENT, { id, comment })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: setAssemblyDayComment: ', result)
    //     return result.data
    // }
    //
    // // __ Получение производственных дней по массиву дат
    // // __ Тут по хорошему надо прикрутить еще и смену, но оставим на потом
    // const getAssemblyDaysByDates = async (dates: string[]) => {
    //     if (!dates.length) {
    //         return []
    //     }
    //
    //     const response = await jwtGet(URL_ASSEMBLY_DAY_DATES, { dates })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyDaysByDates: ', result)
    //     return result.data
    // }
    //
    // // __ Старт СЗ
    // const startAssemblyDay = async (id: number) => {
    //     const response = await jwtPatch_(URL_ASSEMBLY_DAY_START, { id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: startAssemblyDay: ', result)
    //     return result.data
    // }
    //
    // // __ Стоп СЗ
    // const finishAssemblyDay = async (id: number) => {
    //     const response = await jwtPatch_(URL_ASSEMBLY_DAY_FINISH, { id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: finishAssemblyDay: ', result)
    //     return result.data
    // }
    //
    // // __ Получение маячка готовности дня с СЗ к добавлению новых СЗ
    // const readyGetAssemblyDay = async (date: string, change: number = 1) => {
    //     const response = await jwtGet(`${URL_ASSEMBLY_DAY_READY_GET}/${date}/${change}`)
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: readyGetAssemblyDay: ', result)
    //     return result.data
    // }
    //
    // // __ Установки маяка готовности к добавлению новых СЗ
    // const readySetAssemblyDay = async (id: number) => {
    //     const response = await jwtPatch_(URL_ASSEMBLY_DAY_READY_SET, { id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: readySetAssemblyDay: ', result)
    //     return result.data
    // }
    //
    // // __ Снятие маяка готовности к добавлению новых СЗ
    // const readyUnsetAssemblyDay = async (id: number) => {
    //     const response = await jwtPatch_(URL_ASSEMBLY_DAY_READY_UNSET, { id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: readyUnsetAssemblyDay: ', result)
    //     return result.data
    // }
    //
    // // __ Устанавливаем новые Производственные Линии
    // const taskLinesManufLineSet = async (data: IAssemblyLineSetData[]) => {
    //     console.log('data: ', data)
    //
    //     const response = await jwtPost(URL_ASSEMBLY_TASK_LINES_TABLE_SET, { data })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: taskLinesManufLineSet: ', result)
    //     return result.data
    // }
    //
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // // !!! ---             Персонал                        !!!
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //
    // // __ Получение активных рабочих
    // const getActiveWorkers = async () => {
    //     if (globalWorkers.value.length) {
    //         return globalWorkers
    //     }
    //
    //     const response = await jwtGet(URL_ASSEMBLY_DAY_WORKERS_ACTIVE)
    //     const result   = await response
    //
    //     // __ кэшируем
    //     globalWorkers.value = result.data
    //         .filter((w: IAssemblyDayWorker) => w.id !== 0)
    //         .sort((a: IAssemblyDayWorker, b: IAssemblyDayWorker) => a.surname.localeCompare(b.surname))
    //
    //
    //     if (DEBUG) console.log('AssemblyStore: getActiveWorkers: ', result)
    //     return result.data
    // }
    //
    // // __ Добавление Рабочего в Производственный день
    // const addWorkerToAssemblyDay = async (day_id: number | null = null, worker_id: number | null = null) => {
    //     if (!day_id || !worker_id) {
    //         return
    //     }
    //     const response = await jwtPost(URL_ASSEMBLY_DAY_WORKER_ADD, { day_id, worker_id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: addWorkerToAssemblyDay: ', result)
    //     return result.data
    // }
    //
    // // __ Добавление Группы Рабочих в Производственный день
    // const addWorkersToAssemblyDay = async (day_id: number | null = null, worker_ids: number[] | null = null) => {
    //     if (!day_id || !worker_ids) {
    //         return
    //     }
    //     const response = await jwtPost(URL_ASSEMBLY_DAY_WORKER_GROUP_ADD, { day_id, worker_ids })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: addWorkersGroupToAssemblyDay: ', result)
    //     return result.data
    // }
    //
    // // __ Удаление Рабочего из Производственного дня
    // const removeWorkerFromAssemblyDay = async (day_id: number, worker_id: number) => {
    //     const response = await jwtPost(URL_ASSEMBLY_DAY_WORKER_REMOVE, { day_id, worker_id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: removeWorkerToAssemblyDay: ', result)
    //     return result.data
    // }
    //
    // // __ Добавление Ответственного в Производственный день
    // const addResponsibleToAssemblyDay = async (day_id: number, worker_id: number) => {
    //     const response = await jwtPatch_(URL_ASSEMBLY_DAY_RESPONSIBLE_ADD, { day_id, worker_id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: addResponsibleToAssemblyDay: ', result)
    //     return result.data
    // }
    //
    // // __ Удаление Ответственного из Производственного дня
    // const removeResponsibleFromAssemblyDay = async (day_id: number, worker_id: number) => {
    //     const response = await jwtPatch_(URL_ASSEMBLY_DAY_RESPONSIBLE_REMOVE, { day_id, worker_id })
    //     const result   = await response
    //     if (DEBUG) console.log('AssemblyStore: removeResponsibleFromAssemblyDay: ', result)
    //     return result.data
    // }
    //
    //
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // // !!! ---               Assembly Lines                   !!!
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //
    // const setAssemblyTaskLinesDone = async (assemblyTaskLinesIds: number[]) => {
    //     if (!assemblyTaskLinesIds.length) {
    //         return []
    //     }
    //     const result = await jwtPost(URL_ASSEMBLY_TASK_LINE_DONE, { ids: assemblyTaskLinesIds })
    //     if (DEBUG) console.log('AssemblyStore: setAssemblyTaskLinesDone: ', result)
    //     return result.data
    // }
    //
    // // __ Устанавливаем "Не Выполнено" на AssemblyTaskLines
    // const setAssemblyTaskLinesFalse = async (assemblyTaskLinesIds: number[], falseReason: string | null = null) => {
    //     if (!assemblyTaskLinesIds.length && !falseReason) {
    //         return []
    //     }
    //     const result = await jwtPost(URL_ASSEMBLY_TASK_LINE_FALSE, { ids: assemblyTaskLinesIds, reason: falseReason })
    //     if (DEBUG) console.log('AssemblyStore: setAssemblyTaskLinesFalse: ', result)
    //     return result.data
    // }
    //
    // // __ Сбрасываем статусы на AssemblyTaskLines
    // const setAssemblyTaskLinesReset = async (assemblyTaskLinesIds: number[]) => {
    //     if (!assemblyTaskLinesIds.length) {
    //         return []
    //     }
    //     const result = await jwtPost(URL_ASSEMBLY_TASK_LINE_RESET, { ids: assemblyTaskLinesIds })
    //     if (DEBUG) console.log('AssemblyStore: setAssemblyTaskLinesReset: ', result)
    //     return result.data
    // }
    //
    // // __ Устанавливаем комментарий для Строки СЗ
    // const setAssemblyTaskLineDescription = async (assemblyTaskLinesId: number, description: string) => {
    //     if (!assemblyTaskLinesId) {
    //         return null
    //     }
    //     const result = await jwtPost(URL_ASSEMBLY_TASK_LINE_DESCRIPTION, { id: assemblyTaskLinesId, description })
    //     if (DEBUG) console.log('AssemblyStore: setAssemblyTaskLineDescription: ', result)
    //     return result.data
    // }
    //
    // // __ Разделение линий СЗ при выполнении СЗ
    // const divideLineInAssemblyTaskPending = async (assemblyTask: IAssemblyTask, period: IPeriod | null = null) => {
    //
    //     const findTask = globalAssemblyTasks.value.find((task: IAssemblyTask) => task.id === assemblyTask.id)
    //     if (!findTask) {
    //         return
    //     }
    //     console.log('findTask: ', findTask)
    //
    //     repositionAssemblyTaskLines(findTask)
    //     // const result = await saveChanges()
    //     return await saveChanges(globalAssemblyTasks.value, globalAssemblyTasksCopy, period)
    //     // return saveChanges(globalAssemblyTasksPending.value, globalAssemblyTasksPendingCopy)
    // }
    //
    //
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // // !!! ---          Время Переналадки                  !!!
    // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //
    //
    // // __ Получаем время переналадки Блоков
    // const getAssemblyCollectionsTuningTime = async () => {
    //     const result = await jwtGet(URL_ASSEMBLY_COLLECTIONS_TUNING_TIME)
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyCollectionsTuningTime: ', result)
    //     return result.data
    // }
    //
    // // __ Получаем время переналадки Блоков для Группы Коллекции Блоков
    // const getAssemblyCollectionsTuningTimeList = async (collectionsList: number[] | null = null) => {
    //     if (!collectionsList || collectionsList.length === 0) {
    //         return []
    //     }
    //
    //     const result = await jwtGet(URL_ASSEMBLY_COLLECTIONS_TUNING_TIME_LIST, { ids: collectionsList })
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyCollectionsTuningTimeList: ', result)
    //     return result.data
    // }
    //
    // // __ Сохраняем время переналадки Блоков
    // const setAssemblyPicturesTuningTime = async (from: number, to: number, time: number) => {
    //     const result = await jwtPost(URL_ASSEMBLY_COLLECTIONS_TUNING_TIME, { from, to, time })
    //     console.log('AssemblyStore: getAssemblyCollectionsTuningTime: ', result)
    //     return result.data
    // }
    //
    // // __ Удаляем время переналадки Блоков
    // const deleteAssemblyPicturesTuningTime = async (from: number, to: number) => {
    //     const result = await jwtDelete(URL_ASSEMBLY_COLLECTIONS_TUNING_TIME, { from, to })
    //     console.log('AssemblyStore: deleteAssemblyPicturesTuningTime: ', result)
    //     return result.data
    // }
    //
    // // __ Получаем оптимизированное время переналадки Блоков для Группы Коллекции Блоков
    // const getAssemblyCollectionsTuningTimeOptimized = async (collectionsList: number[] | null = null, startCollection: number = 0) => {
    //     if (!collectionsList || collectionsList.length === 0) {
    //         return []
    //     }
    //
    //     const result = await jwtGet(URL_ASSEMBLY_COLLECTIONS_TUNING_TIME_OPTIMIZED, { ids: collectionsList, start: startCollection })
    //     if (DEBUG) console.log('AssemblyStore: getAssemblyCollectionsTuningTimeOptimized: ', result)
    //     return result.data
    // }
    //
    //
    // // // __ Получаем время переналадки Блоков между двумя рисунками
    // // const getAssemblyPicturesBetweenTuningTime = async (from, to) => {
    // //     const result = await jwtGet(`${URL_FABRIC_PICTURE_TUNING_TIME}/${from}/${to}`)
    // //     console.log('store: getAssemblyPicturesBetweenTuningTime: ', result)
    // //     return result.data
    // // }
    // //
    // //
    // // // __ Получаем время переналадки Блоков между двумя ПС
    // // const getAssemblyBetweenTuningTime = async (from, to) => {
    // //     const result = await jwtGet(`${URL_FABRICS_BETWEEN_TUNING_TIME}/${from}/${to}`)
    // //     console.log('store: getAssemblyBetweenTuningTime: ', result)
    // //     return result.data
    // // }
    // //
    // // // __ Получаем последний рисунок с предыдущего СЗ
    // // const getLastRoll = async (taskDate, machineID) => {
    // //     const result = await jwtGet(`${URL_FABRIC_TASKS_EXECUTE_ROLL_LAST}/${taskDate}/${machineID}`)
    // //     console.log('store: getLastRoll: ', result)
    // //     return result.data
    // // }
    //
    //


    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---    Группы Моделей для Сортировки            !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    // __ Получаем Группы Моделей для Сортировки
    const getModelManufactureGroups = async () => {
        const response = await jwtGet(URL_ASSEMBLY_MODEL_MANUFACTURE_GROUPS)
        const result   = await response
        if (DEBUG) console.log('AssemblyStore: getModelManufacturerGroups: ', result)
        return result.data
    }

    // __ Удаляем Группу Моделей для Сортировки
    const deleteModelManufactureGroup = async (id: number) => {
        const response = await jwtDelete(URL_ASSEMBLY_MODEL_MANUFACTURE_GROUPS, { id })
        const result   = await response
        if (DEBUG) console.log('AssemblyStore: deleteModelManufactureGroup: ', result)
        return result.data
    }


    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---                 Тесты                       !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    const test = async () => {
        const response = await jwtGet(URL_ASSEMBLY_TEST)
        const result   = await response
        if (DEBUG) console.log('AssemblyStore: test: ', result)
        return result
    }


    return {
        // globalAssemblyTasks,
        // globalAssemblyTaskStatuses,
        // globalRenderPeriod,
        // globalAssemblyTaskTimesShow,
        // globalAssemblyTaskFullDaysShow,
        // globalAssemblyTaskAssemblyInSquare,
        // globalAssemblyTaskActiveOrderId,
        // globalAssemblyTaskOrderTypeColor,
        // globalManageTaskCardActiveAssemblyLine,
        //
        // globalAssemblyTasksPending,
        //
        // globalWorkers,
        //
        // // globalAssemblyTaskPrintData,
        //
        // addAssemblyTaskToGlobal,
        // applyAssemblyTaskComment,
        // applyChanges,
        // saveChanges,
        // setAssemblyTasksLines,
        // applyMergeTasks,
        // applyMergeTasksGroups,
        // setGlobalArrayChangeManufLines,
        //
        // setAssemblyTaskLinesDone,
        // setAssemblyTaskLinesFalse,
        // setAssemblyTaskLinesReset,
        // setAssemblyTaskLineDescription,
        // divideLineInAssemblyTaskPending,
        //
        // taskLinesManufLineSet,
        //
        // getAssemblyDayByPeriod,
        // getAssemblyDayByDateAndChange,
        // setAssemblyDayComment,
        // modifyChange,
        // readyGetAssemblyDay,
        // getAssemblyDaysByDates,
        //
        // getAssemblyTasks,
        // getAssemblyTasksByOrderId,
        // deleteAssemblyTasksByOrderId,
        // addAssemblyTasksByOrderId,
        // calcAssemblyTasksCutByOrderId,
        // getAssemblyTasksByStatusBeforeDateAndChange,
        // getAssemblyTasksByStatusOnDate,
        // checkAssemblyTasksByStatusOnDate,
        // setAssemblyTaskComment,
        // setAssemblyTaskActionAt,
        // readySetAssemblyDay,
        // readyUnsetAssemblyDay,
        // startAssemblyDay,
        // finishAssemblyDay,
        //
        // getAssemblyCollections,
        // getAssemblyCollectionById,
        // createAssemblyCollection,
        // updateAssemblyCollection,
        //
        // getAssemblyCollectionsTuningTime,
        // getAssemblyCollectionsTuningTimeList,
        // setAssemblyPicturesTuningTime,
        // deleteAssemblyPicturesTuningTime,
        // getAssemblyCollectionsTuningTimeOptimized,
        //
        // getAssemblyById,
        // createAssembly,
        // updateAssembly,
        //
        // getAssemblyTasksByStatus,
        // getAssemblyTasksByStatusAndPeriod,
        //
        // getAssemblyTaskStatuses,
        // patchAssemblyTaskStatusColor,
        // setAssemblyTasksStatuses,
        //
        // getActiveWorkers,
        // addWorkerToAssemblyDay,
        // addWorkersToAssemblyDay,
        // removeWorkerFromAssemblyDay,
        // addResponsibleToAssemblyDay,
        // removeResponsibleFromAssemblyDay,

        getModelManufactureGroups,
        deleteModelManufactureGroup,

        test,
    }
})
