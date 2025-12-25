<script setup>
import { reactive, computed, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '#6366f1' }
});
const emit = defineEmits(['update:modelValue', 'select']);

// Используем объект вместо отдельных переменных, чтобы обращаться по ключу в цикле
const channels = reactive({
    r: 99,
    g: 102,
    b: 241
});

const rgbColor = computed(() => `rgb(${channels.r}, ${channels.g}, ${channels.b})`);

const hexColor = computed(() => {
    const toHex = (n) => Math.min(255, Math.max(0, n)).toString(16).padStart(2, '0');
    return `#${toHex(channels.r)}${toHex(channels.g)}${toHex(channels.b)}`.toUpperCase();
});

const handleSelect = () => {
    emit('update:modelValue', hexColor.value);
    emit('select', hexColor.value);
};

// Валидация через глубокое слежение за объектом
watch(channels, (newVal) => {
    if (newVal.r > 255) channels.r = 255;
    if (newVal.g > 255) channels.g = 255;
    if (newVal.b > 255) channels.b = 255;
}, { deep: true });

// Конфигурация для цикла
const config = [
    { key: 'r', label: 'Red', colorClass: 'text-red-500', accent: 'accent-red-500 bg-red-50' },
    { key: 'g', label: 'Green', colorClass: 'text-green-500', accent: 'accent-green-500 bg-green-50' },
    { key: 'b', label: 'Blue', colorClass: 'text-blue-500', accent: 'accent-blue-500 bg-blue-50' }
];
</script>

<template>
    <div class="max-w-sm p-6 bg-white rounded-3xl shadow-2xl border border-slate-100">
        <div
            class="w-full h-32 rounded-2xl mb-8 transition-all duration-300 flex items-center justify-center shadow-lg"
            :style="{ backgroundColor: rgbColor }"
        >
      <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-sm font-mono font-bold">
        {{ hexColor }}
      </span>
        </div>

        <div class="space-y-5">
            <div v-for="item in config" :key="item.key" class="space-y-1">
                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <span :class="item.colorClass">{{ item.label }}</span>
                    <input
                        type="number"
                        v-model.number="channels[item.key]"
                        class="w-10 bg-transparent text-right outline-none focus:text-slate-800"
                    />
                </div>
                <input
                    type="range" min="0" max="255"
                    v-model.number="channels[item.key]"
                    :class="['w-full h-1.5 rounded-lg appearance-none cursor-pointer', item.accent]"
                />
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
            <div class="text-base font-mono font-black text-slate-700">{{ hexColor }}</div>
            <button
                @click="handleSelect"
                class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all active:scale-95 shadow-md"
            >
                Выбрать
            </button>
        </div>
    </div>
</template>
