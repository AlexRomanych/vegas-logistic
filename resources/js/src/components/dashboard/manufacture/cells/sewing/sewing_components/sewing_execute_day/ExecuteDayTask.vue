<template>
    <div
        class="container bg-slate-100 p-1 text-slate-600 relative overflow-hidden flex flex-col font-sans"
        @contextmenu.prevent="openContextMenu"
    >
        <div class="flex justify-between items-center mb-2">
            <div class="flex justify-between items-center gap-3 w-full">
                <!-- __ Название Заявки -->
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ taskTitle }}</h1>
                    <p class="text-sm text-slate-400 font-medium">Выбрано элементов: {{ selectedIds.size }}</p>
                </div>
            </div>

            <div class="flex gap-0.5">

                <template v-for="(sewingLinesGroup, idx) of sewingLinesGroups" :key="idx">

                    <template v-if="sewingLinesGroup.hasData">
                        <!-- __ Группа сортировки СЗ по АШМ/УШМ -->
                        <AppLabelTS
                            :height="MENU_HEIGHT"
                            :text="sewingLinesGroup.groupName"
                            :type="activeTabIndex === idx ? 'primary' : sewingLinesGroup.groupType"
                            align="center"
                            class="menu-button"
                            rounded="4"
                            text-size="mini"
                            width="w-[50px]"
                            @click="activeTabIndex = idx"
                        />
                    </template>
                </template>

                <!-- __ Комментарий -->
                <AppLabelTS
                    :height="MENU_HEIGHT"
                    :text="sewingTask.comment ?? ''"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text-size="mini"
                    type="indigo"
                    width="min-w-[300px]"
                />


                <div
                    v-if="isRunning"
                    class="flex"
                >
                    <!-- __ Прогресс -->
                    <AppProgressBar
                        :progress="(statistics.time.finished / statistics.time.total) * 100"
                        :text="`${formatTimeWithLeadingZeros(statistics.time.finished)} / ${formatTimeWithLeadingZeros(statistics.time.total)}`"
                        height="h-[50px]"
                        text-size="mini"
                        width="w-[200px]"
                    />

                    <!-- __ Выполнено -->
                    <AppLabelTS
                        :disabled="selectedIds.size === 0"
                        :height="MENU_HEIGHT"
                        :type="selectedIds.size === 0 ? 'dark' : 'success'"
                        :width="MENU_WIDTH"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text="✓ Выполнено"
                        text-size="small"
                        @click="completeSelected"
                    />

                    <!-- __ Не Выполнено -->
                    <AppLabelTS
                        :disabled="selectedIds.size === 0"
                        :height="MENU_HEIGHT"
                        :type="selectedIds.size === 0 ? 'dark' : 'danger'"
                        :width="MENU_WIDTH"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text="✘ Не выполнено"
                        text-size="small"
                        @click="unCompleteSelected"
                    />

                    <!-- __ Сброс отметки -->
                    <AppLabelTS
                        :disabled="selectedIds.size === 0"
                        :height="MENU_HEIGHT"
                        :type="selectedIds.size === 0 ? 'dark' : 'stone'"
                        :width="MENU_WIDTH"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text="↺ Сбросить"
                        text-size="small"
                        @click="resetStatus"
                    />

                    <!-- __ Разбить количество -->
                    <AppLabelTS
                        :disabled="selectedIds.size === 0"
                        :height="MENU_HEIGHT"
                        :type="selectedIds.size === 0 ? 'dark' : 'indigo'"
                        :width="MENU_WIDTH"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text="⛏ Разбить"
                        text-size="small"
                        @click="divideElementAmount"
                    />
                </div>
            </div>
        </div>

        <!-- __ Заголовок для Линий -->
        <div class="ml-[23px]">
            <ExecuteDayTaskLineHeader :field-widths="fieldWidths"/>
        </div>

        <div
            ref="scrollContainer"
            class="py-2 flex-1 overflow-y-auto border border-slate-200 rounded-xl bg-white select-none custom-scroll relative shadow-sm"
        >

            <div v-for="(subgroup, sgIndex) of sewingLinesGroup" :key="sgIndex">

                <template v-if="subgroup.hasData">

                    <div class="ml-2 pt-1"><span class="font-semibold italic underline">{{ subgroup.subgroupName }}</span></div>

                    <div
                        v-for="sewingLine of subgroup.lines"
                        :key="sewingLine.id"
                        :class="[
                            selectedIds.has(sewingLine.id) ? 'bg-slate-300 text-slate-900' : 'hover:bg-gray-50',
                            sewingLine.completed ? '' : '',
                        ]"
                        :data-task-id="sewingLine.id"
                        class="h-[30px] flex items-center px-6 border-b border-gray-100 transition-colors relative"
                        @mousedown="startSelectionById(sewingLine.id, $event)"
                        @mouseenter="updateSelectionById(sewingLine.id, $event)"
                    >
                        <!-- __ Строка СЗ -->
                        <ExecuteDayTaskLine
                            :field-widths="fieldWidths"
                            :sewing-line="sewingLine"
                            @dblclick="showLineInfo(sewingLine)"
                        />

                        <!--class="absolute inset-y-0 left-0 w-1 bg-slate-500 pointer-events-none"-->
                        <div
                            v-if="selectedIds.has(sewingLine.id)"
                            class="absolute inset-0 border-l-4 border-r-4 border-slate-500 pointer-events-none animate-select"
                        ></div>
                    </div>

                </template>

                <!--<div-->
                <!--    v-for="(sewingLine, index) of subgroup.lines"-->
                <!--    :key="sewingLine.id"-->
                <!--    :class="[-->
                <!--    selectedIds.has(sewingLine.id) ? 'bg-slate-300 text-slate-900' : 'hover:bg-gray-50',-->
                <!--    sewingLine.completed ? '' : '',-->
                <!--]"-->
                <!--    :data-task-id="sewingLine.id"-->
                <!--    class="h-[30px] flex items-center px-6 border-b border-gray-100 transition-colors relative"-->
                <!--    @mousedown="startSelection(index, $event)"-->
                <!--    @mouseenter="updateSelection(index, $event)"-->
                <!--&gt;-->
                <!--    &lt;!&ndash; __ Строка СЗ &ndash;&gt;-->
                <!--    <ExecuteDayTaskLine-->
                <!--        :field-widths="fieldWidths"-->
                <!--        :sewing-line="sewingLine"-->
                <!--        @dblclick="showLineInfo(sewingLine)"-->
                <!--    />-->

                <!--    &lt;!&ndash;class="absolute inset-y-0 left-0 w-1 bg-slate-500 pointer-events-none"&ndash;&gt;-->
                <!--    <div-->
                <!--        v-if="selectedIds.has(sewingLine.id)"-->
                <!--        class="absolute inset-0 border-l-4 border-r-4 border-slate-500 pointer-events-none animate-select"-->
                <!--    ></div>-->
                <!--</div>-->

            </div>
        </div>

        <!-- __ Меню по правой кнопке мыши -->
        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="showMenu"
                    ref="menuRef"
                    :style="{ top: `${menuPosition.y}px`, left: `${menuPosition.x}px` }"
                    class="fixed z-[100] w-64 bg-white border border-gray-200 shadow-xl rounded-2xl overflow-hidden py-1.5 backdrop-blur-xl"
                    @click.stop
                >
                    <div
                        class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-gray-50 mb-1"
                    >
                        Действия ({{ selectedIds.size }})
                    </div>
                    <button
                        class="w-full flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-green-600 hover:text-white transition-colors"
                        @click="handleMenuAction('done')"
                    >
                        <span class="mr-3 text-lg">✓</span> Выполнено
                    </button>
                    <button
                        class="w-full flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-red-600 hover:text-white transition-colors"
                        @click="handleMenuAction('false')"
                    >
                        <span class="mr-3 text-lg">✘</span> Не выполнено
                    </button>
                    <!--<div class="h-[1px] bg-gray-100 my-1"></div>-->
                    <button
                        class="w-full flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-stone-600 hover:text-white transition-colors"
                        @click="handleMenuAction('reset')"
                    >
                        <span class="mr-3 text-lg">↺</span> Сбросить
                    </button>
                    <button
                        class="w-full flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-indigo-600 hover:text-white transition-colors"
                        @click="handleMenuAction('divide')"
                    >
                        <span class="mr-3 text-lg">⛏</span> Разбить
                    </button>
                </div>
            </Transition>
        </Teleport>

        <div class="mt-4 text-[11px] font-bold text-slate-400 flex gap-6 px-2">
            <span class="flex items-center gap-1.5"
            ><span class="bg-gray-200 px-1 rounded text-[10px]">DRAG</span> Выделение</span
            >
            <span class="flex items-center gap-1.5"
            ><span class="bg-gray-200 px-1 rounded text-[10px]">CTRL</span> Выбор вразнобой</span
            >
            <span class="flex items-center gap-1.5"
            ><span class="bg-gray-200 px-1 rounded text-[10px]">SHIFT</span> Диапазон</span
            >
        </div>
    </div>

    <!-- __ Модальное окно для добавления причины не выполнения -->
    <ExecuteDayFalseReason
        ref="executeDayFalseReason"
        :false-reason="falseReason"
        label="Причина не выполнения"
    />

    <!-- __ Модальное окно для информации о записи -->
    <OrderItemInfo
        ref="orderItemInfo"
        :order-line="orderLine"
    />

    <!-- __ Разбить Количество Модальное окно  -->
    <AppRangeModalAsyncTS
        ref="appRangeModalAsyncTS"
        :item="dividerElement"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />
</template>

<script lang="ts" setup>
import { ref, onMounted, onUnmounted, nextTick, watch, computed, onBeforeUnmount } from 'vue'

import type { IColorTypes, IDividerItem, ISewingTask, ISewingTaskLine, ISewingTaskOrderLine } from '@/types'

import {
    getCoverSizeString,
    getExecuteTaskStatistics,
    getSewingTaskModelCoverName, groupTaskLinesForExecute,
} from '@/app/helpers/manufacture/helpers_sewing.ts'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import ExecuteDayFalseReason from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute_day/ExecuteDayFalseReason.vue'
import ExecuteDayTaskLine from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute_day/ExecuteDayTaskLine.vue'
import OrderItemInfo from '@/components/dashboard/manufacture/cells/sewing/sewing_components/common/OrderItemInfo.vue'
import ExecuteDayTaskLineHeader from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute_day/ExecuteDayTaskLineHeader.vue'
import AppRangeModalAsyncTS from '@/components/ui/modals/AppRangeModalAsyncTS.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'

interface IProps {
    sewingTask: ISewingTask
    isRunning: boolean | null
}

const props = defineProps<IProps>()

const emits = defineEmits<{
    (e: 'setFinishStatus', payload: number[]): void
    (e: 'setFalseStatus', payload: number[], falseReason: string): void
    (e: 'resetStatus', payload: number[]): void
    (e: 'divideLine', taskId: number, lineId: number, divideAmount: { take: number; keep: number }): void
}>()

const MENU_WIDTH  = 'w-[130px]'
const MENU_HEIGHT = 'h-[50px]'

// __ Тип для модального окна информации о записи в Заявке
const orderLine     = ref<ISewingTaskOrderLine | null>(null)
const orderItemInfo = ref<InstanceType<typeof OrderItemInfo> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна изменения Причины не выполнения
const falseReason           = ref('')
const executeDayFalseReason = ref<InstanceType<typeof ExecuteDayFalseReason> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна "Разбить количество"
const modalType            = ref<IColorTypes>('primary')
const modalText            = ref<string>('')
const modalMode            = ref<'inform' | 'confirm'>('inform')
const dividerElement       = ref<IDividerItem>({ name: '', amount: 0 })
const appRangeModalAsyncTS = ref<InstanceType<typeof AppRangeModalAsyncTS> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

const statistics = computed(() => getExecuteTaskStatistics(props.sewingTask))

// --- Инициализация данных ---
// const tasks = ref(Array.from({ length: 40 }, (_, i) => ({
//     id:        Date.now() + i,
//     title:     `Подготовить отчет по модулю ${String.fromCharCode(65 + i % 26)}${i}`,
//     completed: false
// })))

// __ Табы Группировки СЗ по АШМ/УШМ
const activeTabIndex = ref(0)

// __ Формируем объект выполнения
// const sewingLines       = ref<ISewingTaskLine[]>([])
const sewingLinesGroups = computed(() => groupTaskLinesForExecute(props.sewingTask.sewing_lines))
const sewingLinesGroup  = computed(() => sewingLinesGroups.value[activeTabIndex.value].subgroups)
const sewingLines       = computed(() => {
    const result: ISewingTaskLine[] = []
    sewingLinesGroups.value[activeTabIndex.value].subgroups.forEach(subgroup => {
        subgroup.lines.forEach(line => {
            result.push(line)
        })
    })
    return result
})


// const getSewingLines = () => {
    // const linesGrouped = groupTaskLinesForExecute(props.sewingTask.sewing_lines)
    // console.log('linesGrouped: ', linesGrouped)

    //
    //     sewingLines.value = props.sewingTask.sewing_lines
    //         .map((line) => ({ ...line, completed: false }))
    //         .sort((a, b) => {
    //             // __ 1. Сначала сравниваем по времени
    //             const compA    = a.finished_at || a.false_at || '2050-01-01 00:00:00'
    //             const compB    = b.finished_at || b.false_at || '2050-01-01 00:00:00'
    //             const timeDiff = new Date(compA).getTime() - new Date(compB).getTime()
    //
    //             // 2. __ Если время разное (не 0), возвращаем результат сравнения времени
    //             if (timeDiff !== 0) {
    //                 return timeDiff
    //             }
    //
    //             // __ 3. Если время совпало, сортируем по позиции
    //             return a.position - b.position
    //         })
// }

// __ Название заявок
const taskTitle = computed(() => {
    if (props.sewingTask.id === 0) {
        return 'Объединение СЗ'
    }
    return `${props.sewingTask.position}. ${props.sewingTask.order.client.short_name} №${props.sewingTask.order.order_no_num}`
})

// __ Показать информацию о записи
const showLineInfo = async (sewingLine: ISewingTaskLine) => {
    orderLine.value = sewingLine.order_line
    await orderItemInfo.value!.show() // показываем модалку и ждем ответ
}

// __ Поля данных
const fieldWidths: Record<string, string> = {
    check      : 'min-w-[25px] max-w-[25px]',
    space      : 'min-w-[10px] max-w-[10px]',
    position   : 'min-w-[30px] max-w-[30px]',
    size       : 'min-w-[80px] max-w-[80px]',
    coverName  : 'min-w-[250px] max-w-[250px]',
    amount     : 'min-w-[30px] max-w-[30px]',
    time       : 'min-w-[70px] max-w-[70px]',
    machine    : 'min-w-[30px] max-w-[30px]',
    textile    : 'min-w-[70px] max-w-[70px]',
    tkch       : 'min-w-[70px] max-w-[70px]',
    kant       : 'min-w-[70px] max-w-[70px]',
    kdch       : 'min-w-[50px] max-w-[50px]',
    composition: 'min-w-[70px] max-w-[70px]',
    describe_1 : 'min-w-[70px] max-w-[70px]',
    describe_2 : 'min-w-[70px] max-w-[70px]',
    describe_3 : 'min-w-[70px] max-w-[70px]',
    timeLabel  : 'min-w-[80px] max-w-[80px]',
    reason     : 'min-w-[250px] max-w-[250px]',
}

// --- Состояние выделения ---
const scrollContainer  = ref<HTMLElement | null>(null)
const selectedIds      = ref(new Set<number>())
const isDragging       = ref(false)
const startIndex       = ref<number | null>(null)
const lastClickedIndex = ref<number | null>(null)

// --- Состояние меню ---
const showMenu     = ref(false)
const menuPosition = ref({ x: 0, y: 0 })
const menuRef      = ref<HTMLElement | null>(null)

// --- Логика автоскролла на requestAnimationFrame ---
let scrollId: number | null = null

/**
 * __ Запуск автоскролла
 * @param direction - 1 для скролла вниз, -1 для скролла вверх
 */
const startAutoScroll = (direction: 1 | -1): void => {
    if (scrollId !== null) return

    const scrollStep = () => {
        if (scrollContainer.value) {
            // __ 12px — это базовая скорость. Можно умножить на коэффициент для ускорения
            scrollContainer.value.scrollTop += direction * 12

            // __ Рекурсивно вызываем следующий кадр
            scrollId = requestAnimationFrame(scrollStep)
        }
    }

    scrollId = requestAnimationFrame(scrollStep)
}

const stopAutoScroll = (): void => {
    if (scrollId !== null) {
        cancelAnimationFrame(scrollId)
        scrollId = null
    }
}

// --- Логика автоскролла на setInterval ---
// __ Используем ReturnType для максимальной точности или просто number | null
// let scrollInterval: ReturnType<typeof setInterval> | null  = null;
//
// /**
//  * Запуск автоскролла
//  * @param direction - 1 для скролла вниз, -1 для скролла вверх
//  */
// const startAutoScroll = (direction: 1 | -1) => {
//     if (scrollInterval) return
//     scrollInterval = setInterval(() => {
//         if (scrollContainer.value) {
//             scrollContainer.value.scrollTop += direction * 12
//         }
//     }, 16)
// }
// const stopAutoScroll = (): void => {
//     if (scrollInterval !== null) {
//         clearInterval(scrollInterval);
//         scrollInterval = null;
//     }
// }

// --- Методы выделения на id ---
const flatVisibleIds = computed(() => {
    return sewingLinesGroup.value.flatMap(subgroup => subgroup.lines.map(line => line.id))
})

const startSelectionById = (id: number, event: MouseEvent) => {
    if (event.button === 2) return
    isDragging.value = true

    const currentIndex = flatVisibleIds.value.indexOf(id)
    startIndex.value   = currentIndex

    if (event.shiftKey && lastClickedIndex.value !== null) {
        applyRangeSelection(lastClickedIndex.value, currentIndex, event.ctrlKey)
    } else if (event.ctrlKey || event.metaKey) {
        if (selectedIds.value.has(id)) {
            selectedIds.value.delete(id)
        } else {
            selectedIds.value.add(id)
        }
    } else {
        selectedIds.value.clear()
        selectedIds.value.add(id)
    }
    lastClickedIndex.value = currentIndex
}

const updateSelectionById = (id: number, event: MouseEvent) => {
    if (!isDragging.value) return
    const currentIndex = flatVisibleIds.value.indexOf(id)
    applyRangeSelection(startIndex.value!, currentIndex, event.ctrlKey || event.metaKey)
}

const applyRangeSelection = (startIdx: number, endIdx: number, isCumulative: boolean) => {
    const start = Math.min(startIdx, endIdx)
    const end   = Math.max(startIdx, endIdx)

    if (!isCumulative) selectedIds.value.clear()

    for (let i = start; i <= end; i++) {
        const id = flatVisibleIds.value[i]
        if (id) selectedIds.value.add(id)
    }
}


// // --- Методы выделения на индексе ---
// const startSelection = (index: number, event: MouseEvent) => {
//     // Если клик правой кнопкой — игнорируем здесь (обработает contextmenu)
//     if (event.button === 2) return
//
//     isDragging.value = true
//     startIndex.value = index
//
//     if (event.shiftKey && lastClickedIndex.value !== null) {
//         applyRangeSelection(lastClickedIndex.value, index, event.ctrlKey)
//     } else if (event.ctrlKey || event.metaKey) {
//         if (selectedIds.value.has(sewingLines.value[index].id)) {
//             selectedIds.value.delete(sewingLines.value[index].id)
//         } else {
//             selectedIds.value.add(sewingLines.value[index].id)
//         }
//     } else {
//         selectedIds.value.clear()
//         selectedIds.value.add(sewingLines.value[index].id)
//     }
//     lastClickedIndex.value = index
// }
//
// const updateSelection = (currentIndex: number, event: MouseEvent) => {
//     if (!isDragging.value) return
//
//     // __ Используем ключи из переданного объекта события
//     const isMultiSelect = event.ctrlKey || event.metaKey
//
//     applyRangeSelection(startIndex.value!, currentIndex, isMultiSelect)
// }
//
// const applyRangeSelection = (startIdx: number, endIdx: number, isCumulative: boolean) => {
//     const start = Math.min(startIdx, endIdx)
//     const end   = Math.max(startIdx, endIdx)
//
//     if (!isCumulative) selectedIds.value.clear()
//
//     for (let i = start; i <= end; i++) {
//         selectedIds.value.add(sewingLines.value[i].id)
//     }
// }

// --- Обработка мыши и скролла ---
const handleMouseMove = (event: MouseEvent) => {
    if (!isDragging.value || !scrollContainer.value) return

    const rect      = scrollContainer.value.getBoundingClientRect()
    const threshold = 50

    if (event.clientY > rect.bottom - threshold) {
        startAutoScroll(1)
    } else if (event.clientY < rect.top + threshold) {
        startAutoScroll(-1)
    } else {
        stopAutoScroll()
    }
}

// --- Контекстное меню ---
const openContextMenu = async (event: MouseEvent) => {
    // __ Проверяем на то, чтобы СЗ было запущено
    if (!props.isRunning) {
        return
    }

    // __ Типизируем поиск ближайшего элемента с ID задачи
    const target = (event.target as HTMLElement).closest<HTMLElement>('[data-task-id]')
    if (target) {
        // __ Извлекаем ID из dataset (в HTML это data-task-id)
        const id = Number(target.dataset.taskId)
        if (!selectedIds.value.has(id)) {
            selectedIds.value.clear()
            selectedIds.value.add(id)
        }
    }

    showMenu.value = true

    // __ Ждем, пока Vue обновит DOM, чтобы измерить размеры меню
    await nextTick()

    let x = event.clientX
    let y = event.clientY

    const menuWidth  = menuRef.value?.offsetWidth || 250
    const menuHeight = menuRef.value?.offsetHeight || 180

    // __ Логика предотвращения выхода меню за границы экрана 🖥️
    if (x + menuWidth > window.innerWidth) x -= menuWidth
    if (y + menuHeight > window.innerHeight) y -= menuHeight

    menuPosition.value = { x, y }
}

// __ Меню
const handleMenuAction = (action: string) => {
    if (action === 'done') {
        completeSelected()
    } else if (action === 'false') {
        unCompleteSelected()
    } else if (action === 'reset') {
        resetStatus()
    } else if (action === 'divide') {
        divideElementAmount()
    }

    showMenu.value = false
}

// __ Выполнено
const completeSelected = () => {
    const ids: number[] = []

    // __ Выбираем только не завершенные задачи
    sewingLines.value.forEach((t) => {
        if (selectedIds.value.has(t.id)) {
            if (!t.finished_at) {
                ids.push(t.id)
            }
        }
    })

    if (!ids.length) {
        return
    }

    emits('setFinishStatus', ids)
    showMenu.value = false
}

// __ Не Выполнено
const unCompleteSelected = async () => {
    const ids: number[] = []

    // __ Выбираем только задачи с нулевым статусом
    sewingLines.value.forEach((t) => {
        if (selectedIds.value.has(t.id)) {
            if (!t.finished_at && !t.false_at) {
                ids.push(t.id)
            }
        }
    })

    if (!ids.length) {
        return
    }

    const answer = await executeDayFalseReason.value?.show()
    if (answer) {
        falseReason.value = executeDayFalseReason.value?.falseReason ?? ''

        if (!falseReason.value) {
            return
        }

        emits('setFalseStatus', ids, falseReason.value)
        showMenu.value = false
    }
}

// __ Сброс статуса
const resetStatus = async () => {
    const ids: number[] = []

    // __ Выбираем только задачи с нулевым статусом
    sewingLines.value.forEach((t) => {
        if (selectedIds.value.has(t.id)) {
            if (t.finished_at || t.false_at) {
                ids.push(t.id)
            }
        }
    })

    if (!ids.length) {
        return
    }

    emits('resetStatus', ids)
    showMenu.value = false
}

// __ Разбить количество
const divideElementAmount = async () => {
    // __ Проверяем, что есть выделенные элементы
    if (selectedIds.value.size === 0) {
        return
    }

    // __ Проверяем, что это не объединение СЗ
    if (props.sewingTask.id === 0) {
        return
    }

    // __ Берем первый элемент из выделенных
    const findElement = JSON.parse(
        JSON.stringify(sewingLines.value.find((line) => line.id === Array.from(selectedIds.value)[0]))
    )

    // __ Проверяем, что элемент не завершен
    if (findElement && (findElement.finished_at || findElement.false_at || findElement.amount === 1)) {
        return
    }

    // __ Формируем название для модального окна
    const modelCover = getSewingTaskModelCoverName(findElement.order_line.model)

    dividerElement.value.name =
        getCoverSizeString(findElement) + ' ' + modelCover + ' ' + findElement.order_line.amount.toString() + ' шт.'

    dividerElement.value.amount = findElement.amount

    const answer = await appRangeModalAsyncTS.value!.show() // показываем модалку и ждем ответ
    if (answer) {
        // __ Получаем диапазон + проверяем его (страховочка)
        const range = appRangeModalAsyncTS.value!.range
        if (!range || range.take === 0 || range.keep === 0) {
            return
        }

        emits('divideLine', props.sewingTask.id, findElement.id, range)
        selectedIds.value.clear()
    }
}

const stopGlobalSelection = () => {
    isDragging.value = false
    stopAutoScroll()
}

// __ Смотрим на то, чтобы при переключении между СЗ не попали на вкладку (УПМ/УШМ) с нулевыми СЗ,
// __ которые не отображаются
watch(
    () => sewingLinesGroups.value,
    () => {
        if (!sewingLinesGroups.value[activeTabIndex.value].hasData) {
            for (let i = 0; i < sewingLinesGroups.value.length; i++) {
                if (sewingLinesGroups.value[i].hasData) {
                    activeTabIndex.value = i
                }
            }
        }
        selectedIds.value.clear() // __ Очистка выделения
    },
    { deep: true, immediate: true }
)


// watch(
//     () => props.sewingTask,
//     () => {
//         selectedIds.value.clear() // __ Очистка выделения
//         getSewingLines()
//         // console.log('sewingTask__: ', props.sewingTask)
//     },
//     { deep: true, immediate: true }
// )

// --- Жизненный цикл ---
onMounted(async () => {
    window.addEventListener('mouseup', stopGlobalSelection)
    window.addEventListener('mousemove', handleMouseMove)
    window.addEventListener('click', () => (showMenu.value = false))

    activeTabIndex.value = 0
})

onBeforeUnmount(() => {
    stopAutoScroll()
})

onUnmounted(() => {
    window.removeEventListener('mouseup', stopGlobalSelection)
    window.removeEventListener('mousemove', handleMouseMove)
    stopAutoScroll()
})
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    width: 6px;
}

.custom-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scroll::-webkit-scrollbar-thumb {
    background: #e2e8f0; /* Светлый скролл */
    border-radius: 10px;
}

.custom-scroll::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.1s ease,
    transform 0.1s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-8px) scale(0.95);
}

.select-none {
    user-select: none;
    -webkit-user-select: none;
}

/* Измененная анимация выделения для светлой темы */
@keyframes select-blink-light {
    0% {
        background-color: rgba(79, 70, 229, 0.05);
    }
    100% {
        background-color: rgba(79, 70, 229, 0.1);
    }
}

.animate-select {
    animation: select-blink-light 0.3s ease-out forwards;
}

.container {
    /*
overflow: auto; !* Включаем скролл для всего контейнера *!
position: relative;
*/

    /* Высота: из всей высоты экрана вычитаем сумму хедера и футера */
    height: calc(100vh - var(--header-height) - var(--footer-height) - 140px);
    /* Ширина: из всей ширины вычитаем ширину сайдбара */
    /*
width: calc(100vw - var(--sidebar-width) - 15px);

@apply border-2 border-gray-300 rounded-md p-1;
*/
}

@keyframes select-blink {
    0% {
        opacity: 0;
        transform: scaleX(0.98);
    }
    50% {
        opacity: 1;
        background-color: rgba(0, 0, 0, 0.3);
    }
    100% {
        opacity: 1;
        transform: scaleX(1);
    }
}

.animate-select {
    animation: select-blink 0.4s ease-out forwards;
}

.menu-button {
    @apply cursor-pointer shadow-[0_0_15px_rgba(79,70,229,0.4)];
}
</style>
