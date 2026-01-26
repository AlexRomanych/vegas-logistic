// INFO: Тут будут интерфейсы для всех данных компонентов


// line-------------------------------------------------------
// ___ Данные для компонента AppSelect
export interface ISelectDataItem {
    id: number
    name: string
    selected?: boolean
    disabled?: boolean
}
export interface ISelectData {
    name: string
    data: ISelectDataItem[]
}








// line-------------------------------------------------------
// ___ Данные для компонента AppCheckbox
export interface ICheckboxDataItem {
    id: number
    name: string
    checked?: boolean
    disabled?: boolean
}
export interface ICheckboxData {
    name: string
    data: ICheckboxDataItem[]
}



// --- --------------------------------------------------------
// ___ Данные для компонента AppRangeModalAsyncTS
export interface IDividerItem {
    name: string
    amount: number
}



// --- --------------------------------------------------------
// ___ Данные для компонента AppModalAsyncMenuTS
export interface IModalAsyncMenu {
    name?: string
    data: IModalAsyncMenuItem[]
}

export interface IModalAsyncMenuItem {
    id: number
    title: string | string[]
}
