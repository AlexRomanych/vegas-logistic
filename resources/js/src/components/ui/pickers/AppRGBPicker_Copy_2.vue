<script setup>
import { reactive, computed, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '#6366F1' }
});
const emit = defineEmits(['update:modelValue', 'select']);

const channels = reactive({ r: 99, g: 102, b: 241 });

// Палитра готовых статусов
const presets = [
    { name: 'Новый', hex: '#3B82F6', description: 'Синий (Инфо)' },
    { name: 'В работе', hex: '#F59E0B', description: 'Оранжевый (Ожидание)' },
    { name: 'Успех', hex: '#10B981', description: 'Зеленый (Готово)' },
    { name: 'Ошибка', hex: '#EF4444', description: 'Красный (Отказ)' },
    { name: 'Архив', hex: '#64748B', description: 'Серый' },
];

// Функция для применения пресета
const applyPreset = (hex) => {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    if (result) {
        channels.r = parseInt(result[1], 16);
        channels.g = parseInt(result[2], 16);
        channels.b = parseInt(result[3], 16);
    }
};

const rgbColor = computed(() => `rgb(${channels.r}, ${channels.g}, ${channels.b})`);
const hexColor = computed(() => {
    const toHex = (n) => Math.min(255, Math.max(0, n)).toString(16).padStart(2, '0');
    return `#${toHex(channels.r)}${toHex(channels.g)}${toHex(channels.b)}`.toUpperCase();
});

const handleSelect = () => {
    emit('update:modelValue', hexColor.value);
    emit('select', hexColor.value);
};

const config = [
    { key: 'r', label: 'R', colorClass: 'text-red-500', accent: 'accent-red-500 bg-red-50' },
    { key: 'g', label: 'G', colorClass: 'text-green-500', accent: 'accent-green-500 bg-green-50' },
    { key: 'b', label: 'B', colorClass: 'text-blue-500', accent: 'accent-blue-500 bg-blue-50' }
];
</script>

<template>
    <div class="max-w-sm p-6 bg-white rounded-3xl shadow-2xl border border-slate-100">
        <div
            class="w-full h-24 rounded-2xl mb-6 transition-all duration-500 flex items-center justify-center shadow-inner border border-black/5"
            :style="{ backgroundColor: rgbColor }"
        >
      <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-sm font-mono font-black shadow-sm">
        {{ hexColor }}
      </span>
        </div>

        <div class="mb-8">
            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 text-center">Быстрые статусы</span>
            <div class="flex justify-between px-2">
                <button
                    v-for="preset in presets"
                    :key="preset.hex"
                    @click="applyPreset(preset.hex)"
                    class="group relative"
                    :title="preset.name"
                >
                    <div
                        class="w-8 h-8 rounded-full border-2 border-white shadow-sm transition-transform group-hover:scale-125 group-active:scale-90"
                        :style="{ backgroundColor: preset.hex }"
                    ></div>
                    <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[8px] font-bold text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
            {{ preset.name }}
          </span>
                </button>
            </div>
        </div>

        <div class="space-y-4 mb-8">
            <div v-for="item in config" :key="item.key">
                <div class="flex justify-between items-center mb-1">
                    <label :class="['text-xs font-black', item.colorClass]">{{ item.label }}</label>
                    <input
                        type="number" v-model.number="channels[item.key]"
                        class="w-12 text-right text-xs font-bold text-slate-600 bg-transparent outline-none"
                    />
                </div>
                <input
                    type="range" min="0" max="255"
                    v-model.number="channels[item.key]"
                    :class="['w-full h-1.5 rounded-lg appearance-none cursor-pointer', item.accent]"
                />
            </div>
        </div>

        <button
            @click="handleSelect"
            class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white text-sm font-black rounded-2xl transition-all active:scale-[0.98] shadow-xl shadow-slate-200"
        >
            ПОДТВЕРДИТЬ ВЫБОР
        </button>
    </div>
</template>
