// __ Коллекция блоков
import { LINE_0, LINE_1, LINE_2, UNIT, UNIT_PICS, UNIT_METERS } from '@/app/constants/blocks.ts'
import type { ICuttingDayWorkerPivot } from '@/types/cutting_types.ts'

export interface IBlockCollection {
    id: number
    code_1c: string
    name: string
    unit: IBlockUnit | null
    kdb: string | null
    kdb_id: number | null
    line: IBlockLine
    line_alt: IBlockLine | null
    priority: number
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
export type IBlockLine = typeof LINE_0 | typeof LINE_1 | typeof LINE_2

// __ Единица измерения
export type IBlockUnit = typeof UNIT | typeof UNIT_PICS | typeof UNIT_METERS


// --- --------------------------------------------------------------------
// --- ---------------------- Для рендера КДБ -----------------------------
// --- --------------------------------------------------------------------
// __ КДБ
export interface IBlockDocument {
    id: number
    kdb: string
    name: string
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



// --- --------------------------------------------------------------------
// --- --------------------- Для Учета персонала  -------------------------
// --- --------------------------------------------------------------------
export type IBlockDayWorker = {
    id: number
    surname: string
    name: string
    patronymic: string
    pivot?: ICuttingDayWorkerPivot
}
