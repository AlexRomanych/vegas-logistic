// info Тут все, что связано с Пошивом

import type {
    IAmountAndTime, IPlanMatrix, IRenderMatrixDiff, IRenderMatrixLineDiffs,
    ISewingMachineKeys, ISewingMachineTimesKeys,
    ISewingTask,
    ISewingTaskLine, ISewingTaskLineAmountAvg, ISewingTaskLineTime,
    ISewingTaskModel,
    ISewingTaskOrderLine
} from '@/types'

import { SEWING_MACHINES } from '@/app/constants/sewing.ts'

import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'
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
export function repositionSewingTaskLines(items: ISewingTaskLine[]) {
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

    // 1. Создаем плоскую карту из копии для быстрого поиска исходного состояния по ID
    // Это поможет нам понять, откуда пришла задача, если она переместилась
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

    // 2. Обходим текущую матрицу
    currentMatrix.forEach((week, weekIdx) => {
        week.forEach((dayTasks, dayIdx) => {
            const currentDayOffset = weekIdx * 7 + dayIdx

            dayTasks.forEach((task) => {
                const original = copyMap.get(task.id)

                // Если задачи не было в исходной матрице (новое СЗ)
                if (!original) {
                    diffs.push({
                        type:         'NEW_TASK',
                        taskId:       task.id,
                        dayToOffset: currentDayOffset,
                        newPosition:  task.position
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

/**
 * Вспомогательная функция для сравнения массива строк пошива
 */
function getLinesDiff(oldLines: ISewingTaskLine[], newLines: ISewingTaskLine[]) {
    const lineChanges: IRenderMatrixLineDiffs[] = []

    // Проверяем каждую строку в задаче
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
                type: 'UPDATED',
                //@ts-ignore
                old:    { amount: oldLine.amount, pos: oldLine.position },
                new:    { amount: newLine.amount, pos: newLine.position }
            })
        }
    })

    // Проверка на удаление строк
    oldLines.forEach(oldLine => {
        if (!newLines.find(l => l.id === oldLine.id)) {
            lineChanges.push({ lineId: oldLine.id, type: 'DELETED' })
        }
    })

    return lineChanges
}

// --- ------------------------------------------------------------------------------------





// __ Пересчитываем позиции СЗ в матрице рендера после перетаскивания мышью
export function setTaskPositionInRenderMatrix(matrix: IPlanMatrix) {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            matrix[weekIndex][dayIndex] = day.map((item, index) => ({ ...item, position: index + 1 })) // __ id пустых заданий меньше нуля
        })
    })
    return matrix
}


// __ Сортируем задания в матрице рендера по позиции
export function sortRenderMatrixByTaskPosition(matrix: IPlanMatrix) {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            matrix[weekIndex][dayIndex] = day.sort((a, b) => a.position - b.position)
        })
    })

}


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

// __ Функция-помощник: говорит TS, является ли item типом ISewingTaskLine
function isSewingTaskOrderLine(item: any): item is ISewingTaskOrderLine {
    return item && typeof item === 'object' && 'models' in item && 'dims' in item
}

// __ Функция-помощник: говорит TS, является ли item типом ISewingTaskOrderLine['model']
function isSewingTaskOrderLineModel(item: any): item is ISewingTaskOrderLine['model'] {
    return item && typeof item === 'object' && 'main' in item && 'base' in item && 'cover' in item
}
