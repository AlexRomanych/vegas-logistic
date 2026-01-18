import type { IPeriod, IPlanMatrix, IPlanMatrixDay, IPlanMatrixDayItem } from '@/types'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import {
    additionDays,
    areDatesEqual,
    formatToYMD,
    getDaysDifference,
    getMondayBefore,
    getSundayAfter
} from '@/app/helpers/helpers_date'

// ___ Самое главное - это поле action_at

// ___ Функция, которая возвращает период для рендера плана (находит границы данных и расширяет до ПН до и ВС после)
export function getRenderPeriodForPlan(plan: IPlanMatrixDayItem[]): IPeriod {
    const datesArray = getRenderArrayForPlan(plan)
    if (datesArray.length === 0) return PERIOD_DRAFT
    return {
        start: formatToYMD(getMondayBefore(datesArray[0])),
        end:   formatToYMD(getSundayAfter(datesArray[datesArray.length - 1])),
    }
}

// ___ Функция, которая возвращает матрицу для рендера плана
// ___ Это сортированный массив дат, на которые нужно отрисовать план
export function getRenderArrayForPlan(plan: IPlanMatrixDayItem[]): Date[] {
    const datesSet   = new Set(plan.map(planItem => splitDate(planItem.action_at)))
    const datesArray = Array.from(datesSet).map(date => new Date(date)).sort((a, b) => a.getTime() - b.getTime())
    return datesArray
}


// ___ Возвращает матрицу для рендера плана
export function getRenderMatrixForPlan(plan: IPlanMatrixDayItem[], renderPeriod: IPeriod): IPlanMatrix {

    const daysDifference = getDaysDifference(renderPeriod.start, renderPeriod.end)
    const weeksAmount    = (daysDifference + 1) / 7

    const renderMatrix = []
    let workDate       = new Date(renderPeriod.start)

    for (let i = 0; i < weeksAmount; i++) {

        const weekLoads = []
        for (let j = 0; j < 7; j++) {

            const dayLoads: IPlanMatrixDay = []
            let loadPosition    = 0

            plan.forEach(planItem => {
                if (areDatesEqual(workDate, new Date(splitDate(planItem.action_at)))) {
                    // planItem.load_position = ++loadPosition,
                    dayLoads.push(planItem)
                }
            })
            weekLoads.push(dayLoads)
            workDate = additionDays(workDate, 1)
        }
        renderMatrix.push(weekLoads)
    }
    return renderMatrix
}


// ___ Функция, которая возвращает дату без времени
// ___ Например, 2024-01-01 12:00:00 -> 2024-01-01
const splitDate = (date: string) => {
    if (date.includes(' ')) return date.split(' ')[0].trim()
    return date.trim()
}

// ___ Функция, которая проверяет, находится ли дата в периоде
export function ifDateInPeriod(date: Date, period: IPeriod | null): boolean {
    if (!period) return false

    const startDate = new Date(period.start)
    const endDate   = new Date(period.end)

    const timeToCheck = date.getTime()
    const startTime   = startDate.getTime()
    const endTime     = endDate.getTime()

    return timeToCheck >= startTime && timeToCheck <= endTime
}
