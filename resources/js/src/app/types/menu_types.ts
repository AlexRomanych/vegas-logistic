export type IMenu = IMenuItem[]

export interface IMenuItem {
    group: IMenuGroup
    items: IMenuGroupItem[]
}

export interface IMenuGroup {
    icon: string
    id: number
    isActive: boolean
    name: string
    shown: boolean
}

export interface IMenuGroupItem {
    id: number
    isActive: boolean
    name: string
    path: string
    shown: boolean
}
