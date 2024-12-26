// Здесь все, что касается даты и времени

import {PERIOD_LENGTH} from "@/src/app/constants/dates.js"

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// возвращает отображаемый период производства
export function getPeriod() {
    const tempDate = new Date()
    const day = tempDate.getDate()
    const month = tempDate.getMonth()
    const year = tempDate.getFullYear()

    const periodStart = new Date(year, month, '01')
    periodStart.setMonth(periodStart.getMonth() - 1)        // отнимаем месяц от предыдущего, то есть минус месяц от текущего

    const periodEnd = new Date(year, month , '00')
    periodEnd.setMonth(periodStart.getMonth() + PERIOD_LENGTH)

    const periodStartText_Day = periodStart.getDate() < 10 ? '0' + periodStart.getDate().toString() : periodStart.getDate().toString()
    const periodStartText_Month = periodStart.getMonth() < 9 ? '0' + (periodStart.getMonth() + 1).toString() : (periodStart.getMonth() + 1).toString()

    const periodEndText_Day = periodEnd.getDate() < 10 ? '0' + periodEnd.getDate().toString() : periodEnd.getDate().toString()
    const periodEndText_Month = periodEnd.getMonth() < 9 ? '0' + (periodEnd.getMonth() + 1).toString() : (periodEnd.getMonth() + 1).toString()

    const periodStartText = periodStart.getFullYear().toString() + '-' + periodStartText_Month + '-' + periodStartText_Day
    const periodEndText = periodEnd.getFullYear().toString() + '-' + periodEndText_Month + '-' + periodEndText_Day

    return {periodStart, periodEnd, periodStartText, periodEndText}
}


// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// Возвращает номер недели для указанной даты
export function getWeekNumber(date) {
    // Копируем дату, чтобы не изменять исходную
    const d = new Date(date);
    // Получаем день недели (0 - воскресенье, 6 - суббота)
    const dayNum = d.getDay();
    // Получаем первый день года
    d.setMonth(0, 1);
    const firstDayOfYear = d.getDay();

    // Расчет разницы в днях и округление вверх
    const diffDays = Math.round((date - d) / (1000 * 60 * 60 * 24));

    // Корректировка номера недели в зависимости от системы подсчета
    // Здесь используется система, где первая неделя начинается с понедельника
    let weekNum = Math.ceil((diffDays + firstDayOfYear - 1) / 7);

    // Если первая неделя года начинается с воскресенья, то:
    // weekNum = Math.ceil((diffDays + firstDayOfYear) / 7);

    return weekNum;
}


// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// Определяет, какая дата в текстовом формате больше:
//      если dateString2 > dateString1, то возвращает true
//      если dateString1 > dateString2, то возвращает false
//      если dateString1 = dateString2, то возвращает undefined
export function compareDatesLogic(dateString1, dateString2) {
    // Преобразуем строки в объекты Date
    const date1 = new Date(dateString1);
    const date2 = new Date(dateString2);

    if (date1 === date2) return undefined
    return date2 > date1
}

// Возвращает разницу дат в миллисекундах: dateString2 - dateString1
export function compareDates(dateString1, dateString2) {
    // Преобразуем строки в объекты Date
    const date1 = new Date(dateString1);
    const date2 = new Date(dateString2);

    return date2 - date1
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// const formattedDate = new Date().toISOString().slice(0, 10)  // дата в формате YYYY-MM-DD
