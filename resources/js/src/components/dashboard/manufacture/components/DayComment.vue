<template>
    <!-- __ Комментарий к дню -->
    <AppLabelTS
        v-if="sewingDay?.comment"
        :text="sewingDay?.comment ?? ''"
        align="left"
        class="start-group"
        height="h-[50px]"
        rounded="4"
        text-size="mini"
        type="warning"
        width="min-w-[600px]"
    />

    <template v-if="showType === 'callout'">

    </template>
    <template v-else-if="showType === 'modal'">
    </template>

</template>

<script setup lang="ts">
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import type { ISewingDay } from '@/types'

interface IProps {
    sewingDay: ISewingDay
    showType?: 'none' | 'callout' | 'modal'
}

const props = withDefaults(defineProps<IProps>(), {
    showType: 'none'
})



</script>

<style scoped>

</style>
