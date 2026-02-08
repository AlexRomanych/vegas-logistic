// info Тут все, что связано с Пошивом

import type {
    IAmountAndTime, IDay, IPlanMatrix, IRenderMatrixDiff, IRenderMatrixLineDiffs,
    ISewingMachineKeys, ISewingMachineTimesKeys,
    ISewingTask, ISewingTaskArrayDiff, ISewingTaskArrayLineDiffs,
    ISewingTaskLine, ISewingTaskLineAmountAvg, ISewingTaskLineTime,
    ISewingTaskModel, ISewingTaskOrder,
    ISewingTaskOrderLine, ISewingTaskStatus
} from '@/types'

import { SEWING_MACHINES, SEWING_TASK_DRAFT, SEWING_TASK_STATUSES } from '@/app/constants/sewing.ts'

import { formatTimeWithLeadingZeros, getDaysDifference } from '@/app/helpers/helpers_date'
import { round } from '@/app/helpers/helpers_lib.ts'


// __ Функция для получения трудозатрат для Записи (SewingLine) в СЗ
export function getSewingTimes(sewingLine: ISewingTaskLine): IAmountAndTime {

    //  __ Создаем сам объект данных с ключами из SEWING_MACHINES и {time: 0, amount: 0} и инициализируем его нулями
    const amountAndTimeObj = createAmountAndTimeObj()

    // __ Получаем тип машины модели по строке
    let machineType: ISewingMachineKeys | null = getSewingLineMachineType(sewingLine)
    if (!machineType) {
        return amountAndTimeObj// __ Страховочка
    }

    // __ Получаем количество
    if (machineType === SEWING_MACHINES.AVERAGE) {
        if (sewingLine.amount_avg) {
            Object.entries(sewingLine.amount_avg).forEach(([key, value]) => {
                const machineType = getMachineType(key as ISewingMachineKeys)
                if (!machineType) {
                    return amountAndTimeObj // __ Страховочка
                }
                amountAndTimeObj[machineType].amount += value
            })
        } else {
            console.log('Error! Amount_avg is not defined for: ' + sewingLine.order_line.model.main.name)
        }
    } else {
        amountAndTimeObj[machineType].amount = sewingLine.amount
    }

    // __ Получаем время
    Object.entries(sewingLine.time).forEach(([key, value]) => {
        const machineTypeKey = key.replace('time_', '') as ISewingMachineKeys
        const machineType    = getMachineType(machineTypeKey)
        if (!machineType) {
            return amountAndTimeObj     // __ Страховочка
        }
        amountAndTimeObj[machineType].time = value
    })

    return amountAndTimeObj
}


// __ Создаем сам объект данных с ключами из SEWING_MACHINES и {time: 0, amount: 0} и инициализируем его нулями
export function createAmountAndTimeObj() {
    return Object.values(SEWING_MACHINES).reduce((acc, value) => {
        acc[value] = { time: 0, amount: 0 }
        return acc
    }, {} as IAmountAndTime)
}


// __ Функция для получения типа машины по ключу (константы ШМ)
export function getMachineType(machineType: ISewingMachineKeys) {
    const findMachineTypeKey = Object.keys(SEWING_MACHINES).find(key => SEWING_MACHINES[key] === machineType)
    return findMachineTypeKey ? SEWING_MACHINES[findMachineTypeKey] : null
}


// __ Функция для получения типа ШМ по Записи в СЗ
// __ Тут может быть ситуация, когда модель - чехол, тогда получаем тип машины по базовой модели
export function getSewingLineMachineType(sewingLine: ISewingTaskLine) {

    // __ Получаем тип машины модели
    let machineType: ISewingMachineKeys | null = SEWING_MACHINES.UNDEFINED
    if (sewingLine.order_line.model.base && !sewingLine.order_line.model.cover) {           // __ Это условие того, что элемент - чехол
        machineType = getMachineType(sewingLine.order_line.model.base.machine_type)         // __ Получаем тип машины модели по базовой модели
    } else if (!sewingLine.order_line.model.base && sewingLine.order_line.model.cover) {    // __ Это условие того, что элемент - матрас
        machineType = getMachineType(sewingLine.order_line.model.main.machine_type)         // __ Получаем тип машины модели по основной модели
    } else if (!sewingLine.order_line.model.base && !sewingLine.order_line.model.cover) {   // __ Это условие того, что элемент - расчетный)
        machineType = getMachineType(sewingLine.order_line.model.main.machine_type)         // __ Получаем тип машины модели по основной модели
    }

    return machineType
}


// __ Получаем трудозатраты в текстовом представлении '05ч. 30м. 18с.'
// __ twoLines = true - Если больше часа, то выводим часы и минуты (обрезаем секунды)
export function getTimeString(sewingLine: ISewingTaskLine, twoLines: boolean = false) {

    const machineType = getSewingLineMachineType(sewingLine)
    if (!machineType) {
        return 'н/д'
    }

    const amountAndTimeObj = getSewingTimes(sewingLine)
    let time               = 0

    // __ Получаем количество
    if (machineType === SEWING_MACHINES.AVERAGE) {
        time = Object.keys(amountAndTimeObj).reduce((acc, key) => acc + amountAndTimeObj[key].time, time)
    } else {
        time = amountAndTimeObj[machineType as ISewingMachineKeys].time
    }

    // __ Если больше часа, то выводим часы и минуты (обрезаем секунды)
    if (twoLines) {
        // __ Получаем время. Если больше часа, то выводим часы и минуты (обрезаем секунды)
        if (time >= 60 * 60) {
            const timeStrArr = formatTimeWithLeadingZeros(time).split(' ')
            if (timeStrArr[0] !== undefined && timeStrArr[1] !== undefined) {
                return timeStrArr[0] + ' ' + timeStrArr[1]
            } else {
                return formatTimeWithLeadingZeros(time)
            }
        }
    }

    return formatTimeWithLeadingZeros(time)
}


// __ Получаем трудозатраты по Заявке или массиву строк (Содержимого) в формате объекта
export function getSewingTaskAmountAndTime(item: ISewingTask | ISewingTaskLine[]) {

    //  __ Создаем сам объект данных с ключами из SEWING_MACHINES и {time: 0, amount: 0} и инициализируем его нулями
    const amountAndTimeObj = createAmountAndTimeObj()

    // __ Проверяем, что пришло на вход
    let itemArr = []
    if (Array.isArray(item)) {
        itemArr = item
    } else {
        itemArr = item.sewing_lines
    }

    // __ Проходим по всем sewing_lines и суммируем данные
    itemArr?.forEach(sewing_line => {

        // __ Получаем тип машины модели
        const machineType = getSewingLineMachineType(sewing_line)
        if (!machineType) {
            return  // __ Страховочка
        }

        // __ Получаем количество
        if (machineType === SEWING_MACHINES.AVERAGE) {
            if (sewing_line.amount_avg) {
                Object.entries(sewing_line.amount_avg).forEach(([key, value]) => {
                    const machineType = getMachineType(key as ISewingMachineKeys)
                    if (!machineType) {
                        return  // __ Страховочка
                    }
                    amountAndTimeObj[machineType].amount += value
                })
            } else {
                console.log('Error! Amount_avg is not defined for: ' + sewing_line.order_line.model.main.name)
            }
        } else {
            amountAndTimeObj[machineType].amount += sewing_line.amount
        }

        // __ Получаем время
        Object.entries(sewing_line.time).forEach(([key, value]) => {
            const machineTypeKey = key.replace('time_', '') as ISewingMachineKeys
            const machineType    = getMachineType(machineTypeKey)
            if (!machineType) {
                return  // __ Страховочка
            }
            amountAndTimeObj[machineType].time += value
        })
    })
    return amountAndTimeObj
}


// __ Возвращаем Чехол модели. Вспомогалочка, когда приходит в Заявку Чехол
export function getSewingTaskModelCover(
    item: ISewingTaskLine | ISewingTaskOrderLine | ISewingTaskOrderLine['model']) {

    let target = null
    if (isSewingTaskLine(item)) {
        // __ Динамическое свойство средней модели
        if (isAverage(item)) {
            return item.order_line.model.main
        }
        target = item.order_line.model
    } else if (isSewingTaskOrderLine(item)) {
        target = item.model
    } else if (isSewingTaskOrderLineModel(item)) {
        target = item
    }

    if (!target) {
        return null
    }

    // __ Сужаем тип, чтобы TS не ругался
    const model = ('main' in target && 'base' in target && 'cover' in target) ? target : null

    if (model && model.base && !model.cover) {           // __ Это условие того, что элемент - чехол (есть база)
        return model.main
    } else if (model && !model.base && model.cover) {    // __ Это условие того, что элемент - матрас (есть чехол)
        return model.cover
    } else if (model && !model.base && !model.cover) {   // __ Это условие того, что либо элемент - расчетный, либо чехол без базы ('Чехол ЖК'), либо база без чехла
        if (isCover(model.main)) return model.main       // __ Дополнительно проверяем, является ли модель Чехлом
        if (isAverage(model.main)) return model.main     // __ Дополнительно проверяем, является ли модель Расчетной, тоже возвращаем
    }

    return null
}


// __ Возвращаем имя Чехла модели.
export function getSewingTaskModelCoverName(
    item: ISewingTaskLine | ISewingTaskOrderLine | ISewingTaskOrderLine['model']) {
    const modelCover = getSewingTaskModelCover(item)

    if (modelCover) {
        return isAverage(modelCover) ? 'Чехол для Планового матраса' : modelCover.name_report
    }
    return ''
}


// __ Сортируем массив строк по размерам
export function sortSewingTaskLinesBySize(item: ISewingTask | ISewingTaskLine[], direction: 'asc' | 'desc' = 'asc') {

    // __ Проверяем, что пришло на вход
    let sourceArray = []
    if (Array.isArray(item)) {
        sourceArray = item
    } else {
        sourceArray = item.sewing_lines
    }

    let isFind = true
    while (isFind) {
        isFind = false

        for (let i = 0; i < sourceArray.length; i++) {
            for (let j = i + 1; j < sourceArray.length; j++) {
                if (direction === 'asc') {
                    if ((sourceArray[i].order_line.dims.width > sourceArray[j].order_line.dims.width)
                        || (sourceArray[i].order_line.dims.width === sourceArray[j].order_line.dims.width && sourceArray[i].order_line.dims.length > sourceArray[j].order_line.dims.length)
                        || (sourceArray[i].order_line.dims.width === sourceArray[j].order_line.dims.width && sourceArray[i].order_line.dims.length === sourceArray[j].order_line.dims.length && sourceArray[i].order_line.dims.height > sourceArray[j].order_line.dims.height)) {

                        const temp     = sourceArray[i]
                        sourceArray[i] = sourceArray[j]
                        sourceArray[j] = temp
                        isFind         = true
                    }
                } else {
                    if ((sourceArray[i].order_line.dims.width < sourceArray[j].order_line.dims.width)
                        || (sourceArray[i].order_line.dims.width === sourceArray[j].order_line.dims.width && sourceArray[i].order_line.dims.length < sourceArray[j].order_line.dims.length)
                        || (sourceArray[i].order_line.dims.width === sourceArray[j].order_line.dims.width && sourceArray[i].order_line.dims.length === sourceArray[j].order_line.dims.length && sourceArray[i].order_line.dims.height < sourceArray[j].order_line.dims.height)) {

                        const temp     = sourceArray[i]
                        sourceArray[i] = sourceArray[j]
                        sourceArray[j] = temp
                        isFind         = true
                    }
                }
            }
        }
    }

    return sourceArray
}


// __ Получаем размеры Чехла модели в текстовом представлении
export function getCoverSizeString(item: ISewingTaskLine | ISewingTaskOrderLine) {
    let workData = null
    if (isSewingTaskLine(item)) {
        workData = item.order_line
    } else if (isSewingTaskOrderLine(item)) {
        workData = item
    } else {
        return ''
    }

    return workData.dims.width.toString() + 'x' +
        workData.dims.length.toString() + 'x' +
        (workData.model.main.cover_height * 100).toString()
}


/**
 * __ Функция, которая возвращает высчитанный объект количества при разделении строки на количество
 * __ Возвращает новый экземпляр с пересчитанными данными
 * @param sewingLine    __Входная строка__
 * @param newAmount     __Новое количество__
 */
export function calculateDividedAmountAndTime(sewingLine: ISewingTaskLine, newAmount: number) {

    // __ Создаем копию строки (референсная)
    const refSewingLine = { ...sewingLine }

    let newAmountAvg: ISewingTaskLineAmountAvg | null = null
    const newTime: ISewingTaskLineTime                = {} as ISewingTaskLineTime

    // __ Средняя модель
    if (refSewingLine.amount_avg) {

        newAmountAvg = {} as ISewingTaskLineAmountAvg
        Object.entries(refSewingLine.amount_avg).forEach(([key, value]) => {
            const amount                             = refSewingLine.amount ? value / refSewingLine.amount * newAmount : 0
            newAmountAvg![key as ISewingMachineKeys] = amount

            const arrKey: ISewingMachineTimesKeys = `time_${key}` as ISewingMachineTimesKeys
            newTime[arrKey]                       = value ? round(refSewingLine.time[arrKey] / value * amount) : 0 // __ Уже новое пересчитанное количество
        })

        // Object.entries(refSewingLine.time).forEach(([key, value]) => {
        //     const arrKey: ISewingMachineTimesKeys = `time_${key}` as ISewingMachineTimesKeys
        //     let amount                            = 0
        //     if (newAmountAvg && newAmountAvg[key as ISewingMachineKeys]) amount = newAmountAvg[key as ISewingMachineKeys]
        //     // newTime[arrKey] = value ? refSewingLine.time[arrKey] / value * amount : 0 // __ Уже новое пересчитанное количество
        // })
    } else {
        Object.entries(sewingLine.time).forEach(([key, value]) => {
            newTime[key as ISewingMachineTimesKeys] = sewingLine.amount ? round(value / sewingLine.amount) * newAmount : 0
        })
    }

    refSewingLine.amount     = newAmount
    refSewingLine.amount_avg = newAmountAvg
    refSewingLine.time       = newTime

    return refSewingLine
}


// __ Пересчитываем позицию по порядку записей в массиве строк (SewingTaskLine[]) по ссылке
// __ Пересчитываем позицию именно в том порядке, в котором они находятся в исходно массиве
// __ (как определил специалист ОПП при перетаскивании строк или упорядочивании или сортировке)
export function repositionSewingTaskLines(entity: ISewingTask | ISewingTaskLine[]) {
    let items
    if (isSewingTask(entity)) {
        items = entity.sewing_lines
    } else if (Array.isArray(entity)) {
        items = entity
    } else {
        return []
    }

    items.forEach((_, index, arr) => {
        arr[index].position = index + 1
    })
    return items
}


// __ Очищаем матрицу рендера от пустых сменных заданий, которые добавляем для рендеринга
export function clearRenderMatrix(matrix: IPlanMatrix) {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            matrix[weekIndex][dayIndex] = day.filter(item => item.id > -1) // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
        })
    })
    return matrix
}

// __ Очищаем день матрицы рендера от пустых сменных заданий, которые добавляем для рендеринга
export function clearRenderMatrixDay(day: IDay[]) {
    return [...day.filter(item => item.id > -1)] // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
}


// __ Разница между предыдущим и текущим состоянием
// __ Разница по задумке должна быть только в одной Заявке:
// __ Либо перемещение в рамках одного дня, либо из одного дня в другой
// __ Задача найти эти дни и эту Заявку

// --- ------------------------------------------------------------------------------------
//__  Функция поиска различий с детальными данными по позициям
export function getDiffsWithPositions(currentMatrix: IPlanMatrix, copyMatrix: IPlanMatrix) {
    const diffs: IRenderMatrixDiff[] = []

    // __ 1. Индексируем копию (старые данные)
    const copyMap = new Map()
    copyMatrix.forEach((week, weekIdx) => {
        week.forEach((dayTasks, dayIdx) => {
            const dayOffset = weekIdx * 7 + dayIdx
            dayTasks.forEach(task => {

                // __ Сохраняем "слепок" состояния для сравнения
                copyMap.set(task.id, {
                    dayOffset,
                    position: task.position,
                    lines:    JSON.parse(JSON.stringify(task.sewing_lines)) // __ глубокая копия строк
                })
            })
        })
    })

    // __ 2. Сравниваем с текущим состоянием
    currentMatrix.forEach((week, weekIdx) => {
        week.forEach((dayTasks, dayIdx) => {
            const currentDayOffset = weekIdx * 7 + dayIdx

            dayTasks.forEach((task) => {
                const old = copyMap.get(task.id)

                if (!old) {

                    // __ Обработка совершенно новой задачи (если такое возможно)
                    diffs.push({ type: 'NEW_TASK', taskId: task.id, newPosition: task.position })
                    return
                }

                const isMoved      = old.dayOffset !== currentDayOffset
                const isPosChanged = old.position !== task.position

                // __ Проверяем детальные изменения в строках (sewing_lines)
                const lineDiffs = getLinesDetailedDiff(old.lines, task.sewing_lines)

                // __ Если хоть что-то изменилось — фиксируем
                if (isMoved || isPosChanged || lineDiffs.length > 0) {
                    diffs.push({
                        taskId: task.id,

                        // __ Информация по датам
                        dayFromOffset: old.dayOffset,
                        dayToOffset:   currentDayOffset,

                        // __ Информация по позиции самой задачи
                        oldTaskPosition:   old.position,
                        newTaskPosition:   task.position,
                        isPositionChanged: isPosChanged,
                        isMoved:           isMoved,

                        // __ Детализация по строкам
                        lineDiffs: lineDiffs
                    })
                }
            })
        })
    })

    return diffs
}


// __ Вспомогательная функция для детального сравнения позиций и данных строк
function getLinesDetailedDiff(oldLines: ISewingTaskLine[], newLines: ISewingTaskLine[]) {
    const changes: IRenderMatrixLineDiffs[] = []

    newLines.forEach((newLine) => {
        const oldLine = oldLines.find(l => l.id === newLine.id)

        if (!oldLine) {
            changes.push({ lineId: newLine.id, type: 'ADDED', newPosition: newLine.position })
        } else {
            const isAmountChanged = oldLine.amount !== newLine.amount
            const isPosChanged    = oldLine.position !== newLine.position

            if (isAmountChanged || isPosChanged) {
                changes.push({
                    lineId:            newLine.id,
                    type:              'UPDATED',
                    oldPosition:       oldLine.position,
                    newPosition:       newLine.position,
                    oldAmount:         oldLine.amount,
                    newAmount:         newLine.amount,
                    isPositionChanged: isPosChanged,
                    isAmountChanged:   isAmountChanged
                })
            }
        }
    })

    // Проверка на удаление (если нужно для БД)
    oldLines.forEach(oldLine => {
        if (!newLines.find(l => l.id === oldLine.id)) {
            changes.push({ lineId: oldLine.id, type: 'DELETED' })
        }
    })

    return changes
}

// --- ------------------------------------------------------------------------------------


// __ Получаем разницу между текущим и копией матрицы рендера без деталей
export function getDiffsInRenderMatrix(currentMatrix: IPlanMatrix, copyMatrix: IPlanMatrix) {
    const diffs: IRenderMatrixDiff[] = []

    // __ 1. Создаем плоскую карту из копии для быстрого поиска исходного состояния по ID
    // __ Это поможет нам понять, откуда пришла задача, если она переместилась
    const copyMap = new Map()
    copyMatrix.forEach((week, weekIdx) => {
        week.forEach((dayTasks, dayIdx) => {
            const dayOffset = weekIdx * 7 + dayIdx
            dayTasks.forEach(task => {
                copyMap.set(task.id, {
                    task,
                    dayOffset,
                    linesHash: JSON.stringify(task.sewing_lines) // Хэш строк для быстрого сравнения
                })
            })
        })
    })

    // __ 2. Обходим текущую матрицу
    currentMatrix.forEach((week, weekIdx) => {
        week.forEach((dayTasks, dayIdx) => {
            const currentDayOffset = weekIdx * 7 + dayIdx

            dayTasks.forEach((task) => {
                const original = copyMap.get(task.id)

                // Если задачи не было в исходной матрице (новое СЗ)
                if (!original) {
                    diffs.push({
                        type:        'NEW_TASK',
                        taskId:      task.id,
                        dayToOffset: currentDayOffset,
                        newPosition: task.position
                    })
                    return
                }

                const isMoved           = original.dayOffset !== currentDayOffset
                const isPositionChanged = original.task.position !== task.position
                const currentLinesHash  = JSON.stringify(task.sewing_lines)
                const areLinesChanged   = original.linesHash !== currentLinesHash

                // Если есть хоть одно изменение — фиксируем объект
                if (isMoved || isPositionChanged || areLinesChanged) {
                    diffs.push({
                        taskId:        task.id,
                        dayFromOffset: original.dayOffset,
                        dayToOffset:   currentDayOffset,
                        isMoved,
                        isPositionChanged,
                        areLinesChanged,

                        // __ Детальные изменения строк, если они были
                        lineDiffs: areLinesChanged ? getLinesDiff(original.task.sewing_lines, task.sewing_lines) : []
                    })
                }
            })
        })
    })

    return diffs
}


// __ Вспомогательная функция для сравнения массива строк пошива
function getLinesDiff(oldLines: ISewingTaskLine[], newLines: ISewingTaskLine[]) {
    const lineChanges: IRenderMatrixLineDiffs[] = []

    // __ Проверяем каждую строку в задаче
    newLines.forEach((newLine) => {
        const oldLine = oldLines.find(l => l.id === newLine.id)

        if (!oldLine) {
            lineChanges.push({ lineId: newLine.id, type: 'ADDED' })
        } else if (
            oldLine.amount !== newLine.amount ||
            oldLine.position !== newLine.position
        ) {
            lineChanges.push({
                lineId: newLine.id,
                type:   'UPDATED',
                //@ts-ignore
                old: { amount: oldLine.amount, pos: oldLine.position },
                new: { amount: newLine.amount, pos: newLine.position }
            })
        }
    })

    // __ Проверка на удаление строк
    oldLines.forEach(oldLine => {
        if (!newLines.find(l => l.id === oldLine.id)) {
            lineChanges.push({ lineId: oldLine.id, type: 'DELETED' })
        }
    })

    return lineChanges
}

// --- ------------------------------------------------------------------------------------


// __ Проблема с draggable
// __ Если день пустой, то перетаскивание не срабатывает
// __ Поэтому добавляем пустое задание в пустой день
export function correctRenderMatrix(matrix: IPlanMatrix) {
    let draftId = -100
    matrix.forEach((week, weekIndex) => {

        week.forEach((day, dayIndex) => {
            let filteredDay = day.filter(item => item.id > -1)      // __ id === 0 (для добавленного СЗ)
            // let filteredDay = day.filter(item => item.id !== SEWING_TASK_DRAFT.id)
            if (filteredDay.length === 0) {
                const draft = { ...SEWING_TASK_DRAFT, id: draftId--, position: 100 }
                filteredDay.push(draft)
            } else {
                // __ Сортируем по позиции (по порядку)
                // filteredDay = filteredDay.sort((a, b) => a.position - b.position)
            }
            matrix[weekIndex][dayIndex] = filteredDay
            // matrix[weekIndex][dayIndex] = {...filteredDay, fullDay: true}
        })
    })

    return matrix
}


// __ Пересчитываем позиции СЗ в матрице рендера после перетаскивания мышью
export function setTaskPositionInRenderMatrix(matrix: IPlanMatrix) {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {

            // __ Новая сортировка по позиции (СЗ статусы, которые не равны "Создано" - остаются на своих местах)
            const nonCreatedStatusTasks: ISewingTask[] = []
            const createdStatusTasks: ISewingTask[]    = []
            const resultDay: ISewingTask[]             = []

            // __ Фильтруем по статусу
            for (let i = 0; i < day.length; i++) {
                if (isTaskStatusCreated(day[i] as ISewingTask)) {
                    createdStatusTasks.push(day[i] as ISewingTask)
                } else {
                    nonCreatedStatusTasks.push(day[i] as ISewingTask)
                }
            }

            nonCreatedStatusTasks.forEach(task => resultDay.push(task))
            createdStatusTasks.forEach(task => resultDay.push(task))

            // console.log('nonCreatedStatusTasks: ', nonCreatedStatusTasks)
            // console.log('createdStatusTasks: ', createdStatusTasks)
            // console.log('resultDay: ', resultDay)
            // matrix[weekIndex][dayIndex] = resultDay

            matrix[weekIndex][dayIndex] = resultDay.map((item, index) => ({ ...item, position: index + 1 })) // __ id пустых заданий меньше нуля

            // __ Старая сортировка по позиции без учета всплытия статусов - без первой части
            // matrix[weekIndex][dayIndex] = day.map((item, index) => ({ ...item, position: index + 1 })) // __ id пустых заданий меньше нуля
        })
    })
    return matrix
}


// __ Сортируем задания в матрице рендера по позиции + сортируем строки по позиции
export function sortRenderMatrixByTaskPosition(matrix: IPlanMatrix) {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            matrix[weekIndex][dayIndex] = day.sort((a, b) => a.position - b.position)
            matrix[weekIndex][dayIndex].forEach(sewingTask => {
                sewingTask.sewing_lines = sewingTask.sewing_lines.sort((a: ISewingTaskLine, b: ISewingTaskLine) => a.position - b.position)
            })
        })
    })
    return matrix
}

// ___ Не доделанаа, используем другую
export function getDiffsInRenderMatrixs(currentMatrix: IPlanMatrix, memMatrix: IPlanMatrix) {

    const diffs: IRenderMatrixDiff = {
        dayFromOffset: 0,
        dayToOffset:   0,
        taskId:        0,
    }

    // __ Очищаем матрицу рендера от пустых сменных заданий, которые добавляем для рендеринга
    currentMatrix = clearRenderMatrix(currentMatrix)

    console.log('catch diffs')
    // return

    // // __ Получаем дату отсчета
    // let workDay = new Date(renderPeriod.start)
    // console.log(renderPeriod)

    // __ Проходим по всем неделям
    for (let i = 0; i < currentMatrix.length; i++) {

        // const workWeek = currentMatrix[i]

        const weekBefore = currentMatrix[i]
        const weekAfter  = memMatrix[i]

        // console.log('weekAfter: ', weekAfter)
        // console.log('weekBefore: ', weekBefore)

        // __ Проходим по всем дням
        for (let j = 0; j < 7; j++) {

            // __ Получаем дни для сравнения
            const dayAfter  = weekAfter[j]
            const dayBefore = weekBefore[j]

            // __ Получаем максимальный индекс для двух массивов
            const maxDayIndex = Math.max(dayAfter.length, dayBefore.length)

            // __ Проходим по всем СЗ
            for (let k = 0; k < maxDayIndex; k++) {

                // __ Проверяем, что есть оба элемента
                if (dayAfter[k] && dayBefore[k]) {

                    // ___ Оба элемента есть


                    // __ Проверяем, что элементы одинаковые по id и позиции
                    if (dayAfter[k].id === dayBefore[k].id &&
                        dayAfter[k].position === dayBefore[k].position) {

                        // ___ Элементы одинаковые по id и позиции


                        // __ Проверяем, что количество строк одинаковое проверяем на содержимое
                        if (dayAfter[k].sewing_lines.length === dayBefore[k].sewing_lines.length) {

                            // ___ Количество строк совпадает. Проверяем, что строки одинаковые


                        } else {

                            // ___ Количество строк не совпадает. Проверяем, что строки одинаковые


                        }


                    } else {

                        // ___ Элементы разные по id и позиции

                    }


                } else {

                    // ___ Один из элементов в каком-то дне отсутствует


                }


            }

        }

    }


    return diffs
}

// --- ------------------------------------------------------------------------------------

// --- ------------------------------------------------------------------------------------
/**
 *  __Находим глубокую разницу между массивами СЗ (текущим и копией)__ !!! Используется эта!
 * @param {ISewingTask[]} currentTasks  - __Массив после манипуляций (vuedraggable и т.д.)__
 * @param {ISewingTask[]} originalTasks - __Глубокая копия (tasksCopy)__
 */
export function getSewingTasksDiff(currentTasks: ISewingTask[], originalTasks: ISewingTask[]) {
    const diffs: ISewingTaskArrayDiff[] = []

    // console.log(currentTasks)
    // console.log(originalTasks)

    // __ Индексируем оригинал по ID для быстрого доступа
    const originalMap = new Map(originalTasks.map(task => [task.id, task]))
    const currentMap  = new Map(currentTasks.map(task => [task.id, task]))

    currentTasks.forEach((task) => {
        const original = originalMap.get(task.id)

        if (!original) {
            // __ Если задачи не было в исходном массиве

            const lineChanges: ISewingTaskArrayLineDiffs[] = []
            task.sewing_lines.forEach(line => lineChanges.push({
                lineId:    line.id,
                lineIdRef: line.id_ref,
                type:      'ADDED',
                amount:    { old: null, new: line.amount },
                position:  { old: null, new: line.position }
            }))

            diffs.push({
                taskId:    task.id,
                taskIdRef: task.id_ref,
                type:      'ADDED',
                // current:     task,
                taskChanges: {
                    action_at: { old: null, new: task.action_at },
                    position:  { old: null, new: task.position },
                },
                lineChanges,
            })
            return
        }

        // __ Сравниваем основные поля задачи
        const hasDateChanged     = task.action_at !== original.action_at
        const hasPositionChanged = task.position !== original.position

        // __ Сравниваем строки пошива (детально)
        const lineDiffs = getTaskLinesDiff(task.sewing_lines, original.sewing_lines)

        // __ Если есть изменения хотя бы в одном месте
        if (hasDateChanged || hasPositionChanged || lineDiffs.length > 0) {
            diffs.push({
                taskId: task.id,
                type:   'UPDATED',

                // __ Поля задачи
                taskChanges: {
                    action_at: hasDateChanged ? { old: original.action_at, new: task.action_at } : null,
                    position:  hasPositionChanged ? { old: original.position, new: task.position } : null,
                },
                // __ Массив изменений в строках
                lineChanges: lineDiffs
            })
        }
    })

    // __ 3. ПРОВЕРКА НА УДАЛЕНИЕ ЗАДАЧ (Новый блок)
    originalTasks.forEach((oldTask) => {
        if (!currentMap.has(oldTask.id)) {
            diffs.push({
                taskId: oldTask.id,
                type:   'DELETED' // Указываем серверу, что эту задачу нужно удалить
            })
        }
    })

    return diffs
}

/**
 * __ Сравнение внутренних строк ISewingTaskLine
 * @param currentLines
 * @param originalLines
 */
function getTaskLinesDiff(currentLines: ISewingTaskLine[], originalLines: ISewingTaskLine[]) {
    const diffs: ISewingTaskArrayLineDiffs[] = []
    const originalLinesMap                   = new Map(originalLines.map(l => [l.id, l]))

    currentLines.forEach((line) => {
        const originalLine = originalLinesMap.get(line.id)

        if (!originalLine) {
            diffs.push({
                lineId:    line.id,
                lineIdRef: line.id_ref,
                type:      'ADDED',
                // newPosition: line.position,
                amount:   { old: null, new: line.amount },
                position: { old: null, new: line.position }
            })
        } else {
            const isAmountChanged = line.amount !== originalLine.amount
            const isPosChanged    = line.position !== originalLine.position

            if (isAmountChanged || isPosChanged) {
                diffs.push({
                    lineId:   line.id,
                    type:     'UPDATED',
                    amount:   isAmountChanged ? { old: originalLine.amount, new: line.amount } : null,
                    position: isPosChanged ? { old: originalLine.position, new: line.position } : null
                })
            }
        }
    })

    // __ Проверка на удаление строк
    originalLines.forEach(oldLine => {
        if (!currentLines.find(l => l.id === oldLine.id)) {
            diffs.push({ lineId: oldLine.id, type: 'DELETED' })
        }
    })

    return diffs
}

// --- ------------------------------------------------------------------------------------

// --- ------------------------------------------------------------------------------------
// __ Проверяем, есть ли в массиве изменений хотя бы одна сущность для создания в БД
export function isAddItemsInDiffsPresents(diffs: ISewingTaskArrayDiff[]) {
    return diffs.some(taskDiff => {

        // __ 1. Проверяем саму задачу
        if (taskDiff.type === 'ADDED') return true

        // __ 2. Безопасно проверяем строки (используем опциональную цепочку ?. )
        // __ Проверяем, есть ли среди изменений строк хотя бы одно с типом 'ADDED'
        return taskDiff.lineChanges?.some(lineDiff => lineDiff.type === 'ADDED') ?? false
    })
}

// --- ------------------------------------------------------------------------------------

// --- ------------------------------------------------------------------------------------
// __ Превращаем массив объектов ISewingTask [{}, {}, ...] в массив массивов объектов [[...], [...]]
// __ которые сгруппированы по одинаковой Заявке с возможностью учитывать статусы Заявок в определенном дне
export function getSewingTasksGroupedByOrder(sewingTasks: ISewingTask[], applyStatus: boolean = true) {
    const clearDay = clearRenderMatrixDay(sewingTasks) // __ Возвращаем новый массив без пустых элементов
    const grouped = clearDay.reduce((acc, item) => {

        // __ Создаем уникальный составной ключ
        let key: string
        if (applyStatus) {
            key = `${item.order.id}-${item.current_status.id}`;
        } else {
            key = `${item.order.id}`;
        }

        // __ Если такого task_id еще нет в аккумуляторе, создаем пустой массив
        if (!acc[key]) {
            acc[key] = []
        }
        // __ Добавляем текущий объект в массив соответствующего task_id
        acc[key].push(item)
        return acc
    }, {} as ISewingTask)

    // __ Превращаем объект { 1: [...], 2: [...] } в массив массивов [[...], [...]]
    return Object.values(grouped)
}


// --- ------------------------------------------------------------------------------------
// __ Проверяем, есть ли в конкретном дне СЗ для какой-то конкретной Заявки
// __ Если передан entity типа ISewingTask и applyStatus = true, то проверяем еще на одинаковость статусов
export function getSewingTasksSameOrderInDay(
    entity: ISewingTask | ISewingTaskOrder | number,
    tasksList: ISewingTask[],
    date: string | null  = null,
    applyStatus: boolean = false) {

    let item
    if (isSewingTask(entity)) {
        item = entity.order.id
        if (!date) date = entity.action_at

    } else if (isSewingTaskOrder(entity)) {
        item = entity.id
    } else if (typeof entity === 'number') {
        item = entity
    } else {
        return []
        // throw new Error('Invalid entity type')
    }

    if (!date) {
        return []
    }

    if (isSewingTask(entity) && applyStatus) {
        return tasksList.filter(task =>
            task.action_at === date &&
            task.order.id === item &&
            task.current_status.id === entity.current_status.id)
    }

    return tasksList.filter(task => task.action_at === date && task.order.id === item)
}


// --- ------------------------------------------------------------------------------------
// __ Объединяем СЗ с одинаковыми Заявками (Заявки, к которым принадлежит СЗ)
export function mergeSewingTasks(tasks: ISewingTask[]): ISewingTask[] {
    const grouped = tasks.reduce((acc, task) => {
        const orderId = task.order.id

        if (!acc[orderId]) {

            // __ Если заказа еще нет в словаре, клонируем объект задачи
            // __ Используем структурированное клонирование, чтобы не мутировать исходный массив
            acc[orderId] = JSON.parse(JSON.stringify(task))

        } else {

            // __ Если заказ уже есть, объединяем его sewing_lines
            const targetTask = acc[orderId]

            task.sewing_lines.forEach((newLine) => {
                const existingLine = targetTask.sewing_lines.find(
                    (l) =>
                        // __ Ищем строку с тем же order_line.id и типом машины
                        l.order_line.id === newLine.order_line.id &&
                        // __ Смотрим, чтобы совпадали ШМ для average моделей
                        l.order_line.model.main.machine_type === newLine.order_line.model.main.machine_type
                )

                if (existingLine) {
                    // __ Если такая строка заказа уже есть — суммируем количество
                    existingLine.amount += newLine.amount
                } else {
                    // __ Если такой строки еще нет — добавляем её целиком
                    targetTask.sewing_lines.push(JSON.parse(JSON.stringify(newLine)))
                }
            })
        }

        return acc
    }, {} as Record<number, ISewingTask>)

    // __Возвращаем массив, сохраняя порядок первого появления каждого order.id
    return Object.values(grouped)
}

// --- ------------------------------------------------------------------------------------

// --- ------------------------------------------------------------------------------------
// __ Объединяем строки СЗ с принадлежностью к одной и той же строке Заявки
export function mergeSewingLines(lines: ISewingTaskLine[]): ISewingTaskLine[] {

    const grouped = lines.reduce((acc, line) => {

        // __ Создаем составной ключ: ID + Тип машины
        const machineType = line.order_line.model.main.machine_type
        const groupKey    = `${line.order_line.id}_${machineType}`

        if (!acc[groupKey]) {
            acc[groupKey] = JSON.parse(JSON.stringify(line))
        } else {

            // __ Складываем количество
            acc[groupKey].amount += line.amount

            // __ Складываем трудозатраты (time)
            // if (acc[groupKey].time && line.time) {
            //     for (const key in line.time) {
            //         acc[groupKey].time[key] = (acc[groupKey].time[key] || 0) + line.time[key];
            //     }
            // }
        }

        return acc
    }, {} as Record<string, ISewingTaskLine>)

    return Object.values(grouped)
}

// --- ------------------------------------------------------------------------------------

// --- ------------------------------------------------------------------------------------
// __ Пересчитываем позиции СЗ в массиве СЗ на определенный день
export function repositionSewingTaskInDay(tasks: ISewingTask[], action_at: string) {
    tasks
        // __ Отбираем только объекты на нужную дату
        .filter(item => item.action_at === action_at)
        // __ Сортируем их по возрастанию текущей позиции (включая x.1)
        .sort((a, b) => a.position - b.position)
        // __ Мутируем каждый объект, присваивая новый порядковый номер
        .forEach((item, index) => {
            item.position = index + 1
        })
    return tasks
}

// --- ------------------------------------------------------------------------------------
// __ Проверяем, является ли Статус СЗ "Создано" или "Создано при закрытии"
export function isTaskStatusCreated(entity: ISewingTask | ISewingTaskStatus | number) {
    let item: number
    if (isSewingTask(entity)) {
        item = entity.current_status.id
    } else if (isSewingTaskStatus(entity)) {
        item = entity.id
    } else if (typeof entity === 'number') {
        item = entity
    } else {
        throw new Error('Invalid entity type')
    }
    return item === SEWING_TASK_STATUSES.CREATED.ID || item === SEWING_TASK_STATUSES.CREATED_CLOSE.ID
}

// --- -------------------------------------------------------------------------------------
// __ Получаем разницу в днях между двумя датами в формате "YYYY-MM-DD HH:mm:ss"
export function getDaysDifferenceFromSewingDates(date1: string, date2: string) {
        const d1 = date1.split(' ')[0]
        const d2 = date2.split(' ')[0]
        return getDaysDifference(d1, d2, true)
}
// --- -------------------------------------------------------------------------------------


// __ Дополнительно проверяем, является ли модель Чехлом
export function isCover(element: ISewingTaskModel) {
    return element.name.toLowerCase().includes('чехол')
}

// __ Дополнительно проверяем, является ли модель Чехлом
export function isAverage(element: ISewingTaskModel | ISewingTaskLine) {
    if (isSewingTaskLine(element)) return element.element_type.is_average
    return element.machine_type_ref === SEWING_MACHINES.AVERAGE
}


// __ Функция-помощник: говорит TS, является ли item типом ISewingTaskLine
function isSewingTaskLine(item: any): item is ISewingTaskLine {
    return item && typeof item === 'object' && 'order_line' in item
}

// __ Функция-помощник: говорит TS, является ли item типом ISewingTaskOrderLine
function isSewingTaskOrderLine(item: any): item is ISewingTaskOrderLine {
    return item && typeof item === 'object' && 'models' in item && 'dims' in item
}

// __ Функция-помощник: говорит TS, является ли item типом ISewingTaskOrderLine['model']
function isSewingTaskOrderLineModel(item: any): item is ISewingTaskOrderLine['model'] {
    return item && typeof item === 'object' && 'main' in item && 'base' in item && 'cover' in item
}

// __ Функция-помощник: говорит TS, является ли item типом ISewingTask
function isSewingTask(item: any): item is ISewingTask {
    return item && typeof item === 'object' && 'order' in item && 'sewing_lines' in item
}

// __ Функция-помощник: говорит TS, является ли item типом ISewingTaskOrder
function isSewingTaskOrder(item: any): item is ISewingTaskOrder {
    return item && typeof item === 'object' && 'client' in item && 'order_type' in item
}

// __ Функция-помощник: говорит TS, является ли item типом ISewingTaskStatus
function isSewingTaskStatus(item: any): item is ISewingTaskStatus {
    return item && typeof item === 'object' && 'id' in item && 'color' in item && 'name' in item && 'pivot' in item
}
