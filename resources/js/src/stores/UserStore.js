import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'
import {useRouter} from "vue-router";
import axios from 'axios'

import UserClass from "@/src/app/classes/UserClass.js";

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
                return currentUser // возвращаем пользователя
            }
        } catch (e) {
            currentUser.id = 0
            currentUser.status = e.response.status
            return currentUser   // возвращаем пользователя
        }

    }

    const getUser = function () {
        return currentUser
    }

    const logout = function () {
        localStorage.removeItem('token')
        const router = useRouter()
        router.push({name: 'Login'})
    }

    return {
        registerUser,
        fetchUser,
        logout,
        getUser,

    }
})
