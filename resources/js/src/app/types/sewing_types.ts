// Info Тут все типы для Пошива

// line -------------------------------------------------------------------
// line ------------------------- План Загрузок----------------------------
// line -------------------------------------------------------------------


import type { IDims } from '@/types/index.ts'

export interface ISewingTask {
    action_at: string
    active: boolean
    change: number
    id: number
    position: number
    order: ISewingTaskOrder
    sewing_lines: ISewingTaskLine[]
}

// __ Связь с Содержимым СЗ
export interface ISewingTaskLine {
    id: number
    amount: number                          // __ Общее количество в заявке
    amount_avg: null | IAmountAvg[]         // __ Количество для средней модели по статистике
    time: ISewingTaskLineTime[]             // __ Трудозатраты
    created_at: string | null
    false_reason: string | null
    finished_at: string | null
    finished_by: number | null              // __ Тут в будущем добавим объект пользователя (Worker)
    position: number
    order_line: ISewingTaskOrderLine
}

// __ Статус Движения (выполнения) Заявки
export interface ISewingTaskStatus {

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
}

// __ Ключи для Типов ШМ
type IAvgKeys =
    'time_universal' |
    'time_auto' |
    'time_solid_hard' |
    'time_solid_lite' |
    'time_undefined' |
    'time_average'

// __ Тип для Средних значений для Average модели
type IAmountAvg = Record<IAvgKeys, number>

// __ Трудозатраты
type ISewingTaskLineTime = Record<IAvgKeys, number>


// --- --------------------------------------------------------------
// --- Типы для работы с сущностями Пошива
// --- --------------------------------------------------------------
export interface ISewingTaskStatusEntity {
    id: number
    name: string
    color: string
    position: number
    description?: string|null
    active?: boolean
    status?: number
    comment?: string|null
    note?: string|null
    meta?: string|null
    created_at?: string|null
    updated_at?: string|null
}
