<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal" class="dark-container" @click.self="select(false)">

                <div :class="[width, height, borderColor, 'modal-container flex flex-col overflow-hidden']">

                    <div class="flex-none border-b border-slate-800/50 pb-4">
                        <div class="flex justify-end p-1">
                            <AppInputButton id="close" :type="type" height="h-8" title="x" width="w-[30px]" @buttonClick="select(false)"/>
                        </div>
                        <div class="pl-8">
                            <h3 class="text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold mb-1 italic">Процедура расчета</h3>
                            <h2 class="text-left text-white text-lg font-bold">{{ title }}</h2>
                        </div>
                    </div>

                    <div class="flex-grow flex flex-row p-6 gap-4 min-h-0 overflow-hidden">
                        <div class="flex-1 flex flex-col min-w-0">
                            <label class="text-slate-500 text-[11px] uppercase font-semibold tracking-tight mb-2">
                                {{ label }}
                            </label>
                            <textarea
                                v-model="text1C"
                                class="w-full flex-grow bg-[#161e2d] text-blue-200 text-[13px] font-mono leading-relaxed
                                   border border-slate-800/50 rounded-xl px-4 py-4
                                   focus:ring-1 focus:ring-blue-500 outline-none transition-all
                                   custom-scrollbar resize-none whitespace-pre-wrap"
                                placeholder="Процедура расчета материалов из 1С"
                                readonly
                            ></textarea>
                        </div>

                        <div v-if="isAdmin" class="flex-1 flex flex-col min-w-0">
                            <label class="text-slate-500 text-[11px] uppercase font-semibold tracking-tight mb-2">
                                Текст процедуры VBA
                            </label>
                            <textarea
                                v-model="textVBA"
                                class="w-full flex-grow bg-[#111827] text-red-200 text-[13px] font-mono leading-relaxed
                                   border border-slate-800/50 rounded-xl px-4 py-4
                                   focus:ring-1 focus:ring-red-500 outline-none transition-all
                                   custom-scrollbar resize-none whitespace-pre-wrap"
                                placeholder="Процедура расчета материалов для VBA"
                            ></textarea>
                        </div>


                    </div>

                    <div class="flex-none flex justify-end gap-2 p-4 bg-slate-800/50 rounded-b-xl border-t border-slate-800/50">
                        <AppInputButton id="close-bottom" title="Понятно" type="primary" @buttonClick="select(true)"/>
                    </div>

                </div>
            </div>

        </Transition>
    </Teleport>
</template>

<script lang="ts" setup>
import { computed, nextTick, ref } from 'vue'
import type { IColorTypes, IModelProcedure } from '@/types'

import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

interface IProps {
    type?: IColorTypes,
    width?: string,
    height?: string,
    procedure: IModelProcedure | null,
    isAdmin?: boolean,
    label?: string,
}

const props = withDefaults(defineProps<IProps>(), {
    type   : 'primary',
    width  : 'min-w-[90vw] max-w-[90vw]',
    height : 'min-h-[90vh] max-h-[90vh]',
    label  : 'Текст процедуры из 1С',
    isAdmin: false,
})

// __ Заголовок
const title = computed(() => props.procedure ? `${props.procedure.code_1c} ${props.procedure.name} (${props.procedure.object_name})` : '')

// __ Текст процедуры
const text1C  = computed(() => props.procedure ? props.procedure.text : '')
const textVBA = computed(() => props.procedure ? props.procedure.text_vba : '')
// const text1C = ref(props.procedure? props.procedure.text : '')

const showModal = ref(false)

// const formData = reactive({
//     falseReason: '',
// })

let resolvePromise: ((value: boolean) => void) | null

const show = async () => {

    await nextTick()

    showModal.value = true

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

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

defineExpose({
    show,
    // get falseReason() {
    //     return formData.falseReason
    // },
})
</script>

<style scoped>
/* Копируем твои стили скроллбара и анимаций */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-700 rounded-full;
}

.dark-container {
    @apply z-[999] bg-slate-500 fixed w-screen h-screen top-0 left-0 flex justify-center items-center backdrop-blur-sm
    /*@apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center*/
}

.modal-container {
    @apply bg-slate-800 rounded-xl flex flex-col border-l-8 shadow-2xl
}

.close-cross-container {
    @apply flex justify-end w-full
}


/* Состояние появления и исчезновения */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.5s ease;
}

/* Стартовое состояние при появлении / Финальное при исчезновении */
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(1.10); /* Легкое увеличение для эффекта приближения */
}

/*.modal-enter-active, .modal-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from, .modal-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
}*/
</style>
