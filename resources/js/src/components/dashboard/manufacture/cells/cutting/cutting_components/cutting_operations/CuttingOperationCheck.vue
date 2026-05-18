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

                    <div class="p-6 flex flex-col overflow-y-auto custom-scrollbar flex-grow">
                        <div class="border border-slate-700/50 rounded-xl overflow-hidden bg-[#161e2d]">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-slate-900/50 border-b border-slate-700/50">
                                <tr>
                                    <th v-for="header in headers" :key="header"
                                        class="px-4 py-3 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
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
                            :type="type"
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
import type { IColorTypes, ISewingOperationSchema } from '@/types'
import { getColorClassByType } from '@/app/helpers/helpers.js'
import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

interface IProps {
    type?: IColorTypes,
    width?: string,
    height?: string,
    subtitle?: string,
    headers: string[], // Заголовки таблицы
    tableData: string[][],  // Массив объектов с данными
    schema: ISewingOperationSchema | null
    // title?: string,
}

const props = withDefaults(defineProps<IProps>(), {
    type    : 'primary', // По умолчанию синий
    width   : 'min-w-[800px] max-w-[800px]',
    height  : 'max-h-[80vh]', // Ограничиваем высоту по экрану
    subtitle: 'Детальная информация по трудозатратам',
    // title:    'Просмотр данных',
})

const title = computed(() => props.schema ? props.schema.name : '')

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
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-indigo-900 rounded-full;
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
