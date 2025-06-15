import axios from 'axios'
import { useRouter } from 'vue-router'
import ErrorClass from '@/app/classes/ErrorClass.js'

const versionBaseUrl = '/api/v1'

const jwtAxios = axios.create({
    withCredentials: false,
    baseURL: versionBaseUrl,
})

// Настраиваем конфиг для экземпляра axios
// Добавляем в заголовки токен
const onFulfilled = (config) => {
    const token = localStorage.getItem('token')
    config.headers.Authorization = `Bearer ${token}`
    // config.headers['Content-Type'] = 'application/x-www-form-urlencoded'
    return config
}

// Добавляем обработчик ошибки
const onRejected = (error) => {
    console.log('onRejected', error.response.data.message)
    console.log('onRejected', error.response)

    // debugger

    // Тут обрабатываем ошибку, связанную с истечением срока действия токена
    if (error.response.data.message === 'Token has expired') {
        return axios
            .post(
                versionBaseUrl + '/auth/refresh',
                {},
                {
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`,
                    },
                },
                // config.headers.authorization = `Bearer ${localStorage.getItem('token')}`
            )
            .then((res) => {
                console.log('in Onrejected ok', res.data)
                console.log('in Onrejected ok', res.data.data.access_token)
                localStorage.setItem('token', res.data.data.access_token) // устанавливаем refresh токен
                error.config.headers.authorization = `Bearer ${res.data.data.access_token}` // В объект ошибки измеяем config, тот же, что и в jwtAxios
                return jwtAxios.request(error.config)
            })
            .catch((error) => {
                // const router = useRouter()
                // router.push({name: 'login'})
            })
    } else {
        // throw new Error({name: error.response.data.message})
        // debugger
        // throw new ErrorClass ({name: 'Ошибка загрузки данных'})
        // return []    // todo Тут надо подумать еще, что возвращать в случае ошибки или как-то ее обрабатывать, прокидывая Callout

        //warning ключ data - обязательный
        return { data: { name: 'Ошибка загрузки данных', status: 600 } }
    }
}

// Настраиваем перехват запроса
jwtAxios.interceptors.request.use(
    (config) => onFulfilled(config),
    (error) => onRejected(error),
)

// Настраиваем перехват ответа
jwtAxios.interceptors.response.use(
    (config) => onFulfilled(config),
    (error) => onRejected(error),
)

// Формируем свой GET
export async function jwtGet(url, params = {}) {
    // jwtAxios.defaults.params = params        // эта намертво закрепляет параметры
    // console.log(params)

    try {
        const res = await jwtAxios.get(url, { params: params })
        const data = await res.data
        return data
    } catch (error) {
        // console.log(error.response)
        console.log('jwtGet: ', error.response.data.message)
        debugger
    }
}

// Формируем свой POST
export async function jwtPost(url, data = {}, headers = {}) {
    // try {

    if (headers) {
        Object.entries(headers).forEach(([key, value]) => {
            jwtAxios.defaults.headers.common[key] = value
        })

        // jwtAxios.defaults.headers.common['Content-Type'] = 'application/x-www-form-urlencoded'
        // jwtAxios.defaults.headers.common['Content-Type'] = 'multipart/form-data'
    }

    // const result = await jwtAxios.post(url, data, {
    //     headers: {
    //         'Content-Type': 'multipart/form-data',
    //         // 'X-Debug': 1,
    //         // 'Content-Type': 'application/json',
    //     }
    // })

    const response = await jwtAxios.post(url, data)
    const result = await response.data

    return result

    // } catch (error) {
    //     console.log('jwtPost: ', error)
    // }
}

// Формируем свой DELETE
export async function jwtDelete(url, data = {}) {
    try {
        const response = await jwtAxios.delete(url, { data: data })

        console.log(response)

        const res = await response.data
        return res
    } catch (error) {
        // console.log(error.response)
        // console.log('jwtDelete: ', error.response.data.message)
        console.log('jwtDelete: ', error)
        debugger
    }
}

export async function jwtUpdate(url, data = {}) {
    try {
        const response = await jwtAxios.put(url, { data: data })

        console.log(response)

        const res = await response.data
        return res
    } catch (error) {
        // console.log(error.response)
        // console.log('jwtDelete: ', error.response.data.message)
        console.log('jwtUpdate: ', error)
        debugger
    }
}

export async function jwtPut(url, data = {}) {
    return await jwtUpdate(url, data)

    // try {
    //     const response = await jwtAxios.put(url, {data: data})
    //
    //     console.log(response)
    //
    //     const res = await response.data
    //     return res
    //
    // } catch (error) {
    //     // console.log(error.response)
    //     // console.log('jwtDelete: ', error.response.data.message)
    //     console.log('jwtUpdate: ', error)
    //     debugger
    // }
}

export async function jwtPatch(url, data = {}) {
    try {
        const response = await jwtAxios.patch(url, { data: data })

        console.log(response)

        const res = await response.data
        return res
    } catch (error) {
        // console.log(error.response)
        // console.log('jwtDelete: ', error.response.data.message)
        console.log('jwtPatch: ', error)
        debugger
    }
}

// export default jwtAxios
