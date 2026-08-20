// __ Коллекция блоков
import {
    LINE_0,
    LINE_1,
    LINE_2,
    UNIT,
    UNIT_PICS,
    UNIT_METERS,
    BLOCK_TASK_STATUS_RUNNING,
    BLOCK_TASK_STATUS_ROLLING,
    BLOCK_TASK_STATUS_CREATED,
    BLOCK_TASK_STATUS_PENDING,
    BLOCK_TASK_STATUS_DONE,
    LINE_0_NAME,
    LINE_1_NAME,
    LINE_2_NAME,
    BLOCK_MANUF_LINES,
    CHANGE_1,
    CHANGE_2,
    OPTIMIZE_BY_TUNING_TIME, OPTIMIZE_BY_PRIORITY
} from '@/app/constants/blocks.ts'
import type { IPlanMatrixDayItem } from '@/types/plan_types.ts'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import type { ICellEvent, IDiffsType } from '@/types/index.ts'


// __ Коллекция Блоков
export interface IBlockCollection {
    id: number
    code_1c: string
    name: string
    unit: IBlockUnit | null
    kdb: string | null
    kdb_id: number | null
    line: IBlockManufLine
    line_alt: IBlockManufLine | null
    priority: number
    priority_2: number
    height: number
    length: number
    productivity: number
    active: boolean
    own: boolean
    description: string | null
    blocks: IBlock[]

    collapsed?: boolean
    can_edit?: boolean
}


// __ Блок
export interface IBlock {
    id: number
    code_1c: string
    name: string
    unit: string | null
    width: number
    length: number
    active: boolean
    description: string | null
    collection: string
    can_edit?: boolean
}

// __ Линия производства
export type IBlockManufLine = typeof LINE_0 | typeof LINE_1 | typeof LINE_2

// __ Единица измерения
export type IBlockUnit = typeof UNIT | typeof UNIT_PICS | typeof UNIT_METERS


// --- --------------------------------------------------------------------
// --- ---------------------- Для рендера КДБ -----------------------------
// --- --------------------------------------------------------------------
// __ КДБ
export interface IBlockDocument {
    id: number
    kdb: string
    file_path: string | null
    description: string | null
}


// --- --------------------------------------------------------------------
// --- ---------------------- Для Статусов СЗ -----------------------------
// --- --------------------------------------------------------------------
export interface IBlockTaskStatusEntity {
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
export type IBlockTaskStatusesSet = { task: number, status: number }

// __ Статус Движения (выполнения) Заявки
export interface IBlockTaskStatus {
    id: number
    color: string
    name: string
    pivot: IBlockTaskStatusPivot
}

// __ Дополнительная инфа
export interface IBlockTaskStatusPivot {
    created_at: string | null
    duration: number | null
    finished_at: string | null
    set_at: string | null
    started_at: string | null
}


// --- ----------- Производственный день ---------------------------
// __ Тип для Производственного Дня
export type IBlockDay = {
    id: number
    change: IBlockTaskChangeKeys
    action_at: string
    action_at_str: string
    description: string | null
    comment: string | null
    start_at: string | null
    paused_at: string | null
    resume_at: string | null
    finish_at: string | null
    duration: number
    block_tasks: IBlockTask[]
    responsible: IBlockDayWorker | null
    workers: IBlockDayWorker[]
    cell_events: ICellEvent[]

    ready: boolean  // __ Готовность к добавлению новых СЗ

    collapsed?: boolean
    personal_collapsed?: boolean
    tasks_collapsed?: boolean
    common_collapsed?: boolean
    cell_events_collapsed?: boolean
}


// --- --------------------------------------------------------------------
// --- --------------------- Для Учета персонала  -------------------------
// --- --------------------------------------------------------------------
export type IBlockDayWorker = {
    id: number
    surname: string
    name: string
    patronymic: string
    pivot?: IBlockDayWorkerPivot
}

export type IBlockDayWorkerPivot = {
    id: number
    working_time: number | null
}


// --- --------------------------------------------------------------------
// --- ------------------------- СЗ Блоков  -------------------------------
// --- --------------------------------------------------------------------
export interface IBlockTask extends IPlanMatrixDayItem {
    id: number
    id_ref: number                                  // __ референсный id (при разбиении нового СЗ, id_ref === id, то есть основаниие старого СЗ)
    action_at: string
    active: boolean
    change: IBlockTaskChangeKeys
    position: number
    comment: string | null,
    order: IBlockTaskOrder
    block_lines: IBlockTaskLine[]
    statuses: IBlockTaskStatus[]
    current_status: IBlockTaskStatus

    collapsed?: boolean
}


// __ Связь с Заявкой
export interface IBlockTaskOrder {
    id: number
    order_no_num: number
    order_no_origin: string
    order_no_str: string
    load_at: string | null
    comment_1c: string | null
    client: IBlockTaskOrderClient
    order_type: IBlockTaskOrderType
}

// __ Связь Основной Заявки с Клиентом
export interface IBlockTaskOrderClient {
    id: number
    name: string
    add_name: string
    short_name: string
}

// __ Связь Основной Заявки с Типом Заявки
export interface IBlockTaskOrderType {
    id: number
    display_name: string
    color: string
}

// __ Связь с Содержимым СЗ
export interface IBlockTaskLine {
    id: number
    id_ref: number                                  // __ референсный id (при разбиении строки СЗ, id_ref === id, то есть основаниие старого СЗ)
    amount: number                                  // __ Общее количество в заявке
    time: number                                    // __ Трудозатраты
    square: number                                  // __ Площадь единицы данного блока
    is_average: boolean                             // __ Флаг для расчетной модели

    created_at: string | null
    false_reason: string | null
    finished_at: string | null
    false_at: string | null
    finished_by: number | null                      // __ Тут в будущем добавим объект пользователя (Worker)
    position: number
    description: string | null

    order_meta?: string                             // __ Номер заявки

    manuf_line: IBlockManufLine
    order_lines: IBlockTaskOrderLine[]

    completed?: boolean                             // __ Флаг для SFC выполнения СЗ
    groupAttr?: string                              // __ Атрибут для группировки строк


    block: IBlockTaskLineBlock
    order_line_ids: IBlockTaskLineExpense[]
}


// __ Метаинформация, которую запихиваем в контекст СЗ (BlockLine) в момент создания
export interface IBlockTaskLineExpense {
    expense: number
    order_line_amount: number
    order_line_id: number
    rest: number
}

// __ Информация по привязке к блоку в БД
export interface IBlockTaskLineBlock {
    id: number
    code_1c: string
    name: string
    active: boolean
    description: string | null
    length: number
    width: number
    unit: string
    collection: IBlockTaskLineBlockCollection
}


// __ Информация по привязке Блока к его Коллекции в БД
export interface IBlockTaskLineBlockCollection {
    code_1c: string
    id: number
    kdb: IBlockDocument | null
    length: number
    manuf_line: IBlockManufLine
    manuf_line_alt: IBlockManufLine
    name: string
    priority_1: number
    priority_2: number
    productivity: number
    unit: string
}

// __ Данные по привязке контекста СЗ Блоков (BlockLine) к строкам (OrderLine) в Заявке (Order) в момент создания
export interface IBlockTaskOrderLine {
    id: number
    model_code_1c: string
    model_name: string
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
}


// --- --------------------------------------------------------------
// --- Типы для работы со статусами СЗ
// --- --------------------------------------------------------------

export type IBlockTaskStatusKeys =
    typeof BLOCK_TASK_STATUS_CREATED |
    typeof BLOCK_TASK_STATUS_ROLLING |
    typeof BLOCK_TASK_STATUS_PENDING |
    typeof BLOCK_TASK_STATUS_RUNNING |
    typeof BLOCK_TASK_STATUS_DONE


// --- ------------------------------------------------------------
// __ Структура для расчета Трудозатрат и Количества для рендеринга в шаблоне
// __ Сначала определим тип структуры данных

export type IBlockManufLineKeys =
    typeof LINE_0_NAME |
    typeof LINE_1_NAME |
    typeof LINE_2_NAME


export type IStatItemBlock = {
    time: number
    amount: number
    square: number
}

export interface IBlockTaskStatusItem {
    ID: number,
    WORD: string
    TITLE: string
    TYPE: IColorTypes
    PRIORITY: number
}

// __ Создаем тип для объекта amount, где ключами будут только ключи из BLOCK_MANUF_LINES
export type IAmountAndTimeBlock = Record<keyof typeof BLOCK_MANUF_LINES, IStatItemBlock>

// --- ------------------------------------------------------------


// --- ------------------------------------------------------------
// __ Тип для разницы между массивами СЗ Пошиве
export interface IBlockTaskArrayDiff {
    taskId: number
    taskIdRef?: number
    type?: IDiffsType
    // current?: IBlockTask
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
    lineChanges?: IBlockTaskArrayLineDiffs[]
}

export interface IBlockTaskArrayLineDiffs {
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
// __ Тип для сохранения (изменения) стола для записи СЗ Раскроя
export type IBlockLineSetData = { id: number, line: IBlockManufLine }


// --- ------------------------------------------------------------
// __ Типы панелей меню в карточке Заказа в Пошиве в календаре
export type IBlockLinesPanel = 'left' | 'right'
export type IBlockManufLinesPanel = typeof LINE_1 | typeof LINE_2 | typeof LINE_0
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для сортировки в Карточке Заказа в Блоках
export type IBlockTaskCardSort = 'none' | 'asc' | 'desc'
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Для смен
export type IBlockTaskChangeKeys = typeof CHANGE_1 | typeof CHANGE_2

export interface IBlockTaskChange {
    ID: number
    NAME: IBlockTaskChangeKeys
    TITLE: string
    TITLE_ROME: string
    ICON: string
    TYPE: IColorTypes
}

// --- ------------------------------------------------------------


// --- ----------- Статистика выполнения СЗ (прогресс) ---------------------------
// __ Вспомогательный Тип для вычисления статистики по СЗ
export interface IBlockTaskExecuteStatistics {
    amount: IBlockTaskExecuteStatisticsItem,
    time: IBlockTaskExecuteStatisticsItem,
}

export interface IBlockTaskExecuteStatisticsItem {
    finished: number
    unfinished: number
    total: number
}


// --- -------------------------------------------------------------------
// --- --------------- Тип для группировки СЗ по ШМ ----------------------
// --- -------------------------------------------------------------------
export type IBlockTaskLinesGroupNames = typeof LINE_1_NAME | typeof LINE_2_NAME | typeof LINE_0_NAME
export type IBlockTaskLinesSubGroupNames = typeof LINE_1_NAME | typeof LINE_2_NAME | typeof LINE_0_NAME


// __ Для набора правил Группировки СЗ по ШМ
export interface IBlockTaskLinesGroup {
    GROUP_NAME: IBlockTaskLinesGroupNames
    GROUP_TYPE: IColorTypes
    GROUP_COLOR?: string | null,
    SUBGROUPS: {
        SUBGROUP_NAME: IBlockTaskLinesSubGroupNames
        SUBGROUP_TYPE: IColorTypes
        SUBGROUP_COLOR?: string | null,
        SUBGROUP_LINE: string[]
    }[]
}

// __ Для отображения СЗ по ШМ
export interface IBlockTaskLinesGroupData {
    action_at?: string
    groupName: IBlockManufLine
    groupType: IColorTypes
    hasData: boolean
    subgroups: IBlockTaskLinesSubgroup[]
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
    square: {
        total: number
        done: number
        incomplete: number
    }
    collapsed?: boolean
    tuningTimeTotal: number

    totals?: {
        amount: { total: number; done: number }
        square: { total: number }
        labor_cost: { total: number }
    }
}


export interface IBlockTaskLinesSubgroup {
    subgroupName: string
    subgroupId: number
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
    square: {
        total: number
        done: number
        incomplete: number
    }
    lines: IBlockTaskLine[]
    // undergroups: IBlockTaskLinesUnderGroup[]
    collapsed?: boolean
    priority: number
    isTuning: boolean

    totals?: {
        amount: { total: number; done: number }
        square: { total: number }
        labor_cost: { total: number }
    }
}

// export interface IBlockTaskLinesUnderGroup {
//     undergroupName: string
//     undergroupOrderTitle: string | null  // Название заявки (для отображения), к которой относится СЗ
//     undergroupType: IColorTypes
//     hasData: boolean
//     time: {
//         total: number
//         done: number
//         incomplete: number
//     }
//     amount: {
//         total: number
//         done: number
//         incomplete: number
//     }
//     lines: IBlockTaskLine[]
//     cutWidth: number
//     cutLength: number
//     cutLengthTotal: number
// }


// --- -------------------------------------------------------------------
// --- -------------------- Для времени переналадки ----------------------
// --- -------------------------------------------------------------------

export interface IBlockCollectionTime {
    code_1c: string
    id: number
    line: IBlockManufLine
    line_alt: IBlockManufLine
    name: string
    priority: string
    tuning_time?: number
    collections_to?: IBlockCollectionTime[]
    db?: boolean                    // __ Для отрисовки в компоненте (признак, что это время из БД на сервере, а не сгенерировано на фронте)
}


// --- -------------------------------------------------------------------
// --- -------------------- Оптимизация выполнения -----------------------
// --- -------------------------------------------------------------------

export type IOptimizeType = typeof OPTIMIZE_BY_TUNING_TIME | typeof OPTIMIZE_BY_PRIORITY
