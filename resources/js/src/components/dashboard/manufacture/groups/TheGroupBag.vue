<template>

    <div
        class="flex flex-col ml-2 mt-2 p-2 border-2 border-slate-400 rounded-lg text-slate-600 w-[500px]"
        @click="gotoGroup(cellsGroup.url)"
    >
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
import { useRouter } from 'vue-router'

import TheCellBag from '@/components/dashboard/manufacture/groups/TheCellBag.vue'

const props = defineProps({
    cellsGroup: {
        type: Object,
        required: true
    }
})

const router = useRouter()

// __ Переход на страницу группы
const gotoGroup = (url) => {
    url && router.push({name: url})
}


</script>

<style scoped>

</style>
