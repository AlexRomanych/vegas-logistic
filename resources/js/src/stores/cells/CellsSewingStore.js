// Хранилище для ПЯ Швейки

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {jwtGet, jwtPost, jwtDelete} from "@/src/app/utils/jwt_api"
import {openNewTab} from "@/src/app/helpers/helpers_service"

import {
    CELL_AUTO_TYPE,
    CELL_UNIVERSAL_TYPE,
    CELL_SOLID_HARD_TYPE,
    CELL_SOLID_LIGHT_TYPE
} from '@/src/app/constants/sewingTypes.js'

// Обертка на бэке
const WRAP = 'sewing'

// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                                               // Префикс API
const GLOBAL_PREFIX = 'manufacture/cells/sewing/'                           // Префикс группы ПЯ Швейка
const URL_CELLS_SEWING_AUTO = GLOBAL_PREFIX + 'auto'                        // URL для АШМ
const URL_CELLS_SEWING_UNIVERSAL = GLOBAL_PREFIX + 'universal'              // URL для УШМ
const URL_CELLS_SEWING_SOLID_HARD = GLOBAL_PREFIX + 'hard'                  // URL для Обшивки hard
const URL_CELLS_SEWING_SOLID_LIGHT = GLOBAL_PREFIX + 'light'                // URL для Обшивки light

// const CELL_AUTO_TYPE = 'auto'
// const CELL_UNIVERSAL_TYPE = 'universal'
// const CELL_SOLID_HARD_TYPE = 'solid_hard'
// const CELL_SOLID_LIGHT_TYPE = 'solid_light'

//attract: Получаем с URL API ПЯ (cellsType указывает на тип ПЯ)
const getUrlBySewingType = (type) => {
    switch (type) {
        case CELL_AUTO_TYPE:
            return URL_CELLS_SEWING_AUTO
        case CELL_UNIVERSAL_TYPE:
            return URL_CELLS_SEWING_UNIVERSAL
        case CELL_SOLID_HARD_TYPE:
            return URL_CELLS_SEWING_SOLID_HARD
        case CELL_SOLID_LIGHT_TYPE:
            return URL_CELLS_SEWING_SOLID_LIGHT
        default:
            return URL_CELLS_SEWING_AUTO
    }
}

export const useCellsSewingStore = defineStore('cells_sewing', () => {

    // Список заказов, которые получили к отображению
    let cellsSewingAutoShow = []                // Список АШМ
    let cellsSewingUniversalShow = []           // Список УШМ
    let cellsSewingSolidHardShow = []           // Список Обшивка hard
    let cellsSewingSolidLightShow = []          // Список Обшивка light

    let dateInterval = {}                       // сюда будет залетать интервал к отображению

    //attract: Получаем с API список ПЯ Швенйки (cellsType указывает на тип ПЯ)
    const getCellsSewing = async (params, cellsType) => {

        console.log(params, cellsType)


        const result = await jwtGet(getUrlBySewingType(cellsType), params)
        console.log(result)

        // кэшируем результат
        switch (cellsType) {
            case CELL_AUTO_TYPE:
                cellsSewingAutoShow.value = result[WRAP]
                break
            case CELL_UNIVERSAL_TYPE:
                cellsSewingUniversalShow.value = result[WRAP]
                break
            case CELL_SOLID_HARD_TYPE:
                cellsSewingSolidHardShow.value = result[WRAP]
                break
            case CELL_SOLID_LIGHT_TYPE:
                cellsSewingSolidLightShow.value = result[WRAP]
                break
        }

        return result[WRAP]                      // все возвращается через Resource с ключем wrap
    }


    return {
        getCellsSewing,
        dateInterval

    }

})


