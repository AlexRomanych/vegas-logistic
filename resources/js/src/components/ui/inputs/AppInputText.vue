<template>
    <div :class="width" class="flex flex-col ml-1 mr-1 mt-2">
        <label v-if="label" :class="['input-label', textColor]" :for="id">{{ label }}</label>
        <input
            :id="id"
            :class="['app-input', borderColor, focusBorderColor, placeholderColor]"
            :placeholder="placeholder"
            type="text"
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
        default: 'normal',
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

const currentColor = computed(() => getColorClassByType(props.type))

const placeholderColor = ref('placeholder-' + currentColor.value)
const borderColor = ref('border-' + currentColor.value)
const focusBorderColor = ref('focus:ring-' + currentColor.value)
const textColor = ref('text-' + currentColor.value)
textColor.value = textColor.value.replace('300', '500')
// const focusBorderColor = 'focus:ring-yellow-300'

// const borderColor_1 = 'border-red-300'// + currentColor.value


// const borderColor_1 = 'border-yellow-300'


// const placeholderColor_1 = 'placeholder-yellow-300'

// console.log(placeholderColor.value)
// console.log(placeholderColor_1)


// console.log('placeholder-' + currentColor.value === placeholderColor_1)


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
