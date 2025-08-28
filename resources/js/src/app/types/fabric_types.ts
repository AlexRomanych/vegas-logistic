import { FABRIC_MACHINES, FABRIC_TASK_STATUS, } from '@/app/constants/fabrics.ts'

// __ Стегальные машины
export interface IFabricMachine {
    ID: number
    TITLE: string
    NAME: string,
    INDEX: string,
}

export type MachineUnionType =
      typeof FABRIC_MACHINES.AMERICAN
    | typeof FABRIC_MACHINES.GERMAN
    | typeof FABRIC_MACHINES.CHINA
    | typeof FABRIC_MACHINES.KOREAN

// line --------------------------------------------

// __ Статусы СЗ
export interface ITaskStatus {
    WORD: string
    CODE: number
    TITLE: string
    TYPE: string
}

export type TaskStatusUnionType =
      typeof FABRIC_TASK_STATUS.UNKNOWN
    | typeof FABRIC_TASK_STATUS.CREATED
    | typeof FABRIC_TASK_STATUS.PENDING
    | typeof FABRIC_TASK_STATUS.RUNNING
    | typeof FABRIC_TASK_STATUS.DONE




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
    fabric: string
    fabric_rate: number
    fabric_mode: boolean
    descr: string
    correct: boolean
    editable: boolean
    note: string | null
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

// __ Полотно стеганное
export interface IFabric {
    id: number
    code_1C: string
    correct: boolean
    name: string
    display_name: string
    picture: {
        id: number
        name: string
    },
    textile: string
    fillersList: string[],
    active: true,
    rare: false,
    machines: {
        id: number,
        short_name: string
    }[],
    buffer: {
        amount: number
        min: number
        max: number
        min_rolls: number
        max_rolls: number
        optimal_party: number
        average_length: number
        rate: number
        productivity: number
    },
    text: {
        description: string | null,
        comment: string | null,
        note: string | null
    }

}


// line --------------------------------------------------------------
// line --------------------- Расход ПС ------------------------------
// line --------------------------------------------------------------

// __ Клиент
export interface IFabricExpenseClient {
    active: boolean
    short_name: string
}

// __ Позиция расхода
export interface IFabricExpenseOrderItem {
    expense: number
    fabric_id: number
}

// __ Заявка расхода ПС
export interface IFabricExpenseOrder {
    active: boolean
    client: IFabricExpenseClient
    closed_at: string | null
    closed_by_user_id: number | null
    code_1C: string
    description: string | null
    expense_date: string
    fabricsExpense: IFabricExpenseOrderItem[]
    id: number
    is_closed: boolean
    order_no: string
    raw_text: string
    time_1C: string | null
    position: number
}

// line --------------------------------------------------------------
