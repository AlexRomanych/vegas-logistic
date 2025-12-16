// info Models

import type { IRouteMeta } from '@/types'

// __ Префикс для всех роутов моделей
const _MODELS_PREFIX = '/models'
const _MAIN_PREFIX = _MODELS_PREFIX + '/'

const models = [
    {
        path: '/models',                                    // __ Список моделей
        name: 'models',
        component: () => import('@/components/dashboard/models/TheModelsShow.vue'),
        meta: {
            title: 'Модели - справочник',
            destination: '/models',
        } as IRouteMeta,
    },
    {
        path: '/models/upload',                             // __ Загрузка / обновление списка моделей
        name: 'models.upload',
        component: () => import('@/components/dashboard/models/TheModelsUpload.vue'),
        meta: {
            title: 'Обновление списка моделей',
        } as IRouteMeta,
    },

    {
        path: '/procedures',                                    // __ Список процедур расчета
        name: 'procedures',
        component: () => import('@/components/dashboard/models/TheProceduresShow.vue'),
        meta: {
            title: 'Процедуры расчета - справочник',
        } as IRouteMeta,
    },
    {
        path: '/models/procedures/upload',                  // __ Загрузка / обновление списка процедур расчета
        name: 'procedures.upload',
        component: () => import('@/components/dashboard/models/TheProceduresUpload.vue'),
        meta: {
            title: 'Обновление списка процедур расчета',
        } as IRouteMeta,
    },

    {
        path: '/models/load',
        name: 'models.load',
        component: () => import('@/components/dashboard/models/TheModelsLoad.vue'),
    },
]

export default models
