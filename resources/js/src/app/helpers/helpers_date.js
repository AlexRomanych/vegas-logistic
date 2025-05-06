// Здесь все, что касается даты и времени

import {PERIOD_LENGTH} from '/resources/js/src/app/constants/dates.js'

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
    const date1 = getDate(dateString1)
    const date2 = getDate(dateString2)

    if (date1.toISOString().slice(0, 10) === date2.toISOString().slice(0, 10)) return undefined
    return date2 > date1
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает разницу дат в миллисекундах: dateString2 - dateString1
export function compareDates(dateString1, dateString2) {
    // Преобразуем строки в объекты Date
    const date1 = getDate(dateString1)
    const date2 = getDate(dateString2)

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
export function formatDate(inDate = new Date()) {
    return getDate(inDate).toLocaleDateString().slice(0, 10)
}

// const formattedDate = new Date().toISOString().slice(0, 10)  // дата в формате YYYY-MM-DD

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает перевод даты в формате "2025-04-17 20:58:57" в формат "17 апреля 2025 года"
export function formatDateInFullFormat(dateTimeString) {
    const date = new Date(dateTimeString);
    const day = date.getDate();
    const monthIndex = date.getMonth();
    const year = date.getFullYear();

    const months = [
        "января", "февраля", "марта", "апреля", "мая", "июня",
        "июля", "августа", "сентября", "октября", "ноября", "декабря"
    ];

    const pad = (num) => num.toString().padStart(2, '0')

    return `${pad(day)} ${months[monthIndex]} ${year} года`;
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает перевод даты в формате "2025-04-17 20:58:57" в нормальный формат "17.04.2025 20:58:57"
export function formatDateAndTimeInShortFormat(dateTimeString) {
    const dateObject = new Date(dateTimeString);

    const day = dateObject.getDate();
    const month = dateObject.getMonth();
    const year = dateObject.getFullYear();

    const hours = dateObject.getHours();
    const minutes = dateObject.getMinutes();
    const seconds = dateObject.getSeconds();

    const pad = (num) => num.toString().padStart(2, '0')

    return `${pad(day)}.${pad(month + 1)}.${year} ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает перевод даты в формате "2025-04-17 20:58:57" в формат времени "20ч. 58м. 57с."
export function formatTimeInFullFormat(dateTimeString) {

    const dateObject = new Date(dateTimeString);

    const hours = dateObject.getHours();        // Получить часы (0-23)
    const minutes = dateObject.getMinutes();    // Получить минуты (0-59)
    const seconds = dateObject.getSeconds();    // Получить секунды (0-59)

    const pad = (num) => num.toString().padStart(2, '0')

    // console.log(`Часы: ${hours}, Минуты: ${minutes}, Секунды: ${seconds}`); // Часы: 20, Минуты: 58, Секунды: 57
    return `${pad(hours)}ч. ${pad(minutes)}м. ${pad(seconds)}с.` // Часы: 20, Минуты: 58, Секунды: 57

// Для получения времени в формате HH:MM:SS:
    const timeString = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    console.log(timeString); // Выведет: 20:58:57

}


// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает день недели в строковом представлении
//  short - 'пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'
export function getDayOfWeek(inDate = new Date(), short = true) {
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
// attract Возвращает, является ли день выходным или рабочим
export function isWorkingDay(inDate = new Date()) {

    const BLACK_DAYS = [
        "04.01.2020", "04.04.2020",
        "16.01.2021", "15.05.2021", "15.09.2021",
        "12.03.2022", "14.05.2022",
        "29.04.2023", "13.05.2023", "29.04.2023", "11.11.2023",
        "18.05.2024", "16.11.2024",
        "11.01.2025", "26.04.2025", "12.07.2025", "20.12.2025"
    ]

    const RED_DAYS = [
        "01.01.2020", "02.01.2020", "06.01.2020", "07.01.2020", "27.04.2020", "28.04.2020", "01.05.2020", "03.07.2020", "25.12.2020",
        "01.01.2021", "07.01.2021", "08.01.2021", "08.03.2021", "10.05.2021", "11.05.2021",
        "07.01.2022", "07.03.2022", "08.03.2022", "02.05.2022", "03.05.2022", "09.05.2022", "07.11.2022",
        "02.01.2023", "08.03.2023", "24.04.2023", "25.04.2023", "01.05.2023", "08.05.2023", "09.05.2023", "03.07.2023", "06.11.2023", "07.11.2023", "25.12.2023",
        "01.01.2024", "02.01.2024", "08.03.2024", "01.05.2024", "09.05.2024", "13.05.2024", "14.05.2024", "03.07.2024", "07.11.2024", "08.11.2024", "25.12.2024",
        "01.01.2025", "02.01.2025", "06.01.2025", "07.01.2025", "28.04.2025", "29.04.2025", "01.05.2025", "09.05.2025", "03.07.2025", "04.07.2025", "07.11.2025", "25.12.2025", "26.12.2025"
    ]

    const workDate = getDate(inDate)

    if (workDate.getDay() === 0 || workDate.getDay() === 6) {
        return BLACK_DAYS.includes(workDate.toLocaleDateString().slice(0, 10))
    }

    return !RED_DAYS.includes(workDate.toLocaleDateString().slice(0, 10))
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает, является ли дата текущей
export function isToday(inDate = new Date()) {
    return getDate(inDate).toLocaleDateString() === new Date().toLocaleDateString()
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает дату в формате объекта
export function getDate(inDate = new Date()) {
    return !(inDate instanceof Date) && typeof inDate === 'string' ? new Date(inDate) : inDate
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает дату в формате "10ч. 59м. 59с."
// attract type - sec, min, hour - входящие данные: секунды, минуты, часы
export function formatTimeWithLeadingZeros(inTime = 0, type = 'sec') {

    let tempTime = inTime

    if (type === 'min') {
        tempTime = Math.round(tempTime * 60, 0)
    } else if (type === 'hour') {
        tempTime = Math.round(tempTime * 60 * 60, 0)
    }

    const hours = Math.floor(tempTime / 3600)
    const minutes = Math.floor((tempTime % 3600) / 60)
    const secs = tempTime % 60

    const pad = (num) => num.toString().padStart(2, '0')

    if (hours === 0 && minutes === 0 && secs === 0) return '00с.'
    if (hours === 0 && minutes === 0) return `${pad(secs)}с.`
    if (hours === 0) return `${pad(minutes)}м. ${pad(secs)}с.`

    return `${pad(hours)}ч. ${pad(minutes)}м. ${pad(secs)}с.`
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract: Возвращает разницу между двумя датами в днях
// attract: absMode - модуль или нет,
// attract: truncMode - отсекать ли дробную часть или нет
export function getDateDiffInDays(inDate1 = new Date(), inDate2 = new Date(), absMode = false, truncMode = true) {

    const MS_IN_DAY = 1000 * 60 * 60 * 24

    const date1 = getDate(inDate1)
    const date2 = getDate(inDate2)

    const diffInMs = date2 - date1

    // console.log('diffInMs', diffInMs)

    if (absMode) diffInMs = Math.abs(date2 - date1)

    if (truncMode) return Math.trunc(diffInMs / MS_IN_DAY)

    // console.log('diffInDays', diffInMs / MS_IN_DAY)

    return diffInMs / MS_IN_DAY
}


// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract: Возвращает длительность периода между двумя датами в формате "10ч. 59м. 59с."
// attract: если вторая дата не указана, то вычисляется разница между текущей и первой датой
// attract: если даты не указаны, то результат вычисляется только по смещению в миллисекундах
// attract: offset - добавляет или отнимает указанное количество миллисекунд к результату
export function getDuration(startMoment = null, endMoment = null, offset = 0) {

    if (!startMoment && !endMoment && !offset) return ''    // если все параметры не указаны, возвращаем пустую строку
    if (!startMoment && !offset) return ''                  // если нет начала периода и нет смещения, возвращаем пустую строку
    if (!offset) offset = 0

    let tempDuration = 0

    if (startMoment && endMoment) {
        tempDuration = new Date(endMoment).getTime() - new Date(startMoment).getTime()
    } else if (startMoment && !endMoment) {
        tempDuration = new Date().getTime() - new Date(startMoment).getTime()
    }

    tempDuration += offset

    tempDuration = Math.floor(tempDuration / 1000)
    return formatTimeWithLeadingZeros(tempDuration)
}


