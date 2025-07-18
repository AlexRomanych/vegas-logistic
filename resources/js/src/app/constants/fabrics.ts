// Info Тут все константы, касающиеся участка Стежки

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


export const FABRIC_TASK_STATUS = Object.freeze({

    // descr: Сменное задание еще не создано (или сохранено)
    UNKNOWN: {
        WORD: 'unknown',
        CODE: 0,
        TITLE: 'Не создано',
        TYPE: 'danger',
    },


    // descr: Сменное задание создано (или сохранено)
    CREATED: {
        WORD: 'created',
        CODE: 1,
        TITLE: 'Создано',
        TYPE: 'dark',
    },

    // descr: Сменное задание отправлено на выполнение
    PENDING: {
        WORD: 'pending',
        CODE: 2,
        TITLE: 'Готово к стежке',
        TYPE: 'primary',
    },

    // descr: Сменное задание взято на выполнение (находится в процессе выполнения)
    RUNNING: {
        WORD: 'running',
        CODE: 3,
        TITLE: 'Выполняется',
        TYPE: 'warning',
    },

    // descr: Сменное задание выполнено (закрыто)
    DONE: {
        WORD: 'done',
        CODE: 4,
        TITLE: 'Выполнено',
        TYPE: 'success',
    },
})


// descr: Тут все константы, касающиеся статусов движения рулона
export const FABRIC_ROLL_STATUS = Object.freeze({

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

    // descr: Рулон поставленный на учет в 1С
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

// descr Константы ID машин стежки
export const FABRIC_MACHINES = Object.freeze({
    UNKNOWN: {
        ID: 0,
        TITLE: 'unknown',
        NAME: 'Неизвестно',
        INDEX: 'unknown',
    },
    AMERICAN: {
        ID: 1,
        TITLE: 'american',
        NAME: 'Американец',
        INDEX: 'american',
    },
    GERMAN: {
        ID: 2,
        TITLE: 'german',
        NAME: 'Немец',
        INDEX: 'german',
    },
    CHINA: {
        ID: 3,
        TITLE: 'china',
        NAME: 'Китаец',
        INDEX: 'china',
    },
    KOREAN: {
        ID: 4,
        TITLE: 'korean',
        NAME: 'Кореец',
        INDEX: 'korean',
    },
})


// descr Создаем болванку "нулевой" ткани для отображения в сервисе
export const FABRICS_NULLABLE = {
    "id": 0,
    "code_1C": "0",
    "name": "Нет данных",
    "display_name": "Нет данных",
    "picture":
        {
            "id": 0,
            "name": "Н/Д"
        },
    "textile": "",
    "fillersList": [],
    "active": true,
    "rare": false,
    "machines": [
        {
            "id": 0,
            "short_name": "Нет данных"
        },
        {
            "id": 0,
            "short_name": "Нет данных"
        },
        {
            "id": 0,
            "short_name": "Нет данных"
        },
        {
            "id": 0,
            "short_name": "Нет данных"
        }
    ],
    "buffer": {
        "amount": 0,
        "min": 0,
        "max": 0,
        "min_rolls": 0,
        "max_rolls": 0,
        "optimal_party": 0,
        "average_length": 0,
        "average_fabric_length": 0,
        "rate": 1,
        "productivity": 0
    },
    "text": {
        "description": null,
        "comment": null,
        "note": null
    }
}


// descr Нулевой рулон - болванка рулона для добавления
export const NEW_ROLL =
    {
        id: 0,
        roll_position: 0,
        average_textile_length: FABRICS_NULLABLE.buffer.average_length,
        average_fabric_length: FABRICS_NULLABLE.buffer.average_fabric_length,
        productivity: FABRICS_NULLABLE.buffer.productivity,
        rate: FABRICS_NULLABLE.buffer.rate,
        buffer: FABRICS_NULLABLE.buffer.amount,
        rolls_amount: 0,
        length_amount: 0,
        fabric_id: FABRICS_NULLABLE.id,
        fabric: FABRICS_NULLABLE.display_name,
        fabric_rate: 0,
        fabric_mode: false,
        descr: '',
        correct: false,
        editable: true,
        rolls_exec: [],
    }


// descr Создаем болванку ПС
export const NEW_FABRIC =
    {
        id: 0,
        code_1C: '',
        name: '',
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
            rate: 0,
            productivity: 0
        },
        text: {
            description: null,
            comment: null,
            note: null
        }
    }


// __ Болванка рисунка стежки
export const NEW_FABRIC_PICTURE = {
    id: 0,
    active: true,
    name: '',
    stitch_length: 0,
    stitch_speed: 0,
    moment_speed: 0,
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


// descr: Скелет объекта сменного задания
export const TASK_DRAFT =
    {
        date: '',
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.UNKNOWN.CODE,
            description: '',
            active: false,
        },
        machines: {
            american: {
                rolls: [],
                description: '',
                active: true,
                finish_at: null,
            },
            german: {
                rolls: [],
                description: '',
                active: true,
                finish_at: null,
            },
            china: {
                rolls: [],
                description: '',
                active: true,
                finish_at: null,
            },
            korean: {
                rolls: [],
                description: '',
                active: true,
                finish_at: null,
            },
        },
        workers: [],
    }


// descr: Тестовые СЗ для разработки
/*
export const TEST_FABRICS = [
    {
        date: '2025-04-02',
        active: true,
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.DONE.CODE,
            description: 'Тестовое СЗ_1',
        },
        machines: {
            american: {
                description: 'Тестовое СЗ american',
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 38001,
                        fabric_id: 7,
                        fabric: 'ПС 218Ж 100С 15С блэк (рис. Ж2)',
                        rolls_amount: 1,
                        length_amount: 150.5,
                        descr: 'I am description 1',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 49002,
                        fabric_id: 9,
                        fabric: 'ПС 220Ж 100С 15С М-24 (рис. К2)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description 2',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 51003,
                        fabric_id: 11,
                        fabric: 'ПС 220Ж 100С 220Ж микрофибра (рис. Ж2)',
                        rolls_amount: 3,
                        length_amount: 150.5,
                        descr: 'I am description 3',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 51004,
                        fabric_id: 12,
                        fabric: 'ПС 220Ж 200С 220Ж микрофибра (рис. Ж2)',
                        rolls_amount: 4,
                        length_amount: 150.5,
                        descr: 'I am description 4',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                ]
            },
            german: {
                description: 'Тестовое СЗ german',
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28004,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                ]
            },
            china: {
                description: 'Тестовое СЗ china',
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29007,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                ]
            },
            korean: {
                description: 'Тестовое СЗ korean',
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 31001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description',
                        textile_length: 36.76965318,
                        fabric_mode: true
                    },
                ]
            },
        }
    },
    {
        date: '2025-03-25',
        active: false,
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.RUNNING.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            german: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28004,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            china: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29007,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            korean: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 31001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
        }
    },
    {
        date: '2025-03-26',
        active: false,
        common: {
            team: 2,
            status: FABRIC_TASK_STATUS.PENDING.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            german: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28004,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            china: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29007,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            korean: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 31001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
        }
    },
    {
        date: '2025-03-27',
        active: false,
        common: {
            team: 2,
            status: FABRIC_TASK_STATUS.CREATED.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            german: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28004,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            china: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29007,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            korean: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 31001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
        }
    },
    {
        date: '2025-03-28',
        active: false,
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.UNKNOWN.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            german: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28004,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            china: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29007,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            korean: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 31001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
        }
    },
    {
        date: '2025-03-29',
        active: false,
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.UNKNOWN.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            german: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28004,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            china: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29007,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            korean: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 31001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
        }
    },
    {
        date: '2025-03-24',
        active: false,
        common: {
            team: 2,
            status: FABRIC_TASK_STATUS.UNKNOWN.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            german: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28004,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            china: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29007,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            korean: {
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 31001,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 1,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
        }
    },
]
*/

