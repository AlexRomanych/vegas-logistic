// info Business Processes types

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
