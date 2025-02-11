import {createRouter, createMemoryHistory, createWebHistory} from "vue-router"
import {useUserStore} from "@/src/stores/UserStore"
import routes from "@/src/router/routes"

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
    // console.log('from:', from.name)
    // console.log('to:', to.name)
    //
    // debugger
    const auth = await user.isAuthenticated()
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

        // Здесь определяем заголовки страниц для меню
        if (to.name === 'menu') {
            to.meta.title = menu[parseInt(to.params.groupId) - 1].group.name
        }

        // console.log(to)


        // debugger
        // window.location.reload()
        next()
    }

})


export default router
