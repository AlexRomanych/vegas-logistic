<template>
    <div :class="[width, height]"
         class="fade-in flex items-center justify-left bg-red-500 p-4 m-1 rounded-xl font-semibold border-l-8 border-red-700">
        <span>{{ text }}</span>
    </div>
</template>

<script setup>
import {colorsList} from "@/src/app/constants/colorsClasses.js"
import {getColorClassByType, getTextColorClassByType} from "@/src/app/helpers/helpers.js"

const props = defineProps({
    width: {
        type: String,
        required: false,
        default: 'w-[500px]',
    },
    height: {
        type: String,
        required: false,
        default: 'h-[100px]',
    },
    type: {
        type: String,
        required: false,
        default: 'primary',
        validator: (type) => colorsList.includes(type)
    },
    text: {
        type: String,
        required: false,
        default: 'This is a callout',
    },


})

</script>

<style scoped>
.fade-in {
    z-index: 100;
    animation: fadeIn 3s linear;
}

@keyframes fadeIn {
    0% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        display: none;
    }
}
</style>
