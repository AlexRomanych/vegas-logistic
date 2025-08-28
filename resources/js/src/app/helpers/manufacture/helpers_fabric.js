// Info Тут все функции для участка Стежки

import {
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
    TASK_DRAFT, FABRIC_ROLL_STATUS,
    WARNINGS_RANGES,
    FABRIC_MANAGE,
    FABRIC_EXECUTE,
} from '@/app/constants/fabrics.js'

import {isEmptyObj} from '@/app/helpers/helpers_lib.js'

import {
    addDays,
    subtractDays,
    getDateDiffInDays,
    getISOFromLocaleDate, formatTimeWithLeadingZeros
} from '@/app/helpers/helpers_date.js'

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

    start = subtractDays(start)                                     // сегодня минус 1 день
    end = addDays(end, PERIOD_LENGTH - 2)                     // начало + неделя - это танцы с бубнами

    return {
        start: getISOFromLocaleDate(start),
        end: getISOFromLocaleDate(end),
    }
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract: добавляет к массиву сменных заданий недостающие дни со статусом FABRIC_TASK_STATUS.UNKNOWN
export function addEmptyFabricTasks(fabricTasks = [], period = null) {
    // console.log('period: ', period)
    // страховочка
    if (!Array.isArray(fabricTasks)) fabricTasks = []

    // создаем пустой объект с данными для формы создания сменного задания (болванка)
    const taskDraft = TASK_DRAFT


    // const taskDraft = {
    //     date: '',
    //     common: {
    //         team: 1,
    //         status: FABRIC_TASK_STATUS.UNKNOWN.CODE,
    //         description: '',
    //         active: false,
    //     },
    //     machines: {
    //         american: {
    //             rolls: [],
    //             description: '',
    //             active: true,
    //             finish_at: null,
    //         },
    //         german: {
    //             rolls: [],
    //             description: '',
    //             active: true,
    //             finish_at: null,
    //         },
    //         china: {
    //             rolls: [],
    //             description: '',
    //             active: true,
    //             finish_at: null,
    //         },
    //         korean: {
    //             rolls: [],
    //             description: '',
    //             active: true,
    //             finish_at: null,
    //         },
    //     },
    //     workers: [],
    // }

    // console.log('period: ', period)
    // console.log('fabricTasks: ', fabricTasks)

    // attract: Может быть ситуация, когда нам с бэка придет пустой массив сменных заданий
    // attract: Если массив пустой, то просто заполняем его default объектами
    if (fabricTasks.length === 0) {

        // страховочка
        if (period === null) return []

        const compareDate = addDays(period.end).toISOString().split('T')[0] // конец периода + 1 день из-за !==

        let tempDate = new Date(period.start)
        while (tempDate.toISOString().split('T')[0] !== compareDate) {
            taskDraft.date = tempDate.toISOString().split('T')[0]

            // создаем глубокую  копию нового объекта - избавляемся от ссылки на объект
            fabricTasks.push(JSON.parse(JSON.stringify(taskDraft)))

            tempDate = addDays(tempDate)
        }

        fabricTasks[0].active = true            // устанавливаем активный флаг для первого элемента. Обязательно!!!
        return fabricTasks
    }

    // сортируем массив по дате начала по возрастанию
    fabricTasks = fabricTasks.sort((a, b) => new Date(a.date) - new Date(b.date))

    // console.log('period: ', period)
    // new Date(Math.min(date1.getTime(), date2.getTime()));
    // descr: Имеет 2 промежутка:
    // descr: 1 - период period
    // descr: 2 - период входного массива СЗ - fabricTasks
    // descr: Определяем период, с началом минимальным из period и fabricTasks, и максимальным из period и fabricTasks
    // descr: А мы будем делать универсальность, хотя можно было бы брать только period

    const startDate = Math.min(new Date(period.start).getTime(), new Date(fabricTasks[0].date).getTime())
    const endDate = Math.max(new Date(period.end).getTime(), new Date(fabricTasks[fabricTasks.length - 1].date).getTime())

    const periodLength = getDateDiffInDays(startDate, endDate)

    let tempDate = new Date(startDate)                  // первый день
    const missingDates = []                             // массив пропущенных дат

    // console.log('periodLength: ', periodLength)
    // console.log('startDate: ', tempDate)

    for (let i = 0; i <= periodLength; i++) {            // перебираем весь период
        let isFind = false
        for (let j = 0; j < fabricTasks.length; j++) {
            if (tempDate.toISOString().split('T')[0] === fabricTasks[j].date) {
                isFind = true
                break
            }
        }

        if (!isFind) missingDates.push(tempDate.toISOString().split('T')[0])

        tempDate = addDays(tempDate)
    }

    // добавляем недостающие даты в массив сменных заданий с статусом FABRIC_TASK_STATUS.UNKNOWN
    missingDates.forEach((date) => {
        taskDraft.date = date

        // создаем новый объект - избавляемся от ссылки на объект
        fabricTasks.push(JSON.parse(JSON.stringify(taskDraft)))
    })

    // console.log('fabricTasks: after', fabricTasks)

    // еще раз сортируем массив по дате начала по возрастанию
    fabricTasks = fabricTasks.sort((a, b) => new Date(a.date) - new Date(b.date))

    // устанавливаем активный флаг для первого элемента
    fabricTasks.forEach((item) => item.active = false)

    // устанавливаем активный флаг для первого элемента. Обязательно!!!
    fabricTasks[0].active = true

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
// __ Возвращает режим, в котором была добавлена ПС - только основная для этой СМ или только альтернативная
export function getAddFabricMode(fabrics = [], machineId = -1, fabricId = -1) {

    if (fabrics.length === 0 || machineId === -1 || fabricId === -1) return ''              // страховочка

    if (fabricId === 0) return false

    // const basicFabrics = filterFabricsByMachineId(fabrics, machineId)                       // Все основные ПС для данной СМ
    // const isBasicFabric = basicFabrics.some((fabric) => fabric.id === fabricId)             // Проверяем, является ли ПС основной для данной СМ

    // console.log('basicFabrics: ', basicFabrics)
    // console.log(isBasicFabric)

    // __ Выбираем только альтернативные ПС для данной СМ
    const result = fabrics.map((fabric) => {
        if ([fabric.machines[1].id, fabric.machines[2].id, fabric.machines[3].id].includes(machineId) && fabric.active) {
            return {...fabric}
        }
    })

    const nonBasicFabrics = result.filter((fabric) => typeof fabric !== "undefined")         // удаляем пустые объекты
    const isNonBasicFabrics = nonBasicFabrics.some((fabric) => fabric.id === fabricId)       // Проверяем, является ли ПС альтернативной для данной СМ

    // console.log('nonBasicFabrics: ', nonBasicFabrics)
    // console.log('isNonBasicFabrics: ', isNonBasicFabrics)

    // return isBasicFabric && !isNonBasicFabrics
    return !isNonBasicFabrics
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract: Заполняет названия ПС в массиве сменных заданий
export function fillFabricsDisplayNames(fabrics = [], tasks = []) {

    // страховочка
    if (!Array.isArray(fabrics) || !Array.isArray(tasks) || fabrics.length === 0 || tasks.length === 0) return [];

    tasks.forEach((task) =>
        Object.keys(task.machines).forEach((key) =>
            task.machines[key].rolls.forEach((roll) =>
                roll.fabric = fabrics.find((fabric) => fabric.id === roll.fabric_id).display_name)
        )
    )

    return tasks
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract: Получаем статус функций и отображения компонентов в зависимости от статуса СЗ
export function getFunctionalByFabricTaskStatus(fabricTask) {
    return [
        FABRIC_TASK_STATUS.UNKNOWN.CODE,
        FABRIC_TASK_STATUS.CREATED.CODE,
    ].includes(fabricTask)
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract: Возвращает тип расскраски компонента по статусу рулона
export function getTypeByRollStatus(rollStatus, isRegistered_1C = false) {

    // if (isRegistered_1C) rollStatus = FABRIC_ROLL_STATUS.REGISTERED_1C.CODE

    if (rollStatus === FABRIC_ROLL_STATUS.CREATED.CODE) return FABRIC_ROLL_STATUS.CREATED.TYPE
    if (rollStatus === FABRIC_ROLL_STATUS.RUNNING.CODE) return FABRIC_ROLL_STATUS.RUNNING.TYPE
    if (rollStatus === FABRIC_ROLL_STATUS.PAUSED.CODE) return FABRIC_ROLL_STATUS.PAUSED.TYPE
    if (rollStatus === FABRIC_ROLL_STATUS.DONE.CODE) return FABRIC_ROLL_STATUS.DONE.TYPE
    if (rollStatus === FABRIC_ROLL_STATUS.FALSE.CODE) return FABRIC_ROLL_STATUS.FALSE.TYPE
    if (rollStatus === FABRIC_ROLL_STATUS.ROLLING.CODE) return FABRIC_ROLL_STATUS.ROLLING.TYPE
    if (rollStatus === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE) return FABRIC_ROLL_STATUS.REGISTERED_1C.TYPE
    if (rollStatus === FABRIC_ROLL_STATUS.MOVED.CODE) return FABRIC_ROLL_STATUS.MOVED.TYPE
    if (rollStatus === FABRIC_ROLL_STATUS.ACCEPTED.CODE) return FABRIC_ROLL_STATUS.ACCEPTED.TYPE
    if (rollStatus === FABRIC_ROLL_STATUS.CANCELLED.CODE) return FABRIC_ROLL_STATUS.CANCELLED.TYPE

    return 'dark'
}


// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract: Возвращает тип расскраски компонента по статусу СЗ
export function getTypeByTaskStatus(taskStatus) {

    if (taskStatus === FABRIC_TASK_STATUS.UNKNOWN.CODE) return FABRIC_TASK_STATUS.UNKNOWN.TYPE
    if (taskStatus === FABRIC_TASK_STATUS.CREATED.CODE) return FABRIC_TASK_STATUS.CREATED.TYPE
    if (taskStatus === FABRIC_TASK_STATUS.PENDING.CODE) return FABRIC_TASK_STATUS.PENDING.TYPE
    if (taskStatus === FABRIC_TASK_STATUS.RUNNING.CODE) return FABRIC_TASK_STATUS.RUNNING.TYPE
    if (taskStatus === FABRIC_TASK_STATUS.DONE.CODE) return FABRIC_TASK_STATUS.DONE.TYPE

    return 'dark'
}

// attract: Получить статус предупреждения по количеству
// attract: Если передано 2 числа, то % = amount / maxAmount
// attract: Если передано 1 число, то % = amount
export function getAmountWarningStatus(amount = 0, maxAmount) {

    const percent = (maxAmount) ? amount / maxAmount : amount

    if (percent < 0) return 'danger'

    let warningStatus = 'success'
    Object.keys(WARNINGS_RANGES).forEach((range) => {
        if (WARNINGS_RANGES[range].LOW_BOUND <= percent && percent < WARNINGS_RANGES[range].HIGH_BOUND) {
            warningStatus = WARNINGS_RANGES[range].TYPE
        }
    })

    return warningStatus
}


// attract: Получаем все машины для отображения и формируем объект для отображения
export function getMachines() {

    const tempMachines = []

    Object.keys(FABRIC_MACHINES).forEach((machine) => {

        if (FABRIC_MACHINES[machine].ID !== 0) {

            // console.log(FABRIC_MACHINES[machine].NAME)
            tempMachines.push({
                id: FABRIC_MACHINES[machine].ID,
                name: FABRIC_MACHINES[machine].NAME,
                title: FABRIC_MACHINES[machine].TITLE,
                index: FABRIC_MACHINES[machine].INDEX,
                show: true,
            })
        }
    })

    // console.log(tempMachines)
    return tempMachines
}


// attract: Получаем трудозатраты для отображения для рулона ОПП (TaskContext)
export function getProductivity(roll) {

    if (isEmptyObj(roll)) return ''

    const fabricLength = roll.length_amount / roll.rate
    // const fabricLength = roll.average_fabric_length * roll.rolls_amount

    const hours = fabricLength / roll.productivity
    return formatTimeWithLeadingZeros(hours, 'hour')
}


// attract: Получаем трудозатраты для отображения для группы рулонов ОПП (TaskContext)
export function getProductivityTotal(rolls) {

    if (rolls.lenght === 0) return ''

    const hours = rolls.reduce((acc, roll) => acc + (roll.length_amount / roll.rate / roll.productivity), 0)
    return formatTimeWithLeadingZeros(hours, 'hour')
}


// attract: Получаем трудозатраты для отображения для дня СЗ (FabricTasksDate)
export function getProductivityTotalTask(task) {

    if (isEmptyObj(task)) return ''

    let hours = 0
    Object.keys(task.machines).forEach(machine => {
        hours += task.machines[machine].rolls.reduce((acc, roll) => acc + (roll.length_amount / roll.rate / roll.productivity), 0)
    })



    // const hours = rolls.reduce((acc, roll) => acc + (roll.length_amount / roll.rate / roll.productivity), 0)
    return formatTimeWithLeadingZeros(hours, 'hour')
}


// attract: Получаем статус СЗ по коду статуса
export function getFabricTaskStatusByCode(taskCode = 0) {
    const statusKey = Object.keys(FABRIC_TASK_STATUS).find((key) => FABRIC_TASK_STATUS[key].CODE === taskCode)
    // console.log(FABRIC_TASK_STATUS[statusKey])
    return FABRIC_TASK_STATUS[statusKey]
}


// __ Получаем активное СЗ и вкладку из LocalStorage


// const TASK_TAB_PREFIX = 'TASK_TAB'                      // __ Управление СЗ
// const TASK_TAB_PREFIX_EXECUTE = 'TASK_EXECUTE_TAB'      // __ Выполнение СЗ

//
// const getPrefixByFuncType = (funcType) => {
//     switch (funcType) {
//         case FABRIC_MANAGE:
//             workPrefix = TASK_TAB_PREFIX
//             break
//         case FABRIC_EXECUTE:
//             workPrefix = TASK_TAB_PREFIX_EXECUTE
//     }
// }
//
// const getActiveTaskAndTab = (funcType) => {
//     const workPrefix = getPrefixByFuncType(funcType)
//
//     try {
//         const data = JSON.parse(localStorage.getItem(workPrefix))
//         if (data) {
//             const findTask = taskData.find(t => t.date === data.activeTaskDate)
//             if (findTask) {
//                 changeActiveTask(findTask)
//                 resetTabs()
//                 const findTab = Object.keys(tabs).find(tab => tabs[tab].id === data.activeTabId)
//                 if (findTab) {
//                     tabs[findTab].shown = true
//                 }
//             }
//         }
//     } catch (e) {
//         catchErrorHandler('LocalStorage: ', e)
//     }
// }
