


import type { IModelConstructItem, IModelConstructMaterial } from '@/types'

// __ Функция-помощник: говорит TS, является ли item типом IModelConstructItem
export function isModelConstructItem(item: any): item is IModelConstructItem {
    return item && typeof item === 'object' && 'material' in item && 'procedure' in item && 'detail' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IModelConstructMaterial
export function isModelConstructMaterial(item: any): item is IModelConstructMaterial {
    return item && typeof item === 'object' && 'code_1c' in item && 'name' in item && 'unit' in item
}

// __ Возвращаем название Чехла
export function getMachineName(machine: string|null = null, short: boolean = true): string {
    switch (machine) {
        case 'universal': return short ? 'У' : 'Универсал'
        case 'auto': return short ? 'А' : 'Автомат'
        case 'solid_hard': return short ? 'ГС' : 'Глухой сложный'
        case 'solid_lite': return short ? 'ГП' : 'Глухой простой'
        case 'average': return short ? 'AV' : 'Расчетный'
        case 'undefined': return short ? '' : 'Нет данных'
    }

    return '??'
}
