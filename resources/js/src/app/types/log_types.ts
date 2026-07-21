// Info Тут все, что связано с логами

//--- ----------------------------------------------------------------------
//--- -------------- Физические рулоны (FabricTaskRolls) -------------------
//--- ----------------------------------------------------------------------

import { LEVEL_ERROR, LEVEL_INFO, LEVEL_WARNING } from '@/app/constants/log.ts'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

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

//--- ----------------------------------------------------------------------


//--- ----------------------------------------------------------------------
//--- ----------------------- События приложения ---------------------------
//--- ----------------------------------------------------------------------

export type IEventLogLevel = typeof LEVEL_INFO | typeof LEVEL_ERROR | typeof LEVEL_WARNING
export interface IEventLogObj {
    LEVEL: IEventLogLevel,
    TITLE: string,
    TYPE : IColorTypes,
}




export interface IEventLog {
    id: number
    level: IEventLogLevel
    target: string
    message: string
    context: Record<string, string | number>[] | null
    created_at: string
}




