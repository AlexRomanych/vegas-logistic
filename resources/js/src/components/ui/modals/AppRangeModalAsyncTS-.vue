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


                <div class="p-4 bg-slate-800 rounded-xl border border-slate-700 w-full max-w-md shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-slate-300 font-bold uppercase text-xs tracking-wider">Разбиение задачи</h3>
                        <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-mono">{{                              item.name
                            }}      </span>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1 text-center">
                                <div class="text-[10px] text-slate-500 uppercase mb-1">Останется</div>
                                <div class="text-2xl font-bold text-white">{{ remaining }}</div>
                            </div>

                            <div class="flex-none flex flex-col items-center">
                                <input
                                    v-model.number="splitValue"
                                    :max="item.amount"
                                    :min="0"
                                    class="w-24 text-center bg-slate-900 border border-slate-600 rounded-lg py-2 text-xl font-bold text-blue-400 focus:border-blue-500 focus:outline-none"
                                    type="number"
                                />
                                <div class="text-[10px] text-slate-500 mt-1 uppercase">Отделить</div>
                            </div>

                            <div class="flex-1 text-center">
                                <div class="text-[10px] text-slate-500 uppercase mb-1">Новый элемент</div>
                                <div class="text-2xl font-bold text-blue-500">{{ splitValue }}</div>
                            </div>
                        </div>

                        <div class="relative pt-2">
                            <input
                                v-model.number="splitValue"
                                :max="item.amount"
                                class="w-full h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-500"
                                min="0"
                                step="1"
                                type="range"
                            />
                            <div class="flex justify-between text-[10px] text-slate-500 mt-2 font-mono">
                                <span>0</span>
                                <span>{{ item.amount }}</span>
                            </div>
                        </div>

                        <button
                            :disabled="splitValue <= 0 || splitValue >= item.amount"
                            class="w-full py-3 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-700 disabled:text-slate-500 text-white rounded-lg font-bold transition-all uppercase text-sm tracking-widest"
                            @click="confirmSplit"
                        >
                            Разбить количество
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
import { computed, ref, watch, } from 'vue'
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

// Количество, которое мы отделяем
const splitValue = ref(0)

// Вычисляемое значение того, что останется в оригинале
const remaining = computed(() => props.item.amount - splitValue.value)

const confirmSplit = () => {
    emit('split', {
        originalId: props.item.name,
        keep:       remaining.value,
        take:       splitValue.value
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


/* Убираем стандартные стрелки у Chrome/Safari для красоты, если нужно,
но вы просили "с помощью стрелок", поэтому оставляем их стандартными.
Если нужны кастомные кнопки +/- , их нужно верстать отдельно. */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    opacity: 1; /* Делаем стрелки всегда видимыми */
}

/* Стилизация ползунка для Webkit */
input[type=range]::-webkit-slider-runnable-track {
    @apply bg-slate-700 rounded-full h-2;
}

input[type=range]::-webkit-slider-thumb {
    @apply appearance-none w-5 h-5 bg-blue-500 rounded-full border-2 border-slate-900 -mt-1.5 shadow-lg;
}
</style>
