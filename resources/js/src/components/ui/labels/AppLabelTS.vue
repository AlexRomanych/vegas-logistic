<template>
    <div
        :class="[
            width,
            height,
            backgroundColor,
            borderColor,
            currentTextColor,
            textSizeClass,
            semibold,
            horizontalAlign,
            rounded
        ]"
        :title="title"
        :style="color ? {'background-color': color} : ''"
        class="flex flex-col justify-center app-label"
        @click="labelClick"
    >
        {{ text }}
    </div>
</template>


<script lang="ts" setup>
import { computed } from 'vue'

import type { IColorTypes } from '@/app/constants/colorsClasses.js'
import type { IFontsType } from '@/app/constants/fontSizes.js'

import {
    getColorClassByType,
    getTextColorClassByType,
    getFontSizeClass, getHorizontalAlignText
} from '@/app/helpers/helpers.js'
import type { IHorizontalAlign } from '@/types'

interface IProps {
    text?: string
    type?: IColorTypes
    width?: string
    height?: string
    textSize?: IFontsType
    bold?: boolean
    align?: IHorizontalAlign
    title?: string
    rounded?: string
    color?: string
}


const props = withDefaults(defineProps<IProps>(), {
    text: 'Enter...',
    type: 'dark',
    width: 'w-[200px]',
    height: 'h-[30px]',
    textSize: 'normal',
    bold: true,
    align: 'left',
    title: '',
    rounded: 'rounded-lg',
    color: '',
})

const emits = defineEmits<{
    (e: 'labelClick', text: string): void
}>()

// __ Обрабатываем нажатие на label
const labelClick = (e: Event) => {
    const target = e.target as HTMLElement
    emits('labelClick', target.innerText)
}

const currentColorIndex = 500       // задаем основной индекс палитры tailwinds
const backgroundColor = computed(() => getColorClassByType(props.type, 'bg', currentColorIndex))
const borderColor = computed(() => getColorClassByType(props.type, 'border', currentColorIndex))
const currentTextColor = computed(() => getTextColorClassByType(props.type))
const textSizeClass = computed(() => getFontSizeClass(props.textSize))
const horizontalAlign = computed(() => getHorizontalAlignText(props.align))
const semibold = computed(() => props.bold ? 'font-semibold' : '')

</script>

<style scoped>
.app-label {
    @apply
    p-1 m-0.5
    border focus:outline-none focus:ring-2;
}

</style>
