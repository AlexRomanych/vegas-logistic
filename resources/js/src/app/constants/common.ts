// Info: Тут все общее

// ___ Режим приложения
export const DEBUG = true

// ___ Показывать ли сообщения об ошибках
export const DISPLAY_CONSOLE_LOG = true

// ___ Показывать ли ошибки catch, или нет
export const DISPLAY_CATCH_ERRORS = true

export const LINE_SEPARATOR = '&nl'  // new line - разделитель строк в тексте


// ___ Настройки лоадера
export const LOADER_SETTINGS = {
    // active: true,
    isFullPage: true,
    loader: 'dots', // 'spinner' | 'bars' | 'dots'
    zIndex: 9999,
    height: 64,
    width: 64,
    backgroundColor: '#444',
    color: 'blue',
    opacity: 0.5,
    canCancel: false,
}


export const REASONS = Object.freeze({
    COMMON: {   // Общие
        CELLS_GROUP: {
            ID: 1,
            NAME: 'Стежка',
        },
        CATEGORY: {}
    },
    FABRIC: {   // Стежка
        CELLS_GROUP: {
            ID: 1,
            NAME: 'Стежка',
        },
        CATEGORY: {
            FALSE: {    // Статус рулона "Не выполнено"
                ID: 1,
                NAME: 'Статус "Не выполнено"',
                ORDER: 1, // Порядок в группе
            },
            ROLLING: {   // Статус рулона "Переходящий"
                ID: 2,
                NAME: 'Статус "Переходящий"',
                ORDER: 2, // Порядок в группе
            },
            CANCELLED: {   // Статус рулона "Отменено"
                ID: 3,
                NAME: 'Статус "Отменено"',
                ORDER: 3, // Порядок в группе
            },
            ADDING: {   // Статус рулона "Добавлен во время выполнения"
                ID: 4,
                NAME: 'Добавлен во время выполнения',
                ORDER: 4, // Порядок в группе
            },
            REORDERED: {   // Статус рулона "Добавлен во время выполнения"
                ID: 5,
                NAME: 'Изменение порядка выполнения',
                ORDER: 5, // Порядок в группе
            },
            PAUSE: {   // Статус рулона "Приостановка выполнения"
                ID: 6,
                NAME: 'Статус "Приостановка выполнения"',
                ORDER: 6, // Порядок в группе
            },

        }
    }
})


// --- ------------------------------------------------------------------------------
// --- ------------------------ Режим работы компонента -----------------------------
// --- ------------------------------------------------------------------------------
// ___ Константы режима работы компонента
export const ROUTER_PAGE_MODE = Object.freeze({
    CREATE: 'create',
    EDIT: 'edit',
    EXECUTE: 'execute',
})

// --- ------------------------------------------------------------------------------
