<template>
I am a fabrics show
</template>

<script setup>
import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

const fabricsStore = useFabricsStore()
const fabrics = await fabricsStore.getFabrics()

console.log('fabrics', fabrics)

</script>

<style scoped>

</style>
