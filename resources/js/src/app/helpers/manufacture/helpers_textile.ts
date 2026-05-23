import type { ICuttingMachineKeys, ICuttingTaskLine, ISewingMachineKeys, ISewingTaskLine } from '@/types'
import { SEWING_MACHINES } from '@/app/constants/sewing.ts'


export function getSewingMachineTitle(line: ISewingTaskLine | ICuttingTaskLine) {
    const machine = getLineMachineType(line)

    console.log(line)
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
        machineType = getMachineType(line.order_line.model.base.machine_type)         // __ Получаем тип машины модели по базовой модели
    } else if (!line.order_line.model.base && line.order_line.model.cover) {    // __ Это условие того, что элемент - матрас
        machineType = getMachineType(line.order_line.model.main.machine_type)         // __ Получаем тип машины модели по основной модели
    } else if (!line.order_line.model.base && !line.order_line.model.cover) {   // __ Это условие того, что элемент - расчетный)
        machineType = getMachineType(line.order_line.model.main.machine_type)         // __ Получаем тип машины модели по основной модели
    }

    return machineType
}


export function getMachineType(machineType: ISewingMachineKeys) {
    console.log(machineType)
    const findMachineTypeKey = Object.keys(SEWING_MACHINES).find(key => SEWING_MACHINES[key] === machineType)
    return findMachineTypeKey ? SEWING_MACHINES[findMachineTypeKey] : null
}
