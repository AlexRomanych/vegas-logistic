// Info Тут все типы для Планов

// line -------------------------------------------------------------------
// line ------------------------- План Загрузок----------------------------
// line -------------------------------------------------------------------
export interface IPlanLoad {
    id: number
    active: boolean
    client: IPlanLoadClient
    order_no: string
    period: string
    order_type: IPlanLoadOrderType
    load_position: number | null
    amounts: IPlanLoadAmounts
    load_at: string
    load_at_previous: string | null
    unload_at: string | null
    status: number | null
    description: string | null
    created_at: string
    updated_at: string | null
}

export interface IPlanLoadAmounts {
    averages?: number
    children?: number
    covers?: number
    formats?: number
    mattresses?: number
    unknowns?: number
    up_covers?: number
    totals?: number
}

export interface IPlanLoadClient {
    id: number
    active: boolean
    name: string
    add_name: string
    short_name: string
    region: IPlanLoadClientRegion
}

export interface IPlanLoadOrderType {
    id: number
    name: string
    display_name: string
    color: string
}

export type IPlanLoadClientRegion = 'east' | 'west'


// __ Матрица рендера плана загрузок
export type IPlanLoadsMatrix = IPlanLoadsMatrixWeek[]
export type IPlanLoadsMatrixWeek = IPlanLoadsMatrixDay[]
export type IPlanLoadsMatrixDay = IPlanLoad[]
// export type IPlanLoadsMatrix = IPlanLoad[][][]    // То же самое


// line -------------------------------------------------------------------



