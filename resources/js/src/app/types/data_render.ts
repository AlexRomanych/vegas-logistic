// info: Интерфейс вывода данных в шаблонах

import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import type { IFontsType } from '@/app/constants/fontSizes.ts'
import type { IHorizontalAlign } from '@/app/types'

export interface IRenderDataItem {
    id?: string | ((...args: any[]) => string)
    header: string | string[] | ((...args: any[]) => string | string[])
    headerType: ((...args: any[]) => IColorTypes) | IColorTypes
    width: string
    show: boolean
    type: ((...args: any[]) => IColorTypes) | IColorTypes
    height?: string
    class?: string
    placeholder?: string
    title?: string | ((...args: any[]) => string)
    headerTextSize?: IFontsType
    dataTextSize?: IFontsType
    filterTextSize?: IFontsType
    textSize? : IFontsType | ((...args: any[]) => string)
    headerAlign?: IHorizontalAlign
    dataAlign?: IHorizontalAlign
    align?: IHorizontalAlign
    data?: (...args: any[]) => string
    click?: (...args: any[]) => void
    color?: (...args: any[]) => string
}

export interface IRenderData {
    [key: string]: IRenderDataItem
}

