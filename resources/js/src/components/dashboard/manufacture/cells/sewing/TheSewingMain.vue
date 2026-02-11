<template>

    <div class="flex flex-wrap">
        <div v-for="group in menuItems" :key="group.path">

            <NavItemCard
                :groupMenuItem="group"
            />

        </div>
    </div>

</template>

<script lang="ts" setup>

interface IMenuItem {
    name: string,
    path: string,
    shown: boolean,
    isActive: boolean
}

import NavItemCard from '@/components/dashboard/nav/NavItemCard.vue'


let menuItems: IMenuItem[] = [
    { name: 'Тест', path: 'manufacture.cell.sewing.test', shown: true, isActive: true },
    { name: 'Управление планом ШЦ', path: 'manufacture.cell.sewing.plan.manage', shown: true, isActive: true },
    { name: 'Управление СЗ', path: 'manufacture.cell.sewing.plan.manage', shown: true, isActive: true },
    { name: 'Выполнение СЗ', path: 'manufacture.cell.sewing.tasks.execute', shown: true, isActive: true },
    { name: 'Статусы движения СЗ', path: 'manufacture.cell.sewing.task.statuses', shown: true, isActive: true },
    { name: 'Типовые операции', path: 'manufacture.cell.sewing.operations', shown: true, isActive: true },
    { name: 'Схемы типовых операций', path: 'manufacture.cell.sewing.operation.schemas', shown: true, isActive: true },
    { name: 'Трудозатраты моделей', path: 'manufacture.cell.sewing.operation.models', shown: true, isActive: true },
    // {name: 'Управление СЗ', path: 'manufacture.cell.fabric.tasks.manage', shown: true, isActive: true},
]
// menuItems = menuItems.map((item, index) => ({...item, id: index, shown: true, isActive: true}))


</script>

<style scoped>

</style>
