import axios from 'axios';
import {useRouter} from "vue-router";

const jwtAxios = axios.create({
    withCredentials: false,
    baseURL: '/api/v1'
})

// Настраиваем конфиг для экземпляра axios
// Добавляем в заголовки токен
const onFulfilled = (config) => {
    const token = localStorage.getItem('token')
    config.headers.Authorization = `Bearer ${token}`
    return config
}

// Добавляем обработчик ошибки
const onRejected = (error) => {
    console.log(error)
    // const router = useRouter()
    // router.push({name: 'login'})

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

export async function jwtGet(url) {
    // console.log(url)

    const res = await jwtAxios.get(url)
    const data = res.data
    // const data = await res
    // console.log(data)
    // console.log(data.models[2])
    return data
}

// export default jwtAxios
