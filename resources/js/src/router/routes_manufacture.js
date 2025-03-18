// info Manufacture

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX = '/cell/'
const _TASK_PREFIX = '/task'
const _MAIN_PREFIX = _MANUFACTURE_PREFIX + _CELL_PREFIX

import {
    CELL_AUTO_TYPE,
    CELL_UNIVERSAL_TYPE,
    CELL_SOLID_HARD_TYPE,
    CELL_SOLID_LIGHT_TYPE
} from '/resources/js/src/app/constants/sewingTypes.js'

const manufactureRaw = [

    //hr----------------------------------------------------------------------------------------------------------------
    // attract: Общая инфа

    // info Группы ПЯ
    {
        path: _MANUFACTURE_PREFIX + '/cells/groups',
        name: 'manufacture.cells.groups',
        component: () => import('@/src/components/dashboard/manufacture/groups/TheCellsGroupsShow.vue'),
        meta: {title: 'Группы ПЯ'},

    },

    // info сами ПЯ
    {
        path: _MANUFACTURE_PREFIX + '/cells',
        name: 'manufacture.cells',
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellsShow.vue'),
        meta: {title: 'Производственные ячейки'},
    },

    // info сменные задания к ПЯ
    {
        path: _MANUFACTURE_PREFIX + '/cells/tasks',
        name: 'manufacture.cells.tasks',
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellsTaskShow.vue'),
        meta: {title: 'Сменные задания к ПЯ'},
    },

    //hr----------------------------------------------------------------------------------------------------------------

    //hr----------------------------------------------------------------------------------------------------------------
    // attract: Производственные ячейки

    // attract: Стежка -------------------------------------------------------------------------------------------------

    {
        // Основная менюха
        path: _MAIN_PREFIX + 'fabrics',
        name: 'manufacture.cell.fabrics',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsMain.vue'),
        meta: {
            title: 'Стежка'
        }
    },

    {
        // Инструмент управления ПС
        path: _MAIN_PREFIX + 'fabrics/manage',
        name: 'manufacture.cell.fabrics.manage',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsManage.vue'),
        meta: {
            title: 'Управление ПС'
        }
    },

    {
        // Список ПС
        path: _MAIN_PREFIX + 'fabrics/show',
        name: 'manufacture.cell.fabrics.show',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsShow.vue'),
        meta: {
            title: 'Список ПС'
        }
    },

    {
        // Загрузка с диска списка ПС
        path: _MAIN_PREFIX + 'fabrics/upload',
        name: 'manufacture.cell.fabrics.upload',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsUpload.vue'),
        meta: {
            title: 'Загрузка ПС в БД'
        }
    },

    {
        // Загрузка с диска списка рисунков ПС
        path: _MAIN_PREFIX + 'fabrics/pictures/upload',
        name: 'manufacture.cell.fabrics.pictures.upload',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsPicturesUpload.vue'),
        meta: {
            title: 'Загрузка ПС в БД'
        }
    },

    {
        // Буфер
        path: _MAIN_PREFIX + 'fabrics/buffer',
        name: 'manufacture.cell.fabrics.buffer',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsBuffer.vue'),
        meta: {
            title: 'Буфер'
        }
    },

    {
        // Добавление ПС
        path: _MAIN_PREFIX + 'fabrics/add',
        name: 'manufacture.cell.fabrics.add',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricEditForm.vue'),
        meta: {
            title: 'Добавление ПС',
            mode: 'add'
        },
    },

    {
        // Редактирование ПС
        path: '/fabric/edit/:id',
        name: 'manufacture.cell.fabric.edit',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricEditForm.vue'),
        meta: {
            title: 'Редактирование ПС',
            mode: 'edit'
        },
    },

    {
        // Загрузка расхода ПС из 1С из отчета "Сводная ведомость потребности материалов" - СВПМ
        path: '/fabric/expense/upload',
        name: 'manufacture.cell.fabrics.expense.upload',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsExpenseUpload.vue'),
        meta: {
            title: 'Загрузка расхода ПС из отчета 1С - СВПМ',
        },
    },

    {
        // Учет ПС
        path: _MAIN_PREFIX + 'fabrics/expense',
        name: 'manufacture.cell.fabrics.expense',
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/fabric/TheFabricsExpensive.vue'),
        meta: {
            title: 'Учет ПС'
        }
    },


    //info Стежка Китаец
    {
        path: _MAIN_PREFIX + 'stitch/china',
        // name: manufacture[0].path,
        // name: getRouteName(_MAIN_PREFIX + 'stitch/china'),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },
    //info Стежка Немец
    {
        path: _MAIN_PREFIX + 'stitch/german',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },
    //info Стежка Американец
    {
        path: _MAIN_PREFIX + 'stitch/american',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },
    //info Стежка Кореец
    {
        path: _MAIN_PREFIX + 'stitch/korean',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },
    //info Стежка одноиголка
    {
        path: _MAIN_PREFIX + 'stitch/one_needle',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },

    // attract: Раскрой-------------------------------------------------------------------------------------------------
    //info Раскрой (панели чехла)
    {
        path: _MAIN_PREFIX + 'cutting/cover_panel',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },
    //info Раскрой (дет. чехла)
    {
        path: _MAIN_PREFIX + 'cutting/cover_panel_child',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },
    //info Оверлок
    {
        path: _MAIN_PREFIX + 'overloсk',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },

    // attract: Швейка -------------------------------------------------------------------------------------------------
    //info Пошив (автоматы) hr ok
    {
        path: _MAIN_PREFIX + 'sewing/' + CELL_AUTO_TYPE,
        // name: getRouteName(this.path),
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/sewing/TheSewingCellAuto.vue'),
        meta: {
            title: 'ПЯ: Пошив (автоматы)',
            type: CELL_AUTO_TYPE
        },
    },
    //info Пошив (УШМ) hr ok
    {
        path: _MAIN_PREFIX + 'sewing/' + CELL_UNIVERSAL_TYPE,
        // name: getRouteName(this.path),
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/sewing/TheSewingCellAuto.vue'),
        meta: {
            title: 'ПЯ: Пошив (УШМ)',
            type: CELL_UNIVERSAL_TYPE
        }
    },
    //info Обшивка 1 (hard) hr ok
    {
        path: _MAIN_PREFIX + 'sewing/' + CELL_SOLID_HARD_TYPE,
        // name: getRouteName(this.path),
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/sewing/TheSewingCellAuto.vue'),
        meta: {
            title: 'Обшивка 1 (hard)',
            type: CELL_SOLID_HARD_TYPE
        }
    },
    //info Обшивка 1 (lite) hr ok
    {
        path: _MAIN_PREFIX + 'sewing/' + CELL_SOLID_LIGHT_TYPE,
        // name: getRouteName(this.path),
        component: () => import('/resources/js/src/components/dashboard/manufacture/cells/sewing/TheSewingCellAuto.vue'),
        meta: {
            title: 'Обшивка 1 (lite)',
            type: CELL_SOLID_LIGHT_TYPE
        }
    },

    // attract: Сборка -------------------------------------------------------------------------------------------------
    //info Сборка Lamit
    {
        path: _MAIN_PREFIX + 'assembly/lamit',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },
    //info Сборка Столы
    {
        path: _MAIN_PREFIX + 'assembly/table',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },

    // attract: Упаковка -----------------------------------------------------------------------------------------------
    //info Упаковка
    {
        path: _MAIN_PREFIX + 'pack',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },

    // attract: НПБ(производство) --------------------------------------------------------------------------------------
    //info НПБ(производство)
    {
        path: _MAIN_PREFIX + 'blocks',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },


    //hr----------------------------------------------------------------------------------------------------------------
    //hr----------------------------------------------------------------------------------------------------------------
    //hr----------------------------------------------------------------------------------------------------------------
    // attract: Сменные задания для Производственных ячеек

    // attract: Стежка -------------------------------------------------------------------------------------------------
    //info Стежка Китаец
    {
        path: _MAIN_PREFIX + 'stitch/china' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка

    },
    //info Стежка Немец
    {
        path: _MAIN_PREFIX + 'stitch/german' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },
    //info Стежка Американец
    {
        path: _MAIN_PREFIX + 'stitch/american' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },
    //info Стежка Кореец
    {
        path: _MAIN_PREFIX + 'stitch/korean' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },
    //info Стежка одноиголка
    {
        path: _MAIN_PREFIX + 'stitch/one_needle' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },

    // attract: Раскрой-------------------------------------------------------------------------------------------------
    //info Раскрой (панели чехла)
    {
        path: _MAIN_PREFIX + 'cutting/cover_panel' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },
    //info Раскрой (дет. чехла)
    {
        path: _MAIN_PREFIX + 'cutting/cover_panel_child' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },
    //info Оверлок
    {
        path: _MAIN_PREFIX + 'overloсk' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },

    // attract: Швейка -------------------------------------------------------------------------------------------------
    //info Пошив (автоматы) hr ok
    {
        path: _MAIN_PREFIX + 'sewing/' + CELL_AUTO_TYPE + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/sewing/TheSewingCellAutoTask.vue'),
        meta: {
            title: 'Сменные задания к ПЯ: Пошив - АШМ',
            type: CELL_AUTO_TYPE
        },
    },
    //info Пошив (УШМ) hr ok
    {
        path: _MAIN_PREFIX + 'sewing/' + CELL_UNIVERSAL_TYPE + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/sewing/TheSewingCellAutoTask.vue'),
        meta: {
            title: 'Сменные задания к ПЯ: Пошив - УШМ',
            type: CELL_UNIVERSAL_TYPE
        },
    },
    //info Обшивка 1 (hard) hr ok
    {
        path: _MAIN_PREFIX + 'sewing/' + CELL_SOLID_HARD_TYPE + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/sewing/TheSewingCellAutoTask.vue'),
        meta: {
            title: 'Сменные задания к ПЯ: Пошив - Обшивка 1 (hard)',
            type: CELL_SOLID_HARD_TYPE
        },
    },
    //info Обшивка 1 (lite) hr ok
    {
        path: _MAIN_PREFIX + 'sewing/' + CELL_SOLID_LIGHT_TYPE + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/sewing/TheSewingCellAutoTask.vue'),
        meta: {
            title: 'Сменные задания к ПЯ: Пошив - Обшивка 1 (lite)',
            type: CELL_SOLID_LIGHT_TYPE
        },
    },

    // attract: Сборка -------------------------------------------------------------------------------------------------
    //info Сборка Lamit
    {
        path: _MAIN_PREFIX + 'assembly/lamit' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },
    //info Сборка Столы
    {
        path: _MAIN_PREFIX + 'assembly/table' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },

    // attract: Упаковка -----------------------------------------------------------------------------------------------
    //info Упаковка
    {
        path: _MAIN_PREFIX + 'pack' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },

    // attract: НПБ(производство) --------------------------------------------------------------------------------------
    //info НПБ(производство)
    {
        path: _MAIN_PREFIX + 'blocks' + _TASK_PREFIX,
        component: () => import('/resources/js/src/components/dashboard/manufacture/tasks/TheCellTaskStopper.vue'), //Заглушка
    },

]


// Меняем слеши на точки для названия маршрута
const getRouteName = (path) => path.slice(1).replace(/\//g, ".")

// attract Определяем маршруты для визуализации ПЯ
const manufacture = manufactureRaw.map(route => {
    if (route.hasOwnProperty('name')) {
        return route
    } else {
        return {...route, name: getRouteName(route.path)}
    }
})


// console.log(manufacture)


export default manufacture
