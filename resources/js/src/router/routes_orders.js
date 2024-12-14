// info Orders

const orders = [

    {
        path: '/orders',
        name: 'orders',
        component: () => import('@/src/components/dashboard/orders/TheOrdersShow.vue'),
    },

    {
        path: '/orders/upload',
        name: 'orders.upload',
        component: () => import('@/src/components/dashboard/orders/TheOrdersUpload.vue'),
    },

]

export default orders
