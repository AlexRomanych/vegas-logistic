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
export const FABRICS_NULLABLE =
    {
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




