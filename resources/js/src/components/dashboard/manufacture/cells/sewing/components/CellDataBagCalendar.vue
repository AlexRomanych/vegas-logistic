<template>
    I am a CellDataBagCalendar
</template>

<script setup>
import {useCellsSewingStore} from '/resources/js/src/stores/cells/CellsSewingStore.js'

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

</script>

<style scoped>

</style>
