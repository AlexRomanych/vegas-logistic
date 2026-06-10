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

import { computed, ref } from 'vue'
import { useUserStore } from '@/stores/UserStore'

interface IMenuItem {
    name: string,
    path: string,
    shown: boolean,
    isActive: boolean
}

import NavItemCard from '@/components/dashboard/nav/NavItemCard.vue'

const userStore = useUserStore()
const isAdmin = computed<boolean>(() => userStore.hasAdminRole())

const menuItems = ref<IMenuItem[]>([
    {name: 'Список процессов', path: 'business.processes.list', shown: true, isActive: true},
    {name: 'Добавить дефолтного клиента', path: 'business.processes.default.set', shown: isAdmin.value, isActive: isAdmin.value},
    // {name: 'План Сборки', path: 'plan.assembly', shown: true, isActive: false},
    // {name: 'План Раскроя', path: 'p  lan.cutting', shown: true, isActive: false},
    // {name: 'План Швейки', path: 'plan.sewing', shown: true, isActive: false},
    // {name: 'Загрузка Плана', path: 'plan.upload', shown: true, isActive: true},
])




// if (userStore.hasAdminRole()) {
//     menuItems.value.push({
//         name    : 'Добавить дефолтный процесс',
//         path    : 'business.processes.default.set',
//         shown   : true,
//         isActive: true,
//     })
// }



// menuItems = menuItems.map((item, index) => ({...item, id: index, shown: true, isActive: true}))


</script>

<style scoped>

</style>
