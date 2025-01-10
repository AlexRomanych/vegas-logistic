<template>
    I am a CellDataBagTable
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

console.log(props.cellsData)
console.log(dateInterval)

</script>

<style scoped>

</style>
