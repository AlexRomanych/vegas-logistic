<template>
    <div class="min-h-full w-full bg-[#161e2d] text-white border border-slate-800/60 p-4 font-sans shadow-2xl">
        <!-- Шапка отчета -->
        <div class="border-b border-slate-800/50 pb-2 mb-2 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black uppercase tracking-wide text-white">
                    Производственный отчет по группам
                </h2>
                <p class="text-base text-slate-400 mt-0.5">Сводные данные по объемам, квадратуре и линиям</p>
            </div>
            <div class="flex gap-2">
                <span class="px-4 py-1 bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 rounded text-sm font-bold tracking-wider">
                    Всего дней: {{ renderData.length }}
                </span>
            </div>
        </div>

        <!-- Зона контента со скроллом -->
        <div class="space-y-2 max-h-[72vh] overflow-y-auto pr-1 custom-scrollbar">

            <div v-for="(day, dayIdx) in renderData" :key="dayIdx" class="space-y-1">

                <!-- ЗАГОЛОВОК ДНЯ ( action_at ) -->
                <div v-if="day && day.length && day[0].action_at" class="flex items-center gap-2 px-1 pt-1 mb-1">
                    <span class="text-base font-black text-indigo-400 uppercase tracking-widest">📅 Дата производства:</span>
                    <span class="text-base font-mono font-black bg-slate-800 px-2.5 py-0.5 rounded-md border border-slate-700/50 shadow-inner">
                        {{ day[0].action_at }}
                    </span>
                </div>

                <!-- ГРУППА (Уровень 1) -->
                <div v-for="(group, gIdx) in day" :key="gIdx"
                     class="bg-[#111827]/40 border border-slate-800/80 rounded-xl overflow-hidden transition-all shadow-md">

                    <!-- Шапка группы -->
                    <div
                        class="bg-[#111827]/80 px-4 py-2 flex items-center justify-between cursor-pointer hover:bg-slate-800/50 transition-colors select-none"
                        @click="group.collapsed = !group.collapsed"
                    >
                        <div class="flex items-center gap-4">
                            <span :class="{ 'rotate-90': !group.collapsed }" class="text-sm text-slate-500 transition-transform duration-200">▶</span>
                            <div class="w-2.5 h-7 bg-[#4f46e5] rounded-full"></div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest block leading-none mb-0.5">Группа</span>
                                <h3 class="text-xl font-black text-white leading-none">Смена №{{ group.groupName }}</h3>
                            </div>
                        </div>

                        <!-- Сводка по группе -->
                        <div class="flex items-center gap-6 text-base">
                            <div class="text-right">
                                <span class="text-slate-400">Кол-во: </span>
                                <span class="font-black text-indigo-400 text-lg">{{ group.amount.total }}</span>
                                <span class="text-slate-500 text-sm font-bold"> / {{ group.amount.done }}</span>
                            </div>
                            <div class="text-right border-l-2 border-slate-800 pl-5">
                                <span class="text-slate-400">Площадь: </span>
                                <span class="font-black text-emerald-400 text-lg">{{ Number(group.square.total).toFixed(2) }} м²</span>
                            </div>
                            <span
                                :class="group.groupType === 'orange' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20'"
                                class="px-3 py-0.5 rounded-md text-xs font-mono uppercase font-black tracking-widest">
                                {{ group.groupType }}
                            </span>
                        </div>
                    </div>

                    <!-- ПОДГРУППЫ (Уровень 2) -->
                    <div v-if="!group.collapsed" class="border-t border-slate-800/60 p-1.5 space-y-1 bg-[#161e2d]/60">
                        <div v-for="(sub, sIdx) in group.subgroups" :key="sIdx" class="bg-[#111827]/30 border border-slate-800/50 rounded-lg overflow-hidden">

                            <!-- Шапка подгруппы -->
                            <div
                                class="p-2 bg-[#111827]/60 flex justify-between items-center cursor-pointer hover:bg-slate-800/40 text-base transition-colors"
                                @click="sub.collapsed = !sub.collapsed"
                            >
                                <div class="flex items-center gap-3 font-bold text-slate-300 text-base">
                                    <span :class="{ 'rotate-90': !sub.collapsed }" class="text-xs text-slate-500 transition-transform duration-200">▶</span>
                                    {{ sub.subgroupName }}
                                    <span v-if="sub.isTuning"
                                          class="bg-red-500/20 text-red-400 text-xs px-2.5 py-0.2 rounded border border-red-500/20 uppercase font-mono font-black tracking-wider">Наладка</span>
                                </div>

                                <!-- Агрегатные данные подгруппы -->
                                <div class="flex items-center gap-5 text-sm font-semibold text-slate-400">
                                    <div>
                                        <span>Кол-во: </span>
                                        <strong class="text-indigo-400 text-base font-black">{{ sub.amount.total }}</strong>
                                        <span v-if="sub.amount.done !== undefined" class="text-slate-500 text-xs font-bold"> / {{ sub.amount.done }}</span>
                                        <span class="text-slate-500 text-xs font-normal"> шт.</span>
                                    </div>

                                    <div v-if="sub.square && sub.square.total" class="border-l border-slate-700/60 pl-4">
                                        <span>Площадь: </span>
                                        <strong class="text-emerald-400 text-base font-black">{{ Number(sub.square.total).toFixed(2) }} м²</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- СТРОКИ / ЛИНИИ (Уровень 3 - Таблица) -->
                            <div v-if="!sub.collapsed" class="p-1 overflow-x-auto bg-[#161e2d]/40">
                                <table class="w-full text-left border-collapse text-sm">
                                    <thead>
                                    <tr class="text-slate-500 uppercase tracking-widest border-b border-slate-800/80 text-xs font-black">
                                        <th class="py-1 px-3 w-14">Поз.</th>
                                        <th class="py-1 px-3">Код 1С / Наименование блока</th>
                                        <th class="py-1 px-3 text-right">Кол-во</th>
                                        <th class="py-1 px-3 text-right">Площадь (м²)</th>
                                        <th class="py-1 px-3 text-right">Произв-ть</th>
                                        <th class="py-1 px-3 text-right">Время (ч)</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/40 text-slate-200">
                                    <tr v-for="line in sub.lines" :key="line.id" class="hover:bg-slate-800/40 transition-colors">
                                        <td class="py-1.5 px-3 font-mono text-slate-400 font-bold text-base">#{{ line.position }}</td>
                                        <td class="py-1.5 px-3">
                                            <div class="flex items-center gap-3">
                                                <span class="font-mono text-indigo-300 font-black text-sm bg-indigo-950/60 px-2 py-0.2 rounded border border-indigo-900/50 tracking-wide">
                                                    {{ line.block.code_1c }}
                                                </span>
                                                <span class="text-white font-bold text-base">{{ line.block.name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-1.5 px-3 text-right font-black text-slate-100 text-base">{{ line.amount }}</td>
                                        <td class="py-1.5 px-3 text-right font-mono text-emerald-400 font-bold text-base">{{ Number(line.square).toFixed(3) }}</td>
                                        <td class="py-1.5 px-3 text-right font-mono text-amber-400 font-bold text-base">{{ Number(line.productivity).toFixed(2) }}</td>
                                        <td class="py-1.5 px-3 text-right font-mono text-slate-400 text-base">{{ Number(line.time).toFixed(2) }}</td>
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

<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import type { IBlockDay, IBlockTaskLinesGroupData, IPeriod } from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { BLOCK_DAY_DRAFT, BLOCK_TASK_DRAFT } from '@/app/constants/blocks.ts'
import { groupTaskLinesForExecute } from '@/app/helpers/manufacture/helpers_blocks.ts'


const blockStore = useBlocksStore()

const isLoading = ref(false)

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
