// info Models

import type { IRouteMeta } from '@/types'

// __ Префикс для всех роутов моделей
const _MODELS_PREFIX = '/models'
// const _MAIN_PREFIX = _MODELS_PREFIX + '/'

const models = [
    {
        // __ Список моделей
        path: '/models',
        name: 'models',
        component: () => import('@/components/dashboard/models/TheModelsShow.vue'),
        meta: {
            title: 'Модели - справочник',
            destination: '/models',
        } as IRouteMeta,
    },
    {
        // __ Загрузка / обновление списка моделей
        path: '/models/upload',
        name: 'models.upload',
        component: () => import('@/components/dashboard/models/TheModelsUpload.vue'),
        meta: {
            title: 'Обновление списка моделей',
        } as IRouteMeta,
    },
    {
        // __ Список процедур расчета
        path: '/models/procedures',
        name: 'procedures',
        component: () => import('@/components/dashboard/models/TheProceduresShow.vue'),
        meta: {
            title: 'Процедуры расчета - справочник',
        } as IRouteMeta,
    },
    {
        // __ Загрузка / обновление списка процедур расчета
        path: '/models/procedures/upload',
        name: 'procedures.upload',
        component: () => import('@/components/dashboard/models/TheProceduresUpload.vue'),
        meta: {
            title: 'Обновление списка процедур расчета',
        } as IRouteMeta,
    },
    {
        // __ Список спецификаций
        path: '/models/constructs',
        name: 'constructs',
        component: () => import('@/components/dashboard/models/TheConstructsShow.vue'),
        meta: {
            title: 'Спецификации изделий - справочник',
        } as IRouteMeta,
    },
    {
        // __ Добавление тендерных спецификаций
        path: '/models/constructs/add',
        name: 'constructs.add',
        component: () => import('@/components/dashboard/models/TheConstructsAdd.vue'),
        meta: {
            title: 'Добавление нестандартных (тендерных) Спецификаций',
        } as IRouteMeta,
    },
    {
        // __ Загрузка / обновление списка процедур расчета
        path: '/models/constructs/upload',
        name: 'constructs.upload',
        component: () => import('@/components/dashboard/models/TheConstructsUpload.vue'),
        meta: {
            title: 'Обновление спецификаций изделий',
        } as IRouteMeta,
    },
    {
        // __ Обновление баз: Модели, Процедуры, Спецификации, Материалы
        path: '/models/references/update',
        name: 'models.references.update',
        component: () => import('@/components/dashboard/models/TheReferencesUpdate.vue'),
        meta: {
            title: 'Обновление справочников моделей',
        } as IRouteMeta,
    },




    {
        path: '/models/load',
        name: 'models.load',
        component: () => import('@/components/dashboard/models/TheModelsLoad.vue'),
    },
]

export default models
