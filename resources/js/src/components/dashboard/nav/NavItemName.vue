<template>
    <span class="nav-item-name">{{ groupName }}</span>
</template>

<script setup>

const props = defineProps({
    groupName: {
        type: String,
        required: true
    }
})

</script>

<style scoped>
  .nav-item-name {
      @apply text-lg
  }
</style>
