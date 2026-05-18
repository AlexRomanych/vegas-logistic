<template>
    <div class="m-5">
    <AppProgressBar
        :progress="progress"


    />
    </div>
</template>

<script lang="ts" setup>

import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import { ref } from 'vue'


const progress = ref(0)

const interval = setInterval(() => {
    progress.value++;
    console.log(`Итерация №${progress.value}`);

    if (progress.value === 101) {
        clearInterval(interval);
        console.log('Таймер остановлен');
    }
}, 500);

</script>

<style scoped>

</style>
