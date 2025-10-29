// Интерфейс для клиентов
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
    region: 'west' | 'east'

    can_edit?: boolean
}
