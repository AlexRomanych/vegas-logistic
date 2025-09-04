<template>

    <div :class="width" class="flex flex-col ml-0.5 mr-0.5 mt-2">
        <label v-if="label"
               :class="['input-label', textColor, labelTextSizeClass ]"
               :for="id">{{ label }}
        </label>
        <div
            :class="[width, height, backgroundColor, borderColor, currentTextColor, textSizeClass, semibold, horizontalAlign]"
            class="flex flex-col justify-center app-div"
            @click="labelClick"
        >
            {{ stateChar }}
        </div>

    </div>

</template>


<script lang="ts" setup>
import { computed, ref } from 'vue'

import type { IHorizontalAlign } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.js'
import type { IFontsType } from '@/app/constants/fontSizes.js'

import {
    getColorClassByType,
    getTextColorClassByType,
    getFontSizeClass,
    getHorizontalAlign
} from '@/app/helpers/helpers.js'


interface IProps {
    id?: string
    label?: string
    width?: string
    height?: string
    align?: IHorizontalAlign
    textSize?: IFontsType
    labelTextSize?: IFontsType
    bold?: boolean
    type?: IColorTypes
    state?: boolean
    stateTrueChar?: string
    stateFalseChar?: string
    stateTrueType?: IColorTypes
    stateFalseType?: IColorTypes
}

const props = withDefaults(defineProps<IProps>(), {
    id: 'id-label-checkbox',
    label: 'label',
    width: 'w-[200px]',
    height: 'h-[30px]',
    align: 'center',
    textSize: 'normal',
    labelTextSize: 'mini',
    bold: true,
    type: 'dark',
    state: false,
    stateTrueChar: '✓',
    stateFalseChar: '✗',
    stateTrueType: 'success',
    stateFalseType: 'danger',
})

const emits = defineEmits<{
    (e: 'labelClick', value: boolean): void
    // (e: 'labelClick', value: string): void
}>()


const state = computed(() => props.state)
// '✓' : '✗'
const stateChar = computed(() => state.value ? props.stateTrueChar : props.stateFalseChar)
const stateType = computed(() => state.value ? props.stateTrueType : props.stateFalseType)

const currentColorIndex = 500       // задаем основной индекс палитры tailwinds

const textSizeClass = computed(() => ref(getFontSizeClass(props.textSize)))
const labelTextSizeClass = computed(() => getFontSizeClass(props.labelTextSize))
const semibold = computed(() => props.bold ? 'font-semibold' : '')
const horizontalAlign = computed(() => getHorizontalAlign(props.align))

const currentTextColor = computed(() => (getTextColorClassByType(stateType.value)))
const backgroundColor = computed(() => getColorClassByType(stateType.value, 'bg', currentColorIndex))
const borderColor = computed(() => getColorClassByType(stateType.value, 'border', currentColorIndex))

// __ Вычисляем размер и тип шрифта для label
const currentColor = computed(() => getColorClassByType('dark')).value + currentColorIndex
// const currentColor = computed(() => getColorClassByType(stateType.value)).value + currentColorIndex
const textColor = computed(() => ('text' + currentColor).replace(currentColorIndex.toString(), (currentColorIndex/* + 200*/).toString()))


// __ Обрабатываем нажатие на label
const labelClick = (e: Event) => {
    const target = e.target as HTMLElement
    emits('labelClick', !stateType.value)
    // emits('labelClick', target.innerText)
    // console.log(target.innerText)
}


</script>

<style scoped>
.app-label {
    @apply p-1 m-0.5 border rounded-lg focus:outline-none focus:ring-2;
}

.input-label {
    @apply font-semibold ml-2 mb-0.5 mt-2;
}

.app-div {
    @apply p-1 border rounded-lg focus:outline-none focus:ring-2;
}

</style>
