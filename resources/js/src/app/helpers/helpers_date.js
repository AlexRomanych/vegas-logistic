// Здесь все, что касается даты и времени

import {PERIOD_LENGTH} from "@/src/app/constants/dates.js"

const MILLISECONDS_IN_DAY = 24 * 60 * 60 * 1000
const GMT_0 = ':00Z'

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// возвращает отображаемый период производства
export function getPeriod() {
    const tempDate = new Date()
    const day = tempDate.getDate()
    const month = tempDate.getMonth()
    const year = tempDate.getFullYear()

    const periodStart = new Date(year, month, '01')
    periodStart.setMonth(periodStart.getMonth() - 1)        // отнимаем месяц от предыдущего, то есть минус месяц от текущего

    const periodEnd = new Date(year, month, '00')
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
// attract Возвращает номер недели для указанной даты
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
// attract Определяет, какая дата в текстовом формате больше:
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
// attract Вычитает из даты количество дней
// inDate - дата, может быть в формате объекта или строки
export function subtractDays(inDate = new Date(), days = 1) {

    const workDate = (!(inDate instanceof Date) && typeof inDate === 'string') ? new Date(inDate) : inDate
    // if (!inDate instanceof Date && inDate instanceof String) workDate = new Date(inDate)

    // const timeInMs = workDate.getTime()                                 // Получаем текущее время в миллисекундах
    // const timeInMsMinusOneDay = timeInMs - MILLISECONDS_IN_DAY * days   // Вычитаем дни в миллисекундах
    //
    // return new Date(timeInMsMinusOneDay)                                // Создаем новый объект Date с измененным временем

    return new Date(workDate.setDate(workDate.getDate() - days))
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Прибавляет к дате количество дней
// inDate - дата, может быть в формате объекта или строки
export function addDays(inDate = new Date(), days = 1) {

    const workDate = (!(inDate instanceof Date) && typeof inDate === 'string') ? new Date(inDate) : inDate
    // if (!inDate instanceof Date && inDate instanceof String) workDate = new Date(inDate)

    // const timeInMs = workDate.getTime()                                 // Получаем текущее время в миллисекундах
    // const timeInMsMinusOneDay = timeInMs + MILLISECONDS_IN_DAY * days   // Прибавляем дни в миллисекундах
    //
    // return new Date(timeInMsMinusOneDay)                                // Создаем новый объект Date с измененным временем

    return new Date(workDate.setDate(workDate.getDate() + days))
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает ближайший понедельник до заданной даты
// inDate - дата, может быть в формате объекта или строки
export function getMondayBefore(inDate = new Date()) {

    let workDate = (!(inDate instanceof Date) && typeof inDate === 'string') ? new Date(inDate) : inDate

    while ((workDate.getDay()) !== 1) {
        workDate = subtractDays(workDate)
    }

    return workDate
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает ближайшее воскресенье после заданной даты
// inDate - дата, может быть в формате объекта или строки
export function getSundayAfter(inDate = new Date()) {

    let workDate = (!(inDate instanceof Date) && typeof inDate === 'string') ? new Date(inDate) : inDate

    while (workDate.getDay() !== 0) {
        workDate = addDays(workDate)
    }

    return workDate
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает интервал в виде матрицу календаря ([1..x] x [1..7]), где элемент массива - дата
//      между ближайшим понедельником до начальной даты и
//      ближайшим воскресеньем после конечной даты
// inDate - дата, может быть в формате объекта или строки
export function getDateIntervalMatrix(startInterval = new Date(), endInterval = new Date()) {

    console.log(startInterval, endInterval)

    const mondayStart = getMondayBefore(startInterval)
    const sundayEnd = getSundayAfter(endInterval)

    const weeksAmount = (compareDates(mondayStart, sundayEnd) + MILLISECONDS_IN_DAY) / (7 * MILLISECONDS_IN_DAY)    // добавляем 1 день к разности

    // console.log(weeksAmount)

    const intervalMatrix = []

    let workDate = mondayStart

    // не понятно почему, но при создании система прибавляет 1 день. Может из-за часового пояса. Вот тут и убираем его.
    // Это касается только формата объекта Date. Если это формат строки - комментируем
    // workDate = subtractDays(workDate)

    // console.log('workDate', workDate)

    for (let i = 0; i < weeksAmount; i++) {
        // intervalMatrix[i] = []
        intervalMatrix.push([])

        for (let j = 0; j < 7; j++) {
            // intervalMatrix[i].push(workDate)
            intervalMatrix[i].push(workDate.toISOString().slice(0, 10))
            // intervalMatrix[i][j] = workDate
            // console.log('workDate', workDate, i, j, intervallMatrix[i][j])
            // console.log(intervalMatrix[i][j])
            workDate = addDays(workDate)
        }
    }

    return intervalMatrix
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает дату в красивом формате
export function formatDate (inDate = new Date()) {
    return getDate(inDate).toLocaleDateString().slice(0, 10)
}

// const formattedDate = new Date().toISOString().slice(0, 10)  // дата в формате YYYY-MM-DD

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает день недели в строковом представлении
//  short - 'пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'
export function getDayOfWeek (inDate = new Date(), short = true) {
    const workDate = getDate(inDate)

    let result = ''

    switch (workDate.getDay()) {
        case 0:
            result = short ? 'вс' : 'воскресенье'
            break
        case 1:
            result = short ? 'пн' : 'понедельник'
            break
        case 2:
            result = short ? 'вт' : 'вторник'
            break
        case 3:
            result = short ? 'ср' : 'среда'
            break
        case 4:
            result = short ? 'чт' : 'четверг'
            break
        case 5:
            result = short ? 'пт' : 'пятница'
            break
        case 6:
            result = short ? 'сб' : 'суббота'
    }

    return result
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает дату в формате объекта
export function getDate (inDate = new Date()) {
    return !(inDate instanceof Date) && typeof inDate === 'string' ? new Date(inDate) : inDate
}

