// info Business Processes types


// ___ Интерфейс бизнес процесса для задания констант
export type IBusinessProcessesConst = Record<IBusinessProcessesConstNames, IBusinessProcessConst>

export interface IBusinessProcessConst {
    ID: number
    NAME: string
}

export type IBusinessProcessesConstNames =
    'ORDER_MOVING' |
    'DEFAULT'

// line -----------------------------------------------------


// ___ Интерфейс для списка бизнес процессов
export interface IBusinessProcessList {
    id: number
    name: string
    active: boolean
    description: string | null
    comment: string | null
    belongs_to: string | null
    type: string
    reference_node: IBusinessProcessNodeList | null
    start_node: IBusinessProcessNodeList | null
    finish_node: IBusinessProcessNodeList | null
    can_edit?: boolean
}


export interface IBusinessProcessNodeList {
    id: number
    name: string
    active: boolean
    description: string | null
    comment: string | null
    has_module: boolean
    route_name: string
}

// line -----------------------------------------------------

// ___ Интерфейс для рендеринга бизнес-процесса
export interface IBusinessProcessRender {
    node: IBusinessProcessItem
    'in': IBusinessProcessItem[]
    'out': IBusinessProcessItem[]
}

export interface IBusinessProcessItem {
    id: number
    name: string
    has_module: boolean
    route_name: string
    defaults?: IBusinessProcessItemDefaults[]
}

export interface IBusinessProcessItemDefaults {
    client: {
        id: number
        name: string
        short_name: string
    }
    offset: number
    process_node_ref_id: number
}

// line -----------------------------------------------------


// ___ Интерфейс для узлов бизнес-процесса
export type IBusinessProcessNodesConst = Record<IBusinessProcessNodeNames, IBusinessProcessNodeConst>

export interface IBusinessProcessNodeConst {
    ID: number
    NAME: string
}

export type IBusinessProcessNodeNames =
    'DEFAULT' |
    'LOADS' |
    'ASSEMBLY' |
    'CUTTING' |
    'SEWING'

// line -----------------------------------------------------

// ___ Интерфейс для узла бизнес-процесса
export interface IBusinessProcessNode {
    id: number
    name: string
    type: string | null
    has_module: boolean | null
    route_name: string | null
    allow_action?: boolean | null
    active?: boolean
    status?: string | null
    order?: number
    description: string | null
}
