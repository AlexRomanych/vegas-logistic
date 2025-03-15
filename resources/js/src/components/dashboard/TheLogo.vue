<template>
    <div class="logo-container">
        <div class="w-full flex justify-center">
            <AppLabel
                :key="$route.fullPath"
                :text="pageTitle"
                align="center"
                textSize="huge"
                type="dark"
                width="w-full"
            />
        </div>
        <div class="h-full flex justify-end items-center">
            <div><span class="chrono-field">{{ formattedTime }}</span><span class="divider">|</span></div>
            <div><span class="chrono-field">{{ formattedWeek }}</span><span class="divider">|</span></div>
            <div><span class="chrono-field">{{ formattedDate }}</span><span class="divider">|</span></div>
            <div class="user-icon">
                <UserCircleIcon/>
            </div>
            <div><span class="chrono-field">{{ currUser.name }}</span></div>
            <!--        <div id="data-status-indicator" class="mr-2 ml-2"></div>-->
        </div>
    </div>

</template>

<script setup>
import {computed, watch, ref, onMounted, onUnmounted} from 'vue'
import {useRoute} from 'vue-router'
import {useUserStore} from "/resources/js/src/stores/UserStore";
import {getWeekNumber} from "/resources/js/src/app/helpers/helpers_date.js"
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import {UserCircleIcon} from '@heroicons/vue/24/solid'

const route = useRoute()
// const pageTitle = computed(() => route.meta.title ? route.meta.title : 'The Page')
const pageTitle = computed(() => route.meta.title ? route.meta.title : '')

// console.log(route.meta)
// console.log(route)

// Вычисляем время
const formattedTime = ref('');
const formattedDate = ref('');
const formattedWeek = ref('');

const getTime = () => {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    formattedTime.value = `${hours}:${minutes}:${seconds}`;
}

const getDate = () => {
    const now = new Date();
    const day = now.getDate().toString().padStart(2, '0')
    let month = now.getMonth()
    month++
    month = month.toString().padStart(2, '0')
    const year = now.getFullYear().toString();
    formattedDate.value = day + '.' + month + '.' + year
}

const getWeek = () => {
    const now = new Date();
    const week = getWeekNumber(now)
    formattedWeek.value = `${week} неделя`;
}

onMounted(() => {
    getTime()
    const intervalTime = setInterval(() => getTime(), 1000) // 1 секунда

    getDate()
    getWeek()
    const intervalDate = setInterval(() => {
        getDate()
        getWeek()
    }, 1000 * 60 * 60); // 1 час

});

onUnmounted(() => {
    // clearInterval(intervalTime)
    // clearInterval(intervalDate)
});

// debugger
const user = useUserStore();
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
    @apply font-semibold pr-2 pl-2 text-slate-600 w-full text-nowrap;
}

.divider {
    @apply text-slate-600;
}

.user-icon {
    @apply text-slate-500 p-2;
    height: var(--header-height);
    width: var(--header-height);
}

</style>
