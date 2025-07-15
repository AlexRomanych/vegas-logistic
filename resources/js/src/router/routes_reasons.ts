// info Причины

const reasons = [
    {
        path: '/reasons',
        name: 'reasons',
        component: () => import('@/components/reasons/TheReasonsShow.vue'),
        meta: { title: 'Причины' },
    },
    // {
    //     path: '/clients',
    //     name: 'clients',
    //     component: () => import('@/components/dashboard/clients/TheClientsShow.vue'),
    //     meta: { title: 'Клиенты - справочник' },
    // },
]

export default reasons
