<template>
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="shown"
                 :class="[width, height, position, bgColor, borderColor, textColor, 'callout-container']"
                 @click="toggleShow">

                <!--                <span>{{ text }}</span>-->

                <div>
                    <div v-for="(text, idx) in textArray" :key="idx">
                        {{ text }}
                    </div>
                </div>

            </div>
        </Transition>
    </Teleport>
</template>

<script setup>

import {reactive} from 'vue'

import {LINE_SEPARATOR} from '/resources/js/src/app/constants/common.js'
import {colorsList} from '/resources/js/src/app/constants/colorsClasses.js'
import {getColorClassByType, getTextColorClassByType} from '/resources/js/src/app/helpers/helpers.js'
import {computed, ref, watch} from "vue";

const props = defineProps({
    width: {
        type: String,
        required: false,
        default: 'w-[500px]',
    },
    height: {
        type: String,
        required: false,
        default: 'h-[100px]',
    },
    type: {
        type: String,
        required: false,
        default: 'primary',
        validator: (type) => colorsList.includes(type)
    },
    text: {
        type: [String, Array],
        required: false,
        default: 'This is a callout',
        validator: (text) => Array.isArray(text) || typeof text === 'string'
    },
    pos_x: {
        type: String,
        required: false,
        default: 'right',
        validator: (pos_x) => ['left', 'right'].includes(pos_x)
    },
    pos_y: {
        type: String,
        required: false,
        default: 'bottom',
        validator: (pos_y) => ['top', 'bottom'].includes(pos_y)
    },
    show: {
        type: Boolean,
        required: false,
        default: false
    }

})

const emits = defineEmits(['toggleShow'])

const shown = ref(props.show)
const toggleShow = () => {
    shown.value = !shown.value
    emits('toggleShow', shown.value)
}

const getPositionClass = (pos_x, pos_y) => {
    let x_pos = ''
    let y_pos = ''

    switch (pos_x) {
        case 'left':
            x_pos = 'left-0';
            break;
        case 'right':
            x_pos = 'right-0';
            break;
    }

    switch (pos_y) {
        case 'top':
            y_pos = 'top-0';
            break;
        case 'bottom':
            y_pos = 'bottom-0';
            break;
    }

    return `${x_pos} ${y_pos}`
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

// attract: Получаем массив строк для вывода
const textArray = ref(getTextArray(props.text))


const position = getPositionClass(props.pos_x, props.pos_y)                                     // Получаем класс для позиции
const bgColor = computed(() => getColorClassByType(props.type, 'bg', 0, false))         // Получаем класс для цвета заднего фона
const borderColor = computed(() => getColorClassByType(props.type, 'border', 700))      // Получаем класс для цвета границы
const textColor = computed(() => getTextColorClassByType(props.type))

watch(() => props.show, (newValue) => shown.value = newValue)
watch(() => props.text, (newValue) => {
    textArray.value = getTextArray(newValue)
    // console.log('text: ', props.text)
    // console.log('textArray: ', textArray.value)
})

</script>


<style scoped>
/*
.fade-in {
    z-index: 100;
    animation: fadeIn 5s linear;
}

@keyframes fadeIn {
    0% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        display: none;
    }
}
*/

.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-enter-to,
.fade-leave-from {
    opacity: 1;
}

.callout-container {
    @apply z-[500] fixed flex items-center justify-start p-10 m-1 rounded-xl font-semibold border-l-8 text-wrap break-words overflow-hidden text-ellipsis
}

</style>
