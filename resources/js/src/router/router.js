import {createRouter, createMemoryHistory} from "vue-router"
// import Help from "../views/Help.vue"
import Help from "../views/Help.vue"

// import * from '@/'

const routes = [
    {path: '/', name: 'home', component: () => import('../views/TheMain.vue')},
    {path: '/about', name: 'about', component: () => import('../views/About.vue')},
    // {path: '/help', name: 'help', component: () => import('../views/Help.vue')}
    {path: '/help', name: 'help', component: Help},

]

const router = createRouter({
    history: createMemoryHistory(),
    routes,
})

export default router
