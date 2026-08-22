// __ Все, что касается Сборки


import type { IPlanMatrixDayItem } from '@/types/plan_types.ts'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import {
    ASSEMBLY_LINE_LAMIT, ASSEMBLY_LINE_TABLE, ASSEMBLY_LINE_UNDEFINED,
    ASSEMBLY_TASK_SECTOR_COCONUT,
    ASSEMBLY_TASK_SECTOR_FOAM_LAYER,
    ASSEMBLY_TASK_SECTOR_FOAM_SIDE,
    ASSEMBLY_TASK_SECTOR_LAMIT,
    ASSEMBLY_TASK_SECTOR_LATEX,
    ASSEMBLY_TASK_SECTOR_LAYER,
    ASSEMBLY_TASK_SECTOR_TABLE,
    ASSEMBLY_TASK_STATUS_CREATED, ASSEMBLY_TASK_STATUS_DONE,
    ASSEMBLY_TASK_STATUS_PENDING,
    ASSEMBLY_TASK_STATUS_ROLLING,
    ASSEMBLY_TASK_STATUS_RUNNING,
    CHANGE_1,
    CHANGE_2
} from '@/app/constants/assembly.ts'
import type { IDiffsType } from '@/types/index.ts'


// --- --------------------------------------------------------------------
// --- ----------- Для рендера Групп Моделей для Сортировки ---------------
// --- --------------------------------------------------------------------
export interface IAssemblyModelManufactureGroup {
    id: number
    name: string
    group_number: number
    active?: boolean
    color?: string
    description?: string | null
}

// --- --------------------------------------------------------------------
// --- -------------------- Сменные Задания (СЗ) --------------------------
// --- --------------------------------------------------------------------

export interface IAssemblyTask extends IPlanMatrixDayItem {
    id: number
    id_ref: number                                  // __ референсный id (при разбиении нового СЗ, id_ref === id, то есть основаниие старого СЗ)
    action_at: string
    active: boolean
    change: IAssemblyTaskChangeKeys
    position: number
    comment: string | null,
    order: IAssemblyTaskOrder
    assembly_lines: IAssemblyTaskLine[]

    statuses: IAssemblyTaskStatus[]
    current_status: IAssemblyTaskStatus

    stats: IAssemblyTaskStats[]

    collapsed?: boolean
}

// __ Статистика по Участкам
export interface IAssemblyTaskStats {
    sector: IAssemblySectorKeys
    total_amount: number
    finished_amount: number
}

// __ Связь с Заявкой
export interface IAssemblyTaskOrder {
    id: number
    order_no_num: number
    order_no_origin: string
    order_no_str: string
    load_at: string | null
    unload_at: string | null
    comment_1c: string | null
    client: IAssemblyTaskOrderClient
    order_type: IAssemblyTaskOrderType

    is_forecast: boolean,
}

// __ Связь Основной Заявки с Клиентом
export interface IAssemblyTaskOrderClient {
    id: number
    name: string
    add_name: string
    short_name: string
}

// __ Связь Основной Заявки с Типом Заявки
export interface IAssemblyTaskOrderType {
    id: number
    display_name: string
    color: string
}

// __ Связь с Содержимым СЗ
export interface IAssemblyTaskLine {
    id: number
    id_ref: number                                  // __ референсный id (при разбиении строки СЗ, id_ref === id, то есть основаниие старого СЗ)
    amount: number                                  // __ Общее количество в заявке
    time: number                                    // __ Трудозатраты
    assembly_line: IAssemblyLineKeys                // __ Линия Сборки

    created_at: string | null
    false_reason: string | null
    finished_at: string | null
    false_at: string | null
    finished_by: number | null                      // __ Тут в будущем добавим объект пользователя (Worker)
    position: number
    productivity: number
    description: string | null

    order_meta?: string                             // __ Номер заявки

    order_line: IAssemblyTaskOrderLine

    sector_lines: IAssemblyTaskLineSector[]

    completed?: boolean                             // __ Флаг для SFC выполнения СЗ
    groupAttr?: string                              // __ Атрибут для группировки строк

    is_average?: boolean
}

// __ Сам элемент расхода
export interface IAssemblyTaskLineSector {
    id: number

    material_code_1c: string
    material_name: string
    sector: IAssemblySectorKeys

    amount: number
    count: number

    detail_dims: {
        width: number
        length: number
        height: number
    }
    dims: {
        width: number
        length: number
        height: number
    }

    expense: number
    rest: number
    total: number

    expense_per_pic: number
    rest_per_pic: number
    total_per_pic: number

    false_at: string | null
    false_history: string[] | null
    false_reason: string | null
    finished_at: string | null
    finished_by: number | null

    time: number
    description: string | null
}


// __ Данные по привязке контекста СЗ Блоков (AssemblyLine) к строкам (OrderLine) в Заявке (Order) в момент создания
export interface IAssemblyTaskOrderLine {
    id: number
    amount: number
    composition: string | null
    describe_1: string | null
    describe_2: string | null
    describe_3: string | null
    dims: {
        height: number
        length: number
        width: number
    }
    size: string
    textile: string
    model: IAssemblyTaskModel
}

// __ Описание модели
export interface IAssemblyTaskModel {
    base_composition: string
    base_height: number
    code_1c: string
    manufacture_group: IAssemblyModelManufactureGroup
    name: string
    name_report: string
    model_type: string
    assembly_line: string
}

// --- ------------------------------------------------------------
// __ Для Линий Сборки
export type IAssemblyLineKeys = typeof ASSEMBLY_LINE_LAMIT | typeof ASSEMBLY_LINE_TABLE | typeof ASSEMBLY_LINE_UNDEFINED

// --- ------------------------------------------------------------
// __ Тип для сохранения (изменения) Линии Сборки для записи СЗ Сборки
export type IAssemblyLineSetData = { id: number, line: IAssemblyLineKeys }

// --- ------------------------------------------------------------
// __ Для смен
export type IAssemblyTaskChangeKeys = typeof CHANGE_1 | typeof CHANGE_2

export interface IAssemblyTaskChange {
    ID: number
    NAME: IAssemblyTaskChangeKeys
    TITLE: string
    TITLE_ROME: string
    ICON: string
    TYPE: IColorTypes
    TIME: string    // __ Время работы
}


// --- --------------------------------------------------------------------
// --- ---------------------- Для Статусов СЗ -----------------------------
// --- --------------------------------------------------------------------

// __ Для разукрашки статусов
export interface IAssemblyTaskStatusEntity {
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

// __ Тип для обновления статуса заявки
export type IAssemblyTaskStatusesSet = { task: number, status: number }

// __ Статус Движения (выполнения) Заявки
export interface IAssemblyTaskStatus {
    id: number
    color: string
    name: string
    pivot: IAssemblyTaskStatusPivot
}

// __ Дополнительная инфа
export interface IAssemblyTaskStatusPivot {
    created_at: string | null
    duration: number | null
    finished_at: string | null
    set_at: string | null
    started_at: string | null
}


// --- --------------------------------------------------------------------
// --- -------------------- Типы Участков (SECTORS)  ----------------------
// --- --------------------------------------------------------------------
export type IAssemblySectorKeys =
    typeof ASSEMBLY_TASK_SECTOR_FOAM_SIDE |
    typeof ASSEMBLY_TASK_SECTOR_FOAM_LAYER |
    typeof ASSEMBLY_TASK_SECTOR_LATEX |
    typeof ASSEMBLY_TASK_SECTOR_LAYER |
    typeof ASSEMBLY_TASK_SECTOR_COCONUT |
    typeof ASSEMBLY_TASK_SECTOR_LAMIT |
    typeof ASSEMBLY_TASK_SECTOR_TABLE

export interface IAssemblySector {
    ID: number
    NAME: IAssemblySectorKeys
    TITLE: string
    LABEL: string[]
    ICON: string
    TYPE: IColorTypes
    SHOW: boolean
}




// --- --------------------------------------------------------------------
// --- ------------------ Типы для разницы состояний  ---------------------
// --- --------------------------------------------------------------------
// __ Тип для разницы между массивами СЗ Пошиве
export interface IAssemblyTaskArrayDiff {
    taskId: number
    taskIdRef?: number
    type?: IDiffsType
    // current?: IAssemblyTask
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
        change: {
            old: string | null,
            new: string
        } | null
    }
    lineChanges?: IAssemblyTaskArrayLineDiffs[]
}

export interface IAssemblyTaskArrayLineDiffs {
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

// --- --------------------------------------------------------------

// --- --------------------------------------------------------------
// --- ------------ Типы для работы со статусами СЗ -----------------
// --- --------------------------------------------------------------

export type IAssemblyTaskStatusKeys =
    typeof ASSEMBLY_TASK_STATUS_CREATED |
    typeof ASSEMBLY_TASK_STATUS_ROLLING |
    typeof ASSEMBLY_TASK_STATUS_PENDING |
    typeof ASSEMBLY_TASK_STATUS_RUNNING |
    typeof ASSEMBLY_TASK_STATUS_DONE

export interface IAssemblyTaskStatusItem {
    ID: number,
    WORD: string
    TITLE: string
    TYPE: IColorTypes
    PRIORITY: number
}


// --- --------------------------------------------------------------
// --- ----- Типы для работы с Трудозатратами и Количеством ---------
// --- --------------------------------------------------------------
export type IStatItemAssembly = {
    time: number
    amount: number
}

// __ Создаем тип для объекта amount, где ключами будут только ключи из ASSEMBLY_LINES
export type IAmountAndTimeAssemblyLines = Record<IAssemblyLineKeys, IStatItemAssembly>

// __ Создаем тип для объекта amount, где ключами будут только ключи из ASSEMBLY_SECTORS
export type IAmountAndTimeAssembly = Record<IAssemblySectorKeys, IStatItemAssembly>


export interface IAssemblyTaskStatusItem {
    ID: number,
    WORD: string
    TITLE: string
    TYPE: IColorTypes
    PRIORITY: number
}




// --- --------------------------------------------------------------
// --- -- Типы для работы со Статистикой выполнения СЗ (прогресс) ---
// --- --------------------------------------------------------------

// __ Вспомогательный Тип для вычисления статистики по СЗ
export interface IAssemblyTaskExecuteStatistics {
    amount: IAssemblyTaskExecuteStatisticsItem,
    time: IAssemblyTaskExecuteStatisticsItem,
}

export interface IAssemblyTaskExecuteStatisticsItem {
    finished: number
    unfinished: number
    total: number
}

export interface IStats {
    id: number
    name: IAssemblySectorKeys
    total: number
    done: number
    percent: number
    color: string
    title: string
}


// --- ------------------------------------------------------------
// __ Типы панелей меню в карточке Заказа в Пошиве в календаре
export type IAssemblyLinesPanel = 'left' | 'right'
export type IAssemblyManufLinesPanel = typeof ASSEMBLY_LINE_LAMIT | typeof ASSEMBLY_LINE_TABLE | typeof ASSEMBLY_LINE_UNDEFINED
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для сортировки в Карточке Заказа в Блоках
export type IAssemblyTaskCardSort = 'none' | 'asc' | 'desc'
// --- ------------------------------------------------------------



export interface IAssemblyManipulateDay {
    action_at: string,
    tasks: IAssemblyTask[],

    collapsed?: boolean
}



// --- --------------------------------------------------------------
// --- ----------- Типы для Группировки в Выполнении СЗ -------------
// --- --------------------------------------------------------------
export interface IMatrixManufactureTask {
    task: IAssemblyTask
    groups: IMatrixManufactureGroup[]
}

export interface IMatrixManufactureGroup {
    group: IAssemblyModelManufactureGroup
    group_lines: IMatrixManufactureGroupLine[]
}

export interface IMatrixManufactureGroupLine {
    order_line: IAssemblyTaskOrderLine
    materials_array: IAssemblyTaskLineSector[]
}





// __ Вспомогательный тип для снятия readonly со всех вложенных полей
export type DeepWritable<T> = {
    -readonly [P in keyof T]: T[P] extends object ? DeepWritable<T[P]> : T[P]
}
