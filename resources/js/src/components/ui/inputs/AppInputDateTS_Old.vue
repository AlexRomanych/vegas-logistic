<template>
    <div :class="width" class="flex flex-col">
        <label v-if="label" :class="['input-label', textColor]" :for="id">{{ label }}</label>

        <VueDatePicker
            v-model="date"
            :action-row="{
                showPreview: false,
                showNow: true,
                selectBtnLabel: 'Выбор',
                cancelBtnLabel: 'Отмена',
                nowBtnLabel: 'Текущая',}"
            :disabled="disabled"
            :formats="{input: 'dd.MM.yyyy г.'}"
            :input-attrs="{
                clearable,
                /*hideInputIcon: true, убираем крестик*/
                id,
            }"
            :locale="ru"
            :max-date="maxDate"
            :min-date="minDate"
            :time-config="{enableTimePicker: false /*убираем выбор времени*/}"
            :timePicker="false"
            class="custom-datepicker"
            dark
            @update:model-value="selectDate"
        />

    </div>

</template>

<script lang="ts" setup>
import type { IColorTypes } from '@/app/constants/colorsClasses.js'
import { getColorClassByType } from '@/app/helpers/helpers.js'
import { computed, ref } from 'vue'
import { ru } from 'date-fns/locale'
import { VueDatePicker } from '@vuepic/vue-datepicker'

import '@vuepic/vue-datepicker/dist/main.css'


interface IProps {
    id: string
    modelValue?: string | Date | null
    type?: IColorTypes
    label?: string
    disabled?: boolean
    clearable?: boolean
    width?: string
    minDate?: Date | string | number,
    maxDate?: Date | string | number,
}

const props = withDefaults(defineProps<IProps>(), {
    type: 'dark',
    modelValue: '',
    label: '',
    disabled: false,
    width: 'w-[150px]',
    minDate: undefined,
    maxDate: undefined,
    clearable: false,
})

const emits = defineEmits<{
    (e: 'update:model-value', payload: Date | null): void,
}>()

const dateFormatter = (value: Date | string | null): Date | null => {
    if (value === null) return null
    if (typeof value === 'string') {
        if (!isNaN(new Date(value).getTime())) return new Date(value)
        // if (value === '') return new Date()
        return new Date()
    } else if (value) {
        if (!isNaN(value.getTime())) {
            return value
        } else {
            return new Date()
        }
    }
    return new Date()
}

const date = ref<Date | null>(dateFormatter(props.modelValue))

const selectDate = () => emits('update:model-value', date.value)

const currentColorIndex = 600 // задаем основной индекс палитры tailwinds
const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex

// const placeholderColor = computed(() => 'placeholder' + currentColor)
// const borderColor = computed(() => 'border' + currentColor)
// const focusBorderColor = computed(() => 'focus:ring' + currentColor)

let textColor = 'text' + currentColor
textColor = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())


</script>

<style scoped>
/*@reference "@css/app.css";*/

.input-label {
    @apply font-semibold ml-3;
    font-size: 0.725rem;
}

</style>

<style>
/*@reference "@css/app.css";*/
.custom-datepicker {
    @apply font-semibold;

    --dp-background-color: #002B36; /* #62748E - Slate */
    --dp-border-radius: 4px; /* Немного меньше радиус */
    --dp-menu-border-radius: 4px;
    --dp-font-family: 'Raleway', sans-serif;
    --dp-primary-color: #4f46e5;

    /* --- ИЗМЕНЕНИЯ ДЛЯ УМЕНЬШЕНИЯ РАЗМЕРА --- */
    --dp-font-size: 0.7rem; /* Уменьшаем размер шрифта (14px) */
    /* Уменьшаем вертикальный отступ для уменьшения высоты */
    --dp-input-padding: 5px 30px 6px 6px;
    /* --- КОНЕЦ ИЗМЕНЕНИЙ --- */
}

.dp__theme_dark {
    --dp-background-color: #002B36;
}


</style>
