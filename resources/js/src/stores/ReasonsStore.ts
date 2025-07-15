import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

// @ts-ignore
import {jwtGet, jwtPost, jwtDelete, jwtUpdate, jwtPut, jwtPatch} from '@/app/utils/jwt_api.js'

import {apiAnswerCheckDecorator} from '@/app/helpers/helpers_checks.ts'

// import {openNewTab} from '@/app/helpers/helpers_service.js'
// import axios from 'axios'

const URL_REASONS = 'reasons'                                                   // Получаем все причины


export const useReasonStore = defineStore('reasons', () => {

    // __ Получаем с API список ПС
    const getReasons = async (active = null) => {

        const result = await jwtGet(URL_REASONS, {active})
        console.log('store', result)

        return apiAnswerCheckDecorator(result).data
    }

    return {
        getReasons,
    }

})
