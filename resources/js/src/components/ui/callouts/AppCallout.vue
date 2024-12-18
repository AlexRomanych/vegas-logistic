<template>
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="show"
                 :class="[width, height, position, bgColor, borderColor, textColor, 'callout-container']"
                 @click="toggleShow">
                <span>{{ text }}</span>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import {colorsList} from "@/src/app/constants/colorsClasses.js"
import {getColorClassByType, getTextColorClassByType} from "@/src/app/helpers/helpers.js"
import {computed, ref} from "vue";

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
    pos_x: {
        type: String,
        required: false,
        default: 'right',
        validator: (pos_x) => ['left', 'right'].includes(pos_x)
    },
    pos_y: {
        type: String,
        required: false,
        default: 'bottom',
        validator: (pos_y) => ['top', 'bottom'].includes(pos_y)
    },


})

const emits = defineEmits(['toggleShow'])

const show = ref(true)
const toggleShow = () => {
    show.value = !show.value
    emits('toggleShow', show.value)
}

const getPositionClass = (pos_x, pos_y) => {
    let x_pos = ''
    let y_pos = ''

    switch (pos_x) {
        case 'left':
            x_pos = 'left-0';
            break;
        case 'right':
            x_pos = 'right-0';
            break;
    }

    switch (pos_y) {
        case 'top':
            y_pos = 'top-0';
            break;
        case 'bottom':
            y_pos = 'bottom-0';
            break;
    }

    return `${x_pos} ${y_pos}`
}

const position = getPositionClass(props.pos_x, props.pos_y)                                     // Получаем класс для позиции
const bgColor = computed(() => getColorClassByType(props.type, 'bg', 0, false))         // Получаем класс для цвета заднего фона
const borderColor = computed(() => getColorClassByType(props.type, 'border', 700))      // Получаем класс для цвета границы
const textColor = computed(() => getTextColorClassByType(props.type))

</script>


<style scoped>
/*
.fade-in {
    z-index: 100;
    animation: fadeIn 5s linear;
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
*/

.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-enter-to,
.fade-leave-from {
    opacity: 1;
}

.callout-container {
    @apply z-[500] fixed flex items-center justify-start p-10 m-1 rounded-xl font-semibold border-l-8 text-wrap break-words overflow-hidden text-ellipsis
}

</style>
