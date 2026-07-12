import type { ICellEvent, ICellEventsCells } from '@/types'


// __ Дублирование констант сервера
export const CELL_EVENT_UNKNOWN = 'unknown' as const
export const CELL_EVENT_BLOCK = 'blocks' as const
export const CELL_EVENT_SEWING = 'sewing' as const
export const CELL_EVENT_CUTTING = 'cutting' as const
export const CELL_EVENT_FABRIC = 'fabric' as const


export const CELL_EVENT_DRAFT: ICellEvent = {
    id: 0,
    cell: CELL_EVENT_UNKNOWN,
    day_id: 0,
    start_at: '',
    finish_at: '',
    event: '',
    answer: null,
    created_at: null,
    updated_at: null,
    readyToSave: false,
}
