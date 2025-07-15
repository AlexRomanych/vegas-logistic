// info: Причина невыполнения

export interface IReason {
    id: number
    name: string
    active: boolean
    display_name: string
    description: string
    reason_number_in_reason_category: number
}

export interface IReasonCategory {
    id: number
    name: string
    active: boolean
    display_name: string
    description: string
    group_number_in_cell_group: number
    reasons: IReason[]
}

export interface ICellsGroupReasons {
    id: number
    name: string
    reason_categories: IReasonCategory[]
}

