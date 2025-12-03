// Info Участок швейки

import type { IRouteMeta } from '@/types'

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX = '/cell'
const _TASK_PREFIX = '/task'
const _SEWING_PREFIX = '/sewing'
const _MAIN_PREFIX = _MANUFACTURE_PREFIX + _CELL_PREFIX + _SEWING_PREFIX + '/'

const sewing = [
    {
        // ___ Основная менюха
        path: _MAIN_PREFIX,
        name: 'manufacture.cell.sewing',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingMain.vue'),
        meta: {
            title: 'Швейный цех'
        } as IRouteMeta,
    },

    {
        // ___ Управление планом Швейного цеха
        path: _MAIN_PREFIX + 'plan/manage',
        name: 'manufacture.cell.sewing.plan.manage',
        component: () => import('@/components/dashboard/plans/ThePlanManageSewing.vue'), // Переносим в планы
        // component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingPlanManage.vue'),
        meta: {
            title: 'Управление планом Швейного цеха'
        } as IRouteMeta,
    },
]

export default sewing
