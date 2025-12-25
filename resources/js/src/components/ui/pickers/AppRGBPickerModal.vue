<script setup>
import { ref } from 'vue';
import RgbPicker from './AppRGBPicker.vue'; // Предыдущий компонент

const props = defineProps({
    modelValue: String,
    title: { type: String, default: 'Выберите цвет статуса' }
});

const emit = defineEmits(['update:modelValue', 'confirm']);

const isOpen = ref(true);
const tempColor = ref(props.modelValue);

const openModal = () => {
    tempColor.value = props.modelValue;
    isOpen.value = true;
};

const closeModal = () => {
    isOpen.value = false;
};

const handleConfirm = (color) => {
    emit('update:modelValue', color);
    emit('confirm', color);
    closeModal();
};
</script>

<template>
    <div @click="openModal" class="cursor-pointer inline-flex items-center space-x-2 p-2 hover:bg-slate-50 rounded-lg transition-colors border border-dashed border-slate-300">
        <div class="w-6 h-6 rounded-full shadow-sm border border-white" :style="{ backgroundColor: modelValue }"></div>
        <span class="text-sm font-medium text-slate-600">{{ modelValue }}</span>
    </div>

    <Teleport to="body">
        <Transition name="fade">
            <div v-if="isOpen" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>

                <div class="relative transition-all transform">
                    <RgbPicker
                        v-model="tempColor"
                        @select="handleConfirm"
                    />

                    <button
                        @click="closeModal"
                        class="absolute -top-12 right-0 text-white/70 hover:text-white transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* Анимация появления */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Эффект масштабирования для контента */
.fade-enter-active .relative {
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.fade-enter-from .relative {
    transform: scale(0.9) translateY(20px);
}
</style>
