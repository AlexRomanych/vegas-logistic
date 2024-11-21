const menuData = () => {

    return [
        {
            group: {name: 'Производство', shown: true, isActive: true, icon: 'HomeModernIcon'},
            items:
                [
                    {name: 'Сменные задания', path: 'plan', shown: false, isActive: true,},
                    {name: 'План производства по ПЯ', path: 'plan', shown: true, isActive: true,},
                    {name: 'Планирование производства', path: 'plan', shown: true, isActive: true,},
                    {name: 'Сменные задания', path: '', shown: true, isActive: true,},
                    {name: 'План производства по ПЯ', path: '', shown: true, isActive: true,},
                    {name: 'Планирование производства', path: '', shown: true, isActive: true,},
                    {name: 'Сменные задания', path: '', shown: true, isActive: true,},
                    {name: 'План производства по ПЯ', path: '', shown: true, isActive: true,},
                    {name: 'Планирование производства', path: '', shown: true, isActive: true,},
                    {name: 'Сменные задания', path: 'plan', shown: true, isActive: true,},
                    {name: 'План производства по ПЯ', path: '', shown: true, isActive: true,},
                    {name: 'Планирование производства', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Заявки', shown: true, isActive: true, icon: 'ShoppingCartIcon'},
            items:
                [
                    {name: 'Заявки за период', path: 'plan', shown: true, isActive: true,},
                    {name: 'Новая заявка', path: 'plan', shown: true, isActive: true,},
                ]
        },
        {
            group: {name: 'Клиенты', shown: true, isActive: true, icon: 'UserGroupIcon'},
            items:
                [
                    {name: 'Список клиентов', path: 'plan', shown: true, isActive: true,},
                    {name: 'Новый клиент', path: 'plan', shown: true, isActive: true,},
                    {name: 'Клиент 3', path: 'plan', shown: true, isActive: true,},
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
            group: {name: 'Справочники', shown: true, isActive: true, icon: 'BookOpenIcon'},
            items:
                [
                    {name: 'Модели', path: 'plan', shown: true, isActive: true,},
                    {name: 'Процедуры', path: 'plan', shown: true, isActive: true,},
                    {name: 'Производственные ячейки', path: 'plan', shown: true, isActive: true,},
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

console.log(menu)

export default menu


// export function menu() {
//     return {
//         menuItems: [
//             {
//                 groupId: 1, groupName: 'Клиенты', shown: false, items: [
//                     {itemId: 1, name: 'Клиент 1'},
//                     {itemId: 2, name: 'Клиент 2'},
//                     {itemId: 3, name: 'Клиент 3'},
//                 ]
//             }, {
//                 groupId: 2, groupName: 'Справочники', shown: false, items: [
//                     {itemId: 1, name: 'Справочник 1'},
//                     {itemId: 2, name: 'Справочник 2'},
//                     {itemId: 3, name: 'Справочник 3'},
//                 ]
//             }
//         ]
//     }
// }


// export default () => {}
// const menu = function () {
//
// }


// export default () => {
//     return {
//         menuItems: [
//             {
//                 groupId: 1, groupName: 'Клиенты', shown: false, items: [
//                     {itemId: 1, itemName: 'Клиент 1'},
//                     {itemId: 2, itemName: 'Клиент 2'},
//                     {itemId: 3, itemName: 'Клиент 3'},
//                 ]
//             }, {
//                 groupId: 2, groupName: 'Справочники', shown: false, items: [
//                     {itemId: 1, itemName: 'Справочник 1'},
//                     {itemId: 2, itemName: 'Справочник 2'},
//                     {itemId: 3, itemName: 'Справочник 3'},
//                 ]
//             }
//         ]
//     }
// }

