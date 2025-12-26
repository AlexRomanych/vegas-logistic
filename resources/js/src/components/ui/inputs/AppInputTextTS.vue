<template>
    <div :class="width" class="flex flex-col ml-0.5 mr-0.5 mt-2">
        <label v-if="label" :class="['input-label', textColor, labelTextSizeClass]" :for="id">{{
            label
        }}</label>
        <!--@input="getInputText"-->
        <input
            :id="id"
            v-model="textModel"
            :class="[
                'app-input',
                height,
                borderColor,
                focusBorderColor,
                placeholderColor,
                placeholderBgColor,
                textSizeClass,
                roundedCSS,
            ]"
            :disabled="disabled"
            :placeholder="placeholder"
            :type="func"
            :value="textModel"
            @blur="leaveFocus"
            @input="(event) => (textModel = (event.target as HTMLInputElement).value)"
        />

        <div v-if="errors" class="mt-0.5">
            <div v-for="(err, index) in errors" :key="index" :class="['input-error', textColor]">
                {{ err.$message }}
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'

import { type IColorTypes } from '@/app/constants/colorsClasses.js'
import { type IFontsType } from '@/app/constants/fontSizes.js'

import { getColorClassByType, getFontSizeClass, getRoundedClass } from '@/app/helpers/helpers.js'

interface IProps {
    id: string
    type?: IColorTypes
    func?: 'text' | 'password' | 'email' | 'number' | 'tel' | 'url'
    placeholder?: string
    label?: string
    disabled?: boolean
    width?: string
    height?: string
    errors?: any[] | null
    textSize?: IFontsType
    labelTextSize?: IFontsType
    rounded?: string
}

const props = withDefaults(defineProps<IProps>(), {
    type: 'dark',
    func: 'text',
    placeholder: 'Enter...',
    label: '',
    disabled: false,
    width: 'w-[500px]',
    height: 'min-h-[25px]',
    errors: null,
    textSize: 'small',
    labelTextSize: 'mini',
    rounded: 'rounded-lg',
})

const emits = defineEmits<{
    (e: 'leaveFocus'): void
}>()

// const emits = defineEmits<{
//     (e: 'update:textValue', payload: string): void
// }>()

// __ Определяем модель
const textModel = defineModel<string>('textValue', { required: true })

const currentColorIndex = 600 // задаем основной индекс палитры tailwinds
const currentColor = computed(() => getColorClassByType(props.type) + currentColorIndex)

// console.log(currentColor.value)

const placeholderColor = computed(() => 'placeholder' + currentColor.value)
const placeholderBgColor = 'bg-white'

// const placeholderBgColor = computed(
//     () => 'bg' + (getColorClassByType(props.type) + (currentColorIndex - 500)),
// )

const borderColor = computed(() => 'border' + currentColor.value)
const backgroundColor = computed(() => 'bg' + currentColor.value)
const focusBorderColor = computed(() => 'focus:ring' + currentColor.value)

const textColor = computed(() => 'text' + currentColor.value)
// attract: Делает цвет темнее
// textColor = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())
const textSizeClass = computed(() => getFontSizeClass(props.textSize))
const labelTextSizeClass = computed(() => getFontSizeClass(props.labelTextSize))

const roundedCSS = computed(() => getRoundedClass(props.rounded))

// warn: (e: Event), а не (e: InputEvent), потому что сразу не типизируется <input />
// const getInputText = (e: Event) => {
//     const target = e.target as HTMLInputElement       // или так
//     // const target = <HTMLInputElement>e.target         // или так
//     emits('update:textValue', target.value)
// }

const leaveFocus = () => emits('leaveFocus')
</script>

<style scoped>
@reference "@css/app.css";

.app-input {
    @apply p-1 border focus:outline-none focus:ring-2;
}

.app-input::placeholder {
    @apply text-xs;
}

.app-input:focus {
    @apply outline-none ring-2 ring-black;
}

.input-error {
    @apply ml-2 font-semibold text-red-600 text-mc;
}

.input-label {
    @apply font-semibold ml-2 mb-0.5 mt-2;
}
</style>
