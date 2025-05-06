<template>

    <div class="flex flex-col ml-2 mt-2 p-2 border-2 border-slate-400 rounded-lg text-slate-600 w-[500px]">
        <div>
            <span class="text-lg font-semibold">{{ cellsGroup.name }}</span>
        </div>

        <div>
            <TheCellBag
                v-for="cell in cellsGroup.cell_items"
                :key="cell.id"
                :cell="cell"
            />
        </div>

    </div>
</template>

<script setup>

import TheCellBag from '/resources/js/src/components/dashboard/manufacture/groups/TheCellBag.vue'

const props = defineProps({
    cellsGroup: {
        type: Object,
        required: true
    }
})

</script>

<style scoped>

</style>
