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
import { ref } from 'vue'
import { useUserStore } from '@/stores/UserStore'
import NavItemCard from '@/components/dashboard/nav/NavItemCard.vue'

const userStore = useUserStore()
interface IMenuItem {
    name: string,
    path: string,
    shown: boolean,
    isActive: boolean
}


const menuItems = ref<IMenuItem[]>([
    {name: 'Управление планом Сборки (Календарь)', path: 'manufacture.cell.assembly.plan.manage', shown: true, isActive: true},
    {name: 'Управление планом Сборки (Список)', path: 'manufacture.cell.assembly.plan.manipulate', shown: true, isActive: true},
    {name: 'Группы моделей для сортировки', path: 'manufacture.cell.assembly.model.manufacture.groups.show', shown: true, isActive: true},
])

if (userStore.hasAdminRole()) {
    menuItems.value.push({
        name    : 'Тест',
        path    : 'manufacture.cell.assembly.test',
        shown   : true,
        isActive: true,
    })
}

</script>

<style scoped>

</style>
