// info Хранилище для Logs

import { defineStore } from 'pinia'
import { jwtGet, jwtPost, jwtDelete, jwtUpdate, jwtPut, jwtPatch } from '@/app/utils/jwt_api'

import type { IPeriod } from '@/types'

const DEBUG = true

const PREFIX = 'logs'

const URL_LOGS_FABRICS_EXECUTE_ROLLS_BY_PERIOD = `${PREFIX}/fabrics/rolls/execute/period`                 // Логи выполнения физических рулонов
const URL_LOGS_FABRICS_EXECUTE_ROLLS_BY_ROLL_NUMBER = `${PREFIX}/fabrics/rolls/execute/roll-number`       // Логи выполнения физических рулонов

export const useLogsStore = defineStore('logs', () => {

    // ___ Получение Логов выполнения рулонов по периоду
    const getLogsFabricsExecuteRollsByPeriod = async (period: IPeriod | null = null, status: number | null = null) => {
        let response

        // console.log(period, status)
        // debugger
        // __ Тут именно такая проверка, потому что если status === 0, то он не передается в запросе
        if (period && status !== null) {
            response = await jwtGet(URL_LOGS_FABRICS_EXECUTE_ROLLS_BY_PERIOD, {period, status})
        } else if (period) {
            response = await jwtGet(URL_LOGS_FABRICS_EXECUTE_ROLLS_BY_PERIOD, {period})
        } else if (status !== null) {
            response = await jwtGet(URL_LOGS_FABRICS_EXECUTE_ROLLS_BY_PERIOD, {status})
        } else {
            response = await jwtGet(URL_LOGS_FABRICS_EXECUTE_ROLLS_BY_PERIOD)
        }

        const result = await response
        if (DEBUG) console.log('LogsStore: getFabricsRollsExecuteByPeriod: ', result)
        return result.data
    }

    // ___ Получение Логов выполнения рулонов по номеру рулона
    const getLogsFabricsExecuteRollsByRollNumber = async (rollNumber: string | null = null) => {
        if (!rollNumber) return

        let response

        if (rollNumber) {
            response = await jwtGet(URL_LOGS_FABRICS_EXECUTE_ROLLS_BY_ROLL_NUMBER, {roll: rollNumber})
        } else {
            response = await jwtGet(URL_LOGS_FABRICS_EXECUTE_ROLLS_BY_ROLL_NUMBER, {roll: rollNumber})
        }

        const result = await response
        if (DEBUG) console.log('LogsStore: getLogsFabricsExecuteRollsByRollNumber: ', result)
        return result.data
    }


    return {
        getLogsFabricsExecuteRollsByPeriod,
        getLogsFabricsExecuteRollsByRollNumber
    }

})



