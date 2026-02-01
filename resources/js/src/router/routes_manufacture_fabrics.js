// Info Участок стежки

import {FABRIC_PAGE_MODE} from '@/app/constants/fabrics.js'

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX = '/cell/'
const _TASK_PREFIX = '/task'
const _MAIN_PREFIX = _MANUFACTURE_PREFIX + _CELL_PREFIX

const fabrics = [
    {
        // ___ Основная менюха
        path: _MAIN_PREFIX + 'fabrics',
        name: 'manufacture.cell.fabrics',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsMain.vue'),
        meta: {
            title: 'Стежка'
        }
    },


    {
        // ___ Движение СЗ ПС
        path: _MAIN_PREFIX + 'fabrics/tasks/movement',
        name: 'manufacture.cell.fabric.tasks.movement',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricTasksMovement.vue'),
        meta: {
            title: 'Движение СЗ участка стежки'
        }
    },

    {
        // ___ Управление СЗ ПС (создание, редактирование, удаление)
        path: _MAIN_PREFIX + 'fabrics/tasks/manage',
        name: 'manufacture.cell.fabric.tasks.manage',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricTasksManage.vue'),
        meta: {
            title: 'Управление СЗ участка стежки'
        }
    },

    {
        // ___ Инструмент управления ПС - календарь
        path: _MAIN_PREFIX + 'fabric/tasks/calendar',
        name: 'manufacture.cell.fabric.tasks.calendar',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricTasksCalendar.vue'),
        meta: {
            title: 'Календарь ПС'
        }
    },

    {
        // ___ Выполнение СЗ ПС
        path: _MAIN_PREFIX + 'fabric/tasks/execute',
        name: 'manufacture.cell.fabric.tasks.execute',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricTasksExecute.vue'),
        meta: {
            title: 'Выполнение СЗ участка стежки',
            // mode: FABRIC_PAGE_MODE.EXECUTE,
        }
    },




    {
        // ___ Список ПС
        path: _MAIN_PREFIX + 'fabrics/show',
        name: 'manufacture.cell.fabrics.show',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsShow.vue'),
        meta: {
            title: 'Список ПС'
        }
    },



    {
        // ___ Загрузка с диска списка ПС
        path: _MAIN_PREFIX + 'fabrics/upload',
        name: 'manufacture.cell.fabrics.upload',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsUpload.vue'),
        meta: {
            title: 'Загрузка ПС в БД'
        }
    },

    {
        // ___ Загрузка с диска списка рисунков ПС
        path: _MAIN_PREFIX + 'fabrics/pictures/upload',
        name: 'manufacture.cell.fabrics.pictures.upload',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsPicturesUpload.vue'),
        meta: {
            title: 'Загрузка рисунков ПС в БД'
        }
    },

    // {
    //     // ___ Буфер
    //     path: _MAIN_PREFIX + 'fabrics/buffer',
    //     name: 'manufacture.cell.fabrics.buffer',
    //     component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsBuffer.vue'),
    //     meta: {
    //         title: 'Буфер'
    //     }
    // },

    {
        // ___ Создание нового ПС
        path: _MAIN_PREFIX + 'fabrics/create',
        name: 'manufacture.cell.fabrics.create',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricEditForm.vue'),
        meta: {
            title: 'Создание нового ПС',
            mode: FABRIC_PAGE_MODE.CREATE,
        },
    },

    {
        // ___ Редактирование ПС
        path: '/fabric/edit/:id',
        name: 'manufacture.cell.fabric.edit',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricEditForm.vue'),
        meta: {
            title: 'Редактирование ПС',
            mode: FABRIC_PAGE_MODE.EDIT,
        },
    },

    {
        // ___: Загрузка расхода ПС из 1С из отчета "Сводная ведомость потребности материалов" - СВПМ
        path: '/fabric/expense/upload',
        name: 'manufacture.cell.fabrics.expense.upload',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsExpenseUpload.vue'),
        meta: {
            title: 'Загрузка расхода ПС из отчета 1С - СВПМ',
        },
    },

    {
        // ___: Расход ПС
        path: _MAIN_PREFIX + 'fabrics/expense',
        name: 'manufacture.cell.fabrics.expense',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsExpense.vue'),
        meta: {
            title: 'Расход ПС'
        }
    },

    {
        // ___: Учет ПС
        path: _MAIN_PREFIX + 'fabrics/movement',
        name: 'manufacture.cell.fabrics.movement',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsMovement.vue'),
        meta: {
            title: 'Учет ПС стежки'
        }
    },


    {
        // ___: Добавление нового СЗ ПС
        path: _MAIN_PREFIX + 'fabrics/task/create',
        name: 'manufacture.cell.fabrics.task.create',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricTasksManage.vue'),
        meta: {
            title: 'Создание нового СЗ участка стежки',
            mode: FABRIC_PAGE_MODE.CREATE,
        }
    },

    {
        // ___: Редактирование СЗ ПС
        path: _MAIN_PREFIX + 'fabrics/task/edit/:id',
        name: 'manufacture.cell.fabrics.task.edit',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricTasksManage.vue'),
        meta: {
            title: 'Редактирование СЗ участка стежки',
            mode: FABRIC_PAGE_MODE.EDIT,
        }
    },



    {
        // ___: Список рисунков ПС
        path: _MAIN_PREFIX + 'fabric/pictures/show',
        name: 'manufacture.cell.fabric.pictures.show',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricPicturesShow.vue'),
        meta: {
            title: 'Список рисунков ПС'
        }
    },
    {
        // ___: Добавление нового рисунка ПС
        path: _MAIN_PREFIX + 'fabrics/picture/create',
        name: 'manufacture.cell.fabrics.picture.create',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricPictureEditForm.vue'),
        meta: {
            title: 'Создание нового рисунка ПС',
            mode: FABRIC_PAGE_MODE.CREATE,
        }
    },
    {
        // ___: Редактирование рисунка ПС
        path: _MAIN_PREFIX + 'fabrics/picture/edit/:id',
        name: 'manufacture.cell.fabrics.picture.edit',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricPictureEditForm.vue'),
        meta: {
            title: 'Редактирование рисунка стежки',
            mode: FABRIC_PAGE_MODE.EDIT,
        }
    },




    {
        // ___: Стегальные машины
        path: _MAIN_PREFIX + 'fabrics/machines',
        name: 'manufacture.cell.fabrics.machines',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsMachines.vue'),
        meta: {
            title: 'Стегальные машины'
        }
    },

    {
        // ___ Архив СЗ
        path: _MAIN_PREFIX + 'fabrics/archive',
        name: 'manufacture.cell.fabric.tasks.archive',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsArchive.vue'),
        meta: {
            title: 'Архив СЗ'
        }
    },

    {
        // ___ Архив СЗ
        path: _MAIN_PREFIX + 'fabrics/pictures/tuning/time',
        name: 'manufacture.cell.fabrics.pictures.tuning.time',
        component: () => import('@/components/dashboard/manufacture/cells/fabric/TheFabricsTuningTime.vue'),
        meta: {
            title: 'Время переналадки СМ'
        }
    },




]

export default fabrics
