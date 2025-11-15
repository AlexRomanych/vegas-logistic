// Info Тут все, что связано с логами

//line----------------------------------------------------------------------
//line-------------- Физические рулоны (FabricTaskRolls) -------------------
//line----------------------------------------------------------------------

export interface IFabricTaskRollLog {
    id: number
    log_at: string
    log_at_date?: Date
    event: string
    description: string | null
    reason: string | null
    ip?: string
    status_before: number
    status_after: number
    roll_position_before: number
    roll_position_after: number
    check_1C: boolean | null
    uncheck_1C: boolean | null
    fabric_task_roll: IFabricTaskRollLogRoll
    responsible: IFabricTaskRollLogResponsible
    user: IFabricTaskRollLogUser
}

export interface IFabricTaskRollLogRoll {
    fabric: IFabricTaskRollLogFabric
    fabric_roll_length: number
    id: number
}

export interface IFabricTaskRollLogFabric {
    id: number
    display_name: string
}

export interface IFabricTaskRollLogResponsible {
    id: number
    name: string
    patronymic: string
    surname: string
}

export interface IFabricTaskRollLogUser {
    id: number
    name: string
    patronymic: string
    surname: string
}

//line----------------------------------------------------------------------
