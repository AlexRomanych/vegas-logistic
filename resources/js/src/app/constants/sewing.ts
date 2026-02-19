// Info Константы для работы с Пошивом (Sewing)


import type {
    ISewingDay, ISewingDayWorker,
    ISewingMachineKeys,
    ISewingOperation,
    ISewingTask,
    ISewingTaskStatusItem,
    ISewingTaskStatusKeys
} from '@/types'


export const SEWING_TASK_STATUS_CREATED = 'CREATED'     // __ Создано
export const SEWING_TASK_STATUS_ROLLING = 'ROLLING'     // __ Переходящий
export const SEWING_TASK_STATUS_PENDING = 'PENDING'     // __ Готово к выполнению
export const SEWING_TASK_STATUS_RUNNING = 'RUNNING'     // __ Выполняется
export const SEWING_TASK_STATUS_DONE = 'DONE'           // __ Выполнено

// __ Статусы движения СЗ на Пошиве
export const SEWING_TASK_STATUSES: Record<ISewingTaskStatusKeys, ISewingTaskStatusItem> = {
    CREATED: {
        ID: 1,
        TITLE: 'Создано',
        WORD: 'created',
        TYPE: 'dark',
    },
    ROLLING: {
        ID: 2,
        TITLE: 'Создано при закрытии СЗ',
        WORD: 'rolling',
        TYPE: 'orange',
    },
    PENDING: {
        ID: 3,
        TITLE: 'Готово к выполнению',
        WORD: 'pending',
        TYPE: 'primary',
    },
    RUNNING: {
        ID: 4,
        TITLE: 'Выполняется',
        WORD: 'running',
        TYPE: 'warning',
    },
    DONE: {
        ID: 5,
        TITLE: 'Создано',
        WORD: 'created',
        TYPE: 'success',
    },
}

// const SEWING_STATUS_CREATED_ID = 1;     // __ Создано
// const SEWING_STATUS_ROLLING_ID = 2;     // __ Переходящий
// const SEWING_STATUS_PENDING_ID = 3;     // __ Готово к выполнению
// const SEWING_STATUS_RUNNING_ID = 4;     // __ Выполняется
// const SEWING_STATUS_DONE_ID = 5;        // __ Выполнено

// __ Статусы движения СЗ на Пошиве
// export const SEWING_TASK_STATUSES = {
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






export const SEWING_TASK_DRAFT: ISewingTask = {
    id:           0,
    id_ref:       0,
    action_at:    '',
    active:       true,
    change:       1,
    position:     0,
    comment:      null,
    order:        {
        id:              0,
        order_no_num:    0,
        order_no_origin: '0',
        order_no_str:    '0',
        load_at:         null,
        comment_1c:      null,
        client:          {
            id:         0,
            name:       '',
            add_name:   '',
            short_name: '',
        },
        order_type:      {
            id:           0,
            display_name: '',
            color:        '',
        }
    },
    sewing_lines: [],
    statuses: [],
    current_status: {
        id: 0,
        name: '',
        color: '',
        pivot: {
            created_at: null,
            duration:  null,
            finished_at:  null,
            set_at:  null,
            started_at:  null,
        }

    }
}

// __ Константы полей швейных машин и трудозатрат, которые приходят с сервера
export const UNIVERSAL  = 'universal'
export const AUTO       = 'auto'
export const SOLID_HARD = 'solid_hard'
export const SOLID_LITE = 'solid_lite'
export const UNDEFINED  = 'undefined'
export const AVERAGE    = 'average'

// __ Объект ШМ
export const SEWING_MACHINES: Record<string, ISewingMachineKeys> = {
    UNIVERSAL,
    AUTO,
    SOLID_HARD,
    SOLID_LITE,
    UNDEFINED,
    AVERAGE,
}


// __ Объект Типовой операции
export const SEWING_OPERATION_DRAFT: ISewingOperation = {
    active: true,
    description:  null,
    id: 0,
    machine: '',
    name: '',
    time: 0,
    type: 'dynamic',
    color: '#64748B'
}


// __ Объект производственного дня Пошива
export const SEWING_DAY_DRAFT: ISewingDay = {
    id: 0,
    action_at: '',
    action_at_str: '',
    change: 0,
    description:  null,
    comment: null,
    start_at:  null,
    paused_at: null,
    resume_at: null,
    finish_at: null,
    duration: 0,
    sewing_tasks: [],
    responsible: {
        name: '',
        surname: '',
        patronymic: '',
        id: 0,
    },
    workers: [],
}





