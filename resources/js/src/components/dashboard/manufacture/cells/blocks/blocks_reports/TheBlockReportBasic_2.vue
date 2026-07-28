<template>
    <template v-if="!isLoading">
        <!-- Внешний контейнер с динамическим фоном -->
        <div :class="isDarkMode ? 'bg-[#0f172a] text-slate-100 border-slate-800' : 'bg-slate-50 text-slate-900 border-slate-200'"
             class="min-h-full w-full border p-4 sm:p-6 font-sans shadow-2xl transition-colors duration-300">

            <!-- Шапка отчета -->
            <div :class="isDarkMode ? 'border-slate-800/80' : 'border-slate-200'"
                 class="border-b pb-4 mb-4 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-6 rounded-full bg-indigo-500"></div>
                        <h2 :class="isDarkMode ? 'text-white' : 'text-slate-900'" class="text-2xl font-bold tracking-tight">
                            Производственный отчет
                        </h2>
                    </div>
                    <p :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-sm mt-1 pl-4">
                        Сводные данные по объемам, квадратуре и линиям
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <!-- КНОПКА ПЕРЕКЛЮЧЕНИЯ ТЕМЫ -->
                    <button
                        :class="isDarkMode
                            ? 'bg-slate-800/90 text-amber-400 border-slate-700 hover:bg-slate-700/80'
                            : 'bg-white text-indigo-600 border-slate-200 hover:bg-slate-100'"
                        class="px-3.5 py-1.5 rounded-lg border text-xs font-semibold flex items-center gap-2 shadow-sm hover:shadow transition-all select-none active:scale-95"
                        @click="isDarkMode = !isDarkMode">
                        <svg v-if="isDarkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"/>
                        </svg>
                        <span>{{ isDarkMode ? 'Дневная' : 'Ночная' }}</span>
                    </button>

                    <!-- Счетчик дней -->
                    <div :class="isDarkMode ? 'bg-indigo-950/50 text-indigo-300 border-indigo-800/60' : 'bg-indigo-50 text-indigo-700 border-indigo-200'"
                         class="px-3.5 py-1.5 border rounded-lg text-xs font-semibold tracking-wide flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        Всего дней: <span class="font-bold font-mono">{{ renderData.length }}</span>
                    </div>
                </div>
            </div>

            <!-- Зона контента со скроллом -->
            <div class="space-y-4 max-h-[75vh] overflow-y-auto pr-1 custom-scrollbar">

                <div v-for="(day, dayIdx) in renderData" :key="dayIdx" class="space-y-2">

                    <!-- ЗАГОЛОВОК ДНЯ ( action_at ) -->
                    <div v-if="day && day.changes.length && day.action_at" class="flex items-center gap-2.5 px-1 pt-2">
                        <div :class="isDarkMode ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20' : 'bg-indigo-50 text-indigo-600 border-indigo-200'"
                             class="p-1.5 rounded-md border">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"/>
                            </svg>
                        </div>
                        <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-600'" class="text-xs font-semibold uppercase tracking-wider">Дата производства:</span>
                        <span :class="isDarkMode ? 'bg-slate-800/90 border-slate-700 text-slate-200' : 'bg-white border-slate-300 text-slate-800'"
                              class="text-sm font-mono font-bold px-3 py-0.5 rounded-md border shadow-sm">
                            {{ formatDateInFullFormat(day.action_at) }}
                        </span>
                    </div>

                    <!-- ГРУППА (Уровень 1 - Смена) -->
                    <div v-for="(group, gIdx) in day" :key="gIdx"
                         :class="isDarkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-white border-slate-200/90 shadow-sm'"
                         class="border rounded-xl overflow-hidden transition-all duration-200 hover:border-slate-700">

                        <!-- Шапка группы -->
                        <div
                            :class="isDarkMode ? 'bg-slate-800/40 hover:bg-slate-800/70' : 'bg-slate-100/60 hover:bg-slate-100'"
                            class="px-4 py-3 flex flex-wrap items-center justify-between cursor-pointer transition-colors select-none gap-3"
                            @click="group.collapsed = !group.collapsed"
                        >
                            <div class="flex items-center gap-3">
                                <div :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'"
                                     :style="{ transform: !group.collapsed ? 'rotate(90deg)' : 'none' }"
                                     class="transition-transform duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
                                    </svg>
                                </div>
                                <div :class="isDarkMode ? 'bg-indigo-500' : 'bg-indigo-600'" class="w-1.5 h-6 rounded-full"></div>
                                <div>
                                    <h3 :class="isDarkMode ? 'text-white' : 'text-slate-900'" class="text-base font-bold leading-tight">
                                        Смена №{{ group.groupName }}
                                    </h3>
                                </div>
                            </div>

                            <!-- Сводка по группе -->
                            <div class="flex items-center gap-3 text-xs sm:text-sm">
                                <div :class="isDarkMode ? 'bg-slate-800/80 border-slate-700/60' : 'bg-white border-slate-200'"
                                     class="px-3 py-1.5 rounded-lg border flex items-center gap-2">
                                    <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-xs uppercase font-medium">Кол-во:</span>
                                    <span :class="isDarkMode ? 'text-indigo-400' : 'text-indigo-600'"
                                          class="font-bold font-mono text-base tabular-nums">{{ group.amount.total }}</span>
                                    <span :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'" class="text-xs">/ {{ group.amount.done }}</span>
                                </div>

                                <div :class="isDarkMode ? 'bg-slate-800/80 border-slate-700/60' : 'bg-white border-slate-200'"
                                     class="px-3 py-1.5 rounded-lg border flex items-center gap-2">
                                    <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-xs uppercase font-medium">Площадь:</span>
                                    <span :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'" class="font-bold font-mono text-base tabular-nums">
                                        {{ Number(group.square.total).toFixed(2) }} <span class="text-xs font-normal">м²</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- ПОДГРУППЫ (Уровень 2) -->
                        <div v-if="!group.collapsed"
                             :class="isDarkMode ? 'border-slate-800 bg-slate-950/40' : 'border-slate-100 bg-slate-50/70'"
                             class="border-t p-2 space-y-2">

                            <div v-for="(sub, sIdx) in group.subgroups" :key="sIdx"
                                 :class="isDarkMode ? 'bg-slate-900/60 border-slate-800/80' : 'bg-white border-slate-200/80'"
                                 class="border rounded-lg overflow-hidden shadow-2xs">

                                <!-- Шапка подгруппы -->
                                <div
                                    :class="isDarkMode ? 'hover:bg-slate-800/40' : 'hover:bg-slate-50/80'"
                                    class="p-2.5 flex flex-wrap justify-between items-center cursor-pointer text-sm transition-colors gap-2"
                                    @click="sub.collapsed = !sub.collapsed"
                                >
                                    <div :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'" class="flex items-center gap-2.5 font-semibold">
                                        <div :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'"
                                             :style="{ transform: !sub.collapsed ? 'rotate(90deg)' : 'none' }"
                                             class="transition-transform duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
                                            </svg>
                                        </div>
                                        <span>{{ sub.subgroupName }}</span>
                                        <span v-if="sub.isTuning"
                                              class="bg-red-500/10 text-red-400 text-[11px] px-2 py-0.5 rounded border border-red-500/20 font-mono font-bold uppercase tracking-wider">
                                            Наладка
                                        </span>
                                    </div>

                                    <!-- Агрегатные данные подгруппы -->
                                    <div :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="flex items-center gap-4 text-xs">
                                        <div class="flex items-center gap-1.5">
                                            <span>Кол-во:</span>
                                            <strong :class="isDarkMode ? 'text-indigo-400' : 'text-indigo-600'"
                                                    class="text-sm font-bold font-mono tabular-nums">
                                                {{ sub.amount.total }}
                                            </strong>
                                            <span v-if="sub.amount.done !== undefined" :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'">
                                                / {{ sub.amount.done }}
                                            </span>
                                            <span class="text-[11px]">шт.</span>
                                        </div>

                                        <div v-if="sub.square && sub.square.total"
                                             :class="isDarkMode ? 'border-slate-800' : 'border-slate-200'"
                                             class="border-l pl-4 flex items-center gap-1.5">
                                            <span>Площадь:</span>
                                            <strong :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'"
                                                    class="text-sm font-bold font-mono tabular-nums">
                                                {{ Number(sub.square.total).toFixed(2) }}
                                            </strong>
                                            <span class="text-[11px]">м²</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- СТРОКИ / ЛИНИИ (Уровень 3 - Таблица) -->
                                <div v-if="!sub.collapsed"
                                     :class="isDarkMode ? 'bg-slate-950/60 border-slate-800/50' : 'bg-slate-50/40 border-slate-100'"
                                     class="p-2 overflow-x-auto border-t">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                        <tr :class="isDarkMode ? 'text-slate-400 border-slate-800' : 'text-slate-500 border-slate-200'"
                                            class="border-b uppercase font-bold tracking-wider text-[11px]">
                                            <th class="py-2 px-3 w-12 text-center">Поз.</th>
                                            <th class="py-2 px-3">Код 1С / Наименование блока</th>
                                            <th class="py-2 px-3 text-right">Кол-во</th>
                                            <th class="py-2 px-3 text-right">Площадь (м²)</th>
                                            <th class="py-2 px-3 text-right">Труд-ты</th>
                                        </tr>
                                        </thead>
                                        <tbody :class="isDarkMode ? 'divide-slate-800/50 text-slate-300' : 'divide-slate-200/60 text-slate-700'"
                                               class="divide-y">
                                        <tr v-for="line in sub.lines" :key="line.id"
                                            :class="isDarkMode ? 'hover:bg-slate-800/40' : 'hover:bg-indigo-50/30'"
                                            class="transition-colors duration-150">
                                            <td :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'" class="py-2 px-3 font-mono text-center font-medium">
                                                #{{ line.position }}
                                            </td>
                                            <td class="py-2 px-3">
                                                <div class="flex items-center gap-2.5">
                                                    <span
                                                        :class="isDarkMode ? 'text-indigo-300 bg-indigo-950/80 border-indigo-800/50' : 'text-indigo-700 bg-indigo-50 border-indigo-200'"
                                                        class="font-mono font-semibold text-[11px] px-2 py-0.5 rounded border tracking-tight">
                                                        {{ line.block.code_1c }}
                                                    </span>
                                                    <span :class="isDarkMode ? 'text-slate-100' : 'text-slate-900'" class="font-medium">
                                                        {{ line.block.name }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td :class="isDarkMode ? 'text-slate-100' : 'text-slate-900'"
                                                class="py-2 px-3 text-right font-bold font-mono tabular-nums">
                                                {{ line.amount }}
                                            </td>
                                            <td :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'"
                                                class="py-2 px-3 text-right font-mono font-semibold tabular-nums">
                                                {{ Number(line.square * line.amount).toFixed(3) }}
                                            </td>
                                            <td :class="isDarkMode ? 'text-amber-400' : 'text-amber-600'"
                                                class="py-2 px-3 text-right font-mono font-semibold tabular-nums">
                                                {{ formatTimeWithLeadingZeros(line.time, 'hour') }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import type { IBlock, IBlockDay, IBlockTask, IBlockTaskLinesGroupData } from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { BLOCK_TASK_DRAFT, CHANGE_1 } from '@/app/constants/blocks.ts'
import { groupTaskLinesForExecute } from '@/app/helpers/manufacture/helpers_blocks.ts'
import { formatDateInFullFormat, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

interface IRenderDay {
    action_at: string
    changes: IBlockTaskLinesGroupData[][]
}

const blockStore = useBlocksStore()

const isLoading  = ref(false)
// Флаг переключения темы (по умолчанию ночная)
const isDarkMode = ref(true)

// __ Переменные
const blockDays  = ref<IBlockDay[]>([]) // Дни блока
const renderData = ref<IRenderDay[]>([]) // Дни блока

const getDays = async () => {
    const days: IBlockDay[] = await blockStore.getBlockDayByPeriod()
    blockDays.value         = days.filter(day => day.block_tasks.length > 0)
}

const getRenderData = () => {

    // __ Собираем Уникальные Даты
    const uniqueDates = new Set<string>()
    blockDays.value.forEach(day => uniqueDates.add(day.action_at))

    Array
        .from(uniqueDates)
        .sort((a, b) => new Date(a).getTime() - new Date(b).getTime())
        .forEach(date => {
            const targetDay: IRenderDay = {
                action_at: date,
                changes: [] as IBlockTaskLinesGroupData[][]
            }

            const filteredDays = blockDays.value.filter(day => day.action_at === date)

            filteredDays.forEach(day => {
                const unionTask = JSON.parse(JSON.stringify(BLOCK_TASK_DRAFT))
                day.block_tasks.forEach(task => {
                    task.block_lines.forEach(line => unionTask.block_lines.push(line))
                })
                const summary = groupTaskLinesForExecute(unionTask.block_lines)

                const targetChange = day.change === CHANGE_1 ? 0 : 1

                targetDay.changes[targetChange] = summary
            })

            renderData.value.push(targetDay)
        })


    console.log('renderData: ', renderData.value)

    //     renderData.value = blockDays.value
    //         .map(day => {
    //             const unionTask = JSON.parse(JSON.stringify(BLOCK_TASK_DRAFT))
    //             day.block_tasks.forEach(task => {
    //                 task.block_lines.forEach(line => unionTask.block_lines.push(line))
    //             })
    //             const summary = groupTaskLinesForExecute(unionTask.block_lines)
    //             if (summary[0]) {
    //                 summary[0].action_at = day.action_at
    //             }
    //             if (summary[1]) {
    //                 summary[1].action_at = day.action_at
    //             }
    //             return summary
    //         })
}

onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getDays()
            getRenderData()

            console.log('blockDays: ', blockDays.value)
            console.log('renderData: ', renderData.value)
        },
        undefined
    )

    isLoading.value = false
})
</script>

<style scoped>
/* Кастомная стилизация скроллбара */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.25);
    border-radius: 9999px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(148, 163, 184, 0.45);
}
</style>
