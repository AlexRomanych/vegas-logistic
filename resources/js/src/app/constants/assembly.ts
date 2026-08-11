import type {
    IAssemblyModelManufactureGroup,
    IAssemblySector,
    IAssemblyTask,
    IAssemblyTaskChange,
    IAssemblyTaskChangeKeys,
    IAssemblyTaskStatusItem,
    IAssemblyTaskStatusKeys,
} from '@/types'

// --- --------------------------------------------------------------------
// --- -------------------- Константы Смен СЗ -----------------------------
// --- --------------------------------------------------------------------
// __ Смены
export const CHANGE_1 = '1'
export const CHANGE_2 = '2'

// __ Режим Сменной Работы
export const CHANGE_MODE = CHANGE_1 satisfies IAssemblyTaskChangeKeys

// __ Константы смен
export const CHANGES = {
    CHANGE_1: {
        ID        : 1,
        NAME      : CHANGE_1,
        TITLE     : '1',
        ICON      : '①',
        TITLE_ROME: 'I',
        TYPE      : 'indigo',
        TIME      : '08:30-20:30',

    },
    CHANGE_2: {
        ID        : 2,
        NAME      : CHANGE_2,
        TITLE     : '2',
        ICON      : '②',
        TITLE_ROME: 'II',
        TYPE      : 'orange',
        TIME      : '20:30-08:30',
    },

} as const satisfies Record<string, IAssemblyTaskChange>


// __ Болванка Группы для Сортировки
export const ASSEMBLY_MODEL_MANUFACTURE_GROUP_DRAFT = {
    id          : 0,
    name        : '',
    active      : true,
    group_number: 0,
    color       : '',
    description : null,
} as const satisfies IAssemblyModelManufactureGroup


// --- --------------------------------------------------------------------
// --- ---------------- Константы Участков (SECTORS)  ---------------------
// --- --------------------------------------------------------------------
export const ASSEMBLY_TASK_SECTOR_COCONUT    = 'coconut'          // __ Кокос
export const ASSEMBLY_TASK_SECTOR_LATEX      = 'latex'              // __ Латекс
export const ASSEMBLY_TASK_SECTOR_LAYER      = 'layer'              // __ Тонкий настил
export const ASSEMBLY_TASK_SECTOR_FOAM_LAYER = 'foam_layer'    // __ Настилы
export const ASSEMBLY_TASK_SECTOR_FOAM_SIDE  = 'foam_side'      // __ Борта
export const ASSEMBLY_TASK_SECTOR_LAMIT      = 'lamit'              // __ Ламит
export const ASSEMBLY_TASK_SECTOR_TABLE      = 'table'              // __ Стол


export const ASSEMBLY_SECTORS = {
    ASSEMBLY_TASK_SECTOR_COCONUT: {
        ID   : 1,
        NAME : ASSEMBLY_TASK_SECTOR_COCONUT,
        TITLE: 'Кокос',
        LABEL: ['Кокос', ''],
        ICON : '',
        TYPE : 'warning',
        SHOW : true,
    },
    ASSEMBLY_TASK_SECTOR_LATEX: {
        ID   : 2,
        NAME : ASSEMBLY_TASK_SECTOR_LATEX,
        TITLE: 'Латекс',
        LABEL: ['Латекс', ''],
        ICON : '',
        TYPE : 'success',
        SHOW : true,
    },
    ASSEMBLY_TASK_SECTOR_LAYER: {
        ID   : 3,
        NAME : ASSEMBLY_TASK_SECTOR_LAYER,
        TITLE: 'Тонкий настил',
        LABEL: ['Тонкий', 'настил'],
        ICON : '',
        TYPE : 'orange',
        SHOW : true,
    },
    ASSEMBLY_TASK_SECTOR_FOAM_LAYER: {
        ID   : 4,
        NAME : ASSEMBLY_TASK_SECTOR_FOAM_LAYER,
        TITLE: 'ППУ Настилы',
        LABEL: ['ППУ', 'Настилы'],
        ICON : '',
        TYPE : 'indigo',
        SHOW : true,
    },
    ASSEMBLY_TASK_SECTOR_FOAM_SIDE : {
        ID   : 5,
        NAME : ASSEMBLY_TASK_SECTOR_FOAM_SIDE,
        TITLE: 'ППУ Борта',
        LABEL: ['ППУ', 'Борта'],
        ICON : '',
        TYPE : 'primary',
        SHOW : true,
    }
} as const satisfies Record<string, IAssemblySector>


// --- --------------------------------------------------------------------
// --- ----------------------- Константы СЗ  ------------------------------
// --- --------------------------------------------------------------------
export const ASSEMBLY_TASK_DRAFT = {
    id: 0,
    id_ref: 0,                                  // __ референсный id (при разбиении нового СЗ, id_ref === id, то есть основаниие старого СЗ)
    action_at: '',
    active: true,
    change: CHANGE_1,
    position: 0,
    comment: null,
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
    assembly_lines: [],

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
} as const satisfies IAssemblyTask



// --- --------------------------------------------------------------------
// --- ------------------ Константы Статусов СЗ  --------------------------
// --- --------------------------------------------------------------------

export const ASSEMBLY_TASK_STATUS_CREATED = 'CREATED'     // __ Создано
export const ASSEMBLY_TASK_STATUS_ROLLING = 'ROLLING'     // __ Переходящий
export const ASSEMBLY_TASK_STATUS_PENDING = 'PENDING'     // __ Готово к выполнению
export const ASSEMBLY_TASK_STATUS_RUNNING = 'RUNNING'     // __ Выполняется
export const ASSEMBLY_TASK_STATUS_DONE    = 'DONE'        // __ Выполнено


// __ Статусы движения СЗ на Блоках
export const ASSEMBLY_TASK_STATUSES: Record<IAssemblyTaskStatusKeys, IAssemblyTaskStatusItem> = {
    CREATED: {
        ID      : 1,
        TITLE   : 'Создано',
        WORD    : 'created',
        TYPE    : 'dark',
        PRIORITY: 1,
    },
    ROLLING: {
        ID      : 2,
        TITLE   : 'Создано при закрытии СЗ',
        WORD    : 'rolling',
        TYPE    : 'orange',
        PRIORITY: 2,
    },
    PENDING: {
        ID      : 3,
        TITLE   : 'Готово к выполнению',
        WORD    : 'pending',
        TYPE    : 'primary',
        PRIORITY: 3,
    },
    RUNNING: {
        ID      : 4,
        TITLE   : 'Выполняется',
        WORD    : 'running',
        TYPE    : 'warning',
        PRIORITY: 4,
    },
    DONE   : {
        ID      : 5,
        TITLE   : 'Создано',
        WORD    : 'created',
        TYPE    : 'success',
        PRIORITY: 5,
    },
} as const
