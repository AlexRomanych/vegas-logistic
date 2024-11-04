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

    let currentUser = null
    const registerUser = async function (userDatа) {
        const res = await api.post(register, userData)
        console.log(res)
    }

    // Функционал для авторизации
    const fetchUser = async function (userDatа) {
        try {
            const res = await api.post('login', userDatа)
            if (res.data.success && res.data.data.access_token) {
                localStorage.setItem('token', res.data.data.access_token)

                currentUser = new UserClass({...userDatа, id: res.data.data.id, name: res.data.data.name})

                return currentUser // возвращаем самого пользователя в случае успешного входа
            }
        } catch (e) {
            return new UserClass({id: 0})   // возвращаем в случае отсутствия пользователя

        }





    }


    const user = ref('user')

    return {
        registerUser,
        fetchUser,
        // currentUser
    }
})
