import {createRouter, createMemoryHistory, createWebHistory} from "vue-router"
import {useUserStore} from "@/src/stores/UserStore";
// import Help from "../views/Help.vue"
// import Help from "../views/Help.vue"
// import TheAutorization from '../views/auth/TheAutorization.vue'
// import TheLogin from '../views/auth/TheLogin.vue'

// import * from '@/'

// component: () => import('../views/auth/TheAutorization.vue'),
// component: () => import('../views/auth/TheLogin.vue'),

const routes = [


//-----------------------------------------------------------------------
// info Autorization
    {
        path: '/login',
        alias: '/',
        name: 'login',
        component: () => import('@/src/views/auth/TheLogin.vue'),
    },
    // {
    //     path: '/login',
    //     name: 'login',
    //     component: () => import('@/src/views/auth/TheLogin.vue'),
    // },


    {
        path: '/register',
        name: 'register',
        component: () => import('@/src/views/auth/TheRegister.vue'),
    },

//-----------------------------------------------------------------------

//-----------------------------------------------------------------------
// info Dashboard

    {
        path: '/dashboard/start',
        alias: '/dashboard',
        name: 'dashboard',
        component: () => import('@/src/components/dashboard/start_pages/TheStartFacade.vue'),

    },

    {
        path: '/dashboard/menu/:groupId',
        name: 'menu',
        component: () => import('@/src/components/dashboard/nav/NavItemMenu.vue'),

    },

    // {
    //     path: '/models',
    //     name: 'models',
    //     component: () => import('@/src/components/dashboard/TheDashboard.vue'),
    //     meta: {source: 'TheModels'},
    // },
    {
        path: '/models',
        name: 'models',
        component: () => import('@/src/components/dashboard/models/TheModels.vue'),
        meta: {destination: '/models'},
    },


    {
        path: '/users',
        name: 'users',
        component: () => import('@/src/components/dashboard/users/TheUsers.vue'),
    },


    {
        path: '/plan',
        name: 'plan',
        component: () => import('@/src/components/dashboard/users/TheUsers.vue'),
    },


    {path: '/ui', name: 'ui', component: () => import('../components/dashboard/TheMain.vue')},

//-----------------------------------------------------------------------

//-----------------------------------------------------------------------
// info Information

    {path: '/about', name: 'about', component: () => import('../views/About.vue')},
    {path: '/help', name: 'help', component: () => import('../views/Help.vue')},
    // {path: '/help', name: 'help', component: Help},


//-----------------------------------------------------------------------

//-----------------------------------------------------------------------
// info Others (Errors - 404)
    {
        path: '/:pathMatch(.*)*',
        alias: '/error/404',
        name: 'error.404',
        component: () => import('../views/errors/TheNotFound.vue')

    }

]

console.log('i am router')

const router = createRouter({
    history: createWebHistory(),
    routes,

})

// Это правильное иcпользование
router.beforeEach(async (to, from, next) => {
    const user = useUserStore()

    // debugger
    // console.log('from:', from.name)
    // console.log('to:', to.name)

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
        // debugger
        next()
    }


    // if (!(to.name === 'login' || user.isAuthenticated())) {
    //     next({ name: 'login' })
    // } else {
    //     next()
    // }

})


export default router
