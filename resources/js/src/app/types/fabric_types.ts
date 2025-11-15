import { FABRIC_MACHINES, FABRIC_TASK_STATUS, type IConstFabricMachine, } from '@/app/constants/fabrics.ts'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
// import { getISOFromLocaleDate } from '@/app/helpers/helpers_date'


// __ Период выборки
export interface ITaskPeriod {
    start: string
    end: string
}

// __ Просто период
export interface IPeriod {
    start: string
    end: string
}

// __ Объект СЗ для отображения в компонентах, который приходит с сервера

// Получаем все названия машин по ключу TITLE
export type FabricMachineTitles = typeof FABRIC_MACHINES[keyof typeof FABRIC_MACHINES]['TITLE'];

// Здесь все то, что приходит с сервера в объекте machines
export interface IMachineData {
    active: boolean
    description: string | null
    finish_at: string | null
    rolls: IRoll[]
    lastExecRoll: IRollExec | null // Последний выполненный рулон на этой машине в предыдущем СЗ
}

// Здесь все то, что приходит с сервера в объекте workers
export interface IWorkerData {
    id: number
    surname: string
    name: string
    patronymic: string
    email: string | null
    phone: string | null
    address: string | null
    record_id: number
}

export interface ITaskItem {
    common: {
        id: number
        active: boolean
        status: TaskStatusUnionCodeType
        created_at: string
        created_by: string
        description: string | null
        start_at: string | null
        finish_at: string | null
        team: number
    }
    date: string
    id: number
    machines: Record<FabricMachineTitles, IMachineData>
    workers: IWorkerData[]
    active?: boolean
}


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
    typeof FABRIC_TASK_STATUS.UNKNOWN |
    typeof FABRIC_TASK_STATUS.CREATED |
    typeof FABRIC_TASK_STATUS.PENDING |
    typeof FABRIC_TASK_STATUS.RUNNING |
    typeof FABRIC_TASK_STATUS.DONE

export type TaskStatusUnionCodeType =
    typeof FABRIC_TASK_STATUS.UNKNOWN.CODE |
    typeof FABRIC_TASK_STATUS.CREATED.CODE |
    typeof FABRIC_TASK_STATUS.PENDING.CODE |
    typeof FABRIC_TASK_STATUS.RUNNING.CODE |
    typeof FABRIC_TASK_STATUS.DONE.CODE


export interface IRollStatus {
    WORD: string
    CODE: number
    TITLE: string
    TYPE: IColorTypes
}


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
    textile_layers_amount: number
    average_textile_roll_length: number
    textile_length?: number
    fabric_name?: string
    isTuning?: boolean          // Указатель на то, что рулон является информацией по переналадке
}

// __ Физический (исполняемый) рулон
export interface IRollExec {
    id: number
    duration: number
    fabric_id: number
    false_reason: string
    status: number
    status_prev: number
    position: number
    productivity: number
    rate: number
    resume_at: null | string
    rolling: boolean
    rolling_length: number
    start_at: null | string
    paused_at: null | string
    finish_at: null | string
    registration_1C_at: null | string
    move_to_cut_at: null | string
    receipt_to_cut_at: null | string
    close_at: null | string
    finish_by: number
    movable: boolean
    descr: string
    textile_length: number
    fabric_length: number
    responsible: IWorkerData[]
}

// __ Полотно стеганное
export interface IFabric {
    id: number
    code_1C: string
    correct: false
    name: string
    display_name: string
    picture: {
        id: number
        name: string
    },
    textile: string
    fillersList: string[],
    active: boolean,
    rare: boolean,
    statistic: boolean
    statistic_length: number
    hand_length: number
    textile_layers_amount: number
    average_textile_roll_length: number
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
        fabric_productivity?: number
        picture_productivity?: number
        average_fabric_length: number
    },
    text: {
        description: string | null,
        comment: string | null,
        note: string | null
    }

}

// line --------------------------------------------------------------
// line --------------------- Рисунки ПС -----------------------------
// line --------------------------------------------------------------
export interface IFabricPictureItem {
    active: boolean
    description: string | null
    id: number
    // machines: IMachinesMap
    machines: { [key in IMachinesNamesUnion]: IFabricMachineItem }
    moment_speed: number
    name: string
    productivity: number
    shuttle_amount: number | null
    stitch_length: number
    stitch_speed: number
}

export interface IFabricMachineItem {
    machine: IFabricMachine
    schema: IFabricSchema
}

export interface IFabricMachine {
    id: number
    active: boolean
    name: string
    short_name: string
    description: string | null
}

export interface IFabricSchema {
    id: number
    schema: string
    schema_name: string
    description: string | null
}

export type IMachinesNamesUnion =
    'fabricMainMachine'
    | 'fabricAltMachine_1'
    | 'fabricAltMachine_2'
    | 'fabricAltMachine_3'

export type IMachinesMap = {
    [key in IMachinesNamesUnion]: IFabricMachineItem
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


// line --------------------------------------------------------------
// line ------------------- Время переналадки ------------------------
// line --------------------------------------------------------------

export interface ITimeItem {
    id: number
    name: string
    active: boolean
    machine: ITimeMachine
    pictures: ITimePictureSchema[]
}

export interface ITimeMachine {
    id: number
    name: string
    short_name: string
}

export interface ITimePicture {
    id: number
    name: string
    active: boolean
    machine: ITimeMachine
}

export interface ITimePictureSchema {
    pic: ITimePicture
    tuning_time: number
    db?: boolean                    // __ Для отрисовки в компоненте (признак, что это время из БД на сервере, а не сгенерировано на фронте)
}

// __ Для получения времени переналадки для Управления СЗ и Выпоолнения СЗ
export interface ITimeContext {
    from: number
    to: number
    time: number | null
}


// ___ Общий массив производительности
export interface IGlobalProductivityItem {
    isTuning: boolean
    time: number
}

export type IGlobalProductivity = Record<FabricMachineTitles, IGlobalProductivityItem>


// line --------------------------------------------------------------
// line ------------- Для обмена между компонентами ------------------
// line --------------------------------------------------------------
export interface IInputData {
    index: number
    machine: IConstFabricMachine
    roll: IRoll
    task: ITaskItem
    taskDescription: string | null
}

// line --------------------------------------------------------------
// line ------------- Рулон для движения между участками -------------
// line --------------------------------------------------------------
export interface IRollMove {
    id: number
    descr: string
    comment: string
    note: string
    rate: number
    duration: number
    start_at: string
    status: number
    textile_length: number
    fabric_length: number
    fabric: {
        display_name: string
        fabric_id: number
    }
    finish_at: string | null
    finish_by: IRollMovePerson
    isRegistered_1C: boolean
    move_to_cut_at: string | null
    move_to_cut_by: IRollMovePerson
    receipt_to_cut_at: string | null
    receipt_to_cut_by: IRollMovePerson
    registration_1C_at: string | null
    registration_1C_by: IRollMovePerson
}

export interface IRollMovePerson {
    id: number
    name: string
    patronymic: string
    surname: string
}

// __ Рендер рулона (преобразуем в удобный для отображения вид)
export interface IRollMoveRender {
    fabric: {
        id: number
        displayName: string
        textileLength: number
        fabricLength: number
        collapsed: boolean
    },

    rolls: IRollMove[],
}

// line --------------------------------------------------------------
