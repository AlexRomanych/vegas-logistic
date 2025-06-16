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


let menuItems = [
    {name: 'Управление СЗ', path: 'manufacture.cell.fabric.tasks.manage', shown: true, isActive: true},
    {name: 'Выполнение СЗ', path: 'manufacture.cell.fabric.tasks.execute', shown: true, isActive: true},
    {name: 'Календарь ПС', path: 'manufacture.cell.fabric.tasks.calendar', shown: true, isActive: true},
    {name: 'Расход ПС', path: 'manufacture.cell.fabrics.expense', shown: true, isActive: true},
    {name: 'Загрузка расхода ПС', path: 'manufacture.cell.fabrics.expense.upload', shown: true, isActive: true},
    {name: 'Учет ПС на стежке', path: 'manufacture.cell.fabrics.movement', shown: true, isActive: true},
    {name: 'Список ПС', path: 'manufacture.cell.fabrics.show', shown: true, isActive: true},
    {name: 'Список рисунков ПС', path: 'manufacture.cell.fabric.pictures.show', shown: true, isActive: true},
    {name: 'Стегальные машины', path: 'manufacture.cell.fabrics.machines', shown: true, isActive: true},
    {name: 'Движение СЗ', path: 'manufacture.cell.fabric.tasks.movement', shown: false, isActive: true},
    {name: 'Загрузка списка рисунков ПС', path: 'manufacture.cell.fabrics.pictures.upload', shown: true, isActive: false},
    {name: 'Загрузка списка ПС', path: 'manufacture.cell.fabrics.upload', shown: true, isActive: false},
    // {name: 'Буфер', path: 'manufacture.cell.fabrics.buffer'},
]

// menuItems = menuItems.map((item, index) => ({...item, id: index, shown: true, isActive: true}))


</script>

<style scoped>

</style>
