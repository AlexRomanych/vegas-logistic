// Info Участок раскороя

// Info Участок швейки

import type { IRouteMeta } from '@/types'
import { ROUTER_PAGE_MODE } from '@/app/constants/common.ts'

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX        = '/cell'
// const _TASK_PREFIX        = '/task'
const _CUTTING_PREFIX     = '/cutting'
const _MAIN_PREFIX        = _MANUFACTURE_PREFIX + _CELL_PREFIX + _CUTTING_PREFIX + '/'

const cutting = [
    {
        // ___ Основная менюха
        path     : _MAIN_PREFIX,
        name     : 'manufacture.cell.cutting',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingMain.vue'),
        meta     : {
            title: 'Раскройный цех'
        } as IRouteMeta,
    },

    {
        // ___ Управление планом Швейного цеха
        path     : _MAIN_PREFIX + 'plan/manage',
        name     : 'manufacture.cell.cutting.plan.manage',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingManage.vue'),
        meta     : {
            title: 'Управление планом Раскройного цеха'
        } as IRouteMeta,
    },

    {
        // ___ Справочник Статусов Движения СЗ
        path     : _MAIN_PREFIX + 'task/statuses',
        name     : 'manufacture.cell.cutting.task.statuses',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingStatusesShow.vue'),
        meta     : {
            title: 'Статусы движения Сменного задания Раскроя'
        } as IRouteMeta,
    },

    {
        // ___ Справочник Типовых операций
        path     : _MAIN_PREFIX + 'operations',
        name     : 'manufacture.cell.cutting.operations',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingOperationsShow.vue'),
        meta     : {
            title: 'Типовые операции в Раскройном цеху'
        } as IRouteMeta,
    },

    {
        // ___ Редактирование Типовой операции
        path     : _MAIN_PREFIX + 'operations/edit/:id',
        name     : 'manufacture.cell.cutting.operations.edit',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingOperationEdit.vue'),
        meta     : {
            title: 'Редактирование Типовой операции Раскроя',
            mode : ROUTER_PAGE_MODE.EDIT,
        } as IRouteMeta,
    },

    {
        // ___ Создание Типовой операции
        path     : _MAIN_PREFIX + 'operations/create',
        name     : 'manufacture.cell.cutting.operations.create',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingOperationEdit.vue'),
        meta     : {
            title: 'Создание Типовой операции Раскроя',
            mode : ROUTER_PAGE_MODE.CREATE,
        } as IRouteMeta,
    },

    {
        // ___ Справочник Схем Типовых операций
        path     : _MAIN_PREFIX + 'operation/schemas',
        name     : 'manufacture.cell.cutting.operation.schemas',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingOperationSchemasShow.vue'),
        meta     : {
            title: 'Схемы Типовых операций в Раскройном цеху',
        } as IRouteMeta,
    },

    {
        // ___ Справочник Моделей и Типовых операций (Трудозатраты моделей)
        path     : _MAIN_PREFIX + 'operation/models',
        name     : 'manufacture.cell.cutting.operation.models',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingOperationModelsShow.vue'),
        meta     : {
            title: 'Трудозатраты моделей для Раскроя',
        } as IRouteMeta,
    },

    {
        // ___ Выполнение сменных заданий (Общая)
        path     : _MAIN_PREFIX + 'tasks/execute',
        name     : 'manufacture.cell.cutting.tasks.execute',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingExecute.vue'),
        meta     : {
            title: 'Выполнение сменных заданий Раскроя',
        } as IRouteMeta,
    },

    {
        // ___ Выполнение сменных заданий (Производственный день)
        path     : _MAIN_PREFIX + 'tasks/execute/day/:date',
        name     : 'manufacture.cell.cutting.tasks.execute.day',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingExecuteDay.vue'),
        meta     : {
            title: 'Выполнение СЗ Раскроя: Производственный день',
        } as IRouteMeta,
    },

    {
        // ___ Архив сменных заданий (Общая)
        path     : _MAIN_PREFIX + 'tasks/archive',
        name     : 'manufacture.cell.cutting.tasks.archive',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingArchive.vue'),
        meta     : {
            title: 'Архив сменных заданий Раскроя',
        } as IRouteMeta,
    },

    {
        // ___ Учет ПС на раскрое
        path     : _MAIN_PREFIX + 'fabrics/movement',
        name     : 'manufacture.cell.cutting.fabrics.movement',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingFabricsMovement.vue'),
        meta     : {
            title: 'Учет ПС раскроя'
        }
    },

    {
        // ___ Справочник Процедур Раскроя
        path     : _MAIN_PREFIX + 'procedures',
        name     : 'manufacture.cell.cutting.procedures',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingProceduresShow.vue'),
        meta     : {
            title: 'Процедуры расчета для Раскроя',
        } as IRouteMeta,
    },

    {
        // ___ Редактирование Процедуры Раскроя
        path     : _MAIN_PREFIX + 'procedures/edit/:id',
        name     : 'manufacture.cell.procedure.edit',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_procedures/TheCuttingProcedureEdit.vue'),
        meta     : {
            title: 'Редактирование Процедуры расчета для Раскроя',
            mode : ROUTER_PAGE_MODE.EDIT,
        } as IRouteMeta,
    },

    //
    // {
    //     // ___ Печать
    //     path     : _MAIN_PREFIX + 'task/print',
    //     name     : 'task.print',
    //     component: () => import('@/components/layouts/PrintLayout.vue'),
    //     children : [
    //         {
    //             path     : _MAIN_PREFIX + 'task/print',
    //             // name     : 'manufacture.cell.cutting.task.print',
    //             component: () => import('@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_print/CuttingTaskPrintForm.vue'),
    //             meta     : {
    //                 title: 'Страница печати сменного задания Пошива',
    //             } as IRouteMeta,
    //         },
    //     ],
    //     meta     : {
    //         title: 'Страница печати сменного задания Пошива',
    //     } as IRouteMeta,
    // },

    {
        // ___ Печать
        path     : _MAIN_PREFIX + 'task/print',
        name     : 'manufacture.cell.cutting.task.print',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_print/CuttingTaskPrintForm.vue'),
        meta     : {
            title     : 'Страница печати сменного задания Раскроя',
            hideNavbar: true,
        } as IRouteMeta,
    },


    {
        // ___ Тест
        path     : _MAIN_PREFIX + 'test',
        name     : 'manufacture.cell.cutting.test',
        component: () => import('@/components/dashboard/manufacture/cells/cutting/Test.vue'),
        meta     : {
            title: 'Тестовая страница',
        } as IRouteMeta,
    },

]

export default cutting
