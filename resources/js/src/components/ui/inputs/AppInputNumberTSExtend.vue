<template>
    <input
        ref="inputRef"
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
            textSelectAvailable,
            textDirection,
            disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-text',
            isFocused ? 'excel-focused' : '',
        ]"
        :disabled="disabled"
        :placeholder="placeholder"
        :readonly="readonly"
        :style="color ? { 'background-color': color } : ''"
        :title="title"
        :value="isFocused ? internalRawValue : formattedDisplayValue"
        class="flex items-center app-number-cell"
        inputmode="decimal"
        type="text"
        @blur="onBlur"
        @focus="onFocus"
        @input="onInput"
        @keydown="onKeyDown"
    />
</template>

<script lang="ts" setup>
import { computed, ref, watch, nextTick } from 'vue'

import type { IColorTypes, IFontsType, IHorizontalAlign } from '@/types'

import {
    getColorClassByType,
    getTextColorClassByType,
    getFontSizeClass,
    getHorizontalAlignText,
    getRoundedClass,
} from '@/app/helpers/helpers.js'

interface IProps {
    modelValue?: number | string | null
    type?: IColorTypes
    width?: string
    height?: string
    textSize?: IFontsType
    bold?: boolean
    align?: IHorizontalAlign
    title?: string
    rounded?: string
    color?: string
    textSelect?: boolean
    direction?: 'row' | 'column'
    placeholder?: string
    disabled?: boolean
    readonly?: boolean
    // __ Дополнительные опции для чисел (Excel)
    precision?: number        // Количество знаков после запятой (например, 2)
    min?: number              // Минимальное значение
    max?: number              // Максимальное значение
    allowNegative?: boolean   // Разрешить отрицательные числа
    formatNumber?: boolean    // Разделять ли тысячи пробелами в режиме просмотра
}

const props = withDefaults(defineProps<IProps>(), {
    modelValue   : null,
    type         : 'dark',
    width        : 'w-[120px]',
    height       : 'h-[30px]',
    textSize     : 'normal',
    bold         : true,
    align        : 'right', // В Excel числа выравниваются по правому краю
    title        : '',
    rounded      : 'rounded-none', // В Excel ячейки с прямыми углами
    color        : '',
    textSelect   : true,
    direction    : 'row',
    placeholder  : '0',
    disabled     : false,
    readonly     : false,
    precision    : undefined,
    min          : undefined,
    max          : undefined,
    allowNegative: true,
    formatNumber : true,
})

const emits = defineEmits<{
    (e: 'update:modelValue', value: number | null): void
    (e: 'change', value: number | null): void
    (e: 'enter', value: number | null): void
    (e: 'escape'): void
    (e: 'focus', event: FocusEvent): void
    (e: 'blur', event: FocusEvent): void
}>()

const inputRef                         = ref<HTMLInputElement | null>(null)
const isFocused                        = ref(false)
const internalRawValue                 = ref<string>('') // Сырая строка во время редактирования
let initialValueOnFocus: number | null = null

// __ Синхронизация с внешним v-model
watch(
    () => props.modelValue,
    (val) => {
        if (!isFocused.value) {
            syncInternalValue(val)
        }
    },
    { immediate: true }
)

function syncInternalValue(val: number | string | null) {
    if (val === null || val === undefined || val === '') {
        internalRawValue.value = ''
    } else {
        internalRawValue.value = String(val).replace('.', ',')
    }
}

// __ Преобразование строки в парсируемое число
const parseToNumber = (val: string): number | null => {
    if (!val || val.trim() === '' || val === '-') return null

    // __ Заменяем запятую на точку для parseFloat
    const normalized = val.replace(',', '.')
    let num          = parseFloat(normalized)

    if (isNaN(num)) return null

    // __ Ограничения min/max
    if (props.min !== undefined && num < props.min) num = props.min
    if (props.max !== undefined && num > props.max) num = props.max

    // __ Точность округления
    if (props.precision !== undefined) {
        num = Number(num.toFixed(props.precision))
    }

    return num
}

// __ Форматированное отображение (когда ячейка НЕ в фокусе)
const formattedDisplayValue = computed(() => {
    const num = parseToNumber(internalRawValue.value)
    if (num === null) return ''

    if (!props.formatNumber) {
        return props.precision !== undefined ? num.toFixed(props.precision) : String(num)
    }

    // Красивое Excel-форматирование с пробелами (1 234 567.89)
    const parts = (props.precision !== undefined ? num.toFixed(props.precision) : String(num)).split('.')
    parts[0]    = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ')

    return parts.join(',')
})

// __ Обработка события ввода (Фильтрация символов)
const onInput = (e: Event) => {
    const target = e.target as HTMLInputElement
    let val      = target.value

    // __ Заменяем точку на запятую для единообразия в RU локали
    val = val.replace('.', ',')

    // __ Формируем регулярное выражение разрешенных символов
    const negPattern = props.allowNegative ? '^-?' : '^'
    const regex      = new RegExp(`${negPattern}\\d*,?\\d*`)

    const match      = val.match(regex)
    const cleanValue = match ? match[0] : ''

    internalRawValue.value = cleanValue
    target.value           = cleanValue
}

// __ Фокус на ячейке (Excel behavior: выделить всё)
const onFocus = (e: FocusEvent) => {
    if (props.readonly || props.disabled) return

    isFocused.value     = true
    initialValueOnFocus = parseToNumber(internalRawValue.value)

    emits('focus', e)

    // Выделяем весь текст в ячейке, как в Excel
    nextTick(() => {
        if (inputRef.value) {
            inputRef.value.select()
        }
    })
}

// __ Потеря фокуса (Сохранение)
const onBlur = (e: FocusEvent) => {
    isFocused.value    = false
    const numericValue = parseToNumber(internalRawValue.value)

    syncInternalValue(numericValue)

    emits('update:modelValue', numericValue)
    emits('change', numericValue)
    emits('blur', e)
}

// __ Нажатия клавиш (Enter / Escape / Tab)
const onKeyDown = (e: KeyboardEvent) => {
    if (e.key === 'Enter') {
        const numericValue = parseToNumber(internalRawValue.value)
        emits('enter', numericValue)
        inputRef.value?.blur() // Выход из режима редактирования
    } else if (e.key === 'Escape') {
        // Отмена изменений
        syncInternalValue(initialValueOnFocus)
        emits('escape')
        inputRef.value?.blur()
    }
}

// __ Computed стили
const currentColorIndex   = 500
const backgroundColor     = computed(() => getColorClassByType(props.type, 'bg', currentColorIndex))
const borderColor         = computed(() => getColorClassByType(props.type, 'border', currentColorIndex))
const currentTextColor    = computed(() => getTextColorClassByType(props.type))
const textSizeClass       = computed(() => getFontSizeClass(props.textSize))
const horizontalAlign     = computed(() => getHorizontalAlignText(props.align))
const semibold            = computed(() => (props.bold ? 'font-semibold' : ''))
const textSelectAvailable = computed(() => (props.textSelect ? '' : 'select-none'))
const roundedCSS          = computed(() => getRoundedClass(props.rounded))

// __ Вертикальный текст
const textDirection = computed(() =>
    props.direction === 'column' ? '[writing-mode:vertical-rl] rotate-180 whitespace-normal' : ''
)
</script>

<style scoped>
.app-number-cell {
    @apply
    p-1 m-0.5
    border focus:outline-none focus:ring-2
    cursor-pointer
    transition-all duration-75
    outline-none;
}

/* Зеленая рамка активной ячейки Excel */
.excel-focused {
    @apply
    border-2 border-emerald-600
    z-10 shadow-sm
    ring-1 ring-emerald-600
    bg-white text-black;
}
</style>



<!--Фрагмент кода-->
<!--<template>-->
<!--    <div class="p-4 flex gap-2 items-center">-->
<!--        &lt;!&ndash; Ячейка ввода цены с 2 знаками после запятой &ndash;&gt;-->
<!--        <AppInputNumberCellTSWrapper-->
<!--            v-model="price"-->
<!--            type="light"-->
<!--            width="w-[140px]"-->
<!--            height="h-[32px]"-->
<!--            textSize="normal"-->
<!--            :precision="2"-->
<!--            placeholder="0,00"-->
<!--            @change="onPriceChange"-->
<!--        />-->

<!--        &lt;!&ndash; Простая ячейка количества (целое число) &ndash;&gt;-->
<!--        <AppInputNumberCellTSWrapper-->
<!--            v-model="quantity"-->
<!--            type="dark"-->
<!--            width="w-[80px]"-->
<!--            :precision="0"-->
<!--            :min="1"-->
<!--        />-->
<!--    </div>-->
<!--</template>-->

<!--<script setup lang="ts">-->
<!--import { ref } from 'vue'-->
<!--import AppInputNumberCellTSWrapper from '@/components/ui/inputs/AppInputNumberCellTSWrapper.vue'-->

<!--const price = ref<number | null>(1250.5)-->
<!--const quantity = ref<number | null>(10)-->

<!--const onPriceChange = (val: number | null) => {-->
<!--    console.log('Новая цена:', val)-->
<!--}-->
<!--</script>-->
