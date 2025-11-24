// info Планы
import type { IRouteMeta } from '@/types'

const PREFIX = '/plan'

const plans = [
    {
        path: '/plans',                         // __ Стартовая
        name: 'plans',
        component: () => import('@/components/dashboard/plans/ThePlanCommon.vue'),
        meta: {title: 'Производственные планы'} as IRouteMeta,
    },

    {
        path: `${PREFIX}/loads`,              // __ План Отгрузок
        name: 'plan.loads',
        component: () => import('@/components/dashboard/plans/ThePlanLoads.vue'),
        meta: {title: 'План Отгрузок'} as IRouteMeta,
    },
    {
        path: `${PREFIX}/assembly`,             // __ План Сборки
        name: 'plan.assembly',
        component: () => import('@/components/dashboard/plans/ThePlanAssembly.vue'),
        meta: {title: 'План Сборки'} as IRouteMeta,
    },
    {
        path: `${PREFIX}/cutting`,              // __ План Раскроя
        name: 'plan.cutting',
        component: () => import('@/components/dashboard/plans/ThePlanCutting.vue'),
        meta: {title: 'План Раскроя'} as IRouteMeta,
    },
    {
        path: `${PREFIX}/sewing`,               // __ План Швейки
        name: 'plan.sewing',
        component: () => import('@/components/dashboard/plans/ThePlanSewing.vue'),
        meta: {title: 'План Швейки'} as IRouteMeta,
    },
    {
        path: `${PREFIX}/upload`,               // __ Загрузка плана Загрузок из Json
        name: 'plan.upload',
        component: () => import('@/components/dashboard/plans/ThePlanLoadsUpLoad.vue'),
        meta: {title: 'Загрузка плана'} as IRouteMeta,
    },


    // {
    //     path: '/reasons/edit/:id',
    //     name: 'reasons.edit',
    //     component: () => import('@/components/dashboard/reasons/TheReasonEditForm.vue'),
    //     meta: {
    //         title: 'Редактирование причины изменения статуса',
    //         mode: FABRIC_PAGE_MODE.EDIT,
    //     } as IRouteMeta,
    // },
    // {
    //     path: '/reasons/create',
    //     name: 'reasons.create',
    //     component: () => import('@/components/dashboard/reasons/TheReasonEditForm.vue'),
    //     meta: {
    //         title: 'Добавление причины изменения статуса',
    //         mode: FABRIC_PAGE_MODE.CREATE,
    //     } as IRouteMeta,
    // },

]

export default plans
