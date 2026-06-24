// Info Константы для работы с Пошивом (Cutting)
import { UNIVERSAL, AUTO, SOLID_HARD, SOLID_LITE, UNDEFINED, AVERAGE } from '@/app/constants/textile_common.ts'


// __ Константы столов
export const TABLE_1         = 'table_1'
export const TABLE_2         = 'table_2'
export const TABLE_3         = 'table_3'
export const TABLE_0         = 'undefined'
export const TABLE_UNDEFINED = TABLE_0

// __ Объект раскройных столов
export const CUTTING_TABLES: Record<string, ICuttingTableKeys> = {
    TABLE_1,
    TABLE_2,
    TABLE_3,
    TABLE_0,
    TABLE_UNDEFINED,
}

// __ Константы отображения
export const TABLE_1_TITLE         = 'Стол 1'
export const TABLE_1_TITLE_EXT     = 'Стол 1 - Крышки АШМ'
export const TABLE_1_TITLE_SHORT   = '1'
export const TABLE_2_TITLE         = 'Стол 2'
export const TABLE_2_TITLE_EXT     = 'Стол 2 - Крышки (УШМ + Глух.)'
export const TABLE_2_TITLE_SHORT   = '2'
export const TABLE_3_TITLE         = 'Стол 3'
export const TABLE_3_TITLE_EXT     = 'Стол 3 - Детали (УШМ + Глух.)'
export const TABLE_3_TITLE_SHORT   = '3'
export const TABLE_0_TITLE         = 'Стол ??'
export const TABLE_0_TITLE_SHORT   = '??'
export const TABLE_UNDEFINED_TITLE = TABLE_0_TITLE


// __ Констианты деталей чехда в Операциях Раскроя
export const DETAIL_PANEL              = 'panel'
export const DETAIL_PANEL_TITLE        = 'Крышка'
export const DETAIL_SIDE               = 'side'
export const DETAIL_SIDE_TITLE         = 'Боковина'
export const DETAIL_SIDE_EXECUTE       = 'Бурлет'
export const DETAIL_SIDE_TITLE_SHORT   = 'Бок-на'
export const DETAIL_COVER_UP_POINTER   = 'up'
export const DETAIL_COVER_DOWN_POINTER = 'down'
export const DETAIL_SIDE_POINTER       = 'side'


export const DETAILS = {
    PANEL: {    // __ Верхняя и нижняя крышки одинаковы
        NAME: 'panel',
        TITLE: 'Крышка',
        TITLE_SHORT: 'Крышка',
        TITLE_COMPACT: 'К',
    },
    PANEL_UP: {
        NAME: 'panel_up',
        TITLE: 'Крышка верх',
        TITLE_SHORT: 'Крышка',
        TITLE_COMPACT: 'В',
    },
    PANEL_DOWN: {
        NAME: 'panel_down',
        TITLE: 'Крышка низ',
        TITLE_SHORT: 'Крышка',
        TITLE_COMPACT: 'Н',
    },
    SIDE: {
        NAME: 'side',
        TITLE: 'Бурлет',
        TITLE_SHORT: 'Бурлет',
        TITLE_COMPACT: 'Б',
    },
}

// __ Название вкладки с общим СЗ
export const CUTTING_UNION_TASK_NAME = 'Объединение СЗ'

// __ Продолжительность смены в часах
export const TOTAL_SHIFT_DURATION = 12

// __ Время начала смены
export const START_SHIFT_TIME = '07:30'


import type {
    ICuttingDay,
    ICuttingMachineKeys,
    ICuttingOperation, ICuttingProcedure, ICuttingTableKeys,
    ICuttingTask,
    ICuttingTaskLinesGroup,
    ICuttingTaskStatusItem,
    ICuttingTaskStatusKeys
} from '@/types'


export const CUTTING_TASK_STATUS_CREATED = 'CREATED'     // __ Создано
export const CUTTING_TASK_STATUS_ROLLING = 'ROLLING'     // __ Переходящий
export const CUTTING_TASK_STATUS_PENDING = 'PENDING'     // __ Готово к выполнению
export const CUTTING_TASK_STATUS_RUNNING = 'RUNNING'     // __ Выполняется
export const CUTTING_TASK_STATUS_DONE    = 'DONE'        // __ Выполнено

// __ Статусы движения СЗ на Пошиве
export const CUTTING_TASK_STATUSES: Record<ICuttingTaskStatusKeys, ICuttingTaskStatusItem> = {
    CREATED: {
        ID   : 1,
        TITLE: 'Создано',
        WORD : 'created',
        TYPE : 'dark',
    },
    ROLLING: {
        ID   : 2,
        TITLE: 'Создано при закрытии СЗ',
        WORD : 'rolling',
        TYPE : 'orange',
    },
    PENDING: {
        ID   : 3,
        TITLE: 'Готово к выполнению',
        WORD : 'pending',
        TYPE : 'primary',
    },
    RUNNING: {
        ID   : 4,
        TITLE: 'Выполняется',
        WORD : 'running',
        TYPE : 'warning',
    },
    DONE   : {
        ID   : 5,
        TITLE: 'Создано',
        WORD : 'created',
        TYPE : 'success',
    },
}

// const CUTTING_STATUS_CREATED_ID = 1;     // __ Создано
// const CUTTING_STATUS_ROLLING_ID = 2;     // __ Переходящий
// const CUTTING_STATUS_PENDING_ID = 3;     // __ Готово к выполнению
// const CUTTING_STATUS_RUNNING_ID = 4;     // __ Выполняется
// const CUTTING_STATUS_DONE_ID = 5;        // __ Выполнено

// __ Статусы движения СЗ на Пошиве
// export const CUTTING_TASK_STATUSES = {
//     CREATED: {
//         ID: 1,
//         TITLE: 'Создано'
//     },
//     CREATED_CLOSE: {
//         ID: 2,
//         TITLE: 'Создано при закрытии СЗ'
//     },
//     PENDING: {
//         ID: 3,
//         TITLE: 'Готово к выполнению'
//     },
//     RUNNING: {
//         ID: 4,
//         TITLE: 'Выполняется'
//     },
//     DONE: {
//         ID: 5,
//         TITLE: 'Выполнено'
//     }
// }


export const CUTTING_TASK_DRAFT: ICuttingTask = {
    id            : 0,
    id_ref        : 0,
    action_at     : '',
    active        : true,
    change        : 1,
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
    cutting_lines : [],
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

// __ Объект ШМ
export const CUTTING_MACHINES: Record<string, ICuttingMachineKeys> = {
    UNIVERSAL,
    AUTO,
    SOLID_HARD,
    SOLID_LITE,
    UNDEFINED,
    AVERAGE,
}


// __ Объект Типовой операции
export const CUTTING_OPERATION_DRAFT: ICuttingOperation = {
    active     : true,
    description: null,
    id         : 0,
    machine    : '',
    name       : '',
    time       : 0,
    type       : 'dynamic',
    color      : '#64748B',
    cover_type : null,
    table      : null,
    detail     : null,
}


// __ Объект Типовой Процедуры Раскроя
export const CUTTING_PROCEDURE_DRAFT: ICuttingProcedure = {
    id         : 0,
    name       : '',
    text       : null,
    object_name: DETAIL_PANEL,
    collapsed  : true,
    description: null,
    active     : false,
}


// __ Объект производственного дня Пошива
export const CUTTING_DAY_DRAFT: ICuttingDay = {
    id           : 0,
    action_at    : '',
    action_at_str: '',
    change       : 0,
    description  : null,
    comment      : null,
    start_at     : null,
    paused_at    : null,
    resume_at    : null,
    finish_at    : null,
    duration     : 0,
    cutting_tasks: [],
    responsible  : {
        name      : '',
        surname   : '',
        patronymic: '',
        id        : 0,
    },
    workers      : [],
    ready        : false,
}


// __ Настройка групп задач на Раскрое
export const CUTTING_TASK_GROUP_RULES: ICuttingTaskLinesGroup[] = [
    {
        GROUP_NAME : TABLE_1_TITLE,
        GROUP_TYPE : 'orange',
        GROUP_COLOR: '#FF9800',
        SUBGROUPS  : [
            {
                SUBGROUP_NAME : TABLE_1_TITLE,
                SUBGROUP_TYPE : 'orange',
                SUBGROUP_COLOR: '#FF9800',
                SUBGROUP_TABLE: [CUTTING_TABLES.TABLE_1],
            }
        ]
    },
    {
        GROUP_NAME : TABLE_2_TITLE,
        GROUP_TYPE : 'warning',
        GROUP_COLOR: '#FF9800',
        SUBGROUPS  : [
            {
                SUBGROUP_NAME : TABLE_2_TITLE,
                SUBGROUP_TYPE : 'orange',
                SUBGROUP_COLOR: '#FF9800',
                SUBGROUP_TABLE: [CUTTING_TABLES.TABLE_2],
            }
        ]
    },
    {
        GROUP_NAME : TABLE_3_TITLE,
        GROUP_TYPE : 'success',
        GROUP_COLOR: '#FF9800',
        SUBGROUPS  : [
            {
                SUBGROUP_NAME : TABLE_3_TITLE,
                SUBGROUP_TYPE : 'orange',
                SUBGROUP_COLOR: '#FF9800',
                SUBGROUP_TABLE: [CUTTING_TABLES.TABLE_3],
            }
        ]
    },
    {
        GROUP_NAME : TABLE_0_TITLE,
        GROUP_TYPE : 'danger',
        GROUP_COLOR: '#FF9800',
        SUBGROUPS  : [
            {
                SUBGROUP_NAME : TABLE_0_TITLE,
                SUBGROUP_TYPE : 'orange',
                SUBGROUP_COLOR: '#FF9800',
                SUBGROUP_TABLE: [CUTTING_TABLES.TABLE_0],
            }
        ]
    },
]

// export const CUTTING_TASK_GROUP_RULES: ICuttingTaskLinesGroup[] = [
//     {
//         GROUP_NAME : 'АШМ',
//         GROUP_TYPE : 'orange',
//         GROUP_COLOR: '#FF9800',
//         SUBGROUPS  : [
//             {
//                 SUBGROUP_NAME : 'Автоматы',
//                 SUBGROUP_TYPE : 'orange',
//                 SUBGROUP_COLOR: '#FF9800',
//                 SUBGROUP_TCHK : ['А 1', 'А 2', 'А 3', 'А 4',],
//             }
//         ]
//     },
//     {
//         GROUP_NAME : 'УШМ',
//         GROUP_TYPE : 'warning',
//         GROUP_COLOR: '#FF9800',
//         SUBGROUPS  : [
//             // {
//             //     SUBGROUP_NAME : 'Глухие, автоматическое чехление',
//             //     SUBGROUP_TYPE : 'orange',
//             //     SUBGROUP_COLOR: '#FF9800',
//             //     SUBGROUP_TCHK : ['ОБ 2',],
//             // },
//             {
//                 SUBGROUP_NAME : 'Глухие',
//                 SUBGROUP_TYPE : 'orange',
//                 SUBGROUP_COLOR: '#FF9800',
//                 SUBGROUP_TCHK : ['ОБ 1', 'ОБ 2', 'ОБ 6', 'ОБ 7',],
//             },
//             {
//                 SUBGROUP_NAME : 'Глухие сложные',
//                 SUBGROUP_TYPE : 'orange',
//                 SUBGROUP_COLOR: '#FF9800',
//                 SUBGROUP_TCHK : ['ОБ 3', 'ОБ 4', 'ОБ 5',],
//             },
//             {
//                 SUBGROUP_NAME : 'УШМ + окантователь',
//                 SUBGROUP_TYPE : 'orange',
//                 SUBGROUP_COLOR: '#FF9800',
//                 SUBGROUP_TCHK : ['ОК 1', 'ОК 2', 'ОК 3', 'ОК 4'],
//             },
//             {
//                 SUBGROUP_NAME : 'УШМ',
//                 SUBGROUP_TYPE : 'orange',
//                 SUBGROUP_COLOR: '#FF9800',
//                 SUBGROUP_TCHK : ['У 1', 'У 2', 'У 3', 'У 4', 'У 5',],
//             },
//         ]
//     },
//     {
//         GROUP_NAME : 'Н/Д',
//         GROUP_TYPE : 'danger',
//         GROUP_COLOR: '#FF9800',
//         SUBGROUPS  : [
//             {
//                 SUBGROUP_NAME : 'Без ТКЧ',
//                 SUBGROUP_TYPE : 'orange',
//                 SUBGROUP_COLOR: '#FF9800',
//                 SUBGROUP_TCHK : [],
//             }
//         ]
//     },
// ]




