<template>
    <div v-if="cellsTableData">
        <AppTable
            :tableData="cellsTableData"

            width="w-[800px]"
        />
    </div>
</template>

<script setup>
import {useCellsGroupsStore} from '@/src/stores/CellsGroupsStore.js'
import AppTable from "@/src/components/ui/tables/AppTable.vue";

const cellsStore = useCellsGroupsStore()
const cells = await cellsStore.getCells()

console.log(cells)


const cellsData = cells.map(cell => {
    return [cell.code1C, cell.name, cell.no, cell.unit, 'Нет данных']
})

const cellsTableData = {}
cellsTableData.headers = ['Код 1С', 'Название ПЯ', 'Номер ПЯ', 'Ед. произв-сти', 'Норма']
cellsTableData.data = cellsData



</script>

<style scoped>

</style>
