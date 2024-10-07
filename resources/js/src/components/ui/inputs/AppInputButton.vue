<template>
    <input
        :id="id"
        :class="[props.width, backgroundColor, borderColor, currentTextColor]"
        :disabled="disabled"
        :value="title"
        class="app-input"
        type="button"
        @click="buttonClick"
        v-model="inputButton"
    />
</template>


<script setup>

import {colorsList} from "@/src/app/constants/colorsClasses.js"
import {getColorClassByType, getTextColorClassByType} from "@/src/app/helpers/helpers.js"
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
    disabled: {
        type: Boolean,
        required: false,
        default: false,
    },
    width: {
        type: String,
        required: false,
        default: 'w-[100px]',

    },
    title: {
        type: String,
        required: false,
        default: 'Нажми меня',
    }

})

console.log(props.title)
// const inputButton = defineModel(props.title)

// const title = computed(() => props.title)
const emit = defineEmits(['buttonClick'])

const title = props.title
const inputButton = defineModel('title')
const buttonClick = function() {
    // inputButton.value++
    // console.log(inputButton.value)
    emit('buttonClick', inputButton.value)
}

const currentColorIndex = 500       // задаем основной индекс палитры tailwinds
const currentTextColor = computed(() => getTextColorClassByType(props.type)).value
const backgroundColor = computed(() => getColorClassByType(props.type, 'bg', currentColorIndex)).value
const borderColor = computed(() => getColorClassByType(props.type, 'border', currentColorIndex)).value

</script>


<style scoped>

.app-input {
    @apply ml-1 mt-1 p-1 border-2 rounded-lg cursor-pointer;
}

</style>
