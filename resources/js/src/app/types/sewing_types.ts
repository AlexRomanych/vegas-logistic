// Info Тут все типы для Пошива

// line -------------------------------------------------------------------
// line ------------------------- План Загрузок----------------------------
// line -------------------------------------------------------------------


import type { IDims, IPlanMatrixDayItem } from '@/types/index.ts'
import { AUTO, AVERAGE, SEWING_MACHINES, SOLID_HARD, SOLID_LITE, UNDEFINED, UNIVERSAL } from '@/app/constants/sewing.ts'

export type IDay = ISewingTask & IPlanMatrixDayItem

export interface ISewingTask extends IPlanMatrixDayItem {
    id: number
    id_ref: number                                  // __ референсный id (при разбиении нового СЗ, id_ref === id, то есть основаниие старого СЗ)
    action_at: string
    active: boolean
    change: number
    position: number
    comment: string | null,
    order: ISewingTaskOrder
    sewing_lines: ISewingTaskLine[]
    statuses: ISewingTaskStatus[]
    current_status: ISewingTaskStatus
}

// __ Связь с Содержимым СЗ
export interface ISewingTaskLine {
    id: number
    id_ref: number                                  // __ референсный id (при разбиении строки СЗ, id_ref === id, то есть основаниие старого СЗ)
    amount: number                                  // __ Общее количество в заявке
    amount_avg: null | ISewingTaskLineAmountAvg     // __ Количество для средней модели по статистике
    time: ISewingTaskLineTime                       // __ Трудозатраты
    element_type: {
        is_average: boolean                         // __ Флаг для расчетной модели
        is_base: boolean                            // __ Флаг для базы
        is_cover: boolean                           // __ Флаг для чехла
        type: ISewingTaskElementTypes               // __ Тип элемента в строковом представлении ('base' | 'cover' | 'average' | 'unknown')
    }
    created_at: string | null
    false_reason: string | null
    finished_at: string | null
    finished_by: number | null                      // __ Тут в будущем добавим объект пользователя (Worker)
    position: number
    order_line: ISewingTaskOrderLine
}

// __ Статус Движения (выполнения) Заявки
export interface ISewingTaskStatus {
    id: number
    color: string
    name: string
    pivot: ISewingTaskStatusPivot
}

// __ Дополнительная инфа
export interface ISewingTaskStatusPivot {
    created_at: string | null
    duration: number | null
    finished_at: string | null
    set_at: string | null
    started_at: string | null
}

// __ Связь с Заявкой
export interface ISewingTaskOrder {
    id: number
    order_no_num: number
    order_no_origin: string
    order_no_str: string
    load_at: string | null
    comment_1c: string | null
    client: ISewingTaskOrderClient
    order_type: ISewingTaskOrderType
}

// __ Связь со Строкой Основной Заявки (Связь с Содержимым Заявки)
export interface ISewingTaskOrderLine {
    id: number
    size: string
    amount: number
    textile: string | null
    composition: string | null
    describe_1: string | null
    describe_2: string | null
    describe_3: string | null
    dims: IDims
    model: {

        // __ Основная модель, та, что в Основной Заявке
        main: ISewingTaskModel

        // __ Базовая модель, если main === матрас, то base === null,
        // __ если main === чехол, то base === модель матраса, к которой относится чехол
        base: null | ISewingTaskModel

        // __ Базовая модель, если main === матрас, то cover === чехол матраса базовой модели,
        // __ если main === чехол, то cover === модель чехла или null
        cover: null | ISewingTaskModel
    }
}

// __ Связь Основной Заявки с Клиентом
export interface ISewingTaskOrderClient {
    id: number
    name: string
    add_name: string
    short_name: string
}

// __ Связь Основной Заявки с Типом Заявки
export interface ISewingTaskOrderType {
    id: number
    display_name: string
    color: string
}


// __ Описание модели
export interface ISewingTaskModel {
    code_1c: string
    name: string
    name_report: string
    base_height: number
    cover_height: number
    kant: string | null
    tkch: string | null
    base_composition: string | null
    cover_type: string | null
    textile: string | null
    textile_composition: string | null
    is_auto: boolean
    is_solid: boolean
    is_solid_hard: boolean
    is_solid_lite: boolean
    is_undefined: boolean
    is_universal: boolean
    is_average: boolean
    machine_type: ISewingMachineKeys
    machine_type_ref: ISewingMachineKeys        // __ референсный тип машины (оригинальный)
}

// __ Типы ШМ
export type ISewingMachineKeys =
    typeof UNIVERSAL |
    typeof AUTO |
    typeof SOLID_HARD |
    typeof SOLID_LITE |
    typeof UNDEFINED |
    typeof AVERAGE

// __ Ключи для Типов ШМ
export type ISewingMachineTimesKeys = `time_${ISewingMachineKeys}`


// __ Тип для Средних значений для Average модели
export type ISewingTaskLineAmountAvg = Record<ISewingMachineKeys, number>

// __ Трудозатраты
export type ISewingTaskLineTime = Record<ISewingMachineTimesKeys, number>


// __ Типы моделей
export type ISewingTaskElementTypes = 'base' | 'cover' | 'average' | 'unknown'


// --- --------------------------------------------------------------
// --- Типы для работы с сущностями Пошива
// --- --------------------------------------------------------------
export interface ISewingTaskStatusEntity {
    id: number
    name: string
    color: string
    position: number
    description?: string | null
    active?: boolean
    status?: number
    comment?: string | null
    note?: string | null
    meta?: string | null
    created_at?: string | null
    updated_at?: string | null
}

// --- ------------------------------------------------------------
// __ Структура для расчета Трудозатрат и Количества по ШМ для рендеринга в шаблоне
// __ Сначала определим тип структуры данных
export type IStatItem = {
    time: number
    amount: number
};
// __ Создаем тип для объекта amount, где ключами будут только ключи из SEWING_MACHINES
export type IAmountAndTime = Record<keyof typeof SEWING_MACHINES, IStatItem>
// --- ------------------------------------------------------------


// --- ------------------------------------------------------------
// __ Типы панелей меню в карточке Заказа в Пошиве в календаре
export type ISewingLinesPanel = 'left' | 'right'
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для сортировки в Карточке Заказа в Пошиве
export type ISewingTaskCardSort = 'none' | 'asc' | 'desc'
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для разницы между матрицами отображения календаря в Пошиве
export interface IRenderMatrixDiff {
    taskId: number
    dayFromOffset?: number
    dayToOffset?: number
    oldTaskPosition?: number
    newTaskPosition?: number
    isPositionChanged?: boolean
    isMoved?: boolean
    lineDiffs?: IRenderMatrixLineDiffs[]

    // __ Это для создания новой заявки (Не используется в данный момент)
    type?: null | 'NEW_TASK'
    newPosition?: number | null

    // __ Не используется в данный момент
    areLinesChanged?: boolean
}

export interface IRenderMatrixLineDiffs {
    lineId: number
    type: IDiffsType
    oldPosition?: number
    newPosition?: number
    oldAmount?: number
    newAmount?: number
    isPositionChanged?: boolean
    isAmountChanged?: boolean
}

// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для разницы между массивами СЗ Пошиве
export interface ISewingTaskArrayDiff {
    taskId: number
    taskIdRef?: number
    type?: IDiffsType
    // current?: ISewingTask
    taskChanges?: {
        action_at?: {
                        old: string | null
                        new: string
                    } | null
        position?: {
                       old: number | null
                       new: number
                   } | null
    }
    lineChanges?: ISewingTaskArrayLineDiffs[]
}

export interface ISewingTaskArrayLineDiffs {
    lineId: number
    lineIdRef?: number
    type: IDiffsType
    amount?: {
                 old: number | null
                 new: number
             } | null
    position?: {
                   old: number | null
                   new: number
               } | null
}

// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Типы для разницы для каждой записи в матрице Пошива
export type IDiffsType = 'UPDATED' | 'ADDED' | 'DELETED'
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для Типовой операции Пошива
export interface ISewingOperation {
    active: boolean
    description: string | null
    id: number
    machine: string
    name: string
    time: number
    type: ICalcMode
    color: string
}

export type ICalcMode = 'dynamic' | 'static'
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для Схемы Типовых операций Пошива
export interface ISewingOperationSchema {
    id: number
    active: boolean
    name: string
    description: string | null
    operations: ISewingOperationItem[]
}

// __ Тип для Типовой операции в Схеме
export interface ISewingOperationItem {
    id: number
    pivot: ISewingOperationItemPivot
}

export interface ISewingOperationItemPivot {
    amount: number | null
    condition: string | null
    position: number | null
    ratio: number | null
}

// __ Объект для обновления Схемы Типовых операций на сервере
export interface ISewingOperationUpdateObject {
    operation_id: number
    target_id: number | string      // __ id Схемы (number) или CODE_1C Модели (string)
    pivot: null | ISewingOperationItemPivot
}

// --- ---------------------- Модели ------------------------------
// __ Тип для Объекта для отображения Списка моделей в Выборе Схемы ТО или просто ТО
export interface ISewingOperationModelsCollection {
    collection: string
    items: ISewingOperationModel[]
    collapsed?: boolean
}

// __ Тип для модели в объекте для отображения Списка моделей
export interface ISewingOperationModel {
    code_1c: string
    name: string
    name_report: string
    sewing_schema_id: number
    operations: ISewingOperationItem[]
}

// --- ------------------------------------------------------------

// __ Тип для обновления статуса заявки
export type ISewingTaskStatusesSet = { task: number, status: number }


// --- ----------- Производственный день ---------------------------
// __ Тип для Производственного Дня
export type ISewingDay = {
    id: number
    action_at: string
    action_at_str: string
    change: number
    description: string | null
    comment: string | null
    start_at: string | null
    paused_at: string | null
    resume_at: string | null
    finish_at: string | null
    duration: number
    responsible :ISewingDayWorker
    workers: ISewingDayWorker[]
}

export type ISewingDayWorker = {
    id: number
    surname: string
    name: string
    patronymic: string
    pivot?: ISewingDayWorkerPivot
}

export type ISewingDayWorkerPivot = {
    id: number
    working_time: number | null
}
