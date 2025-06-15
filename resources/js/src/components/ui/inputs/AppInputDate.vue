<template>
    <div :class="width" class="flex flex-col">
        <label v-if="label" :class="['input-label', textColor]" :for="id">{{ label }}</label>
        <input
            :id="id"
            v-model="inputDate"
            :class="['app-input', borderColor, focusBorderColor, placeholderColor]"
            :disabled="disabled"
            :max="maxDateTemp"
            :min="minDateTemp"
            step="1"
            type="date"
            @change="getInputDate"
        />
        <span v-if="error" :class="['input-error', textColor]">{{ error.message }}</span>
    </div>
</template>

<script setup>
import { colorsClasses, colorsList } from '@/app/constants/colorsClasses.js'
import { getColorClassByType } from '@/app/helpers/helpers.js'
import { getPeriod } from '@/app/helpers/helpers_date.js'
import { computed, ref } from 'vue'

const props = defineProps({
    id: {
        required: true,
    },
    type: {
        type: String,
        required: false,
        default: 'primary',
        validator: (type) => colorsList.includes(type),
    },
    label: {
        type: String,
        required: false,
        default: '',
    },
    disabled: {
        type: Boolean,
        required: false,
        default: false,
    },
    width: {
        type: String,
        required: false,
        default: 'w-[150px]',
    },
    minDate: {
        type: String,
        required: false,
        default: '',
    },
    maxDate: {
        type: String,
        required: false,
        default: '',
    },
    value: {
        type: String,
        required: false,
        // default: '2024-10-05',
    },
    error: {
        type: Object,
        required: false,
        default: null,
        message: {
            type: String,
            required: false,
            default: 'Ошибка...',
        },
    },
})

// const defaultDate_= defineModel({
//     name: 'defaultDate_',
//     type: String,
//     default: '2024-10-05'
// })

const inputDate = defineModel({
    name: 'inputDate',
    type: String,
    default: '',
})
// console.log(inputDate.value)
// const inputDate_ = defineModel({
//     name: 'inputDate_',
//     type: String,
//     default: ''
// })

const selectedDate = new Date().toISOString().slice(0, 10)
const defaultDate = selectedDate

// const selectedDate = ref(new Date().toISOString().slice(0, 10));

// const defaultDate = computed(() => {
//     // Здесь можно выполнять дополнительные логики для определения даты по умолчанию
//     return selectedDate.value;
// });

const { periodStart, periodEnd, periodStartText, periodEndText } = getPeriod()
// console.log(props.minDate)
// console.log(props.maxDate)

const minDateTemp = props.minDate === '' ? periodStartText : props.minDate
const maxDateTemp = props.maxDate === '' ? periodEndText : props.maxDate

// console.log(minDateTemp)
// console.log(maxDateTemp)
// console.trace()

// const value = ref(periodStartText)

const currentColorIndex = 600 // задаем основной индекс палитры tailwinds
const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex

const placeholderColor = 'placeholder' + currentColor
const borderColor = 'border' + currentColor
const focusBorderColor = 'focus:ring' + currentColor

let textColor = 'text' + currentColor
textColor = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())

const emit = defineEmits(['getInputDate'])

const getInputDate = (e) => emit('getInputDate', { id: props.id, value: e.target.value }) //todo сделать валидацию ввода даты

// const getInputDate = (e) => console.log(e.target.value)

// const onInput = function(e) {
//     console.log(e.target.value)
// }
// const onInput = function(inputText) {
//     console.log(inputText.target.value)
// }
</script>

<style scoped>
.app-input {
    @apply p-1 border rounded-lg focus:outline-none focus:ring-2;
}

.input-error {
    @apply text-sm ml-2 font-semibold;
}

.input-label {
    @apply text-sm font-semibold ml-2 mb-0.5;
}
</style>
