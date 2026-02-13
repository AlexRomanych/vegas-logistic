<template>
    <AppLabelTS
        v-if="renderObject.show"
        :align="header ? renderObject.headerAlign : renderObject.dataAlign"
        :class="renderObject.class || ''"
        :color="color"
        :height="renderObject.height"
        :text="header ? (renderObject.header as string): renderObject.data!(arg)"
        :text-size="header ? renderObject.headerTextSize : renderObject.dataTextSize"
        :title="title"
        :type="colorType"
        :width="renderObject.width"
        rounded="rounded-[3px]"
    />
</template>

<script lang="ts" setup>


import type { IRenderDataItem } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { computed } from 'vue'

interface IProps {
    renderObject: IRenderDataItem
    headerType?: IColorTypes
    arg?: any
    header?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    headerType: 'primary',
    arg:        undefined,
    header:     false,
})


const colorType = computed(() => {
    if (props.header) {
        return props.renderObject.headerType
            ? typeof props.renderObject.headerType === 'function'
                ? props.renderObject.headerType()
                : props.renderObject.headerType
            : props.headerType
    }
    return typeof props.renderObject.type === 'function'
        ? props.renderObject.type(props.arg)
        : props.renderObject.type
})


const color = computed(() => props.renderObject.color && props.renderObject.color(props.arg))
const title = computed(() => {
    return typeof props.renderObject.title === 'function'
        ? props.renderObject.title(props.arg)
        : props.renderObject.title
})

const handleClick = () => props.renderObject.click && props.renderObject.click(props.arg)

</script>

<style scoped>

</style>
