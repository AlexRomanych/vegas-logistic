// info Бизнес процессы
import type { IRouteMeta } from '@/types'

const PREFIX = '/business-processes'

const processes = [
    {
        path: PREFIX,                         // __ Стартовая
        name: 'business.processes',
        component: () => import('@/components/dashboard/processes/TheProcessesCommon.vue'),
        meta: {title: 'Бизнес-процессы'} as IRouteMeta,
    },

    {
        path: `${PREFIX}/list`,              // __ Список Бизнес-процессов
        name: 'business.processes.list',
        component: () => import('@/components/dashboard/processes/TheProcessesList.vue'),
        meta: {title: 'Список Бизнес-процессов'} as IRouteMeta,
    },
    // {
    //     path: `${PREFIX}/assembly`,             // __ План Сборки
    //     name: 'plan.assembly',
    //     component: () => import('@/components/dashboard/plans/ThePlanAssembly.vue'),
    //     meta: {title: 'План Сборки'} as IRouteMeta,
    // },
    // {
    //     path: `${PREFIX}/cutting`,              // __ План Раскроя
    //     name: 'plan.cutting',
    //     component: () => import('@/components/dashboard/plans/ThePlanCutting.vue'),
    //     meta: {title: 'План Раскроя'} as IRouteMeta,
    // },
    // {
    //     path: `${PREFIX}/sewing`,               // __ План Швейки
    //     name: 'plan.sewing',
    //     component: () => import('@/components/dashboard/plans/ThePlanSewing.vue'),
    //     meta: {title: 'План Швейки'} as IRouteMeta,
    // },
    // {
    //     path: `${PREFIX}/upload`,               // __ Загрузка плана Загрузок из Json
    //     name: 'plan.upload',
    //     component: () => import('@/components/dashboard/plans/ThePlanLoadsUpLoad.vue'),
    //     meta: {title: 'Загрузка плана'} as IRouteMeta,
    // },


]

export default processes
