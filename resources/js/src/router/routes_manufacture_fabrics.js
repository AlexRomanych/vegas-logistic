// Info Участок стежки

import {FABRIC_PAGE_MODE} from '/resources/js/src/app/constants/fabrics.js'

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX = '/cell/'
const _TASK_PREFIX = '/task'
const _MAIN_PREFIX = _MANUFACTURE_PREFIX + _CELL_PREFIX

const fabrics = [{
    // Основная менюха
    path: _MAIN_PREFIX + 'fabrics',
    name: 'manufacture.cell.fabrics',
    component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsMain.vue'),
    meta: {
        title: 'Стежка'
    }
},

    {
        // descr Инструмент управления ПС
        path: _MAIN_PREFIX + 'fabrics/manage',
        name: 'manufacture.cell.fabrics.manage',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsManage.vue'),
        meta: {
            title: 'Управление ПС'
        }
    },

    {
        // descr Список ПС
        path: _MAIN_PREFIX + 'fabrics/show',
        name: 'manufacture.cell.fabrics.show',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsShow.vue'),
        meta: {
            title: 'Список ПС'
        }
    },

    {
        // descr Загрузка с диска списка ПС
        path: _MAIN_PREFIX + 'fabrics/upload',
        name: 'manufacture.cell.fabrics.upload',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsUpload.vue'),
        meta: {
            title: 'Загрузка ПС в БД'
        }
    },

    {
        // descr Загрузка с диска списка рисунков ПС
        path: _MAIN_PREFIX + 'fabrics/pictures/upload',
        name: 'manufacture.cell.fabrics.pictures.upload',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsPicturesUpload.vue'),
        meta: {
            title: 'Загрузка ПС в БД'
        }
    },

    {
        // descr Буфер
        path: _MAIN_PREFIX + 'fabrics/buffer',
        name: 'manufacture.cell.fabrics.buffer',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsBuffer.vue'),
        meta: {
            title: 'Буфер'
        }
    },

    {
        // descr Добавление ПС
        path: _MAIN_PREFIX + 'fabrics/add',
        name: 'manufacture.cell.fabrics.add',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricEditForm.vue'),
        meta: {
            title: 'Добавление ПС',
            mode: FABRIC_PAGE_MODE.ADD,
        },
    },

    {
        // descr Редактирование ПС
        path: '/fabric/edit/:id',
        name: 'manufacture.cell.fabric.edit',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricEditForm.vue'),
        meta: {
            title: 'Редактирование ПС',
            mode: FABRIC_PAGE_MODE.EDIT,
        },
    },

    {
        // descr Загрузка расхода ПС из 1С из отчета "Сводная ведомость потребности материалов" - СВПМ
        path: '/fabric/expense/upload',
        name: 'manufacture.cell.fabrics.expense.upload',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsExpenseUpload.vue'),
        meta: {
            title: 'Загрузка расхода ПС из отчета 1С - СВПМ',
        },
    },

    {
        // descr Расход ПС
        path: _MAIN_PREFIX + 'fabrics/expense',
        name: 'manufacture.cell.fabrics.expense',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsExpensive.vue'),
        meta: {
            title: 'Расход ПС'
        }
    },

    {
        // descr Учет ПС
        path: _MAIN_PREFIX + 'fabrics/movement',
        name: 'manufacture.cell.fabrics.movement',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsMovement.vue'),
        meta: {
            title: 'Учет ПС'
        }
    },

    {
        // descr Управление СЗ ПС (создание, редактирование, удаление)
        path: _MAIN_PREFIX + 'fabrics/task/manage',
        name: 'manufacture.cell.fabrics.task.manage',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTaskManage.vue'),
        meta: {
            title: 'Управление СЗ участка стежки'
        }
    },

    {
        // descr Добавление нового СЗ ПС
        path: _MAIN_PREFIX + 'fabrics/task/add',
        name: 'manufacture.cell.fabrics.task.add',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTaskDetails.vue'),
        meta: {
            title: 'Создание нового СЗ участка стежки',
            mode: FABRIC_PAGE_MODE.ADD,
        }
    },

    {
        // descr Редактирование СЗ ПС
        path: _MAIN_PREFIX + 'fabrics/task/edit/:id',
        name: 'manufacture.cell.fabrics.task.add',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTaskDetails.vue'),
        meta: {
            title: 'Редактирование СЗ участка стежки',
            mode: FABRIC_PAGE_MODE.EDIT,
        }
    },

    {
        // descr Выполнение СЗ ПС
        path: _MAIN_PREFIX + 'fabrics/task/execute',
        name: 'manufacture.cell.fabrics.task.execute',
        // component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTaskExecute.vue'),
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTaskDetails.vue'),
        meta: {
            title: 'Выполнение СЗ участка стежки',
            mode: FABRIC_PAGE_MODE.EXECUTE,
        }
    },
]

export default fabrics
