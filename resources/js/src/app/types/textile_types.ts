// Info: Тут все общие типы для Пошива и Раскроя

import { UNIVERSAL, AUTO, SOLID_HARD, SOLID_LITE, UNDEFINED, AVERAGE } from '@/app/constants/textile_common.ts'
import type { IDiffsType } from '@/types/index.ts'

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
}

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

    // __ Добавляем отслеживание Смены
    oldChange?: number // или IBlockTaskChangeKeys, смотря какой у тебя тип в IBlockTask
    newChange?: string
    isChangeChanged?: boolean
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
