<template>
    <div class="bg-[#0f172a] p-4 border-y border-blue-500/30 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-blue-500/5 rotate-45 translate-x-8 -translate-y-8 border-b border-blue-500/20"></div>

        <div v-if="title" class="mb-4 flex items-center gap-3">
            <div class="h-4 w-1 bg-blue-500 shadow-[0_0_10px_#3b82f6]"></div>
            <h3 class="text-blue-100 text-sm font-black uppercase tracking-widest font-mono">
                {{ title }}
            </h3>
        </div>

        <div class="flex flex-col gap-1">
            <div
                v-for="(item, index) in items"
                :key="index"
                class="flex items-center group py-1 px-2 hover:bg-blue-500/10 transition-colors duration-150"
            >
                <div class="flex-none w-32 md:w-40">
          <span class="text-blue-500/70 text-[10px] font-mono uppercase font-bold tracking-tighter group-hover:text-blue-400">
            {{ item.label }}
          </span>
                </div>

                <div class="flex-none px-2 text-blue-900 font-mono text-[10px]">
                    &gt;
                </div>

                <div class="flex-1 min-w-0">
          <span class="text-blue-100 text-[13px] font-medium font-mono truncate block group-hover:translate-x-1 transition-transform">
            {{ item.value || 'NULL' }}
          </span>
                </div>

                <div class="flex-none opacity-0 group-hover:opacity-100 transition-opacity flex gap-0.5">
                    <div class="w-1 h-3 bg-blue-500/20"></div>
                    <div class="w-2 h-3 bg-blue-500/40"></div>
                    <div class="w-0.5 h-3 bg-blue-500/60"></div>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-2 border-t border-blue-900/50 flex justify-between items-center">
            <span class="text-[8px] text-blue-900 font-mono uppercase tracking-[0.3em]">System.Data.Output</span>
            <div class="flex gap-1">
                <div class="w-2 h-2 bg-blue-500/20 rounded-full animate-pulse"></div>
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

    /* Шрифт Mono придает "компьютерный" вид */
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap');

    .font-mono {
        font-family: 'JetBrains Mono', monospace;
    }
</style>
