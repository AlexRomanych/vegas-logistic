// Хранилище для заказов

import { defineStore } from 'pinia'
import { jwtGet, jwtPost, jwtDelete, jwtGet_, /*jwtPatch_, jwtUpdate,*/ } from '@/app/utils/jwt_api'

const DEBUG = true

// __ Устанавливаем глобальные переменные
const URL_TEXTILE_DESIGN_DOCUMENTS      = 'documents/kdch'                      // URL для получения КДЧ
const URL_TEXTILE_DESIGN_DOCUMENTS_BLOB = 'documents/kdch/blob'                 // URL для получения потока КДЧ
const URL_TEXTILE_DESIGN_DOCUMENTS_KDCH = 'documents/kdch/kdch'                 // URL для получения КДЧ по номеру
const URL_BLOCK_DESIGN_DOCUMENTS        = 'documents/kdb'                       // URL для получения КДБ
const URL_BLOCK_DESIGN_DOCUMENTS_BLOB   = 'documents/kdb/blob'                  // URL для получения потока КДБ
const URL_BLOCK_DESIGN_DOCUMENTS_KDB    = 'documents/kdb/kdb'                   // URL для получения КДБ по номеру


export const useSharedStore = defineStore('shared', () => {

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---      Конструкторская документация чехла (КДЧ)         !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

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
        const result = await jwtDelete(URL_TEXTILE_DESIGN_DOCUMENTS, { id })
        if (DEBUG) console.log('sharedStore: deleteTextileDesignDocumentById', result)
        return result
    }

    // __ Загружаем КДЧ
    const uploadTextileDesignDocument = async (formData: FormData) => {
        const headers = {
            'Content-Type': 'multipart/form-data',
        }
        const result  = await jwtPost(URL_TEXTILE_DESIGN_DOCUMENTS, formData, headers)
        if (DEBUG) console.log('sharedStore: uploadTextileDesignDocument', result)
        return result
    }


    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---      Конструкторская документация блоков (КДБ)        !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    // __ Получаем список КДБ
    const getBlockDesignDocuments = async () => {
        const result = await jwtGet(URL_BLOCK_DESIGN_DOCUMENTS)
        if (DEBUG) console.log('sharedStore: getBlockDesignDocuments', result)
        return result.data
    }

    // __ Получаем КДБ по id в потоке
    const getBlockDesignDocumentByIdBlob = async (id: number) => {
        const result = await jwtGet_(`${URL_BLOCK_DESIGN_DOCUMENTS_BLOB}/${id}`, undefined, {
            responseType: 'blob'
        })

        if (DEBUG) console.log('sharedStore: getBlockDesignDocuments', result)
        return result
    }

    // __ Получаем КДБ по номеру
    const getBlockDesignDocumentByKdb = async (kdb: string) => {
        const result = await jwtGet(`${URL_BLOCK_DESIGN_DOCUMENTS_KDB}/${kdb}`)
        if (DEBUG) console.log('sharedStore: getBlockDesignDocumentByKdch', result)
        return result.data
    }

    // __ Удаляем КДБ по id
    const deleteBlockDesignDocumentById = async (id: number) => {
        const result = await jwtDelete(URL_BLOCK_DESIGN_DOCUMENTS, { id })
        if (DEBUG) console.log('sharedStore: deleteBlockDesignDocumentById', result)
        return result
    }

    // __ Загружаем КДБ
    const uploadBlockDesignDocument = async (formData: FormData) => {
        const headers = {
            'Content-Type': 'multipart/form-data',
        }
        const result  = await jwtPost(URL_BLOCK_DESIGN_DOCUMENTS, formData, headers)
        if (DEBUG) console.log('sharedStore: uploadBlockDesignDocument', result)
        return result
    }


    return {
        getTextileDesignDocuments,
        uploadTextileDesignDocument,
        getTextileDesignDocumentByIdBlob,
        getTextileDesignDocumentByKdch,
        deleteTextileDesignDocumentById,

        getBlockDesignDocuments,
        uploadBlockDesignDocument,
        getBlockDesignDocumentByIdBlob,
        getBlockDesignDocumentByKdb,
        deleteBlockDesignDocumentById,

    }

})
