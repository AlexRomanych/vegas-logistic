import type {
    ICuttingMachineKeys,
    ICuttingTaskLine,
    ICuttingTaskModel,
    ICuttingTaskOrderLine,
    ISewingMachineKeys,
    ISewingTaskLine,
    ISewingTaskModel, ISewingTaskOrderLine
} from '@/types'
import { SEWING_MACHINES } from '@/app/constants/sewing.ts'

// __ Получаем название ШМ
export function getSewingMachineTitle(line: ISewingTaskLine | ICuttingTaskLine) {
    const machine = getLineMachineType(line)

    // console.log(line)
    // console.log(machine)

    switch (machine) {
        case SEWING_MACHINES.UNIVERSAL:
            return 'У'
        case SEWING_MACHINES.AUTO:
            return 'А'
        case SEWING_MACHINES.SOLID_HARD:
            return 'ГС'
        case SEWING_MACHINES.SOLID_LITE:
            return 'ГП'
    }

    return '??'
}




export function getLineMachineType(line: ISewingTaskLine | ICuttingTaskLine) {

    // __ Получаем тип машины модели
    let machineType: ICuttingMachineKeys | null = SEWING_MACHINES.UNDEFINED
    if (line.order_line.model.base && !line.order_line.model.cover) {           // __ Это условие того, что элемент - чехол
        machineType = getMachineType(line.order_line.model.base.machine_type)   // __ Получаем тип машины модели по базовой модели
    } else if (!line.order_line.model.base && line.order_line.model.cover) {    // __ Это условие того, что элемент - матрас
        machineType = getMachineType(line.order_line.model.main.machine_type)   // __ Получаем тип машины модели по основной модели
    } else if (!line.order_line.model.base && !line.order_line.model.cover) {   // __ Это условие того, что элемент - расчетный)
        machineType = getMachineType(line.order_line.model.main.machine_type)   // __ Получаем тип машины модели по основной модели
    }

    return machineType
}


export function getMachineType(machineType: ISewingMachineKeys) {
    const findMachineTypeKey = Object.keys(SEWING_MACHINES).find(key => SEWING_MACHINES[key] === machineType)
    return findMachineTypeKey ? SEWING_MACHINES[findMachineTypeKey] : null
}



// __ Получаем КДЧ
export function getKDCH(item: ISewingTaskLine | ICuttingTaskLine | ISewingTaskModel | ICuttingTaskModel | ICuttingTaskOrderLine | ISewingTaskOrderLine): string {
    let source
    if (isModel(item)) {
        return item.kdch ?? ''
    } else if (isTaskLine(item)) {
        source = item.order_line.model
    } else if (isTaskOrderLine(item)) {
        source = item.model
    } else {
        return ''
    }

    if (source.cover && source.cover.kdch) {
        return source.cover.kdch
    } else if (source.main && source.main.kdch) {
        return source.main.kdch
    } else if (source.base && source.base.kdch) {
        return source.base.kdch
    }

    return ''
}

// __ Получаем cсылку на файл КДЧ
export function getDocFileKDCH(item: ISewingTaskLine | ICuttingTaskLine | ISewingTaskModel | ICuttingTaskModel | ICuttingTaskOrderLine | ISewingTaskOrderLine): number | null {
    let source
    if (isModel(item)) {
        return item.kdch_doc ?? null
    } else if (isTaskLine(item)) {
        source = item.order_line.model
    } else if (isTaskOrderLine(item)) {
        source = item.model
    } else {
        return null
    }

    if (source.cover && source.cover.kdch_doc) {
        return source.cover.kdch_doc
    } else if (source.main && source.main.kdch_doc) {
        return source.main.kdch_doc
    } else if (source.base && source.base.kdch_doc) {
        return source.base.kdch_doc
    }

    return null
}


// __ Функция-помощник: говорит TS, является ли item типом ISewingTaskLine или ICuttingTaskLine
function isTaskLine(item: unknown): item is (ISewingTaskLine | ICuttingTaskLine) {
    return !!item && typeof item === 'object' && 'order_line' in item /*&& !('table' in item)*/
}

// __ Функция-помощник: говорит TS, является ли item типом ICuttingTaskModel или ISewingTaskModel
function isModel(item: unknown): item is (ICuttingTaskModel | ISewingTaskModel) {
    return !!item && typeof item === 'object' && 'kdch' in item && 'tkch' in item
}

// __ Функция-помощник: говорит TS, является ли item типом ICuttingTaskOrderLine или ISewingTaskModel
function isTaskOrderLine(item: unknown): item is (ICuttingTaskOrderLine | ISewingTaskOrderLine) {
    return !!item && typeof item === 'object' && 'dims' in item && 'model' in item
}
