// Хранилище для заказов

import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'

import {jwtGet, jwtPost} from "@/src/app/utils/jwt_api"

// Обертка на бэке
const WRAP = 'clients'

// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                   // Префикс API
const URL_CLIENTS = 'clients/'                  // URL для клиентов
const URL_CLIENT = 'client/'                    // URL для клиентов

const URL_CLIENTS_LOAD = 'clients/load'         // URL для загрузки клиентов из хранилища


export const useClientsStore = defineStore('clients', () => {

    let clientsShow = []

    //attract: загружает клиентов из файла в базу данных
    const clientsLoad = async () => {
        const response = await jwtGet(URL_CLIENTS_LOAD)
        return response
    }

    //attract: Получаем с API список моделей
    const getClients = async (params) => {
        const result = await jwtGet(URL_CLIENTS, params)
        clientsShow.value = result[WRAP]                // кэшируем
        return result[WRAP]                             // все возвращается через Resource с ключем data
    }

    return {
        getClients,
        clientsLoad,
    }

})
