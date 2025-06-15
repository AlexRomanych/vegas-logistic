<template>
    <!--    <div>I am Menu {{$route.params.groupId}}</div>-->
    <div class="flex flex-wrap">
        <div v-for="groupMenuItem in group.items" :key="groupMenuItem.id">
            <NavItemCard :groupMenuItem="groupMenuItem" />
        </div>
    </div>
</template>

<script setup>
import { useMenuStore } from '@/stores/MenuStore.js'
import NavItemCard from '@/components/dashboard/nav/NavItemCard.vue'
import { useRouter } from 'vue-router'
import { ref } from 'vue'

const menuStore = useMenuStore()
const menu = menuStore.menu

const router = useRouter()
const groupId = +router.currentRoute.value.params.groupId

// Находим группу по заданному id
const group = menu.find((item) => item.group.id === groupId)

// console.log(groupId)
// console.log(menu)
// console.log('Group', group)
</script>

<style scoped></style>
