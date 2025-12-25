<script setup>
import { reactive, computed, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '#6366F1' }
});
const emit = defineEmits(['update:modelValue', 'select']);

const channels = reactive({ r: 99, g: 102, b: 241 });

// Проверка поддержки EyeDropper API браузером
const isEyeDropperSupported = 'EyeDropper' in window;

// Функция активации пипетки
const openEyeDropper = async () => {
    if (!isEyeDropperSupported) {
        alert('Ваш браузер не поддерживает пипетку');
        return;
    }

    const eyeDropper = new window.EyeDropper();

    try {
        const result = await eyeDropper.open();
        // Результат приходит в формате HEX (например, #ffffff)
        applyHex(result.sRGBHex);
    } catch (e) {
        // Пользователь нажал Esc или закрыл пипетку
        console.log('Выбор цвета отменен');
    }
};

// Универсальный парсер HEX в RGB
const applyHex = (hex) => {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    if (result) {
        channels.r = parseInt(result[1], 16);
        channels.g = parseInt(result[2], 16);
        channels.b = parseInt(result[3], 16);
    }
};

// ... остальные computed свойства (hexColor, rgbColor) как в предыдущем ответе ...
const rgbColor = computed(() => `rgb(${channels.r}, ${channels.g}, ${channels.b})`);
const hexColor = computed(() => {
    const toHex = (n) => Math.min(255, Math.max(0, n)).toString(16).padStart(2, '0');
    return `#${toHex(channels.r)}${toHex(channels.g)}${toHex(channels.b)}`.toUpperCase();
});
</script>

<template>
    <div class="max-w-sm p-6 bg-white rounded-3xl shadow-2xl border border-slate-100">
        <div
            class="relative w-full h-24 rounded-2xl mb-6 transition-all duration-500 flex items-center justify-center shadow-inner group"
            :style="{ backgroundColor: rgbColor }"
        >
      <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-sm font-mono font-black shadow-sm">
        {{ hexColor }}
      </span>

            <button
                v-if="isEyeDropperSupported"
                @click="openEyeDropper"
                class="absolute right-3 bottom-3 p-2 bg-white rounded-full shadow-md hover:scale-110 active:scale-95 transition-all text-slate-600 hover:text-indigo-600"
                title="Использовать пипетку"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-3" />
                </svg>
            </button>
        </div>

        <div class="mb-8 flex justify-center space-x-3">
            <button
                v-for="color in ['#EF4444', '#F59E0B', '#10B981', '#3B82F6']"
                :key="color"
                @click="applyHex(color)"
                class="w-6 h-6 rounded-full border border-white shadow-sm hover:scale-125 transition-transform"
                :style="{ backgroundColor: color }"
            ></button>
        </div>

        <div class="space-y-4 mb-8">
            <div v-for="key in ['r', 'g', 'b']" :key="key">
                <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase mb-1">
                    <span>{{ key }}</span>
                    <span>{{ channels[key] }}</span>
                </div>
                <input
                    type="range" min="0" max="255"
                    v-model.number="channels[key]"
                    class="w-full h-1.5 rounded-lg appearance-none cursor-pointer accent-slate-800 bg-slate-100"
                />
            </div>
        </div>

        <button
            @click="emit('select', hexColor)"
            class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg transition-all active:scale-95"
        >
            ВЫБРАТЬ ЦВЕТ
        </button>
    </div>
</template>
