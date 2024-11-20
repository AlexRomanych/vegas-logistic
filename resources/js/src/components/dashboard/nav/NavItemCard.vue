<template>
    I am card {{groupID}}
</template>

<script setup>
const props = defineProps({
    groupID: Number,
})
console.log(props.groupID)

</script>

<style scoped>

</style>
