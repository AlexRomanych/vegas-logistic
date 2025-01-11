<template>
    I am a CellDataBagCalendar
</template>

<script setup>
import {useCellsSewingStore} from '/resources/js/src/stores/cells/CellsSewingStore.js'

import {getDateIntervalMatrix} from '/resources/js/src/app/helpers/helpers_date.js'
// import {subtractDays, addDays} from 'resources/js/src/app/helpers/helpers_date.js'

const props = defineProps({
    cellsData: {
        type: Array,
        required: true
    }
})

const sewingStore = useCellsSewingStore()
const dateInterval = sewingStore.dateInterval       // восстанавливаем интервал выборки

// находим максимальное количество заказов в день
const maxOrdersInDay = props.cellsData.reduce((maxOrders, item,) => item.orders.length > maxOrders ? item.orders.length : maxOrders, 0)

console.log(props.cellsData)
console.log(dateInterval)
console.log(maxOrdersInDay)

// attract Возвращаем матрицу календаря ([1..x] x [1..7]), где элемент массива - дата
const dateIntervalMatrix = getDateIntervalMatrix(dateInterval.start, dateInterval.end)

console.log(dateIntervalMatrix)
// console.log({...dateInterval})


</script>

<style scoped>

</style>
