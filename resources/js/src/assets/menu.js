const menuData = () => {

    return [
        {
            group: {name: 'Производство', shown: true, isActive: true, icon: 'HomeModernIcon'},
            items:
                [
                    {name: 'Сменные задания', path: 'manufacture',},
                    {name: 'План производства по ПЯ', path: 'plan',},
                    {name: 'Планирование производства', path: 'plan'},
                ]
        },
        {
            group: {name: 'Заявки', shown: true, isActive: true, icon: 'ShoppingCartIcon'},
            items:
                [
                    {name: 'Заявки за период'},
                    {name: 'Новая заявка'},
                ]
        },
        {
            group: {name: 'Клиенты', shown: true, isActive: true, icon: 'UserGroupIcon'},
            items:
                [
                    {name: 'Список клиентов'},
                    {name: 'Новый клиент'},
                    {name: 'Клиент 3'},
                ]
        },
        {
            group: {name: 'Склад', shown: true, isActive: false, icon: 'BuildingStorefrontIcon'},
            items:
                [
                    {name: 'План отгрузок'},
                    {name: 'Динамика загрузки склада'},
                ]
        },
        {
            group: {name: 'Справочники', shown: true, isActive: false, icon: 'BookOpenIcon'},
            items:
                [
                    {name: 'Модели'},
                    {name: 'Процедуры'},
                    {name: 'Производственные ячейки'},
                ]
        },
        {
            group: {name: 'Отчеты', shown: true, isActive: false, icon: 'ClipboardDocumentIcon'},
            items:
                [
                    {name: 'Отчет_1'},
                    {name: 'Отчет_2'},
                    {name: 'Отчет_3'},
                ]
        },
        {
            group: {name: 'Пользователи', shown: true, isActive: false, icon: 'UsersIcon'},
            items:
                [
                    {name: 'Отчет_1'},
                    {name: 'Отчет_2'},
                    {name: 'Отчет_3'},
                ]
        },
        {
            group: {name: 'Настройки', shown: true, isActive: false, icon: 'Cog8ToothIcon'},
            items:
                [
                    {name: 'Отчет_1'},
                    {name: 'Отчет_2'},
                    {name: 'Отчет_3'},
                ]
        },
        {
            group: {name: 'Tutorials', shown: false, isActive: false, icon: 'BookOpenIcon'},
            items:
                [
                    {name: 'Tutorial 1'},
                    {name: 'Tutorial 2'},
                    {name: 'Tutorial 3'},
                ]
        },
        {
            group: {name: '1234567890', shown: false, isActive: false, icon: 'BookOpenIcon'},
            items:
                [
                    {name: 'Tutorial 1'},
                    {name: 'Tutorial 2'},
                    {name: 'Tutorial 3'},
                ]
        }

    ]
}

// Добавляем id к группам и подменю
const menu = menuData().map((item, index) => {
    const {group, items} = item
    group.id = ++index
    const newItems = items.map((item, index) => ({...item, 'id': ++index}))
    return {group, items: newItems}
})

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

