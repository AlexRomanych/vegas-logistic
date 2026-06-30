// Хранилище для ПЯ Швейки

import { defineStore } from 'pinia'

import { jwtGet, jwtPost, /*jwtDelete,*/ jwtPatch, jwtPut_, jwtPut, jwtPatch_, jwtDelete } from '@/app/utils/jwt_api'
import type {
    IBlock,
    IBlockCollection,
    IBlockDayWorker,
    IBlockTaskStatusEntity,
    IBlockTaskStatusesSet,
    ICuttingDayWorker,
    ICuttingTaskStatusEntity,
} from '@/types'
import { ref } from 'vue'


const DEBUG = true

// Устанавливаем глобальные переменные
// const API_PREFIX                           = '/api/v1/' // Префикс API
const URL_BLOCKS_COLLECTIONS               = '/blocks/collections'                 // URL для получения Коллекций Блоков
const URL_BLOCKS                           = '/blocks'                             // URL для получения Блоков
const URL_BLOCKS_TEST                      = '/blocks/test'                        // URL для тестирования
const URL_BLOCKS_TASK_STATUSES             = '/blocks/task/statuses'               // URL для получения Статуса Движения СЗ
const URL_BLOCKS_TASK_STATUSES_SET         = '/blocks/task/statuses/set'           // URL для изменения/добавления Статуса Движения СЗ
const URL_BLOCKS_TASK_STATUSES_COLOR_PATCH = '/blocks/task/statuses/color/patch'   // URL для получения Статуса Движения СЗ


export const useBlocksStore = defineStore('blocks', () => {

    // __ Статусы Движения СЗ
    const globalBlockTaskStatuses = ref<IBlockTaskStatusEntity[]>([])

    // __ Массив Рабочих
    const globalWorkers = ref<IBlockDayWorker[]>([])


    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---        Коллекции (Группы) Блоков            !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

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
        if (DEBUG) console.log('BlocksStore: getBlockCollectionById: ', result)
        return result.data
    }

    // __ Создаем Коллекции блоков
    const createBlockCollection = async (blockCollection: IBlockCollection) => {
        const result = await jwtPost(URL_BLOCKS_COLLECTIONS, blockCollection)
        if (DEBUG) console.log('BlocksStore: createBlockCollection: ', result)
        return result
    }

    // __ Обновляем Коллекции блоков
    const updateBlockCollection = async (blockCollection: IBlockCollection) => {
        const result = await jwtPut_(URL_BLOCKS_COLLECTIONS, blockCollection)
        if (DEBUG) console.log('BlocksStore: updateBlockCollection: ', result)
        return result
    }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---                 Блоки                       !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // __ Получение Блока по id
    const getBlockById = async (id: number) => {
        const response = await jwtGet(URL_BLOCKS + '/' + id)
        const result   = await response
        if (DEBUG) console.log('BlocksStore: getBlockById: ', result)
        return result.data
    }

    // __ Создаем Блок
    const createBlock = async (block: IBlock) => {
        const result = await jwtPost(URL_BLOCKS, block)
        if (DEBUG) console.log('BlocksStore: createBlock: ', result)
        return result
    }

    // __ Обновляем Блок
    const updateBlock = async (block: IBlock) => {
        const result = await jwtPut_(URL_BLOCKS, block)
        if (DEBUG) console.log('BlocksStore: updateBlock: ', result)
        return result
    }

    // --- ----------------------------------------------------------
    // --- ------------------- Статусы СЗ ---------------------------
    // --- ----------------------------------------------------------

    // __ Получение Статусов Движения СЗ
    const getBlockTaskStatuses = async () => {
        const response = await jwtGet(URL_BLOCKS_TASK_STATUSES)
        const result   = await response
        if (DEBUG) console.log('BlockStore: getBlockTaskStatuses: ', result)
        globalBlockTaskStatuses.value = result.data    // __ кэшируем
        return result.data
    }

    // __ Устанавливаем цвет ярлычка Типов заказов (серийная, гаррмем, прогнозная и т.д.)
    const patchBlockTaskStatusColor = async (cuttingTaskStatusId: number, color: string) => {
        const result = await jwtPatch(URL_BLOCKS_TASK_STATUSES_COLOR_PATCH, { id: cuttingTaskStatusId, color })
        if (DEBUG) console.log('BlockStore: patchBlockTaskStatusColor', result)
        await getBlockTaskStatuses()   // __ Обновляем статусы, чтобы был актуальный цвет
        return result.data
    }

    // __ Устанавливаем статусы для СЗ.
    // __ data: [{ task: number, status: number }]
    const setBlockTasksStatuses = async (data: IBlockTaskStatusesSet[]) => {
        const response = await jwtPost(URL_BLOCKS_TASK_STATUSES_SET, data)
        const result   = await response
        if (DEBUG) console.log('BlockStore: setStatuses: ', result)
        return result.data
    }


    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---                 Тесты                       !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    const test = async () => {
        const response = await jwtGet(URL_BLOCKS_TEST)
        const result   = await response
        if (DEBUG) console.log('BlocksStore: test: ', result)
        return result
    }


    return {
        getBlockCollections,
        getBlockCollectionById,
        createBlockCollection,
        updateBlockCollection,

        getBlockById,
        createBlock,
        updateBlock,

        getBlockTaskStatuses,
        patchBlockTaskStatusColor,
        setBlockTasksStatuses,

        test,
    }
})
