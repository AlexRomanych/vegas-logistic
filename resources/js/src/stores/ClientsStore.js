// INFO Хранилище для клиентов

import { defineStore } from 'pinia'
// import { ref, reactive, computed, watch } from 'vue'

import { jwtGet, jwtPost, jwtPut, jwtPut_, jwtPatch, jwtDelete } from '@/app/utils/jwt_api'

const SHOW_LOGS = true


// Обертка на бэке
const WRAP = 'clients'

// Устанавливаем глобальные переменные
const API_PREFIX = '/api/v1/'                   // Префикс API
const URL_CLIENTS = 'clients'                   // URL для клиентов
const URL_CLIENT = 'client'                     // URL для клиента
const URL_CLIENT_CREATE = 'client/create'       // создание клиента
const URL_CLIENT_UPDATE = 'client/update'       // обновление клиента
const URL_CLIENT_DELETE = 'client/delete'       // удаление клиента

const URL_CLIENTS_LOAD = 'clients/load' // URL для загрузки клиентов из хранилища

export const useClientsStore = defineStore('clients', () => {
    let clientsShow = []

    //__ Загружает клиентов из файла в базу данных
    const clientsLoad = async () => {
        const response = await jwtGet(URL_CLIENTS_LOAD)
        return response
    }

    //__ Получаем с API всех клиентов
    /**
     *
     * @param {boolean | null} status // нет параметра - все, true - активные, false - неактивные
     * @returns {Promise<*>}
     */
    const getClients = async (status = null) => {
        const params = status === true
            ? '1'
            : status === false
                ? '0'
                : ''
        const result = await jwtGet(`${URL_CLIENTS}/${params}`)

        clientsShow.value = result.data // кэшируем

        if (SHOW_LOGS) console.log('useClientsStore: getClients: ', result)

        return result.data // все возвращается через Resource с ключем data
    }

    //__ Получаем с API клиента по id
    const getClient = async (id = 0) => {
        if (id == 0) return null
        const result = await jwtGet(`${URL_CLIENT}/${id}`)
        if (SHOW_LOGS) console.log('useClientsStore: getClient: ', result)
        return result.data
    }

    /**
     * __ Создаем клиента
     * @param {IClient} client
     * @returns {Promise<*>}
     */
    const createClient = async (client) => {
        const result = await jwtPost(URL_CLIENT_CREATE, client)
        if (SHOW_LOGS) console.log('clientsStore: createClient: ', result)
        return result
    }

    /**
     * __ Обновляем клиента
     * @param {IClient} client
     * @returns {Promise<*>}
     */
    const updateClient = async (client) => {
        const result = await jwtPut_(URL_CLIENT_UPDATE, client)
        if (SHOW_LOGS) console.log('clientsStore: updateClient: ', result)
        return result
    }


    /**
     * __ Удаляем клиента
     * @param {number} id
     * @returns {Promise<*>}
     */
    const deleteClient = async (id) => {
        console.log('deleteClient!:', id)
        const result = await jwtDelete(URL_CLIENT_DELETE, {id})
        // const result = await jwtDelete(URL_CLIENT_DELETE, id)
        if (SHOW_LOGS) console.log('clientsStore: deleteClient: ', result)
        return result
    }



    return {
        getClients, getClient, createClient, updateClient, deleteClient,
        clientsLoad,
    }
})
