<template>
    <div class="flex justify-center w-full mt-6">
        <AppLabelTS
            align="center"
            class="cursor-pointer"
            height="h-24"
            text="Управление планом Сборочного цеха"
            text-size="huge"
            type="stone"
            width="w-1/2"
            @click="$router.push(
                {
                    name: 'plan.business.process.node',
                    params: {
                        businessProcessId: BUSINESS_PROCESSES.ORDER_MOVING.ID,
                        businessProcessNodeId: BUSINESS_PROCESS_NODES.ASSEMBLY.ID,
                    }
                })"
        />
    </div>
</template>

<script lang="ts" setup>

import { BUSINESS_PROCESS_NODES, BUSINESS_PROCESSES } from '@/app/constants/business_processes.ts'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

</script>

<style scoped>

</style>
