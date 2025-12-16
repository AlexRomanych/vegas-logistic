// Хранилище для моделей

import { defineStore } from 'pinia'
import { ref, /*reactive, computed, watch */} from 'vue'

import { jwtGet, jwtPost, jwtDelete } from '@/app/utils/jwt_api'
// import { openNewTab } from '@/app/helpers/helpers_service'

const DEBUG = true

const WRAP = 'models'   // Обертка на бэке

// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                                                   // Префикс API
const URL_MODELS = 'models/'                                                    // URL для получения списка моделей
const URL_MODEL = 'model/'                                                      // URL для получения модели

const URL_MODELS_UPLOAD = 'models/upload/'                                      // URL для загрузки моделей с диска
const URL_MODELS_LOAD = 'models/load/'                                          // URL для загрузки моделей из хранилища

const URL_MODELS_PROCEDURES = 'models/procedures/'                              // URL для получения процедур расчета с диска
const URL_MODELS_PROCEDURES_UPLOAD = 'models/procedures/upload/'                // URL для загрузки (обновлении) процедур расчета с диска
// const URL_MODEL_DELETE = 'models/delete/'                                    // URL для загрузки модели

export const useModelsStore = defineStore('models', () => {

    // __ Кэш Моделей
    const modelsCacshe = ref([])

    // __ Загрузка (Обновление) моделей на сервер
    const uploadModels = async (fileData: string) => {

        const headers = {
            'Content-Type': 'application/json',
            // 'Content-Type': 'application/octet-stream',
            // 'Content-Type': 'multipart/form-data',
            // 'Content-Type': 'application/x-www-form-urlencoded',
        }

        const response = await jwtPost(URL_MODELS_UPLOAD, {data: fileData}, headers)
        // const response = await jwtPost(URL_PLAN_ULOADS_UPLOAD, fileData, headers)
        const result = await response

        if (DEBUG) console.log('ModelsStore: uploadModels: ', result)

        return result
    }

    //__ Загружаем список моделей из файла на сервере
    const modelsLoad = async () => {
        const response = await jwtGet(URL_MODELS_LOAD)
        return response
    }


    //__ Получаем с API список моделей
    const getModels = async (params: any) => {
        const result = await jwtGet(URL_MODELS, params)
        modelsCacshe.value = result[WRAP] // кэшируем
        return result[WRAP] // все возвращается через Resource с ключем data
    }


    //__ Загружаем список моделей из файла на сервере
    const getProcedures = async () => {
        const response = await jwtGet(URL_MODELS_PROCEDURES)
        return response.data
    }


    // __ Загрузка (Обновление) Процедур расчета на сервере
    const uploadProcedures = async (fileData: string) => {

        const headers = {
            'Content-Type': 'application/json',
        }

        const response = await jwtPost(URL_MODELS_PROCEDURES_UPLOAD, {data: fileData}, headers)
        const result = await response

        if (DEBUG) console.log('ModelsStore: uploadProcedures: ', result)

        return result
    }

    return {
        uploadModels,
        modelsLoad,
        getModels,

        getProcedures,
        uploadProcedures,
    }
})


