<template>
    <div class="logo-container">
        <div><span class="text-xm font-semibold pr-2 pl-2">{{ formattedTime }}</span></div>
        <div><span class="text-xm font-semibold pr-2 pl-2">{{ currUser.name }}</span></div>
    </div>

</template>

<script setup>
import {computed, watch, ref, onMounted, onUnmounted} from 'vue'
import {useUserStore} from "@/src/stores/UserStore";
// Вычисляем время
const formattedTime = ref('');

onMounted(() => {
    const interval = setInterval(() => {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        formattedTime.value = `${hours}:${minutes}:${seconds}`;
    }, 1000);

    onUnmounted(() => clearInterval(interval));
});

const user = useUserStore();
const currUser = user.getUser()

</script>

<style scoped>
.logo-container {
    @apply h-full w-full flex justify-end items-center;
    height: var(--header-height);
}
</style>
