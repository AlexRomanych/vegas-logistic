// Info Тут все константы, касающиеся участка Стежки

/*
 // attract Это из PHP, в ней все названия и значения для статусов
 // descr Сменное задание создано (или сохранено)
 const FABRIC_TASK_CREATED = 'created';
 const FABRIC_TASK_CREATED_CODE = 1;

 // descr Сменное задание отправлено на выполнение
 const FABRIC_TASK_PENDING = 'pending';
 const FABRIC_TASK_PENDING_CODE = 2;

 // descr Сменное задание взято на выполнение (находится в процессе выполнения)
 const FABRIC_TASK_RUNNING = 'running';
 const FABRIC_TASK_RUNNING_CODE = 3;

 // descr Сменное задание выполнено (закрыто)
 const FABRIC_TASK_DONE = 'done';
 const FABRIC_TASK_DONE_CODE = 4;
*/

import {reactive} from "vue";

export const FABRIC_TASK_STATUS = Object.freeze({

    // descr Сменное задание еще не создано (или сохранено)
    UNKNOWN: {
        WORD: 'unknown',
        CODE: 0,
        TITLE: 'Не создано',
    },


    // descr Сменное задание создано (или сохранено)
    CREATED: {
        WORD: 'created',
        CODE: 1,
        TITLE: 'Создано',
    },

    // descr Сменное задание отправлено на выполнение
    PENDING: {
        WORD: 'pending',
        CODE: 2,
        TITLE: 'Готов к стежке',
    },

    // descr Сменное задание взято на выполнение (находится в процессе выполнения)
    RUNNING: {
        WORD: 'running',
        CODE: 3,
        TITLE: 'Выполняется',
    },

    // descr Сменное задание выполнено (закрыто)
    DONE: {
        WORD: 'done',
        CODE: 4,
        TITLE: 'Выполнено',
    },
})


// descr Константы режима работы компонента
export const FABRIC_PAGE_MODE = Object.freeze({
    CREATE: 'create',
    EDIT: 'edit',
    EXECUTE: 'execute',
})

// descr Константы ID машин стежки
export const FABRIC_MACHINES = Object.freeze({
    UNRNOWN: {
        ID: 0,
        TITLE: 'unknown',
    },
    AMERICAN: {
        ID: 1,
        TITLE: 'american',
    },
    GERMAN: {
        ID: 2,
        TITLE: 'german',
    },
    CHINA: {
        ID: 3,
        TITLE: 'china',
    },
    KOREAN: {
        ID: 4,
        TITLE: 'korean',
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
        "rate": 0,
        "productivity": 0
    },
    "text": {
        "description": null,
        "comment": null,
        "note": null
    }
}


// descr Нулевой рулон - болванка рулона для добавления
export const NEW_ROLL = {
    average_length: FABRICS_NULLABLE.buffer.average_length,
    roll_id: 0,
    num: 0,
    fabric_id: FABRICS_NULLABLE.id,
    fabric: FABRICS_NULLABLE.display_name,
    fabric_mode: false,
    rolls_amount: 0,
    length_amount: 0,
    descr: ''
}

// descr Тестовые СЗ для разработки
export const TEST_FABRICS = [
    {
        date: '2025-04-02',
        active: true,
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.DONE.CODE,
            description: 'Тестовое СЗ',
        },
        machines: {
            american: {
                // id: FABRIC_MACHINES.AMERICAN,
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 38001,
                        fabric_id: 7,
                        fabric: 'ПС 218Ж 100С 15С блэк (рис. Ж2)',
                        rolls_amount: 1,
                        length_amount: 150.5,
                        descr: 'I am description 1'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 49002,
                        fabric_id: 9,
                        fabric: 'ПС 220Ж 100С 15С М-24 (рис. К2)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description 2'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 51003,
                        fabric_id: 11,
                        fabric: 'ПС 220Ж 100С 220Ж микрофибра (рис. Ж2)',
                        rolls_amount: 3,
                        length_amount: 150.5,
                        descr: 'I am description 3'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 51004,
                        fabric_id: 12,
                        fabric: 'ПС 220Ж 200С 220Ж микрофибра (рис. Ж2)',
                        rolls_amount: 4,
                        length_amount: 150.5,
                        descr: 'I am description 4'
                    },
                ]
            },
            german: {
                // id: FABRIC_MACHINES.GERMAN,
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28004,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            china: {
                // id: FABRIC_MACHINES.CHINA,
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29007,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                ]
            },
            korean: {
                // id: FABRIC_MACHINES.KOREAN,
                rolls: [
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 31001,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 27003,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28005,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 28006,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29008,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 29009,
                        fabric_id: 0,
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
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 32002,
                        fabric_id: 0,
                        fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)',
                        rolls_amount: 2,
                        length_amount: 150.5,
                        descr: 'I am description'
                    },
                    {
                        average_length: 59.9,
                        roll_id: 0,
                        num: 33003,
                        fabric_id: 0,
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
