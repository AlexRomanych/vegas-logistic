const menu = function () {

    return [
        {
            groupName: 'Клиенты', shown: false, isActive: true, icon: 'UserGroupIcon', items:
                [
                    {itemId: 1, itemName: 'Клиент 1'},
                    {itemId: 2, itemName: 'Клиент 2'},
                    {itemId: 3, itemName: 'Клиент 3'},
                ]
        }, {
            groupName: 'Справочники', shown: false, isActive: false, icon: 'BookOpenIcon', items:
                [
                    {itemId: 1, itemName: 'Справочник 1'},
                    {itemId: 2, itemName: 'Справочник 2'},
                    {itemId: 3, itemName: 'Справочник 3'},
                ]
        }
    ]
}


export default menu


// export function menu() {
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

