import { FABRIC_PAGE_MODE } from '@/app/constants/fabrics.js'
import type { ISewingTask } from '@/types/sewing_types.ts'
import type { ICuttingTask } from '@/types/cutting_types.ts'
import type { IBlockTask } from '@/types/blocks_types.ts'
import type { IPlanMatrixDayItem } from '@/types/plan_types.ts'

export * from './reasons_types.ts'                  // Причина невыполнения
export * from './data_render.ts'                    // Элемент вывода датных в шаблон
export * from './components_definitions.ts'         // Компоненты
export * from './fabric_types.ts'                   // Все типы для стежки
export * from './client_types.ts'                   // Все типы для клиентов
export * from './plan_types.ts'                     // Все типы для планов
export * from './log_types.ts'                      // Все типы для логов
export * from './business_processes_types.ts'       // Все типы для бизнес-процессов
export * from './order_types.ts'                    // Все типы для Заявок
export * from './worker_types.ts'                   // Все типы для Персонала
export * from './material_types.ts'                 // Все типы для Материалов
export * from './model_types.ts'                    // Все типы для Моделей


export * from './textile_types.ts'                  // Общие типы для Пошива и Раскроя
export * from './sewing_types.ts'                   // Все типы для Пошива
export * from './cutting_types.ts'                  // Все типы для Раскроя
export * from './blocks_types.ts'                   // Все типы для Блоков
export * from './assembly_types.ts'                 // Все типы для Сборки
export * from './cell_event_types.ts'               // События для производственных участков


export type { IFontsType } from '@/app/constants/fontSizes.ts'   // Все типы для шрифтов
export type { IColorTypes } from '@/app/constants/colorsClasses.ts'

// ___ Константы выравнивания текста
export type IHorizontalAlign = 'left' | 'right' | 'center'

// ___ Константы режима работы компонента
export type IPageMode = typeof FABRIC_PAGE_MODE[keyof typeof FABRIC_PAGE_MODE]
export type IRouteMeta = {
    title: string
    mode?: IPageMode
    data?: string | number
}


// ___ Типы для валидации ввода даты
export type IDataInputObj = {
    newValue: string
    oldValue: string
}


// ___ Тип для размера
export type IDims = {
    width: number
    length: number
    height: number
}


export type IDay = (ISewingTask | ICuttingTask | IBlockTask) & IPlanMatrixDayItem

// --- ------------------------------------------------------------
// __ Типы для разницы для каждой записи в матрице Пошива
export type IDiffsType = 'UPDATED' | 'ADDED' | 'DELETED'


// __ Чтобы анализатор не ругался
export interface DraggableHTMLElement extends HTMLElement {
    item: {
        _underlying_vm_?: unknown // Вместо any можно указать тип вашей модели, например: User, Task и т.д.
    },
    draggedContext: {
        element?: unknown
    }

    // _underlying_vm_?: IBlockTaskLine // Вместо any можно указать тип вашей модели, например: User, Task и т.д.
}
