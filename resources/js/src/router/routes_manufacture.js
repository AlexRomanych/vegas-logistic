// info Manufacture

// Префикс для всех роутов производства
const _MANUFACTURE_PREFIX = '/manufacture'
const _CELL_PREFIX = '/cell/'
const _MAIN_PREFIX = _MANUFACTURE_PREFIX + _CELL_PREFIX

const manufactureRaw = [

    //hr----------------------------------------------------------------------------------------------------------------
    // attract: Общая инфа

    // info Группы ПЯ
    {
        path: _MANUFACTURE_PREFIX + '/cells/groups',
        name: 'manufacture.cells.groups',
        component: () => import('@/src/components/dashboard/manufacture/groups/TheCellsGroupsShow.vue'),
        // meta: {destination: '/models'},

    },
    // info сами ПЯ
    {
        path: _MANUFACTURE_PREFIX + '/cells',
        name: 'manufacture.cells',
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellsShow.vue'),
    },
    //hr----------------------------------------------------------------------------------------------------------------

    //hr----------------------------------------------------------------------------------------------------------------
    // attract: Производственные ячейки

    // attract: Стежка -------------------------------------------------------------------------------------------------
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
        path: _MAIN_PREFIX + 'sewing/auto',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/sewing/TheSewingCellAuto.vue'),
    },
    //info Пошив (УШМ)
    {
        path: _MAIN_PREFIX + 'sewing/universal',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },
    //info Обшивка 1 (hard)
    {
        path: _MAIN_PREFIX + 'sewing/hard',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
    },
    //info Обшивка 1 (lite)
    {
        path: _MAIN_PREFIX + 'sewing/light',
        // name: getRouteName(this.path),
        component: () => import('@/src/components/dashboard/manufacture/cells/TheCellStopper.vue'), //Заглушка
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

]


// Меняем слеши на точки для названия маршрута
const getRouteName = (path) => path.slice(1).replace(/\//g, ".")

const manufacture = manufactureRaw.map(route => {
    if (route.hasOwnProperty('name')) {
        return route
    } else {
        return {...route, name: getRouteName(route.path)}
    }
})

// console.log(manufacture)

export default manufacture
