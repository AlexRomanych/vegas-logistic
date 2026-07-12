import { CELL_EVENT_BLOCK, CELL_EVENT_CUTTING, CELL_EVENT_FABRIC, CELL_EVENT_SEWING, CELL_EVENT_UNKNOWN } from '@/app/constants/cell_events.ts'


export type ICellEventsCells =
    typeof CELL_EVENT_BLOCK |
    typeof CELL_EVENT_SEWING |
    typeof CELL_EVENT_CUTTING |
    typeof CELL_EVENT_FABRIC |
    typeof CELL_EVENT_UNKNOWN



// __ События на Производственном участке для Журнала Событий
export interface ICellEvent {
    id: number
    cell: ICellEventsCells
    day_id: number
    start_at: string
    finish_at: string
    event: string
    answer: string | null
    created_at: string | null
    updated_at: string | null

    readyToSave?: boolean
}
