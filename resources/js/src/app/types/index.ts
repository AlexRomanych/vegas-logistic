import { FABRIC_PAGE_MODE } from '@/app/constants/fabrics.js'

export * from './reasons_types.ts'                  // Причина невыполнения
export * from './data_render.ts'                    // Элемент вывода датных в шаблон
export * from './components_definitions.ts'         // Компоненты
export * from './fabric_types.ts'                   // Все типы для стежки
export * from './client_types.ts'                   // Все типы для клиентов
export * from './plan_types.ts'                     // Все типы для планов


// ___ Константы выравнивания текста
export type IHorizontalAlign = 'left' | 'right' | 'center'

// ___ Константы режима работы компонента
export type IPageMode = typeof FABRIC_PAGE_MODE[keyof typeof FABRIC_PAGE_MODE]
export type IRouteMeta = {
    title: string
    mode?: IPageMode
    data?: string | number
}



