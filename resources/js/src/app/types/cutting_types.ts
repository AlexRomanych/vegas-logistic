// Info Тут все типы для Раскроя

// line -------------------------------------------------------------------
// line ------------------------- План Загрузок----------------------------
// line -------------------------------------------------------------------
import type { ICalcMode, IColorTypes, IDiffsType, IDims, IPlanMatrixDayItem, ITextileMachineKeys } from '@/types/index.ts'
import {
    CUTTING_TASK_STATUS_CREATED,
    CUTTING_TASK_STATUS_ROLLING,
    CUTTING_TASK_STATUS_PENDING,
    CUTTING_TASK_STATUS_RUNNING,
    CUTTING_TASK_STATUS_DONE,
    DETAIL_PANEL, DETAIL_SIDE,
    TABLE_1, TABLE_2, TABLE_3, TABLE_UNDEFINED,

} from '@/app/constants/cutting.ts'

// export type IDay = ICuttingTask & IPlanMatrixDayItem

export interface ICuttingTask extends IPlanMatrixDayItem {
    id: number
    id_ref: number                                  // __ референсный id (при разбиении нового СЗ, id_ref === id, то есть основаниие старого СЗ)
    action_at: string
    active: boolean
    change: number
    position: number
    comment: string | null,
    order: ICuttingTaskOrder
    cutting_lines: ICuttingTaskLine[]
    statuses: ICuttingTaskStatus[]
    current_status: ICuttingTaskStatus

    collapsed?: boolean
}

// __ Связь с Содержимым СЗ
export interface ICuttingTaskLine {
    id: number
    id_ref: number                                  // __ референсный id (при разбиении строки СЗ, id_ref === id, то есть основаниие старого СЗ)
    amount: number                                  // __ Общее количество в заявке
    amount_avg: null | ICuttingTaskLineAmountAvg     // __ Количество для средней модели по статистике
    // time: ICuttingTaskLineTime                       // __ Трудозатраты
    time: number                                    // __ Трудозатраты
    element_type: {
        is_average: boolean                         // __ Флаг для расчетной модели
        is_base: boolean                            // __ Флаг для базы
        is_cover: boolean                           // __ Флаг для чехла
        type: ICuttingTaskElementTypes               // __ Тип элемента в строковом представлении ('base' | 'cover' | 'average' | 'unknown')
    }
    created_at: string | null
    false_reason: string | null
    finished_at: string | null
    false_at: string | null
    finished_by: number | null                      // __ Тут в будущем добавим объект пользователя (Worker)
    position: number

    order_meta?: string                             // __ Номер заявки

    table: ICuttingTableKeys
    order_line: ICuttingTaskOrderLine
    details?: ICuttingTaskLine[]

    is_panel: boolean
    is_side: boolean
    has_side: boolean

    completed?: boolean                             // __ Флаг для SFC выполнения СЗ
    groupAttr?: string                              // __ Атрибут для группировки строк
}

// __ Статус Движения (выполнения) Заявки
export interface ICuttingTaskStatus {
    id: number
    color: string
    name: string
    pivot: ICuttingTaskStatusPivot
}

// __ Дополнительная инфа
export interface ICuttingTaskStatusPivot {
    created_at: string | null
    duration: number | null
    finished_at: string | null
    set_at: string | null
    started_at: string | null
}

// __ Связь с Заявкой
export interface ICuttingTaskOrder {
    id: number
    order_no_num: number
    order_no_origin: string
    order_no_str: string
    load_at: string | null
    comment_1c: string | null
    client: ICuttingTaskOrderClient
    order_type: ICuttingTaskOrderType
}

// __ Связь со Строкой Основной Заявки (Связь с Содержимым Заявки)
export interface ICuttingTaskOrderLine {
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
        main: ICuttingTaskModel

        // __ Базовая модель, если main === матрас, то base === null,
        // __ если main === чехол, то base === модель матраса, к которой относится чехол
        base: null | ICuttingTaskModel

        // __ Базовая модель, если main === матрас, то cover === чехол матраса базовой модели,
        // __ если main === чехол, то cover === модель чехла или null
        cover: null | ICuttingTaskModel
    }
}

// __ Связь Основной Заявки с Клиентом
export interface ICuttingTaskOrderClient {
    id: number
    name: string
    add_name: string
    short_name: string
}

// __ Связь Основной Заявки с Типом Заявки
export interface ICuttingTaskOrderType {
    id: number
    display_name: string
    color: string
}


// __ Описание модели
export interface ICuttingTaskModel {
    code_1c: string
    name: string
    name_report: string
    base_height: number
    cover_height: number
    kant: string | null
    tkch: string | null
    kdch: string | null
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
    machine_type: ICuttingMachineKeys
    machine_type_ref: ICuttingMachineKeys        // __ референсный тип машины (оригинальный)
}

// __ Типы ШМ
export type ICuttingMachineKeys = ITextileMachineKeys

// __ Ключи для Типов ШМ
export type ICuttingMachineTimesKeys = ICuttingMachineKeys
// export type ICuttingMachineTimesKeys = `time_${ICuttingMachineKeys}`


// __ Тип для Средних значений для Average модели
export type ICuttingTaskLineAmountAvg = Record<ICuttingMachineKeys, number>

// __ Трудозатраты
export type ICuttingTaskLineTime = Record<ICuttingMachineTimesKeys, number>

// __ Типы моделей
export type ICuttingTaskElementTypes = 'base' | 'cover' | 'average' | 'unknown'


export type ICuttingTableKeys =
    typeof TABLE_1 |
    typeof TABLE_2 |
    typeof TABLE_3 |
    typeof TABLE_UNDEFINED

// --- --------------------------------------------------------------
// --- Типы для работы со статусами СЗ
// --- --------------------------------------------------------------

export type ICuttingTaskStatusKeys =
    typeof CUTTING_TASK_STATUS_CREATED |
    typeof CUTTING_TASK_STATUS_ROLLING |
    typeof CUTTING_TASK_STATUS_PENDING |
    typeof CUTTING_TASK_STATUS_RUNNING |
    typeof CUTTING_TASK_STATUS_DONE


export interface ICuttingTaskStatusItem {
    ID: number,
    WORD: string
    TITLE: string
    TYPE: IColorTypes
}

// --- --------------------------------------------------------------
// --- Типы для работы с сущностями Пошива
// --- --------------------------------------------------------------
export interface ICuttingTaskStatusEntity {
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
// __ Типы панелей меню в карточке Заказа в Пошиве в календаре
export type ICuttingLinesPanel = 'left' | 'right'
export type ICuttingTablePanel = typeof TABLE_1 |  typeof TABLE_2 |  typeof TABLE_3 | typeof TABLE_UNDEFINED
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для сортировки в Карточке Заказа в Пошиве
export type ICuttingTaskCardSort = 'none' | 'asc' | 'desc'
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для разницы между массивами СЗ Пошиве
export interface ICuttingTaskArrayDiff {
    taskId: number
    taskIdRef?: number
    type?: IDiffsType
    // current?: ICuttingTask
    taskChanges?: {
        action_at?: {
            old: string | null
            new: string
        } | null
        position?: {
            old: number | null
            new: number
        } | null
        status?: {
            old: number | null
            new: number
        } | null
    }
    lineChanges?: ICuttingTaskArrayLineDiffs[]
}

export interface ICuttingTaskArrayLineDiffs {
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
// export type IDiffsType = 'UPDATED' | 'ADDED' | 'DELETED'
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для Типовой операции Пошива
export interface ICuttingOperation {
    id: number
    name: string
    active: boolean
    description: string | null
    machine: string | null
    time: number
    color: string
    type: ICalcMode
    cover_type: string | null
    table: string | null
    detail: ICuttingOperationDetailTypes | null
}

export type ICuttingOperationDetailTypes = typeof DETAIL_PANEL | typeof DETAIL_SIDE

// export type ICalcMode = 'dynamic' | 'static'
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для Схемы Типовых операций Пошива
export interface ICuttingOperationSchema {
    id: number
    active: boolean
    name: string
    description: string | null
    operations: ICuttingOperationItem[]
}

// __ Тип для Типовой операции в Схеме
export interface ICuttingOperationItem {
    id: number
    pivot: ICuttingOperationItemPivot
}

export interface ICuttingOperationItemPivot {
    amount: number | null
    condition: string | null
    position: number | null
    ratio: number | null
}

// __ Объект для обновления Схемы Типовых операций на сервере
export interface ICuttingOperationUpdateObject {
    operation_id: number
    target_id: number | string      // __ id Схемы (number) или CODE_1C Модели (string)
    pivot: null | ICuttingOperationItemPivot
}

// --- ---------------------- Модели ------------------------------
// __ Тип для Объекта для отображения Списка моделей в Выборе Схемы ТО или просто ТО
export interface ICuttingOperationModelsCollection {
    collection: string
    items: ICuttingOperationModel[]
    collapsed?: boolean
}

// __ Тип для модели в объекте для отображения Списка моделей
export interface ICuttingOperationModel {
    code_1c: string
    name: string
    name_report: string
    cutting_schema_id: number
    operations: ICuttingOperationItem[]
}

// --- ------------------------------------------------------------

// __ Тип для обновления статуса заявки
export type ICuttingTaskStatusesSet = { task: number, status: number }


// --- ----------- Производственный день ---------------------------
// __ Тип для Производственного Дня
export type ICuttingDay = {
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
    cutting_tasks: ICuttingTask[]
    responsible: ICuttingDayWorker | null
    workers: ICuttingDayWorker[]

    ready: boolean  // __ Готовность к добавлению новых СЗ

    collapsed?: boolean
    personal_collapsed?: boolean
    tasks_collapsed?: boolean
    common_collapsed?: boolean
}

export type ICuttingDayWorker = {
    id: number
    surname: string
    name: string
    patronymic: string
    pivot?: ICuttingDayWorkerPivot
}

export type ICuttingDayWorkerPivot = {
    id: number
    working_time: number | null
}


// --- ----------- Статистика выполнения СЗ (прогресс) ---------------------------
// __ Вспомогательный Тип для вычисления статистики по СЗ
export interface ICuttingTaskExecuteStatistics {
    amount: ICuttingTaskExecuteStatisticsItem,
    time: ICuttingTaskExecuteStatisticsItem,
}

export interface ICuttingTaskExecuteStatisticsItem {
    finished: number
    unfinished: number
    total: number
}


// --- -------------------------------------------------------------------
// --- --------------- Тип для группировки СЗ по ШМ ----------------------
// --- -------------------------------------------------------------------
export type ICuttingTaskLinesGroupNames = 'АШМ' | 'УШМ' | 'Н/Д'
export type ICuttingTaskLinesSubGroupNames =
    'Автоматы'
    | 'Глухие, автоматическое чехление'
    | 'Глухие'
    | 'Глухие сложные'
    | 'УШМ + окантователь'
    | 'УШМ'
    | 'Без ТКЧ'

// __ Для набора правил Группировки СЗ по ШМ
export interface ICuttingTaskLinesGroup {
    GROUP_NAME: ICuttingTaskLinesGroupNames
    GROUP_TYPE: IColorTypes
    GROUP_COLOR?: string | null,
    SUBGROUPS: {
        SUBGROUP_NAME: ICuttingTaskLinesSubGroupNames
        SUBGROUP_TYPE: IColorTypes
        SUBGROUP_COLOR?: string | null,
        SUBGROUP_TCHK: string[]
    }[]
}

// __ Для отображения СЗ по ШМ
export interface ICuttingTaskLinesGroupData {
    groupName: ICuttingTaskLinesGroupNames
    groupType: IColorTypes
    hasData: boolean
    time: {
        total: number
        done: number
        incomplete: number
    }
    amount: {
        total: number
        done: number
        incomplete: number
    }
    subgroups: ICuttingTaskLinesSubgroup[]
    // subgroups: {
    //     subgroupName:ICuttingTaskLinesSubGroupNames
    //     subgroupOrderTitle: string | null,  // Название заявки (для отображения), к которой относится СЗ
    //     subgroupType: IColorTypes
    //     hasData: boolean,
    //     lines: ICuttingTaskLine[]
    // }[]
    collapsed?: boolean
}


export interface ICuttingTaskLinesSubgroup {
    subgroupName: ICuttingTaskLinesSubGroupNames
    subgroupOrderTitle: string | null  // Название заявки (для отображения), к которой относится СЗ
    subgroupType: IColorTypes
    hasData: boolean
    time: {
        total: number
        done: number
        incomplete: number
    }
    amount: {
        total: number
        done: number
        incomplete: number
    }
    lines: ICuttingTaskLine[]
}

// __ Тип для сохранения (изменения) стола для записи СЗ Раскроя
export type ICuttingLineTableSetData = {id: number, table: ICuttingTableKeys}
