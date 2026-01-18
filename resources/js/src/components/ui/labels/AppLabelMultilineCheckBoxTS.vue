<template>

    <div :class="width" class="flex flex-col ml-0.5 mr-0.5 ">
        <label v-if="label"
               :class="['input-label mt-2', textColor, labelTextSizeClass ]"
               :for="id">{{ label }}
        </label>

        <AppLabelMultiLineTS
            :text="stateChar"
            :type="stateType"
            :rounded="rounded"
            :color="color"
            :width="width"
            :height="height"
            :textSize="textSize"
            :bold="bold"
            :align="align"
            @label-click="labelClick"
        />

    </div>

</template>


<script lang="ts" setup>
import { computed, /*ref*/ } from 'vue'

import type { IHorizontalAlign, IColorTypes, IFontsType } from '@/types'

import {
    getColorClassByType,
    getFontSizeClass,
    // getTextColorClassByType,
    // getHorizontalAlign
} from '@/app/helpers/helpers.js'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'

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
    stateTrueChar?: string | string[]
    stateFalseChar?: string | string[]
    stateTrueType?: IColorTypes
    stateFalseType?: IColorTypes
    rounded?: string
    color?: string
}

const props = withDefaults(defineProps<IProps>(), {
    id:             'id-label-checkbox',
    label:          '',
    width:          'w-[200px]',
    height:         'h-[20px]',
    align:          'center',
    textSize:       'normal',
    labelTextSize:  'mini',
    bold:           true,
    type:           'dark',
    state:          false,
    stateTrueChar:  '✓',
    stateFalseChar: '✗',
    stateTrueType:  'success',
    stateFalseType: 'danger',
    rounded:        'rounded-[4px]',
    color:          '',
})

const emits = defineEmits<{
    (e: 'labelClick', value: boolean): void
    // (e: 'labelClick', value: string): void
}>()


const state     = computed(() => props.state)

// __ '✓' : '✗'
const stateChar = computed(() => state.value ? props.stateTrueChar : props.stateFalseChar)
const stateType = computed(() => state.value ? props.stateTrueType : props.stateFalseType)

const currentColorIndex = 500       // задаем основной индекс палитры tailwinds

// const textSizeClass      = computed(() => ref(getFontSizeClass(props.textSize)))
// const semibold           = computed(() => props.bold ? 'font-semibold' : '')
// const horizontalAlign    = computed(() => getHorizontalAlign(props.align))
// const currentTextColor = computed(() => (getTextColorClassByType(stateType.value)))
// const backgroundColor  = computed(() => getColorClassByType(stateType.value, 'bg', currentColorIndex))
// const borderColor      = computed(() => getColorClassByType(stateType.value, 'border', currentColorIndex))

// __ Вычисляем размер и тип шрифта для label
const currentColor = computed(() => getColorClassByType('dark')).value + currentColorIndex
// const currentColor = computed(() => getColorClassByType(stateType.value)).value + currentColorIndex
const textColor    = computed(() => ('text' + currentColor).replace(currentColorIndex.toString(), (currentColorIndex/* + 200*/).toString()))
const labelTextSizeClass = computed(() => getFontSizeClass(props.labelTextSize))

// __ Обрабатываем нажатие на label
const labelClick = (/*$returnedText: string, e: Event*/) => {
    emits('labelClick', !stateType.value)
    // const target = e.target as HTMLElement
    // emits('labelClick', target.innerText)
    // console.log(target.innerText)
}


</script>

<style scoped>
.input-label {
    @apply font-semibold ml-2 mb-0.5 mt-2;
}
</style>
