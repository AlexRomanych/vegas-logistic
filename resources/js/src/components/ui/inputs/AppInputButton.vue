<template>
    <div :class="width" class="flex flex-col ml-1 mr-1 mt-2">
        <input
            :id="id"
            v-model="inputText"
            :class="['app-input', borderColor, focusBorderColor]"
            type="button"
            @input="getInputText"

        >

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
        default: 'normal',
        validator: (type) => colorsList.includes(type)
    },
    width: {
        type: String,
        required: false,
        default: 'w-[100px]',

    },



})
// console.log(props)

const currentColor = computed(() => getColorClassByType(props.type))

const placeholderColor = 'placeholder-' + currentColor.value
const borderColor = 'border-' + currentColor.value
const focusBorderColor = 'focus:ring-' + currentColor.value
let textColor = 'text-' + currentColor.value
textColor = textColor.replace('300', '500')

const inputText = defineModel({
    type: String,
    default: ''
})

const emit = defineEmits(['getInputText'])

const getInputText = e => emit('getInputText', e.target.value)
// const onInput = function(e) {
//     console.log(e.target.value)
// }
// const onInput = function(inputText) {
//     console.log(inputText.target.value)
// }


</script>


<style scoped>
.app-input {
    @apply p-1 border rounded-md focus:outline-none focus:ring-2;
}

.input-error {
    @apply text-sm ml-2 font-semibold;
}

.input-label {
    @apply text-sm font-semibold ml-2 mb-0.5 mt-2
}

.needed-red {
    @apply
    border-red-300
    placeholder-red-300
    focus:ring-red-300
    text-red-300
    text-red-500
}

.needed-yellow {
    @apply
    border-yellow-300
    placeholder-yellow-300
    focus:ring-yellow-300
    text-yellow-300
    text-yellow-500
}

.needed-green {
    @apply
    border-green-300
    placeholder-green-300
    focus:ring-green-300
    text-green-300
    text-green-500
}

.needed-gray {
    @apply
    border-gray-300
    placeholder-gray-300
    focus:ring-gray-300
    text-gray-300
    text-gray-500
}


</style>
