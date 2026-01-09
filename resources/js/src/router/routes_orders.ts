// info Orders

const orders = [
    {   // __ Заявки за период
        path: '/orders',
        name: 'orders',
        component: () => import('@/components/dashboard/orders/TheOrdersShow.vue'),
        meta: { title: 'Заявки за период' },
    },

    {   // __ Загрузка заявок в БД
        path: '/orders/upload',
        name: 'orders.upload',
        component: () => import('@/components/dashboard/orders/TheOrdersUpload.vue'),
        meta: { title: 'Загрузка Заявок в БД' },
    },

    {   // __ Типы заявок
        path: '/orders/types',
        name: 'orders.types',
        component: () => import('@/components/dashboard/orders/TheOrdersTypesShow.vue'),
        meta: { title: 'Типы заявок' },
    },

]

export default orders
