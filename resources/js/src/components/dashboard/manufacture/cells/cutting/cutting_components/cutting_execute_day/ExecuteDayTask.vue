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

                <template v-for="(cuttingLinesGroup, idx) of cuttingLinesGroups" :key="idx">

                    <template v-if="cuttingLinesGroup.hasData">
                        <!-- __ Группа сортировки СЗ по АШМ/УШМ -->
                        <AppLabelMultiLineTS
                            :height="MENU_HEIGHT"
                            :text="cuttingLinesGroup.groupName"
                            :type="activeTabIndex === idx ? 'primary' : cuttingLinesGroup.groupType"
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
                    type="dark"
                    width="w-[50px]"
                    @click="toggleUnderGroupTitleVisibility"
                />

                <!-- __ Свернуть/Развернуть ПС -->
                <AppLabelMultiLineTS
                    :height="MENU_HEIGHT_MULTILINE"
                    :text="collapsedSubGroupsState ? ['Раскрыть', '▼ Ткань'] : ['Свернуть', '▲ Ткань']"
                    :type="'primary'"
                    :width="MENU_WIDTH"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text-size="mini"
                    title="Свернуть/Развернуть группу Ткани"
                    @click="toggleSubGroups"
                />

                <!-- __ Свернуть/Развернуть Крой '▲' : '▼' -->
                <AppLabelMultiLineTS
                    :disabled="showUndergroupNames"
                    :height="MENU_HEIGHT_MULTILINE"
                    :text="collapsedUnderGroupsState ? ['Раскрыть', '▼ Крой'] : ['Свернуть', '▲ Крой']"
                    :type="!showUndergroupNames ? 'dark' : 'indigo'"
                    :width="MENU_WIDTH"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text-size="mini"
                    title="Свернуть/Развернуть группу Кроя"
                    @click="toggleUnderGroups"
                />

                <!-- __ Слои -->
                <AppLabelTS
                    :height="MENU_HEIGHT"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text="📐"
                    text-size="huge"
                    type="dark"
                    width="w-[50px]"
                    @click="console.log('layers')"
                />

                <!-- __ Печать -->
                <AppLabelTS
                    :height="MENU_HEIGHT"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text="📄"
                    text-size="huge"
                    type="dark"
                    width="w-[50px]"
                    @click="printTask"
                />

                <!-- __ Комментарий -->
                <template v-if="cuttingTask.comment">
                    <AppLabelTS
                        :height="MENU_HEIGHT"
                        :text="cuttingTask.comment ?? ''"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text-size="mini"
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

                    <!-- __ Изменить Стол -->
                    <AppLabelTS
                        :height="MENU_HEIGHT"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text="⚙️"
                        text-size="huge"
                        title="Переместить элемент на другой Стол"
                        type="dark"
                        width="w-[50px]"
                        @click="changeTable"
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

            <div v-for="(subgroup, sgIndex) of cuttingLinesGroup" :key="sgIndex">

                <template v-if="subgroup.hasData">
                    <!-- __ Название ПС -->
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

                        <div v-for="undergroup of subgroup.undergroups" :key="undergroup.undergroupName">

                            <!-- __ Название Кроя -->
                            <div v-if="showUndergroupNames" class="ml-2">
                                <ExecuteDayTaskUnderGroup
                                    :collapsed="collapseStates[`${subgroup.subgroupName}${CONCAT_SYMBOLS}${undergroup.undergroupName}`]"
                                    :undergroup
                                    @toggle-collapse="toggleCollapse(`${subgroup.subgroupName}${CONCAT_SYMBOLS}${undergroup.undergroupName}`)"
                                    @select-undergroup-items="selectUndergroupItems(undergroup)"
                                />
                            </div>

                            <div
                                v-if="!collapseStates[`${subgroup.subgroupName}${CONCAT_SYMBOLS}${undergroup.undergroupName}`]"
                                :class="[!collapseStates[`${subgroup.subgroupName}${CONCAT_SYMBOLS}${undergroup.undergroupName}`] ? 'mb-1' : '']"
                            >

                                <div
                                    v-for="(cuttingLine, index) of undergroup.lines"
                                    :key="cuttingLine.id"
                                    :class="[
                                        selectedIds.has(cuttingLine.id) ? 'bg-slate-300 text-slate-900' : 'hover:bg-gray-50',
                                        cuttingLine.completed ? '' : '',
                                    ]"
                                    :data-task-id="cuttingLine.id"
                                    class="my-[-1px] flex items-center px-6 border-b border-gray-100 transition-colors relative"
                                    @mousedown="startSelectionById(cuttingLine.id, $event)"
                                    @mouseenter="updateSelectionById(cuttingLine.id, $event)"
                                >
                                    <!-- __ Строка СЗ -->
                                    <ExecuteDayTaskLine
                                        :cutting-line="cuttingLine"
                                        :field-widths="fieldWidths"
                                        :index="index + 1"
                                        :ordering="'index'"
                                        @dblclick="showLineInfo(cuttingLine)"
                                        @show-document="showDocument(cuttingLine, $event)"
                                    />

                                    <!--class="absolute inset-y-0 left-0 w-1 bg-slate-500 pointer-events-none"-->
                                    <div
                                        v-if="selectedIds.has(cuttingLine.id)"
                                        class="absolute inset-0 border-l-4 border-r-4 border-slate-500 pointer-events-none animate-select"
                                    ></div>
                                </div>

                            </div>

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

    <!-- __ Смена Стола -->
    <ManageTaskTables
        ref="manageTaskTables"
        :mode="modalModeTable"
        :task="taskCardTable"
        :text="modalTextTable"
        :type="modalTypeTable"
    />

    <!-- __ Просмотр PDF в модальном режиме -->
    <TextileDesignDocumentAsync
        ref="textileDesignDocumentAsync"
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
    ICuttingTask,
    ICuttingTaskLine,
    ICuttingTaskOrderLine, ICuttingLineTableSetData, ITextileDocument, ICuttingTaskLinesUnderGroup, ICuttingTaskLinesSubgroup
} from '@/types'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import { TASK_TO_PRINT_KEY, TASK_TO_PRINT_META_KEY } from '@/app/constants/common.ts'
import { CUTTING_TASK_DRAFT, CUTTING_UNION_TASK_NAME } from '@/app/constants/cutting.ts'

import {
    getCoverSizeString,
    getExecuteTaskStatistics,
    getCuttingTaskModelCoverName,
    groupTaskLinesForExecute,
    isTaskLineReset,
    // groupTaskLinesForExecuteForUnion,
} from '@/app/helpers/manufacture/helpers_cutting.ts'
import { formatTimeWithLeadingZeros, splitDate } from '@/app/helpers/helpers_date'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import { getKDCH } from '@/app/helpers/manufacture/helpers_textile.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import ExecuteDayFalseReason from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute_day/ExecuteDayFalseReason.vue'
import ExecuteDayTaskLine from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute_day/ExecuteDayTaskLine.vue'
import OrderItemInfo from '@/components/dashboard/manufacture/cells/cutting/cutting_components/common/OrderItemInfo.vue'
import ExecuteDayTaskLineHeader from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute_day/ExecuteDayTaskLineHeader.vue'
import AppRangeModalAsyncTS from '@/components/ui/modals/AppRangeModalAsyncTS.vue'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import ManageTaskTables from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskTables.vue'
import TextileDesignDocumentAsync from '@/components/dashboard/manufacture/shared/textile_design/TextileDesignDocumentAsync.vue'
import ExecuteDayTaskSubGroup from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute_day/ExecuteDayTaskSubGroup.vue'
import ExecuteDayTaskUnderGroup from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute_day/ExecuteDayTaskUnderGroup.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'


interface IProps {
    cuttingTask: ICuttingTask
    isRunning: boolean | null
}

const props = defineProps<IProps>()

const emits = defineEmits<{
    (e: 'setFinishStatus', payload: number[]): void
    (e: 'setFalseStatus', payload: number[], falseReason: string): void
    (e: 'resetStatus', payload: number[]): void
    (e: 'divideLine', taskId: number, lineId: number, divideAmount: { take: number; keep: number }): void
}>()

const router       = useRouter()
const cuttingStore = useCuttingStore()

// console.log('props.cuttingTask: ', props.cuttingTask)

const UNION_TASKS_ID = 0

const MENU_WIDTH            = 'w-[85px]'
const MENU_HEIGHT           = 'h-[50px]'
const MENU_HEIGHT_MULTILINE = 'h-[25px]'

// __ Тип для модального окна информации о записи в Заявке
const orderLine     = ref<ICuttingTaskOrderLine | null>(null)
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

// __ Тип для Каротчки и Изменения стола
const modalTypeTable   = ref<IColorTypes>('primary')
const modalTextTable   = ref<string>('')
const modalModeTable   = ref<'inform' | 'confirm'>('inform')
const manageTaskTables = ref<InstanceType<typeof ManageTaskTables> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Карточка СЗ
const taskCardTable = ref<ICuttingTask>(CUTTING_TASK_DRAFT)

const statistics = computed(() => getExecuteTaskStatistics(props.cuttingTask))


// __ Табы Группировки СЗ по АШМ/УШМ
const activeTabIndex = ref(0)

// __ Название заявок
const taskTitle = computed(() => {
    if (isUnionTask.value) {
        return CUTTING_UNION_TASK_NAME
    }
    return `${props.cuttingTask.position}. ${props.cuttingTask.order.client.short_name} №${props.cuttingTask.order.order_no_num}`
})

// __ Получаем название подгруппы
// const getSubgroupTitle = (subgroup:  ICuttingTaskLinesSubgroup) => {
//     return `${subgroup.subgroupOrderTitle}: ${subgroup.subgroupName} - Всего: ${subgroup.amount.total} / Выполнено: ${subgroup.amount.done}`
// }

// __ Формируем объект выполнения
const cuttingLinesGroups = computed(() => {
    // if (isUnionTask.value) {
    // if (props.cuttingTask.id === UNION_TASKS_ID) {
    //     return groupTaskLinesForExecuteForUnion(props.cuttingTask.cutting_lines)
    // }
    const title = `${props.cuttingTask.position}. ${props.cuttingTask.order.client.short_name} №${props.cuttingTask.order.order_no_num}`
    return groupTaskLinesForExecute(props.cuttingTask.cutting_lines, title)
})

// const cuttingLinesGroup = ref(cuttingLinesGroups.value[activeTabIndex.value].subgroups)
const cuttingLinesGroup = computed(() => cuttingLinesGroups.value[activeTabIndex.value].subgroups)
const cuttingLines      = computed(() => {
    const result: ICuttingTaskLine[] = []
    cuttingLinesGroups.value[activeTabIndex.value].subgroups.forEach(subgroup => {
        subgroup.undergroups.forEach(undergroup => {
            undergroup.lines.forEach(line => {
                result.push(line)
            })
        })

        // subgroup.lines.forEach(line => {
        //     result.push(line)
        // })
    })
    return result
})


console.log('cuttingLinesGroups: ', cuttingLinesGroups.value)

// const getCuttingLines = () => {
// const linesGrouped = groupTaskLinesForExecute(props.cuttingTask.cutting_lines)
// console.log('linesGrouped: ', linesGrouped)

//
//     cuttingLines.value = props.cuttingTask.cutting_lines
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


// __ Проверка на то, что Заявка - объединенная
const isUnionTask = computed(() => props.cuttingTask.id === UNION_TASKS_ID)


// __ Показать информацию о записи
const showLineInfo = async (cuttingLine: ICuttingTaskLine) => {
    orderLine.value = cuttingLine.order_line
    await orderItemInfo.value!.show() // показываем модалку и ждем ответ
}

// __ Поля данных
const fieldWidths: Record<string, string> = {
    check         : 'min-w-[25px] max-w-[25px]',
    space         : 'min-w-[10px] max-w-[10px]',
    position      : 'min-w-[30px] max-w-[30px]',
    size          : 'min-w-[80px] max-w-[80px]',
    coverName     : 'min-w-[300px] max-w-[300px]',
    amount        : 'min-w-[40px] max-w-[40px]',
    time          : 'min-w-[70px] max-w-[70px]',
    machine       : 'min-w-[35px] max-w-[35px]',
    textile       : 'min-w-[250px] max-w-[250px]',
    tkch          : 'min-w-[70px] max-w-[70px]',
    kant          : 'min-w-[90px] max-w-[90px]',
    kdch          : 'min-w-[70px] max-w-[70px]',
    composition   : 'min-w-[70px] max-w-[70px]',
    describe_1    : 'min-w-[70px] max-w-[70px]',
    describe_2    : 'min-w-[70px] max-w-[70px]',
    describe_3    : 'min-w-[70px] max-w-[70px]',
    timeLabel     : 'min-w-[100px] max-w-[100px]',
    reason        : 'min-w-[300px] max-w-[300px]',
    table         : 'min-w-[25px] max-w-[25px]',
    detail        : 'min-w-[25px] max-w-[25px]',
    false_reason  : 'min-w-[110px] max-w-[110px]',
    cut           : 'min-w-[60px] max-w-[60px]',
    angle         : 'min-w-[50px] max-w-[50px]',
    layers        : 'min-w-[50px] max-w-[50px]',
    expense       : 'min-w-[50px] max-w-[50px]',
    fabric_roll   : 'min-w-[50px] max-w-[50px]',
    fabric_roll_at: 'min-w-[100px] max-w-[100px]',
    defects       : 'min-w-[50px] max-w-[50px]',
    defects_reason: 'min-w-[110px] max-w-[110px]',
    order         : 'min-w-[200px] max-w-[200px]',
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
    return cuttingLinesGroup.value.flatMap(subgroup =>
        subgroup.undergroups.flatMap(undergroup =>
            undergroup.lines.map(line => line.id)
        )
    )
})
// const flatVisibleIds = computed(() => {
//     return cuttingLinesGroup.value.flatMap(subgroup => subgroup.lines.map(line => line.id))
// })

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
//         if (selectedIds.value.has(cuttingLines.value[index].id)) {
//             selectedIds.value.delete(cuttingLines.value[index].id)
//         } else {
//             selectedIds.value.add(cuttingLines.value[index].id)
//         }
//     } else {
//         selectedIds.value.clear()
//         selectedIds.value.add(cuttingLines.value[index].id)
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
//         selectedIds.value.add(cuttingLines.value[i].id)
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
    const isReady: boolean = await cuttingStore.readyGetCuttingDay(splitDate(props.cuttingTask.action_at))
    console.log('isReady: ', isReady)

    if (isReady) {
        await showError(TASK_READY_ADD_STATUS_MESSAGE)
        return
    }

    const ids: number[] = []

    // __ Выбираем только задачи с нулевым статусом
    cuttingLines.value.forEach((t) => {
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
    const isReady: boolean = await cuttingStore.readyGetCuttingDay(splitDate(props.cuttingTask.action_at))
    if (isReady) {
        await showError(TASK_READY_ADD_STATUS_MESSAGE)
        return
    }

    const ids: number[] = []

    // __ Выбираем только задачи с нулевым статусом
    cuttingLines.value.forEach((t) => {
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
    const isReady: boolean = await cuttingStore.readyGetCuttingDay(splitDate(props.cuttingTask.action_at))
    if (isReady) {
        await showError(TASK_READY_ADD_STATUS_MESSAGE)
        return
    }

    const ids: number[] = []

    // __ Выбираем только задачи не с нулевым статусом
    cuttingLines.value.forEach((t) => {
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
    const isReady: boolean = await cuttingStore.readyGetCuttingDay(splitDate(props.cuttingTask.action_at))
    if (isReady) {
        await showError(TASK_READY_ADD_STATUS_MESSAGE)
        return
    }

    // __ Проверяем, что есть выделенные элементы
    if (selectedIds.value.size === 0) {
        return
    }

    // __ Проверяем, что это не объединение СЗ
    // if (props.cuttingTask.id === 0) {
    //     return
    // }

    // __ Берем первый элемент из выделенных
    const findElement = JSON.parse(
        JSON.stringify(cuttingLines.value.find((line) => line.id === Array.from(selectedIds.value)[0]))
    )

    // console.log('selected: ', findElement)

    // __ Проверяем, что элемент не завершен
    if (findElement && (findElement.finished_at || findElement.false_at || findElement.amount === 1)) {
        return
    }

    // __ Формируем название для модального окна
    const modelCover = getCuttingTaskModelCoverName(findElement.order_line.model)

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

        emits('divideLine', props.cuttingTask.id, findElement.id, range)
        selectedIds.value.clear()
    }
}

const stopGlobalSelection = () => {
    isDragging.value = false
    stopAutoScroll()
}


// __ Изменить Стол
const changeTable = async () => {
    // __ Копируем объект, чтобы не мутировал оригинал
    taskCardTable.value = JSON.parse(JSON.stringify(props.cuttingTask))
    // __ Добавляем метаданные Заявки в каждую строку
    taskCardTable.value.cutting_lines.forEach(line => line.order_meta = `${taskCardTable.value.order.client.short_name} №${taskCardTable.value.order.order_no_str}`)


    // __ Показываем модальное окно обработки СЗ
    const answer = await manageTaskTables.value!.show()
    if (!answer) {
        return
    }

    // __ Получаем ссылки на панели
    const mutations                                 = manageTaskTables.value!.mutations
    const setTablesData: ICuttingLineTableSetData[] = mutations.map(line => ({ id: line.id, table: line.table }))

    console.log('mutations: ', setTablesData)

    const result = await cuttingStore.taskLinesTableSet(setTablesData)
    if (checkCRUD(result)) {
        // __ Меняем глобальный стейт
        cuttingStore.setGlobalArrayChangeTables(setTablesData)
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
    // router.push({name: 'manufacture.cell.cutting.task.print'})

    // __ Запоминаем данные для печати
    // const { globalCuttingTaskPrintData } = storeToRefs(cuttingStore)
    // globalCuttingTaskPrintData.value = cuttingLinesGroup.value
    // console.log(props.cuttingTask)

    localStorage.setItem(TASK_TO_PRINT_KEY, JSON.stringify(cuttingLinesGroup.value))
    localStorage.setItem(TASK_TO_PRINT_META_KEY, JSON.stringify({
        action_at    : props.cuttingTask.action_at,
        task_title   : taskTitle.value,
        cutting_group: cuttingLinesGroups.value[activeTabIndex.value].groupName,
    }))

    // 1. Получаем объект с путем и параметрами
    const routeData = router.resolve({
        name: 'manufacture.cell.cutting.task.print',
        // query: { orderId: id }
    })

    // console.log('cuttingStore.globalCuttingTaskPrintData: ', cuttingStore.globalCuttingTaskPrintData)

    // 2. Открываем новое окно через стандартный JS
    window.open(routeData.href, '_blank')
}

// __ Смотрим на то, чтобы при переключении между СЗ не попали на вкладку (УПМ/УШМ) с нулевыми СЗ,
// __ которые не отображаются
const setActiveTabIndex = () => {
    if (!cuttingLinesGroups.value[activeTabIndex.value].hasData) {
        for (let i = 0; i < cuttingLinesGroups.value.length; i++) {
            if (cuttingLinesGroups.value[i].hasData) {
                activeTabIndex.value = i
            }
        }
    }
}


// __ Показываем КДЧ
const textileDesignDocumentAsync = ref<InstanceType<typeof TextileDesignDocumentAsync> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией
const doc                        = ref<ITextileDocument | null>()

const showDocument = async (cuttingLine: ICuttingTaskLine, id: number) => {
    doc.value = {
        id,
        kdch       : getKDCH(cuttingLine),
        file_path  : null,
        description: null,
    }
    await textileDesignDocumentAsync.value!.show()
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

// Следим за изменением активной группы таба и наполняем ключи, если их еще нет
watch(() => cuttingLinesGroup.value, (newSubgroups) => {
    if (!newSubgroups) return

    newSubgroups.forEach(subgroup => {
        // Инициализируем состояние для Подгруппы (Ткани), если её еще нет в памяти
        if (collapseStates.value[subgroup.subgroupName] === undefined) {
            collapseStates.value[subgroup.subgroupName] = true // по дефолту свернуто
        }

        // Инициализируем состояние для Субподгруппы (Размеры кроя x)
        subgroup.undergroups.forEach(undergroup => {
            // Ключ делаем уникальным (ИмяТкани + ИмяКроя), чтобы размеры не пересекались между тканями
            const undergroupKey = `${subgroup.subgroupName}${CONCAT_SYMBOLS}${undergroup.undergroupName}`
            if (collapseStates.value[undergroupKey] === undefined) {
                collapseStates.value[undergroupKey] = true // по дефолту свернуто
            }
        })

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

// __ Раскрываем / Сворачиваем Крой
const toggleUnderGroups = () => {
    // __ Если скрыты заголовки Кроя, ничего не делаем
    if (!showUndergroupNames.value) {
        return
    }
    collapsedUnderGroupsState.value = !collapsedUnderGroupsState.value
    Object.keys(collapseStates.value).forEach(k => {
        if (k.includes(CONCAT_SYMBOLS)) collapseStates.value[k] = collapsedUnderGroupsState.value
    })
}

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

// __ Добавить в выделение все элементы Кроя
const selectSubgroupItems = async (subgroup: ICuttingTaskLinesSubgroup) => {
    modalInfoType.value = 'primary'
    modalInfoMode.value = 'confirm'
    modalInfoText.value = [
        `Выделить все элементы в группе Ткани: `,
        `${subgroup.subgroupName}?`
    ]

    const answer = await appModalAsyncMultilineTS.value!.show() // показываем модалку и ждем ответ
    if (answer) {
        subgroup.undergroups.forEach(undergroup => {
            undergroup.lines.forEach(l => selectedIds.value.add(l.id))
        })
    }
}

// __ Добавить в выделение все элементы Кроя
const selectUndergroupItems = async (undergroup: ICuttingTaskLinesUnderGroup) => {
    modalInfoType.value = 'indigo'
    modalInfoMode.value = 'confirm'
    modalInfoText.value = `Выделить все элементы в группе Кроя с размером: ${undergroup.undergroupName}?`

    const answer = await appModalAsyncMultilineTS.value!.show() // показываем модалку и ждем ответ
    if (answer) {
        undergroup.lines.forEach(l => selectedIds.value.add(l.id))
    }
}


watch(
    () => cuttingLinesGroups.value,
    () => {
        setActiveTabIndex()
        // if (!cuttingLinesGroups.value[activeTabIndex.value].hasData) {
        //     for (let i = 0; i < cuttingLinesGroups.value.length; i++) {
        //         if (cuttingLinesGroups.value[i].hasData) {
        //             activeTabIndex.value = i
        //         }
        //     }
        // }
        selectedIds.value.clear() // __ Очистка выделения
    },
    { deep: true, immediate: true }
)


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
