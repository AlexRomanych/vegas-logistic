<template>
    <span class="nav-item-name">{{ groupName }}</span>
</template>

<script lang="ts" setup>

interface IProps {
    groupName: string
}

defineProps<IProps>()

</script>

<style scoped>
.nav-item-name {
    @apply text-lg font-semibold;
}
</style>
