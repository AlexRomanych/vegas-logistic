// info Dashboard
import menu from '/resources/js/src/assets/menu.js'

// console.log(menu)

const dashboard = [
    {
        path: '/dashboard/start',
        name: 'dashboard',
        component: () => import('@/components/dashboard/start_pages/TheStartFacade.vue'),
        meta: { title: 'ИС управления производством' },
    },

    // {
    //     path: '/dashboard/menu/:groupId',
    //     name: 'menu',
    //     component: () => import('@/components/dashboard/nav/NavItemMenu.vue'),
    //     meta: (route) => ({title: route.params.groupId})
    //
    // },

    {
        // path: '/dashboard/menu/:groupId/:id?',
        path: '/dashboard/menu/:groupId',
        name: 'menu',
        component: () => import('@/components/dashboard/nav/NavItemMenu.vue'),
        meta: { title: 'Меню' },
    },
]

export default dashboard
