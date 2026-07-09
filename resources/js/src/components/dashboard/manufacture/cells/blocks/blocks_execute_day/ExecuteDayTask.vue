<template>
    <div
        class="wrapper bg-slate-100 p-1 text-slate-600 relative overflow-hidden flex flex-col font-sans"
        @contextmenu.prevent="openContextMenu"
    >
        <div class="flex justify-between items-center mb-2 gap-4">
            <div class="flex justify-between items-center gap-3">
                <!-- __ Название Заявки -->
                <div class="flex-none w-[350px]">
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight truncate">{{ taskTitle }}</h1>
                    <p class="text-sm text-slate-400 font-medium">Выбрано элементов: {{ selectedIds.size }}</p>
                </div>
            </div>

            <div class="flex-1 flex justify-start items-center gap-0.5">

                <template v-for="(blockLinesGroup, idx) of blockLinesGroups" :key="idx">

                    <template v-if="blockLinesGroup.hasData">
                        <!-- __ Группа сортировки СЗ по АШМ/УШМ -->
                        <AppLabelMultiLineTS
                            :height="MENU_HEIGHT"
                            :text="blockLinesGroup.groupName"
                            :title="`${blockLinesGroup.groupName}`"
                            :type="activeTabIndex === idx ? 'primary' : blockLinesGroup.groupType"
                            align="center"
                            class="menu-button"
                            rounded="4"
                            text-size="mini"
                            width="w-[50px]"
                            @click="activeTabIndex = idx"
                        />
                    </template>
                </template>

                <!-- __ Видимость заголовков -->
                <AppLabelTS
                    :height="MENU_HEIGHT"
                    :text="showUndergroupNames ? '🔓' : '🔒'"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text-size="huge"
                    title="Скрыть/Показать название размеров Кроя"
                    type="dark"
                    width="w-[50px]"
                    @click="toggleUnderGroupTitleVisibility"
                />

                <!-- __ Свернуть/Развернуть Блоки -->
                <AppLabelMultiLineTS
                    :height="MENU_HEIGHT_MULTILINE"
                    :text="collapsedSubGroupsState ? ['Раскрыть', '▼ Группу'] : ['Свернуть', '▲ Группу']"
                    :type="'primary'"
                    :width="MENU_WIDTH"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text-size="mini"
                    title="Свернуть/Развернуть Коллекцию Блоков"
                    @click="toggleSubGroups"
                />

                <!-- __ Свернуть/Развернуть Крой '▲' : '▼' -->
                <!--<AppLabelMultiLineTS-->
                <!--    :disabled="showUndergroupNames"-->
                <!--    :height="MENU_HEIGHT_MULTILINE"-->
                <!--    :text="collapsedUnderGroupsState ? ['Раскрыть', '▼ Крой'] : ['Свернуть', '▲ Крой']"-->
                <!--    :type="!showUndergroupNames ? 'dark' : 'indigo'"-->
                <!--    :width="MENU_WIDTH"-->
                <!--    align="center"-->
                <!--    class="menu-button"-->
                <!--    rounded="4"-->
                <!--    text-size="mini"-->
                <!--    title="Свернуть/Развернуть группу Кроя"-->
                <!--    @click="toggleUnderGroups"-->
                <!--/>-->

                <!-- __ Рулоны -->
                <!--<AppLabelTS-->
                <!--    :height="MENU_HEIGHT"-->
                <!--    align="center"-->
                <!--    class="menu-button"-->
                <!--    rounded="4"-->
                <!--    text="🔗"-->
                <!--    text-size="huge"-->
                <!--    title="Операции с рулонами ткани"-->
                <!--    type="dark"-->
                <!--    width="w-[50px]"-->
                <!--    @click="showSummary"-->
                <!--/>-->

                <!-- __ Слои -->
                <!--<AppLabelTS-->
                <!--    :height="MENU_HEIGHT"-->
                <!--    align="center"-->
                <!--    class="menu-button"-->
                <!--    rounded="4"-->
                <!--    text="📐"-->
                <!--    text-size="huge"-->
                <!--    title="Операции с Настилами"-->
                <!--    type="dark"-->
                <!--    width="w-[50px]"-->
                <!--    @click="console.log('layers')"-->
                <!--/>-->

                <!-- __ Печать -->
                <AppLabelTS
                    :height="MENU_HEIGHT"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text="📄"
                    text-size="huge"
                    title="Печать Сменного Задания"
                    type="dark"
                    width="w-[50px]"
                    @click="printTask"
                />

                <!-- __ Комментарий -->
                <template v-if="blockTask.comment">
                    <AppLabelTS
                        :height="MENU_HEIGHT"
                        :text="blockTask.comment ?? ''"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text-size="mini"
                        title="Комментарий к Сменному Заданию"
                        type="indigo"
                        width="min-w-[250px]"
                    />
                </template>

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

                    <!-- __ Изменить Линию -->
                    <AppLabelTS
                        :height="MENU_HEIGHT"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text="⚙️"
                        text-size="huge"
                        title="Переместить элемент на другую Линию"
                        type="dark"
                        width="w-[50px]"
                        @click="changeManufLines"
                    />

                    <!-- __ Выполнено -->
                    <AppLabelMultiLineTS
                        :disabled="selectedIds.size === 0"
                        :height="MENU_HEIGHT_MULTILINE"
                        :text="['✓', 'Вып-но']"
                        :type="selectedIds.size === 0 ? 'dark' : 'success'"
                        :width="MENU_WIDTH"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text-size="small"
                        title="Установить отметку Выполнено"
                        @click="completeSelected"
                    />

                    <!-- __ Не Выполнено -->
                    <AppLabelMultiLineTS
                        :disabled="selectedIds.size === 0"
                        :height="MENU_HEIGHT_MULTILINE"
                        :text="['✘', 'Не вып-но']"
                        :type="selectedIds.size === 0 ? 'dark' : 'danger'"
                        :width="MENU_WIDTH"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text-size="small"
                        title="Установить отметку Не выполнено"
                        @click="unCompleteSelected"
                    />

                    <!-- __ Сброс отметки -->
                    <AppLabelMultiLineTS
                        :disabled="selectedIds.size === 0"
                        :height="MENU_HEIGHT_MULTILINE"
                        :text="['↺', 'Сбросить']"
                        :type="selectedIds.size === 0 ? 'dark' : 'stone'"
                        :width="MENU_WIDTH"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text-size="small"
                        title="Сброс отметки Выполнено/Не выполнено"
                        @click="resetStatus"
                    />

                    <!-- __ Разбить количество -->
                    <AppLabelMultiLineTS
                        :disabled="selectedIds.size === 0"
                        :height="MENU_HEIGHT_MULTILINE"
                        :text="['⛏', 'Разбить']"
                        :type="selectedIds.size === 0 ? 'dark' : 'indigo'"
                        :width="MENU_WIDTH"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text-size="small"
                        title="Разбить количество элементов"
                        @click="divideElementAmount"
                    />

                    <!-- __ Сброс отметки выделения -->
                    <AppLabelMultiLineTS
                        :disabled="selectedIds.size === 0"
                        :height="MENU_HEIGHT_MULTILINE"
                        :text="['↺', 'Отменить']"
                        :type="selectedIds.size === 0 ? 'dark' : 'warning'"
                        :width="MENU_WIDTH"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text-size="small"
                        title="Отменить выделение"
                        @click="selectedIds.clear()"
                    />

                </div>
            </div>
        </div>

        <!-- __ Заголовок для Линий -->
        <div class="ml-[39px]">
            <ExecuteDayTaskLineHeader :field-widths="fieldWidths"/>
        </div>

        <div
            ref="scrollContainer"
            class="py-2 flex-1 overflow-y-auto border border-slate-200 rounded-xl bg-white select-none custom-scroll relative shadow-sm"
        >

            <div v-for="(subgroup, sgIndex) of blockLinesGroup" :key="sgIndex">

                <template v-if="subgroup.hasData">
                    <!-- __ Название Коллекции Блоков -->
                    <div
                        :class="[!collapseStates[subgroup.subgroupName] ? 'mt-2' : '']"
                        class="ml-2"
                    >
                        <ExecuteDayTaskSubGroup
                            :collapsed="collapseStates[subgroup.subgroupName]"
                            :subgroup
                            @toggle-collapse="toggleCollapse(subgroup.subgroupName)"
                            @select-subgroup-items="selectSubgroupItems(subgroup)"
                        />
                    </div>

                    <div
                        v-if="!collapseStates[subgroup.subgroupName]"
                        :class="[!collapseStates[subgroup.subgroupName] ? 'mb-2' : '']"
                        class="ml-4"
                    >

                        <!-- !!! С фиксированной высотой строки СЗ !!! -->
                        <!--class="h-[35px] flex items-center px-6 border-b border-gray-100 transition-colors relative"-->

                        <div
                            v-for="(blockLine, index) of subgroup.lines"
                            :key="blockLine.id"
                            :class="[
                                selectedIds.has(blockLine.id) ? 'bg-slate-300 text-slate-900' : 'hover:bg-gray-50',
                                blockLine.completed ? '' : '',
                            ]"
                            :data-task-id="blockLine.id"
                            class="my-[-1px] flex items-center px-6 border-b border-gray-100 transition-colors relative"
                            @mousedown="startSelectionById(blockLine.id, $event)"
                            @mouseenter="updateSelectionById(blockLine.id, $event)"
                        >
                            <!-- __ Строка СЗ -->
                            <ExecuteDayTaskLine
                                :block-line="blockLine"
                                :field-widths="fieldWidths"
                                :index="index + 1"
                                :ordering="'index'"
                                @show-document="showDocument(blockLine, $event)"
                            />

                            <!--class="absolute inset-y-0 left-0 w-1 bg-slate-500 pointer-events-none"-->
                            <div
                                v-if="selectedIds.has(blockLine.id)"
                                class="absolute inset-0 border-l-4 border-r-4 border-slate-500 pointer-events-none animate-select"
                            ></div>
                        </div>

                    </div>
                </template>

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

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
    />

    <!-- __ Смена Производственной линии -->
    <ManageTaskManufLines
        ref="manageTaskManufLines"
        :mode="modalMode"
        :task="taskCard"
        :text="modalText"
        :type="modalType"
    />

    <!-- __ Просмотр PDF в модальном режиме -->
    <BlockDesignDocumentAsync
        ref="blockDesignDocumentAsync"
        :doc="doc"
        ok-word="Понятно"
        type="primary"
    />

</template>

<script lang="ts" setup>
import { ref, onMounted, onUnmounted, nextTick, watch, computed, onBeforeUnmount, } from 'vue'
import { useRouter } from 'vue-router'

import type {
    IColorTypes,
    IDividerItem,
    IBlockTask,
    IBlockTaskLine,
    IBlockTaskOrderLine, IBlockLineSetData, IBlockTaskLinesSubgroup, IBlockTaskLinesGroupData, IBlockDocument
} from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

import { TASK_TO_PRINT_KEY, TASK_TO_PRINT_META_KEY } from '@/app/constants/common.ts'
import { BLOCK_TASK_DRAFT, BLOCK_UNION_TASK_NAME } from '@/app/constants/blocks.ts'

import {
    getExecuteTaskStatistics,
    groupTaskLinesForExecute,
    isTaskLineReset,
    // groupTaskLinesForExecuteForUnion,
} from '@/app/helpers/manufacture/helpers_blocks.ts'
import { formatTimeWithLeadingZeros, splitDate } from '@/app/helpers/helpers_date'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppRangeModalAsyncTS from '@/components/ui/modals/AppRangeModalAsyncTS.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'

import ExecuteDayFalseReason from '@/components/dashboard/manufacture/cells/blocks/blocks_execute_day/ExecuteDayFalseReason.vue'
import ExecuteDayTaskLine from '@/components/dashboard/manufacture/cells/blocks/blocks_execute_day/ExecuteDayTaskLine.vue'
import OrderItemInfo from '@/components/dashboard/manufacture/cells/blocks/common/OrderItemInfo.vue'
import ExecuteDayTaskLineHeader from '@/components/dashboard/manufacture/cells/blocks/blocks_execute_day/ExecuteDayTaskLineHeader.vue'
import ExecuteDayTaskSubGroup from '@/components/dashboard/manufacture/cells/blocks/blocks_execute_day/ExecuteDayTaskSubGroup.vue'
import BlockDesignDocumentAsync from '@/components/dashboard/manufacture/shared/block_design/BlockDesignDocumentAsync.vue'
import ManageTaskManufLines from '@/components/dashboard/manufacture/cells/blocks/blocks_manage/ManageTaskManufLines.vue'


interface IProps {
    blockTask: IBlockTask
    isRunning: boolean | null
}

const props = defineProps<IProps>()

const emits = defineEmits<{
    (e: 'setFinishStatus', payload: number[]): void
    (e: 'setFalseStatus', payload: number[], falseReason: string): void
    (e: 'resetStatus', payload: number[]): void
    (e: 'divideLine', taskId: number, lineId: number, divideAmount: { take: number; keep: number }): void
}>()

const router     = useRouter()
const blockStore = useBlocksStore()

// console.log('props.blockTask: ', props.blockTask)

const UNION_TASKS_ID = 0

const MENU_WIDTH            = 'w-[85px]'
const MENU_HEIGHT           = 'h-[50px]'
const MENU_HEIGHT_MULTILINE = 'h-[25px]'

// __ Тип для модального окна информации о записи в Заявке
const orderLine     = ref<IBlockTaskOrderLine | null>(null)
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

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для Карточки и Изменения Линии
const manageTaskManufLines = ref<InstanceType<typeof ManageTaskManufLines> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Карточка СЗ
const taskCard = ref<IBlockTask>(BLOCK_TASK_DRAFT)

// __ Агрегатор
const statistics = computed(() => getExecuteTaskStatistics(props.blockTask))


// __ Табы Группировки СЗ по АШМ/УШМ
const activeTabIndex = ref(0)

// __ Название заявок
const taskTitle = computed(() => {
    if (isUnionTask.value) {
        return BLOCK_UNION_TASK_NAME
    }
    return `${props.blockTask.position}. ${props.blockTask.order.client.short_name} №${props.blockTask.order.order_no_num}`
})

// __ Формируем объект выполнения
const blockLinesGroups = computed<IBlockTaskLinesGroupData[]>(() => {
    const title = `${props.blockTask.position}. ${props.blockTask.order.client.short_name} №${props.blockTask.order.order_no_num}`
    return groupTaskLinesForExecute(props.blockTask.block_lines, title)
})

const blockLinesGroup = computed(() => blockLinesGroups.value[activeTabIndex.value].subgroups)
const blockLines      = computed(() => {
    const result: IBlockTaskLine[] = []
    blockLinesGroups.value[activeTabIndex.value].subgroups.forEach(subgroup => {
        subgroup.lines.forEach(line => {
            result.push(line)
        })
    })
    return result
})

console.log('blockLinesGroups: ', blockLinesGroups.value)

// __ Проверка на то, что Заявка - объединенная
const isUnionTask = computed(() => props.blockTask.id === UNION_TASKS_ID)

// __ Поля данных
const fieldWidths: Record<string, string> = {
    check       : 'min-w-[25px] max-w-[25px]',
    space       : 'min-w-[10px] max-w-[10px]',
    position    : 'min-w-[30px] max-w-[30px]',
    name        : 'min-w-[300px] max-w-[300px]',
    amount      : 'min-w-[40px] max-w-[40px]',
    time        : 'min-w-[100px] max-w-[100px]',
    square      : 'min-w-[70px] max-w-[70px]',
    textile     : 'min-w-[250px] max-w-[250px]',
    kdb         : 'min-w-[70px] max-w-[70px]',
    timeLabel   : 'min-w-[100px] max-w-[100px]',
    manuf_line  : 'min-w-[40px] max-w-[40px]',
    false_reason: 'min-w-[174px] max-w-[174px]',
    order       : 'min-w-[300px] max-w-[300px]',
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

// --- Методы выделения на id ---
const flatVisibleIds = computed(() => {
    return blockLinesGroup.value.flatMap(subgroup => subgroup.lines.map(line => line.id))
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


// __ Показываем сообщение об ошибке
async function showError(error: string | string[] | null = null) {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'

    let renderError = ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    if (typeof error === 'string' && error.length > 0) {
        renderError = [error]
    } else if (Array.isArray(error) && error.length > 0) {
        renderError = error
    }

    modalInfoText.value = renderError
    await appModalAsyncMultilineTS.value!.show()
}


const TASK_READY_ADD_STATUS_MESSAGE = ['СЗ приостановлено для', 'добавления новых заданий']

// __ Выполнено
const completeSelected = async () => {

    // __ Получаем флаг готовности к добавлению новых СЗ и если все в процессе выполнения - выходим
    const isReady: boolean = await blockStore.readyGetBlockDay(splitDate(props.blockTask.action_at))
    console.log('isReady: ', isReady)

    if (isReady) {
        await showError(TASK_READY_ADD_STATUS_MESSAGE)
        return
    }

    const ids: number[] = []

    // __ Выбираем только задачи с нулевым статусом
    blockLines.value.forEach((t) => {
        if (selectedIds.value.has(t.id)) {
            if (isTaskLineReset(t)) {
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

    // __ Получаем флаг готовности к добавлению новых СЗ и если все в процессе выполнения - выходим
    const isReady: boolean = await blockStore.readyGetBlockDay(splitDate(props.blockTask.action_at))
    if (isReady) {
        await showError(TASK_READY_ADD_STATUS_MESSAGE)
        return
    }

    const ids: number[] = []

    // __ Выбираем только задачи с нулевым статусом
    blockLines.value.forEach((t) => {
        if (selectedIds.value.has(t.id)) {
            if (isTaskLineReset(t)) {
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

    // __ Получаем флаг готовности к добавлению новых СЗ и если все в процессе выполнения - выходим
    const isReady: boolean = await blockStore.readyGetBlockDay(splitDate(props.blockTask.action_at))
    if (isReady) {
        await showError(TASK_READY_ADD_STATUS_MESSAGE)
        return
    }

    const ids: number[] = []

    // __ Выбираем только задачи не с нулевым статусом
    blockLines.value.forEach((t) => {
        if (selectedIds.value.has(t.id)) {
            if (!isTaskLineReset(t)) {
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

    // __ Получаем флаг готовности к добавлению новых СЗ и если все в процессе выполнения - выходим
    const isReady: boolean = await blockStore.readyGetBlockDay(splitDate(props.blockTask.action_at))
    if (isReady) {
        await showError(TASK_READY_ADD_STATUS_MESSAGE)
        return
    }

    // __ Проверяем, что есть выделенные элементы
    if (selectedIds.value.size === 0) {
        return
    }

    // __ Проверяем, что это не объединение СЗ
    // if (props.blockTask.id === 0) {
    //     return
    // }

    // __ Берем первый элемент из выделенных
    const findElement = JSON.parse(
        JSON.stringify(blockLines.value.find((line) => line.id === Array.from(selectedIds.value)[0]))
    )

    // console.log('selected: ', findElement)

    // __ Проверяем, что элемент не завершен
    if (findElement && (findElement.finished_at || findElement.false_at || findElement.amount === 1)) {
        return
    }

    // __ Формируем название для модального окна
    dividerElement.value.name =
        findElement.block.name + findElement.amount.toString() + ' шт.'

    dividerElement.value.amount = findElement.amount

    const answer = await appRangeModalAsyncTS.value!.show() // показываем модалку и ждем ответ
    if (answer) {
        // __ Получаем диапазон + проверяем его (страховочка)
        const range = appRangeModalAsyncTS.value!.range
        if (!range || range.take === 0 || range.keep === 0) {
            return
        }

        emits('divideLine', props.blockTask.id, findElement.id, range)
        selectedIds.value.clear()
    }
}

const stopGlobalSelection = () => {
    isDragging.value = false
    stopAutoScroll()
}

// __ Изменение Производственной Линии
const changeManufLines = async (blockTask: IBlockTask) => {
    // __ Копируем объект, чтобы не мутировал оригинал
    taskCard.value = JSON.parse(JSON.stringify(blockTask))
    // __ Добавляем метаданные Заявки в каждую строку
    taskCard.value.block_lines.forEach(line => line.order_meta = `${taskCard.value.order.client.short_name} №${taskCard.value.order.order_no_str}`)


    // __ Показываем модальное окно обработки СЗ
    const answer = await manageTaskManufLines.value!.show()
    if (!answer) {
        return
    }

    // __ Получаем ссылки на панели
    const mutations                          = manageTaskManufLines.value!.mutations
    const setTablesData: IBlockLineSetData[] = mutations.map(line => ({ id: line.id, line: line.manuf_line, }))

    console.log('mutations: ', setTablesData)

    const result = await blockStore.taskLinesManufLineSet(setTablesData)
    if (checkCRUD(result)) {
        // __ Меняем глобальный стейт
        blockStore.setGlobalArrayChangeManufLines(setTablesData)
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'
        modalInfoText.value = 'Данные успешно обновлены'
        await appModalAsyncMultilineTS.value!.show()

    } else {
        await showError()
    }
}


// __ Печать
const printTask = () => {
    // router.push({name: 'manufacture.cell.block.task.print'})

    // __ Запоминаем данные для печати
    // const { globalBlockTaskPrintData } = storeToRefs(blockStore)
    // globalBlockTaskPrintData.value = blockLinesGroup.value
    // console.log(props.blockTask)

    localStorage.setItem(TASK_TO_PRINT_KEY, JSON.stringify(blockLinesGroup.value))
    localStorage.setItem(TASK_TO_PRINT_META_KEY, JSON.stringify({
        action_at  : props.blockTask.action_at,
        task_title : taskTitle.value,
        block_group: blockLinesGroups.value[activeTabIndex.value].groupName,
    }))

    // 1. Получаем объект с путем и параметрами
    const routeData = router.resolve({
        name: 'manufacture.cell.blocks.task.print',
        // query: { orderId: id }
    })

    // console.log('blockStore.globalBlockTaskPrintData: ', blockStore.globalBlockTaskPrintData)

    // 2. Открываем новое окно через стандартный JS
    window.open(routeData.href, '_blank')
}

// __ Смотрим на то, чтобы при переключении между СЗ не попали на вкладку (УПМ/УШМ) с нулевыми СЗ,
// __ которые не отображаются
const setActiveTabIndex = () => {
    if (!blockLinesGroups.value[activeTabIndex.value].hasData) {
        for (let i = 0; i < blockLinesGroups.value.length; i++) {
            if (blockLinesGroups.value[i].hasData) {
                activeTabIndex.value = i
            }
        }
    }
}


// __ Показываем КДБ
const blockDesignDocumentAsync = ref<InstanceType<typeof BlockDesignDocumentAsync> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией
const doc                      = ref<IBlockDocument | null>()

const showDocument = async (blockLine: IBlockTaskLine, id: number) => {
    doc.value = {
        id,
        kdb        : blockLine.block.collection.kdb?.kdb || '',
        file_path  : null,
        description: null,
    }
    await blockDesignDocumentAsync.value!.show()
    doc.value = null
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Collapse                     !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

const collapsedSubGroupsState   = ref(true)
const collapsedUnderGroupsState = ref(true)

// __ Видимость названий подгрупп
const showUndergroupNames = ref(true)

// --- Логика реактивного сворачивания групп (Тканей и Нарезки) ---
// __ Храним пары: [ИмяГруппы]: boolean (true - свернуто, false - развернуто)
const CONCAT_SYMBOLS = '__'

const collapseStates = ref<Record<string, boolean>>({})
let collapseStatesMem: Record<string, boolean>

// __ Следим за изменением активной группы таба и наполняем ключи, если их еще нет
watch(() => blockLinesGroup.value, (newSubgroups) => {
    if (!newSubgroups) return

    newSubgroups.forEach(subgroup => {
        // Инициализируем состояние для Подгруппы (Ткани), если её еще нет в памяти
        if (collapseStates.value[subgroup.subgroupName] === undefined) {
            collapseStates.value[subgroup.subgroupName] = true // по дефолту свернуто
        }
        collapseStatesMem = JSON.parse(JSON.stringify(collapseStates.value))
    })
}, { immediate: true, deep: true })

// __ Переключаем Свернутость (Универсальный метод для переключения флага (для вызова из шаблона))
const toggleCollapse = (key: string) => {
    if (collapseStates.value[key] !== undefined) {
        collapseStates.value[key] = !collapseStates.value[key]
    }
}

// __ Раскрываем / Сворачиваем ПС
const toggleSubGroups = () => {
    collapsedSubGroupsState.value = !collapsedSubGroupsState.value
    Object.keys(collapseStates.value).forEach(k => {
        if (!k.includes(CONCAT_SYMBOLS)) collapseStates.value[k] = collapsedSubGroupsState.value
    })
}

// // __ Раскрываем / Сворачиваем Крой
// const toggleUnderGroups = () => {
//     // __ Если скрыты заголовки Кроя, ничего не делаем
//     if (!showUndergroupNames.value) {
//         return
//     }
//     collapsedUnderGroupsState.value = !collapsedUnderGroupsState.value
//     Object.keys(collapseStates.value).forEach(k => {
//         if (k.includes(CONCAT_SYMBOLS)) collapseStates.value[k] = collapsedUnderGroupsState.value
//     })
// }

// __ Скрываем размеры Раскроя
const toggleUnderGroupTitleVisibility = () => {
    if (showUndergroupNames.value) {
        // __ Переключение в "Показать". Запоминаем состояние и все дочернее отображаем
        collapseStatesMem = JSON.parse(JSON.stringify(collapseStates.value))
        Object.keys(collapseStates.value).forEach(k => {
            if (k.includes(CONCAT_SYMBOLS)) collapseStates.value[k] = false
        })
    } else {
        // __ Переключение в "Показать". Запоминаем состояние и все дочернее восстанавливаем
        Object.keys(collapseStatesMem).forEach(k => {
            if (k.includes(CONCAT_SYMBOLS)) collapseStates.value[k] = collapseStatesMem[k]
        })
        // collapseStates.value = JSON.parse(JSON.stringify(collapseStatesMem))
    }

    showUndergroupNames.value = !showUndergroupNames.value
}

// __ Добавить в выделение все элементы ПС
const selectSubgroupItems = async (subgroup: IBlockTaskLinesSubgroup) => {
    modalInfoType.value = 'primary'
    modalInfoMode.value = 'confirm'
    modalInfoText.value = [
        `Выделить все элементы в коллекции Блоков: `,
        `${subgroup.subgroupName}?`
    ]

    const answer = await appModalAsyncMultilineTS.value!.show() // показываем модалку и ждем ответ
    if (answer) {
        subgroup.lines.forEach(l => selectedIds.value.add(l.id))
    }
}


// --- Жизненный цикл ---
onMounted(async () => {
    window.addEventListener('mouseup', stopGlobalSelection)
    window.addEventListener('mousemove', handleMouseMove)
    window.addEventListener('click', () => (showMenu.value = false))

    setActiveTabIndex()
    // activeTabIndex.value = 0

    localStorage.removeItem(TASK_TO_PRINT_KEY)      // __ Очищаем данные для печати
    localStorage.removeItem(TASK_TO_PRINT_META_KEY) // __ Очищаем данные для печати
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

.wrapper {
    /* Оставляем расчет высоты, он важен для overflow-y-auto внутри */
    height: calc(100vh - var(--header-height) - var(--footer-height) - 140px);

    /* Убираем фиксированный width и ставим это: */
    width: 100%;
    /*width: calc(100vw - var(--sidebar-width) - 15px);*/
    min-width: 0; /* Важно для flex-контейнеров, чтобы не распирало контентом */

    /*    display: flex;
        flex-direction: column;*/

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
