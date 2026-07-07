// Info Константы для работы с Блоками (Blocks)

import type {
    IBlockCollection,
    IBlock,
    IBlockTask,
    IBlockManufLine,
    IBlockTaskStatusKeys,
    IBlockTaskStatusItem,
    IBlockTaskChange
} from '@/types'

// __ Продолжительность смены в часах
export const TOTAL_SHIFT_DURATION = 12

// __ Время начала смены
export const START_SHIFT_TIME = '07:30'


export const LINE_0 = '0'
export const LINE_1 = '1'
export const LINE_2 = '2'

export const LINE_0_NAME = 'Н/Д'
export const LINE_1_NAME = 'Линия 1'
export const LINE_2_NAME = 'Линия 2'

export const UNIT        = ''
export const UNIT_PICS   = 'шт.'
export const UNIT_METERS = 'м.п.'

export const CHANGE_1 = '1'
export const CHANGE_2 = '2'


// __ Объект Коллекции блоков
export const BLOCK_COLLECTION_DRAFT: IBlockCollection = {
    id          : 0,
    code_1c     : '',
    name        : '',
    unit        : null,
    kdb         : null,
    kdb_id      : null,
    line        : LINE_1,
    line_alt    : LINE_0,
    priority    : 0,
    height      : 0,
    length      : 0,
    productivity: 0,
    active      : true,
    own         : true,
    description : null,
    blocks      : [],
}


// __ Объект Блока
export const BLOCK_DRAFT: IBlock = {
    id         : 0,
    code_1c    : '',
    name       : '',
    unit       : null,
    width      : 0,
    length     : 0,
    active     : true,
    description: null,
    collection: '000000000' // Без коллекции

}

// __ Болванка СЗ Блоков
export const BLOCK_TASK_DRAFT: IBlockTask = {
    id            : 0,
    id_ref        : 0,
    action_at     : '',
    active        : true,
    change        : CHANGE_1,
    position      : 0,
    comment       : null,
    order         : {
        id             : 0,
        order_no_num   : 0,
        order_no_origin: '0',
        order_no_str   : '0',
        load_at        : null,
        comment_1c     : null,
        client         : {
            id        : 0,
            name      : '',
            add_name  : '',
            short_name: '',
        },
        order_type     : {
            id          : 0,
            display_name: '',
            color       : '',
        }
    },
    block_lines : [],
    statuses      : [],
    current_status: {
        id   : 0,
        name : '',
        color: '',
        pivot: {
            created_at : null,
            duration   : null,
            finished_at: null,
            set_at     : null,
            started_at : null,
        }

    }
}


// __ Константы смен
export const CHANGES = {
    CHANGE_1: {
        ID: 1,
        NAME: CHANGE_1,
        TITLE: '1',
        ICON: '①',
        TITLE_ROME: 'I',
        TYPE: 'indigo',
    },
    CHANGE_2: {
        ID: 2,
        NAME: CHANGE_2,
        TITLE: '2',
        ICON: '②',
        TITLE_ROME: 'II',
        TYPE: 'orange',
    },

} as const satisfies Record<string, IBlockTaskChange>


// __ Константы Производственных Линий Блоков
export const BLOCK_MANUF_LINES: Record<string, IBlockManufLine> = {
    LINE_0,
    LINE_1,
    LINE_2,
} as const



export const BLOCK_TASK_STATUS_CREATED = 'CREATED'     // __ Создано
export const BLOCK_TASK_STATUS_ROLLING = 'ROLLING'     // __ Переходящий
export const BLOCK_TASK_STATUS_PENDING = 'PENDING'     // __ Готово к выполнению
export const BLOCK_TASK_STATUS_RUNNING = 'RUNNING'     // __ Выполняется
export const BLOCK_TASK_STATUS_DONE    = 'DONE'        // __ Выполнено


// __ Статусы движения СЗ на Блоках
export const BLOCK_TASK_STATUSES: Record<IBlockTaskStatusKeys, IBlockTaskStatusItem> = {
    CREATED: {
        ID   : 1,
        TITLE: 'Создано',
        WORD : 'created',
        TYPE : 'dark',
        PRIORITY: 1,
    },
    ROLLING: {
        ID   : 2,
        TITLE: 'Создано при закрытии СЗ',
        WORD : 'rolling',
        TYPE : 'orange',
        PRIORITY: 2,
    },
    PENDING: {
        ID   : 3,
        TITLE: 'Готово к выполнению',
        WORD : 'pending',
        TYPE : 'primary',
        PRIORITY: 3,
    },
    RUNNING: {
        ID   : 4,
        TITLE: 'Выполняется',
        WORD : 'running',
        TYPE : 'warning',
        PRIORITY: 4,
    },
    DONE   : {
        ID   : 5,
        TITLE: 'Создано',
        WORD : 'created',
        TYPE : 'success',
        PRIORITY: 5,
    },
} as const
