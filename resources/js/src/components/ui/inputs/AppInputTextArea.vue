<template>
    <div :class="[width, height]" class="flex flex-col">
        <!--        <label v-if="label" :class="['input-label', textColor]" :for="id">{{ label }}</label>-->
        <label v-if="label" :class="['input-label', 'text-slate-600', labelTextSizeClass]" :for="id">{{ label }}</label>

        <textarea
            :id="id"
            v-model="areaText"
            :class="[
                'app-input',
                height,
                borderColor,
                placeholderColor,
                textSizeClass,
                semibold,
                bgColor,
                textColor,
            ]"
            :disabled="disabled"
            :maxlength="maxlength"
            :placeholder="placeholder"
            :readonly="readonly"
            :cols="cols"
            :rows="rows"
            @input="getAreaText"
        />

        <div v-if="errors">
            <div v-for="(err, index) in errors" :key="index">
                <span :class="['input-error', textColorErrors]">
                    {{ err.$message }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { colorsClasses, colorsList } from '@/app/constants/colorsClasses.js'
// import {colorsClasses, colorsList} from '/js/src/app/constants/colorsClasses.js'
import {
    getColorClassByType,
    getFontSizeClass,
    getTextColorClassByType,
} from '@/app/helpers/helpers.js'
import { computed, ref, watch } from 'vue'
import { fontSizesList } from '@/app/constants/fontSizes.js'

const props = defineProps({
    id: {
        required: true,
    },
    type: {
        type: String,
        required: false,
        default: 'dark',
        validator: (type) => colorsList.includes(type),
    },
    value: {
        type: String,
        required: false,
        default: '',
    },
    placeholder: {
        type: String,
        required: false,
        default: 'Enter...',
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
        default: 'w-[500px]',
    },
    height: {
        type: String,
        required: false,
        default: 'h-[30px]',
    },
    rows: {
        type: Number,
        required: false,
        default: 3,
    },
    cols: {
        type: Number,
        required: false,
        default: 50,
    },
    bold: {
        type: Boolean,
        required: false,
        default: false,
    },
    readonly: {
        type: Boolean,
        required: false,
        default: false,
    },
    maxlength: {
        type: Number,
        required: false,
        default: 500,
    },
    errors: {
        type: Array,
        required: false,
        default: [],
    },
    errorsType: {
        type: String,
        required: false,
        default: 'danger',
        validator: (type) => colorsList.includes(type),
    },
    textSize: {
        type: String,
        required: false,
        default: 'normal',
        validator: (size) => fontSizesList.includes(size),
    },
    labelTextSize: {
        type: String,
        required: false,
        default: 'mini',
        validator: (size) => fontSizesList.includes(size),
    },
})

// todo: Доработать этот элемент
// rows - Определяет видимое количество строк в текстовом поле.
// cols - Определяет видимую ширину текстового поля в символах.
// name - Указывает имя текстового поля, которое используется для отправки данных формы на сервер.
// placeholder - Отображает подсказку внутри текстового поля до того, как пользователь начнет вводить текст.
// disabled - Отключает текстовое поле, делая его недоступным для ввода.
// readonly - Делает текстовое поле доступным только для чтения, запрещая ввод.
// required - Указывает, что текстовое поле обязательно для заполнения перед отправкой формы.
// maxlength - Определяет максимальное количество символов, которое пользователь может ввести в текстовое поле.

const currentColorIndex = 600 // задаем основной индекс палитры tailwinds
const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex
const currentColorErrors =
    computed(() => getColorClassByType(props.errorsType)).value + currentColorIndex

const placeholderColor = 'placeholder' + currentColor
const borderColor = 'border' + currentColor
// const focusBorderColor = 'focus:ring-3' + currentColor

// let textColor = 'text' + currentColor
let textColorErrors = 'text' + currentColorErrors

let bgColor = ref(getColorClassByType(props.type, 'bg')) // Получаем класс для цвета заднего фона
let textColor = ref(getTextColorClassByType(props.type))

// textColor = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())
textColorErrors = textColorErrors.replace(
    currentColorIndex.toString(),
    (currentColorIndex + 200).toString(),
)

const areaText = defineModel({
    type: String,
    default: '',
})

// Задаем начальное значение
areaText.value = props.value

const emit = defineEmits(['getAreaText'])

const getAreaText = (e) => emit('getAreaText', e.target.value)

const textSizeClass = ref(getFontSizeClass(props.textSize))
const labelTextSizeClass = ref(getFontSizeClass(props.labelTextSize))
const semibold = props.bold ? 'font-semibold' : ''
</script>

<style scoped>
.app-input {
    @apply border-2 rounded-lg m-0.5 p-1 outline-none;
}

.input-label {
    @apply font-semibold ml-2 mb-0.5 mt-2;
}

.input-error {
    @apply text-mc ml-2 font-semibold;
}
</style>
