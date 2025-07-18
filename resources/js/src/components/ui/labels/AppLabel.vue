<template>
    <div
        :class="[width, height, backgroundColor, borderColor, currentTextColor, textSizeClass, semibold, horizontalAlign]"
        :title="title"
        class="flex flex-col justify-center app-label"
        @click="labelClick"
    >

        <!--        <div>-->
        {{ text }}
        <!--        </div>-->
    </div>
</template>


<script setup>

import {colorsList} from '@/app/constants/colorsClasses.js'
import {fontSizesList} from '@/app/constants/fontSizes.js'
import {getColorClassByType, getTextColorClassByType, getFontSizeClass} from '@/app/helpers/helpers.js'

import {computed, ref, watch, watchEffect} from "vue";

const props = defineProps({
    text: {
        type: String,
        required: false,
        default: 'Enter...',
    },
    type: {
        type: String,
        required: false,
        default: 'dark',
        validator: (type) => colorsList.includes(type)
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
    textSize: {
        type: String,
        required: false,
        default: 'normal',
        validator: (size) => fontSizesList.includes(size)
        // validator: (size) => ['micro', 'mini', 'normal', 'small', 'large', 'huge'].includes(size)
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
        validator: (position) => ['left', 'right', 'center'].includes(position)
    },
    title: {
        type: String,
        required: false,
        default: '',
    }
})

const emits = defineEmits(['labelClick'])

// обрабатываем нажатие на label
const labelClick = (e) => {
    // console.log(e.target.innerText)
    emits('labelClick', e.target.innerText)
}


const textSizeClass = ref(getFontSizeClass(props.textSize))
const semibold = props.bold ? 'font-semibold' : ''
const currentColorIndex = 500       // задаем основной индекс палитры tailwinds
// const currentTextColor = computed(() => getTextColorClassByType(props.type)).value
// const backgroundColor = computed(() => getColorClassByType(props.type, 'bg', currentColorIndex)).value
// const borderColor = computed(() => getColorClassByType(props.type, 'border', currentColorIndex)).value

// const itemType = ref(props.type)

const currentTextColor = ref(getTextColorClassByType(props.type))
const backgroundColor = ref(getColorClassByType(props.type, 'bg', currentColorIndex))
const borderColor = ref(getColorClassByType(props.type, 'border', currentColorIndex))

// вычисляем горизонтальное выравнивание
const getHorizontalAlign = (alignPosition) => {
    let horizontalAlign = 'items-'
    switch (alignPosition) {
        case 'left':
            horizontalAlign += 'start'
            break;
        case 'right':
            horizontalAlign += 'end'
            break;
        case 'center':
            horizontalAlign += 'center'
            break;
    }
    return horizontalAlign
}

const horizontalAlign = ref(getHorizontalAlign(props.align))


// Без этой функции не перерисовывает стили
watch(() => props.type, (type) => {
    currentTextColor.value = getTextColorClassByType(props.type)
    backgroundColor.value = getColorClassByType(props.type, 'bg', currentColorIndex)
    borderColor.value = getColorClassByType(props.type, 'border', currentColorIndex)
})

// делаем реактивным горизонтальное выравнивание
watch(() => props.align, (align) => horizontalAlign.value = getHorizontalAlign(align))

// делаем реактивным размер шрифта выравнивание
watch(() => props.textSize, (textSize) => textSizeClass.value = getFontSizeClass(textSize))

// watchEffect(() => {})


</script>

<style scoped>
.app-label {
    @apply
        /*flex flex-col justify-center*/
    p-1 m-0.5
    border rounded-lg focus:outline-none focus:ring-2;
}


</style>
