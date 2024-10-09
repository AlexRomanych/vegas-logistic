<template>
    <div id="id" :class="[width, currentTextColor, backgroundColor, borderColor, 'btn-container']">
        <button
            class="btn"
            type="button"
            @click="buttonClick"
        >
            {{ title }}
        </button>
    </div>
</template>

<script setup>
import {colorsList} from "@/src/app/constants/colorsClasses.js"
import {getColorClassByType, getTextColorClassByType} from "@/src/app/helpers/helpers.js"
import {computed} from "vue";

const props = defineProps({
    id: {
        type: String,
        required: false,
        default: Date.now().toString()
    },
    type: {
        type: String,
        required: false,
        default: 'primary',
        validator: (type) => colorsList.includes(type)
    },
    title: {
        type: String,
        required: false,
        default: 'Action'
    },
    width: {
        type: String,
        required: false,
        default: 'w-[100px]',
    },


})

const emit = defineEmits(['buttonClick'])
const buttonClick = () => emit('buttonClick', props.title)

const currentTextColor = computed(() => getTextColorClassByType(props.type)).value
const backgroundColor = getColorClassByType(props.type, 'bg')
const borderColor = computed(() => getColorClassByType(props.type, 'border')).value

</script>


<style scoped>
.btn-container {
    @apply ml-1 mt-1 p-1 border-2 rounded-lg cursor-pointer flex justify-center items-center;
}
.btn {
    @apply w-full h-full;
}

</style>
