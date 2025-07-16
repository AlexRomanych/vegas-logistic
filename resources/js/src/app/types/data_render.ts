// info: Интерфейс вывода данных в шаблонах

import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import type { IFontsType } from '@/app/constants/fontSizes.ts'
import type { IHorizontalAlign } from '@/app/types'

export interface IRenderDataItem {
    header: string | string[]
    width: string
    show: boolean
    type: IColorTypes
    placeholder?: string
    title?: string
    headerTextSize?: IFontsType
    dataTextSize?: IFontsType
    filterTextSize?: IFontsType
    headerAlign?: IHorizontalAlign
    dataAlign?: IHorizontalAlign
    data?: (...args: any[]) => string,
}

export interface IRenderData {
    [key: string]: IRenderDataItem
}

