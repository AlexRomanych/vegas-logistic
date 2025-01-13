// info Тут все, что касается логики заказов

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает отформатированный цифровой номер заказа
export function getPrettyOrderNumber(noNum) {

    let orderNo = noNum
    if (orderNo.indexOf('.') !== -1) {

        const orderNoDec = orderNo.split('.')[0]
        const orderNoFloat = orderNo.split('.')[1]

        if (+orderNoFloat === 0) {
            orderNo = orderNoDec
        }
    }

    return orderNo
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает общее количество изделий в заказе
export function getOrderElementsAmount(order) {
    return order.cells.reduce((acc, cell) => acc + cell.amount, 0)
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает общее количество изделий в день
export function getDayOrdersElementsAmount(ordersDayBag) {
    return ordersDayBag.orders.reduce((acc, order) => {
        let orderTotalAmount = order.order.cells.reduce((acc2, cell) => acc2 + cell.amount, 0)
        return acc + orderTotalAmount
    }, 0)
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает общие трудозатраты изделий в заказе
export function getOrderTimesAmount(order) {
    return order.cells.reduce((acc, cell) => acc + cell.norm, 0)
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает общие трудозатраты изделий в день
export function getDayOrdersTimesAmount(ordersDayBag) {
    return ordersDayBag.orders.reduce((acc, order) => {
        let orderManufTime = order.order.cells.reduce((acc2, cell) => acc2 + cell.norm, 0)
        return acc + orderManufTime
    }, 0)
}
