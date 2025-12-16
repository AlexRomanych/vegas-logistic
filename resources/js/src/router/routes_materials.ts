// info Materials

import type { IRouteMeta } from '@/types'

// __ Префикс для всех роутов материалов
const _MATERIALS_PREFIX = '/materials'
const _MAIN_PREFIX = _MATERIALS_PREFIX + '/'

const materials = [
    {
        path: _MATERIALS_PREFIX,                             // __ Список материалов
        name: 'materials',
        component: () => import('@/components/dashboard/materials/TheMaterialsShow.vue'),
        meta: {
            title: 'Материалы - справочник',
        } as IRouteMeta,
    },
    {
        path: '/materials/upload',                            // __ Загрузка / обновление списка материалов
        name: 'materials.upload',
        component: () => import('@/components/dashboard/materials/TheMaterialsUpload.vue'),
        meta: {
            title: 'Обновление списка материалов',
        } as IRouteMeta,
    },


]

export default materials
