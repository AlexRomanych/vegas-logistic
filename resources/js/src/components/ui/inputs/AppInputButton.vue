<template>
    <input
        :id="id"
        :class="[width, height, backgroundColor, borderColor, currentTextColor, textSizeClass, semibold]"
        :disabled="disabled"
        :value="title"
        :type="func"
        class="app-input"
        @click="buttonClick"

    />
</template>


<script setup>

import {colorsList} from "@/src/app/constants/colorsClasses.js"
import {fontSizesList} from "@/src/app/constants/fontSizes.js"
import {getColorClassByType, getTextColorClassByType, getFontSizeClass} from "@/src/app/helpers/helpers.js"

import {computed, ref, watch} from "vue"

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
    func: {
        type: String,
        required: false,
        default: 'button',
        validator: (func) => ['button', 'submit', 'reset'].includes(func)
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
    height: {
        type: String,
        required: false,
        default: 'h-[50px]',
    },
    title: {
        type: String,
        required: false,
        default: 'Нажми меня',
    },
    textSize: {
        type: String,
        required: false,
        default: 'normal',
        validator: (size) => fontSizesList.includes(size)
        // validator: (size) => ['mini', 'normal', 'small', 'large', 'huge'].includes(size)
    },
    bold: {
        type: Boolean,
        required: false,
        default: true,
    },
})



// console.log(props.title)
// const inputButton = defineModel(props.title)

// const title = computed(() => props.title)
const emit = defineEmits(['buttonClick'])

// const title = props.title
// const inputButton = defineModel('title')
const buttonClick = function(e) {
    // inputButton.value++
    // console.log(inputButton.value)
    emit('buttonClick', e.target.value, props.id)
}

const textSizeClass = getFontSizeClass(props.textSize)
const semibold = props.bold ? 'font-semibold' : ''
const currentColorIndex = 500       // задаем основной индекс палитры tailwinds
// const currentTextColor = computed(() => getTextColorClassByType(props.type)).value
// const backgroundColor = computed(() => getColorClassByType(props.type, 'bg', currentColorIndex)).value
// const borderColor = computed(() => getColorClassByType(props.type, 'border', currentColorIndex)).value

const currentTextColor = ref(getTextColorClassByType(props.type))
const backgroundColor =ref( getColorClassByType(props.type, 'bg', currentColorIndex))
const borderColor = ref(getColorClassByType(props.type, 'border', currentColorIndex))

// Без этой функции не перерисовывает стили
watch(() => props.type, (type) => {
    currentTextColor.value = getTextColorClassByType(props.type)
    backgroundColor.value = getColorClassByType(props.type, 'bg', currentColorIndex)
    borderColor.value = getColorClassByType(props.type, 'border', currentColorIndex)
})


</script>


<style scoped>

.app-input {
    @apply m-0.5 p-1 border-2 rounded-lg cursor-pointer;
}

</style>
