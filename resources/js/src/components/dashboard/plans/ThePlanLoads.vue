<template>
    I am the plan Loads
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import type { IPlanLoad } from '@/types'

import { usePlansStore } from '@/stores/PlansStore.ts'

const DEBUG = true

const planStore = usePlansStore()

const planLoads = ref<IPlanLoad[]>([])

planLoads.value = await planStore.getPlanLoads()

DEBUG && console.log('planLoads: ', planLoads.value)

</script>

<style scoped>

</style>
