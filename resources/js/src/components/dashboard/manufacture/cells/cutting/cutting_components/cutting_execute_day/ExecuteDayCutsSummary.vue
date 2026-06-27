<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal" class="dark-container" @click.self="close">

                <div :class="[width, 'modal-container overflow-hidden']">

                    <!-- Кнопка закрытия (крестик) -->
                    <div class="close-cross-container">
                        <div class="m-1 p-1">
                            <AppInputButton
                                id="close"
                                :type="type"
                                height="w-5"
                                title="x"
                                width="w-[30px]"
                                @buttonClick="close"
                            />
                        </div>
                    </div>

                    <!-- Шапка модалки -->
                    <div class="w-full pl-8 border-b border-slate-800/50 pb-4">
                        <h3 class="text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold mb-1">
                            Общее количество Ткани
                        </h3>
                        <h2 class="text-left text-white font-bold">
                            {{ `Количество Ткани по всем Столам для СЗ: ${orderTitle}`}}
                        </h2>
                    </div>

                    <!-- Контент: Сама таблица со скроллом -->
                    <div :class="[height, 'overflow-y-auto custom-scrollbar w-full bg-slate-900/20']">
                        <table class="w-full text-left border-collapse">
                            <thead>
                            <tr class="border-b border-slate-800/40 bg-slate-950/40 sticky top-0 z-10 backdrop-blur-sm">
                                <th class="pl-8 py-3 text-slate-400 uppercase tracking-wider text-[12px] font-semibold">
                                    Наименование Ткани
                                </th>
                                <th class="pr-8 py-3 text-right text-slate-400 tracking-wider uppercase text-[12px] font-semibold w-[200px]">
                                    Общая длина, м.п.
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/30">
                            <tr
                                v-for="(value, key) in localSums"
                                :key="key"
                                class="hover:bg-slate-800/30 transition-colors group"
                            >
                                <td class="pl-8 py-1 text-slate-300 text-sm font-medium group-hover:text-white transition-colors">
                                    {{ key }}
                                </td>
                                <td class="pr-8 py-1 text-right text-blue-400 font-mono text-sm leading-relaxed">
                                    {{ formatNumber(value) }}
                                </td>
                            </tr>

                            <tr v-if="Object.keys(localSums).length === 0">
                                <td colspan="2" class="text-center py-8 text-slate-500 text-sm italic">
                                    Нет данных для отображения
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Подвал модалки -->
                    <div class="w-full flex justify-between items-center p-4 bg-slate-800/50 rounded-b-xl border-t border-slate-800/30">
                        <!-- Слева выведем счетчик строк -->
                        <span class="pl-4 text-[11px] text-slate-500 font-mono uppercase tracking-wider">
                            Уникальных позиций: {{ Object.keys(localSums).length }}
                        </span>

                        <AppInputButton
                            id="confirm"
                            :type="type"
                            title="Закрыть"
                            @buttonClick="close"
                        />
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script lang="ts" setup>
import { nextTick, ref } from 'vue'
import type { IColorTypes } from '@/types'
import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

interface IProps {
    orderTitle?: string
    type?: IColorTypes
    width?: string
    height?: string
}

withDefaults(defineProps<IProps>(), {
    orderTitle: '',
    type: 'primary',
    width: 'min-w-[650px] max-w-[650px]',
    height: 'max-h-[400px]', // Ограничиваем высоту таблицы, чтобы модалка не уезжала за экран
})

const showModal = ref(false)
const localSums = ref<Record<string, number>>({})

let resolvePromise: ((value: boolean) => void) | null

// Императивный вызов окна. Передаем объект с суммами прямо сюда
const show = async (sumsData: Record<string, number>) => {
    await nextTick()
    localSums.value = sumsData
    showModal.value = true

    return new Promise<boolean>((resolve) => {
        resolvePromise = resolve
    })
}

const close = () => {
    if (resolvePromise) {
        resolvePromise(true)
        showModal.value = false
        resolvePromise = null
    }
}

const formatNumber = (num: number): string => {
    return new Intl.NumberFormat('ru-RU', {
        minimumFractionDigits: 3,
        maximumFractionDigits: 3
    }).format(num)
    // return new Intl.NumberFormat('ru-RU').format(num)
}

// Экспортируем метод наружу
defineExpose({
    show
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-700 rounded-full;
}

.dark-container {
    @apply z-[999] bg-slate-900/80 fixed w-screen h-screen top-0 left-0 flex justify-center items-center backdrop-blur-sm
}

.modal-container {
    @apply bg-slate-800 rounded-xl flex flex-col border-l-8 border-blue-500 shadow-2xl
}

.close-cross-container {
    @apply flex justify-end w-full
}

/* Анимация один-в-один как у тебя */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.5s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(1.10);
}
</style>

<!--<template>-->
<!--    <div :class="[width, 'bg-slate-900 border border-slate-800/80 rounded-xl overflow-hidden shadow-xl flex flex-col']">-->

<!--        &lt;!&ndash; Шапка таблицы &ndash;&gt;-->
<!--        <div class="w-full px-6 py-4 border-b border-slate-800/50 flex justify-between items-center bg-slate-900/50">-->
<!--            <div>-->
<!--                <h3 class="text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold mb-1">-->
<!--                    Сводная аналитика-->
<!--                </h3>-->
<!--                <h2 class="text-left text-white text-base font-bold">-->
<!--                    Итого по подгруппам (все табы)-->
<!--                </h2>-->
<!--            </div>-->

<!--            &lt;!&ndash; Маленький бейдж общего количества уникальных подгрупп &ndash;&gt;-->
<!--            <span class="bg-slate-800 border border-slate-700/50 text-slate-400 text-[11px] font-mono px-2 py-0.5 rounded-md">-->
<!--                Всего: {{ Object.keys(sums).length }}-->
<!--            </span>-->
<!--        </div>-->

<!--        &lt;!&ndash; Контейнер с таблицей и скроллом &ndash;&gt;-->
<!--        <div :class="[maxHeight, 'overflow-y-auto custom-scrollbar w-full']">-->
<!--            <table class="w-full text-left border-collapse">-->
<!--                <thead>-->
<!--                <tr class="border-b border-slate-800/60 bg-slate-950/40 sticky top-0 z-10 backdrop-blur-sm">-->
<!--                    <th class="pl-6 py-3 text-slate-500 uppercase tracking-wider text-[10px] font-semibold">-->
<!--                        Наименование подгруппы-->
<!--                    </th>-->
<!--                    <th class="pr-6 py-3 text-right text-slate-500 uppercase tracking-wider text-[10px] font-semibold w-[150px]">-->
<!--                        Общая длина-->
<!--                    </th>-->
<!--                </tr>-->
<!--                </thead>-->
<!--                <tbody class="divide-y divide-slate-800/30">-->
<!--                &lt;!&ndash; Рендерим, если данные есть &ndash;&gt;-->
<!--                <tr-->
<!--                    v-for="(value, key) in sums"-->
<!--                    :key="key"-->
<!--                    class="hover:bg-slate-800/30 transition-colors group"-->
<!--                >-->
<!--                    &lt;!&ndash; Название подгруппы (Шелк, Хлопок и т.д.) &ndash;&gt;-->
<!--                    <td class="pl-6 py-3 text-slate-300 text-sm font-medium group-hover:text-white transition-colors">-->
<!--                        {{ key }}-->
<!--                    </td>-->

<!--                    &lt;!&ndash; Суммированное значение (Длина/Количество) &ndash;&gt;-->
<!--                    <td class="pr-6 py-3 text-right text-blue-400 font-mono text-sm leading-relaxed">-->
<!--                        {{ formatNumber(value) }}-->
<!--                    </td>-->
<!--                </tr>-->

<!--                &lt;!&ndash; Плейсхолдер на случай, если всё пусто &ndash;&gt;-->
<!--                <tr v-if="Object.keys(sums).length === 0">-->
<!--                    <td colspan="2" class="text-center py-8 text-slate-500 text-sm italic">-->
<!--                        Нет данных для отображения-->
<!--                    </td>-->
<!--                </tr>-->
<!--                </tbody>-->
<!--            </table>-->
<!--        </div>-->

<!--    </div>-->
<!--</template>-->

<!--<script lang="ts" setup>-->
<!--interface IProps {-->
<!--    // Сюда передаем твой computed объект: allTabsSubgroupSums-->
<!--    sums: Record<string, number>-->
<!--    // Опциональные параметры размеров для гибкости-->
<!--    width?: string-->
<!--    maxHeight?: string-->
<!--}-->

<!--withDefaults(defineProps<IProps>(), {-->
<!--    width: 'w-full max-w-[600px]',-->
<!--    maxHeight: 'max-h-[350px]'-->
<!--})-->

<!--// Хелпер для красивого вывода чисел (разбивка тысяч по пробелам: 12 450)-->
<!--const formatNumber = (num: number): string => {-->
<!--    return new Intl.NumberFormat('ru-RU').format(num)-->
<!--}-->
<!--</script>-->

<!--<style scoped>-->
<!--/* Стили скроллбара один-в-один как в твоей модалке */-->
<!--.custom-scrollbar::-webkit-scrollbar {-->
<!--    width: 4px;-->
<!--}-->
<!--.custom-scrollbar::-webkit-scrollbar-thumb {-->
<!--    @apply bg-slate-700 rounded-full;-->
<!--}-->
<!--.custom-scrollbar::-webkit-scrollbar-track {-->
<!--    @apply bg-transparent;-->
<!--}-->
<!--</style>-->
