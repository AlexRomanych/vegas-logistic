<template>
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="shown"
                 :class="[width, height, position, bgColor, borderColor, textColor, 'callout-container']"
                 @click="toggleShow">

                <div>
                    <div v-for="(text, idx) in textArray" :key="idx">
                        {{ text }}
                    </div>
                </div>

            </div>
        </Transition>
    </Teleport>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'

import { LINE_SEPARATOR } from '@/app/constants/common.js'
import type { IColorTypes } from '@/app/constants/colorsClasses.js'

import { getColorClassByType, getTextColorClassByType } from '@/app/helpers/helpers.js'


type IHorizontalAlignmentType = 'left' | 'right'
type IVerticalAlignmentType = 'top' | 'bottom'

interface IProps {
    width?: string
    height?: string
    type?: IColorTypes
    text?: string | string[]
    pos_x?: IHorizontalAlignmentType
    pos_y?: IVerticalAlignmentType
    show?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    width:  'w-[500px]',
    height: 'h-[100px]',
    type:   'primary',
    text:   'This is a callout',
    pos_x:  'right',
    pos_y:  'bottom',
    show:   false
})

const emits = defineEmits<{
    (e: 'toggleShow', value: boolean): void,
}>()

const shown      = ref(props.show)
const toggleShow = () => {
    shown.value = !shown.value
    emits('toggleShow', shown.value)
}

const getPositionClass = (pos_x: IHorizontalAlignmentType, pos_y: IVerticalAlignmentType) => {
    let x_pos = ''
    let y_pos = ''

    switch (pos_x) {
        case 'left':
            x_pos = 'left-0'
            break
        case 'right':
            x_pos = 'right-0'
            break
    }

    switch (pos_y) {
        case 'top':
            y_pos = 'top-0'
            break
        case 'bottom':
            y_pos = 'bottom-0'
            break
    }

    return `${x_pos} ${y_pos}`
}

// __ Преобразуем данные в массив
// ___ Если первый элемент массива - не пустая строка, а вторая - пустая,
// ___ и массив из 2-х элементов, то мы получаем массив из одной строки
// ___ Также возвращаем массив, если есть сепаратор строки - '&nl'
const getTextArray = (inTextData: string | string[]) => {
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

// __ Получаем массив строк для вывода
const textArray = computed(() => getTextArray(props.text))


const position    = getPositionClass(props.pos_x, props.pos_y)                                     // Получаем класс для позиции
const bgColor     = computed(() => getColorClassByType(props.type, 'bg', 0, false))         // Получаем класс для цвета заднего фона
const borderColor = computed(() => getColorClassByType(props.type, 'border', 700))      // Получаем класс для цвета границы
const textColor   = computed(() => getTextColorClassByType(props.type))

watch(() => props.show, (newValue) => shown.value = newValue)

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
