// info Clients

const clients = [
    {
        path: '/clients/load',
        name: 'clients.load',
        component: () => import('@/components/dashboard/clients/TheClientsLoad.vue'),
        meta: { title: 'Загрузка клиентов из базы' },
    },
    {
        path: '/clients',
        name: 'clients',
        component: () => import('@/components/dashboard/clients/TheClientsShow.vue'),
        meta: { title: 'Клиенты - справочник' },
    },
]

export default clients
