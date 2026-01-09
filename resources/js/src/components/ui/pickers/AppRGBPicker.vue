<template>
    <div class="w-[400px] p-6 bg-white rounded-3xl shadow-2xl border border-slate-100">
        <div
            :style="{ backgroundColor: rgbColor }"
            class="relative w-full h-24 rounded-2xl mb-6 transition-all duration-500 flex items-center justify-center shadow-inner group"
        >
      <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-sm font-mono font-black shadow-sm">
        {{ hexColor }}
      </span>

            <button
                v-if="isEyeDropperSupported"
                class="absolute right-3 bottom-3 p-2 bg-white rounded-full shadow-md hover:scale-110 active:scale-95 transition-all text-slate-600 hover:text-indigo-600"
                title="Использовать пипетку"
                @click="openEyeDropper"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-3"
                        stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"/>
                </svg>
            </button>
        </div>

        <!-- __ Через Grid -->
        <div class="mb-8 grid grid-cols-8 gap-3 justify-items-center max-w-max mx-auto">
            <button
                v-for="color in colors"
                :key="color"
                :style="{ backgroundColor: color }"
                class="w-6 h-6 rounded-full border border-white shadow-sm hover:scale-125 transition-transform"
                @click="applyHex(color)"
            ></button>
        </div>

        <!-- __ Через Flex -->
        <!--<div class="mb-8 flex justify-center space-x-3">-->
        <!--    <button-->
        <!--        v-for="color in colors"-->
        <!--        :key="color"-->
        <!--        @click="applyHex(color)"-->
        <!--        class="w-6 h-6 rounded-full border border-white shadow-sm hover:scale-125 transition-transform"-->
        <!--        :style="{ backgroundColor: color }"-->
        <!--    ></button>-->
        <!--</div>-->

        <div class="space-y-4 mb-8">
            <div v-for="key in ['r', 'g', 'b']" :key="key">
                <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase mb-1">
                    <span>{{ key }}</span>
                    <span>{{ channels[key as 'r' | 'g' | 'b'] }}</span>
                </div>
                <input
                    v-model.number="channels[key as 'r' | 'g' | 'b']"
                    class="w-full h-1.5 rounded-lg appearance-none cursor-pointer accent-slate-800 bg-slate-100"
                    max="255"
                    min="0"
                    type="range"
                />
            </div>
        </div>

        <button
            class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg transition-all active:scale-95"
            @click="emitsEvents"
        >
            ВЫБРАТЬ ЦВЕТ
        </button>
    </div>
</template>

<script lang="ts" setup>
import { reactive, computed, watch } from 'vue'

interface IProps {
    modelValue?: string
}

const props = withDefaults(defineProps<IProps>(), {
    modelValue: '#FF0000'
})

const emits = defineEmits<{
    (e: 'update:modelValue', value: string): void
    (e: 'select', value: string): void
}>()
// const emits = defineEmits(['update:modelValue', 'select']);

const colors = [
    '#EF4444', '#F59E0B', '#10B981', '#3B82F6',
    '#FF0000', '#0000FF', '#FFFF00', '#00FF00',
    '#800000', '#008000', '#000080', '#808000',
    '#FF00FF', '#800080', '#00FFFF', '#008080',
]

// __ Основной объект с каналами
const channels = reactive({r: 255, g: 0, b: 0})

// __ Проверка поддержки EyeDropper API браузером
const isEyeDropperSupported = 'EyeDropper' in window

// __ Функция активации пипетки
const openEyeDropper = async () => {
    if (!isEyeDropperSupported) {
        console.log('Ваш браузер не поддерживает пипетку')
        return
    }

    const eyeDropper = new (window as any).EyeDropper()

    try {
        const result = await eyeDropper.open()
        // __ Результат приходит в формате HEX (например, #ffffff)
        applyHex(result.sRGBHex)
    } catch (e) {
        // __ Пользователь нажал Esc или закрыл пипетку
        console.log('Выбор цвета отменен')
    }
}

// __ Универсальный парсер HEX в RGB
const applyHex = (hex: string) => {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex)
    if (result) {
        channels.r = parseInt(result[1], 16)
        channels.g = parseInt(result[2], 16)
        channels.b = parseInt(result[3], 16)
    }
}

const toHex = (n: number) => Math.min(255, Math.max(0, n)).toString(16).padStart(2, '0')

const rgbColor = computed(() => `rgb(${channels.r}, ${channels.g}, ${channels.b})`)
const hexColor = computed(() => `#${toHex(channels.r)}${toHex(channels.g)}${toHex(channels.b)}`.toUpperCase())

const emitsEvents = () => {
    const returnColor = `#${toHex(channels.r)}${toHex(channels.g)}${toHex(channels.b)}`.toUpperCase()
    emits('select', returnColor)
    emits('update:modelValue', returnColor)
    // console.log('returnColor: ', returnColor)
}


// Вызываем при загрузке
applyHex(props.modelValue)

// И следим, если вдруг модель изменится извне
watch(() => props.modelValue, (newVal) => {
    applyHex(newVal)
}, {immediate: true})

// async function pickColor() {
//     if ('EyeDropper' in window) {
//         // Используем Type Assertion, чтобы TS понял, что мы проверили наличие
//         const EyeDropperClass = (window as any).EyeDropper;
//         const eyeDropper = new EyeDropperClass();
//
//         try {
//             const result = await eyeDropper.open();
//             console.log(result.sRGBHex); // Получаем HEX-код
//         } catch (e) {
//             console.log("Выбор цвета отменен");
//         }
//     } else {
//         console.warn("Ваш браузер не поддерживает EyeDropper API");
//     }
// }
</script>


