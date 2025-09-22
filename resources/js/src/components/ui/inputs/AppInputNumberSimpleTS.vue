<template>

    <div :class="width" class="flex flex-col ml-1 mr-1 mt-2">
        <label v-if="label" :class="['input-label', textColor, labelTextSizeClass ]" :for="id">{{ label }}</label>
        <input
            :id="id"

            :class="[
                    'app-input',
                    showSpins ? '' : 'no-spinners',
                    borderColor,
                    focusBorderColor,
                    placeholderColor,
                    inputBgColor,
                    textSizeClass,
                    horizontalAlign,
                    semibold
                ]"
            :disabled="disabled"
            :placeholder="placeholder"
            :step="step"
            :value="numberModel"
            type="number"
            @input="handleInput"
            @blur="leaveFocus"
            @focus="takeFocus"
        >

        <div v-if="errors" class="mt-0.5">
            <div v-for="(err, index) in errors" :key="index" :class="['input-error', textColor, labelTextSizeClass]">
                {{ err.$message }}
            </div>
        </div>

    </div>

</template>


<script lang="ts" setup>
import { computed, watch } from 'vue'

import type { IHorizontalAlign } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.js'
import type { IFontsType } from '@/app/constants/fontSizes.js'

import {
    getColorClassByType,
    getFontSizeClass,
    getHorizontalAlignText,
    getTextColorClassByType
} from '@/app/helpers/helpers.js'

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
    showSpins?: boolean // показывать спины в поле ввода
    showZeros?: boolean // показывать нули в поле ввода
    bgColor?: boolean   // показывать цвет фона поля ввода
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
    showSpins: true,
    showZeros: true,
    bgColor: false,
})

const emits = defineEmits<{
    (e: 'leaveFocus'): void
    (e: 'takeFocus'): void
}>()

// __ Определяем модель
const numberModel = defineModel<number | null>('inputNumber', {required: true})


const currentColorIndex = 500       // задаем основной индекс палитры tailwinds
const placeholderColor = computed(() => getColorClassByType(props.type, 'placeholder', currentColorIndex))
const focusBorderColor = computed(() => getColorClassByType(props.type, 'focus:ring', currentColorIndex))
const borderColor = computed(() => getColorClassByType(props.type, 'border', currentColorIndex))
const currentTextColor = computed(() => getTextColorClassByType(props.type))
// const backgroundColor = props.inputBgColor === 'none' ? ref(getColorClassByType(props.type, 'bg', currentColorIndex)) : props.inputBgColor

// __ Определяем цвет фона поля ввода
const inputBgColor = computed(() => props.bgColor ? getColorClassByType(props.type, 'bg', 200) : 'bg-white')


const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex
const textColor = computed(() => 'text' + currentColor)

// __ горизонтальное выравнивание
const horizontalAlign = computed(() => getHorizontalAlignText(props.align))

// __ стили для текста
const semibold = computed(() => props.bold ? 'font-semibold' : '')
const textSizeClass = computed(() => getFontSizeClass(props.textSize))
const labelTextSizeClass = computed(() => getFontSizeClass(props.labelTextSize))

// __ Обработчик события input
const handleInput = (event: Event) => {
    const value = (event.target as HTMLInputElement).value
    // Если поле пустое, присваиваем null, иначе — число
    numberModel.value = value === '' ? null : Number(value)
}

// __ Обработчик события blur
const leaveFocus = () => emits('leaveFocus')

// __ Обработчик события focus
const takeFocus = () => emits('takeFocus')



// __ Отслеживаем изменение модели и подменяем 0
watch(() => numberModel.value, (newVal) => {
    if (newVal === 0) {
        if (!props.showZeros) {
            numberModel.value = null
        }
    }
}, {immediate: true})

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

.no-spinners {
    -moz-appearance: textfield;
}

.no-spinners::-webkit-outer-spin-button,
.no-spinners::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}


/*
input[type="number"] {
    -moz-appearance: textfield;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
*/
</style>
