
<script setup>
import { ref, computed, watch } from 'vue';

// Пропсы для начального цвета и события для синхронизации с родителем
const props = defineProps({
    modelValue: {
        type: String,
        default: '#6366f1'
    }
});
const emit = defineEmits(['update:modelValue']);

// Каналы цвета
const r = ref(99);
const g = ref(102);
const b = ref(241);

// Вычисляем CSS строку цвета
const rgbColor = computed(() => `rgb(${r.value}, ${g.value}, ${b.value})`);

// Преобразование в HEX для вывода (удобно для БД)
const hexColor = computed(() => {
    const toHex = (n) => n.toString(16).padStart(2, '0');
    return `#${toHex(r.value)}${toHex(g.value)}${toHex(b.value)}`.toUpperCase();
});

// Следим за изменениями и отправляем наверх
watch([r, g, b], () => {
    emit('update:modelValue', hexColor.value);
});

// Вспомогательная функция для копирования
const copyToClipboard = () => {
    navigator.clipboard.writeText(hexColor.value);
    alert('Цвет скопирован!');
};
</script>

<template>
    <div class="max-w-sm p-6 bg-white rounded-2xl shadow-xl border border-slate-100">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Выбор цвета</h3>

        <div
            class="w-full h-32 rounded-xl mb-8 shadow-inner border border-white transition-colors duration-200 flex items-end justify-end p-3"
            :style="{ backgroundColor: rgbColor }"
        >
            <button
                @click="copyToClipboard"
                class="bg-white/20 hover:bg-white/40 backdrop-blur-md text-white text-xs font-bold py-1 px-3 rounded-lg border border-white/30 transition-all"
            >
                {{ hexColor }}
            </button>
        </div>

        <div class="space-y-6">
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-bold text-red-500 uppercase tracking-wider">
                    <span>Red</span>
                    <span>{{ r }}</span>
                </div>
                <input type="range" min="0" max="255" v-model.number="r"
                       class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-red-500" />
            </div>

            <div class="space-y-2">
                <div class="flex justify-between text-xs font-bold text-green-500 uppercase tracking-wider">
                    <span>Green</span>
                    <span>{{ g }}</span>
                </div>
                <input type="range" min="0" max="255" v-model.number="g"
                       class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-green-500" />
            </div>

            <div class="space-y-2">
                <div class="flex justify-between text-xs font-bold text-blue-500 uppercase tracking-wider">
                    <span>Blue</span>
                    <span>{{ b }}</span>
                </div>
                <input type="range" min="0" max="255" v-model.number="b"
                       class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-500" />
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-50 flex items-center justify-between">
            <div class="flex space-x-2">
                <div class="text-center">
                    <span class="block text-[10px] text-slate-400 font-bold mb-1">R</span>
                    <input
                        type="number" v-model.number="r" min="0" max="255"
                        class="w-12 text-center text-sm font-semibold p-1 bg-slate-50 border border-slate-200 rounded-md focus:ring-2 focus:ring-red-500 outline-none"
                    />
                </div>

                <div class="text-center">
                    <span class="block text-[10px] text-slate-400 font-bold mb-1">G</span>
                    <input
                        type="number" v-model.number="g" min="0" max="255"
                        class="w-12 text-center text-sm font-semibold p-1 bg-slate-50 border border-slate-200 rounded-md focus:ring-2 focus:ring-green-500 outline-none"
                    />
                </div>

                <div class="text-center">
                    <span class="block text-[10px] text-slate-400 font-bold mb-1">B</span>
                    <input
                        type="number" v-model.number="b" min="0" max="255"
                        class="w-12 text-center text-sm font-semibold p-1 bg-slate-50 border border-slate-200 rounded-md focus:ring-2 focus:ring-blue-500 outline-none"
                    />
                </div>
            </div>

            <div class="text-right">
                <span class="block text-[10px] text-slate-400 font-bold mb-1">HEX</span>
                <span class="text-sm font-mono font-bold text-slate-700">{{ hexColor }}</span>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center space-x-4">
            <div class="flex-1">
                <span class="block text-[10px] text-slate-400 font-bold mb-0.5 uppercase">Код цвета</span>
                <span class="text-base font-mono font-black text-slate-700">{{ hexColor }}</span>
            </div>

            <button
                @click="handleSelect"
                class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl transition-all active:scale-95 shadow-lg shadow-slate-200"
            >
                Выбрать
            </button>
        </div>

    </div>
</template>

<style scoped>
/* Убираем стрелочки у инпутов типа number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>
