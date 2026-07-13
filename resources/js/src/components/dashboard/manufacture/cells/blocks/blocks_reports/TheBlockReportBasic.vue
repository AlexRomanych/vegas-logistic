<template>
    <template v-if="!isLoading">
        <!-- Внешний контейнер с динамическим фоном и границей -->
        <div :class="isDarkMode ? 'bg-[#161e2d] text-white border-slate-800/60' : 'bg-slate-50 text-slate-900 border-slate-200'"
             class="min-h-full w-full border p-4 font-sans shadow-2xl transition-colors duration-200">

            <!-- Шапка отчета -->
            <div :class="isDarkMode ? 'border-slate-800/50' : 'border-slate-200'"
                 class="border-b pb-2 mb-2 flex justify-between items-center">
                <div>
                    <h2 :class="isDarkMode ? 'text-white' : 'text-slate-900'" class="text-2xl font-black uppercase tracking-wide">
                        Производственный отчет
                    </h2>
                    <p :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-base mt-0.5">Сводные данные по объемам, квадратуре и линиям</p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- КНОПКА ПЕРЕКЛЮЧЕНИЯ ТЕМЫ -->
                    <button
                        :class="isDarkMode ? 'bg-slate-800 text-amber-400 border-slate-700 hover:bg-slate-700' : 'bg-white text-indigo-600 border-slate-300 hover:bg-slate-100'"
                        class="px-3 py-1 rounded-md border text-sm font-bold flex items-center gap-2 shadow-sm transition-all select-none"
                        @click="isDarkMode = !isDarkMode">
                        <span>{{ isDarkMode ? '☀️ Дневная' : '🌙 Ночная' }}</span>
                    </button>

                    <span :class="isDarkMode ? 'bg-indigo-600/20 text-indigo-400 border-indigo-500/30' : 'bg-indigo-50 text-indigo-600 border-indigo-200'"
                          class="px-4 py-1 border rounded text-sm font-bold tracking-wider">
                    Всего дней: {{ renderData.length }}
                </span>
                </div>
            </div>

            <!-- Зона контента со скроллом -->
            <div class="space-y-2 max-h-[72vh] overflow-y-auto pr-1 custom-scrollbar">

                <div v-for="(day, dayIdx) in renderData" :key="dayIdx" class="space-y-1">

                    <!-- ЗАГОЛОВОК ДНЯ ( action_at ) -->
                    <div v-if="day && day.length && day[0].action_at" class="flex items-center gap-2 px-1 pt-1 mb-1">
                        <span :class="isDarkMode ? 'text-indigo-400' : 'text-indigo-600'" class="text-base font-black uppercase tracking-widest">📅 Дата производства:</span>
                        <span :class="isDarkMode ? 'bg-slate-800 border-slate-700/50' : 'bg-slate-200 border-slate-300 text-slate-800'"
                              class="text-base font-mono font-black px-2.5 py-0.5 rounded-md border shadow-inner">
                        {{ formatDateInFullFormat(day[0].action_at) }}
                    </span>
                    </div>

                    <!-- ГРУППА (Уровень 1) -->
                    <div v-for="(group, gIdx) in day" :key="gIdx"
                         :class="isDarkMode ? 'bg-[#111827]/40 border-slate-800/80' : 'bg-white border-slate-200 shadow-sm'"
                         class="border rounded-xl overflow-hidden transition-all shadow-md">

                        <!-- Шапка группы -->
                        <div
                            :class="isDarkMode ? 'bg-[#111827]/80 hover:bg-slate-800/50' : 'bg-slate-100/80 hover:bg-slate-200/60'"
                            class="px-4 py-2 flex items-center justify-between cursor-pointer transition-colors select-none"
                            @click="group.collapsed = !group.collapsed"
                        >
                            <div class="flex items-center gap-4">
                            <span :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'"
                                  :style="{ transform: !group.collapsed ? 'rotate(90deg)' : 'none' }"
                                  class="text-xl transition-transform duration-200 block">▶</span>
                                <div :class="isDarkMode ? 'bg-[#4f46e5]' : 'bg-indigo-600'" class="w-2.5 h-7 rounded-full"></div>
                                <div>
                                    <!--<span :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'" class="text-[11px] font-bold uppercase tracking-widest block leading-none mb-0.5">Группа</span>-->
                                    <h3 :class="isDarkMode ? 'text-white' : 'text-slate-900'" class="text-xl font-black leading-none">Смена №{{
                                            group.groupName
                                        }}</h3>
                                </div>
                            </div>

                            <!-- Сводка по группе -->
                            <div class="flex items-center gap-6 text-base">
                                <div class="text-right">
                                    <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">Кол-во: </span>
                                    <span :class="isDarkMode ? 'text-indigo-400' : 'text-indigo-600'" class="font-black text-lg">{{ group.amount.total }}</span>
                                    <span :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'" class="text-sm font-bold"> / {{ group.amount.done }}</span>
                                </div>
                                <div :class="isDarkMode ? 'border-slate-800' : 'border-slate-300'" class="text-right border-l-2 pl-5">
                                    <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">Площадь: </span>
                                    <span :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'"
                                          class="font-black text-lg">{{ Number(group.square.total).toFixed(2) }} м²</span>
                                </div>
                                <!--<span-->
                                <!--    :class="group.groupType === 'orange'-->
                                <!--        ? (isDarkMode ? 'bg-orange-500/10 text-orange-400 border-orange-500/20' : 'bg-orange-50 text-orange-600 border-orange-200')-->
                                <!--        : (isDarkMode ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20' : 'bg-indigo-50 text-indigo-600 border-indigo-200')"-->
                                <!--    class="px-3 py-0.5 border rounded-md text-xs font-mono uppercase font-black tracking-widest">-->
                                <!--    {{ group.groupType }}-->
                                <!--</span>-->
                            </div>
                        </div>

                        <!-- ПОДГРУППЫ (Уровень 2) -->
                        <div v-if="!group.collapsed"
                             :class="isDarkMode ? 'border-slate-800/60 bg-[#161e2d]/60' : 'border-slate-200 bg-slate-50/50'"
                             class="border-t p-1.5 space-y-1">

                            <div v-for="(sub, sIdx) in group.subgroups" :key="sIdx"
                                 :class="isDarkMode ? 'bg-[#111827]/30 border-slate-800/50' : 'bg-white border-slate-200'"
                                 class="border rounded-lg overflow-hidden">

                                <!-- Шапка подгруппы -->
                                <div
                                    :class="isDarkMode ? 'bg-[#111827]/60 hover:bg-slate-800/40' : 'bg-slate-50 hover:bg-slate-100'"
                                    class="p-2 flex justify-between items-center cursor-pointer text-base transition-colors"
                                    @click="sub.collapsed = !sub.collapsed"
                                >
                                    <div :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'" class="flex items-center gap-3 font-bold text-base">
                                    <span :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'"
                                          :style="{ transform: !sub.collapsed ? 'rotate(90deg)' : 'none' }"
                                          class="text-base transition-transform duration-200 block">▶</span>
                                        {{ sub.subgroupName }}
                                        <span v-if="sub.isTuning"
                                              class="bg-red-500/20 text-red-500 text-xs px-2.5 py-0.2 rounded border border-red-500/20 uppercase font-mono font-black tracking-wider">Наладка</span>
                                    </div>

                                    <!-- Агрегатные данные подгруппы -->
                                    <div :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="flex items-center gap-5 text-sm font-semibold">
                                        <div>
                                            <span>Кол-во: </span>
                                            <strong :class="isDarkMode ? 'text-indigo-400' : 'text-indigo-600'" class="text-base font-black">{{
                                                    sub.amount.total
                                                }}</strong>
                                            <span v-if="sub.amount.done !== undefined" :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'"
                                                  class="text-xs font-bold"> / {{ sub.amount.done }}</span>
                                            <span :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'" class="text-xs font-normal"> шт.</span>
                                        </div>

                                        <div v-if="sub.square && sub.square.total" :class="isDarkMode ? 'border-slate-700/60' : 'border-slate-200'"
                                             class="border-l pl-4">
                                            <span>Площадь: </span>
                                            <strong :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'"
                                                    class="text-base font-black">{{ Number(sub.square.total).toFixed(2) }} м²</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- СТРОКИ / ЛИНИИ (Уровень 3 - Таблица) -->
                                <div v-if="!sub.collapsed"
                                     :class="isDarkMode ? 'bg-[#161e2d]/40' : 'bg-white'"
                                     class="p-1 overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-sm">
                                        <thead>
                                        <tr :class="isDarkMode ? 'text-slate-500 border-slate-800/80' : 'text-slate-400 border-slate-200'"
                                            class="uppercase tracking-widest border-b text-xs font-black">
                                            <th class="py-1 px-3 w-14">Поз.</th>
                                            <th class="py-1 px-3">Код 1С / Наименование блока</th>
                                            <th class="py-1 px-3 text-right">Кол-во</th>
                                            <th class="py-1 px-3 text-right">Площадь (м²)</th>
                                            <th class="py-1 px-3 text-right">Труд-ты</th>
                                        </tr>
                                        </thead>
                                        <tbody :class="isDarkMode ? 'divide-slate-800/40 text-slate-200' : 'divide-slate-100 text-slate-700'"
                                               class="divide-y">
                                        <tr v-for="line in sub.lines" :key="line.id"
                                            :class="isDarkMode ? 'hover:bg-slate-800/40' : 'hover:bg-slate-50'"
                                            class="transition-colors">
                                            <td :class="isDarkMode ? 'text-slate-400' : 'text-slate-400'" class="py-1.5 px-3 font-mono font-bold text-base">
                                                #{{ line.position }}
                                            </td>
                                            <td class="py-1.5 px-3">
                                                <div class="flex items-center gap-3">
                                                <span
                                                    :class="isDarkMode ? 'text-indigo-300 bg-indigo-950/60 border-indigo-900/50' : 'text-indigo-600 bg-indigo-50 border-indigo-100'"
                                                    class="font-mono font-black text-sm px-2 py-0.2 rounded border tracking-wide">
                                                    {{ line.block.code_1c }}
                                                </span>
                                                    <span :class="isDarkMode ? 'text-white' : 'text-slate-900'" class="font-bold text-base">{{
                                                            line.block.name
                                                        }}</span>
                                                </div>
                                            </td>
                                            <td :class="isDarkMode ? 'text-slate-100' : 'text-slate-900'" class="py-1.5 px-3 text-right font-black text-base">
                                                {{ line.amount }}
                                            </td>
                                            <td :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'"
                                                class="py-1.5 px-3 text-right font-mono font-bold text-base">{{ Number(line.square * line.amount).toFixed(3) }}
                                            </td>
                                            <td :class="isDarkMode ? 'text-amber-400' : 'text-amber-600'"
                                                class="py-1.5 px-3 text-right font-mono font-bold text-base">{{ formatTimeWithLeadingZeros(line.time, 'hour') }}
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
import type { IBlockDay, IBlockTaskLinesGroupData } from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { BLOCK_TASK_DRAFT } from '@/app/constants/blocks.ts'
import { groupTaskLinesForExecute } from '@/app/helpers/manufacture/helpers_blocks.ts'
import { formatDateInFullFormat, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'


const blockStore = useBlocksStore()

const isLoading  = ref(false)
// Флаг переключения темы (по умолчанию ночная)
const isDarkMode = ref(true)

// __ Переменные
const blockDays  = ref<IBlockDay[]>([]) // Дни блока
const renderData = ref<IBlockTaskLinesGroupData[][]>([]) // Дни блока
// const reportData = ref(JSON.parse(JSON.stringify(props.data)))

const getDays = async () => {
    const days: IBlockDay[] = await blockStore.getBlockDayByPeriod()
    blockDays.value         = days.filter(day => day.block_tasks.length > 0)

}


const getRenderData = () => {
    renderData.value = blockDays.value
        .map(day => {
            // const newDay = JSON.parse(JSON.stringify(BLOCK_DAY_DRAFT))
            const unionTask = JSON.parse(JSON.stringify(BLOCK_TASK_DRAFT))
            day.block_tasks.forEach(task => {
                task.block_lines.forEach(line => unionTask.block_lines.push(line))
            })
            // day.block_tasks.push(unionTask)
            // __ Возвращаем только суммарную Инфу
            const summary = groupTaskLinesForExecute(unionTask.block_lines)
            if (summary[0]) {
                summary[0].action_at = day.action_at
            }
            if (summary[1]) {
                summary[1].action_at = day.action_at
            }
            return summary
        })

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
        // false,
    )

    isLoading.value = false
})
</script>

<style scoped>

</style>
