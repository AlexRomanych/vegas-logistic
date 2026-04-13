


import type { IModelConstructItem, IModelConstructMaterial } from '@/types'

// __ Функция-помощник: говорит TS, является ли item типом IModelConstructItem
export function isModelConstructItem(item: any): item is IModelConstructItem {
    return item && typeof item === 'object' && 'material' in item && 'procedure' in item && 'detail' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IModelConstructMaterial
export function isModelConstructMaterial(item: any): item is IModelConstructMaterial {
    return item && typeof item === 'object' && 'code_1c' in item && 'name' in item && 'unit' in item
}

