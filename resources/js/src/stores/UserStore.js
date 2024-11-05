import {defineStore} from 'pinia'
import axios from 'axios'
import {ref, reactive, computed, watch} from 'vue'
import UserClass from "@/src/app/classes/UserClass.js";

const registerUrl = 'localhost:8000/api/v1/auth/register'
const loginUrl = 'localhost:8000/api/v1/auth/login'

const api = axios.create({
    withCredentials: false,
    baseURL: '/api/v1/auth'
})

export const useUserStore = defineStore('user', () => {

    let currentUser = new UserClass()               // Переменная, хранящая информацию о текущем пользователе
    const registerUser = async function (userDatа) {
        try {
            const res = await api.post('register', userDatа)
            currentUser.status = res.status
            return currentUser
        } catch (e) {
            currentUser.id = 0
            currentUser.status = e.response.status
            return currentUser   // возвращаем пользователя
        }

    }

    // Функционал для авторизации
    const fetchUser = async function (userDatа) {

        try {
            const res = await api.post('login', userDatа)
            console.log(res)
            if (res.data.success && res.data.data.access_token) {
                localStorage.setItem('token', res.data.data.access_token)

                currentUser = new UserClass({
                    ...userDatа,
                    id: res.data.data.id,
                    name: res.data.data.name,
                    status: res.status
                })
                // console.log(currentUser)
                return currentUser
            }
        } catch (e) {
            currentUser.id = 0
            currentUser.status = e.response.status
            return currentUser   // возвращаем пользователя
        }

    }


    // const user = ref('user')

    return {
        registerUser,
        fetchUser,
        currentUser
    }
})
