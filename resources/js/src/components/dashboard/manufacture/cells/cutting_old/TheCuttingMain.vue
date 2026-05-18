<template>

    <div class="flex flex-wrap">
        <div v-for="group in menuItems" :key="group.path">

            <NavItemCard
                :groupMenuItem="group"
            />

        </div>
    </div>

</template>

<script setup lang="ts">

interface IMenuItem {
    name: string,
    path: string,
    shown: boolean,
    isActive: boolean
}

import NavItemCard from '@/components/dashboard/nav/NavItemCard.vue'

let menuItems: IMenuItem[] = [
    {name: 'Учет ПС на раскрое', path: 'manufacture.cell.cutting.fabrics.movement', shown: true, isActive: true,},
    {name: 'Управление планом Раскроя', path: 'manufacture.cell.cutting.plan.manage', shown: true, isActive: true,},
// {name: 'Выполнение СЗ', path: 'manufacture.cell.fabric.tasks.execute', shown: true, isActive: true,},
]
// menuItems = menuItems.map((item, index) => ({...item, id: index, shown: true, isActive: true}))

</script>

<style scoped>

</style>



