import { FABRIC_MACHINES, FABRICS_NULLABLE } from '@/app/constants/fabrics.ts'

// __ Стегальные машины
export interface IFabricMachine  {
    ID: number
    TITLE: string
    NAME: string,
    INDEX: string,
}

export type MachineUnionType =
    | typeof FABRIC_MACHINES.AMERICAN
    | typeof FABRIC_MACHINES.GERMAN
    | typeof FABRIC_MACHINES.CHINA
    | typeof FABRIC_MACHINES.KOREAN

// line --------------------------------------------


// __ Контекстный рулон
export interface IRoll {
    id: number
    roll_position: number
    average_textile_length: number
    average_fabric_length: number
    productivity: number
    rate: number
    buffer: number
    rolls_amount: number
    length_amount: number
    fabric_id: number
    fabric: number
    fabric_rate: number
    fabric_mode: boolean
    descr: string
    correct: boolean
    editable: boolean
    rolls_exec: IRollExec[]
}

// __ Физический (исполняемый) рулон
export interface IRollExec {
    descr: string
    duration: number
    fabric_id: number
    false_reason: string
    finish_at: null | string
    finish_by: number
    id: number
    movable: boolean
    paused_at: null | string
    position: number
    productivity: number
    rate: number
    resume_at: null | string
    rolling: boolean
    rolling_length: number
    start_at: null | string
    status: number
    status_prev: number
    textile_length: number
}



