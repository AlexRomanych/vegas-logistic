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

                <!-- __ Видимость заголовков -->
                <AppLabelTS
                    :height="MENU_HEIGHT"
                    :text="showGroups ? '🔓' : '🔒'"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text-size="huge"
                    title="Скрыть/Показать название размеров Кроя"
                    type="dark"
                    width="w-[50px]"
                    @click="toggleShowGroups"
                />

                <!-- __ Свернуть/Развернуть Группы Сортировки -->
                <AppLabelMultiLineTS
                    :height="MENU_HEIGHT_MULTILINE"
                    :text="collapsedGroupsState ? ['Раскрыть', '▼ Группу'] : ['Свернуть', '▲ Группу']"
                    :type="'primary'"
                    :width="MENU_WIDTH"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text-size="mini"
                    title="Свернуть/Развернуть Коллекцию Блоков"
                    @click="toggleGroups"
                />

                <!-- __ Журнал -->
                <AppLabelTS
                    :height="MENU_HEIGHT"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text="📖"
                    text-size="huge"
                    title="Журнал Событий"
                    type="dark"
                    width="w-[50px]"
                    @click="showEvents"
                />

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

                <!--&lt;!&ndash; __ Оптимизация по Приоритету &ndash;&gt;-->
                <!--<AppLabelTS-->
                <!--    :height="MENU_HEIGHT"-->
                <!--    :type="optimizationType === OPTIMIZE_BY_PRIORITY ? 'warning' : 'dark'"-->
                <!--    align="center"-->
                <!--    class="menu-button"-->
                <!--    rounded="4"-->
                <!--    text="⬇️"-->
                <!--    text-size="huge"-->
                <!--    title="Оптимизация по Приоритету"-->
                <!--    width="w-[50px]"-->
                <!--    @click="optimizationType = OPTIMIZE_BY_PRIORITY"-->
                <!--/>-->

                <!--&lt;!&ndash; __ Оптимизация по Переналадке &ndash;&gt;-->
                <!--<template v-if="uniqueIds.length <= MAX_BLOCK_COLLECTIONS_OPTIMIZED">-->
                <!--    <AppLabelTS-->
                <!--        :height="MENU_HEIGHT"-->
                <!--        :type="optimizationType === OPTIMIZE_BY_TUNING_TIME ? 'primary' : 'dark'"-->
                <!--        align="center"-->
                <!--        class="menu-button"-->
                <!--        rounded="4"-->
                <!--        text="⌛"-->
                <!--        text-size="huge"-->
                <!--        title="Оптимизация по времени Переналадки"-->
                <!--        width="w-[50px]"-->
                <!--        @click="optimizeByTuningTime"-->
                <!--    />-->
                <!--</template>-->

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
                <template v-if="assemblyTask.comment">
                    <AppLabelTS
                        :height="MENU_HEIGHT"
                        :text="assemblyTask.comment ?? ''"
                        align="center"
                        class="menu-button"
                        rounded="4"
                        text-size="mini"
                        title="Комментарий к Сменному Заданию"
                        type="indigo"
                        width="min-w-[250px]"
                    />
                </template>

                <!--<div v-if="isRunning" class="flex">-->
                <!--&lt;!&ndash; __ Прогресс &ndash;&gt;-->
                <!--<AppProgressBar-->
                <!--    :progress="(manufLinesGroup.time.done / manufLinesGroup.time.total) * 100"-->
                <!--    :text="`${formatTimeWithLeadingZeros(manufLinesGroup.time.done, 'hour')} / ${formatTimeWithLeadingZeros(manufLinesGroup.time.total, 'hour')}`"-->
                <!--    height="h-[50px]"-->
                <!--    text-size="mini"-->
                <!--    width="w-[200px]"-->
                <!--/>-->

                <!-- __ Прогресс -->
                <!--<AppProgressBar-->
                <!--    :progress="(statistics.time.finished / statistics.time.total) * 100"-->
                <!--    :text="`${formatTimeWithLeadingZeros(statistics.time.finished, 'hour')} / ${formatTimeWithLeadingZeros(statistics.time.total, 'hour')}`"-->
                <!--    height="h-[50px]"-->
                <!--    text-size="mini"-->
                <!--    width="w-[200px]"-->
                <!--/>-->

                <!-- __ Изменить Линию Сборки-->
                <AppLabelTS
                    :height="MENU_HEIGHT"
                    :type="assemblyTask.id === UNION_TASKS_ID ? 'danger' : 'dark'"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text="⚙️"
                    text-size="huge"
                    title="Переместить элемент на другую Линию"
                    width="w-[50px]"
                    @click="changeAssemblyLines"
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

        <!-- __ Заголовок для Линий + Материалы -->
        <div class="ml-[10px]">
            <ManipulateDayTaskLineHeader
                :field-widths="fieldWidths"
                :materials="data.materials"
                :show-order-title="data.task.id === UNION_TASKS_ID"
                @toggle-all="toggleGroups"
            />
        </div>

        <!-- __ Блок Самих данных -->
        <div
            ref="scrollContainer"
            class="py-2 flex-1 overflow-y-auto border border-slate-200 rounded-xl bg-white select-none custom-scroll relative shadow-sm"
        >
            <div v-for="(group, gIndex) of data.groups" :key="gIndex">

                <!-- __ Группы сортировки -->
                <div
                    v-if="showGroups"
                    class="ml-2"
                >
                    <ManipulateDayTaskGroup
                        :collapsed="collapsedStates[group.group.name]"
                        :group
                        @toggle-collapse="toggleGroup(group.group.name)"
                        @select-group-items="selectGroupItems(group)"
                    />
                </div>

                <!--:class="[!collapseStates[subgroup.subgroupName] ? 'mb-2' : '']"-->
                <div
                    v-if="!collapsedStates[group.group.name]"
                    class="ml-5"
                >

                    <!-- !!! С фиксированной высотой строки СЗ !!! -->
                    <!--class="h-[35px] flex items-center px-6 border-b border-gray-100 transition-colors relative"-->

                    <div
                        v-for="(record, index) of group.group_lines"
                        :key="record.order_line.id"
                        :class="[
                                selectedIds.has(record.order_line.id) ? 'bg-slate-300 text-slate-900' : 'hover:bg-gray-50',
                            ]"
                        :data-task-id="record.order_line.id"
                        class="my-[-1px] flex items-center px-6 border-b border-gray-100 transition-colors relative"
                        @mousedown="startSelectionById(record.order_line.id, $event)"
                        @mouseenter="updateSelectionById(record.order_line.id, $event)"
                    >
                        <!-- __ Строка СЗ -->
                        <!--@show-document="showDocument(blockLine, $event)"-->
                        <!--@change-description="changeDescription(blockLine, $event)"-->
                        <ManipulateDayTaskLine
                            :field-widths="fieldWidths"
                            :group-line="record"
                            :index="index + 1"
                            :ordering="'index'"
                            :show-order-title="data.task.id === UNION_TASKS_ID"
                        />

                        <!--class="absolute inset-y-0 left-0 w-1 bg-slate-500 pointer-events-none"-->
                        <div
                            v-if="selectedIds.has(record.order_line.id)"
                            class="absolute inset-0 border-l-4 border-r-4 border-slate-500 pointer-events-none animate-select"
                        ></div>
                    </div>

                </div>
            </div>

            <TheDividerLineTS/>

            <!-- __ Итого -->
            <!--<div class="ml-2">-->
            <!--    <ExecuteDayTaskSubGroupCommon-->
            <!--        :subgroup="commonSubGroup"-->
            <!--    />-->
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
                <button
                    class="w-full flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-stone-600 hover:text-white transition-colors"
                    @click="handleMenuAction('cancel')"
                >
                    <span class="mr-3 text-lg">↺</span> Отменить
                </button>
            </div>
        </Transition>
    </Teleport>

    <div class="mt-1 text-[11px] font-bold text-slate-400 flex gap-6 px-2">
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


    <!-- __ Модальное окно для добавления причины не выполнения -->
    <ManipulateDayFalseReason
        ref="manipulateDayFalseReason"
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
        width="w-[800px]"
    />

    <!-- __ Смена Производственной линии -->
    <!--<ManageTaskManufLines-->
    <!--    ref="manageTaskManufLines"-->
    <!--    :mode="modalMode"-->
    <!--    :task="taskCard"-->
    <!--    :text="modalText"-->
    <!--    :type="modalType"-->
    <!--/>-->

    <!-- __ Просмотр PDF в модальном режиме -->
    <!--<BlockDesignDocumentAsync-->
    <!--    ref="blockDesignDocumentAsync"-->
    <!--    :doc="doc"-->
    <!--    ok-word="Понятно"-->
    <!--    type="primary"-->
    <!--/>-->

     <!--__ Журнал Событий -->
    <ManageEventsAsync
        ref="manageEventsAsync"
        :cell="CELL_EVENT_ASSEMBLY"
        :day-id="day.id"
    />

</template>

<script lang="ts" setup>
import { ref, computed, nextTick, onUnmounted, onMounted, onBeforeUnmount } from 'vue'

import type {
    IColorTypes,
    IAssemblyModelManufactureGroup,
    IAssemblyTask,
    IAssemblyTaskOrderLine,
    IDividerItem, IMatrixManufactureGroup,
    IMatrixManufactureTask, IAssemblyDay
} from '@/types'

import { CELL_EVENT_ASSEMBLY } from '@/app/constants/cell_events.ts'
import { TASK_TO_PRINT_KEY, TASK_TO_PRINT_META_KEY } from '@/app/constants/common.ts'
import { ASSEMBLY_UNION_TASK_NAME } from '@/app/constants/assembly.ts'

import { formatTimeWithLeadingZeros, splitDate } from '@/app/helpers/helpers_date'
import { isTaskLineDone, isTaskLineFalse, isTaskLineReset } from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'
import AppRangeModalAsyncTS from '@/components/ui/modals/AppRangeModalAsyncTS.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'

import OrderItemInfo from '@/components/dashboard/manufacture/cells/assembly/common/OrderItemInfo.vue'
import ManipulateDayFalseReason from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulateDayFalseReason.vue'
import ManageTaskManufLines from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskManufLines.vue'
import ManageEventsAsync from '@/components/dashboard/manufacture/events/ManageEventsAsync.vue'

import ManipulateDayTaskLineHeader from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulateDayTaskLineHeader.vue'
import ManipulateDayTaskGroup from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulateDayTaskGroup.vue'
import ManipulateDayTaskLine from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulateDayTaskLine.vue'



interface IProps {
    data: IMatrixManufactureTask
    day: IAssemblyDay   // __ Прокидываем для Журнала Событий, чтобы был виден в каждом СЗ
}

const props = defineProps<IProps>()

const emits = defineEmits<{
    (e: 'setFinishStatus', payload: number[]): void
    (e: 'setFalseStatus', payload: number[], falseReason: string): void
    (e: 'resetStatus', payload: number[]): void
    (e: 'divideLine', taskId: number, lineId: number, divideAmount: { take: number; keep: number }): void
}>()


const assemblyTask = computed<IAssemblyTask>(() => props.data.task)

const UNION_TASKS_ID = 0

const MENU_WIDTH            = 'w-[85px]'
const MENU_HEIGHT           = 'h-[50px]'
const MENU_HEIGHT_MULTILINE = 'h-[25px]'

// __ Тип для модального окна информации о записи в Заявке
const orderLine     = ref<IAssemblyTaskOrderLine | null>(null)
const orderItemInfo = ref<InstanceType<typeof OrderItemInfo> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна изменения Причины не выполнения
const falseReason              = ref('')
const manipulateDayFalseReason = ref<InstanceType<typeof ManipulateDayFalseReason> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

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


// __ Поля данных
const fieldWidths: Record<string, string> = {
    check       : 'min-w-[25px] max-w-[25px]',
    position    : 'min-w-[30px] max-w-[30px]',
    name        : 'min-w-[200px] max-w-[200px]',
    size        : 'min-w-[100px] max-w-[100px]',
    amount      : 'min-w-[40px] max-w-[40px]',
    time        : 'min-w-[100px] max-w-[100px]',
    kdb         : 'min-w-[70px] max-w-[70px]',
    timeLabel   : 'min-w-[100px] max-w-[100px]',
    manuf_line  : 'min-w-[40px] max-w-[40px]',
    false_reason: 'min-w-[174px] max-w-[174px]',
    order       : 'min-w-[300px] max-w-[300px]',
    description : 'min-w-[300px] max-w-[300px]',
    material    : 'min-w-[110px] max-w-[110px]',
    order_title : 'min-w-[110px] max-w-[110px]',
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---            Журнал Событий                   !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Журнал событий
const manageEventsAsync = ref<InstanceType<typeof ManageEventsAsync> | null>(null)

// __ Показываем Журнал Событий
const showEvents = async () => {
    await manageEventsAsync.value!.show()
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                 Ошибки                      !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Collapse                     !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Видимость названий подгрупп
const showGroups       = ref(true)
// __ Скрываем Переналадку
const toggleShowGroups = () => {
    showGroups.value = !showGroups.value
    if (!showGroups.value) {
        collapsedGroupsState.value = false
        Object.keys(collapsedStates.value).forEach(key => collapsedStates.value[key] = collapsedGroupsState.value)
    }
}

// __ Устанавливаем Collapsed
const collapsedStates    = ref<Record<string, boolean>>({})
const setCollapsedStates = () => {
    props.data.groups.forEach(group => collapsedStates.value[group.group.name] = true)
}

// __ Переключаем Группу
const toggleGroup = (groupName: string) => {
    if (collapsedStates.value[groupName] !== undefined) {
        collapsedStates.value[groupName] = !collapsedStates.value[groupName]
    }
}

// __ Устанавливаем Общий Collapsed
const collapsedGroupsState = ref(true)
const toggleGroups         = () => {
    collapsedGroupsState.value = !collapsedGroupsState.value
    Object.keys(collapsedStates.value).forEach(key => collapsedStates.value[key] = collapsedGroupsState.value)
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Побочка                      !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Проверка на то, что Заявка - объединенная
const isUnionTask = computed(() => assemblyTask.value.id === UNION_TASKS_ID)// __ Название заявок
const taskTitle   = computed(() => {
    if (isUnionTask.value) {
        return ASSEMBLY_UNION_TASK_NAME
    }
    return `${assemblyTask.value.position}. ${assemblyTask.value.order.client.short_name} №${assemblyTask.value.order.order_no_num}`
})


// __ Получаем все id Участков выделенных Линий
// __ Выбираем только задачи с нулевым статусом
const getSelectedSectorIds = (selector: 'reset' | null = null) => {
    const ids = new Set<number>()
    props.data.groups.forEach(group => {
        group.group_lines.forEach(groupLine => {
            if (selectedIds.value.has(groupLine.order_line.id)) {
                groupLine.materials_array.forEach(details => {
                    if (details) {
                        details.forEach(sector => {
                            if (selector === 'reset') {
                                if (isTaskLineDone(sector) || isTaskLineFalse(sector)) {
                                    ids.add(sector.id)
                                }
                            } else {
                                if (isTaskLineReset(sector)) {
                                    ids.add(sector.id)
                                }
                            }
                        })
                    }
                })
            }
        })
    })
    return Array.from(ids)
}

// __ Выполнено
const completeSelected = async () => {
    const ids: number[] = getSelectedSectorIds()

    if (!ids.length) {
        return
    }

    emits('setFinishStatus', ids)
    showMenu.value = false
}

// __ Не Выполнено
const unCompleteSelected = async () => {
    const ids: number[] = getSelectedSectorIds()

    if (!ids.length) {
        return
    }

    const answer = await manipulateDayFalseReason.value?.show()
    if (answer) {
        falseReason.value = manipulateDayFalseReason.value?.falseReason ?? ''

        if (!falseReason.value) {
            return
        }

        emits('setFalseStatus', ids, falseReason.value)
        showMenu.value = false
    }
}

// __ Сброс статуса
const resetStatus = async () => {
    const ids: number[] = getSelectedSectorIds('reset')

    if (!ids.length) {
        return
    }

    emits('resetStatus', ids)
    showMenu.value = false
}

// __ Разбить количество
const divideElementAmount = async () => {
    // // __ Проверяем, что есть выделенные элементы
    // if (selectedIds.value.size === 0) {
    //     return
    // }
    //
    // // __ Проверяем, что это не объединение СЗ
    // // if (props.blockTask.id === 0) {
    // //     return
    // // }
    //
    // // __ Берем первый элемент из выделенных
    // const findElement = JSON.parse(
    //     JSON.stringify(blockLines.value.find((line) => line.id === Array.from(selectedIds.value)[0]))
    // )
    //
    // // console.log('selected: ', findElement)
    //
    // // __ Проверяем, что элемент не завершен
    // if (findElement && (findElement.finished_at || findElement.false_at || findElement.amount === 1)) {
    //     return
    // }
    //
    // // __ Формируем название для модального окна
    // dividerElement.value.name =
    //     findElement.block.name + findElement.amount.toString() + ' шт.'
    //
    // dividerElement.value.amount = findElement.amount
    //
    // const answer = await appRangeModalAsyncTS.value!.show() // показываем модалку и ждем ответ
    // if (answer) {
    //     // __ Получаем диапазон + проверяем его (страховочка)
    //     const range = appRangeModalAsyncTS.value!.range
    //     if (!range || range.take === 0 || range.keep === 0) {
    //         return
    //     }
    //
    //     emits('divideLine', props.blockTask.id, findElement.id, range)
    //     selectedIds.value.clear()
    // }
}

const stopGlobalSelection = () => {
    isDragging.value = false
    stopAutoScroll()
}


// __ Печать
const printTask = () => {
    // // router.push({name: 'manufacture.cell.block.task.print'})
    //
    // // __ Запоминаем данные для печати
    // // const { globalBlockTaskPrintData } = storeToRefs(blockStore)
    // // globalBlockTaskPrintData.value = blockLinesGroup.value
    // // console.log(props.blockTask)
    //
    // localStorage.setItem(TASK_TO_PRINT_KEY, JSON.stringify(blockLinesGroup.value))
    // localStorage.setItem(TASK_TO_PRINT_META_KEY, JSON.stringify({
    //     action_at  : props.blockTask.action_at,
    //     change     : props.blockTask.change,
    //     task_title : taskTitle.value,
    //     block_group: blockLinesGroups.value[activeTabIndex.value].groupName,
    // }))
    //
    // // 1. Получаем объект с путем и параметрами
    // const routeData = router.resolve({
    //     name: 'manufacture.cell.blocks.task.print',
    //     // query: { orderId: id }
    // })
    //
    // // console.log('blockStore.globalBlockTaskPrintData: ', blockStore.globalBlockTaskPrintData)
    //
    // // 2. Открываем новое окно через стандартный JS
    // window.open(routeData.href, '_blank')
}


// __ Изменение Производственной Линии
const changeAssemblyLines = async (/*blockTask: IBlockTask*/) => {
    // // __ Для объединения СЗ не меняем Линии
    // if (props.blockTask.id === UNION_TASKS_ID) {
    //     return
    // }
    //
    // // __ Копируем объект, чтобы не мутировал оригинал
    // taskCard.value = JSON.parse(JSON.stringify(props.blockTask))
    //
    // console.log('taskCard: ', taskCard.value)
    //
    // // __ Добавляем метаданные Заявки в каждую строку
    // taskCard.value.block_lines.forEach(line => line.order_meta = `${taskCard.value.order.client.short_name} №${taskCard.value.order.order_no_str}`)
    //
    //
    // // __ Показываем модальное окно обработки СЗ
    // const answer = await manageTaskManufLines.value!.show()
    // if (!answer) {
    //     return
    // }
    //
    // // __ Получаем ссылки на панели
    // const mutations                          = manageTaskManufLines.value!.mutations
    // const setTablesData: IBlockLineSetData[] = mutations.map(line => ({ id: line.id, line: line.manuf_line, }))
    //
    // console.log('mutations: ', setTablesData)
    //
    // const result = await blockStore.taskLinesManufLineSet(setTablesData)
    // if (checkCRUD(result)) {
    //     // __ Меняем глобальный стейт
    //     blockStore.setGlobalArrayChangeManufLines(setTablesData)
    //     modalInfoType.value = 'success'
    //     modalInfoMode.value = 'inform'
    //     modalInfoText.value = 'Данные успешно обновлены'
    //     await appModalAsyncMultilineTS.value!.show()
    //
    // } else {
    //     await showError()
    // }
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---           Скрол и выделение                 !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

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
    return props.data.groups.flatMap(group => group.group_lines.map(line => line.order_line.id))
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
const handleMenuAction = async (action: string) => {
    if (action === 'done') {
        await completeSelected()
    } else if (action === 'false') {
        await unCompleteSelected()
    } else if (action === 'reset') {
        await resetStatus()
    } else if (action === 'divide') {
        await divideElementAmount()
    } else if (action === 'cancel') {
        selectedIds.value.clear()
    }
    showMenu.value = false
}


// __ Добавить в выделение все элементы ПС
const selectGroupItems = async (group: IMatrixManufactureGroup) => {
    modalInfoType.value = 'primary'
    modalInfoMode.value = 'confirm'
    modalInfoText.value = [
        `Выделить все элементы в коллекции Блоков: `,
        `${group.group.name}?`
    ]

    // __ Показываем модалку и ждем ответ
    const answer = await appModalAsyncMultilineTS.value!.show()
    if (answer) {
        group.group_lines.forEach(l => selectedIds.value.add(l.order_line.id))
    }
}

// --- Жизненный цикл ---
onMounted(async () => {
    window.addEventListener('mouseup', stopGlobalSelection)
    window.addEventListener('mousemove', handleMouseMove)
    window.addEventListener('click', () => (showMenu.value = false))

    // setActiveTabIndex()
    // activeTabIndex.value = 0

    setCollapsedStates()

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
    height: calc(100vh - var(--header-height) - var(--footer-height) - 169px);

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

