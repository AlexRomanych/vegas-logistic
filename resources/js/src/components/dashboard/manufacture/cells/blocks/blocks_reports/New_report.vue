<template>
    <div :class="[isDarkMode ? 'bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-900', 'min-h-screen p-4 transition-colors duration-200']">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Переключатель темы (для удобства отладки) -->
            <div class="flex justify-end mb-4">
                <button
                    @click="isDarkMode = !isDarkMode"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors cursor-pointer"
                    :class="isDarkMode ? 'bg-slate-900 border-slate-700 text-slate-300 hover:bg-slate-800' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-100'"
                >
                    {{ isDarkMode ? '🌙 Тёмная тема' : '☀️ Светлая тема' }}
                </button>
            </div>

            <!-- Список дней -->
            <div v-for="day in renderData" :key="day.action_at" class="rounded-xl border overflow-hidden transition-all"
                 :class="isDarkMode ? 'bg-slate-900/60 border-slate-800' : 'bg-white border-slate-200 shadow-xs'">

                <!-- Заголовок ДНЯ + Итоговые показатели ДНЯ -->
                <div class="p-4 border-b flex flex-wrap items-center justify-between gap-4"
                     :class="isDarkMode ? 'border-slate-800/80 bg-slate-900/80' : 'border-slate-100 bg-slate-50/50'">

                    <div class="flex items-center gap-3">
                        <button @click="day.collapsed = !day.collapsed" class="p-1 rounded-md hover:bg-slate-500/10 transition-colors">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                {{ day.collapsed ? '▶' : '▼' }}
                            </span>
                        </button>
                        <h2 class="text-base font-bold tracking-wide">
                            {{ formatDateInFullFormat(day.action_at) }}
                        </h2>
                    </div>

                    <!-- ИТОГО по ДНЮ -->
                    <div class="flex flex-wrap items-center gap-2 text-xs font-medium">
                        <span class="px-2.5 py-1 rounded-md border"
                              :class="isDarkMode ? 'bg-slate-800/80 border-slate-700 text-slate-300' : 'bg-white border-slate-200 text-slate-700'">
                            📦 Кол-во: <strong class="text-emerald-500">{{ day.totals.amount.done }}</strong> / {{ day.totals.amount.total }} шт.
                        </span>
                        <span class="px-2.5 py-1 rounded-md border"
                              :class="isDarkMode ? 'bg-slate-800/80 border-slate-700 text-slate-300' : 'bg-white border-slate-200 text-slate-700'">
                            📐 Площадь: <strong>{{ day.totals.square.total.toFixed(2) }}</strong> м²
                        </span>
                        <!-- АГРЕГАТОР: Трудозатраты Дня -->
                        <span class="px-2.5 py-1 rounded-md border"
                              :class="isDarkMode ? 'bg-amber-950/50 border-amber-800/60 text-amber-300' : 'bg-amber-50 border-amber-200 text-amber-800'">
                            ⏱️ Трудозатраты: <strong>{{ day.totals.labor_cost.total.toFixed(1) }}</strong> н·ч
                        </span>
                    </div>
                </div>

                <!-- СМЕНЫ внутри Дня -->
                <div v-show="!day.collapsed" class="p-4 space-y-4 custom-scrollbar overflow-x-auto">
                    <div v-for="shift in day.changes" :key="shift.changeIndex"
                         class="rounded-lg border p-4 transition-all"
                         :class="[getShiftBorderClass(shift.changeIndex), isDarkMode ? 'bg-slate-900/40' : 'bg-slate-50/30']">

                        <!-- Заголовок СМЕНЫ + Итоговые показатели СМЕНЫ -->
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full" :class="getShiftDotClass(shift.changeIndex)"></span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border" :class="getShiftBadgeClass(shift.changeIndex)">
                                    Смена {{ shift.changeIndex + 1 }}
                                </span>
                            </div>

                            <!-- ИТОГО по СМЕНЕ -->
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="text-slate-400">Штуки: <strong class="text-slate-200">{{ shift.totals.amount.done }}/{{ shift.totals.amount.total }}</strong></span>
                                <span class="text-slate-400">•</span>
                                <span class="text-slate-400">Площадь: <strong class="text-slate-200">{{ shift.totals.square.total.toFixed(2) }} м²</strong></span>
                                <span class="text-slate-400">•</span>
                                <!-- АГРЕГАТОР: Трудозатраты Смены -->
                                <span class="font-semibold" :class="shift.changeIndex === 0 ? 'text-indigo-400' : 'text-orange-400'">
                                    ⏱️ {{ shift.totals.labor_cost.total.toFixed(1) }} н·ч
                                </span>
                            </div>
                        </div>

                        <!-- ГРУППЫ ЛИНИЙ / ЛИНИИ внутри Смены -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div v-for="(group, gIdx) in shift.groups" :key="group.groupName || gIdx"
                                 class="relative overflow-hidden rounded-lg border p-3 transition-all flex flex-col justify-between"
                                 :class="getLineCardBorderClass(group, gIdx)">

                                <!-- Цветной индикатор линии -->
                                <div class="absolute top-0 left-0 bottom-0 w-1" :class="getLineIndicatorClass(group, gIdx)"></div>

                                <div>
                                    <!-- Имя Линии / Группы блоков -->
                                    <div class="flex items-center justify-between gap-2 mb-2 pl-2">
                                        <span class="text-xs font-bold uppercase tracking-wide">
                                            Группа / Линия {{ group.groupName }}
                                        </span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded border font-mono" :class="get1CBadgeClass(group, gIdx)">
                                            1С Sync
                                        </span>
                                    </div>

                                    <!-- Метрики Группы / Линии -->
                                    <div class="pl-2 space-y-1.5 text-xs">
                                        <div class="flex justify-between items-center">
                                            <span class="text-slate-400">Объем:</span>
                                            <span class="font-semibold" :class="getLineQuantityClass(group, gIdx)">
                                                {{ group.amount?.done || 0 }} / {{ group.amount?.total || 0 }} шт.
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-slate-400">Площадь:</span>
                                            <span class="font-medium">{{ Number(group.square?.total || 0).toFixed(2) }} м²</span>
                                        </div>

                                        <!-- АГРЕГАТОР: Трудозатраты на уровне отдельной Линии / Группы -->
                                        <div class="flex justify-between items-center pt-1 border-t border-slate-700/30">
                                            <span class="text-slate-400 flex items-center gap-1">
                                                ⏱️ Трудозатраты:
                                            </span>
                                            <span class="font-bold text-amber-400">
                                                {{ getGroupLaborCost(group).toFixed(1) }} н·ч
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'
import type { IBlockDay, IBlockTaskLinesGroupData } from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { CHANGE_1 } from '@/app/constants/blocks.ts'
import { groupTaskLinesForExecute } from '@/app/helpers/manufacture/helpers_blocks.ts'
import { formatDateInFullFormat } from '@/app/helpers/helpers_date'

interface ITotalsSummary {
    amount: {
        total: number
        done: number
    }
    square: {
        total: number
    }
    labor_cost: {
        total: number
    }
}

interface IShiftData {
    changeIndex: number
    collapsed?: boolean
    groups: IBlockTaskLinesGroupData[]
    totals: ITotalsSummary
}

interface IRenderDay {
    action_at: string
    collapsed?: boolean
    changes: IShiftData[]
    totals: ITotalsSummary
}

const blockStore = useBlocksStore()

const isLoading  = ref(false)
const isDarkMode = ref(true)
const blockDays  = ref<IBlockDay[]>([])

// __ Вспомогательная функция безопасного извлечения трудозатрат из группы
const getGroupLaborCost = (group: IBlockTaskLinesGroupData): number => {
    if (typeof group.labor_cost === 'object' && group.labor_cost !== null) {
        return Number(group.labor_cost.total || 0)
    }
    return Number(group.labor_cost || group.labor_hours || group.standard_hours || 0)
}

// __ Helper для подсчета сумм из массива групп (Линий / Блоков)
const calculateGroupsTotals = (groups: IBlockTaskLinesGroupData[]): ITotalsSummary => {
    return groups.reduce(
        (acc, g) => {
            acc.amount.total += g.amount?.total || 0
            acc.amount.done += g.amount?.done || 0
            acc.square.total += Number(g.square?.total || 0)
            acc.labor_cost.total += getGroupLaborCost(g)
            return acc
        },
        {
            amount: { total: 0, done: 0 },
            square: { total: 0 },
            labor_cost: { total: 0 }
        }
    )
}

// __ Computed реактивные данные для рендеринга (агрегация День -> Смена -> Группа линий)
const renderData = computed<IRenderDay[]>(() => {
    if (!blockDays.value.length) return []

    // Группировка дней по дате O(n)
    const daysByDateMap = new Map<string, IBlockDay[]>()
    blockDays.value.forEach(day => {
        const group = daysByDateMap.get(day.action_at) || []
        group.push(day)
        daysByDateMap.set(day.action_at, group)
    })

    const sortedDates = Array.from(daysByDateMap.keys()).sort(
        (a, b) => new Date(a).getTime() - new Date(b).getTime()
    )

    return sortedDates.map(date => {
        const filteredDays = daysByDateMap.get(date) || []
        const shiftsMap: Record<number, IBlockTaskLinesGroupData[]> = {}

        filteredDays.forEach(day => {
            const allLines = day.block_tasks.flatMap(task => task.block_lines || [])
            const summary  = groupTaskLinesForExecute(allLines)

            const targetChange = day.change === CHANGE_1 ? 0 : 1
            shiftsMap[targetChange] = summary
        })

        const dayTotals: ITotalsSummary = {
            amount: { total: 0, done: 0 },
            square: { total: 0 },
            labor_cost: { total: 0 }
        }

        const changes: IShiftData[] = []

        ;[0, 1].forEach(changeIdx => {
            const groups = shiftsMap[changeIdx]
            if (groups && groups.length) {
                const shiftTotals = calculateGroupsTotals(groups)

                // Агрегация в ДЕНЬ из СМЕН
                dayTotals.amount.total += shiftTotals.amount.total
                dayTotals.amount.done  += shiftTotals.amount.done
                dayTotals.square.total += shiftTotals.square.total
                dayTotals.labor_cost.total += shiftTotals.labor_cost.total

                changes.push({
                    changeIndex: changeIdx,
                    collapsed: false,
                    groups,
                    totals: shiftTotals
                })
            }
        })

        return {
            action_at: date,
            collapsed: false,
            changes,
            totals: dayTotals
        }
    })
})

// __ Helpers для динамических стилей СМЕН (Indigo / Orange)
const getShiftBorderClass = (changeIndex: number) => {
    const isShiftZero = changeIndex === 0
    if (isDarkMode.value) {
        return isShiftZero ? 'border-indigo-500/30' : 'border-orange-500/30'
    }
    return isShiftZero ? 'border-indigo-300' : 'border-orange-300'
}

const getShiftBadgeClass = (changeIndex: number) => {
    const isShiftZero = changeIndex === 0
    if (isDarkMode.value) {
        return isShiftZero
            ? 'bg-indigo-950/80 text-indigo-300 border-indigo-800/60'
            : 'bg-orange-950/80 text-orange-300 border-orange-800/60'
    }
    return isShiftZero
        ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
        : 'bg-orange-50 text-orange-700 border-orange-200'
}

const getShiftDotClass = (changeIndex: number) => {
    return changeIndex === 0 ? 'bg-indigo-500' : 'bg-orange-500'
}

// __ Helpers для динамических стилей ЛИНИЙ (Indigo / Orange)
const isLineIndigo = (group: IBlockTaskLinesGroupData, gIdx: number): boolean => {
    const name = String(group.groupName).trim()
    if (name === '1') return true
    if (name === '2') return false
    return gIdx === 0
}

const getLineIndicatorClass = (group: IBlockTaskLinesGroupData, gIdx: number) => {
    const indigo = isLineIndigo(group, gIdx)
    if (isDarkMode.value) {
        return indigo ? 'bg-indigo-500' : 'bg-orange-500'
    }
    return indigo ? 'bg-indigo-600' : 'bg-orange-600'
}

const getLineCardBorderClass = (group: IBlockTaskLinesGroupData, gIdx: number) => {
    const indigo = isLineIndigo(group, gIdx)
    if (isDarkMode.value) {
        return indigo
            ? 'bg-slate-900/90 border-slate-800 hover:border-indigo-500/40'
            : 'bg-slate-900/90 border-slate-800 hover:border-orange-500/40'
    }
    return indigo
        ? 'bg-white border-slate-200/90 hover:border-indigo-300 shadow-xs'
        : 'bg-white border-slate-200/90 hover:border-orange-300 shadow-xs'
}

const getLineQuantityClass = (group: IBlockTaskLinesGroupData, gIdx: number) => {
    const indigo = isLineIndigo(group, gIdx)
    if (isDarkMode.value) {
        return indigo ? 'text-indigo-400' : 'text-orange-400'
    }
    return indigo ? 'text-indigo-600' : 'text-orange-600'
}

const get1CBadgeClass = (group: IBlockTaskLinesGroupData, gIdx: number) => {
    const indigo = isLineIndigo(group, gIdx)
    if (isDarkMode.value) {
        return indigo
            ? 'text-indigo-300 bg-indigo-950/80 border-indigo-800/50'
            : 'text-orange-300 bg-orange-950/80 border-orange-800/50'
    }
    return indigo
        ? 'text-indigo-700 bg-indigo-50 border-indigo-200'
        : 'text-orange-700 bg-orange-50 border-orange-200'
}

const getDays = async () => {
    const days: IBlockDay[] = await blockStore.getBlockDayByPeriod()
    blockDays.value         = days.filter(day => day.block_tasks?.length > 0)
}

onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getDays()
        },
        undefined
    )

    isLoading.value = false
})
</script>

<style scoped>
/* Cross-browser custom scrollbar */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(148, 163, 184, 0.25) transparent;
}

.custom-scrollbar:hover {
    scrollbar-color: rgba(148, 163, 184, 0.45) transparent;
}

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
