<template>
    <AppLabelMultiLineTS
        v-if="renderObject.show"
        :align="renderObject.headerAlign"
        :text="renderObject.header"
        :text-size="renderObject.headerTextSize"
        :title="renderObject.title"
        :type="
            renderObject.headerType
            ? typeof renderObject.headerType === 'function'
            ? renderObject.headerType()
            : renderObject.headerType
            : headerType"
        :width="renderObject.width"
        :class="renderObject.class"
        rounded="4"
    />
</template>

<script setup lang="ts">

import type { IRenderDataItem } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'

interface IProps {
    renderObject: IRenderDataItem
    headerType?: IColorTypes
}

const props = withDefaults(defineProps<IProps>(), {
    headerType: 'primary'
})


</script>

<style scoped>

</style>
