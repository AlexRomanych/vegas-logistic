<template>


<!--    <div-->
<!--        :class="[width, height, bgColor, textColor]"-->
<!--        class=" flex flex-col ml-1 mr-1 mt-2 items-center justify-around rounded-lg border-2">-->
<!--        <label v-if="label" :class="['input-label', textColor, labelTextSizeClass ]" :for="id">{{ label }}</label>-->
<!--        <input-->
<!--            :class="['app-input border-2 bg-slate-600', borderColor, focusBorderColor, textSizeClass ]"-->
<!--            :id="id"-->
<!--            :checked="checked"-->
<!--            :disabled="disabled"-->
<!--            :value="id"-->
<!--            :name="name"-->
<!--            type="checkbox"-->
<!--            @change="checked"-->
<!--        />-->

<!--    </div>-->















    <div class="flex items-center justify-around rounded-lg border-2"
    :class="[width, height, bgColor, textColor]"
    >
        <input
            :id="id"
            :checked="checked"
            :disabled="disabled"
            :value="id"
            :name="name"
            type="checkbox"
            @change="checked"
        />

    </div>

</template>

<script setup>
import {computed, ref,} from 'vue'
import {fontSizesList} from '/resources/js/src/app/constants/fontSizes.js'
import {colorsList} from '/resources/js/src/app/constants/colorsClasses.js'
import {getColorClassByType, getFontSizeClass, getTextColorClassByType} from '/resources/js/src/app/helpers/helpers.js'

const props = defineProps({
    id: {
        type: String,
        required: false,
        default: Date.now().toString()
    },
    name: {
        type: String,
        required: false,
        default: 'checkbox'
    },
    width: {
        type: String,
        required: false,
        default: 'w-[300px]'
    },
    height: {
        type: String,
        required: false,
        default: 'h-[30px]'
    },
    type: {
        type: String,
        required: false,
        default: 'dark',
        validator: (type) => colorsList.includes(type)
    },
    checked: {
        type: Boolean,
        required: false,
        default: false
    },
    disabled: {
        type: Boolean,
        required: false,
        default: false
    },
    label: {
        type: String,
        required: false,
        default: ''
    },
    textSize: {
        type: String,
        required: false,
        default: 'small',
        validator: (size) => fontSizesList.includes(size)
    },
    labelTextSize: {
        type: String,
        required: false,
        default: 'mini',
        validator: (size) => fontSizesList.includes(size)
    },

})

const currentColorIndex = 600       // задаем основной индекс палитры tailwinds
const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex
const bgColor = computed(() => getColorClassByType(props.type, 'bg', 0, false))                   // Получаем класс для цвета заднего фона

const placeholderColor = 'placeholder' + currentColor
const borderColor = 'border' + currentColor
const focusBorderColor = 'focus:ring' + currentColor

let textColor = 'text' + currentColor
// attract: Делает цвет темнее
// textColor = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())

const textSizeClass = ref(getFontSizeClass(props.textSize))
const labelTextSizeClass = ref(getFontSizeClass(props.labelTextSize))


</script>

<style scoped>
.app-input {
    @apply p-1 border rounded-lg focus:outline-none focus:ring-2;
}

.input-error {
    @apply text-sm ml-2 font-semibold;
}

.input-label {
    @apply font-semibold ml-2 mb-0.5 mt-2
}


</style>
