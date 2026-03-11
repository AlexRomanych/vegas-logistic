<template>
    <div
        class="mr-1 bg-blue-950 rounded-xl border-2 border-blue-600 overflow-hidden shadow-[0_0_20px_rgba(37,99,235,0.2)] flex flex-col h-[calc(100vh-190px)]">

        <div v-if="title" class="px-5 py-3 bg-blue-600 flex items-center justify-between shrink-0">
            <h3 class="text-white text-base font-black uppercase tracking-tighter">
                {{ title }}
            </h3>
            <div class="h-2 w-2 rounded-full bg-white animate-pulse"></div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar p-2 gap-1 flex flex-col">
            <div
                v-for="(item, index) in items"
                :key="index"
                class="flex items-center bg-blue-950/30 border border-blue-900 rounded-lg group hover:border-blue-400 transition-all shrink-0"
            >
                <div class="w-48 flex-none bg-blue-900/50 px-3 py-1 border-r border-blue-800">
                    <span class="text-blue-400 text-[11px] font-black uppercase tracking-tight group-hover:text-blue-300">
                        {{ item.label }}
                    </span>
                </div>

                <div class="bg-[#111c3a] flex-1 px-4 py-1">
                    <span class="text-white text-sm font-bold tracking-wide group-hover:text-cyan-400 transition-colors">
                        {{ item.value || 'НЕТ ДАННЫХ' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="h-1 w-full bg-gradient-to-r from-transparent via-blue-500 to-transparent opacity-50 shrink-0"></div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useOrder, useId } from './../injectionKeys.ts'
import { formatDateAndTimeInShortFormat, formatDateIntl, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'


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


const title      = computed(() => `${order.value?.client.short_name} №${order.value?.order_no_str}`)
const durationKS = computed(() => {
    const start = order.value?.manager_start ? (new Date(order.value.manager_start)).getTime() : (new Date()).getTime()
    const end   = order.value?.manager_end ? (new Date(order.value.manager_end)).getTime() : (new Date()).getTime()
    return formatTimeWithLeadingZeros(((end - start) / 1000))
})
const durationKB = computed(() => {
    const start = order.value?.design_start ? (new Date(order.value.design_start)).getTime() : (new Date()).getTime()
    const end   = order.value?.design_end ? (new Date(order.value.design_end)).getTime() : (new Date()).getTime()
    return formatTimeWithLeadingZeros(((end - start) / 1000))
})

const items: IInfoItem[] = [
    { label: 'Клиент', value: order.value?.client.short_name },
    { label: 'Номер', value: order.value?.order_no_str },
    { label: 'Тип изделий', value: order.value?.elements_type_render },
    { label: 'Тип заявки', value: order.value?.order_type.display_name },
    { label: 'Количество изделий', value: order.value?.amounts.totals.toString() },
    { label: 'Старт КС', value: formatDateAndTimeInShortFormat(order.value?.manager_start) },
    { label: 'Финиш КС', value: formatDateAndTimeInShortFormat(order.value?.manager_end) },
    { label: 'Длительность КС', value: durationKS.value },
    { label: 'Старт КБ', value: formatDateAndTimeInShortFormat(order.value?.design_start) },
    { label: 'Финиш КБ', value: formatDateAndTimeInShortFormat(order.value?.design_end) },
    { label: 'Длительность КБ', value: durationKB.value },
    { label: 'Ответственный (1c)', value: order.value?.responsible ?? '' },
    { label: 'Комментарий (1c)', value: order.value?.comment_1c ?? '' },
    { label: 'Готовность', value: order.value?.is_forecast ? 'прогнозная' : 'раскрытая'},
    { label: 'Период Заявки', value: formatDateIntl(order.value?.order_period, true, false)},
    { label: 'Загрузка на складе', value: formatDateIntl(order.value?.load_at, true)},
    { label: 'Загрузка на складе (1с)', value: formatDateIntl(order.value?.manager_load_date, true)},
    { label: 'Разгрузка у клиена', value: order.value?.unload_at ? formatDateIntl(order.value?.unload_at, true) : ''},
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

    /* Анимация прокрутки */
/*    .marquee-content {
        display: flex;
        flex-direction: column;
        animation: scroll v-bind(scrollDuration) linear infinite;
    }

    !* Остановка при наведении курсора *!
    .pause-animation {
        animation-play-state: paused;
    }

    @keyframes scroll {
        from {
            transform: translateY(0);
        }
        to {
            transform: translateY(-50%); !* Смещаем ровно на половину высоты (на весь первый список) *!
        }
    }*/


    .marquee-content {
        /* Анимация привязывается к общей высоте этого блока */
        animation: scroll-vertical 30s linear infinite;
        display: flex;
        flex-direction: column;
    }

    .pause-animation:hover {
        animation-play-state: paused;
    }

    @keyframes scroll-vertical {
        0% {
            transform: translateY(0);
        }
        100% {
            /* Сдвигаем ровно на высоту одного полного блока (т.е. на 50% от общей высоты двух списков) */
            transform: translateY(-50%);
        }
    }

    /* Стилизация скроллбара для соответствия дизайну */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(23, 37, 84, 0.5); /* Темно-синий фон трека */
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #2563eb; /* Цвет blue-600 под границы */
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #60a5fa; /* Цвет blue-400 при наведении */
    }
</style>
