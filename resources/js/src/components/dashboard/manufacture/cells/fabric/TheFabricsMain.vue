<template>

    <div class="flex flex-wrap">
        <div v-for="group in menuItems" :key="group.id">

            <NavItemCard
                :groupMenuItem="group"
            />

        </div>
    </div>

</template>

<script setup>
import NavItemCard from '/resources/js/src/components/dashboard/nav/NavItemCard.vue'

/*
// attract: Список ПС
const showFabrics = {

    name: 'Список ПС',
    path: 'manufacture.cell.fabrics.show',
    shown: true,
    isActive: true,
}


// attract: Буффер
const bufferFabrics = {
    name: 'Буфер',
    path: 'manufacture.cell.fabrics.buffer',
    shown: true,
    isActive: true,
}

// attract: Загрузка ПС из 1С
const uploadFabrics = {
    name: 'Загрузка ПС из 1С',
    path: 'manufacture.cell.fabrics.upload',
    shown: true,
    isActive: true,
}
*/


let menuItems = [
    {name: 'Список ПС', path: 'manufacture.cell.fabrics.show'},
    {name: 'Буфер', path: 'manufacture.cell.fabrics.buffer'},
    {name: 'Загрузка расхода ПС', path: 'manufacture.cell.fabrics.expense.upload'},
    {name: 'Загрузка списка рисунков ПС', path: 'manufacture.cell.fabrics.pictures.upload'},
    {name: 'Загрузка списка ПС', path: 'manufacture.cell.fabrics.upload'},
]

menuItems = menuItems.map((item, index) => ({...item, id: index, shown: true, isActive: true}))


</script>

<style scoped>

</style>
