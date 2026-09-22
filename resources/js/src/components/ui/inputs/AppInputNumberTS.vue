<template>
    <div :class="[width, 'flex flex-col m-0.5']">
        <label v-if="label" :class="['input-label', textColor]" :for="id">{{ label }}</label>

        <input
            :id="id"
            v-model.number="model"
            :class="[
                'app-input',
                height,
                textSizeClass,
                semibold,
                horizontalAlign,
                borderColor,
                focusBorderColor,
                placeholderColor,
                backgroundColor,
                currentTextColor
            ]"
            :disabled="disabled"
            :max="max"
            :min="min"
            :placeholder="placeholder"
            :step="step"
            type="number"
            @input="handleInput"
        >

        <div v-if="errors && errors.length">
            <div v-for="(err, index) in errors" :key="index">
                <span :class="['input-error', textColor]">
                    {{ typeof err === 'string' ? err : err.$message }}
                </span>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import type { IColorTypes, IFontsType } from '@/types'
import { getColorClassByType, getFontSizeClass, getTextColorClassByType } from '@/app/helpers/helpers.js'

interface IErrorItem {
    $message: string
}

interface IProps {
    id?: string
    type?: IColorTypes
    step?: string
    placeholder?: string
    label?: string
    disabled?: boolean
    width?: string
    height?: string
    errors?: (string | IErrorItem)[] | null
    bold?: boolean
    align?: 'left' | 'right' | 'center' | 'justify'
    fractionDigits?: number
    textSize?: IFontsType
    labelTextSize?: IFontsType
    inputBgColor?: string
    min?: number
    max?: number
}

const props = withDefaults(defineProps<IProps>(), {
    id: 'number-input',
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
    inputBgColor: 'none',
    min: 0,
    max: 1_000_000,
})

// Стандартная модель Vue 3 (v-model)
const model = defineModel<number | string | null>({ default: 0 })

const emits = defineEmits<{
    (e: 'getInputNumber', payload: number): void
}>()

const currentColorIndex = 500
const placeholderColor  = computed(() => getColorClassByType(props.type, 'placeholder', currentColorIndex))
const focusBorderColor  = computed(() => getColorClassByType(props.type, 'focus:ring', currentColorIndex))
const borderColor       = computed(() => getColorClassByType(props.type, 'border', currentColorIndex))
const currentTextColor  = computed(() => getTextColorClassByType(props.type))
const backgroundColor   = computed(() => props.inputBgColor === 'none' ? getColorClassByType(props.type, 'bg', currentColorIndex) : props.inputBgColor)
const textColor         = computed(() => currentTextColor.value.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString()))

const horizontalAlign = computed(() => 'text-' + props.align)
const textSizeClass   = computed(() => getFontSizeClass(props.textSize))
const semibold        = computed(() => props.bold ? 'font-semibold' : '')

const handleInput = (e: Event) => {
    const target = e.target as HTMLInputElement
    const numericValue = target.value !== '' ? Number(target.value) : 0
    emits('getInputNumber', numericValue)
}
</script>

<style scoped>
.app-input {
    @apply p-1 border rounded focus:outline-none focus:ring-2;
}

.input-error {
    @apply text-sm ml-2 font-semibold;
}

.input-label {
    @apply text-sm font-semibold ml-2 mb-0.5 mt-2;
}
</style>
