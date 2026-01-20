// info Тут все, что связано с Пошивом


import type { IAmountAndTime, ISewingMachineKeys, ISewingTask, ISewingTaskLine } from '@/types'
import { SEWING_MACHINES } from '@/app/constants/sewing.ts'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'


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

    const machineType      = getSewingLineMachineType(sewingLine)
    if (!machineType) {
        return 'н/д'
    }

    const amountAndTimeObj = getSewingTimes(sewingLine)
    let time = 0

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
