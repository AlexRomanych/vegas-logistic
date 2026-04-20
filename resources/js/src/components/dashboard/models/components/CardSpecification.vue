<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal" class="dark-container" @click.self="select(false)">

                <div :class="[width, height, borderColor, 'modal-container']">

                    <div class="close-cross-container">
                        <div class="m-1 p-1">
                            <AppInputButton
                                id="close"
                                :type="type"
                                height="h-[35px]"
                                title="x"
                                width="w-[30px]"
                                @buttonClick="select(false)"
                            />
                        </div>
                    </div>

                    <div class="w-full pl-8 border-b border-slate-700/50 pb-4">
                        <h3 class="text-left text-indigo-400 uppercase tracking-widest text-[10px] font-bold mb-1">
                            {{ subtitle }}
                        </h3>
                        <h2 class="text-left text-white text-lg font-bold">
                            {{ title }}
                        </h2>
                    </div>

                    <div class="p-6 flex flex-col overflow-hidden flex-grow">
                        <div class="border border-slate-700/50 rounded-xl overflow-y-auto custom-scrollbar bg-[#161e2d] max-h-full">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-slate-900 sticky top-0 z-10 shadow-sm">
                                <tr class="border-b border-slate-700/50">
                                    <th v-for="header in headers" :key="header"
                                        class="px-4 py-3 text-[10px] uppercase tracking-wider text-indigo-400 font-bold bg-slate-900">
                                        {{ header }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/50 font-mono text-[12px]">
                                <tr v-for="(row, idx) in tableData" :key="idx" class="hover:bg-slate-700/20 transition-colors">
                                    <td v-for="(value, key) in row" :key="key" class="px-4 py-2 text-indigo-100">
                                        {{ value }}
                                    </td>
                                </tr>
                                <tr v-if="tableData.length === 0">
                                    <td :colspan="headers.length" class="px-4 py-8 text-center text-slate-500 italic">
                                        Данные отсутствуют
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="w-full flex justify-end gap-2 p-4 bg-slate-900/50 rounded-b-xl border-t border-slate-700/50">
                        <AppInputButton
                            id="close-bottom"
                            title="Понятно"
                            type="primary"
                            @buttonClick="select(true)"
                        />
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script lang="ts" setup>
import { computed, nextTick, ref } from 'vue'

import type { IColorTypes, IModelConstruct } from '@/types'

import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

interface IProps {
    type?: IColorTypes,
    width?: string,
    height?: string,
    subtitle?: string,
    construct: IModelConstruct | null
    // title?: string,
    // headers?: string[], // Заголовки таблицы
    // tableData: any[],  // Массив объектов с данными
}

const props = withDefaults(defineProps<IProps>(), {
    type  : 'primary', // По умолчанию синий
    width : 'min-w-[60vw] max-w-[90vw]',
    // width : 'min-w-[1000px] max-w-[1000px]',
    subtitle: 'Спецификация',
    height: 'max-h-[90vh]', // Ограничиваем высоту по экрану
    // title:    'Просмотр данных',
})

// __ Заголовки таблицы с составом спецификации
const headers = computed(() => {
    return ['Код', 'Название', 'H', 'Деталь', 'Процедура', 'Кол-во']
})

// __ Название спецификации
const title = computed(() => props.construct ? `${props.construct.code_1c} ${props.construct.name}` : 'Нет данных')

// __ Данные спецификации
const tableData = computed(() => {
    if (!props.construct) {
        return []
    }

    const result: string[][] = []
    const sorted = props.construct.items.toSorted((a, b) => a.position - b.position)
    sorted.forEach(item => {
        result.push([
            item.material.code_1c,
            item.material.name,
            item.detail_height ? item.detail_height.toFixed(3) : '',
            item.detail ?? '',
            item.procedure?.name ?? '',
            item.amount.toFixed(3),
        ])
    })

    return result
})

// console.log('tableData.value: ', tableData.value)
// console.log('props.construct: ', props.construct)


const showModal = ref(false)
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

defineExpose({ show })
</script>

<style scoped>

thead th {
    background-color: #0f172a; /* Темный фон под цвет шапки */
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px; /* Чуть увеличим для удобства */
}

.custom-scrollbar::-webkit-scrollbar-track {
    @apply bg-slate-900/50;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-indigo-600/50 rounded-full hover:bg-indigo-500;
}

.dark-container {
    @apply z-[999] bg-slate-950/80 fixed w-screen h-screen top-0 left-0 flex justify-center items-center backdrop-blur-md
}

.modal-container {
    @apply bg-[#0b111a] rounded-xl flex flex-col border-l-8 shadow-2xl overflow-hidden
}

.close-cross-container {
    @apply flex justify-end w-full
}

.modal-enter-active, .modal-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from, .modal-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
}
</style>
