<template>
    <div class="m-2 bg-blue-950 p-6 rounded-2xl border border-blue-800/40 backdrop-blur-xl shadow-xl shadow-blue-900/20">

        <div v-if="title" class="mb-6 flex flex-col gap-1 border-l-2 border-cyan-400 pl-4">
      <span
          class="text-blue-400 uppercase tracking-[0.25em] text-[9px] font-black drop-shadow-[0_0_8px_rgba(34,211,238,0.4)]">
        {{ subtitle || 'Сведения' }}
      </span>
            <h3 class="text-blue-50 text-xl font-bold tracking-tight italic">
                {{ title }}
            </h3>
        </div>

        <div class="flex flex-wrap -mx-2 -my-2">
            <div
                v-for="(item, index) in items"
                :key="index"
                class="flex-auto min-w-[180px] p-2"
            >
                <div
                    class="group h-full px-5 py-3 rounded-xl bg-blue-900/20 border border-blue-800/30 hover:border-cyan-500/50 hover:bg-blue-900/40 transition-all duration-300">

                    <div
                        class="text-blue-400/70 text-[10px] uppercase font-bold tracking-widest mb-1 group-hover:text-cyan-300 transition-colors">
                        {{ item.label }}
                    </div>

                    <div
                        class="text-blue-50 text-base font-semibold group-hover:text-white transition-colors flex items-center gap-2">
                        <span
                            class="w-1.5 h-1.5 rounded-full bg-cyan-500 shadow-[0_0_5px_#22d3ee] opacity-0 group-hover:opacity-100 transition-opacity"></span>
                        {{ item.value || '—' }}
                    </div>

                </div>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { inject } from 'vue'
import { useOrder, useId } from './../injectionKeys.ts'


const order = useOrder()
const id    = useId()

console.log('order: ', order?.value)
console.log('id: ', id.value)


interface IInfoItem {
    label: string;
    value: string | number | null | undefined;
}

//
// interface IProps {
//     title?: string;
//     subtitle?: string;
//     items: IInfoItem[];
// }
//
// defineProps<IProps>();


const title    = 'Титульник'
const subtitle = 'СабТитульник'

const items: IInfoItem[] = [
    { label: 'Surname', value: 'Иванов' },
    { label: 'Name', value: 'Иван' },
    { label: 'Отчество', value: 'Иванович' },
]


</script>

<style scoped>
    /* Плавное появление для контента */
    .flex-auto {
        animation: fadeIn 0.4s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    /* Эффект появления "стекла" */
    .flex-auto {
        animation: slideIn 0.5s ease-out forwards;
        opacity: 0;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Каскадная задержка для элементов (опционально) */
    .flex-auto:nth-child(1) {
        animation-delay: 0.1s;
    }

    .flex-auto:nth-child(2) {
        animation-delay: 0.2s;
    }

    .flex-auto:nth-child(3) {
        animation-delay: 0.3s;
    }


</style>
