import {createRouter, createMemoryHistory, createWebHistory} from "vue-router"
// import Help from "../views/Help.vue"
import Help from "../views/Help.vue"

// import TheAutorization from '../views/auth/TheAutorization.vue'
// import TheLogin from '../views/auth/TheLogin.vue'

// import * from '@/'

// component: () => import('../views/auth/TheAutorization.vue'),
// component: () => import('../views/auth/TheLogin.vue'),

const routes = [
    {path: '/login',
        alias: '/',
        name: 'login',
        component: () => import('@/src/views/auth/TheLogin.vue'),
    },

    {path: '/register',
        name: 'register',
        component: () => import('@/src/views/auth/TheRegister.vue'),
    },


    {path: '/users',
        name: 'users',
        component: () => import('@/src/views/users/TheUsers.vue'),
    },

    {path: '/models',
        name: 'models',
        component: () => import('@/src/views/models/TheModels.vue'),
    },

    {path: '/ui', name: 'ui', component: () => import('../views/TheMain.vue')},

    {path: '/about', name: 'about', component: () => import('../views/About.vue')},
    // {path: '/help', name: 'help', component: () => import('../views/Help.vue')}
    {path: '/help', name: 'help', component: Help},

]

const router = createRouter({
    history: createWebHistory(),
    routes,
})


export default router
