<template>
    <Teleport to="body">

        <div v-if="showModal"
             class="dark-container">

            <div :class="[width, height, borderColor, 'modal-container']">

                <div class="close-cross-container">
                    <div class="m-1 p-1">
                        <AppInputButton
                            id="close"
                            :type="type"
                            height="w-5"
                            title="x"
                            width="w-[30px]"
                            @buttonClick="select(false)"
                        />
                    </div>
                </div>


                <div class="p-6 bg-slate-800 rounded-2xl border border-slate-700 w-full max-w-sm shadow-2xl select-none">

                    <div class="mb-8 text-center">
                        <div class="text-slate-500 text-[10px] uppercase tracking-[0.2em] mb-1 font-bold">Разделение партии</div>
                        <div class="text-white font-bold text-xl truncate px-4">{{ item.name }}</div>
                    </div>

                    <div class="space-y-8">

                        <div class="flex items-center justify-center gap-4">

                            <button
                                @mousedown="startChanging(-1)"
                                @mouseup="stopChanging"
                                @mouseleave="stopChanging"
                                @touchstart.prevent="startChanging(-1)"
                                @touchend="stopChanging"
                                :disabled="splitValue <= 0"
                                class="w-14 h-14 flex items-center justify-center bg-slate-700 hover:bg-slate-600 active:scale-90 disabled:opacity-20 disabled:pointer-events-none rounded-2xl border border-slate-600 text-white text-3xl transition-all shadow-lg"
                            >
                                −
                            </button>

                            <div class="relative group">
                                <input
                                    type="number"
                                    v-model.number="splitValue"
                                    @input="handleManualInput"
                                    class="w-32 h-20 text-center bg-slate-900 border-2 border-blue-500/30 rounded-3xl text-4xl font-black text-blue-400 focus:border-blue-500 focus:outline-none transition-all shadow-inner"
                                />
                                <div class="absolute -bottom-6 left-0 w-full text-center text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                                    Единиц
                                </div>
                            </div>

                            <button
                                @mousedown="startChanging(1)"
                                @mouseup="stopChanging"
                                @mouseleave="stopChanging"
                                @touchstart.prevent="startChanging(1)"
                                @touchend="stopChanging"
                                :disabled="splitValue >= item.amount"
                                class="w-14 h-14 flex items-center justify-center bg-slate-700 hover:bg-slate-600 active:scale-90 disabled:opacity-20 disabled:pointer-events-none rounded-2xl border border-slate-600 text-white text-3xl transition-all shadow-lg"
                            >
                                +
                            </button>

                        </div>

                        <div class="px-2 pt-4">
                            <input
                                type="range"
                                v-model.number="splitValue"
                                min="0"
                                :max="item.amount"
                                step="1"
                                class="w-full h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-500 hover:accent-blue-400 transition-all"
                            />
                            <div class="flex justify-between mt-3 text-[10px] font-mono text-slate-500">
                                <span>0</span>
                                <span class="text-slate-400 font-bold">Всего: {{ item.amount }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-900/80 p-3 rounded-xl border border-slate-700 text-center">
                                <div class="text-[9px] text-slate-500 uppercase mb-1 font-bold">Останется</div>
                                <div class="text-xl font-black text-white">{{ remaining }}</div>
                            </div>
                            <div class="bg-blue-600/10 p-3 rounded-xl border border-blue-500/30 text-center">
                                <div class="text-[9px] text-blue-400 uppercase mb-1 font-bold">Отделится</div>
                                <div class="text-xl font-black text-blue-500">{{ splitValue }}</div>
                            </div>
                        </div>

                        <button
                            @click="confirmSplit"
                            :disabled="splitValue <= 0 || splitValue >= item.amount"
                            class="w-full py-4 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-700/50 disabled:text-slate-600 text-white rounded-2xl font-black transition-all uppercase text-[11px] tracking-[0.2em] shadow-xl shadow-blue-900/20 active:translate-y-0.5"
                        >
                            Подтвердить разделение
                        </button>

                    </div>
                </div>


                <div class="w-full h-full flex justify-end">

                    <div v-if="mode === 'confirm'"
                         class="m-1 p-1">
                        <AppInputButton
                            id="confirm"
                            :type="type"
                            title="Да"
                            @buttonClick="select(true)"
                        />
                    </div>

                    <div
                        class="m-1 p-1">
                        <AppInputButton
                            id="confirm"
                            :title="mode === 'confirm' ? 'Отмена' : 'Закрыть'"
                            :type="type"
                            @buttonClick="select(false)"
                        />
                    </div>

                </div>
            </div>
        </div>

    </Teleport>
</template>

<script lang="ts" setup>
import { computed, onUnmounted, ref, watch, } from 'vue'
import type { IColorTypes } from '@/app/constants/colorsClasses.js'
import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

interface IItem {
    name: string;
    amount: number;
}

interface IProps {
    type?: IColorTypes,
    width?: string,
    height?: string,
    text?: string,
    mode?: 'inform' | 'confirm'

    item: IItem
}

const props = withDefaults(defineProps<IProps>(), {
    type:   'primary',
    width:  'min-w-[500px]',
    height: 'min-h-[300px]',
    text:   'This is a Modal Window',
    mode:   'inform'
})

// const emits = defineEmits<{
//     (e: 'select'): void
// }>()

const showModal = ref(false)           // реактивность видимости модального окна
const showText  = ref(props.text)              // реактивность текста сообщения в модальном окне

let resolvePromise: ((value: boolean) => void) | null
const show = (msg = showText.value) => {
    showModal.value = true
    showText.value  = msg
    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: boolean) => {
    if (resolvePromise) {
        resolvePromise(value)
        showModal.value = false
        resolvePromise  = null
    }
}

defineExpose({
    show,
})

// Следим за отображением текста в модальном окне
watch(() => props.text, (value) => {
    showText.value = value
})

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

const emit = defineEmits(['split'])

const splitValue = ref(0)
const remaining = computed(() => props.item.amount - splitValue.value)

// --- ЛОГИКА "СЕКУНДОМЕРА" ---
let intervalId: any = null
let timeoutId: any = null

const startChanging = (step: number) => {
    // 1. Одиночное изменение при клике
    changeValue(step)

    // 2. Установка задержки перед быстрым перебором (500мс)
    timeoutId = setTimeout(() => {
        // 3. Интервал быстрого изменения (70мс)
        intervalId = setInterval(() => {
            if ((step > 0 && splitValue.value < props.item.amount) || (step < 0 && splitValue.value > 0)) {
                changeValue(step)
            } else {
                stopChanging()
            }
        }, 70)
    }, 500)
}

const stopChanging = () => {
    if (timeoutId) clearTimeout(timeoutId)
    if (intervalId) clearInterval(intervalId)
    timeoutId = null
    intervalId = null
}

const changeValue = (step: number) => {
    const next = splitValue.value + step
    if (next >= 0 && next <= props.item.amount) {
        splitValue.value = next
    }
}

// Защита при ручном вводе
const handleManualInput = () => {
    if (splitValue.value > props.item.amount) splitValue.value = props.item.amount
    if (splitValue.value < 0 || isNaN(splitValue.value)) splitValue.value = 0
}

// Очистка при уничтожении компонента
onUnmounted(() => stopChanging())

const confirmSplit = () => {
    emit('split', {
        taken: splitValue.value,
        remaining: remaining.value
    })
}
</script>

<style scoped>

.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between items-center border-l-8
}

.close-cross-container {
    @apply flex justify-end w-full h-full
}

.text-container {
    @apply flex items-end
}

.text-data {
    @apply border-2 border-slate-800 w-full h-full text-white
}

/*
.close-button-container {
    @apply w-full h-full flex justify-end
}
*/

/* Убираем стандартные стрелки браузера */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number] {
    -moz-appearance: textfield;
}

/* Стилизация ползунка */
input[type=range]::-webkit-slider-runnable-track {
    @apply bg-slate-700 rounded-full h-2;
}
input[type=range]::-webkit-slider-thumb {
    @apply appearance-none w-6 h-6 bg-blue-500 rounded-full border-[4px] border-slate-900 -mt-2 shadow-lg transition-transform active:scale-125;
}
</style>
