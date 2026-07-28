<template>
    <template v-if="!isLoading">
        <!-- __ Внешний контейнер с динамическим фоном -->
        <div :class="isDarkMode ? 'bg-[#0f172a] text-slate-100 border-slate-800' : 'bg-slate-50 text-slate-900 border-slate-200'"
             class="min-h-full w-full border p-4 sm:p-6 font-sans shadow-2xl transition-colors duration-300">

            <!-- __ Шапка отчета -->
            <div :class="isDarkMode ? 'border-slate-800/80' : 'border-slate-200'"
                 class="border-b pb-3 mb-3 flex flex-wrap justify-between items-center gap-4">

                <!-- ЛЕВАЯ ЧАСТЬ: Текстовый блок и счетчик дней -->
                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-6 rounded-full bg-indigo-500"></div>
                            <h2 :class="isDarkMode ? 'text-white' : 'text-slate-900'" class="text-2xl font-bold tracking-tight">
                                Производственный отчет
                            </h2>
                            <!-- __ Счетчик дней -->
                            <div :class="isDarkMode ? 'bg-indigo-950/50 text-indigo-300 border-indigo-800/60' : 'bg-indigo-50 text-indigo-700 border-indigo-200'"
                                 class="ml-5 px-3.5 py-1.5 border rounded-lg text-xs font-semibold tracking-wide flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                Всего дней: <span class="font-bold font-mono">{{ renderData.length }}</span>
                            </div>
                        </div>
                        <p :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-sm mt-1 pl-4">
                            Сводные данные по Количеству, Площади, Линиям и Трудозатратам
                        </p>
                    </div>
                </div>

                <!-- ПРАВАЯ ЧАСТЬ: Элементы управления (Переключатель темы + Выбор дат) -->
                <div class="flex items-center gap-3">
                    <!-- __ КНОПКА ПЕРЕКЛЮЧЕНИЯ ТЕМЫ -->
                    <button
                        :class="isDarkMode
                ? 'bg-slate-800/90 text-amber-400 border-slate-700 hover:bg-slate-700/80'
                : 'bg-white text-indigo-600 border-slate-200 hover:bg-slate-100'"
                        class="px-3.5 py-1.5 rounded-lg border text-xs font-semibold flex items-center gap-2 shadow-sm hover:shadow transition-all select-none active:scale-95"
                        @click="isDarkMode = !isDarkMode">
                        <svg v-if="isDarkMode" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <span>{{ isDarkMode ? 'Дневная' : 'Ночная' }}</span>
                    </button>

                    <!-- __ Выбор дат -->
                    <CellDatesSelectMiniTS
                        :period="renderPeriod"
                        @apply="loadData"
                    />
                </div>
            </div>

            <!-- __ Зона контента со скроллом -->
            <div class="space-y-3.5 max-h-[75vh] overflow-y-auto pr-1 custom-scrollbar">

                <!-- __ ДНИ -->
                <div v-for="(dayItem, dayIdx) in renderData" :key="dayIdx" class="space-y-2">

                    <!-- __ ЗАГОЛОВОК ДНЯ -->
                    <div class="flex flex-wrap items-center justify-between gap-2 px-1 pt-1 cursor-pointer select-none group"
                         @click="dayItem.collapsed = !dayItem.collapsed">

                        <div class="flex items-center gap-2.5">
                            <!-- __ Стрелка разворачивания дня -->
                            <div :class="isDarkMode ? 'text-slate-400 group-hover:text-slate-200' : 'text-slate-500 group-hover:text-slate-800'"
                                 :style="{ transform: !dayItem.collapsed ? 'rotate(90deg)' : 'none' }"
                                 class="transition-transform duration-200 p-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>

                            <div :class="isDarkMode ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20' : 'bg-indigo-50 text-indigo-600 border-indigo-200'"
                                 class="p-1.5 rounded-md border">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-600'" class="text-xs font-semibold uppercase tracking-wider">Дата:</span>
                            <span :class="isDarkMode ? 'bg-slate-800/90 border-slate-700 text-slate-200' : 'bg-white border-slate-300 text-slate-800'"
                                  class="text-sm font-mono font-bold px-3 py-0.5 rounded-md border shadow-xs">
                                {{ formatDateInFullFormat(dayItem.action_at) }}
                            </span>
                        </div>

                        <!-- __ АГРЕГАТОРЫ ДНЯ -->
                        <div class="flex items-center gap-2 ml-auto">
                            <!-- Кол-во -->
                            <div :class="isDarkMode ? 'bg-slate-800/90 border-slate-700/80' : 'bg-white border-slate-300'"
                                 class="w-36 sm:w-40 px-2.5 py-1 rounded-lg border flex items-center justify-between text-xs shadow-xs shrink-0">
                                <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] uppercase font-medium whitespace-nowrap">Кол-во:</span>
                                <div class="font-mono font-bold text-sm tabular-nums whitespace-nowrap">
                                    <span :class="isDarkMode ? 'text-indigo-400' : 'text-indigo-600'">{{ dayItem.totals.amount.total }}</span>
                                    <span :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'" class="text-xs font-normal"> / {{ dayItem.totals.amount.done }}</span>
                                </div>
                            </div>

                            <!-- __ Площадь -->
                            <div :class="isDarkMode ? 'bg-slate-800/90 border-slate-700/80' : 'bg-white border-slate-300'"
                                 class="w-36 sm:w-40 px-2.5 py-1 rounded-lg border flex items-center justify-between text-xs shadow-xs shrink-0">
                                <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] uppercase font-medium whitespace-nowrap">Площадь:</span>
                                <span :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'" class="font-bold font-mono text-sm tabular-nums whitespace-nowrap">
                                    {{ Number(dayItem.totals.square.total).toFixed(2) }} <span class="text-xs font-normal">м²</span>
                                </span>
                            </div>

                            <!-- __ АГРЕГАТОР 1: Трудозатраты ДНЯ -->
                            <div :class="isDarkMode ? 'bg-slate-800/90 border-slate-700/80' : 'bg-white border-slate-300'"
                                 class="w-44 sm:w-52 px-2.5 py-1 rounded-lg border flex items-center justify-between text-xs shadow-xs shrink-0">
                                <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] uppercase font-medium whitespace-nowrap">Труд-ты:</span>
                                <span :class="isDarkMode ? 'text-amber-400' : 'text-amber-600'" class="font-bold font-mono text-sm tabular-nums whitespace-nowrap">
                                    {{ formatTimeWithLeadingZeros(dayItem.totals.labor_cost?.total || 0, 'hour') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- __ СМЕНЫ В РАМКАХ ДНЯ -->
                    <template v-if="!dayItem.collapsed">
                        <template v-for="shift in dayItem.changes" :key="shift.changeIndex">
                            <div v-if="shift.groups && shift.groups.length"
                                 :class="getShiftBorderClass(shift.changeIndex)"
                                 class="space-y-2 pl-3 sm:pl-4 border-l-2 ml-2 transition-all">

                                <!-- __ Заголовок смены + ИТОГИ СМЕНЫ -->
                                <div class="flex flex-wrap items-center justify-between gap-2 cursor-pointer select-none group"
                                     @click="shift.collapsed = !shift.collapsed">

                                    <div class="flex items-center gap-2">
                                        <div :class="getShiftChevronClass(shift.changeIndex)"
                                             :style="{ transform: !shift.collapsed ? 'rotate(90deg)' : 'none' }"
                                             class="transition-transform duration-200 p-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>

                                        <span :class="getShiftBadgeClass(shift.changeIndex)"
                                              class="px-2.5 py-0.5 rounded-md border text-xs font-bold uppercase font-mono tracking-wider flex items-center gap-1.5 shadow-2xs">
                                            <span :class="getShiftDotClass(shift.changeIndex)" class="w-1.5 h-1.5 rounded-full"></span>
                                            Смена №{{ shift.changeIndex + 1 }}
                                        </span>
                                    </div>

                                    <!-- __ АГРЕГАТОРЫ СМЕНЫ -->
                                    <div class="flex items-center gap-2 text-xs ml-auto">
                                        <!-- __ Кол-во -->
                                        <div :class="isDarkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-white border-slate-200'"
                                             class="w-36 sm:w-40 px-2.5 py-1 rounded-lg border flex items-center justify-between text-xs shadow-2xs shrink-0">
                                            <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] uppercase font-medium whitespace-nowrap">Кол-во:</span>
                                            <div class="font-mono font-bold text-sm tabular-nums whitespace-nowrap">
                                                <span :class="shift.changeIndex === 0 ? (isDarkMode ? 'text-indigo-400' : 'text-indigo-600') : (isDarkMode ? 'text-orange-400' : 'text-orange-600')">
                                                    {{ shift.totals.amount.total }}
                                                </span>
                                                <span :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'" class="text-xs font-normal"> / {{ shift.totals.amount.done }}</span>
                                            </div>
                                        </div>

                                        <!-- __ Площадь -->
                                        <div :class="isDarkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-white border-slate-200'"
                                             class="w-36 sm:w-40 px-2.5 py-1 rounded-lg border flex items-center justify-between text-xs shadow-2xs shrink-0">
                                            <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] uppercase font-medium whitespace-nowrap">Площадь:</span>
                                            <span :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'" class="font-bold font-mono text-sm tabular-nums whitespace-nowrap">
                                                {{ Number(shift.totals.square.total).toFixed(2) }} <span class="text-xs font-normal">м²</span>
                                            </span>
                                        </div>

                                        <!-- __ АГРЕГАТОР 2: Трудозатраты СМЕНЫ -->
                                        <div :class="isDarkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-white border-slate-200'"
                                             class="w-44 sm:w-52 px-2.5 py-1 rounded-lg border flex items-center justify-between text-xs shadow-2xs shrink-0">
                                            <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] uppercase font-medium whitespace-nowrap">Труд-ты:</span>
                                            <span :class="isDarkMode ? 'text-amber-400' : 'text-amber-600'" class="font-bold font-mono text-sm tabular-nums whitespace-nowrap">
                                                {{ formatTimeWithLeadingZeros(shift.totals.labor_cost?.total || 0, 'hour') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- __ ЛИНИИ ПРОИЗВОДСТВА -->
                                <template v-if="!shift.collapsed">
                                    <div v-for="(group, gIdx) in shift.groups" :key="gIdx"
                                         :class="getLineCardBorderClass(group, gIdx)"
                                         class="border rounded-xl overflow-hidden transition-all duration-200">

                                        <!-- __ Шапка Линии производства -->
                                        <div
                                            :class="isDarkMode ? 'bg-slate-800/40 hover:bg-slate-800/70' : 'bg-slate-100/60 hover:bg-slate-100'"
                                            class="px-3.5 py-2 flex flex-wrap items-center justify-between cursor-pointer transition-colors select-none gap-2.5"
                                            @click="group.collapsed = !group.collapsed"
                                        >
                                            <div class="flex items-center gap-2.5">
                                                <div :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'"
                                                     :style="{ transform: !group.collapsed ? 'rotate(90deg)' : 'none' }"
                                                     class="transition-transform duration-200">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </div>

                                                <div :class="getLineIndicatorClass(group, gIdx)" class="w-1.5 h-6 rounded-full"></div>
                                                <div>
                                                    <h3 :class="isDarkMode ? 'text-white' : 'text-slate-900'" class="text-sm font-bold leading-tight">
                                                        {{ `Линия ${group.groupName}` }}
                                                    </h3>
                                                </div>
                                            </div>

                                            <!-- __ Сводка по линии (Агрегаторы с выравниванием) -->
                                            <div class="flex items-center gap-2 ml-auto text-xs">
                                                <!-- __ Наладка -->
                                                <div v-if="group.tuningTimeTotal"
                                                     :class="isDarkMode ? 'bg-amber-950/40 text-amber-400 border-amber-800/50' : 'bg-amber-50 text-amber-700 border-amber-200'"
                                                     class="w-28 sm:w-32 px-2.5 py-1 rounded-lg border flex items-center justify-between shadow-2xs shrink-0">
                                                    <span :class="isDarkMode ? 'text-amber-500' : 'text-amber-600'" class="text-[11px] uppercase font-medium whitespace-nowrap">Наладка:</span>
                                                    <span class="font-bold font-mono whitespace-nowrap">{{ formatTimeWithLeadingZeros(group.tuningTimeTotal, 'hour') }}</span>
                                                </div>

                                                <!-- __ Кол-во -->
                                                <div :class="isDarkMode ? 'bg-slate-800/80 border-slate-700/60' : 'bg-white border-slate-200'"
                                                     class="w-36 sm:w-40 px-2.5 py-1 rounded-lg border flex items-center justify-between shadow-2xs shrink-0">
                                                    <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] uppercase font-medium whitespace-nowrap">Кол-во:</span>
                                                    <div class="font-mono font-bold text-sm tabular-nums whitespace-nowrap">
                                                        <span :class="getLineQuantityClass(group, gIdx)">{{ group.amount.total }}</span>
                                                        <span :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'" class="text-xs font-normal"> / {{ group.amount.done }}</span>
                                                    </div>
                                                </div>

                                                <!-- __ Площадь -->
                                                <div :class="isDarkMode ? 'bg-slate-800/80 border-slate-700/60' : 'bg-white border-slate-200'"
                                                     class="w-36 sm:w-40 px-2.5 py-1 rounded-lg border flex items-center justify-between shadow-2xs shrink-0">
                                                    <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] uppercase font-medium whitespace-nowrap">Площадь:</span>
                                                    <span :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'" class="font-bold font-mono text-sm tabular-nums whitespace-nowrap">
                                                        {{ Number(group.square.total).toFixed(2) }} <span class="text-xs font-normal">м²</span>
                                                    </span>
                                                </div>

                                                <!-- __ АГРЕГАТОР 3: Трудозатраты ЛИНИИ -->
                                                <div :class="isDarkMode ? 'bg-slate-800/80 border-slate-700/60' : 'bg-white border-slate-200'"
                                                     class="w-44 sm:w-52 px-2.5 py-1 rounded-lg border flex items-center justify-between shadow-2xs shrink-0">
                                                    <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] uppercase font-medium whitespace-nowrap">Труд-ты:</span>
                                                    <span :class="isDarkMode ? 'text-amber-400' : 'text-amber-600'" class="font-bold font-mono text-sm tabular-nums whitespace-nowrap">
                                                        {{ formatTimeWithLeadingZeros(group.totals?.labor_cost?.total ?? group.labor_cost?.total ?? group.labor_cost ?? 0, 'hour') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- __ ПОДГРУППЫ (Группа блоков / Уровень 2) -->
                                        <div v-if="!group.collapsed"
                                             :class="isDarkMode ? 'border-slate-800 bg-slate-950/40' : 'border-slate-100 bg-slate-50/70'"
                                             class="border-t p-1.5 space-y-1.5">

                                            <div v-for="(sub, sIdx) in group.subgroups" :key="sIdx"
                                                 :class="isDarkMode ? 'bg-slate-900/60 border-slate-800/80' : 'bg-white border-slate-200/80'"
                                                 class="border rounded-lg overflow-hidden shadow-2xs">

                                                <!-- __ Шапка подгруппы -->
                                                <div
                                                    :class="isDarkMode ? 'hover:bg-slate-800/40' : 'hover:bg-slate-50/80'"
                                                    class="p-2 flex flex-wrap justify-between items-center cursor-pointer text-sm transition-colors gap-2"
                                                    @click="sub.collapsed = !sub.collapsed"
                                                >
                                                    <div :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'" class="flex items-center gap-2 font-semibold">
                                                        <div :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'"
                                                             :style="{ transform: !sub.collapsed ? 'rotate(90deg)' : 'none' }"
                                                             class="transition-transform duration-200">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </div>
                                                        <span>{{ sub.subgroupName }}</span>

                                                        <span v-if="sub.subgroupOrderTitle"
                                                              :class="isDarkMode ? 'bg-slate-800 text-slate-300 border-slate-700' : 'bg-slate-100 text-slate-600 border-slate-200'"
                                                              class="text-[11px] px-2 py-0.5 rounded border font-mono font-normal">
                                                            {{ sub.subgroupOrderTitle }}
                                                        </span>

                                                        <span v-if="sub.isTuning"
                                                              class="bg-red-500/10 text-red-400 text-[11px] px-2 py-0.5 rounded border border-red-500/20 font-mono font-bold uppercase tracking-wider">
                                                            Наладка
                                                        </span>
                                                    </div>

                                                    <!-- __ Выровненные данные подгруппы -->
                                                    <div class="flex items-center gap-2 ml-auto text-xs">
                                                        <!-- __ Кол-во -->
                                                        <div class="w-36 sm:w-40 px-1 flex items-center justify-between shrink-0">
                                                            <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] whitespace-nowrap">Кол-во:</span>
                                                            <div class="font-bold font-mono text-sm tabular-nums whitespace-nowrap">
                                                                <span :class="getLineQuantityClass(group, gIdx)">{{ sub.amount.total }}</span>
                                                                <span v-if="sub.amount.done !== undefined" :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'" class="text-xs font-normal">
                                                                    / {{ sub.amount.done }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- __ Площадь -->
                                                        <div v-if="sub.square && sub.square.total"
                                                             class="w-36 sm:w-40 px-1 flex items-center justify-between shrink-0">
                                                            <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] whitespace-nowrap">Площадь:</span>
                                                            <span :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'" class="font-bold font-mono text-sm tabular-nums whitespace-nowrap">
                                                                {{ Number(sub.square.total).toFixed(2) }} <span class="text-xs font-normal">м²</span>
                                                            </span>
                                                        </div>

                                                        <!-- __ АГРЕГАТОР 4: Трудозатраты ПОДГРУППЫ -->
                                                        <div class="w-44 sm:w-52 px-1 flex items-center justify-between shrink-0">
                                                            <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] whitespace-nowrap">Труд-ты:</span>
                                                            <span :class="isDarkMode ? 'text-amber-400' : 'text-amber-600'" class="font-bold font-mono text-sm tabular-nums whitespace-nowrap">
                                                                {{ formatTimeWithLeadingZeros(sub.totals?.labor_cost?.total ?? sub.labor_cost?.total ?? sub.labor_cost ?? 0, 'hour') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- __ СТРОКИ / ЛИНИИ (Таблица) -->
                                                <div v-if="!sub.collapsed"
                                                     :class="isDarkMode ? 'bg-slate-950/60 border-slate-800/50' : 'bg-slate-50/40 border-slate-100'"
                                                     class="p-1.5 overflow-x-auto border-t">
                                                    <table class="w-full text-left border-collapse text-xs">
                                                        <thead>
                                                        <tr :class="isDarkMode ? 'text-slate-400 border-slate-800' : 'text-slate-500 border-slate-200'"
                                                            class="border-b uppercase font-bold tracking-wider text-[11px]">
                                                            <th class="py-1.5 px-2.5 w-12 text-center">Поз.</th>
                                                            <th class="py-1.5 px-2.5">Код 1С / Наименование блока</th>
                                                            <th class="py-1.5 px-2.5 text-right">Кол-во</th>
                                                            <th class="py-1.5 px-2.5 text-right">Площадь (м²)</th>
                                                            <th class="py-1.5 px-2.5 text-right">Труд-ты</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody :class="isDarkMode ? 'divide-slate-800/50 text-slate-300' : 'divide-slate-200/60 text-slate-700'"
                                                               class="divide-y">
                                                        <tr v-for="line in sub.lines" :key="line.id"
                                                            :class="isLineIndigo(group, gIdx)
                                                                ? (isDarkMode ? 'hover:bg-slate-800/40' : 'hover:bg-indigo-50/30')
                                                                : (isDarkMode ? 'hover:bg-slate-800/40' : 'hover:bg-orange-50/30')"
                                                            class="transition-colors duration-150">
                                                            <td :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'" class="py-1.5 px-2.5 font-mono text-center font-medium">
                                                                #{{ line.position }}
                                                            </td>
                                                            <td class="py-1.5 px-2.5">
                                                                <div class="flex items-center gap-2">
                                                                    <span
                                                                        :class="get1CBadgeClass(group, gIdx)"
                                                                        class="font-mono font-semibold text-[11px] px-1.5 py-0.5 rounded border tracking-tight">
                                                                        {{ line.block.code_1c }}
                                                                    </span>
                                                                    <span :class="isDarkMode ? 'text-slate-100' : 'text-slate-900'" class="font-medium">
                                                                        {{ line.block.name }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td :class="isDarkMode ? 'text-slate-100' : 'text-slate-900'" class="py-1.5 px-2.5 text-right font-bold font-mono tabular-nums">
                                                                {{ line.amount }}
                                                            </td>
                                                            <td :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-600'"
                                                                class="py-1.5 px-2.5 text-right font-mono font-semibold tabular-nums">
                                                                {{ Number(line.square * line.amount).toFixed(3) }}
                                                            </td>
                                                            <td :class="isDarkMode ? 'text-amber-400' : 'text-amber-600'"
                                                                class="py-1.5 px-2.5 text-right font-mono font-semibold tabular-nums">
                                                                {{ formatTimeWithLeadingZeros(line.time, 'hour') }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </template>
                </div>
            </div>
        </div>
    </template>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import type { IBlockDay, IBlockTaskLinesGroupData, IPeriod } from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { BLOCK_TASK_DRAFT, CHANGE_1 } from '@/app/constants/blocks.ts'
import { groupTaskLinesForExecute } from '@/app/helpers/manufacture/helpers_blocks.ts'
import { formatDateInFullFormat, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'
import CellDatesSelectMiniTS from '@/components/dashboard/orders/components/CellDatesSelectMiniTS.vue'

interface ITotalsSummary {
    amount: {
        total: number
        done: number
    }
    square: {
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
const renderData = ref<IRenderDay[]>([])

const renderPeriod = ref<IPeriod | null>(null)

// __ Helper для подсчета сумм из массива групп
// const calculateGroupsTotals = (groups: IBlockTaskLinesGroupData[]): ITotalsSummary => {
//     return groups.reduce(
//         (acc, g) => {
//             acc.amount.total += g.amount?.total || 0
//             acc.amount.done += g.amount?.done || 0
//             acc.square.total += Number(g.square?.total || 0)
//             return acc
//         },
//         { amount: { total: 0, done: 0 }, square: { total: 0 } }
//     )
// }

// __ Helpers для динамических стилей СМЕН (Indigo / Orange)
const getShiftBorderClass = (changeIndex: number) => {
    if (changeIndex === 0) {
        return isDarkMode.value ? 'border-indigo-500/30' : 'border-indigo-300'
    }
    return isDarkMode.value ? 'border-orange-500/30' : 'border-orange-300'
}

const getShiftBadgeClass = (changeIndex: number) => {
    if (changeIndex === 0) {
        return isDarkMode.value
            ? 'bg-indigo-950/80 text-indigo-300 border-indigo-800/60'
            : 'bg-indigo-50 text-indigo-700 border-indigo-200'
    }
    return isDarkMode.value
        ? 'bg-orange-950/80 text-orange-300 border-orange-800/60'
        : 'bg-orange-50 text-orange-700 border-orange-200'
}

const getShiftChevronClass = (changeIndex: number) => {
    if (changeIndex === 0) {
        return isDarkMode.value ? 'text-slate-400 group-hover:text-indigo-400' : 'text-slate-500 group-hover:text-indigo-600'
    }
    return isDarkMode.value ? 'text-slate-400 group-hover:text-orange-400' : 'text-slate-500 group-hover:text-orange-600'
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
    if (isLineIndigo(group, gIdx)) {
        return isDarkMode.value ? 'bg-indigo-500' : 'bg-indigo-600'
    }
    return isDarkMode.value ? 'bg-orange-500' : 'bg-orange-600'
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
    if (isLineIndigo(group, gIdx)) {
        return isDarkMode.value ? 'text-indigo-400' : 'text-indigo-600'
    }
    return isDarkMode.value ? 'text-orange-400' : 'text-orange-600'
}

const get1CBadgeClass = (group: IBlockTaskLinesGroupData, gIdx: number) => {
    if (isLineIndigo(group, gIdx)) {
        return isDarkMode.value
            ? 'text-indigo-300 bg-indigo-950/80 border-indigo-800/50'
            : 'text-indigo-700 bg-indigo-50 border-indigo-200'
    }
    return isDarkMode.value
        ? 'text-orange-300 bg-orange-950/80 border-orange-800/50'
        : 'text-orange-700 bg-orange-50 border-orange-200'
}


//
// const getRenderData = () => {
//     renderData.value = []
//
//     const uniqueDates = new Set<string>()
//     blockDays.value.forEach(day => uniqueDates.add(day.action_at))
//
//     Array
//         .from(uniqueDates)
//         .sort((a, b) => new Date(a).getTime() - new Date(b).getTime())
//         .forEach(date => {
//             const dayTotals: ITotalsSummary = {
//                 amount: { total: 0, done: 0 },
//                 square: { total: 0 }
//             }
//
//             const targetDay: IRenderDay = {
//                 action_at: date,
//                 collapsed: false,
//                 changes: [],
//                 totals: dayTotals
//             }
//
//             const filteredDays = blockDays.value.filter(day => day.action_at === date)
//             const shiftsMap: { [key: number]: IBlockTaskLinesGroupData[] } = {}
//
//             filteredDays.forEach(day => {
//                 const unionTask = JSON.parse(JSON.stringify(BLOCK_TASK_DRAFT))
//                 day.block_tasks.forEach(task => {
//                     task.block_lines.forEach(line => unionTask.block_lines.push(line))
//                 })
//                 const summary = groupTaskLinesForExecute(unionTask.block_lines)
//
//                 const targetChange = day.change === CHANGE_1 ? 0 : 1
//                 shiftsMap[targetChange] = summary
//             })
//
//             // Формируем смены с агрегацией данных
//             ;[0, 1].forEach(changeIdx => {
//                 const groups = shiftsMap[changeIdx]
//                 if (groups && groups.length) {
//                     const shiftTotals = calculateGroupsTotals(groups)
//
//                     dayTotals.amount.total += shiftTotals.amount.total
//                     dayTotals.amount.done  += shiftTotals.amount.done
//                     dayTotals.square.total += shiftTotals.square.total
//
//                     targetDay.changes.push({
//                         changeIndex: changeIdx,
//                         collapsed: false,
//                         groups,
//                         totals: shiftTotals
//                     })
//                 }
//             })
//
//             renderData.value.push(targetDay)
//         })
// }


// Если нужно, расширяем тип итогов
interface ITotalsSummary {
    amount: { total: number; done: number }
    square: { total: number }
    labor_cost: { total: number }
}

// Вспомогательная функция расчёта и проброса итогов по группам/подгруппам
const calculateGroupsTotals = (groups: IBlockTaskLinesGroupData[]): ITotalsSummary => {
    const shiftTotals: ITotalsSummary = {
        amount: { total: 0, done: 0 },
        square: { total: 0 },
        labor_cost: { total: 0 }
    }

    groups.forEach(group => {
        let groupLaborTotal = 0

        // 1. Проходим по подгруппам и считаем трудозатраты каждой
        group.subgroups?.forEach(sub => {
            const subLaborTotal = (sub.lines || []).reduce((sum, line) => {
                return sum + (Number(line.time) || 0)
            }, 0)

            // Записываем totals в подгруппу
            sub.totals = {
                ...sub.totals,
                labor_cost: { total: subLaborTotal }
            }

            groupLaborTotal += subLaborTotal
        })

        // 2. Записываем totals в линию (группу)
        group.totals = {
            ...group.totals,
            labor_cost: { total: groupLaborTotal }
        }

        // 3. Аккумулируем в итоги смены
        shiftTotals.amount.total += group.amount?.total || 0
        shiftTotals.amount.done += group.amount?.done || 0
        shiftTotals.square.total += group.square?.total || 0
        shiftTotals.labor_cost.total += groupLaborTotal
    })

    return shiftTotals
}

const getRenderData = () => {
    renderData.value = []

    const uniqueDates = new Set<string>()
    blockDays.value.forEach(day => uniqueDates.add(day.action_at))

    Array
        .from(uniqueDates)
        .sort((a, b) => new Date(a).getTime() - new Date(b).getTime())
        .forEach(date => {
            // Инициализируем агрегатор ДНЯ (включая labor_cost)
            const dayTotals: ITotalsSummary = {
                amount: { total: 0, done: 0 },
                square: { total: 0 },
                labor_cost: { total: 0 }
            }

            const targetDay: IRenderDay = {
                action_at: date,
                collapsed: false,
                changes: [],
                totals: dayTotals
            }

            const filteredDays = blockDays.value.filter(day => day.action_at === date)
            const shiftsMap: { [key: number]: IBlockTaskLinesGroupData[] } = {}

            filteredDays.forEach(day => {
                const unionTask = JSON.parse(JSON.stringify(BLOCK_TASK_DRAFT))
                day.block_tasks.forEach(task => {
                    task.block_lines.forEach(line => unionTask.block_lines.push(line))
                })
                const summary = groupTaskLinesForExecute(unionTask.block_lines)

                const targetChange = day.change === CHANGE_1 ? 0 : 1
                shiftsMap[targetChange] = summary
            })

            // Формируем смены с агрегацией данных
            ;[0, 1].forEach(changeIdx => {
                const groups = shiftsMap[changeIdx]
                if (groups && groups.length) {
                    // Вычисляем итоги смены + проставляем totals в подгруппы и линии
                    const shiftTotals = calculateGroupsTotals(groups)

                    // Аккумулируем показатели смены в общий день
                    dayTotals.amount.total += shiftTotals.amount.total
                    dayTotals.amount.done  += shiftTotals.amount.done
                    dayTotals.square.total += shiftTotals.square.total
                    dayTotals.labor_cost.total += shiftTotals.labor_cost.total

                    targetDay.changes.push({
                        changeIndex: changeIdx,
                        collapsed: false,
                        groups,
                        totals: shiftTotals
                    })
                }
            })

            renderData.value.push(targetDay)
        })
}

const getDays = async (period: IPeriod | null = null) => {
    renderPeriod.value = period
    console.log(renderPeriod.value)

    const days: IBlockDay[] = await blockStore.getBlockDayByPeriod(renderPeriod.value)
    blockDays.value         = days.filter(day => day.block_tasks.length > 0)
}

const loadData = async (period: IPeriod | null = null) => {
    renderPeriod.value = period
    await getDays(renderPeriod.value)
    getRenderData()
}

onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            // await getDays()
            // getRenderData()

            await loadData()
        },

        undefined
    )

    isLoading.value = false
})
</script>

<style scoped>
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
