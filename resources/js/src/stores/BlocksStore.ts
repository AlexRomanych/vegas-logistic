// Хранилище для ПЯ Швейки

import { defineStore } from 'pinia'

import { jwtGet, jwtPost, /*jwtDelete,*/ jwtPatch, jwtPut_, jwtPut, jwtPatch_, jwtDelete } from '@/app/utils/jwt_api'


const DEBUG = true

// Устанавливаем глобальные переменные
// const API_PREFIX                           = '/api/v1/' // Префикс API
const URL_BLOCKS_COLLECTIONS = '/blocks/collections'                       // URL для получения Коллекций Блоков
const URL_BLOCKS_TEST        = '/blocks/test'                        // URL для тестирования

export const useBlocksStore = defineStore('blocks', () => {

    // __ Получение Коллекции Блоков
    const getBlockCollections = async () => {
        const response = await jwtGet(URL_BLOCKS_COLLECTIONS)
        const result   = await response
        if (DEBUG) console.log('BlocksStore: getBlockCollections: ', result)
        return result.data
    }

    // __ Получение Коллекции блоков по id
    const getBlockCollectionById = async (id: number) => {
        const response = await jwtGet(URL_BLOCKS_COLLECTIONS + '/' + id)
        const result   = await response
        if (DEBUG) console.log('CuttingStore: getBlockCollectionById: ', result)
        return result.data
    }


    const test = async () => {
        const response = await jwtGet(URL_BLOCKS_TEST)
        const result   = await response
        if (DEBUG) console.log('BlocksStore: test: ', result)
        return result
    }


    return {
        getBlockCollections,
        getBlockCollectionById,


        test,
    }
})
