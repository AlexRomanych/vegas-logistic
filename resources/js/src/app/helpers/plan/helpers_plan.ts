import type { IPeriod, IPlanLoad, IPlanLoadsMatrix } from '@/types'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import {
    additionDays,
    areDatesEqual,
    formatToYMD,
    getDaysDifference,
    getMondayBefore,
    getSundayAfter
} from '@/app/helpers/helpers_date'


// ___ Функция, которая возвращает период для рендера плана (находит границы данных и расширяет до ПН до и ВС после)
export function getRenderPeriodForPlanLoads(planLoads: IPlanLoad[]): IPeriod {
    const datesArray = getRenderArrayForPlanLoads(planLoads)
    if (datesArray.length === 0) return PERIOD_DRAFT
    return {
        start: formatToYMD(getMondayBefore(datesArray[0])),
        end: formatToYMD(getSundayAfter(datesArray[datesArray.length - 1])),
    }
}

// ___ Функция, которая возвращает матрицу для рендера плана
// ___ Это сортированный массив дат, на которые нужно отрисовать план
export function getRenderArrayForPlanLoads(planLoads: IPlanLoad[]): Date[] {
    const datesSet = new Set(planLoads.map(planLoad => splitDate(planLoad.load_at)))
    const datesArray = Array.from(datesSet).map(date => new Date(date)).sort((a, b) => a.getTime() - b.getTime())
    return datesArray
}


// ___ Возвращает матрицу для рендера плана
export function getRenderMatrixForPlanLoads(planLoads: IPlanLoad[], renderPeriod: IPeriod): IPlanLoadsMatrix {

    const daysDifference = getDaysDifference(renderPeriod.start, renderPeriod.end)
    const weeksAmount = (daysDifference + 1) / 7

    const renderMatrix = []
    let workDate = new Date(renderPeriod.start)

    for (let i = 0; i < weeksAmount; i++) {

        const weekLoads = []
        for (let j = 0; j < 7; j++) {

            const dayLoads: IPlanLoad[] = []
            let loadPosition = 0

            planLoads.forEach(planLoad => {
                if (areDatesEqual(workDate, new Date(splitDate(planLoad.load_at)))) {
                    planLoad.load_position = ++loadPosition,
                    dayLoads.push(planLoad)
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
export function ifDateInPeriod (date: Date, period: IPeriod | null): boolean {
    if (!period) return false

    const startDate = new Date(period.start)
    const endDate = new Date(period.end)

    const timeToCheck = date.getTime()
    const startTime = startDate.getTime()
    const endTime = endDate.getTime()

    return timeToCheck >= startTime && timeToCheck <= endTime
}
