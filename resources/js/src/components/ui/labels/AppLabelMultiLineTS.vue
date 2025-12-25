<template>
    <div
        :class="[
            width,
            labelHeight,
            backgroundColor,
            borderColor,
            currentTextColor,
            textSizeClass,
            semibold,
            horizontalAlign,
            roundedCSS,
        ]"
        class="flex flex-col m-0.5 app-label justify-center"
        @click="labelClick"
    >
        <div v-for="(text, idx) in textArray" :key="idx">
            {{ text }}
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed, /*ref*/ } from 'vue'

import type { IHorizontalAlign } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import type { IFontsType } from '@/app/constants/fontSizes.ts'

import {
    getColorClassByType,
    getTextColorClassByType,
    getFontSizeClass,
    getHorizontalAlign, getRoundedClass,
} from '@/app/helpers/helpers.js'

import { getDigitPart } from '@/app/helpers/helpers_lib.ts'

interface IProps {
    text?: string | string[]
    type?: IColorTypes
    width?: string
    height?: string
    textSize?: IFontsType
    bold?: boolean
    align?: IHorizontalAlign
    rounded?: string
}

const props = withDefaults(defineProps<IProps>(), {
    text: 'Enter...',
    type: 'dark',
    width: 'w-[200px]',
    height: 'h-[25px]',
    textSize: 'normal',
    bold: true,
    align: 'left',
    rounded: 'rounded-lg',
})

const emits = defineEmits<{
    (e: 'labelClick', text: string): void
}>()

// __ newline - разделитель строк в тексте
const LINE_SEPARATOR = '&nl'

const currentColorIndex = 500 // задаем основной индекс палитры tailwinds

const textSizeClass = computed(() => getFontSizeClass(props.textSize))
const semibold = computed(() => (props.bold ? 'font-semibold' : ''))
const horizontalAlign = computed(() => getHorizontalAlign(props.align))

const currentTextColor = computed(() => getTextColorClassByType(props.type))
const backgroundColor = computed(() => getColorClassByType(props.type, 'bg', currentColorIndex))
const borderColor = computed(() => getColorClassByType(props.type, 'border', currentColorIndex))

const roundedCSS = computed(() => getRoundedClass(props.rounded))

// __ Обрабатываем нажатие на label
const labelClick = (e: Event) => {
    const target = e.target as HTMLElement
    emits('labelClick', target.innerText)
}

// __ Возвращаем количество строк в массиве
const getLinesAmount = (inTextData: string | string[]): number | never => {
    if (typeof inTextData === 'string') {
        return 1
    } else if (Array.isArray(inTextData)) {
        return inTextData.length
    }

    return inTextData
    // const exhaustiveCheck: never = inTextData
    // return exhaustiveCheck
}

// __ Получаем высоту Label в зависимости от количества строк
const getLabelHeight = (inTextData: string[]) => {
    let linesAmount = getLinesAmount(inTextData)

    if (Array.isArray(props.text) && props.text.length === 2 && props.text[1] === '') linesAmount++

    const height = parseInt(getDigitPart(props.height)) * linesAmount
    return `h-[${height}px]`
}

// ___ Преобразуем данные в массив
// ___ Если первый элемент массива - не пустая строка, а вторая - пустая,
// ___ и массив из 2-х элементов, то мы получаем массив из одной строки
// ___ Также возвращаем массив, если есть сепаратор строки - '&nl'
const getTextArray = (inTextData: string | string[]) => {
    if (typeof inTextData === 'string') {
        if (inTextData.toLowerCase().includes(LINE_SEPARATOR.toLowerCase())) {
            inTextData = inTextData.replaceAll(' ' + LINE_SEPARATOR + ' ', LINE_SEPARATOR)
            inTextData = inTextData.replaceAll(' ' + LINE_SEPARATOR, LINE_SEPARATOR)
            inTextData = inTextData.replaceAll(LINE_SEPARATOR + ' ', LINE_SEPARATOR)
            inTextData = inTextData.replaceAll('  ', ' ')
            inTextData = inTextData.replaceAll('  ', ' ')

            return inTextData.split(LINE_SEPARATOR)
        }
        return [inTextData]
    }

    if (Array.isArray(inTextData)) {
        if (inTextData.length === 1) return inTextData
        if (inTextData.length === 2 && inTextData[1] === '') return [inTextData[0]]
    }
    return inTextData
}

// __ Получаем массив строк для вывода
const textArray = computed(() => getTextArray(props.text))

// __ Получаем tailwind класс высоты Label в зависимости от количества строк
const labelHeight = computed(() => getLabelHeight(textArray.value))
</script>

<style scoped>
@reference "@css/app.css";

.app-label {
    @apply flex flex-col justify-center
    p-1 m-0.5
    border focus:outline-none focus:ring-2;
}
</style>
