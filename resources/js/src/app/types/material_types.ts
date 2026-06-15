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
export interface IMaterial extends IMaterialCommon {
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


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!!     --- Материалы для вывода расхода по заказам    !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Общие типы, так как отношения Материалов - рекурсивны
export interface IMaterialRenderCommon {
    code_1c: string
    name: string
    collapsed: boolean
}

// __ Группа материала
export interface IMaterialRenderGroup extends IMaterialRenderCommon {
    categories: IMaterialRenderCategory[]
}

// __ Категория материала
export interface IMaterialRenderCategory extends IMaterialRenderCommon {
    materials: IMaterialRender[]
}

// __ Сам материал
export interface IMaterialRender extends IMaterialRenderCommon {
    orders: IMaterialRenderOrder[]
    unit: string
    material_amount: number
    material_total: number
    // object_name: string | null
}

// __ Заказы, к которым относится материал
export interface IMaterialRenderOrder {
    order_id: number,
    order_no: string,
    client_name: string,
    order_lines: IMaterialRenderOrderLine[]
    order_amount: number
    order_total: number
    collapsed: boolean
}

// __ Запись в Заказе (Линия)
export interface IMaterialRenderOrderLine {
    amount: number
    expense: number
    line_id: number
    model_code_1c: string
    model_name: string
    rest: number
    size: IMaterialRenderOrderLineDims
    total: number
}


export interface IMaterialRenderOrderLineDims {
    height: number
    length: number
    width: number
}
