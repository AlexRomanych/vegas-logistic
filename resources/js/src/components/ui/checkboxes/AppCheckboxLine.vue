<template>

    <div :class="width" class="flex flex-col ml-1 mr-1 mt-2">
        <label v-if="label" :class="['input-label', textColor, labelTextSizeClass ]" :for="id">{{ label }}</label>
        <input
            :id="id"
            v-model="inputText"

            :class="['app-input w-[25px]', height, borderColor, focusBorderColor, placeholderColor, textSizeClass ]"
            :disabled="disabled"
            :placeholder="placeholder"
            type="checkbox"
            @input="getInputText"

        >

    </div>

</template>


<script setup>

import { computed, ref } from 'vue'

import { colorsClasses, colorsList } from '@/app/constants/colorsClasses.js'
import { getColorClassByType, getFontSizeClass } from '@/app/helpers/helpers.js'
import { fontSizesList } from '@/app/constants/fontSizes.js'

const props = defineProps({
    id: {
        required: true,
    },
    type: {
        type: String,
        required: false,
        default: 'dark',
        validator: (type) => colorsList.includes(type)
    },
    func: {
        type: String,
        required: false,
        default: 'text',
        validator: (func) => ['text', 'password', 'email', 'tel', 'number'].includes(func)
    },
    value: {
        type: String,
        required: false,
        default: '',
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
        default: 'w-[500px]',

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

})

const currentColorIndex = 600       // задаем основной индекс палитры tailwinds
const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex

const placeholderColor = 'placeholder' + currentColor
const borderColor = 'border' + currentColor
const focusBorderColor = 'focus:ring' + currentColor

let textColor = 'text' + currentColor
// attract: Делает цвет темнее
// textColor = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())

const textSizeClass = ref(getFontSizeClass(props.textSize))
const labelTextSizeClass = ref(getFontSizeClass(props.labelTextSize))

const inputText = defineModel({
    type: String,
    default: '',
})

// Задаем начальное значение
inputText.value = props.value

const emit = defineEmits(['getInputText'])

const getInputText = (e) => emit('getInputText', e.target.value)
// const onInput = function(e) {
//     console.log(e.target.value)
// }
// const onInput = function(inputText) {
//     console.log(inputText.target.value)
// }


</script>

<style scoped>
.app-input {
    @apply p-1 border rounded-lg focus:outline-none focus:ring-2;
}

.input-error {
    @apply text-sm ml-2 font-semibold;
}

.input-label {
    @apply font-semibold ml-2 mb-0.5 mt-2
}

</style>
