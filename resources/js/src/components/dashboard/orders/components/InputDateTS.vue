<template>
    <div :class="width" class="flex flex-col">
        <label v-if="label" :class="['input-label', textColor]" :for="id">{{ label }}</label>

        <!-- Не разобрался, но не работает :enable-time-picker="timeEnable /* Правильное имя по документации */" -->
        <VueDatePicker
            v-model="date"
            :action-row="{
                showPreview: false,
                showNow: true,
                selectBtnLabel: 'Выбор',
                cancelBtnLabel: 'Отмена',
                nowBtnLabel: 'Текущая',}"
            :disabled="disabled"
            :formats="timeEnable ? {input: onlyTime ? 'HH:mm' : 'dd.MM.yyyy HH:mm'} : {input: 'dd.MM.yyyy г.'}"
            :input-attrs="{
                clearable: false,
                /*hideInputIcon: true, убираем крестик*/
                id,
            }"
            :locale="ru"
            :max-date="maxDate"
            :min-date="minDate"
            :time-config="{enableTimePicker: timeEnable /*убираем выбор времени - делаем опциональным*/}"
            :timePicker="onlyTime /* onlyTime Если false - то есть календарь, если true - только время*/"
            class="custom-datepicker"
            dark
            @update:model-value="selectDate"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import type { IColorTypes } from '@/app/constants/colorsClasses.js'
import { getColorClassByType } from '@/app/helpers/helpers.js'
import { ru } from 'date-fns/locale'
import { VueDatePicker } from '@vuepic/vue-datepicker'

import '@vuepic/vue-datepicker/dist/main.css'
import { formatDateTime } from '@/app/helpers/helpers_date'


interface IProps {
    id: string
    modelValue?: string | Date
    type?: IColorTypes
    label?: string
    disabled?: boolean
    width?: string
    minDate?: Date | string | number,
    maxDate?: Date | string | number,
    timeEnable?: boolean
    onlyTime?: boolean
    color?: string
}

const props = withDefaults(defineProps<IProps>(), {
    type      : 'dark',
    modelValue: '',
    label     : '',
    disabled  : false,
    width     : 'w-[150px]',
    minDate   : undefined,
    maxDate   : undefined,
    timeEnable: false,
    onlyTime  : false,
    color     : '#4f46e5',
})

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string): void, // Перевели в camelCase
    // (e: 'update:modelValue', payload: Date): void, // Перевели в camelCase
    (e: 'getInputDate', payload: string): void, // Перевели в camelCase
}>()


// Изменяем тип возвращаемого значения, так как пикер в режиме времени хочет объект
const dateFormatter = (value: Date | string | undefined): any => {
    let targetDate = new Date()

    if (value instanceof Date && !isNaN(value.getTime())) {
        targetDate = value
    } else if (typeof value === 'string' && value.trim() !== '') {
        // Если пришла строка времени "14:30"
        const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]/
        if (timeRegex.test(value)) {
            const [hours, minutes] = value.split(':').map(Number)
            targetDate.setHours(hours, minutes, 0, 0)
        } else {
            const parsed = new Date(value)
            if (!isNaN(parsed.getTime())) targetDate = parsed
        }
    }

    // ЛОВУШКА ДОКУМЕНТАЦИИ: Если режим "только время", возвращаем объект времени
    if (props.onlyTime) {
        return {
            hours  : targetDate.getHours(),
            minutes: targetDate.getMinutes(),
            seconds: 0
        }
    }

    return targetDate
}

// const dateFormatter = (value: Date | string): Date => {
//     console.log('value: ', value)
//
//     if (typeof value === 'string') {
//
//         if (!isNaN(new Date(value).getTime())) {
//             console.log('reached: ', new Date(value))
//             return new Date(value)
//         }
//
//         // if (value === '') return new Date()
//
//         return new Date()
//     } else if (value) {
//         if (!isNaN(value.getTime())) {
//             return value
//         } else {
//             return new Date()
//         }
//     }
//     return new Date()
// }

const date = ref<Date>(dateFormatter(props.modelValue))
// console.log('date: ', date.value)

// const selectDate = () => emits('update:modelValue', date.value)

const selectDate = (val: any) => {
    if (!val) return

    if (props.onlyTime && val.hours !== undefined) {
        const d = new Date()
        d.setHours(val.hours, val.minutes, 0, 0)
        emits('update:modelValue', formatDateTime(d))
        // emits('update:modelValue', d)
        emits('getInputDate', formatDateTime(d))
    } else {
        emits('update:modelValue', formatDateTime(val as unknown as Date))
        // emits('update:modelValue', val)
        emits('getInputDate', formatDateTime(val as unknown as Date))
    }
}

const currentColorIndex = 600 // задаем основной индекс палитры tailwinds
const currentColor      = computed(() => getColorClassByType(props.type)).value + currentColorIndex

// const placeholderColor = computed(() => 'placeholder' + currentColor)
// const borderColor = computed(() => 'border' + currentColor)
// const focusBorderColor = computed(() => 'focus:ring' + currentColor)

let textColor = 'text' + currentColor
textColor     = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())

// Следим за тем, чтобы при изменении даты в родителе, внутренний ref тоже обновлялся
watch(() => props.modelValue, (newValue) => {
    date.value = dateFormatter(newValue)
    // console.log('date: ', date.value)
}, { deep: true })

</script>

<style scoped>

.input-label {
    @apply font-semibold ml-3;
    font-size: 0.725rem;
}

</style>

<style>
.custom-datepicker {

    --dp-background-color: #002B36; /* #62748E - Slate */
    --dp-border-radius: 4px; /* Немного меньше радиус */
    --dp-menu-border-radius: 8px;
    --dp-font-family: 'Inter', sans-serif;
    --dp-primary-color: #4f46e5;

    /* --- ИЗМЕНЕНИЯ ДЛЯ УМЕНЬШЕНИЯ РАЗМЕРА --- */
    --dp-font-size: 0.725rem; /* Уменьшаем размер шрифта (14px) */
    /* Уменьшаем вертикальный отступ для уменьшения высоты */
    --dp-input-padding: 10px 30px 8px 8px;
    /* --- КОНЕЦ ИЗМЕНЕНИЙ --- */
}

.dp__theme_dark {
    --dp-background-color: v-bind(color);
   /* --dp-background-color: #4f46e5;*/
}


</style>
