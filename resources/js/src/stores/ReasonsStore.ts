import { defineStore } from 'pinia'

// import {ref, reactive, computed, watch} from 'vue'

import type { IReason } from '@/types'


// @ts-ignore
import { jwtGet, jwtPost, jwtDelete, jwtUpdate, /*jwtPut, jwtPatch*/ } from '@/app/utils/jwt_api.js'

import { apiAnswerCheckDecorator } from '@/app/helpers/helpers_checks.ts'

// import {openNewTab} from '@/app/helpers/helpers_service.js'
// import axios from 'axios'

const URL_REASONS = 'reasons/'                                                   // Получаем все причины


export const useReasonStore = defineStore('reasons', () => {

    // __ Получаем с API список Причин
    const getReasons = async (active = null) => {
        const result = await jwtGet(URL_REASONS, {active})
        console.log('getReasons: ', result)
        return apiAnswerCheckDecorator(result).data
    }

    // __ Получаем с API Причину по id
    const getReasonById = async (id: number) => {
        const result = await jwtGet(URL_REASONS + id)
        console.log('store: getReasonById: ', result)
        return apiAnswerCheckDecorator(result).data
    }

    // __ Создание Причины
    const createReason = async (reason: IReason) => {
        const result = await jwtPost(URL_REASONS, {data: reason})
        console.log('store: createReason: ', result)
        return apiAnswerCheckDecorator(result).data
    }

    // __ Обновление Причины
    const updateReason = async (reason: IReason) => {
        const result = await jwtUpdate(URL_REASONS + reason.id, reason)     // Передаем id для автоматического извлечения модели
        console.log('store: updateReason: ', result)
        return apiAnswerCheckDecorator(result).data
    }


    // __ Удаление Причины
    const deleteReason = async (id: number = 0) => {
        if (id === 0) return
        const result = await jwtDelete(URL_REASONS + id)
        console.log('store: deleteReason: ', result)
        return apiAnswerCheckDecorator(result).data
    }


    // __ Получаем список причин по Группе ПЯ и Категории причин
    const getReasonsByCellsGroupAndReasonCategory = async (cellsGroupId: number, reasonsCategoryID: number) => {
        const result = await jwtGet(`${URL_REASONS}${cellsGroupId}/${reasonsCategoryID}`)
        console.log('store: getReasonsByCellsGroupAndReasonCategory: ', result)
        return apiAnswerCheckDecorator(result).data
    }


    return {
        getReasons, getReasonById, createReason, updateReason, deleteReason, getReasonsByCellsGroupAndReasonCategory
    }

})
