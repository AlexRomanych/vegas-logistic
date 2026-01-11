<template>
    <div class="min-w-[100px] max-h-[30px] my-0.5 mx-0.5 cursor-pointer inline-flex items-center px-2 space-x-2 bg-white hover:bg-slate-100 rounded transition-colors border border-slate-300"
         @click="openModal">
        <div :style="{ backgroundColor: modelValue }" class="w-[20px] h-[20px] rounded-full shadow-sm border border-white"></div>
        <span class="text-mc font-medium text-slate-600">{{ modelValue }}</span>
    </div>

    <Teleport to="body">
        <Transition name="fade">
            <div v-if="isOpen" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>

                <div class="relative transition-all transform">
                    <AppRgbPicker
                        v-model="tempColor"
                        @update:model-value="tempColor = $event"
                        @select="handleConfirm"
                    />

                    <button
                        class="absolute -top-12 right-0 text-white/70 hover:text-white transition-colors"
                        @click="closeModal"
                    >
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"/>
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import AppRgbPicker from './AppRGBPicker.vue'

interface IProps {
    modelValue: string
    title?: string
}

const props = withDefaults(defineProps<IProps>(), {
    title: 'Выберите цвет статуса',
})


const emits = defineEmits<{
    (e: 'update:modelValue', value: string): void,
    (e: 'confirm', value: string): void,
}>()

const isOpen    = ref(false)
const tempColor = ref(props.modelValue)

const openModal = () => {
    tempColor.value = props.modelValue
    isOpen.value    = true
}

const closeModal = () => {
    isOpen.value = false
}

const handleConfirm = (event: string) => {
    emits('update:modelValue', event)
    emits('confirm', event)
    closeModal()
}
</script>


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
