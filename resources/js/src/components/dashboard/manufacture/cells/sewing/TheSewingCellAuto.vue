<template>
    <CellDatesSelect
        :sewing-type="THIS_SEWING_TYPE"
        @clickApply="getSewingCellData"
    />

    <div class="flex flex-row justify-start items-center m-3 space-x-10">
        <AppLabel
            :bold="true"
            align="center"
            height="h-[35px]"
            text="Список"
            textSize="small"
            width="w-[100px]"
        />
        <AppLabel
            :bold="true"
            align="center"
            height="h-[35px]"
            text="Таблица"
            textSize="small"
            width="w-[100px]"
        />

        <AppLabel
            :bold="true"
            align="center"
            height="h-[35px]"
            text="Календарь"
            textSize="small"
            width="w-[100px]"
        />

        <AppLabel
            :bold="true"
            align="center"
            height="h-[35px]"
            text="Oбъединение"
            textSize="small"
            width="w-[100px]"
        />

    </div>

    <div class="ml-3 mt-3">
        <CellDataBag v-if="datesWithOrders.length" :key="Date.now()"
                     :cells-data="datesWithOrders"
        />
    </div>
</template>

<script setup>
import {ref, reactive} from 'vue'
import {useCellsSewingStore} from '/resources/js/src/stores/cells/CellsSewingStore.js'

import CellDatesSelect from '/resources/js/src/components/dashboard/manufacture/cells/components/CellDatesSelect.vue'
import CellDataBag from '/resources/js/src/components/dashboard/manufacture/cells/components/CellDataBag.vue'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'


import {
    CELL_AUTO_TYPE,
    CELL_UNIVERSAL_TYPE,
    CELL_SOLID_HARD_TYPE,
    CELL_SOLID_LIGHT_TYPE
} from '/resources/js/src/app/constants/sewingTypes.js'

// attract: Задаем тип ПЯ Швейки
const THIS_SEWING_TYPE = CELL_AUTO_TYPE

const datesWithOrders = ref([])                    // выходной массив
// const datesWithOrders = []                    // выходной массив

// attract: получаем данные
let sewingCells = []
const getSewingCellData = async (dateInterval) => {
    // console.log(dateInterval)
    const sewingStore = useCellsSewingStore()
    sewingCells = await sewingStore.getCellsSewing(dateInterval, THIS_SEWING_TYPE)

    console.log(sewingCells);

    const orders = sewingCells.orders           // Список заявок
    const cells = sewingCells.cell_data         // Список матрасов


    // attract: Преобразуем в вид, пригодный для вывода в виде списка
    // attract: Дата -> Клиент -> Список матрасов

    // attract: Получаем массив уникальных дат
    const uniqueDates = [...new Set(cells.map(cells => cells.manufactured_at))].sort()

    datesWithOrders.value = uniqueDates.map(date => {

        // attract: собираем все cellsLines, которые принадлежат данной дате
        const cellsList = []
        cells.forEach(cell => {
            if (cell.manufactured_at === date) {
                cellsList.push(cell)
            }
        })

        // attract: собираем все уникальные orders_id в списке cellsLines, которые принадлежат данной дате
        const uniqueOrders = [...new Set(cellsList.map(cells => cells.line.order_id))].sort()

        // attract: собираем все cellsLines, которые принадлежат выбранным заказам
        const ordersList = []
        uniqueOrders.forEach(orderId => {

            // attract: Находим заказ по id
            let findOrder = null
            orders.forEach(order => {
                if (order.id === orderId) {
                    findOrder = order

                    // Выбираем все cellsLines, которые принадлежат данному заказу
                    const cellsInOrder = cellsList.filter(cells => cells.line.order_id === orderId)
                    findOrder.cells = cellsInOrder              // Список матрасов пихаем в свойство cells заказа
                    // ordersList.push({order: findOrder, cells: cellsInOrder})
                    ordersList.push({order: findOrder})
                }
            })

        })

        // return {date: date, cells: cellsList, orders: ordersList}
        return {date: date, orders: ordersList}
    })


    console.log(datesWithOrders.value)

}


//
//
// // console.log(orders.value.length)
//
// // todo Сделать вывод ошибки, если сервер криво ответил
// if (isResponseWithError(orders.value)) {
//     orders.value = []
// }
//
// // console.log(orders.value)
// ordersShowIsChanged.value = false
// showEmptyMessage.value = orders.value.length === 0
//
// // console.log(orders.value)


</script>

<style scoped>

</style>
