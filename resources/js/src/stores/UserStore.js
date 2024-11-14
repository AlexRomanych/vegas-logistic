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

    const errorApi = function (err) {
        currentUser.status = err.response.status      // возвращаем статус для отображения ошибки
        currentUser.id = 0
    }
    const registerUser = async function (userDatа) {
        try {
            const res = await api.post('register', userDatа)
            currentUser.status = res.status
            return currentUser
        } catch (e) {
            errorApi(e)                                 // обрабатываем ошибку
            return currentUser                          // возвращаем пользователя
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

                // debugger
                return currentUser // возвращаем пользователя
            }

        } catch (e) {

            errorApi(e)                                 // обрабатываем ошибку
            return currentUser                          // возвращаем пользователя

        }

    }

    // Возвращаем текущего пользователя
    const getUser = async function () {
        // // заглушка
        //     const tempUser = new UserClass({
        //         id: 100,
        //         name: 'test',
        //         status: 200,
        //         email: 'test@test.com'
        //     })
        //     currentUser = tempUser                  // warning todo Убрать!!!


        if (!currentUser.id) {

            // debugger

            try {

                const profile = await api.post('profile', {}, {
                    headers: {
                        'authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                })

                // debugger
                // console.log(profile)


                if (profile.data.success && profile.data.data.id != 0) {
                    currentUser = new UserClass({
                        id: profile.data.data.id,
                        email: profile.data.data.email,
                        name: profile.data.data.name,
                        status: profile.status
                    })
                    return currentUser // возвращаем пользователя
                }

            } catch (e) {

                // debugger




                errorApi(e)                                 // обрабатываем ошибку
                return currentUser                          // возвращаем пользователя

            }

        }

        // console.log(currentUser)

        return currentUser
    }

    // Возвращаем статус авторизации
    const isAuthenticated = async function () {
        currentUser = await getUser()
        // console.log(currentUser)
        // return true                             // warning todo Убрать!!!

        // if (!currentUser.id) {
        //     const router = useRouter()
        //     console.log(router)
        //     // router.push({name: 'login'})
        // }
        return !!currentUser.id
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
        isAuthenticated,

    }
})
