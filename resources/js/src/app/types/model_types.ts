// info Тут все типы для Моделей

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

