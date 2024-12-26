<template>
    <div>
        <div v-for="cellsGroup in cellsGroups" :key="cellsGroup.id">
            <TheGroupBag
                :cellsGroup="cellsGroup"
            />
        </div>
    </div>
</template>

<script setup>
import {useCellsGroupsStore} from '@/src/stores/CellsGroupsStore.js'
import TheGroupBag from '@/src/components/dashboard/manufacture/groups/TheGroupBag.vue'

const cellsGroupsStore = useCellsGroupsStore()
const cellsGroups = await cellsGroupsStore.getCellsGroups()

console.log(cellsGroups)

</script>

<style scoped>

</style>
