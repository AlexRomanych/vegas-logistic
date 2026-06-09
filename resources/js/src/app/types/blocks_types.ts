export interface IBlockCollection {
    id: number
    code_1c: string
    name: string
    unit: string | null
    kdb: string | null
    line: number
    priority: number
    height: number
    active: boolean
    description: string | null
    blocks: IBlock[]

    collapsed?: boolean
    can_edit?: boolean
}

export interface IBlock {
    id: number
    code_1c: string
    name: string
    unit: string | null
    width: number
    length: number
    active: boolean
    description: string | null

    can_edit?: boolean
}
