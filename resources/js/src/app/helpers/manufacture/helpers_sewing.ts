// info Тут все, что связано с Пошивом

import type {
    IAmountAndTime, ISewingLinesPanel,
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


// __ Дополнительно проверяем, является ли модель Чехлом
export function isCover(element: ISewingTaskModel) {
    return element.name.toLowerCase().includes('чехол')
}

// __ Дополнительно проверяем, является ли модель Чехлом
export function isAverage(element: ISewingTaskModel | ISewingTaskLine) {
    if (isSewingTaskLine(element)) return element.is_average
    return element.machine_type === SEWING_MACHINES.AVERAGE
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
