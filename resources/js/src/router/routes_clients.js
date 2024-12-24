// info Clients

const clients = [

    {
        path: '/clients/load',
        name: 'clients.load',
        component: () => import('@/src/components/dashboard/clients/TheClientsLoad.vue'),
    },
    {
        path: '/clients',
        name: 'clients',
        component: () => import('@/src/components/dashboard/clients/TheClientsShow.vue'),
    },

]

export default clients
