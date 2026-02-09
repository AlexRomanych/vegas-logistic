// Info Константы для работы с Пошивом (Sewing)


import type { ISewingMachineKeys, ISewingOperation, ISewingTask } from '@/types'


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



// __ Статусы движения СЗ на Пошиве
export const SEWING_TASK_STATUSES = {
    CREATED: {
        ID: 1,
        TITLE: 'Создано'
    },
    CREATED_CLOSE: {
        ID: 2,
        TITLE: 'Создано при закрытии СЗ'
    },
    PENDING: {
        ID: 3,
        TITLE: 'Готово к выполнению'
    },
    RUNNING: {
        ID: 4,
        TITLE: 'Выполняется'
    },
    DONE: {
        ID: 5,
        TITLE: 'Выполнено'
    }
}



