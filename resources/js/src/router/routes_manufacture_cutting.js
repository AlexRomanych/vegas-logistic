// Info Участок раскороя

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX = '/cell'
const _TASK_PREFIX = '/task'
const _CUTTING_PREFIX = '/cutting'
const _MAIN_PREFIX = _MANUFACTURE_PREFIX + _CELL_PREFIX + _CUTTING_PREFIX

const cutting = [
    {
        // descr: Основная менюха
        path: _MAIN_PREFIX,
        name: 'manufacture.cell.cutting',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/cutting/TheCuttingMain.vue'),
        meta: {
            title: 'Раскрой'
        }
    },

/*


    {
        // descr: Движение СЗ ПС
        path: _MAIN_PREFIX + 'fabrics/tasks/movement',
        name: 'manufacture.cell.fabric.tasks.movement',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTasksMovement.vue'),
        meta: {
            title: 'Движение СЗ участка стежки'
        }
    },

    {
        // descr Управление СЗ ПС (создание, редактирование, удаление)
        path: _MAIN_PREFIX + 'fabrics/tasks/manage',
        name: 'manufacture.cell.fabric.tasks.manage',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTasksManage.vue'),
        meta: {
            title: 'Управление СЗ участка стежки'
        }
    },

    {
        // descr Инструмент управления ПС - календарь
        path: _MAIN_PREFIX + 'fabric/tasks/calendar',
        name: 'manufacture.cell.fabric.tasks.calendar',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTasksCalendar.vue'),
        meta: {
            title: 'Календарь ПС'
        }
    },

    {
        // descr: Выполнение СЗ ПС
        path: _MAIN_PREFIX + 'fabric/tasks/execute',
        name: 'manufacture.cell.fabric.tasks.execute',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTasksExecute.vue'),
        meta: {
            title: 'Выполнение СЗ участка стежки',
            // mode: FABRIC_PAGE_MODE.EXECUTE,
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
            title: 'Загрузка рисунков ПС в БД'
        }
    },

    // {
    //     // descr Буфер
    //     path: _MAIN_PREFIX + 'fabrics/buffer',
    //     name: 'manufacture.cell.fabrics.buffer',
    //     component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsBuffer.vue'),
    //     meta: {
    //         title: 'Буфер'
    //     }
    // },

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
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsExpense.vue'),
        meta: {
            title: 'Расход ПС'
        }
    },

    {
        // descr: Учет ПС
        path: _MAIN_PREFIX + 'fabrics/movement',
        name: 'manufacture.cell.fabrics.movement',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsMovement.vue'),
        meta: {
            title: 'Учет ПС'
        }
    },



    {
        // descr Добавление нового СЗ ПС
        path: _MAIN_PREFIX + 'fabrics/task/create',
        name: 'manufacture.cell.fabrics.task.create',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTasksManage.vue'),
        meta: {
            title: 'Создание нового СЗ участка стежки',
            mode: FABRIC_PAGE_MODE.CREATE,
        }
    },

    {
        // descr Редактирование СЗ ПС
        path: _MAIN_PREFIX + 'fabrics/task/edit/:id',
        name: 'manufacture.cell.fabrics.task.edit',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricTasksManage.vue'),
        meta: {
            title: 'Редактирование СЗ участка стежки',
            mode: FABRIC_PAGE_MODE.EDIT,
        }
    },



    {
        // descr: Список рисунков ПС
        path: _MAIN_PREFIX + 'fabric/pictures/show',
        name: 'manufacture.cell.fabric.pictures.show',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricPicturesShow.vue'),
        meta: {
            title: 'Список рисунков ПС'
        }
    },

    {
        // descr: Стегальные машины
        path: _MAIN_PREFIX + 'fabrics/machines',
        name: 'manufacture.cell.fabrics.machines',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsMachines.vue'),
        meta: {
            title: 'Стегальные машины'
        }
    },



*/


]

export default cutting
