// info Workers

import { ROUTER_PAGE_MODE } from '@/app/constants/common.ts'
import type { IRouteMeta } from '@/types'

const workers = [
    {
        path:      '/workers',
        name:      'workers',
        component: () => import('@/components/dashboard/workers/TheWorkersShow.vue'),
        meta:      {
                       title: 'Список сотрудников',
                   } as IRouteMeta,
    },
    {
        path:      '/workers/create',
        name:      'worker.create',
        component: () => import('@/components/dashboard/workers/TheWorkerEditForm.vue'),
        meta:      {
                       title: 'Добавление сотрудника',
                       mode:  ROUTER_PAGE_MODE.CREATE,
                   } as IRouteMeta,
    },
    {
        path:      '/worker/edit/:id',
        name:      'worker.edit',
        component: () => import('@/components/dashboard/workers/TheWorkerEditForm.vue'),
        meta:      {
                       title: 'Редактирование сотрудника',
                       mode:  ROUTER_PAGE_MODE.EDIT,
                   } as IRouteMeta,
    }

]

export default workers
