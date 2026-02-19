// Info Участок швейки

import type { IRouteMeta } from '@/types'
import { ROUTER_PAGE_MODE } from '@/app/constants/common.ts'

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX        = '/cell'
const _TASK_PREFIX        = '/task'
const _SEWING_PREFIX      = '/sewing'
const _MAIN_PREFIX        = _MANUFACTURE_PREFIX + _CELL_PREFIX + _SEWING_PREFIX + '/'

const sewing = [
    {
        // ___ Основная менюха
        path:      _MAIN_PREFIX,
        name:      'manufacture.cell.sewing',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingMain.vue'),
        meta:      {
                       title: 'Швейный цех'
                   } as IRouteMeta,
    },

    {
        // ___ Управление планом Швейного цеха
        path:      _MAIN_PREFIX + 'plan/manage',
        name:      'manufacture.cell.sewing.plan.manage',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingManage.vue'),
        meta:      {
                       title: 'Управление планом Швейного цеха'
                   } as IRouteMeta,
    },

    {
        // ___ Справочник Статусов Движения СЗ
        path:      _MAIN_PREFIX + 'task/statuses',
        name:      'manufacture.cell.sewing.task.statuses',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingStatusesShow.vue'),
        meta:      {
                       title: 'Статусы движения Сменного задания Пошива'
                   } as IRouteMeta,
    },

    {
        // ___ Справочник Типовых операций
        path:      _MAIN_PREFIX + 'operations',
        name:      'manufacture.cell.sewing.operations',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingOperationsShow.vue'),
        meta:      {
                       title: 'Типовые операции в Швейном цеху'
                   } as IRouteMeta,
    },

    {
        // ___ Редактирование Типовой операции
        path:      _MAIN_PREFIX + 'operations/edit/:id',
        name:      'manufacture.cell.sewing.operations.edit',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingOperationEdit.vue'),
        meta:      {
                       title: 'Редактирование Типовой операции',
                       mode:  ROUTER_PAGE_MODE.EDIT,
                   } as IRouteMeta,
    },

    {
        // ___ Создание Типовой операции
        path:      _MAIN_PREFIX + 'operations/create',
        name:      'manufacture.cell.sewing.operations.create',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingOperationEdit.vue'),
        meta:      {
                       title: 'Создание Типовой операции',
                       mode:  ROUTER_PAGE_MODE.CREATE,
                   } as IRouteMeta,
    },

    {
        // ___ Справочник Схем Типовых операций
        path:      _MAIN_PREFIX + 'operation/schemas',
        name:      'manufacture.cell.sewing.operation.schemas',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingOperationSchemasShow.vue'),
        meta:      {
                       title: 'Схемы Типовых операций в Швейном цеху',
                   } as IRouteMeta,
    },

    {
        // ___ Справочник Моделей и Типовых операций (Трудозатраты моделей)
        path:      _MAIN_PREFIX + 'operation/models',
        name:      'manufacture.cell.sewing.operation.models',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingOperationModelsShow.vue'),
        meta:      {
                       title: 'Трудозатраты моделей',
                   } as IRouteMeta,
    },

    {
        // ___ Выполнение сменных заданий (Общая)
        path:      _MAIN_PREFIX + 'tasks/execute',
        name:      'manufacture.cell.sewing.tasks.execute',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingExecute.vue'),
        meta:      {
                       title: 'Выполнение сменных заданий Пошива',
                   } as IRouteMeta,
    },

    {
        // ___ Выполнение сменных заданий (Производственный день)
        path:      _MAIN_PREFIX + 'tasks/execute/day/:date',
        name:      'manufacture.cell.sewing.tasks.execute.day',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/TheSewingExecuteDay.vue'),
        meta:      {
                       title: 'Выполнение СЗ Пошива: Производственный день',
                   } as IRouteMeta,
    },


    {
        // ___ Тест
        path:      _MAIN_PREFIX + 'test',
        name:      'manufacture.cell.sewing.test',
        component: () => import('@/components/dashboard/manufacture/cells/sewing/Test.vue'),
        meta:      {
                       title: 'Тестовая страница',
                   } as IRouteMeta,
    },



]


export default sewing
