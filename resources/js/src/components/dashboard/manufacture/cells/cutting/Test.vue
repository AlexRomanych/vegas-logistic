<template>

    <div class="m-5 p-5 bg-slate-500 text-white cursor-pointer" @click="sendRequest">
        ТЕСТ
    </div>

    <div class="m-5">
    <AppProgressBar
        :progress="progress"
    />
    </div>
</template>

<script lang="ts" setup>

import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import { ref } from 'vue'
import { useCuttingStore } from '@/stores/CuttingStore.ts'

const cuttingStore = useCuttingStore()

const progress = ref(0)

const interval = setInterval(() => {
    // progress.value++;
    // console.log(`Итерация №${progress.value}`);
    //
    // if (progress.value === 101) {
    //     clearInterval(interval);
    //     console.log('Таймер остановлен');
    // }
}, 500);


const sendRequest = async () => {
    const result = await cuttingStore.test()
    console.log('result: ', result)
}

</script>

<style scoped>

</style>
