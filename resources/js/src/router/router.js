import {createRouter, createMemoryHistory, createWebHistory} from "vue-router"
import {useUserStore} from "/resources/js/src/stores/UserStore"
import routes from "/resources/js/src/router/routes"

import menu from '/resources/js/src/assets/menu.js'

// import Help from "../views/Help.vue"
// import Help from "../views/Help.vue"
// import TheAutorization from '../views/auth/TheAutorization.vue'
// import TheLogin from '../views/auth/TheLogin.vue'

// import * from '@/'

// component: () => import('../views/auth/TheAutorization.vue'),
// component: () => import('../views/auth/TheLogin.vue'),


// console.log('i am router')

const router = createRouter({
    history: createWebHistory(),
    routes,

})

// Это правильное иcпользование, отдельным операндом,
// так как createRouter() создает router, а возвращает не его экземпляр
router.beforeEach(async (to, from, next) => {
    const user = useUserStore()

    // debugger
    console.log('from:', from.name)
    console.dir(from)
    console.log('to:', to.name)

    //
    // debugger
    let auth = await user.isAuthenticated()
    // console.log(auth)
    // debugger

    // debugger

    // if (!user.isAuthenticated()) {
    if (!auth) {

        if (to.name === 'register' || to.name === 'login' || to.name === 'error.404') {
            next()
        } else {
            next({name: 'login'})
        }

    } else {

        // если пользователь уже в системе и он зашел на страницу регистрации или авторизации, то выходим из системы
        if (to.name === 'register' || to.name === 'login') {

            next({name: 'dashboard'})
            // console.log('logout', from.name)

            // todo Критически важное место! Logout - разобраться!!!

            // user.logout()
            // auth = false
            // next({name: 'to.name'})

        } else if (to.name === 'menu') {

            console.log('menu')

            // Здесь определяем заголовки страниц для меню
            to.meta.title = menu[parseInt(to.params.groupId) - 1].group.name
            next()

        } else {

            next()
        }

        // console.log(to)


        // debugger
        // window.location.reload()
        // next()
    }

})


export default router
