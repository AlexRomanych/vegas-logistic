<template>

</template>

<script setup lang="ts">

import { useFabricsStore } from '@/stores/FabricsStore.js'



const fabricsStore = useFabricsStore()

const tuningTime = fabricsStore.getFabricsPicturesTuningTime()

console.log('tuningTime:', tuningTime)


</script>

<style scoped>

</style>
