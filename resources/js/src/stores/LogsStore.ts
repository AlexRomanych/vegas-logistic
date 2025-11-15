// info Хранилище для Logs

import { defineStore } from 'pinia'
import { jwtGet, jwtPost, jwtDelete, jwtUpdate, jwtPut, jwtPatch } from '@/app/utils/jwt_api'

import type { IPeriod } from '@/types'

const DEBUG = true

const PREFIX = 'logs'

const URL_LOGS_FABRICS_EXECUTE_ROLLS = `${PREFIX}/fabrics/rolls/execute`        // Логи выполнения физических рулонов

export const useLogsStore = defineStore('logs', () => {

    // ___ Получение Плана загрузок с сервера
    const getLogsFabricsExecuteRollsByPeriod = async (period: IPeriod | null = null) => {
        let response

        if (period) {
            response = await jwtGet(URL_LOGS_FABRICS_EXECUTE_ROLLS, {period})
        } else {
            response = await jwtGet(URL_LOGS_FABRICS_EXECUTE_ROLLS)
        }

        const result = await response

        if (DEBUG) console.log('LogsStore: getFabricsRollsExecuteByPeriod: ', result)

        return result.data
    }


    return {
        getLogsFabricsExecuteRollsByPeriod
    }

})



