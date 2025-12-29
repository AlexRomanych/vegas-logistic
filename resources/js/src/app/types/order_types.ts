// ___ Тут все тиы для Заказов

import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

export interface IRenderOrder {
    id: number
    active: boolean
    elements_type_ref: string       // __ Типы элементов референсный (матрасы или чехлы или что-то ещё)
    elements_type: string           // __ Типы элементов (матрасы + чехлы)
    elements_type_render: string    // __ Типы элементов (матрасы + чехлы) на русском
    client: IRenderOrderClient
    client_name_1c: string
    order_no_str: string
    no_1c: string
    order_type: IRenderOrderType
    amounts: IRenderOrderAmounts

    status: number

    comment_1c: string
    description?: string

    // __ КС
    manager_start: string | null
    manager_end: string | null

    // __ КБ
    design_end: string | null
    design_start: string | null
    manager_load_date: string | null        // __ Дата загрузки в КБ менеджером из 1С

    unload_at: string | null
    load_at: string | null

    order_period: string
    responsible: string | null

    is_forecast: boolean                    // __ Прогнозный заказ или нет
    shown: boolean                          // __ Показывать или нет в плане
    stat_include: boolean                   // __ Включать в статистику или нет

    lines: IRenderOrderLine[]

    collapsed?: boolean
}

export interface IRenderOrderClient {
    id: number
    short_name: string
}

export interface IRenderOrderType {
    color: string
    display_name: string
}

export interface IRenderOrderAmounts {
    averages: number
    children: number
    covers: number
    formats: number
    mattresses: number
    totals: number
    unknowns: number
    up_covers: number
}

export interface IRenderOrderLine {
    size: string
    textile: string
    amount: number
    composition: string
    describe_1: string
    describe_2: string
    describe_3: string
    id: number
    model: IRenderOrderLineModel
}

export interface IRenderOrderLineModel {
    base_composition: string
    code_1c: string
    is_auto: boolean
    is_solid: boolean
    is_solid_hard: boolean
    is_solid_light: boolean
    is_undefined: boolean
    is_universal: boolean
    kant: string | null
    model_type: string
    name_report: string
    tkch: string | null
}


// ___ Проверенная Заявка, котороая вернулась с сервера
export interface IValidatedOrder {
    client_id: number
    client_full_name: string
    client_add_name: string
    client_code: string
    order_code: string
    order_no: string
    order_no_1c: string
    manuf_at_1c: string
    load_at: string
    load_at_1c: string
    unload_at: string
    kb_start: string
    kb_end: string
    mg_start: string
    mg_end: string
    responsible: string
    comment: string
    base: string
    service: string

    collapsed?: boolean
    elements_type?: string
    renderType?: IColorTypes,

    validate: IValidatedOrderValidator

    items: IValidatedOrderItem[]
}

export interface IValidatedOrderItem {
    n: string
    c: string
    s: string
    t: string
    a: number
    d: string
    d1: string
    d2: string
    d3: string

    validate: IValidatedOrderValidator
}

export interface IValidatedOrderValidator {
    check: string
    action: string
    advice: string
}

