<template>
    <div :class="width" class="flex flex-col ml-1 mr-1 mt-2">
        <label v-if="label" :class="['input-label', textColor]" :for="id">{{ label }}</label>
        <input
            :id="id"
            :class="['app-input', borderColor, focusBorderColor, placeholderColor]"
            :placeholder="placeholder"
            :disabled="disabled"
            type="text"
            v-model="inputText"
            @input="getInputText"

        >
        <span v-if="error" :class="['input-error', textColor]">{{ error.message }}</span>


    </div>

</template>


<script setup>

import {colorsClasses, colorsList} from "@/src/app/constants/colorsClasses.js"
import {getColorClassByType} from "@/src/app/helpers/helpers.js"
import {computed, ref} from "vue";

const props = defineProps({
    id: {
        required: true,
    },
    type: {
        type: String,
        required: false,
        default: 'primary',
        validator: (type) => colorsList.includes(type)
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
    error: {
        type: Object,
        required: false,
        default: null,
        message: {
            type: String,
            required: false,
            default: 'Ошибка...',
        }
    }


})



const currentColorIndex = 600       // задаем основной индекс палитры tailwinds
const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex

const placeholderColor = 'placeholder' + currentColor
const borderColor = 'border' + currentColor
const focusBorderColor = 'focus:ring' + currentColor

let textColor = 'text' + currentColor
textColor = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())

const inputText = defineModel({
    type: String,
    default: ''
})

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
    @apply text-sm font-semibold ml-2 mb-0.5 mt-2
}

</style>
