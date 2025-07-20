// info Причины
import { FABRIC_PAGE_MODE } from '@/app/constants/fabrics.js'

import type { IRouteMeta } from '@/types'

const reasons = [
    {
        path: '/reasons',
        name: 'reasons',
        component: () => import('@/components/reasons/TheReasonsShow.vue'),
        meta: {title: 'Причины изменения статуса'} as IRouteMeta,
    },
    {
        path: '/reasons/edit/:id',
        name: 'reasons.edit',
        component: () => import('@/components/reasons/TheReasonEditForm.vue'),
        meta: {
            title: 'Редактирование причины изменения статуса',
            mode: FABRIC_PAGE_MODE.EDIT,
        } as IRouteMeta,
    },
    {
        path: '/reasons/create',
        name: 'reasons.create',
        component: () => import('@/components/reasons/TheReasonEditForm.vue'),
        meta: {
            title: 'Добавление причины изменения статуса',
            mode: FABRIC_PAGE_MODE.CREATE,
        } as IRouteMeta,
    },

]

export default reasons
