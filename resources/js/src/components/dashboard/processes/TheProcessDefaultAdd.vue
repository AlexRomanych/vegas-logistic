<template>
    <div class="m-5 p-5 bg-slate-500 text-white cursor-pointer" @click="sendRequest">
        Добавить дефолтного клиента
    </div>
</template>

<script lang="ts" setup>
// import { ref } from 'vue'

import { useBusinessProcessesStore } from '@/stores/BusinessProcessesStore'
const businessProcessesStore = useBusinessProcessesStore()



const sendRequest = async () => {
    const result = await businessProcessesStore.setDefaultClient()
    console.log('result: ', result)
}

</script>

<style scoped>

</style>
