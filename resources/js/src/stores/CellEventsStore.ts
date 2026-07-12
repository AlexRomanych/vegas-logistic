import { defineStore } from 'pinia'

import { jwtGet, jwtPost, /*jwtDelete,*/ jwtPatch, jwtPut_, jwtPut, jwtPatch_, jwtDelete } from '@/app/utils/jwt_api'
import type { ICellEvent, ICellEventsCells } from '@/types'


const DEBUG = true

// Устанавливаем глобальные переменные
// const API_PREFIX                           = '/api/v1/' // Префикс API
const URL_CELL_EVENTS = '/cell/events'                        // URL для получения Журнала Событий

export const useCellEventsStore = defineStore('cellEvents', () => {


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


    //
    // // __ Получение Коллекции блоков по id
    // const getBlockCollectionById = async (id: number) => {
    //     const response = await jwtGet(URL_BLOCKS_COLLECTIONS + '/' + id)
    //     const result   = await response
    //     if (DEBUG) console.log('BlocksStore: getBlockCollectionById: ', result)
    //     return result.data
    // }
    //
    // // __ Создаем Коллекции блоков
    // const createBlockCollection = async (blockCollection: IBlockCollection) => {
    //     const result = await jwtPost(URL_BLOCKS_COLLECTIONS, blockCollection)
    //     if (DEBUG) console.log('BlocksStore: createBlockCollection: ', result)
    //     return result
    // }
    //
    // // __ Обновляем Коллекции блоков
    // const updateBlockCollection = async (blockCollection: IBlockCollection) => {
    //     const result = await jwtPut_(URL_BLOCKS_COLLECTIONS, blockCollection)
    //     if (DEBUG) console.log('BlocksStore: updateBlockCollection: ', result)
    //     return result
    // }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---                 Блоки                       !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // __ Получение Блока по id
    // const getBlockById = async (id: number) => {
    //     const response = await jwtGet(URL_BLOCKS + '/' + id)
    //     const result   = await response
    //     if (DEBUG) console.log('BlocksStore: getBlockById: ', result)
    //     return result.data
    // }
    //
    // // __ Создаем Блок
    // const createBlock = async (block: IBlock) => {
    //     const result = await jwtPost(URL_BLOCKS, block)
    //     if (DEBUG) console.log('BlocksStore: createBlock: ', result)
    //     return result
    // }
    //
    // // __ Обновляем Блок
    // const updateBlock = async (block: IBlock) => {
    //     const result = await jwtPut_(URL_BLOCKS, block)
    //     if (DEBUG) console.log('BlocksStore: updateBlock: ', result)
    //     return result
    // }
    //


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
