// Info Тут все функции для участка Стежки

import {
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
} from '/resources/js/src/app/constants/fabrics.js'

import {isEmptyObj} from '/resources/js/src/app/helpers/helpers_lib.js'

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
// attract: добавляет к массиву сменных заданий недостающие дни со статусом FABRIC_TASK_STATUS.UNKNOWN
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
        // console.log(taskDraft.date)

        fabricTasks.push({...taskDraft})       // создаем новый объект - избавляемся от ссылки на объект
    })

    // еще раз сортируем массив по дате начала по возрастанию
    fabricTasks = fabricTasks.sort((a, b) => new Date(a.date) - new Date(b.date))

    // устанавливаем активный флаг для первого элемента
    fabricTasks.forEach((item) => item.active = false)
    fabricTasks[0].active = true

    // console.log(fabricTasks)

    return fabricTasks
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract: выбирает из массива ПС, только те, которые соответствуют заданной стегальной машине
// attract: onlyBasics: если true, то возвращает только основные ПС, иначе - все ПС, которые можно простегать
export function filterFabricsByMachineId(fabrics = [], machineId = -1, onlyBasics = true) {

    if (fabrics.length === 0 || machineId === -1) return []   // страховочка

    let result

    if (onlyBasics) {
        result = fabrics.filter((fabric) => fabric.machines[0].id === machineId && fabric.active)        // получаем массив объектов, где каждый элемент - Proxy()
        return result.map((fabric) => ({...fabric}))                                    // избавляемся от Proxy()
    }

    // получаем массив объектов, где остались только нужные ПС
    result = fabrics.map((fabric) => {
        if ([fabric.machines[0].id, fabric.machines[1].id, fabric.machines[2].id, fabric.machines[3].id].includes(machineId) && fabric.active) {
            return {...fabric}
        }
    })

    return result.filter((fabric) => typeof fabric !== "undefined")                     // удаляем пустые объекты
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract: Возвращает режим, в котором была добавлена ПС - только основная для этой СМ или только альтернативная
export function getAddFabricMode(fabrics = [], machineId = -1, fabricId = -1) {

    if (fabrics.length === 0 || machineId === -1 || fabricId === -1) return ''   // страховочка

    if (fabricId === 0) return false

    const basicFabrics = filterFabricsByMachineId(fabrics, machineId )                     // Все основные ПС для данной СМ
    const isBasicFabric = basicFabrics.some((fabric) => fabric.id === fabricId)            // Проверяем, является ли ПС основной для данной СМ

    // console.log(basicFabrics)
    // console.log(isBasicFabric)

    // Выбираем только альтернативные ПС для данной СМ
    const result =fabrics.map((fabric) => {
        if ([fabric.machines[1].id, fabric.machines[2].id, fabric.machines[3].id].includes(machineId) && fabric.active) {
            return {...fabric}
        }
    })

    const nonBasicFabrics = result.filter((fabric) => typeof fabric !== "undefined")         // удаляем пустые объекты
    const isNonBasicFabrics = nonBasicFabrics.some((fabric) => fabric.id === fabricId)       // Проверяем, является ли ПС альтернативной для данной СМ

    // console.log(nonBasicFabrics)
    // console.log(isNonBasicFabrics)

    // return isBasicFabric && !isNonBasicFabrics
    return !isNonBasicFabrics

}
