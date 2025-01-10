<template>
    <div class="ml-16">

        <div class="flex justify-start items-center">
            <AppLabel
                :bold="true"
                :text="cell.line.size"
                align="center"
                textSize="small"
                type="dark"
                width="w-[100px]"
            />

            <AppLabel
                :bold="true"
                :text="cell.line.model"
                align="left"
                textSize="small"
                type="dark"
                width="w-[200px]"
            />

            <AppLabel
                :bold="true"
                :text="cell.amount.toString()"
                align="center"
                textSize="small"
                type="dark"
                width="w-[40px]"
            />

            <AppLabel
                :bold="true"
                :text="(cell.norm).toFixed(3)"
                align="center"
                textSize="small"
                type="primary"
                width="w-[60px]"
            />

        </div>

    </div>
</template>

<script setup>
import AppLabel from "/resources/js/src/components/ui/labels/AppLabel.vue"

const props = defineProps({
    cell: {
        type: Object,
        required: true
    }


})
</script>

<style scoped>

</style>
