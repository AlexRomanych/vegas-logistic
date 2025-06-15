// info Orders

const orders = [
    {
        path: '/orders',
        name: 'orders',
        component: () => import('@/components/dashboard/orders/TheOrdersShow.vue'),
        meta: { title: 'Заявки за период' },
    },

    {
        path: '/orders/upload',
        name: 'orders.upload',
        component: () => import('@/components/dashboard/orders/TheOrdersUpload.vue'),
        meta: { title: 'Загрузка с диска' },
    },
]

export default orders
