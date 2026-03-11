// info Orders

import type { IRouteMeta } from '@/types'

const orders = [
    {   // __ Заявки за период
        path:      '/orders',
        name:      'orders',
        component: () => import('@/components/dashboard/orders/TheOrdersShow.vue'),
        meta:      { title: 'Заявки за период' },
    },

    {   // __ Загрузка заявок в БД
        path:      '/orders/upload',
        name:      'orders.upload',
        component: () => import('@/components/dashboard/orders/TheOrdersUpload.vue'),
        meta:      { title: 'Загрузка Заявок в БД' },
    },

    {   // __ Типы заявок
        path:      '/orders/types',
        name:      'orders.types',
        component: () => import('@/components/dashboard/orders/TheOrdersTypesShow.vue'),
        meta:      { title: 'Типы заявок' },
    },

    {
        // __ Добавление прогнозной Заявки
        path:      '/orders/average/add',
        name:      'orders.average.add',
        component: () => import('@/components/dashboard/orders/TheOrderAverageAdd.vue'),
        meta:      {
                       title: 'Добавление прогнозной Заявки',
                   } as IRouteMeta,
    },

    {
        // __ Карточка Заявки
        path:      '/orders/card/:id',
        name:      'orders.card',
        component: () => import('@/components/dashboard/orders/TheOrderCard.vue'),
        meta:      {
                       title: 'Карточка Заявки',
                   } as IRouteMeta,
    },

]

export default orders
