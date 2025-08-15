// info: Интерфейс вывода данных в шаблонах

import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import type { IFontsType } from '@/app/constants/fontSizes.ts'
import type { IHorizontalAlign } from '@/app/types'

export interface IRenderDataItem {
    header: string | string[]
    width: string
    show: boolean
    type: (...args: any[]) => string | IColorTypes,
    class?: string
    placeholder?: string
    title?: string
    headerTextSize?: IFontsType
    dataTextSize?: IFontsType
    filterTextSize?: IFontsType
    headerAlign?: IHorizontalAlign
    dataAlign?: IHorizontalAlign
    data?: (...args: any[]) => string,
    click?: (...args: any[]) => void,
}

export interface IRenderData {
    [key: string]: IRenderDataItem
}

