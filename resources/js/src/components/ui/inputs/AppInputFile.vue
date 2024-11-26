<template>

        <div :class="width" class="flex flex-col m-4">

<!-- todo дооформить AppInputFile-->

<!--            <label v-if="label" :class="['input-label', textColor]" :for="id">{{ label }}</label>-->
            <label v-if="label" :class="['input-label']" :for="id">{{ label }}</label>
            <input
                :id="id"
                :class="['app-input', borderColor, focusBorderColor, placeholderColor]"
                :disabled="disabled"
                type="file"
                @change="inputText"
                @input="getInputText"

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
    errors: {
        type: Array,
        required: false,
        default: null,
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
    /*@apply p-1 border rounded-lg focus:outline-none focus:ring-2;*/
    @apply block w-[600px] text-lg text-slate-500 font-semibold border-2 rounded-2xl border-slate-500
    hover:bg-slate-200
    file:py-4
    file:px-4
    file:rounded-l-xl file:border-0 file:border-slate-500
    file:text-lg file:font-semibold
    file:bg-slate-500 file:text-white
    hover:file:bg-slate-400
}

.input-error {
    @apply text-sm ml-2 font-semibold;
}

.input-label {
    @apply text-sm font-semibold ml-2 mb-0.5 mt-2 text-slate-600;
}

</style>
