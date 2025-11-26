<template>
    <svg
        :class="[sizeClass, customClasses]"
        fill="none"
        stroke-linecap="round"
        stroke-linejoin="round"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <defs>
            <linearGradient id="vArrowGradient" x1="0%" x2="0%" y1="0%" y2="100%">
                <stop :stop-color="gradientStart" offset="0%"/>
                <stop :stop-color="gradientEnd" offset="100%"/>
            </linearGradient>
        </defs>

        <path
            :stroke-width="strokeWidth"
            d="m6 10 6 6 6-6"
            stroke="url(#vArrowGradient)"
        />

        <path
            :stroke-width="strokeWidth"
            d="M12 4L12 10"
            stroke="url(#vArrowGradient)"
        />

    </svg>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    size: {
        type: [String, Number],
        default: '6', // Уменьшаем размер по умолчанию до 8 (32px)
    },
    gradientStart: {
        type: String,
        default: '#8A2BE2', // Фиолетовый
    },
    gradientEnd: {
        type: String,
        default: '#4B0082', // Индиго
    },
    strokeWidth: {
        type: [String, Number],
        default: '3.5', // Толщина обводки
    },
    customClasses: {
        type: String,
        default: '',
    }
})

const sizeClass = computed(() => 'w-[40px] h-[40px]')
// const sizeClass = computed(() => `w-${props.size} h-${props.size}`);

</script>

<style scoped>
svg {
    transition: all 0.3s ease;
}
</style>
