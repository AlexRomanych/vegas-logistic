// Info Тут все константы, касающиеся участка Стежки

import type { IFabricMachine, ITaskStatus, IRoll, IFabric, ITaskItem, IRollStatus } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

/*
 // attract Это из PHP, в ней все названия и значения для статусов
 // descr: Сменное задание создано (или сохранено)
 const FABRIC_TASK_CREATED = 'created';
 const FABRIC_TASK_CREATED_CODE = 1;

 // descr: Сменное задание отправлено на выполнение
 const FABRIC_TASK_PENDING = 'pending';
 const FABRIC_TASK_PENDING_CODE = 2;

 // descr: Сменное задание взято на выполнение (находится в процессе выполнения)
 const FABRIC_TASK_RUNNING = 'running';
 const FABRIC_TASK_RUNNING_CODE = 3;

 // descr: Сменное задание выполнено (закрыто)
 const FABRIC_TASK_DONE = 'done';
 const FABRIC_TASK_DONE_CODE = 4;
*/


// descr: Длина рабочей смены в часах
export const FABRIC_WORKING_SHIFT_LENGTH = 10.5


// ___ Значение, которое присваивается при отсутствии времени переналадки
export const FABRIC_DEFAULT_TUNING_TIME = -1

// descr: Интервалы состояний количества
// descr: Нижнее количество включается, верхнее - нет
export const WARNINGS_RANGES = Object.freeze({
    [0]: {
        LOW_BOUND: 0.00,
        HIGH_BOUND: 0.25,
        TYPE: 'danger',
    },
    [1]: {
        LOW_BOUND: 0.25,
        HIGH_BOUND: 0.50,
        TYPE: 'warning',
    },
    [2]: {
        LOW_BOUND: 0.50,
        HIGH_BOUND: 0.75,
        TYPE: 'info',
    },
    [3]: {
        LOW_BOUND: 0.75,
        HIGH_BOUND: 1.00,
        TYPE: 'success',
    },
})


// ___ Тут все константы, касающиеся статусов СЗ
export const FABRIC_TASK_STATUS: Record<string, ITaskStatus> = Object.freeze({

    // ___ Сменное задание еще не создано (или сохранено)
    UNKNOWN: {
        WORD: 'unknown',
        CODE: 0,
        TITLE: 'Не создано',
        TYPE: 'danger',
    },
    // ___ Сменное задание создано (или сохранено)
    CREATED: {
        WORD: 'created',
        CODE: 1,
        TITLE: 'Создано',
        TYPE: 'dark',
    },
    // ___ Сменное задание отправлено на выполнение
    PENDING: {
        WORD: 'pending',
        CODE: 2,
        TITLE: 'Готово к стежке',
        TYPE: 'primary',
    },
    // ___ Сменное задание взято на выполнение (находится в процессе выполнения)
    RUNNING: {
        WORD: 'running',
        CODE: 3,
        TITLE: 'Выполняется',
        TYPE: 'warning',
    },
    // ___ Сменное задание выполнено (закрыто)
    DONE: {
        WORD: 'done',
        CODE: 4,
        TITLE: 'Выполнено',
        TYPE: 'success',
    },
})


// descr: Тут все константы, касающиеся статусов движения рулона
export const FABRIC_ROLL_STATUS: Record<string, IRollStatus> = Object.freeze({

    // descr: Рулон создан (или сохранен)
    CREATED: {
        WORD: 'created',
        CODE: 0,
        TITLE: 'Создано',
        TYPE: 'dark',
    },

    // descr: Рулон взят на выполнение (находится в процессе выполнения)
    RUNNING: {
        WORD: 'running',
        CODE: 1,
        TITLE: 'В процессе',
        TYPE: 'warning',
    },

    // descr: Рулон был взят на выполнение, но выполнение приостановлено (например, перенос на другую смену)
    PAUSED: {
        WORD: 'paused',
        CODE: 2,
        TITLE: 'Пауза',
        TYPE: 'light',
    },

    // descr: Рулон выполнен (закрыт)
    DONE: {
        WORD: 'done',
        CODE: 3,
        TITLE: 'Выполнено',
        TYPE: 'success',
    },

    // descr: Рулон не выполнен (не закрыт)
    FALSE: {
        WORD: 'false',
        CODE: 4,
        TITLE: 'Не выполнено',
        TYPE: 'danger',
    },

    // descr: Рулон переходящий (с одной смены на другую)
    ROLLING: {
        WORD: 'rolling',
        CODE: 5,
        TITLE: 'Переходящий',
        TYPE: 'orange',
    },

    // descr: Рулон поставленный на учет в 1С. Не учавствует в движении заявки
    REGISTERED_1C: {
        WORD: 'registered',
        CODE: 6,
        TITLE: 'Учет в 1С',
        TYPE: 'orange',
    },

    // descr: Рулон перемещенный на участок закроя
    MOVED: {
        WORD: 'moved',
        CODE: 7,
        TITLE: 'Перемещенный',
        TYPE: 'indigo',
    },

    // descr: Рулон принятый на закрое
    ACCEPTED: {
        WORD: 'accepted',
        CODE: 8,
        TITLE: 'Принятый',
        TYPE: 'success',
    },

    // descr: Рулон отменный на закрое
    CANCELLED: {
        WORD: 'cancelled',
        CODE: 9,
        TITLE: 'Отменено',
        TYPE: 'stone',
    },

    // ___ Рулон, без учета в 1С (чисто технический статус). Не учавствует в движении заявки
    UNREGISTERED_1C: {
        WORD: 'unregistered',
        CODE: 100,
        TITLE: 'Без учета в 1С',
        TYPE: 'dark',
    },

})


// descr: Массив (псевдомассив - объект) статусов движения рулона
export const FABRIC_ROLL_STATUS_LIST = {
    [FABRIC_ROLL_STATUS.CREATED.CODE]: FABRIC_ROLL_STATUS.CREATED,
    [FABRIC_ROLL_STATUS.RUNNING.CODE]: FABRIC_ROLL_STATUS.RUNNING,
    [FABRIC_ROLL_STATUS.PAUSED.CODE]: FABRIC_ROLL_STATUS.PAUSED,
    [FABRIC_ROLL_STATUS.DONE.CODE]: FABRIC_ROLL_STATUS.DONE,
    [FABRIC_ROLL_STATUS.FALSE.CODE]: FABRIC_ROLL_STATUS.FALSE,
    [FABRIC_ROLL_STATUS.ROLLING.CODE]: FABRIC_ROLL_STATUS.ROLLING,
    [FABRIC_ROLL_STATUS.CANCELLED.CODE]: FABRIC_ROLL_STATUS.CANCELLED,

    [FABRIC_ROLL_STATUS.MOVED.CODE]: FABRIC_ROLL_STATUS.MOVED,
    [FABRIC_ROLL_STATUS.ACCEPTED.CODE]: FABRIC_ROLL_STATUS.ACCEPTED,
    [FABRIC_ROLL_STATUS.REGISTERED_1C.CODE]: FABRIC_ROLL_STATUS.REGISTERED_1C,
}

// descr Константы статусов выполнения СЗ
export const FABRIC_TASKS_EXECUTE = Object.freeze({
    START: {
        WORD: 'start',
        CODE: 1,
        TITLE: 'Начать',
    },
    STOP: {
        WORD: 'stop',
        CODE: 2,
        TITLE: 'Закончить',
    }
})


// descr Константы режима работы компонента
export const FABRIC_PAGE_MODE = Object.freeze({
    CREATE: 'create',
    EDIT: 'edit',
    EXECUTE: 'execute',
})

// ___ Константы ID машин стежки
export interface IConstFabricMachine {
    readonly ID: number
    readonly TITLE: string
    readonly NAME: string
    readonly INDEX: string
    readonly ORIGIN: string
    readonly TYPE: IColorTypes
}

export type IMachineKey = 'UNKNOWN' | 'AMERICAN' | 'GERMAN' | 'CHINA' | 'KOREAN'

export const FABRIC_MACHINES: Record<IMachineKey, IConstFabricMachine> = Object.freeze({
    UNKNOWN: {
        ID: 0,
        TITLE: 'unknown',
        NAME: 'Неизвестно',
        INDEX: 'unknown',
        ORIGIN: '',
        TYPE: 'dark'
    },
    AMERICAN: {
        ID: 1,
        TITLE: 'american',
        NAME: 'Американец',
        INDEX: 'american',
        ORIGIN: 'LEGACY-4',
        TYPE: 'orange'
    },
    GERMAN: {
        ID: 2,
        TITLE: 'german',
        NAME: 'Немец',
        INDEX: 'german',
        ORIGIN: 'CHAINTRONIC',
        TYPE: 'success'
    },
    CHINA: {
        ID: 3,
        TITLE: 'china',
        NAME: 'Китаец',
        INDEX: 'china',
        ORIGIN: 'HY-W-DGW',
        TYPE: 'danger'
    },
    KOREAN: {
        ID: 4,
        TITLE: 'korean',
        NAME: 'Кореец',
        INDEX: 'korean',
        ORIGIN: 'МТ-94',
        TYPE: 'warning'
    },
})


// descr Создаем болванку "нулевой" ткани для отображения в сервисе
export const FABRICS_NULLABLE: IFabric = {
    'id': 0,
    'code_1C': '0',
    'name': 'Нет данных',
    'display_name': 'Нет данных',
    'picture':
        {
            'id': 0,
            'name': 'Н/Д'
        },
    'textile': '',
    'fillersList': [],
    'active': true,
    'rare': false,
    'machines': [
        {
            'id': 0,
            'short_name': 'Нет данных'
        },
        {
            'id': 0,
            'short_name': 'Нет данных'
        },
        {
            'id': 0,
            'short_name': 'Нет данных'
        },
        {
            'id': 0,
            'short_name': 'Нет данных'
        }
    ],
    'buffer': {
        'amount': 0,
        'min': 0,
        'max': 0,
        'min_rolls': 0,
        'max_rolls': 0,
        'optimal_party': 0,
        'average_length': 0,
        'average_fabric_length': 0,
        'rate': 1,
        'productivity': 0
    },
    'text': {
        'description': null,
        'comment': null,
        'note': null
    },
    textile_layers_amount: 1,
    statistic: false,
    statistic_length: 0,
    hand_length: 0,
    correct: false,
    average_textile_roll_length: 0,
}


// ___ Нулевой рулон - болванка рулона для добавления
export const NEW_ROLL: IRoll =
    {
        id: -1,
        roll_position: 0,
        average_textile_length: FABRICS_NULLABLE.buffer.average_length,
        average_fabric_length: FABRICS_NULLABLE.buffer.average_fabric_length,
        productivity: FABRICS_NULLABLE.buffer.productivity,
        rate: FABRICS_NULLABLE.buffer.rate,
        buffer: FABRICS_NULLABLE.buffer.amount,
        rolls_amount: 0,
        length_amount: 0,
        fabric_id: FABRICS_NULLABLE.id,
        fabric: FABRICS_NULLABLE.name,
        fabric_rate: 0,
        fabric_mode: false,
        descr: '',
        correct: false,
        editable: true,
        rolls_exec: [],
        note: null,
        textile_layers_amount: 1,
        average_textile_roll_length: 0,
        fabric_name: FABRICS_NULLABLE.name,
        isTuning: false
    }


// ___ Создаем болванку ПС
export const NEW_FABRIC: IFabric =
    {
        id: 0,
        code_1C: '',
        name: '',
        correct: false,
        display_name: '',
        picture: {
            id: 0,
            name: ''
        },
        textile: '',
        fillersList: [],
        active: true,
        rare: false,
        machines: [],
        buffer: {
            amount: 0,
            min: 0,
            max: 0,
            min_rolls: 0,
            max_rolls: 0,
            optimal_party: 0,
            average_length: 0,
            average_fabric_length: 0,
            rate: 0,
            productivity: 0,
        },
        text: {
            description: null,
            comment: null,
            note: null
        },
        textile_layers_amount: 1,
        statistic: false,
        statistic_length: 0,
        hand_length: 0,
        average_textile_roll_length: 0,
    }


// __ Болванка рисунка стежки
export const NEW_FABRIC_PICTURE = {
    id: 0,
    active: true,
    name: '',
    stitch_length: 0,
    stitch_speed: 0,
    moment_speed: 0,
    productivity: 0,
    shuttle_amount: 0,
    description: '',
    fabricMainMachineId: 0,
    fabricMainMachineSchemaId: 0,
    fabricAltMachineId_1: 0,
    fabricAltMachineSchemaId_1: 0,
    fabricAltMachineId_2: 0,
    fabricAltMachineSchemaId_2: 0,
    fabricAltMachineId_3: 0,
    fabricAltMachineSchemaId_3: 0,
}


// __ Скелет объекта сменного задания
export const TASK_DRAFT: ITaskItem =
    {
        id: 0,
        date: '',
        common: {
            id: 0,
            team: 1,
            status: FABRIC_TASK_STATUS.UNKNOWN.CODE,
            description: '',
            active: false,
            created_at: '',
            created_by: '',
            start_at: null,
            finish_at: null,

        },
        machines: {
            american: {
                rolls: [],
                description: '',
                active: true,
                finish_at: null,
                lastExecRoll: null,
            },
            german: {
                rolls: [],
                description: '',
                active: true,
                finish_at: null,
                lastExecRoll: null,
            },
            china: {
                rolls: [],
                description: '',
                active: true,
                finish_at: null,
                lastExecRoll: null,
            },
            korean: {
                rolls: [],
                description: '',
                active: true,
                finish_at: null,
                lastExecRoll: null,
            },
        },
        workers: [],
    }

// __ Константы функционала
export const FABRIC_MANAGE = 'manage'       // __ Управление СЗ
export const FABRIC_EXECUTE = 'execute'     // __ Выполнение СЗ

// __ Константы для localStorage
export const TASK_TAB_PREFIX = 'TASK_TAB'                      // __ Управление СЗ
export const TASK_TAB_PREFIX_EXECUTE = 'TASK_EXECUTE_TAB'      // __ Выполнение СЗ
