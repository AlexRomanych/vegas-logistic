// INFO Интерфейс для клиентов

export type IClientRegion = 'east' | 'west'

export interface IClient {
    id: number
    name: string
    add_name: string
    short_name: string
    active: boolean
    address: string | null
    comment: string | null
    country: string | null
    description: string | null
    latitude: number
    longitude: number
    manager_id: number
    meta: string | null
    note: string | null
    region: IClientRegion
    code_1c: string | null

    can_edit?: boolean
}
