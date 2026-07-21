import type { IEventLogObj } from '@/types'

export const LEVEL_INFO    = 'INFO'
export const LEVEL_ERROR   = 'ERROR'
export const LEVEL_WARNING = 'WARNING'

export const EVENT_LOG_LEVELS = {
    INFO: {
        LEVEL: 'INFO',
        TITLE: 'Инфо',
        TYPE : 'success',
    },
    ERROR: {
        LEVEL: 'ERROR',
        TITLE: 'Ошибка',
        TYPE : 'danger',
    },
    WARNING: {
        LEVEL: 'WARNING',
        TITLE: 'Предупреждение',
        TYPE : 'warning',
    },
} as const satisfies Record<string, IEventLogObj>
