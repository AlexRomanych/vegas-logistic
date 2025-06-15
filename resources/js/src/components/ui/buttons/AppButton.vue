<template>
    <div
        id="id"
        :class="[width, height, currentTextColor, backgroundColor, borderColor, 'btn-container']"
    >
        <button class="btn" :type="func" @click="buttonClick">
            {{ title }}
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue'

import { colorsList } from '@/app/constants/colorsClasses.js'
import { getColorClassByType, getTextColorClassByType } from '@/app/helpers/helpers.js'

const props = defineProps({
    id: {
        type: String,
        required: false,
        default: Date.now().toString(),
    },
    type: {
        type: String,
        required: false,
        default: 'primary',
        validator: (type) => colorsList.includes(type),
    },
    func: {
        type: String,
        required: false,
        default: 'button',
        validator: (func) => ['button', 'submit', 'reset'].includes(func),
    },
    title: {
        type: String,
        required: false,
        default: 'Action',
    },
    width: {
        type: String,
        required: false,
        default: 'w-[100px]',
    },
    height: {
        type: String,
        required: false,
        default: 'min-h-[20px]',
    },
})

const emit = defineEmits(['buttonClick'])
const buttonClick = () => emit('buttonClick', props.id)

const currentTextColor = computed(() => getTextColorClassByType(props.type)).value
const backgroundColor = getColorClassByType(props.type, 'bg')
const borderColor = computed(() => getColorClassByType(props.type, 'border')).value
</script>

<style scoped>
.btn-container {
    @apply ml-1 mt-1 p-1 border-2 rounded-lg cursor-pointer flex justify-center items-center;
}
.btn {
    @apply w-full h-full font-semibold;
}
</style>
