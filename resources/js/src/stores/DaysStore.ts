import { defineStore } from 'pinia'

import { jwtGet, jwtPost, jwtPut_, jwtDelete, /*jwtPut, jwtPatch_,*/ } from '@/app/utils/jwt_api'
import type { ICellEvent, ICellEventsCells } from '@/types'

const DEBUG = true

const URL_CELL_EVENTS = '/cell/events'                        // URL для получения Журнала Событий

export const useCellEventsStore = defineStore('cellDays', () => {

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---              Журнал Событий                 !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    // __ Получение Журнала Событий
    const getEvents = async (dayId: number, cell: ICellEventsCells) => {
        const response = await jwtGet(URL_CELL_EVENTS, { day: dayId, cell })
        const result   = await response
        if (DEBUG) console.log('CellEventsStore: getEvents: ', result)
        return result.data
    }

    // __ Обновление Записи Журнала Событий
    const updateEvent = async (cellEvent: ICellEvent) => {
        const response = await jwtPut_(URL_CELL_EVENTS, { data: cellEvent })
        const result   = await response
        if (DEBUG) console.log('CellEventsStore: updateEvent: ', result)
        return result.data
    }

    // __ Создание Записи Журнала Событий
    const createEvent = async (dayId: number, cellEvent: ICellEvent) => {
        const response = await jwtPost(URL_CELL_EVENTS, { data: { day: dayId, ...cellEvent } })
        const result   = await response
        if (DEBUG) console.log('CellEventsStore: createEvent: ', result)
        return result.data
    }

    // __ Удаление Записи Журнала Событий
    const deleteEvent = async (cellEvent: ICellEvent) => {
        const response = await jwtDelete(URL_CELL_EVENTS, { data: cellEvent })
        const result   = await response
        if (DEBUG) console.log('CellEventsStore: deleteEvent: ', result)
        return result.data
    }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---                 Тесты                       !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    // const test = async () => {
    //     const response = await jwtGet(URL_BLOCKS_TEST)
    //     const result   = await response
    //     if (DEBUG) console.log('BlocksStore: test: ', result)
    //     return result
    // }


    return {

        getEvents,
        updateEvent,
        createEvent,
        deleteEvent,

    }
})
