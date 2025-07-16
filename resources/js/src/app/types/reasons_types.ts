// info: Причина невыполнения

export interface IReason {
    id: number
    name: string
    active: boolean
    display_name: string
    description: string
    reason_number_in_reason_category: number
    reason_category_id: number
}

export interface IReasonCategory {
    id: number
    name: string
    active: boolean
    display_name: string
    description: string
    group_number_in_cell_group: number
    reasons: IReason[]
    collapsed: boolean
    cells_group_id: number
}

export interface ICellsGroupReasons {
    id: number
    name: string
    collapsed: boolean
    reason_categories: IReasonCategory[]
}

