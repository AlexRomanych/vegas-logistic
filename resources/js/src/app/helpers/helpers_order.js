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
        } else {
            let orderNoFloatNumber = +orderNoFloat

            while (orderNoFloatNumber % 10 == 0) orderNoFloatNumber /= 10

            orderNo = orderNoDec + '.' + orderNoFloatNumber.toString()
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

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает максимальное количество заказов в день
export function getMaxOrdersInDay(cellsData) {
    return cellsData.reduce((maxOrders, item) => item.orders.length > maxOrders ? item.orders.length : maxOrders, 0)
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Возвращает матрицу для отображения календаря с заказами =
// attract Накладываем на матрицу календаря массив с данными о заказах
//  dateIntervalMatrix  - матрица диапазонов дат
//  cellsData            - массив с заказами
export function getDateIntervalMatrixRender(dateIntervalMatrix, cellsData) {

    return dateIntervalMatrix.map((item, index) => {

        const weekOrders = []

        for (let i = 0; i < item.length; i++) {

            weekOrders[i] = {'date': item[i]}

            let orders = []

            for (let j = 0; j < cellsData.length; j++) {

                if (cellsData[j].date.slice(0, 10) === item[i]) {
                    orders = cellsData[j].orders
                    break       // это обязательный break
                }

            }

            weekOrders[i] = {...weekOrders[i], 'orders': orders}

        }
        return weekOrders
    })
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// attract Перемещает заказ с одной даты на другую
//  dateFrom                    - дата, с которой перемещаем заказ
//  dateTo                      - дата, на которую перемещаем заказ
//  order                       - заказ, который перемещаем
//  dateIntervalMatrixRender    - матрица для отображения календаря с заказами, передаем по ссылке
export function moveOrder(dateFrom, dateTo, order, dateIntervalMatrixRender) {

    console.log(dateFrom)
    console.log(dateTo)

    const dateFromIndex = {}
    const dateToIndex = {}
    for (let i = 0; i < dateIntervalMatrixRender.length; i++) {
        for (let j = 0; j < dateIntervalMatrixRender[i].length; j++) {
            if (dateIntervalMatrixRender[i][j].date === dateFrom) {
                dateFromIndex.i = i
                dateFromIndex.j = j
            } else if (dateIntervalMatrixRender[i][j].date === dateTo) {
                dateToIndex.i = i
                dateToIndex.j = j
            }
        }
    }

    // console.log(order)
    // console.log(dateIntervalMatrixRender[dateFromIndex.i][dateFromIndex.j].orders)

    // добавляем заказ
    dateIntervalMatrixRender[dateToIndex.i][dateToIndex.j].orders.push(order)

    // удаляем заказ
    dateIntervalMatrixRender[dateFromIndex.i][dateFromIndex.j].orders =
        dateIntervalMatrixRender[dateFromIndex.i][dateFromIndex.j].orders.filter(item => item.order.id !== order.order.id)

    // dateIntervalMatrixRender[dateFromIndex.i][dateFromIndex.j].orders.forEach(item => console.log(item.order))

}
