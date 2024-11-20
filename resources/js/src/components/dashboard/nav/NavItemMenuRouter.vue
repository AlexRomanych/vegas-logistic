<template>
    <NavItemCard :groupID="groupID"/>
    <!--<div>I am Menu {{$route.params.groupId}}</div>-->
</template>

<script setup>
// Здесь делаем импорт другого элемента, потому что
// vue router кэширует маршруты
// такой способ позволит ререндерить страницы,
// пототу что будет вызываться хук onMounted()
import {useRoute} from "vue-router";
import NavItemCard from "@/src/components/dashboard/nav/NavItemCard.vue"
import NavItemMenu from "@/src/components/dashboard/nav/NavItemCard.vue"

const route = useRoute()
const groupID = route.params.groupId * 1

</script>

<style scoped>

</style>
