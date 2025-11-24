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
    {name: 'Список процессов', path: 'business.processes.list', shown: true, isActive: true},
    // {name: 'План Сборки', path: 'plan.assembly', shown: true, isActive: false},
    // {name: 'План Раскроя', path: 'p  lan.cutting', shown: true, isActive: false},
    // {name: 'План Швейки', path: 'plan.sewing', shown: true, isActive: false},
    // {name: 'Загрузка Плана', path: 'plan.upload', shown: true, isActive: true},
]

// menuItems = menuItems.map((item, index) => ({...item, id: index, shown: true, isActive: true}))


</script>

<style scoped>

</style>
