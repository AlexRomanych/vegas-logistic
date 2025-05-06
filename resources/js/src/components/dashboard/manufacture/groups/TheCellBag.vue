<template>
    <div class="ml-4">

        <router-link :to="{name: routeName}">

            <span>{{ 'ПЯ №' + (cell.no / 100).toFixed(2).slice(2) + ' - ' + cell.name}}</span>

        </router-link>


    </div>
</template>

<script setup>
const props = defineProps({
    cell: {
        type: Object,
        required: true
    }
})

const routeName = 'error.404'
// const routeName = 'manufacture.cell.' + props.cell.url.replace('/', '.')

</script>

<style scoped>

</style>
