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


                <div class="p-5 bg-slate-800 rounded-xl border border-slate-700 w-full max-w-sm shadow-2xl">

                    <div class="mb-6 text-center">
                        <div class="text-slate-500 text-[10px] uppercase tracking-[0.2em] mb-1">{{ dividerTitle }}</div>
                        <div class="text-white font-bold text-lg">{{ item.name }}</div>
                    </div>

                    <div class="space-y-8">

                        <div class="flex items-center justify-center gap-3">

                            <button
                                :disabled="splitValue <= 0"
                                class="w-12 h-12 flex items-center justify-center bg-slate-700 hover:bg-slate-600 disabled:opacity-30 disabled:cursor-not-allowed rounded-xl border border-slate-600 text-white text-2xl transition-all active:scale-95"
                                @click="changeValue(-1)"
                            >
                                −
                            </button>

                            <div class="relative">
                                <input
                                    v-model.number="splitValue"
                                    class="w-28 h-16 text-center bg-slate-900 border-2 border-blue-500/30 rounded-2xl text-3xl font-black text-blue-400 focus:border-blue-500 focus:outline-none transition-colors"
                                    type="number"
                                />
                                <div
                                    class="absolute -bottom-6 left-0 w-full text-center text-[9px] text-slate-500 font-bold uppercase">
                                    {{ units }}
                                </div>
                            </div>

                            <button
                                :disabled="splitValue >= item.amount"
                                class="w-12 h-12 flex items-center justify-center bg-slate-700 hover:bg-slate-600 disabled:opacity-30 disabled:cursor-not-allowed rounded-xl border border-slate-600 text-white text-2xl transition-all active:scale-95"
                                @click="changeValue(1)"
                            >
                                +
                            </button>

                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4">
                            <div class="bg-slate-900/50 p-3 rounded-lg border border-slate-700/50 text-center">
                                <div class="text-[9px] text-slate-500 uppercase mb-1">{{ remainingTitle }}</div>
                                <div class="text-xl font-bold text-slate-300">{{ remaining }}</div>
                            </div>
                            <div class="bg-blue-500/10 p-3 rounded-lg border border-blue-500/20 text-center">
                                <div class="text-[9px] text-blue-400 uppercase mb-1">{{ splitTitle }}</div>
                                <div class="text-xl font-bold text-blue-500">{{ splitValue }}</div>
                            </div>
                        </div>

                        <div class="px-2">
                            <input
                                v-model.number="splitValue"
                                :max="item.amount"
                                class="w-full h-1.5 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-500"
                                min="0"
                                step="1"
                                type="range"
                            />
                        </div>

                        <button
                            :disabled="splitValue <= 0 || splitValue >= item.amount"
                            class="w-full py-4 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-700/50 disabled:text-slate-600 text-white rounded-xl font-black transition-all uppercase text-xs tracking-widest shadow-lg shadow-blue-900/20"
                            @click="confirmSplit"
                        >
                            {{ dividerConfirm }}
                        </button>

                    </div>
                </div>


                <div class="w-full h-full flex justify-end">

                    <div
                        class="m-1 p-1">
                        <AppInputButton
                            id="confirm"
                            :type="type"
                            title="Закрыть"
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

import type { IColorTypes, IDividerItem } from '@/types'

import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'


interface IProps {
    type?: IColorTypes,
    width?: string,
    height?: string,
    text?: string,
    mode?: 'inform' | 'confirm'

    item?: IDividerItem
    units?: string
    dividerTitle?: string
    dividerConfirm?: string
    remainingTitle?: string
    splitTitle?: string

}

const props = withDefaults(defineProps<IProps>(), {
    type:           'primary',
    width:          'min-w-[500px]',
    height:         'min-h-[300px]',
    item:           () => ({
        name:   'Название партии',
        amount: 100,
    }),
    units:          'штуки',
    dividerTitle:   'Изменить количество',
    dividerConfirm: 'Изменить количество',
    remainingTitle: 'Остаток в текущем',
    splitTitle:     'Количество в новом',
})

const emits = defineEmits<{
    (e: 'split', payload: { take: number, keep: number }): void
}>()


const showModal = ref(false)           // реактивность видимости модального окна

let resolvePromise: ((value: boolean) => void) | null
const show = () => {
    showModal.value = true
    splitValue.value = 0        // сбрасываем значение при открытии модального окна
    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: boolean) => {
    if (resolvePromise) {
        // resolvePromise( {value, text: targetText.value})    // возвращает объект, можно так возвращать результаты
        resolvePromise(value)
        showModal.value = false
        resolvePromise  = null

        if (!value) {
            splitValue.value = 0

        }
    }
}

defineExpose({
    show,
    get range() {
        return { take: splitValue.value, keep: remaining.value }
    },
})

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

const splitValue = ref(0)
const remaining  = computed(() => props.item.amount - splitValue.value)

// __ Функция изменения значения (для кнопок)
const changeValue = (step: number) => {
    const newValue = splitValue.value + step
    if (newValue >= 0 && newValue <= props.item.amount) {
        splitValue.value = newValue
    }
}

// __ Следим, чтобы пользователь не ввел вручную число больше допустимого
watch(splitValue, (val) => {
    if (val > props.item.amount) splitValue.value = props.item.amount
    if (val < 0) splitValue.value = 0
})

const confirmSplit = () => {
    emits('split', { take: splitValue.value, keep: remaining.value })
    select(true)
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




/* __ Прячем стандартные стрелки, так как у нас теперь есть большие кнопки */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type=number] {
    -moz-appearance: textfield;
}

/* __ Стилизация ползунка */
input[type=range]::-webkit-slider-thumb {
    @apply appearance-none w-6 h-6 bg-blue-500 rounded-full border-4 border-slate-800 cursor-pointer shadow-md;
}
</style>
