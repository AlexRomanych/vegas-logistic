<template>
    <div
        :class="[width, labelHeight, backgroundColor, borderColor, currentTextColor, textSizeClass, semibold, horizontalAlign]"
        class="flex flex-col m-0.5 app-label justify-center"
        @click="labelClick"
    >
        <div v-for="(text, idx) in textArray" :key="idx">
            {{ text }}
        </div>

    </div>
</template>


<script setup>

import {colorsList} from '/resources/js/src/app/constants/colorsClasses.js'
import {fontSizesList} from '/resources/js/src/app/constants/fontSizes.js'
import {getColorClassByType, getTextColorClassByType, getFontSizeClass} from '/resources/js/src/app/helpers/helpers.js'
import {getDigitPart} from "/resources/js/src/app/helpers/helpers_lib.js";

import {computed, reactive, ref, watch, watchEffect} from "vue";

const LINE_SEPARATOR = '&nl'  // new line - разделитель строк в тексте

const props = defineProps({
    text: {
        type: [String, Array],
        required: false,
        default: 'Enter...',
        validator: (text) => Array.isArray(text) || typeof text === 'string'
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
        default: 'h-[25px]',

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

// Вычисляем CSS выравнивания
const horizontalAlign = ref(getHorizontalAlign(props.align))

// Возвращаем количество строк в массиве
const getLinesAmount = (inTextData) => {
    if (typeof inTextData === 'string') {
        return 1
    } else if (Array.isArray(inTextData)) {
        return inTextData.length
    }
}

// Получаем высоту Label в зависимости от количества строк
const getLabelHeight = (inTextData) => {
    // if (typeof inTextData === 'string') {
    //     return props.height
    // }
    let linesAmount = getLinesAmount(inTextData)

    if (Array.isArray(props.text) &&
        props.text.length === 2 &&
        props.text[1] === '') linesAmount++

    const height = parseInt(getDigitPart(props.height)) * linesAmount
    return `h-[${height}px]`
}

// Преобразуем данные в массив
// descr: Если первый элемент массива - не пустая строка, а вторая - пустая,
// descr: и массив из 2-х элементов, то мы получаем массив из одной строки
// descr: Также возвращаем массив, если есть сепаратор строки - '&nl'
const getTextArray = (inTextData) => {
    if (typeof inTextData === 'string') {
        if (inTextData.toLowerCase().includes(LINE_SEPARATOR.toLowerCase())) {
            inTextData = inTextData.replaceAll(' ' + LINE_SEPARATOR + ' ', LINE_SEPARATOR)
            inTextData = inTextData.replaceAll(' ' + LINE_SEPARATOR, LINE_SEPARATOR)
            inTextData = inTextData.replaceAll(LINE_SEPARATOR + ' ', LINE_SEPARATOR)
            inTextData = inTextData.replaceAll('  ', ' ')
            inTextData = inTextData.replaceAll('  ', ' ')

            return inTextData.split(LINE_SEPARATOR)
        }
        return [inTextData]
    }

    if (Array.isArray(inTextData)) {
        if (inTextData.length === 1) return inTextData
        if (inTextData.length === 2 && inTextData[1] === '') return [inTextData[0]]
    }
    return inTextData
}

// Получаем массив строк для вывода
let textArray = reactive(getTextArray(props.text))

// Получаем tailwind класс высоты Label в зависимости от количества строк
const labelHeight = ref(getLabelHeight(textArray))

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

// делаем реактивной высоту label
watch(() => props.text, (text) => {
    textArray = getTextArray(text)
    labelHeight.value = getLabelHeight(textArray)
})

// watchEffect(() => {})


</script>

<style scoped>
.app-label {
    @apply
    flex flex-col justify-center
    p-1 m-0.5
    border rounded-lg focus:outline-none focus:ring-2;
}

.load-style {
    @apply
    w-[60px]
    w-[50px]
    w-[75px]
    w-[90px]

}

</style>
