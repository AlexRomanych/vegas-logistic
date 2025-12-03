// Info Тут все типы для Планов

// line -------------------------------------------------------------------
// line ------------------------- План Загрузок----------------------------
// line -------------------------------------------------------------------
export interface IPlanLoad extends IPlan {}
export interface IPlan {
    id: number
    active: boolean
    client: IPlanClient
    order_no: string
    period: string
    order_type: IPlanOrderType
    load_position: number | null
    amounts: IPlanAmounts
    load_at: string
    action_at: string
    load_at_previous: string | null
    unload_at: string | null
    status: number | null
    description: string | null
    created_at: string
    updated_at: string | null
}

export interface IPlanLoadAmounts extends IPlanAmounts{}
export interface IPlanAmounts {
    averages?: number
    children?: number
    covers?: number
    formats?: number
    mattresses?: number
    unknowns?: number
    up_covers?: number
    totals?: number
}


export interface IPlanLoadClient extends IPlanClient{}
export interface IPlanClient {
    id: number
    active: boolean
    name: string
    add_name: string
    short_name: string
    region: IPlanLoadClientRegion
}

export interface IPlanLoadOrderType extends IPlanOrderType{}
export interface IPlanOrderType {
    id: number
    name: string
    display_name: string
    color: string
}

export type IPlanLoadClientRegion = IPlanClientRegion
export type IPlanClientRegion = 'east' | 'west'


// __ Матрица рендера плана загрузок
export type IPlanMatrix = IPlanMatrixWeek[]
export type IPlanMatrixWeek = IPlanMatrixDay[]
export type IPlanMatrixDay = IPlan[]

export type IPlanLoadsMatrix = IPlanLoadsMatrixWeek[]
export type IPlanLoadsMatrixWeek = IPlanLoadsMatrixDay[]
export type IPlanLoadsMatrixDay = IPlanLoad[]
// export type IPlanLoadsMatrix = IPlanLoad[][][]    // То же самое


// line -------------------------------------------------------------------



