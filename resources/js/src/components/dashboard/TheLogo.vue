<template>
    <div class="logo-container">
        <div class="w-full flex justify-center">

            <AppLabel
                align="center"
                class="cursor-pointer"
                text="Назад"
                text-size="mini"
                type="dark"
                width="w-[70px]"
                @click="navigateBack"
            />

            <AppLabel
                :key="$route.fullPath"
                :text="pageTitle"
                align="center"
                textSize="huge"
                type="dark"
                width="w-full"
            />

            <AppLabel
                align="center"
                class="cursor-pointer"
                text="Вперед"
                text-size="mini"
                type="dark"
                width="w-[70px]"
                @click="navigateForward"

            />

        </div>
        <div class="h-full flex justify-end items-center">
            <div class="mr-[-8px] ml-[2px]">🕘</div>
            <div><span class="chrono-field">{{ formattedTime }}</span><span class="divider">|</span></div>
            <div><span class="chrono-field">{{ formattedWeek }}</span><span class="divider">|</span></div>
            <!--<div><span class="chrono-field">{{ formattedDay }}</span><span class="divider">|</span></div>-->
            <div class="mr-[-8px] ml-[2px]">📅</div>
            <div><span class="chrono-field">{{ formattedDate }}</span><span class="divider">|</span></div>
            <!--<div class="user-icon">-->
            <UserCircleIcon class="user-icon"/>
            <!--</div>-->
            <div><span class="chrono-field">{{ currUser.name }}</span></div>
            <!--        <div id="data-status-indicator" class="mr-2 ml-2"></div>-->
        </div>
    </div>

</template>

<script setup>
import { computed, watch, ref, onMounted, onUnmounted } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useUserStore } from '@/stores/UserStore'

import { getDayOfWeek, getWeekNumber } from '@/app/helpers/helpers_date.js'

import { UserCircleIcon } from '@heroicons/vue/24/solid'

import AppLabel from '@/components/ui/labels/AppLabel.vue'

const router = useRouter()
const route = useRoute()
// const pageTitle = computed(() => route.meta.title ? route.meta.title : 'The Page')
const pageTitle = computed(() => route.meta.title ? route.meta.title : '')

// console.log(route.meta)
// console.log(route)

// Вычисляем время
const formattedTime = ref('')
const formattedDate = ref('')
const formattedWeek = ref('')
const formattedDay = ref('')

const getTime = () => {
    const now = new Date()
    const hours = now.getHours().toString().padStart(2, '0')
    const minutes = now.getMinutes().toString().padStart(2, '0')
    const seconds = now.getSeconds().toString().padStart(2, '0')
    formattedTime.value = `${hours}:${minutes}:${seconds}`
}

const getDate = () => {
    const now = new Date()
    const day = now.getDate().toString().padStart(2, '0')
    let month = now.getMonth()
    month++
    month = month.toString().padStart(2, '0')
    const year = now.getFullYear().toString()
    formattedDate.value = day + '.' + month + '.' + year + ` ${formattedDay.value}`
}

const getWeek = () => {
    const now = new Date()
    const week = getWeekNumber(now)
    formattedWeek.value = `${week} нед.`
}

const getDay = () => {
    formattedDay.value = getDayOfWeek().toUpperCase()
}

const navigateBack = () => {
    router.back()
}

const navigateForward = () => {
    router.forward()
}

onMounted(() => {
    getTime()
    const intervalTime = setInterval(() => getTime(), 1000) // 1 секунда

    // !!! Порядок важен
    getDay()
    getDate()
    getWeek()
    const intervalDate = setInterval(() => {
        getDate()
        getWeek()
    }, 1000 * 60 * 60) // 1 час

})

onUnmounted(() => {
    // clearInterval(intervalTime)
    // clearInterval(intervalDate)
})

// debugger
const user = useUserStore()
const currUser = ref(await user.getUser())
// debugger
// console.log('logo', currUser.value)

</script>

<style scoped>

.logo-container {
    @apply h-full w-full flex justify-center items-center;
    height: var(--header-height);
}

.chrono-field {
    @apply font-semibold text-sm pr-2 pl-2 text-slate-600 w-full text-nowrap;
}

.divider {
    @apply text-slate-600;
}

.user-icon {
    @apply text-slate-500 min-h-7 min-w-7 mr-[-6px] ml-[2px];
    /*    height: var(--header-height);*/
    /*    width: var(--header-height);*/
}

</style>
