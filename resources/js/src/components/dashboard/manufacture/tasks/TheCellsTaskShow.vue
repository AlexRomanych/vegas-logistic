<template>
    <div
        class="ml-2 mt-2 p-2 border-2 border-slate-400 rounded-lg text-slate-600 w-[700px] font-semibold"
    >
        <div
            v-for="cell in cells"
            :key="cell.id"
            class="hover:bg-slate-500 hover:text-slate-100 rounded-lg"
        >
            <router-link :to="{ name: getRouteName(cell.url) }">
                <div class="flex justify-start items-center">
                    <div class="w-[70px] pl-2">
                        <span>{{ cell.code1C }}</span>
                    </div>

                    <div class="w-[200px]">
                        <span>{{ cell.name }}</span>
                    </div>

                    <div class="w-[50px] text-right pr-3">
                        <span>{{ cell.no }}</span>
                    </div>

                    <div class="w-[50px] text-center">
                        <span>{{ cell.unit }}</span>
                    </div>
                </div>
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { useCellsGroupsStore } from '@/stores/CellsGroupsStore.js'
import AppTable from '@/components/ui/tables/AppTable.vue'

const cellsStore = useCellsGroupsStore()

const cells = await cellsStore.getCells()

const TASK_NAME_PREFIX = '.task'
const getRouteName = (url) => 'manufacture.cell.' + url.replace('/', '.') + TASK_NAME_PREFIX

console.log(cells)

// const cellsData = cells.map(cell => {
//     return [cell.code1C, cell.name, cell.no, cell.unit, 'Нет данных']
// })
//
// const cellsTableData = {}
// cellsTableData.headers = ['Код 1С', 'Название ПЯ', 'Номер ПЯ', 'Ед. произв-сти', 'Норма']
// cellsTableData.data = cellsData
</script>

<style scoped></style>
