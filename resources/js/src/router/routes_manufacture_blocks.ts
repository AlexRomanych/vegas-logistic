// Info Участок Блоков пружинных


import type { IRouteMeta } from '@/types'
import { ROUTER_PAGE_MODE } from '@/app/constants/common.ts'

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX        = '/cell'
// const _TASK_PREFIX        = '/task'
const _BLOCKS_PREFIX     = '/blocks'
const _MAIN_PREFIX        = _MANUFACTURE_PREFIX + _CELL_PREFIX + _BLOCKS_PREFIX + '/'

const blocks = [
    {
        // ___ Основная менюха
        path     : _MAIN_PREFIX,
        name     : 'manufacture.cell.blocks',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/TheBlocksMain.vue'),
        meta     : {
            title: 'Производство Пружинных блоков'
        } as IRouteMeta,
    },

    {
        // ___ Справочник Коллекций Блоков
        path     : _MAIN_PREFIX + 'collections/show',
        name     : 'manufacture.cell.blocks.collections.show',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/show/TheBlockCollectionsShow.vue'),
        meta     : {
            title: 'Справочник Групп пружинных блоков'
        } as IRouteMeta,
    },

    {
        // ___ Редактирование Коллекции блоков
        path     : _MAIN_PREFIX + 'collections/edit/:id',
        name     : 'manufacture.cell.blocks.collections.edit',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/show/TheBlockCollectionsEdit.vue'),
        meta     : {
            title: 'Редактирование Группы пружинных блоков',
            mode : ROUTER_PAGE_MODE.EDIT,
        } as IRouteMeta,
    },

    {
        // ___ Создание Коллекции блоков
        path     : _MAIN_PREFIX + 'collections/create',
        name     : 'manufacture.cell.blocks.collections.create',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/show/TheBlockCollectionsEdit.vue'),
        meta     : {
            title: 'Создание Коллекции блоков',
            mode : ROUTER_PAGE_MODE.CREATE,
        } as IRouteMeta,
    },

    {
        // ___ Справочник Блоков
        path     : _MAIN_PREFIX + 'show',
        name     : 'manufacture.cell.blocks.show',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/show/TheBlocksShow.vue'),
        meta     : {
            title: 'Справочник Пружинных блоков'
        } as IRouteMeta,
    },

    {
        // ___ Редактирование блоков
        path     : _MAIN_PREFIX + 'edit/:id',
        name     : 'manufacture.cell.blocks.edit',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/show/TheBlocksEdit.vue'),
        meta     : {
            title: 'Редактирование Пружинного блока',
            mode : ROUTER_PAGE_MODE.EDIT,
        } as IRouteMeta,
    },

    {
        // ___ Создание блоков
        path     : _MAIN_PREFIX + 'create',
        name     : 'manufacture.cell.blocks.create',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/show/TheBlocksEdit.vue'),
        meta     : {
            title: 'Создание Пружинного блока',
            mode : ROUTER_PAGE_MODE.CREATE,
        } as IRouteMeta,
    },

    {
        // ___ Справочник Статусов Движения СЗ
        path     : _MAIN_PREFIX + 'task/statuses',
        name     : 'manufacture.cell.blocks.task.statuses',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/statuses/TheBlockStatusesShow.vue'),
        meta     : {
            title: 'Статусы движения Сменного задания производства Блоков'
        } as IRouteMeta,
    },

    {
        // ___ Управление планом Блоков
        path     : _MAIN_PREFIX + 'plan/manage',
        name     : 'manufacture.cell.blocks.plan.manage',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/blocks_manage/TheBlockManage.vue'),
        meta     : {
            title: 'Управление планом Производства Блоков'
        } as IRouteMeta,
    },

    {
        // ___ Выполнение сменных заданий (Общая)
        path     : _MAIN_PREFIX + 'tasks/execute',
        name     : 'manufacture.cell.blocks.tasks.execute',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/blocks_execute/TheBlockExecute.vue'),
        meta     : {
            title: 'Выполнение сменных заданий участка ПБ',
        } as IRouteMeta,
    },

    {
        // ___ Выполнение сменных заданий (Производственный день)
        path     : _MAIN_PREFIX + 'tasks/execute/day/:date',
        name     : 'manufacture.cell.blocks.tasks.execute.day',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/blocks_execute_day/TheBlockExecuteDay.vue'),
        meta     : {
            title: 'Выполнение сменных заданий участка ПБ: Производственный день',
        } as IRouteMeta,
    },




    //
    // {
    //     // ___ Справочник Типовых операций
    //     path     : _MAIN_PREFIX + 'operations',
    //     name     : 'manufacture.cell.cutting.operations',
    //     component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingOperationsShow.vue'),
    //     meta     : {
    //         title: 'Типовые операции в Раскройном цеху'
    //     } as IRouteMeta,
    // },
    //

    //
    // {
    //     // ___ Справочник Схем Типовых операций
    //     path     : _MAIN_PREFIX + 'operation/schemas',
    //     name     : 'manufacture.cell.cutting.operation.schemas',
    //     component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingOperationSchemasShow.vue'),
    //     meta     : {
    //         title: 'Схемы Типовых операций в Раскройном цеху',
    //     } as IRouteMeta,
    // },
    //
    // {
    //     // ___ Справочник Моделей и Типовых операций (Трудозатраты моделей)
    //     path     : _MAIN_PREFIX + 'operation/models',
    //     name     : 'manufacture.cell.cutting.operation.models',
    //     component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingOperationModelsShow.vue'),
    //     meta     : {
    //         title: 'Трудозатраты моделей для Раскроя',
    //     } as IRouteMeta,
    // },

    // {
    //     // ___ Архив сменных заданий (Общая)
    //     path     : _MAIN_PREFIX + 'tasks/archive',
    //     name     : 'manufacture.cell.cutting.tasks.archive',
    //     component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingArchive.vue'),
    //     meta     : {
    //         title: 'Архив сменных заданий Раскроя',
    //     } as IRouteMeta,
    // },
    //
    // {
    //     // ___ Учет ПС на раскрое
    //     path     : _MAIN_PREFIX + 'fabrics/movement',
    //     name     : 'manufacture.cell.cutting.fabrics.movement',
    //     component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingFabricsMovement.vue'),
    //     meta     : {
    //         title: 'Учет ПС раскроя'
    //     }
    // },
    //
    // {
    //     // ___ Справочник Процедур Раскроя
    //     path     : _MAIN_PREFIX + 'procedures',
    //     name     : 'manufacture.cell.cutting.procedures',
    //     component: () => import('@/components/dashboard/manufacture/cells/cutting/TheCuttingProceduresShow.vue'),
    //     meta     : {
    //         title: 'Процедуры расчета для Раскроя',
    //     } as IRouteMeta,
    // },
    //
    // {
    //     // ___ Создание Процедуры Раскроя
    //     path     : _MAIN_PREFIX + 'procedures/create',
    //     name     : 'manufacture.cell.cutting.procedures.create',
    //     component: () => import('@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_procedures/TheCuttingProcedureEdit.vue'),
    //     meta     : {
    //         title: 'Создание Процедуры расчета для Раскроя',
    //         mode : ROUTER_PAGE_MODE.CREATE,
    //     } as IRouteMeta,
    // },
    //
    // {
    //     // ___ Редактирование Процедуры Раскроя
    //     path     : _MAIN_PREFIX + 'procedures/edit/:id',
    //     name     : 'manufacture.cell.cutting.procedures.edit',
    //     component: () => import('@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_procedures/TheCuttingProcedureEdit.vue'),
    //     meta     : {
    //         title: 'Редактирование Процедуры расчета для Раскроя',
    //         mode : ROUTER_PAGE_MODE.EDIT,
    //     } as IRouteMeta,
    // },



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

    // {
    //     // ___ Печать
    //     path     : _MAIN_PREFIX + 'task/print',
    //     name     : 'manufacture.cell.cutting.task.print',
    //     component: () => import('@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_print/CuttingTaskPrintForm.vue'),
    //     meta     : {
    //         title     : 'Страница печати сменного задания Раскроя',
    //         hideNavbar: true,
    //     } as IRouteMeta,
    // },


    {
        // ___ Тест
        path     : _MAIN_PREFIX + 'test',
        name     : 'manufacture.cell.block.test',
        component: () => import('@/components/dashboard/manufacture/cells/blocks/Test.vue'),
        meta     : {
            title: 'Тестовая страница',
        } as IRouteMeta,
    },

]

export default blocks
