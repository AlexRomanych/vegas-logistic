<template>

    <div class="flex items-center justify-around rounded-lg border-2"
    :class="[width, height, bgColor, textColor]"
    >
        <input
            :id="id"
            :checked="checked"
            :disabled="disabled"
            :value="id"
            name="checkbox"
            type="checkbox"
            @change="checked"
        />

    </div>

</template>

<script setup>
import {colorsList} from '/resources/js/src/app/constants/colorsClasses.js'
import {getColorClassByType, getTextColorClassByType} from "/resources/js/src/app/helpers/helpers.js"
import {computed, reactive} from "vue";

const props = defineProps({
    id: {
        type: String,
        required: false,
        default: Date.now().toString()
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
    }

})

const bgColor = computed(() => getColorClassByType(props.type, 'bg', 0, false))                   // Получаем класс для цвета заднего фона
const textColor = computed(() => getTextColorClassByType(props.type))

</script>

<style scoped>

.check-box-data-container {
    @apply m-1 rounded-lg;
}

.fieldset-container {
    @apply m-1 p-1 border-2 rounded-lg;
}



</style>
