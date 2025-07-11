<template>

    <div :class="[width, 'flex flex-col m-0.5']">
        <label v-if="label" :class="['input-label', textColor]" :for="id">{{ label }}</label>
        <input
            :id="id"
            v-model.number="inputNumber"
            :class="['app-input', height, textSizeClass, semibold, horizontalAlign, borderColor, focusBorderColor, placeholderColor, backgroundColor, currentTextColor]"
            :disabled="disabled"
            :max="max"
            :min="min"
            :placeholder="placeholder"
            :step="step"
            :value="value"
            type="number"
            @input="getInputNumber"
        >
        <div v-if="errors">
            <div v-for="(err, index) in errors" :key="index">
                <span :class="['input-error', textColor]">
                    {{ err.$message }}
                </span>
            </div>
        </div>

    </div>

</template>


<script setup>

import {computed, ref, watch} from 'vue'

import {colorsClasses, colorsList} from '@/app/constants/colorsClasses.js'
import {getColorClassByType, getFontSizeClass, getTextColorClassByType} from '@/app/helpers/helpers.js'
import {fontSizesList} from '@/app/constants/fontSizes.js'

const props = defineProps({
    id: {
        required: true,
    },
    type: {
        type: String,
        required: false,
        default: 'dark',
        validator: (type) => colorsList.includes(type),
    },
    value: {
        // type: Number,
        required: false,
        default: 0,
    },
    step: {
        type: String,
        required: false,
        default: '1',
    },
    placeholder: {
        type: String,
        required: false,
        default: 'Enter...',
    },
    label: {
        type: String,
        required: false,
        default: '',
    },
    disabled: {
        type: Boolean,
        required: false,
        default: false,
    },
    width: {
        type: String,
        required: false,
        default: 'w-[200px]',

    },
    height: {
        type: String,
        required: false,
        default: 'h-[30px]',

    },
    errors: {
        type: Array,
        required: false,
        default: null,
    },
    bold: {
        type: Boolean,
        required: false,
        default: true,
    },
    align: {
        type: String,
        required: false,
        default: 'left',
        validator: (position) => ['left', 'right', 'center', 'justify'].includes(position)
    },
    fractionDigits: {
        type: Number,
        required: false,
        default: 0,
    },
    textSize: {
        type: String,
        required: false,
        default: 'small',
        validator: (size) => fontSizesList.includes(size)
    },
    labelTextSize: {
        type: String,
        required: false,
        default: 'mini',
        validator: (size) => fontSizesList.includes(size)
    },
    inputBgColor: {
        type: String,
        required: false,
        default: 'none'
    },
    min: {
        type: Number,
        required: false,
        default: 0,
    },
    max: {
        type: Number,
        required: false,
        default: 1_000_000,
    }

})

const currentColorIndex = 500       // задаем основной индекс палитры tailwinds
const placeholderColor = ref(getColorClassByType(props.type, 'placeholder', currentColorIndex))
const focusBorderColor = ref(getColorClassByType(props.type, 'focus:ring', currentColorIndex))
const currentTextColor = ref(getTextColorClassByType(props.type))
const backgroundColor = props.inputBgColor === 'none' ? ref(getColorClassByType(props.type, 'bg', currentColorIndex)) : props.inputBgColor
const borderColor = ref(getColorClassByType(props.type, 'border', currentColorIndex))
const textColor = ref(currentTextColor.value.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString()))

// const currentColor = getColorClassByType(props.type) + currentColorIndex
// const placeholderColor = 'placeholder' + currentColor
// const borderColor = 'border' + currentColor
// const focusBorderColor = 'focus:ring' + currentColor

// вычисляем горизонтальное выравнивание
const horizontalAlign = ref('text-' + props.align)
const textSizeClass = ref(getFontSizeClass(props.textSize))
const semibold = ref(props.bold ? 'font-semibold' : '')

// attract: Определяем модель
// const inputNumber = ref(props.value)
const inputNumber = defineModel('inputNumber', {
    // type: Number,
    default: 0,
})

inputNumber.value = props.value === '' ? 0 : props.value     // Задаем начальное значение

const emits = defineEmits(['getInputNumber'])
const getInputNumber = (e) => emits('getInputNumber', e.target.value)

// Делаем реактивность вычисляемых стилей
watch(() => props.type, (newType) => {
    placeholderColor.value = getColorClassByType(props.type, 'placeholder', currentColorIndex)
    focusBorderColor.value = getColorClassByType(props.type, 'focus:ring', currentColorIndex)
    currentTextColor.value = getTextColorClassByType(props.type)
    backgroundColor.value = getColorClassByType(props.type, 'bg', currentColorIndex)
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
    @apply text-sm ml-2 font-semibold;
}

.input-label {
    @apply text-sm font-semibold ml-2 mb-0.5 mt-2
}

</style>
