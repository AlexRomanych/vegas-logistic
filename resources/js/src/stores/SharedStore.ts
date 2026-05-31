// Хранилище для заказов

import { defineStore } from 'pinia'
import { ref } from 'vue'

import { jwtGet, jwtPost, jwtDelete, jwtPatch_, jwtUpdate, jwtGet_, } from '@/app/utils/jwt_api'


const DEBUG = true

// Устанавливаем глобальные переменные
const URL_TEXTILE_DESIGN_DOCUMENTS      = 'documents/kdch'                           // URL для получения КДЧ
const URL_TEXTILE_DESIGN_DOCUMENTS_BLOB = 'documents/kdch/blob'                      // URL для получения потока КДЧ
const URL_TEXTILE_DESIGN_DOCUMENTS_KDCH = 'documents/kdch/kdch'                      // URL для получения КДЧ по номеру


export const useSharedStore = defineStore('shared', () => {

    // __ Получаем список КДЧ
    const getTextileDesignDocuments = async () => {
        const result = await jwtGet(URL_TEXTILE_DESIGN_DOCUMENTS)
        if (DEBUG) console.log('sharedStore: getTextileDesignDocuments', result)
        return result.data
    }


    // __ Получаем КДЧ по id в потоке
    const getTextileDesignDocumentByIdBlob = async (id: number) => {
        const result = await jwtGet_(`${URL_TEXTILE_DESIGN_DOCUMENTS_BLOB}/${id}`, undefined, {
            responseType: 'blob'
        })

        if (DEBUG) console.log('sharedStore: getTextileDesignDocuments', result)
        return result
    }

    // __ Получаем КДЧ по номеру
    const getTextileDesignDocumentByKdch = async (kdch: string) => {
        const result = await jwtGet(`${URL_TEXTILE_DESIGN_DOCUMENTS_KDCH}/${kdch}`)
        if (DEBUG) console.log('sharedStore: getTextileDesignDocumentByKdch', result)
        return result.data
    }

    // __ Удаляем КДЧ по id
    const deleteTextileDesignDocumentById = async (id: number) => {
        const result = await jwtDelete(URL_TEXTILE_DESIGN_DOCUMENTS, {id})
        if (DEBUG) console.log('sharedStore: deleteTextileDesignDocumentById', result)
        return result
    }

    // __ Загружаем КДЧ
    const uploadTextileDesignDocument = async (formData: FormData) => {
        // const result = await jwtGet(URL_TEXTILE_DESIGN_DOCUMENTS)
        // if (DEBUG) console.log('sharedStore: getTextileDesignDocuments', result)
        // return result.data

        const headers = {
            'Content-Type': 'multipart/form-data',
        }

        const result = await jwtPost(URL_TEXTILE_DESIGN_DOCUMENTS, formData, headers)

        if (DEBUG) console.log('sharedStore: uploadTextileDesignDocument', result)

        return result
        // const response = await axios.post('/api/design-documents/upload', formData, {
        //     headers: {
        //         'Content-Type': 'multipart/form-data',
        //     },
        // })
    }


    // // __ Получаем рабочего по id
    // const getWorkerById = async (id: number = 0) => {
    //
    //     if (id === 0) return WORKER_DRAFT
    //     const result = await jwtGet(`${URL_WORKERS}/${id}`)
    //     if (DEBUG) console.log('workersStore: getWorkerById', result.data)
    //     return result.data
    // }
    //
    // // __ Обновление рабочего
    // const updateWorker = async (worker: IWorker | null = null) => {
    //     if (worker === null) {
    //         return
    //     }
    //     const result = await jwtPatch_(URL_WORKERS, worker)
    //     if (DEBUG) console.log('workersStore: updateWorker', result.data)
    //     return result
    // }
    //
    // // __ Создание рабочего
    // const createWorker = async (worker: IWorker | null = null) => {
    //     if (worker === null) {
    //         return
    //     }
    //     const result = await jwtPost(URL_WORKERS, worker)
    //     return result
    // }
    //
    // // __ Удаление рабочего
    // const deleteWorker = async (delWorkerId: number = 0) => {
    //     if (delWorkerId === 0) {
    //         return
    //     }
    //     const result = await jwtDelete(URL_WORKERS, { id: delWorkerId })
    //     return result
    // }


    return {
        getTextileDesignDocuments,
        uploadTextileDesignDocument,
        getTextileDesignDocumentByIdBlob,
        getTextileDesignDocumentByKdch,
        deleteTextileDesignDocumentById,

        // deleteWorker,
        // getWorkerById,
        // updateWorker,
        // createWorker,

    }

})
