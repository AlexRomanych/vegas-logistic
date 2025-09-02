<template>

    <div :class="width" class="flex flex-col ml-1 mr-1 mt-2">
        <label v-if="label" :class="['input-label', textColor, labelTextSizeClass ]" :for="id">{{ label }}</label>
        <input
            :id="id"

            :class="['app-input', borderColor, focusBorderColor, placeholderColor, textSizeClass ]"
            :disabled="disabled"
            :placeholder="placeholder"
            :step="step"
            :value="numberModel"
            type="number"
            @input="handleInput"
        >

        <div v-if="errors" class="mt-0.5">
            <div v-for="(err, index) in errors" :key="index" :class="['input-error', textColor, labelTextSizeClass]">
                {{ err.$message }}
            </div>
        </div>

    </div>

</template>


<script lang="ts" setup>
import { computed, ref, watch } from 'vue'

import type { IHorizontalAlign } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.js'
import type { IFontsType } from '@/app/constants/fontSizes.js'

import { getColorClassByType, getFontSizeClass, getTextColorClassByType } from '@/app/helpers/helpers.js'

interface IProps {
    id: string
    type?: IColorTypes,
    step?: string
    placeholder?: string
    label?: string
    disabled?: boolean
    width?: string
    height?: string
    errors?: any[] | null
    bold?: boolean
    align?: IHorizontalAlign
    fractionDigits?: number
    textSize?: IFontsType,
    labelTextSize?: IFontsType
}

const props = withDefaults(defineProps<IProps>(), {
    type: 'dark',
    step: '1',
    placeholder: 'Enter...',
    label: '',
    disabled: false,
    width: 'w-[200px]',
    height: 'h-[30px]',
    errors: null,
    bold: true,
    align: 'left',
    fractionDigits: 0,
    textSize: 'small',
    labelTextSize: 'mini',
})


// __ Определяем модель
const numberModel = defineModel<number | null>('inputNumber', {required: true})

const currentColorIndex = 500       // задаем основной индекс палитры tailwinds
const placeholderColor = ref(getColorClassByType(props.type, 'placeholder', currentColorIndex))
const focusBorderColor = ref(getColorClassByType(props.type, 'focus:ring', currentColorIndex))
const currentTextColor = ref(getTextColorClassByType(props.type))
const borderColor = ref(getColorClassByType(props.type, 'border', currentColorIndex))
// const backgroundColor = props.inputBgColor === 'none' ? ref(getColorClassByType(props.type, 'bg', currentColorIndex)) : props.inputBgColor

const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex
const textColor = ref('text' + currentColor)

// __ вычисляем горизонтальное выравнивание
const horizontalAlign = ref('text-' + props.align)
const semibold = ref(props.bold ? 'font-semibold' : '')

const textSizeClass = ref(getFontSizeClass(props.textSize))
const labelTextSizeClass = ref(getFontSizeClass(props.labelTextSize))

// __ Обработчик события input
const handleInput = (event: Event) => {
    const value = (event.target as HTMLInputElement).value
    // Если поле пустое, присваиваем null, иначе — число
    numberModel.value = value === '' ? null : Number(value)
}


// Делаем реактивность вычисляемых стилей
watch(() => props.type, (newType) => {
    placeholderColor.value = getColorClassByType(props.type, 'placeholder', currentColorIndex)
    focusBorderColor.value = getColorClassByType(props.type, 'focus:ring', currentColorIndex)
    currentTextColor.value = getTextColorClassByType(props.type)
    // backgroundColor.value = getColorClassByType(props.type, 'bg', currentColorIndex)
    borderColor.value = getColorClassByType(props.type, 'border', currentColorIndex)
    textColor.value = currentTextColor.value.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())
})

watch(() => props.align, (newAlign) => horizontalAlign.value = 'text-' + newAlign)
watch(() => props.textSize, (newTextSize) => textSizeClass.value = getFontSizeClass(newTextSize))
watch(() => props.bold, (newBold) => semibold.value = newBold ? 'font-semibold' : '')

</script>

<style scoped>
.app-input {
    @apply p-1 border rounded-lg focus:outline-none focus:ring-2;
}

.input-error {
    @apply text-mc ml-2 font-semibold text-red-500;
}

.input-label {
    @apply font-semibold ml-2 mb-0.5 mt-2
}

</style>
