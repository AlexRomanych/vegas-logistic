<template>
I am Blocks
</template>

<script setup lang="ts">
import type { IRenderOrder } from '@/types'

interface IProps {
    order: IRenderOrder
    id: number
}

const props = defineProps<IProps>()
</script>

<style scoped>

</style>
