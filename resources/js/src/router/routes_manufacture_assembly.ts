// Info Участок сборки

import type { IRouteMeta } from '@/types'

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX = '/cell'
const _TASK_PREFIX = '/task'
const _ASSEMBLY_PREFIX = '/assembly'
const _MAIN_PREFIX = _MANUFACTURE_PREFIX + _CELL_PREFIX + _ASSEMBLY_PREFIX + '/'

const assembly = [
    {
        // ___ Основная менюха
        path: _MAIN_PREFIX,
        name: 'manufacture.cell.assembly',
        component: () => import('@/components/dashboard/manufacture/cells/assembly/TheAssemblyMain.vue'),
        meta: {
            title: 'Сборочный цех'
        } as IRouteMeta,
    },

    {
        // ___ Управление планом Cборочного цеха
        path: _MAIN_PREFIX + 'plan/manage',
        name: 'manufacture.cell.assembly.plan.manage',
        component: () => import('@/components/dashboard/plans/ThePlanManageAssembly.vue'), // Переносим в планы
        // component: () => import('@/components/dashboard/manufacture/cells/assembly/TheAssemblyPlanManage.vue'),
        meta: {
            title: 'Управление планом Сборочного цеха'
        } as IRouteMeta,
    },
]

export default assembly
