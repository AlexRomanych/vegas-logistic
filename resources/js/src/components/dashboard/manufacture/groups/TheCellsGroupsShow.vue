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

import {useCellsGroupsStore} from '/resources/js/src/stores/CellsGroupsStore.js'
import TheGroupBag from '/resources/js/src/components/dashboard/manufacture/groups/TheGroupBag.vue'

const cellsGroupsStore = useCellsGroupsStore()
const cellsGroups = await cellsGroupsStore.getCellsGroups()

console.log('cellsGroups: ', cellsGroups)

</script>

<style scoped>

</style>
