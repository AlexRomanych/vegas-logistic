// info Dashboard

const dashboard = [
    {
        path: '/dashboard/start',
        name: 'dashboard',
        component: () => import('@/src/components/dashboard/start_pages/TheStartFacade.vue'),

    },

    {
        path: '/dashboard/menu/:groupId',
        name: 'menu',
        component: () => import('@/src/components/dashboard/nav/NavItemMenu.vue'),

    },

]

export default dashboard
