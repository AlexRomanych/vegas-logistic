// info Хранилище для Планов

import { defineStore } from 'pinia'
import { jwtGet, jwtPost, jwtDelete, jwtUpdate, jwtPut, jwtPatch } from '@/app/utils/jwt_api'

const URL_PLAN_ULOADS_UPLOAD = 'plan/loads/upload'

export const usePlansStore = defineStore('fabrics', () => {


    // __ Загрузка Плана загрузок на сервер
    // fileData - данные файла, отправляем в RAW формате
    const uploadLoads = async (fileData: string) => {

        // console.log(fileData)

        const headers = {
            'Content-Type': 'application/json',
            // 'Content-Type': 'application/octet-stream',
            // 'Content-Type': 'multipart/form-data',
            // 'Content-Type': 'application/x-www-form-urlencoded',
        }

        // const response = await jwtPost(URL_PLAN_ULOADS_UPLOAD, fileData, headers)
        const response = await jwtPost(URL_PLAN_ULOADS_UPLOAD, {data: fileData}, headers)
        const result = await response

        console.log('uploadLoads: ', result)

        return result
    }

    return {
        uploadLoads,
    }

})



