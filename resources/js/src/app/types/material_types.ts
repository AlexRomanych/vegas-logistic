// info Тут все типы для Материалов


// __ Общие типы, так как отношения Материалов - рекурсивны
interface IMaterialCommon {
    code_1c: string
    name: string
    active: boolean
    is_shown: boolean
    order: number
}

// __ Сам материал
export interface IMaterial extends IMaterialCommon{
    unit: string | null
    alt_unit: string | null
    description: string | null
    supplier: string | null
    object_name: string | null
    properties: Record<string, string>[] | null
}

// __ Категория материала
export interface IMaterialCategory extends IMaterialCommon {
    materials: IMaterial[]
    collapsed: boolean
}

// __ Группа материала
export interface IMaterialGroup extends IMaterialCommon {
    categories: IMaterialCategory[]
    collapsed: boolean
}
