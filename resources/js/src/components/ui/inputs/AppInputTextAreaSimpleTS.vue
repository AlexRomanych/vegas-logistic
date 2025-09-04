<template>
    <div :class="[width, height]" class="flex flex-col ml-1 mr-1 mt-2">

        <label v-if="label" :class="['input-label', textColor, labelTextSizeClass ]" :for="id">{{ label }}</label>

        <textarea
            :id="id"
            v-model="textModel"

            :class="['app-input', borderColor, focusBorderColor, placeholderColor, textSizeClass, semibold]"

            :cols="cols"
            :disabled="disabled"
            :maxlength="maxlength"
            :placeholder="placeholder"

            :readonly="read"
            :rows="rows"
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


<script lang="ts" setup>
import { computed, ref } from 'vue'

import type { IColorTypes } from '@/app/constants/colorsClasses.js'
import type { IFontsType } from '@/app/constants/fontSizes.js'

import { getColorClassByType, getFontSizeClass } from '@/app/helpers/helpers.js'

interface IProps {
    id: string
    type?: IColorTypes
    placeholder?: string
    label?: string
    disabled?: boolean
    width?: string
    height?: string
    rows?: number
    cols?: number
    bold?: boolean
    read?: boolean
    maxlength?: number
    errors?: any[] | null
    errorsType?: IColorTypes
    textSize?: IFontsType
    labelTextSize?: IFontsType
}

const props = withDefaults(defineProps<IProps>(), {
    type: 'dark',
    placeholder: 'Enter...',
    label: '',
    disabled: false,
    width: 'w-[500px]',
    height: 'h-[30px]',
    rows: 3,
    cols: 50,
    bold: true,
    read: false,
    maxlength: 500,
    errors: null,
    errorsType: 'danger',
    textSize: 'small',
    labelTextSize: 'mini',
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

// __ Задаем модель
const textModel = defineModel<string>('textValue', {required: true})

const currentColorIndex = 600       // задаем основной индекс палитры tailwinds
const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex

const placeholderColor = 'placeholder' + currentColor
const borderColor = 'border' + currentColor
const focusBorderColor = 'focus:ring' + currentColor

let textColor = 'text' + currentColor
// attract: Делает цвет темнее
// textColor = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())

const textSizeClass = ref(getFontSizeClass(props.textSize))
const labelTextSizeClass = ref(getFontSizeClass(props.labelTextSize))

const semibold = props.bold ? 'font-semibold' : ''


// const currentColorIndex = 600       // задаем основной индекс палитры tailwinds
// const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex
const currentColorErrors = computed(() => getColorClassByType(props.errorsType)).value + currentColorIndex
//
// const placeholderColor = 'placeholder' + currentColor
// const borderColor = 'border' + currentColor
// const focusBorderColor = 'focus:ring-3' + currentColor

// let textColor = 'text' + currentColor
let textColorErrors = 'text' + currentColorErrors

// let bgColor = ref(getColorClassByType(props.type, 'bg'))                   // Получаем класс для цвета заднего фона
// let textColor = ref(getTextColorClassByType(props.type))

// textColor = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())
textColorErrors = textColorErrors.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())

</script>

<style scoped>
.app-input {
    @apply p-1 border rounded-lg focus:outline-none focus:ring-2;
}

.app-input::placeholder {
    @apply text-xs;
}

.input-error {
    @apply text-sm ml-2 font-semibold;
}

.input-label {
    @apply font-semibold ml-2 mb-0.5 mt-2
}
</style>

