// Info: Тут все общие типы для Пошива и Раскроя

import { UNIVERSAL, AUTO, SOLID_HARD, SOLID_LITE, UNDEFINED, AVERAGE } from '@/app/constants/textile_common.ts'
import type { IPlanMatrixDayItem } from '@/types/plan_types.ts'
import type { ISewingTask } from '@/types/sewing_types.ts'
import type { ICuttingTask } from '@/types/cutting_types.ts'

// --- ------------------------------------------------------------
// __ Структура для расчета Трудозатрат и Количества по ШМ для рендеринга в шаблоне
// __ Сначала определим тип структуры данных


// __ Объект ШМ
export const TEXTILE_MACHINES: Record<string, ITextileMachineKeys> = {
    UNIVERSAL,
    AUTO,
    SOLID_HARD,
    SOLID_LITE,
    UNDEFINED,
    AVERAGE,
}

export type IStatItem = {
    time: number
    amount: number
};
// __ Создаем тип для объекта amount, где ключами будут только ключи из SEWING_MACHINES
export type IAmountAndTime = Record<keyof typeof TEXTILE_MACHINES, IStatItem>
// --- ------------------------------------------------------------


export type ITextileMachineKeys =
    typeof UNIVERSAL |
    typeof AUTO |
    typeof SOLID_HARD |
    typeof SOLID_LITE |
    typeof UNDEFINED |
    typeof AVERAGE


export type ICalcMode = 'dynamic' | 'static'


export type IDay = (ISewingTask  | ICuttingTask) & IPlanMatrixDayItem


// --- ------------------------------------------------------------
// __ Типы для разницы для каждой записи в матрице Пошива
export type IDiffsType = 'UPDATED' | 'ADDED' | 'DELETED'
// --- ------------------------------------------------------------

// --- ------------------------------------------------------------
// __ Тип для разницы между матрицами отображения календаря в Пошиве
export interface IRenderMatrixDiff {
    taskId: number
    dayFromOffset?: number
    dayToOffset?: number
    oldTaskPosition?: number
    newTaskPosition?: number
    isPositionChanged?: boolean
    isMoved?: boolean
    lineDiffs?: IRenderMatrixLineDiffs[]

    // __ Это для создания новой заявки (Не используется в данный момент)
    type?: null | 'NEW_TASK'
    newPosition?: number | null

    // __ Не используется в данный момент
    areLinesChanged?: boolean

    // __ Дополнительно задаем статус, чтобы задать его на бэке
    statusId?: number
}

export interface IRenderMatrixLineDiffs {
    lineId: number
    type: IDiffsType
    oldPosition?: number
    newPosition?: number
    oldAmount?: number
    newAmount?: number
    isPositionChanged?: boolean
    isAmountChanged?: boolean
}

// --- ------------------------------------------------------------
