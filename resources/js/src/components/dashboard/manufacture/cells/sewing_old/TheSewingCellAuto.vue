<template>
    <CellDatesSelect
        :sewing-type="THIS_SEWING_TYPE"
        @clickApply="getSewingCellData"
    />

    <!--    <div class="flex flex-row justify-start items-center m-3 space-x-10">-->
    <!--        <AppLabel-->
    <!--            :bold="true"-->
    <!--            :text="tabs.list.name"-->
    <!--            @label-click="changeTab"-->
    <!--            align="center"-->
    <!--            height="h-[35px]"-->
    <!--            textSize="small"-->
    <!--            width="w-[100px]"-->
    <!--        />-->

    <!--        <AppLabel-->
    <!--            :bold="true"-->
    <!--            :text="tabs.table.name"-->
    <!--            @label-click="changeTab"-->
    <!--            align="center"-->
    <!--            height="h-[35px]"-->
    <!--            textSize="small"-->
    <!--            width="w-[100px]"-->
    <!--        />-->

    <!--        <AppLabel-->
    <!--            :bold="true"-->
    <!--            :text="tabs.calendar.name"-->
    <!--            @label-click="changeTab"-->
    <!--            align="center"-->
    <!--            height="h-[35px]"-->
    <!--            textSize="small"-->
    <!--            width="w-[100px]"-->
    <!--        />-->

    <!--        <AppLabel-->
    <!--            :bold="true"-->
    <!--            :text="tabs.union.name"-->
    <!--            @label-click="changeTab"-->
    <!--            align="center"-->
    <!--            height="h-[35px]"-->
    <!--            textSize="small"-->
    <!--            width="w-[100px]"-->
    <!--        />-->

    <!--    </div>-->

    <!-- attract Выводим табы -->
    <div class="flex flex-row justify-start items-center m-3 space-x-10">
        <AppLabel
            v-for="tab in tabs"
            :key="tab.name"
            :bold="true"
            :text="tab.name"
            :type="tab.shown ? 'primary' : 'dark'"
            align="center"
            class="cursor-pointer"
            height="h-[35px]"
            textSize="small"
            width="w-[100px]"
            @label-click="changeTab"
        />
    </div>


    <div class="ml-3 mt-3">

        <!--attract: Список-->
        <div v-if="tabs.list.shown">
            <CellDataBagList
                v-if="datesWithOrders.length"
                :key="Date.now()"
                :cells-data="datesWithOrders"
            />
        </div>

        <!--attract: Таблица-->
        <div v-if="tabs.table.shown">
            <CellDataBagTable
                :key="Date.now()"
                :cells-data="datesWithOrders"
            />
        </div>

        <!--attract: Календарь-->
        <div v-if="tabs.calendar.shown">
            <CellDataBagCalendar
                :key="Date.now()"
                :cells-data="datesWithOrders"
            />
        </div>

        <!--attract: Объединение-->
        <div v-if="tabs.union.shown">
            <CellDataBagUnion
                :key="Date.now()"
                :cells-data="datesWithOrders"
            />
        </div>

    </div>


</template>

<script setup>
import {ref, reactive} from 'vue'
import {useRoute} from 'vue-router'
import {useCellsSewingStore} from '/resources/js/src/stores/cells/CellsSewingStore.js'

import CellDatesSelect from '/resources/js/src/components/dashboard/manufacture/cells/components/CellDatesSelect.vue'
import CellDataBagList
    from '/resources/js/src/components/dashboard/manufacture/cells/sewing/components/CellDataBagList.vue'
import CellDataBagTable
    from '/resources/js/src/components/dashboard/manufacture/cells/sewing/components/CellDataBagTable.vue'
import CellDataBagCalendar
    from '/resources/js/src/components/dashboard/manufacture/cells/sewing/components/CellDataBagCalendar.vue'
import CellDataBagUnion
    from '/resources/js/src/components/dashboard/manufacture/cells/sewing/components/CellDataBagUnion.vue'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
// import AppButton from '/resources/js/src/components/ui/buttons/AppButton.vue'


import {
    CELL_AUTO_TYPE,
    CELL_UNIVERSAL_TYPE,
    CELL_SOLID_HARD_TYPE,
    CELL_SOLID_LIGHT_TYPE
} from '/resources/js/src/app/constants/sewingTypes.js'

const route = useRoute()

// attract: Задаем тип ПЯ Швейки
const THIS_SEWING_TYPE = route.meta.type    // получаем из маршрута
// const THIS_SEWING_TYPE = CELL_AUTO_TYPE


// attract: Задаем отображение вкладок (список, таблица, календарь, объединение)
const tabs = reactive({
    list: {shown: false, name: 'Список'},
    table: {shown: false, name: 'Таблица'},
    calendar: {shown: false, name: 'Календарь'},
    union: {shown: false, name: 'Объединение'},
})

// переключаем выбранную вкладку
const changeTab = (selectedTab) => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = tabs[tab].name === selectedTab
        }
    }
    console.log(tabs)
}

// сбрасываем все вкладки в false, потому что в некоторых ситуациях появляется мультивыбор
const resetTabs = () => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = false
        }
    }
    console.log(tabs)
}

const datesWithOrders = ref([])                    // выходной массив
// const datesWithOrders = []                    // выходной массив

// attract: получаем данные
let sewingCells = []

const getSewingCellData = async (dateInterval) => {
    // console.log(dateInterval)
    const sewingStore = useCellsSewingStore()

    sewingStore.dateInterval = dateInterval     // заносим в переменную дату интервала
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

    resetTabs()                             // сбрасываем все табы
    tabs.list.shown = true                  // делаем вкладку "список" активной, чтобы запустить реактивность

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
