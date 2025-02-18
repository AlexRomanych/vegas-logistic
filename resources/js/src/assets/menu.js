const menuData = () => {

    return [
        {
            group: {name: 'Производство', shown: true, isActive: true, icon: 'HomeModernIcon'},
            items:
                [
                    {name: 'Группы ПЯ', path: 'manufacture.cells.groups', shown: true, isActive: true,},
                    {name: 'Производственные ячейки (ПЯ)', path: 'manufacture.cells', shown: true, isActive: true,},
                    {name: 'Сменные задания', path: 'manufacture.cells.tasks', shown: true, isActive: true,},
                    {name: 'Стежка', path: 'manufacture.cell.fabrics', shown: true, isActive: true,},
                    // {name: 'План производства по ПЯ', path: 'plan', shown: true, isActive: true,},
                    // {name: 'Планирование производства', path: '', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Заявки', shown: true, isActive: true, icon: 'ShoppingCartIcon'},
            items:
                [
                    {name: 'Заявки за период', path: 'orders', shown: true, isActive: true,},
                    {name: 'Загрузить с диска', path: 'orders.upload', shown: true, isActive: true,},
                    {name: 'Загрузить из 1C', path: 'plan', shown: true, isActive: false,},
                    {name: 'Новая заявка', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Модели', shown: true, isActive: true, icon: 'PuzzlePieceIcon'},
            items:
                [
                    {name: 'Список моделей', path: 'models', shown: true, isActive: true,},
                    {name: 'Загрузка из базы', path: 'models.load', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Клиенты', shown: true, isActive: true, icon: 'UserGroupIcon'},
            items:
                [
                    {name: 'Список клиентов', path: 'clients', shown: true, isActive: true,},
                    {name: 'Загрузка из базы', path: 'clients.load', shown: true, isActive: true,},
                    {name: 'Новый клиент', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Персонал', shown: true, isActive: true, icon: 'UserPlusIcon'},
            items:
                [
                    {name: 'Список сотрудников', path: 'workers', shown: true, isActive: true,},
                    {name: 'Добавить сотрудника', path: 'workers.add', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Справочники', shown: true, isActive: true, icon: 'BookOpenIcon'},
            items:
                [
                    {name: 'Модели', path: 'models', shown: true, isActive: true,},
                    {name: 'Группы ПЯ', path: 'manufacture.cells.groups', shown: true, isActive: true,},
                    {name: 'Производственные ячейки (ПЯ)', path: 'manufacture.cells', shown: true, isActive: true,},
                    {name: 'Список клиентов', path: 'clients', shown: true, isActive: true,},
                    {name: 'Процедуры расчета', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Инструменты', shown: true, isActive: true, icon: 'WrenchScrewdriverIcon'},
            items:
                [
                    {name: 'Резка ППУ(A)', path: '', shown: true, isActive: false,},
                    {name: 'Резка ППУ', path: '', shown: true, isActive: false,},
                ]
        },
        {
            group: {name: 'Склад', shown: true, isActive: false, icon: 'BuildingStorefrontIcon'},
            items:
                [
                    {name: 'План отгрузок', path: 'plan', shown: true, isActive: true,},
                    {name: 'Динамика загрузки склада', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Отчеты', shown: true, isActive: false, icon: 'ClipboardDocumentIcon'},
            items:
                [
                    {name: 'Report_1', path: 'plan', shown: true, isActive: true,},
                    {name: 'Report_2', path: 'plan', shown: true, isActive: true,},
                    {name: 'Report_3', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Пользователи', shown: true, isActive: false, icon: 'UsersIcon'},
            items:
                [
                    {name: 'User_1', path: 'plan', shown: true, isActive: true,},
                    {name: 'User_2', path: 'plan', shown: true, isActive: true,},
                    {name: 'User_3', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Настройки', shown: true, isActive: false, icon: 'Cog8ToothIcon'},
            items:
                [
                    {name: 'Setting_1', path: 'plan', shown: true, isActive: true,},
                    {name: 'Setting_2', path: 'plan', shown: true, isActive: true,},
                    {name: 'Setting_3', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Tutorials', shown: false, isActive: false, icon: 'BookOpenIcon'},
            items:
                [
                    {name: 'Tutorial 1', path: 'plan', shown: true, isActive: true,},
                    {name: 'Tutorial 2', path: 'plan', shown: true, isActive: true,},
                    {name: 'Tutorial 3', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: '1234567890', shown: false, isActive: false, icon: 'BookOpenIcon'},
            items:
                [
                    {name: 'Tutorial 1', path: 'plan', shown: true, isActive: true,},
                    {name: 'Tutorial 2', path: 'plan', shown: true, isActive: true,},
                    {name: 'Tutorial 3', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'UI', shown: true, isActive: true, icon: 'BookOpenIcon'},
            items:
                [
                    {name: 'User Interface', path: 'ui.show', shown: true, isActive: true,},
                ]
        }

    ]
}

// Добавляем id к группам и подменю
// Делаем защиту: если path='' то isActive = false
const menu = menuData().map((item, index) => {
    const {group, items} = item
    group.id = ++index
    const newItems = items.map((item, index) => {
        item.isActive = item.path !== ''
        return {...item, 'id': ++index}
    })

    return {group, items: newItems}
})

export default menu
