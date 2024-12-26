// Хранилище для Групп ПЯ
// Сделаю тут же и для всех ПЯ

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {jwtGet, jwtPost, jwtDelete} from "@/src/app/utils/jwt_api"
import {openNewTab} from "@/src/app/helpers/helpers_service"

// Обертка на бэке
const WRAP_GROUPS = 'cells_groups'
const WRAP_CELLS = 'cells'

// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                                           // Префикс API
const GLOBAL_GROUP_PREFIX = 'manufacture/'                              // Префикс группы
const URL_CELLS_GROUPS = GLOBAL_GROUP_PREFIX + 'cells/groups/'          // URL для получения списка групп
const URL_CELLS_GROUP = GLOBAL_GROUP_PREFIX + 'cells/group/'            // URL для получения группы

const URL_CELLS = GLOBAL_GROUP_PREFIX + 'cells'                         // URL для получения списка всех ПЯ



export const useCellsGroupsStore = defineStore('cells_groups', () => {

    // Список заказов, которые получили к отображению
    let groupsShow = []         // Список групп ПЯ
    let cellsShow = []          // Список ПЯ

    //attract: Получаем с API список Групп ПЯ
    const getCellsGroups = async (params) => {
        const result = await jwtGet(URL_CELLS_GROUPS, params)
        // console.log(result)
        groupsShow.value = result[WRAP_GROUPS]          // кэшируем
        return result[WRAP_GROUPS]                      // все возвращается через Resource с ключем wrap
    }

    //attract: Получаем с API  ПЯ
    const getCells = async (params) => {
        const result = await jwtGet(URL_CELLS, params)
        // console.log(result)
        cellsShow.value = result[WRAP_CELLS]            // кэшируем
        return result[WRAP_CELLS]                       // все возвращается через Resource с ключем wrap
    }

    return {
        getCellsGroups,
        getCells,
    }

})
