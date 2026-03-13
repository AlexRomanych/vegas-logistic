<template>
    <div
        class="mr-2 bg-blue-950 rounded-xl border-2 border-blue-600 overflow-hidden shadow-[0_0_20px_rgba(37,99,235,0.2)] flex flex-col h-[calc(100vh-190px)]">

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
                    <span
                        class="text-blue-400 text-[11px] font-black uppercase tracking-tight group-hover:text-blue-300">
                        {{ item.label }}
                    </span>
                </div>

                <div :class="getClass(item)" class="bg-[#111c3a] flex-1 px-4 py-1" @dblclick="handleClick(item)">
                    <span
                        class="select-none text-white text-sm font-bold tracking-wide group-hover:text-cyan-400 transition-colors">
                        {{ item.value || 'НЕТ ДАННЫХ' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="h-1 w-full bg-gradient-to-r from-transparent via-blue-500 to-transparent opacity-50 shrink-0"></div>
    </div>

    <!-- __ Выбор даты -->
    <AppModalAsyncDateTS
        id="select-date"
        ref="appModalAsyncDateTS"
        :date="modalDate"
        :type="modalDateType"
    />

    <!-- __ Выбор текста -->
    <AppModalAsyncTextTS
        ref="appModalAsyncTextTS"
        :text="modalText"
        :title="modalTitle"
    />

</template>

<script setup lang="ts">
import { computed, ref, /*inject, watch*/ } from 'vue'
import type { IRenderOrder, IColorTypes } from '@/types'

// import { useOrder, useId } from './../injectionKeys.ts'

import {
    formatDateAndTimeInShortFormat,
    formatDateIntl,
    formatTimeWithLeadingZeros,
    // formatDateTime,
} from '@/app/helpers/helpers_date'

import AppModalAsyncDateTS from '@/components/ui/modals/AppModalAsyncDateTS.vue'
import AppModalAsyncTextTS from '@/components/ui/modals/AppModalAsyncTextTS.vue'
// import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'


interface IInfoItem {
    label: string;
    value: string | number | null | undefined;
}


interface IProps {
    order: IRenderOrder
    id: number
}

const props = defineProps<IProps>()

// const order = useOrder()
// const id    = useId()


const emits = defineEmits<{
    (e: 'patch-load-at', payload: Date): void,
    (e: 'patch-description', payload: string): void,
}>()


// __ Тип для модального окна Выбора даты
const modalDateType       = ref<IColorTypes>('danger')
const modalDate           = ref<string | null | undefined>('')
const appModalAsyncDateTS = ref<InstanceType<typeof AppModalAsyncDateTS> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна Выбора текста
// const modalTextType       = ref<IColorTypes>('danger')
const modalText           = ref<string | null>('')
const modalTitle          = ref<string | undefined >('')
const appModalAsyncTextTS = ref<InstanceType<typeof AppModalAsyncTextTS> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией


const title      = computed(() => `${props.order.client.short_name} №${props.order.order_no_str}`)
const durationKS = computed(() => {
    if (!props.order.manager_start || !props.order.manager_end) {
        return 0
    }
    const start = props.order.manager_start ? (new Date(props.order.manager_start)).getTime() : (new Date()).getTime()
    const end   = props.order.manager_end ? (new Date(props.order.manager_end)).getTime() : (new Date()).getTime()
    return formatTimeWithLeadingZeros(((end - start) / 1000))
})
const durationKB = computed(() => {
    if (!props.order.design_start || !props.order.design_end) {
        return 0
    }
    const start = props.order.design_start ? (new Date(props.order.design_start)).getTime() : (new Date()).getTime()
    const end   = props.order.design_end ? (new Date(props.order.design_end)).getTime() : (new Date()).getTime()
    return formatTimeWithLeadingZeros(((end - start) / 1000))
})


const LOAD_AT_LABEL     = 'Загрузка на складе'
const DESCRIPTION_LABEL = 'Описание'

const items = computed<IInfoItem[]>(() => [
        { label: 'Клиент', value: props.order.client.short_name },
        { label: 'Номер', value: props.order.order_no_str },
        { label: 'Тип изделий', value: props.order.elements_type_render },
        { label: 'Тип заявки', value: props.order.order_type.display_name },
        { label: 'Количество изделий', value: props.order.amounts.totals.toString() },
        { label: 'Старт КС', value: formatDateAndTimeInShortFormat(props.order.manager_start) },
        { label: 'Финиш КС', value: formatDateAndTimeInShortFormat(props.order.manager_end) },
        { label: 'Длительность КС', value: durationKS.value },
        { label: 'Старт КБ', value: formatDateAndTimeInShortFormat(props.order.design_start) },
        { label: 'Финиш КБ', value: formatDateAndTimeInShortFormat(props.order.design_end) },
        { label: 'Длительность КБ', value: durationKB.value },
        { label: 'Ответственный (1С)', value: props.order.responsible ?? '' },
        { label: DESCRIPTION_LABEL, value: props.order.description ?? '' },
        { label: 'Комментарий (1С)', value: props.order.comment_1c ?? '' },
        { label: 'Готовность', value: props.order.is_forecast ? 'прогнозная' : 'раскрытая' },
        { label: 'Период Заявки', value: formatDateIntl(props.order.order_period, true, false) },
        { label: LOAD_AT_LABEL, value: formatDateIntl(props.order.load_at, true) },
        { label: 'Загрузка на складе (1С)', value: formatDateIntl(props.order.manager_load_date, true) },
        { label: 'Разгрузка у клиента', value: props.order.unload_at ? formatDateIntl(props.order.unload_at, true) : '' },
        {
            label: 'Загрузка в систему',
            value: props.order.created_at ? formatDateAndTimeInShortFormat(props.order.created_at, true) : '',
        },
        { label: 'Код Заявки в 1С', value: props.order.code_1c },
    ],
)

// __ Получаем класс, если элемент редактируемый
const getClass = (item: IInfoItem) => {
    if ([LOAD_AT_LABEL, DESCRIPTION_LABEL].includes(item.label)) {
        return 'cursor-pointer truncate'
    }
    return ''
}

// __ Обрабатываем клик по информационному полю
const handleClick = async (item: IInfoItem) => {
    let answer
    switch (item.label) {

        // __ Дата загрузки
        case LOAD_AT_LABEL:
            modalDate.value = props.order.load_at

            answer = await appModalAsyncDateTS.value!.show()
            if (answer) {
                const newDate = appModalAsyncDateTS.value!.date
                emits('patch-load-at', newDate)
            }
            break

        // __ Описание
        case DESCRIPTION_LABEL:
            modalText.value  = props.order.description || ''
            modalTitle.value = 'Изменение/добавление комментария'

            answer = await appModalAsyncTextTS.value!.show()
            if (answer) {
                const newText = appModalAsyncTextTS.value!.text
                if (newText === modalText.value) {
                    return
                }
                emits('patch-description', newText)
            }
            break
    }
}


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
