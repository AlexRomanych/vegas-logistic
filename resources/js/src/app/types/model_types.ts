// info Тут все типы для Моделей


// --- --------------------------------------------------------------------
// --- ----------------- Для рендера Моделей (Show) -----------------------
// --- --------------------------------------------------------------------

// __ Коллекция
export interface IModelCollection {
    code_1c: string
    name: string
    active: boolean
    color: string | null
    description: string | null
    models: IModel[]

    collapsed: boolean
    shown: boolean
}

// __ Модель
export interface IModel {
    code_1c: string
    serial: string | null
    name: string
    name_report: string | null
    base_height: number
    cover_height: number
    textile: string | null
    textile_composition: string | null
    stitch_pattern: string | null
    base_composition: string | null
    side_foam: string | null
    side_height: string | null
    base_block: string | null
    lamit: boolean
    sewing_machine: string | null
    kant: string | null
    tkch: string | null
    kdch: string | null
    cover_type: string | null
    barcode: string | null
    description: string | null

    cover: IModel | null
    model_type: IModelType | null
    manufacture_group: IModelManufactureGroup | null
    manufacture_type: IModelManufactureType | null
    manufacture_status: IModelManufactureStatus | null
    sewing_operation_schema: IModelSewingOperationSchema | null
    collection: IModelCollection | null

    cover_collapsed: boolean
}


// __ Схема трудозатрат по швейным операциям
export interface IModelSewingOperationSchema {
    id: number
    name: string
}

// __ Тип изделия (матрас, чехол, подушка и т.д.)
export interface IModelType {
    code_1c: string
    name: string
}

// __ Группа производства (FMX, Неопознанные, и т.д.)
export interface IModelManufactureGroup {
    id: number
    name: string
    group_number: number
}

// __ Тип производства (Производство матрасов, Производство чехлов, Производство подушек, и т.д.)
export interface IModelManufactureType {
    code_1c: string
    name: string
}

// __ Статус производства (Выпускается, Вариант исполнения, Архив, и т.д.)
export interface IModelManufactureStatus {
    id: number
    name: string
}


// --- --------------------------------------------------------------------
// --- -------- Для рендера Спецификаций Моделей (Show) -------------------
// --- --------------------------------------------------------------------

// __ Спецификация (Модель для группировки информации по спецификациям)
export interface IModelSpecification {
    code_1c: string
    name: string
    constructs: IModelConstruct[]

    collapsed: boolean
    shown: boolean
}

// __ Спецификации
export interface IModelConstruct {
    code_1c: string
    name: string
    items: IModelConstructItem[]

    collapsed: boolean
    shown: boolean
}

// __ Элемент спецификации
export interface IModelConstructItem {
    detail_height: number | null
    detail: string | null
    amount: number
    position: number
    material: IModelConstructMaterial               // __ Тут подменяем в контроллере и material будет всегда
    procedure: IModelConstructProcedure | null      // __ Тут может быть и null
}

// __ Материал
export interface IModelConstructMaterial {
    code_1c: string
    // code_1c_copy: string
    name: string
    unit: string | null
    in_base: boolean,
}

// __ Процедура расчета в элементе спецификации
export interface IModelConstructProcedure {
    name: string | null
}

// --- --------------------------------------------------------------------
// --- ------------ Для рендера Процедур расчета (Show) -------------------
// --- --------------------------------------------------------------------

// __ Процедура расчета
export interface IModelProcedure {
    code_1c: string
    name: string
    object_code_1c: string | null
    object_name: string | null
    text: string | null
    text_vba: string | null

    collapsed: boolean
}
