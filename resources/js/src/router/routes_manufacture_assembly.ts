// Info Участок сборки

import type { IRouteMeta } from '@/types'
import { ROUTER_PAGE_MODE } from '@/app/constants/common.ts'

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX = '/cell'
const _ASSEMBLY_PREFIX = '/assembly'
const _MAIN_PREFIX = _MANUFACTURE_PREFIX + _CELL_PREFIX + _ASSEMBLY_PREFIX + '/'
// const _TASK_PREFIX = '/task'

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
        // ___ Группы модели для сортировки
        path: _MAIN_PREFIX + 'model/manufacture/groups/show',
        name: 'manufacture.cell.assembly.model.manufacture.groups.show',
        component: () => import('@/components/dashboard/manufacture/cells/assembly/assembly_models_manufacture_groups/TheAssemblyManufactureGroupsShow.vue'),
        meta: {
            title: 'Группы моделей для сортировки'
        } as IRouteMeta,
    },

    {
        // ___ Создание Группы моделей для сортировки
        path: _MAIN_PREFIX + 'model/manufacture/groups/create',
        name: 'manufacture.cell.assembly.model.manufacture.groups.create',
        component: () => import('@/components/dashboard/manufacture/cells/assembly/assembly_models_manufacture_groups/TheAssemblyManufactureGroupEdit.vue'),
        meta: {
            title: 'Создание Группы моделей для сортировки',
            mode : ROUTER_PAGE_MODE.CREATE,
        } as IRouteMeta,
    },

    {
        // ___ Редактирование Группы моделей для сортировки
        path: _MAIN_PREFIX + 'model/manufacture/groups/edit/:id',
        name: 'manufacture.cell.assembly.model.manufacture.groups.edit',
        component: () => import('@/components/dashboard/manufacture/cells/assembly/assembly_models_manufacture_groups/TheAssemblyManufactureGroupEdit.vue'),
        meta: {
            title: 'Редактирование Группы моделей для сортировки',
            mode : ROUTER_PAGE_MODE.EDIT,
        } as IRouteMeta,
    },


    {
        // ___ Управление планом Cборочного цеха - Календарь
        path: _MAIN_PREFIX + 'plan/manage',
        name: 'manufacture.cell.assembly.plan.manage',
        component: () => import('@/components/dashboard/manufacture/cells/assembly/assembly_manage/TheAssemblyManage.vue'),
        // component: () => import('@/components/dashboard/plans/ThePlanManageAssembly.vue'), // Переносим в планы
        meta: {
            title: 'Управление планом Сборочного цеха - Календарь'
        } as IRouteMeta,
    },


    {
        // ___ Управление планом Cборочного цеха - Список
        path: _MAIN_PREFIX + 'plan/manipulate',
        name: 'manufacture.cell.assembly.plan.manipulate',
        component: () => import('@/components/dashboard/manufacture/cells/assembly/assembly_manipulate/TheAssemblyManipulate.vue'),
        meta: {
            title: 'Управление планом Сборочного цеха - Список'
        } as IRouteMeta,
    },

    {
        // ___ Фиксация действий Участков Cборочного цеха - День производства
        path: _MAIN_PREFIX + 'manipulate/day/:date',
        name: 'manufacture.cell.assembly.manipulate.day',
        component: () => import('@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/TheAssemblyManipulateDay.vue'),
        meta: {
            title: 'Сменные Задания участков Сборки'
        } as IRouteMeta,
    },

    {
        // ___ Тест
        path     : _MAIN_PREFIX + 'test',
        name     : 'manufacture.cell.assembly.test',
        component: () => import('@/components/dashboard/manufacture/cells/assembly/Test.vue'),
        meta     : {
            title: 'Тестовая страница',
        } as IRouteMeta,
    },


]

export default assembly
