// info Clients

import { FABRIC_PAGE_MODE } from '@/app/constants/fabrics.ts'
import type { IRouteMeta } from '@/types'

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
        meta: { title: 'Клиенты' },
    },
    {
        path: '/clients/create',
        name: 'clients.create',
        component: () => import('@/components/dashboard/clients/TheClientsEdit.vue'),
        meta: {
            title: 'Добавление клиента',
            mode: FABRIC_PAGE_MODE.CREATE,
        } as IRouteMeta,
    },
    {
        path: '/clients/edit/:id',
        name: 'clients.edit',
        component: () => import('@/components/dashboard/clients/TheClientsEdit.vue'),
        meta: {
            title: 'Редактирование клиента',
            mode: FABRIC_PAGE_MODE.EDIT,
        } as IRouteMeta,
    },


]

export default clients
