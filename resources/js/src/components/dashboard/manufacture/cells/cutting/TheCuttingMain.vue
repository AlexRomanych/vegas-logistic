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
    // { name: 'Тест', path: 'manufacture.cell.cutting.test', shown: true, isActive: true },
    { name: 'Управление планом РЦ', path: 'manufacture.cell.cutting.plan.manage', shown: true, isActive: true },
    // { name: 'Управление СЗ', path: 'manufacture.cell.cutting.plan.manage', shown: true, isActive: true },
    { name: 'Выполнение СЗ', path: 'manufacture.cell.cutting.tasks.execute', shown: true, isActive: true },
    { name: 'Архив СЗ', path: 'manufacture.cell.cutting.tasks.archive', shown: true, isActive: true },
    { name: 'Учет ПС на раскрое', path: 'manufacture.cell.cutting.fabrics.movement', shown: true, isActive: true, },
    { name: 'Статусы движения СЗ', path: 'manufacture.cell.cutting.task.statuses', shown: true, isActive: true },
    { name: 'Типовые операции', path: 'manufacture.cell.cutting.operations', shown: true, isActive: true },
    { name: 'Схемы типовых операций', path: 'manufacture.cell.cutting.operation.schemas', shown: true, isActive: true },
    { name: 'Трудозатраты + процедуры раскроя для моделей', path: 'manufacture.cell.cutting.operation.models', shown: true, isActive: true },
    { name: 'Процедуры расчета раскроя', path: 'manufacture.cell.cutting.procedures', shown: true, isActive: true },
    // {name: 'Управление СЗ', path: 'manufacture.cell.fabric.tasks.manage', shown: true, isActive: true},
])

if (userStore.hasAdminRole()) {
    menuItems.value.push({
        name: 'Тест',
        path: 'manufacture.cell.cutting.test',
        shown: true,
        isActive: true,
    })
}


// menuItems = menuItems.map((item, index) => ({...item, id: index, shown: true, isActive: true}))


</script>

<style scoped>

</style>
