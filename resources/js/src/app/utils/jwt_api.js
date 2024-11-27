import axios from 'axios';
import {useRouter} from "vue-router";
import ErrorClass from "@/src/app/classes/ErrorClass.js";


const versionBaseUrl = '/api/v1'

const jwtAxios = axios.create({
    withCredentials: false,
    baseURL: versionBaseUrl
})

// Настраиваем конфиг для экземпляра axios
// Добавляем в заголовки токен
const onFulfilled = (config) => {
    const token = localStorage.getItem('token')
    config.headers.Authorization = `Bearer ${token}`
    console.log(config)
    return config
}

// Добавляем обработчик ошибки
const onRejected = (error) => {
    console.log('onRejected', error.response.data.message)
    console.log('onRejected', error.response)

// Тут обрабатываем ошибку, связанную с истечением срока действия токена
    if (error.response.data.message === 'Token has expired') {

        return axios.post(versionBaseUrl + '/auth/refresh', {},
            {
                headers: {
                    'authorization': `Bearer ${localStorage.getItem('token')}`
                }
            }
            // config.headers.authorization = `Bearer ${localStorage.getItem('token')}`

        ).then(res => {
            console.log('in Onrejected ok', res.data)
            console.log('in Onrejected ok', res.data.data.access_token)
            localStorage.setItem('token', res.data.data.access_token)                       // устанавливаем refresh токен
            error.config.headers.authorization = `Bearer ${res.data.data.access_token}`     // В объект ошибки измеяем config, тот же, что и в jwtAxios
            return jwtAxios.request(error.config)

        }).catch(error => {
            // const router = useRouter()
            // router.push({name: 'login'})
        })
    } else {
        // throw new Error({name: error.response.data.message})
        // debugger
        // throw new ErrorClass ({name: 'Ошибка загрузки данных'})
        return []    // todo Тут надо подумать еще, что возвращать в случае ошибки или как-то ее обрабатывать, прокидывая Callout
    }

}

// Настраиваем перехват запроса
jwtAxios.interceptors.request.use(
    (config) => onFulfilled(config),
    (error) => onRejected(error)
)

// Настраиваем перехват ответа
jwtAxios.interceptors.response.use(
    (config) => onFulfilled(config),
    (error) => onRejected(error)
)

export async function jwtGet(url, params = {}) {
    // console.log(url)
    try {

        const res = await jwtAxios.get(url, params)
        const data = await res.data
        return data

    } catch (error) {
        // console.log(error.response)
        console.log('jwtGet', error.response.data.message)
        debugger
    }

}

export async function jwtPost(url, dataIn = {}, headers = {}) {
    console.log(dataIn)
    try {

        jwtAxios.defaults.headers.common['Content-Type'] = 'multipart/form-data'

        const res = await jwtAxios.post(url, dataIn)
        const data = await res.data

        console.log(res)
        console.log(data)

        return data


    } catch (error) {
        // console.log(error.response)
        console.log('jwtGet', error)
        debugger
    }

}

// export default jwtAxios
