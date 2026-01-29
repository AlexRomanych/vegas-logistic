// Здесь все, что касается даты и времени

import { PERIOD_LENGTH } from '/resources/js/src/app/constants/dates.js'

const MILLISECONDS_IN_DAY = 24 * 60 * 60 * 1000
const GMT_0               = ':00Z'

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// возвращает отображаемый период производства
export function getPeriod() {
    const tempDate = new Date()
    const day      = tempDate.getDate()
    const month    = tempDate.getMonth()
    const year     = tempDate.getFullYear()

    const periodStart = new Date(year, month, '01')
    periodStart.setMonth(periodStart.getMonth() - 1) // отнимаем месяц от предыдущего, то есть минус месяц от текущего

    const periodEnd = new Date(year, month, '00')
    periodEnd.setMonth(periodStart.getMonth() + PERIOD_LENGTH)

    const periodStartText_Day   =
              periodStart.getDate() < 10
                  ? '0' + periodStart.getDate().toString()
                  : periodStart.getDate().toString()
    const periodStartText_Month =
              periodStart.getMonth() < 9
                  ? '0' + (periodStart.getMonth() + 1).toString()
                  : (periodStart.getMonth() + 1).toString()

    const periodEndText_Day   =
              periodEnd.getDate() < 10
                  ? '0' + periodEnd.getDate().toString()
                  : periodEnd.getDate().toString()
    const periodEndText_Month =
              periodEnd.getMonth() < 9
                  ? '0' + (periodEnd.getMonth() + 1).toString()
                  : (periodEnd.getMonth() + 1).toString()

    const periodStartText =
              periodStart.getFullYear().toString() +
              '-' +
              periodStartText_Month +
              '-' +
              periodStartText_Day
    const periodEndText   =
              periodEnd.getFullYear().toString() + '-' + periodEndText_Month + '-' + periodEndText_Day

    return { periodStart, periodEnd, periodStartText, periodEndText }
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает номер недели для указанной даты
export function getWeekNumber(date) {
    // Копируем дату, чтобы не изменять исходную
    const d      = new Date(date)
    // Получаем день недели (0 - воскресенье, 6 - суббота)
    const dayNum = d.getDay()
    // Получаем первый день года
    d.setMonth(0, 1)
    const firstDayOfYear = d.getDay()

    // Расчет разницы в днях и округление вверх
    const diffDays = Math.round((date - d) / (1000 * 60 * 60 * 24))

    // Корректировка номера недели в зависимости от системы подсчета
    // Здесь используется система, где первая неделя начинается с понедельника
    let weekNum = Math.ceil((diffDays + firstDayOfYear - 1) / 7)

    // Если первая неделя года начинается с воскресенья, то:
    // weekNum = Math.ceil((diffDays + firstDayOfYear) / 7);

    return weekNum
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

    // console.log('date1: ', date1)
    // console.log('date2: ', date2)

    if (getISOFromLocaleDate(date1) === getISOFromLocaleDate(date2)) return undefined
    // if (date1.toISOString().slice(0, 10) === date2.toISOString().slice(0, 10)) return undefined
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
    const workDate =
              !(inDate instanceof Date) && typeof inDate === 'string' ? new Date(inDate) : inDate
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
    const workDate =
              !(inDate instanceof Date) && typeof inDate === 'string' ? new Date(inDate) : inDate
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
    let workDate = !(inDate instanceof Date) && typeof inDate === 'string' ? new Date(inDate) : inDate

    while (workDate.getDay() !== 1) {
        workDate = subtractDays(workDate)
    }

    return workDate
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает ближайшее воскресенье после заданной даты
// inDate - дата, может быть в формате объекта или строки
export function getSundayAfter(inDate = new Date()) {
    let workDate = !(inDate instanceof Date) && typeof inDate === 'string' ? new Date(inDate) : inDate

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
    const sundayEnd   = getSundayAfter(endInterval)

    const weeksAmount =
              (compareDates(mondayStart, sundayEnd) + MILLISECONDS_IN_DAY) / (7 * MILLISECONDS_IN_DAY) // добавляем 1 день к разности

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
// __ Возвращает дату в красивом формате
/**
 *
 * @param {string | Date} inDate
 * @returns {string}
 */
export function formatDate(inDate = new Date()) {
    return getDate(inDate).toLocaleDateString().slice(0, 10)
}

// const formattedDate = new Date().toISOString().slice(0, 10)  // дата в формате YYYY-MM-DD

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает перевод даты в формате "2025-04-17 20:58:57" в формат "17 апреля 2025 года"
export function formatDateInFullFormat(dateTimeString, monthShort = false, fullYearLabel = true) {
    const date       = new Date(dateTimeString)
    const day        = date.getDate()
    const monthIndex = date.getMonth()
    const year       = date.getFullYear()

    const monthsArray = [
        'января',
        'февраля',
        'марта',
        'апреля',
        'мая',
        'июня',
        'июля',
        'августа',
        'сентября',
        'октября',
        'ноября',
        'декабря',
    ]

    const monthsArrayShorts = ['янв.', 'фев.', 'мар.', 'апр.', 'мая', 'июн.', 'июл.', 'авг.', 'сен.', 'окт.', 'ноя.', 'дек.']

    const pad = (num) => num.toString().padStart(2, '0')

    let fullYearText = ''
    if (fullYearLabel !== null) {
        fullYearText = fullYearLabel ? 'года' : 'г.'
    }

    const returnString = monthShort ?
        `${pad(day)} ${monthsArrayShorts[monthIndex]} ${year} ${fullYearText}` :
        `${pad(day)} ${monthsArray[monthIndex]} ${year} ${fullYearText}`

    return returnString.trim()
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает перевод даты в формате "2025-04-17 20:58:57" в нормальный формат "17.04.2025 20:58:57"
export function formatDateAndTimeInShortFormat(dateTimeString, fullYear = true) {
    if (!dateTimeString) return ''

    const dateObject = new Date(dateTimeString)

    const day   = dateObject.getDate()
    const month = dateObject.getMonth()
    const year  = fullYear ? dateObject.getFullYear() : dateObject.getFullYear().toString().slice(2)

    const hours   = dateObject.getHours()
    const minutes = dateObject.getMinutes()
    const seconds = dateObject.getSeconds()

    const pad = (num) => num.toString().padStart(2, '0')

    return `${pad(day)}.${pad(month + 1)}.${year} ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает дату из формата "2025-04-17 20:58:57" в нормальный формат "17.04.2025"
export function getDateFromDateTimeString(dateTimeString, fullYear = true) {

    if (!dateTimeString) return ''

    // dateTimeString = getDate(dateTimeString)

    // const dateObject = new Date(dateTimeString)
    const dateObject = getDate(dateTimeString)

    const day   = dateObject.getDate()
    const month = dateObject.getMonth()
    const year  = fullYear ? dateObject.getFullYear() : dateObject.getFullYear().toString().slice(2)

    // const hours = dateObject.getHours();
    // const minutes = dateObject.getMinutes();
    // const seconds = dateObject.getSeconds();

    const pad = (num) => num.toString().padStart(2, '0')

    return `${pad(day)}.${pad(month + 1)}.${year}`
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает перевод даты в формате "2025-04-17 20:58:57" в формат времени "20ч. 58м. 57с."
export function formatTimeInFullFormat(dateTimeString) {
    const dateObject = new Date(dateTimeString)

    const hours   = dateObject.getHours() // Получить часы (0-23)
    const minutes = dateObject.getMinutes() // Получить минуты (0-59)
    const seconds = dateObject.getSeconds() // Получить секунды (0-59)

    const pad = (num) => num.toString().padStart(2, '0')

    // console.log(`Часы: ${hours}, Минуты: ${minutes}, Секунды: ${seconds}`); // Часы: 20, Минуты: 58, Секунды: 57
    return `${pad(hours)}ч. ${pad(minutes)}м. ${pad(seconds)}с.` // Часы: 20, Минуты: 58, Секунды: 57

    // Для получения времени в формате HH:MM:SS:
    const timeString = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
    console.log(timeString) // Выведет: 20:58:57
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает день недели в строковом представлении
//  short - 'пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'
/**
 *
 * @param {string | Date} inDate
 * @param short
 * @returns {string}
 */
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
        '04.01.2020', '04.04.2020',
        '16.01.2021', '15.05.2021', '15.09.2021', '12.03.2022',
        '14.05.2022',
        '29.04.2023', '13.05.2023', '29.04.2023', '11.11.2023',
        '18.05.2024', '16.11.2024',
        '11.01.2025', '26.04.2025', '12.07.2025', '20.12.2025',
        '25.04.2026',
    ]

    const RED_DAYS = [
        '01.01.2020', '02.01.2020', '06.01.2020', '07.01.2020', '27.04.2020', '28.04.2020', '01.05.2020', '03.07.2020', '25.12.2020',
        '01.01.2021', '07.01.2021', '08.01.2021', '08.03.2021', '10.05.2021', '11.05.2021',
        '07.01.2022', '07.03.2022', '08.03.2022', '02.05.2022', '03.05.2022', '09.05.2022', '07.11.2022',
        '02.01.2023', '08.03.2023', '24.04.2023', '25.04.2023', '01.05.2023', '08.05.2023', '09.05.2023', '03.07.2023', '06.11.2023', '07.11.2023', '25.12.2023',
        '01.01.2024', '02.01.2024', '08.03.2024', '01.05.2024', '09.05.2024', '13.05.2024', '14.05.2024', '03.07.2024', '07.11.2024', '08.11.2024', '25.12.2024',
        '01.01.2025', '02.01.2025', '06.01.2025', '07.01.2025', '28.04.2025', '29.04.2025', '01.05.2025', '09.05.2025', '03.07.2025', '04.07.2025', '07.11.2025', '25.12.2025', '26.12.2025',
        '01.01.2026', '02.01.2026', '07.01.2026', '08.03.2026', '21.04.2026', '01.05.2026', '09.05.2026', '03.07.2026', '07.11.2026', '25.12.2026',
    ]

    const workDate = getDate(inDate)

    if (workDate.getDay() === 0 || workDate.getDay() === 6) {
        return BLACK_DAYS.includes(workDate.toLocaleDateString().slice(0, 10))
    }

    return !RED_DAYS.includes(workDate.toLocaleDateString().slice(0, 10))
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает, является ли день выходным или рабочим
/**
 * @param {Date} inDate
 * @returns {boolean}
 */
export function isHoliday(inDate = new Date()) {
    return !isWorkingDay(inDate)
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
// attract type - sec, min, hour - вид входящих данных: секунды, минуты, часы
export function formatTimeWithLeadingZeros(inTime = 0, type = 'sec') {
    let tempTime = inTime

    if (type === 'min') {
        tempTime = Math.round(tempTime * 60, 0)
    } else if (type === 'hour') {
        tempTime = Math.round(tempTime * 60 * 60, 0)
    }

    const hours   = Math.floor(tempTime / 3600)
    const minutes = Math.floor((tempTime % 3600) / 60)
    const secs    = tempTime % 60

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
export function getDateDiffInDays(
    inDate1   = new Date(),
    inDate2   = new Date(),
    absMode   = false,
    truncMode = true,
) {
    const MS_IN_DAY = 1000 * 60 * 60 * 24

    const date1 = getDate(inDate1)
    const date2 = getDate(inDate2)

    let diffInMs = date2 - date1

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
    if (!startMoment && !endMoment && !offset) return '' // если все параметры не указаны, возвращаем пустую строку
    if (!startMoment && !offset) return '' // если нет начала периода и нет смещения, возвращаем пустую строку
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

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// __ Возвращает дату в формате YYYY-MM-DD, но полученную из локального формата (без -03:00))
export function getISOFromLocaleDate(inDate) {
    inDate = inDate ? getDate(inDate) : new Date()
    return inDate.toLocaleDateString().split('.').reverse().join('-')
}

// __ Получаем тип раскраски в зависимости от типа дня недели (выходной или рабочий или текущий)
export function getDayOfWeekStyle(date) {
    if (isToday(date)) return 'success' // текущий
    if (isWorkingDay(date)) return 'dark' // рабочмй
    return 'danger' // выходной
}


// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// __ Проверяет реактивный ввод даты на корректность + корректирует формат ввода
// __ Получает на вход строку с датой и возвращает проверенную строку с датой
/**
 *
 * @param {IDataInputObj | null} input
 * @returns {string}
 */
export function validateInputDateHelper(input = null) {

    if (!input) {
        return ''
    }

    const workInput = JSON.parse(JSON.stringify(input))

    // console.log('input: ', workInput.newValue, workInput.newValue.length)
    // console.log('oldInput: ', workInput.oldValue, workInput.oldValue.length)
    // console.log()

    const direction = workInput.newValue.length > workInput.oldValue.length    // для проверки на ввод или удаление, true - ввод, false - удаление

    const ifDigit = (char) => ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'].includes(char)

    if (input.newValue.length === 0) {

        input.newValue = ''
        input.oldValue = input.newValue

    } else if (input.newValue.length === 1) {  // ?d.mm.yyyy // Если длина строки равна 1, то проверяем, является ли символ цифрой от 0 до 3 (dd)

        if (['0', '1', '2', '3'].includes(input.newValue)) {
            input.oldValue = input.newValue
        } else {
            input.newValue = ''
            input.oldValue = input.newValue
        }

    } else if (input.newValue.length === 2) {   // d?.mm.yyyy

        // удаляем символ
        if (!direction) {
            input.newValue = input.newValue[0]
            input.oldValue = input.newValue
            return
        }

        // добавляем
        if (ifDigit(input.newValue[1])) {
            input.newValue += '.'
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 3) {   // dd?mm.yyyy

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

    } else if (input.newValue.length === 4) {   // dd.?m.yyyy

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

        if (['0', '1'].includes(input.newValue[3])) {
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 5) { // dd.m?.yyyy

        // удаляем символ
        if (!direction) {
            input.newValue = input.newValue.substring(0, 4)
            input.oldValue = input.newValue
            return
        }

        if ((input.newValue[3] === '0' && ifDigit(input.newValue[4])) ||
            (input.newValue[3] === '1' && ['0', '1', '2'].includes(input.newValue[4]))) {   // mm: 01-12
            input.newValue += '.'
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 6) { // dd.mm?yyyy

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

    } else if (input.newValue.length === 7) { // dd.mm.?yyy

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

        if (input.newValue[6] === '2') {
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 8) { // dd.mm.y?yy

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

        if (input.newValue[7] === '0') {
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 9) { // dd.mm.yy?y

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

        if (['2', '3'].includes(input.newValue[8])) {
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 10) { // dd.mm.yy?y

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

        if ((input.newValue[8] === '2') && ['5', '6', '7', '8', '9'].includes(input.newValue[9]) ||
            (input.newValue[8] === '3') && ifDigit(input.newValue[9])) { // yyyy: 2025-2039
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }
    }

    input.newValue = input.oldValue
}

export function validateInputDateHelper_new(input = null) {

    if (!input) {
        return ''
    }

    const workInput = JSON.parse(JSON.stringify(input))

    // console.log('input: ', workInput.newValue, workInput.newValue.length)
    // console.log('oldInput: ', workInput.oldValue, workInput.oldValue.length)
    // console.log()

    const direction = workInput.newValue.length > workInput.oldValue.length    // для проверки на ввод или удаление, true - ввод, false - удаление

    const ifDigit = (char) => ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'].includes(char)

    console.log('direction: ', direction)

    // __ Удаление символа
    if (!direction) {
        if ([4, 7].includes(input.oldValue.length)) {
            input.newValue = input.newValue.slice(0, -1)
        }
        input.oldValue = input.newValue
        return
        // if (input.newValue.length === 0) {
        //
        //     input.newValue = ''
        //     input.oldValue = input.newValue
        //     return
        // }
        //
        //
        // if ([10, 9, 8, 5, 2].includes(input.oldValue.length)) {
        //     input.oldValue = input.newValue
        //     return
        // }
        //
        // result = str.slice(0, -2)
    }


    if (input.newValue.length === 0) {

        input.newValue = ''
        input.oldValue = input.newValue

    } else if (input.newValue.length === 1) {  // ?d.mm.yyyy // Если длина строки равна 1, то проверяем, является ли символ цифрой от 0 до 3 (dd)

        if (['0', '1', '2', '3'].includes(input.newValue)) {
            input.oldValue = input.newValue
        } else {
            input.newValue = ''
            input.oldValue = input.newValue
        }

    } else if (input.newValue.length === 2) {   // d?.mm.yyyy

        // удаляем символ
        if (!direction) {
            input.newValue = input.newValue[0]
            input.oldValue = input.newValue
            return
        }

        // добавляем
        if (ifDigit(input.newValue[1])) {
            input.newValue += '.'
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 3) {   // dd?mm.yyyy

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

    } else if (input.newValue.length === 4) {   // dd.?m.yyyy

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

        if (['0', '1'].includes(input.newValue[3])) {
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 5) { // dd.m?.yyyy

        // удаляем символ
        if (!direction) {
            input.newValue = input.newValue.substring(0, 4)
            input.oldValue = input.newValue
            return
        }

        if ((input.newValue[3] === '0' && ifDigit(input.newValue[4])) ||
            (input.newValue[3] === '1' && ['0', '1', '2'].includes(input.newValue[4]))) {   // mm: 01-12
            input.newValue += '.'
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 6) { // dd.mm?yyyy

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

    } else if (input.newValue.length === 7) { // dd.mm.?yyy

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

        if (input.newValue[6] === '2') {
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 8) { // dd.mm.y?yy

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

        if (input.newValue[7] === '0') {
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 9) { // dd.mm.yy?y

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

        if (['2', '3'].includes(input.newValue[8])) {
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }

    } else if (input.newValue.length === 10) { // dd.mm.yy?y

        // удаляем символ
        if (!direction) {
            input.oldValue = input.newValue
            return
        }

        if ((input.newValue[8] === '2') && ['5', '6', '7', '8', '9'].includes(input.newValue[9]) ||
            (input.newValue[8] === '3') && ifDigit(input.newValue[9])) { // yyyy: 2025-2039
            input.oldValue = input.newValue
        } else {
            input.newValue = input.oldValue
        }
    }

    input.newValue = input.oldValue
}


/**
 * ___ Функция для преобразования объекта Date в строку формата YYYY-MM-DD
 * ___ в ЛОКАЛЬНОМ часовом поясе.
 * @param {Date} date - Исходный объект Date.
 * @returns {string} - Отформатированная строка (YYYY-MM-DD).
 */
export function formatToYMD(date) {
    if (!(date instanceof Date) || isNaN(date.getTime())) {
        return ''
    }
    const year  = date.getFullYear()
    const month = date.getMonth() + 1
    const day   = date.getDate()

    const pad = (num) => num.toString().padStart(2, '0')
    return `${year}-${pad(month)}-${pad(day)}`
}


// --- ПРИМЕРЫ ИСПОЛЬЗОВАНИЯ ---
// const today = new Date();
// const specificDate = new Date('2026-04-05T10:30:00'); // Апрель, 5-е
// const singleDigitMonth = new Date('2025-01-09'); // Январь, 9-е
//
// console.log(`Текущая дата (локально): ${formatToYMD(today)}`);
// console.log(`Конкретная дата: ${formatToYMD(specificDate)}`);
// console.log(`Дата с нулями: ${formatToYMD(singleDigitMonth)}`);
// console.log(`Невалидная дата: ${formatToYMD(new Date('abc'))}`);


// -------------------------------------------------------------------
// --- АЛЬТЕРНАТИВНЫЙ СПОСОБ: Использование toISOString() (в UTC) ---
// -------------------------------------------------------------------

/**
 * ___ Этот метод быстрый, но всегда возвращает дату в формате UTC.
 * ___ Если текущее локальное время смещает вас на следующий день UTC,
 * ___ результат будет отличаться.
 */
export function formatToYMD_UTC(date) {
    // toISOString() возвращает "YYYY-MM-DDTHH:mm:ss.sssZ"
    // Срез [0, 10] дает "YYYY-MM-DD"
    return date.toISOString().slice(0, 10)
}


/**
 * ___ Функция для вычисления разницы в днях между двумя датами,
 * ___ представленными в виде строк ("YYYY-MM-DD").
 * ___ * ВАЖНО: При парсинге строк "YYYY-MM-DD" Date() по умолчанию
 * ___ интерпретирует их как время в UTC (полночь), что устраняет
 * ___ проблемы с локальным часовым поясом и летним временем при сравнении.
 * @param {string} dateString1 - Первая дата в формате YYYY-MM-DD.
 * @param {string} dateString2 - Вторая дата в формате YYYY-MM-DD.
 * @returns {number} - Количество полных дней между датами (целое число).
 */
export function getDaysDifference(dateString1, dateString2) {
    // Константы для преобразования
    const MS_PER_SECOND      = 1000
    const SECONDS_PER_MINUTE = 60
    const MINUTES_PER_HOUR   = 60
    const HOURS_PER_DAY      = 24

    // Общее количество миллисекунд в одном дне
    const MS_PER_DAY = MS_PER_SECOND * SECONDS_PER_MINUTE * MINUTES_PER_HOUR * HOURS_PER_DAY

    // 1. Преобразование строк в объекты Date
    // Парсинг строки "YYYY-MM-DD" в JS интерпретируется как UTC,
    // что предотвращает смещение из-за локального часового пояса.
    const date1 = new Date(dateString1)
    const date2 = new Date(dateString2)

    // Проверка на корректность парсинга
    if (isNaN(date1.getTime()) || isNaN(date2.getTime())) {
        console.error("Ошибка: Одна из дат невалидна.")
        return NaN
    }

    // 2. Вычисление разницы в миллисекундах
    // Используем Math.abs(), чтобы разница всегда была положительной, независимо от порядка ввода дат.
    const diffInMilliseconds = Math.abs(date1.getTime() - date2.getTime())

    // 3. Преобразование миллисекунд в дни. Используем Math.floor() для округления до меньшего целого, чтобы получить количество полных дней.
    const diffInDays = Math.floor(diffInMilliseconds / MS_PER_DAY)

    return diffInDays
}


/**
 * ___ Функция для проверки равенства ДАТ (год, месяц, день) двух объектов Date,
 * ___ игнорируя время и часовой пояс.
 * @param {Date} date1 - Первый объект Date.
 * @param {Date} date2 - Второй объект Date.
 * @returns {boolean} - true, если даты равны.
 */
export function areDatesEqual(date1, date2) {
    // 1. Проверяем, что оба аргумента являются валидными объектами Date
    const isValidDate = (d) => d instanceof Date && !isNaN(d.getTime())

    if (!isValidDate(date1) || !isValidDate(date2)) {
        // console.error("Ошибка: Один или оба аргумента не являются валидными объектами Date.")
        return false
    }

    return date1.getFullYear() === date2.getFullYear() &&
        date1.getMonth() === date2.getMonth() &&
        date1.getDate() === date2.getDate()

    // --- АЛЬТЕРНАТИВНЫЙ МЕТОД: Сравнение строкового представления в UTC ---
    /*
    const dateString1 = date1.toISOString().slice(0, 10);
    const dateString2 = date2.toISOString().slice(0, 10);

    return dateString1 === dateString2;
    */
}


/**
 * ___ Функция для прибавления указанного количества дней к дате.
 * ___ ВАЖНО!!!: Функция возвращает НОВЫЙ объект Date, не изменяя исходный.
 * @param {Date} originalDate - Исходный объект Date.
 * @param {number} days - Количество дней для прибавления (положительное или отрицательное).
 * @returns {Date} - Новый объект Date с измененной датой.
 */
export function additionDays(originalDate, days) {
    // 1. Создаем копию исходной даты, чтобы не изменять оригинал
    const newDate = new Date(originalDate.getTime())

    // 2. Получаем текущий день месяца
    const currentDay = newDate.getDate()

    // 3. Используем setDate(): Установка нового дня, который автоматически обрабатывает переходы на новый месяц/год.
    newDate.setDate(currentDay + days)

    return newDate
}


/**
 * ___ **Функция для изменения даты на N дней для формата "YYYY-MM-DD HH:mm:ss"**
 * @param {string} dateStr  - __Исходная дата "YYYY-MM-DD HH:mm:ss"__
 * @param {number} days     - __Количество дней (положительное или отрицательное)__
 * @returns {string}        - __Новая дата в том же формате__
 */
export function additionDaysInStrFormat(dateStr, days) {

    // 1. Разбиваем строку на части: "2026-01-13" и "00:00:00"
    let [datePart, timePart] = dateStr.split(' ');

    if (!timePart) timePart = '00:00:00'    // если нет времени, то добавляем

    const [year, month, day] = datePart.split('-').map(Number);
    const [hours, minutes, seconds] = timePart.split(':').map(Number);

    // 2. Создаем дату, передавая числа (месяцы в JS начинаются с 0, поэтому month - 1)
    // Конструктор new Date(year, month, day...) всегда работает в локальном времени
    const date = new Date(year, month - 1, day, hours, minutes, seconds);

    // 3. Прибавляем дни
    date.setDate(date.getDate() + days);

    // 4. Форматируем обратно
    const pad = (num) => String(num).padStart(2, '0');

    const YYYY = date.getFullYear();
    const MM   = pad(date.getMonth() + 1);
    const DD   = pad(date.getDate());
    const HH   = pad(date.getHours());
    const mm   = pad(date.getMinutes());
    const ss   = pad(date.getSeconds());

    return `${YYYY}-${MM}-${DD} ${HH}:${mm}:${ss}`;


    //
    // // console.log(dateStr, days)
    //
    // // 1. Создаем объект даты.
    // // Чтобы избежать сюрпризов, работаем с ней как с UTC
    // const date = new Date(dateStr.replace(' ', 'T'))
    //
    // // 2. Прибавляем/отнимаем дни
    // date.setDate(date.getDate() + days)
    // // date.setUTCDate(date.getUTCDate() + days)
    //
    // // 3. Форматируем обратно, используя UTC методы
    // const pad = (num) => String(num).padStart(2, '0')
    //
    // // Используем getUTC..., чтобы получить ровно те цифры, которые лежат в объекте
    // const YYYY = date.getFullYear()
    // const MM   = pad(date.getMonth() + 1)
    // const DD   = pad(date.getDate())
    // const HH   = pad(date.getHours())
    // const mm   = pad(date.getMinutes())
    // const ss   = pad(date.getSeconds())
    //
    // // const YYYY = date.getUTCFullYear()
    // // const MM   = pad(date.getUTCMonth() + 1)
    // // const DD   = pad(date.getUTCDate())
    // // const HH   = pad(date.getUTCHours())
    // // const mm   = pad(date.getUTCMinutes())
    // // const ss   = pad(date.getUTCSeconds())
    //
    // const result = `${YYYY}-${MM}-${DD} ${HH}:${mm}:${ss}`
    // // console.log(result);
    // return result
}


/**
 * ___ Возвращает слово "день" в правильной падежной форме в зависимости от числа.
 * ___Корректно склоняет для произвольного целого числа.
 * @param count
 * @returns
 */
export function formatDayString(count) {

    let dayWord = ''
    if (!Number.isInteger(count)) {
        return dayWord
        // throw new RangeError("Число должно быть целым и неотрицательным.");
    }

    count = Math.abs(count) // Возвращаем абсолютное значение

    // Специальное правило для чисел, заканчивающихся на 11, 12, 13, 14
    // Эти числа всегда требуют Родительного падежа множественного числа ("дней")
    if (count % 100 >= 11 && count % 100 <= 14) {
        dayWord = 'дней'
    } else {
        // Анализ последней цифры (для всех остальных случаев)
        const remainder = count % 10

        if (remainder === 1) {
            // Заканчивается на 1: Именительный падеж, единственное число ("день")
            // Например: 1, 21, 101...
            dayWord = 'день'
        } else if (remainder >= 2 && remainder <= 4) {
            // Заканчивается на 2, 3, 4: Родительный падеж, единственное число ("дня")
            // Например: 2, 22, 104...
            dayWord = 'дня'
        } else {
            // Заканчивается на 0, 5, 6, 7, 8, 9: Родительный падеж, множественное число ("дней")
            // Например: 0, 5, 20, 109...
            dayWord = 'дней'
        }
    }

    return dayWord
    // return `${count} ${dayWord}`;
}

// ___ Возвращаем дату в форматированном виде по русскую локализацию
/**
 * @param {string | Date | null} dateEntity
 * @param {boolean} monthLong
 * @param {boolean} hasDate - Показывать ли дату: 01.12.2025 -> дек. 2025 г.
 * @return {string}
 */
export function formatDateIntl(dateEntity = null, monthLong = false, hasDate = true) {

    let workDate = null

    if (typeof dateEntity === 'string') {
        // Важно: парсинг даты из строки YYYY-MM-DD может вызвать смещение часового пояса.
        // Лучше всего парсить как UTC, чтобы избежать смещения на день назад в некоторых часовых поясах.

        dateEntity.includes(' ')
            ? workDate = new Date(dateEntity)                       // строка в формате "YYYY-MM-DD HH:mm:ss"
            : workDate = new Date(dateEntity + 'T00:00:00Z') // строка в формате "YYYY-MM-DD"

        if (isNaN(workDate.getTime())) return ''                    // Ошибка парсинга

    } else if (dateEntity instanceof Date) {
        workDate = dateEntity
    } else {
        return ''
    }

    // 2. Используем Intl.DateTimeFormat для локализованного форматирования.

    const options = {
        day:   'numeric',      // 31
        month: monthLong ? 'long' : 'short',       // декабря
        year:  'numeric'      // 2025
    }

    if (!hasDate) {
        delete options.day
    }

    // const timeOptions = {
    //     hour: '2-digit',
    //     minute: '2-digit',
    //     second: '2-digit',
    //     hour12: false // Используем 24-часовой формат
    // };

    let formattedDate = new Intl.DateTimeFormat('ru-RU', options).format(workDate)

    // Регулярное выражение /\s+г\.?$/ находит:
    // - \s+ : один или несколько пробелов
    // - г : букву 'г'
    // - \.? : необязательную точку
    // - $ : в конце строки.
    // formattedDate = formattedDate.replace(/\s+г\.?$/, '');
    // return formattedDate + 'г.'

    return formattedDate.replace(/ г\.$/, 'г.')
}
