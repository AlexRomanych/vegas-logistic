<template>
    <!--    <div>I am Menu {{$route.params.groupId}}</div>-->
    <div class="flex flex-wrap">
        <div v-for="groupMenuItem in group.items" :key="groupMenuItem.id">

            <NavItemCard
                :groupMenuItem="groupMenuItem"
            />

        </div>
    </div>
</template>

<script lang="ts" setup>
import { useRouter } from 'vue-router'
import { useMenuStore } from '@/stores/MenuStore.ts'

import NavItemCard from '@/components/dashboard/nav/NavItemCard.vue'

const menuStore = useMenuStore()
const menu      = menuStore.menu

const router  = useRouter()
const groupId = +router.currentRoute.value.params.groupId

// Находим группу по заданному id
const group = menu.find((item) => item.group.id === groupId)
</script>

<style scoped></style>
