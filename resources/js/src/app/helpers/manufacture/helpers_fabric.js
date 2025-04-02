// Info Тут все функции для участка Стежки

import {FABRIC_TASK_STATUS} from '/resources/js/src/app/constants/fabrics.js'
import {addDays, subtractDays} from '/resources/js/src/app/helpers/helpers_date.js'

// descr Получить тип стиля по коду статуса СЗ на стежке
export const getStyleTypeByFabricTaskStatusCode = function (taskStatusCode = null) {
    if (taskStatusCode === null) return 'dark'

    switch (taskStatusCode) {
        case FABRIC_TASK_STATUS.UNKNOWN.CODE:
            return 'danger'

        case FABRIC_TASK_STATUS.CREATED.CODE:
            return 'dark'

        case FABRIC_TASK_STATUS.PENDING.CODE:
            return 'primary'

        case FABRIC_TASK_STATUS.RUNNING.CODE:
            return 'warning'

        case FABRIC_TASK_STATUS.DONE.CODE:
            return 'success'

        default:
            return 'dark'
    }
}


// descr Получить название статуса по коду статуса СЗ на стежке
export const getTitleByFabricTaskStatusCode = function (taskStatusCode = null) {
    if (taskStatusCode === null) return ''

    switch (taskStatusCode) {
        case FABRIC_TASK_STATUS.UNKNOWN.CODE:
            return FABRIC_TASK_STATUS.UNKNOWN.TITLE

        case FABRIC_TASK_STATUS.CREATED.CODE:
            return FABRIC_TASK_STATUS.CREATED.TITLE

        case FABRIC_TASK_STATUS.PENDING.CODE:
            return FABRIC_TASK_STATUS.PENDING.TITLE

        case FABRIC_TASK_STATUS.RUNNING.CODE:
            return FABRIC_TASK_STATUS.RUNNING.TITLE

        case FABRIC_TASK_STATUS.DONE.CODE:
            return FABRIC_TASK_STATUS.DONE.TITLE

        default:
            return ''
    }
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает период отображения сменных заданий на стежке
// период: начало = сегодня минус 1 день, конец = начало + неделя
export function getFabricTasksPeriod() {
    const PERIOD_LENGTH = 7                             // 7 дней - длина периода отображения

    // warning Тут непонятка! Пока дата в формате Date, все yнормально
    // warning Как только преобразуем в .toISOString().split('T')[0] - отнимается еще 1 день

    let start = new Date()
    let end = new Date(start)

    // start = subtractDays(start)                     // сегодня минус 1 день
    end = addDays(end, PERIOD_LENGTH)               // начало + неделя

    return {
        start: start.toISOString().split('T')[0],
        end: end.toISOString().split('T')[0]
    }
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract добавляет к массиву сменных заданий недостающие дни со статусом FABRIC_TASK_STATUS.UNKNOWN
export function addEmptyFabricTasks(fabricTasks = []) {

    // Страхуемся
    if (!Array.isArray(fabricTasks)) return []
    if (fabricTasks.length === 0) return []

    // сортируем массив по дате начала по возрастанию
    fabricTasks = fabricTasks.sort((a, b) => new Date(a.date) - new Date(b.date))

    // создаем пустой объект с данными для формы создания сменного задания (болванка)
    const taskDraft = {
        date: '',
        active: false,
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.UNKNOWN.CODE,
        },
        machines: {
            american: {
                rolls: []
            },
            german: {
                rolls: []
            },
            china: {
                rolls: []
            },
            korean: {
                rolls: []
            },
        }
    }

    let tempDate = new Date(fabricTasks[0].date)      // первый день в массиве - минус 1 день
    const missingDates = []                                     // массив пропущенных дат

    let i = 0
    while (i < fabricTasks.length) {

        if (tempDate.toISOString().split('T')[0] !== fabricTasks[i].date) {
            missingDates.push(tempDate.toISOString().split('T')[0])
        } else {
            i++
        }

        tempDate = addDays(tempDate)
    }

    // добавляем недостающие даты в массив сменных заданий с статусом FABRIC_TASK_STATUS.UNKNOWN
    missingDates.forEach((date) => {
        taskDraft.date = date
        console.log(taskDraft.date)

        fabricTasks.push({... taskDraft})
    })

    // еще раз сортируем массив по дате начала по возрастанию
    fabricTasks = fabricTasks.sort((a, b) => new Date(a.date) - new Date(b.date))

    console.log(fabricTasks)

    return fabricTasks
}
