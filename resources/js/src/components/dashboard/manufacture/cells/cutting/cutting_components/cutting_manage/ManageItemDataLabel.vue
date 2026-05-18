<template>
    <div
        :class="[
            width,
            height,
            backgroundColor,
            borderColor,
            currentTextColor,
            textSizeClass,
            semibold,
            horizontalAlign,
            roundedCSS,
            class_,
        ]"
        :style="color ? {'background-color': color} : ''"
        class="flex flex-col m-0.5 app-label justify-center select-none"
        @click="labelClick"
    >
        <div v-if="amountShow"><span>{{ amount.toFixed(TOTAL_PRECISION) }}</span></div>
        <hr v-if="amountShow && timeShow" class="w-full my-[2px]">
        <div v-if="timeShow" class="text-center">
            <div><span class="italic">{{ formatTimeForWrap[0] }}</span></div>
            <div><span class="italic">{{ formatTimeForWrap[1] }}</span></div>
            <div><span class="italic">{{ formatTimeForWrap[2] }}</span></div>
        </div>
        <hr v-if="percentShow || (reference === null && amountShow && timeShow)" class="w-full my-[2px]">
        <div v-if="percentShow || (reference === null && amountShow && timeShow)" class="text-center">
            <div v-if="reference === null"><span class="italic">{{ '\u00A0' }}</span></div>
            <div v-else><span class="italic">{{ percent.toFixed(TOTAL_PRECISION) }}%</span></div>
        </div>


    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IHorizontalAlign, IFontsType, IColorTypes } from '@/types'

import {
    getColorClassByType,
    getTextColorClassByType,
    getFontSizeClass,
    getHorizontalAlign, getRoundedClass,
} from '@/app/helpers/helpers.js'

import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'
import { TOTAL_PRECISION } from '@/stores/OrdersStore.ts'

interface IProps {
    type?: IColorTypes
    width?: string
    height?: string
    textSize?: IFontsType
    bold?: boolean
    align?: IHorizontalAlign
    rounded?: string
    color?: string
    class_?: string

    amount?: number         // __ Количество в штуках
    time?: number           // __ Трудозатраты в секундах
    reference?: number | null // __ Референсное значение. Если передано, то тип элемента зависит от % Трудозатрат к Референсному значению
                              // __ Если null, то элемент рендерим, но значение не отображаем (выравниваем верстку)
    timeShow?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    type:     'stone',
    width:    'w-[200px]',
    height:   'h-[25px]',
    textSize: 'micro',
    bold:     true,
    align:    'center',
    rounded:  'rounded-[4px]',
    color:    '',
    class_:   '',

    amount:    0,
    time:      0,
    reference: 0,
    timeShow:  true,
})

const emits = defineEmits<{
    (e: 'labelClick', text: string): void
}>()

const currentColorIndex = 500 // задаем основной индекс палитры tailwinds
const textSizeClass     = computed(() => getFontSizeClass(props.textSize))
const semibold          = computed(() => (props.bold ? 'font-semibold' : ''))
const horizontalAlign   = computed(() => getHorizontalAlign(props.align))

// __ Процент трудозатрат от референсного значения
const percent = computed(() => props.reference ? props.time / props.reference * 100 : 0)

// __ Определяем тип элемента в зависимости от референсного значения
const typeByReference = computed(() => {
    if (!props.reference || percent.value === 0) {
        return props.type
    } else {
        if (percent.value < 20) {
            return 'dark'
        } else if (percent.value < 30) {
            return 'stone'
        } else if (percent.value < 40) {
            return 'info'
        } else if (percent.value < 60) {
            return 'indigo'
        } else if (percent.value < 70) {
            return 'primary'
        } else if (percent.value < 90) {
            return 'success'
        } else if (percent.value < 95) {
            return 'warning'
        } else if (percent.value <= 100) {
            return 'orange'
        }

        return 'danger'
    }
})

const currentTextColor = computed(() => getTextColorClassByType(typeByReference.value))
const backgroundColor  = computed(() => getColorClassByType(typeByReference.value, 'bg', currentColorIndex))
const borderColor      = computed(() => getColorClassByType(typeByReference.value, 'border', currentColorIndex))

const roundedCSS = computed(() => getRoundedClass(props.rounded))


// __ Обрабатываем нажатие на label
const labelClick = (e: Event) => {
    const target = e.target as HTMLElement
    emits('labelClick', target.innerText)
}

// __ Форматируем время для вывода в label в виде массива строк
const formatTimeForWrap = computed(() => {

    // __ Находим все совпадения: цифры + одна буква (ч, м или с)
    // __ Это автоматически игнорирует точки, пробелы и другие символы
    const timeStr = formatTimeWithLeadingZeros(props.time)
    let matches   = (timeStr.match(/\d+[чмс]/g) || []) as string[]

    // __ Дополняем до нужного количества строк &nbsp; === '\u00A0'
    const n          = 3 // Количество строк в выводе
    const countToAdd = n - matches.length
    if (countToAdd > 0) {
        matches = [...Array(countToAdd).fill('\u00A0'), ...matches]
    }
    return matches
})

const amountShow  = computed(() => props.amount !== 0)
const timeShow    = computed(() => props.time !== 0 && props.timeShow)
const percentShow = computed(() => !(!props.reference || percent.value === 0))


</script>

<style scoped>

.app-label {
    @apply flex flex-col justify-center
    p-1 m-0.5
    border focus:outline-none focus:ring-2;
}
</style>
