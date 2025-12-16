// ___ Хранилище для материалов

import { defineStore } from 'pinia'
import { ref, /*reactive, computed, watch */} from 'vue'

import { jwtGet, jwtPost, jwtDelete } from '@/app/utils/jwt_api'
// import { openNewTab } from '@/app/helpers/helpers_service'

const DEBUG = true


// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                                                   // Префикс API
const URL_MATERIALS = 'materials/'                                              // URL для получения списка материалов
const URL_MATERIAL = 'material/'                                                // URL для получения материала

const URL_MATERIALS_UPLOAD = 'materials/upload/'                                // URL для загрузки материалов с диска

export const useMaterialsStore = defineStore('materials', () => {

    // __ Кэш Материалов
    const materialsCacshe = ref([])

    // __ Загрузка (Обновление) моделей на сервер
    const uploadMaterials = async (fileData: string) => {

        const headers = {
            'Content-Type': 'application/json',
            // 'Content-Type': 'application/octet-stream',
            // 'Content-Type': 'multipart/form-data',
            // 'Content-Type': 'application/x-www-form-urlencoded',
        }

        const response = await jwtPost(URL_MATERIALS_UPLOAD, {data: fileData}, headers)
        const result = await response

        if (DEBUG) console.log('MaterialsStore: uploadMaterials: ', result)

        return result
    }


    //__ Получаем с API список моделей
    const getMaterials = async (params: any = null) => {
        const result = await jwtGet(URL_MATERIALS, params)
        materialsCacshe.value = result.data // кэшируем
        return result.data // все возвращается через Resource с ключем data
    }


    return {
        uploadMaterials,
        getMaterials,
    }
})


