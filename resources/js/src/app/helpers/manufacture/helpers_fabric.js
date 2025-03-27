// Info Тут все функции для участка Стежки

import {FABRIC_TASK_STATUS} from '/resources/js/src/app/constants/fabrics.js'

// descr Получить тип стиля по коду статуса СЗ на стежке
export const getStyleTypeByFabricTaskStatusCode = function (taskStatusCode = null) {
    if (taskStatusCode === null) return 'dark'

    switch (taskStatusCode) {
        case FABRIC_TASK_STATUS.UNKNOWN.CODE:
            return 'danger'

        case FABRIC_TASK_STATUS.CREATED.CODE:
            return 'dark'

        case FABRIC_TASK_STATUS.PENDING.CODE:
            return 'primary'

        case FABRIC_TASK_STATUS.RUNNING.CODE:
            return 'warning'

        case FABRIC_TASK_STATUS.DONE.CODE:
            return 'success'

        default:
            return 'dark'
    }
}


// descr Получить название статуса по коду статуса СЗ на стежке
export const getTitleByFabricTaskStatusCode = function (taskStatusCode = null) {
    if (taskStatusCode === null) return ''

    switch (taskStatusCode) {
        case FABRIC_TASK_STATUS.UNKNOWN.CODE:
            return FABRIC_TASK_STATUS.UNKNOWN.TITLE

        case FABRIC_TASK_STATUS.CREATED.CODE:
            return FABRIC_TASK_STATUS.CREATED.TITLE

        case FABRIC_TASK_STATUS.PENDING.CODE:
            return FABRIC_TASK_STATUS.PENDING.TITLE

        case FABRIC_TASK_STATUS.RUNNING.CODE:
            return FABRIC_TASK_STATUS.RUNNING.TITLE

        case FABRIC_TASK_STATUS.DONE.CODE:
            return FABRIC_TASK_STATUS.DONE.TITLE

        default:
            return ''
    }
}
