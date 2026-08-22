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

                <!--&lt;!&ndash; __ Видимость заголовков &ndash;&gt;-->
                <!--<AppLabelTS-->
                <!--    :height="MENU_HEIGHT"-->
                <!--    :text="showTuningTimes ? '🔓' : '🔒'"-->
                <!--    align="center"-->
                <!--    class="menu-button"-->
                <!--    rounded="4"-->
                <!--    text-size="huge"-->
                <!--    title="Скрыть/Показать название размеров Кроя"-->
                <!--    type="dark"-->
                <!--    width="w-[50px]"-->
                <!--    @click="toggleShowTuningTimesVisibility"-->
                <!--/>-->

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
    </div>


    <!-- __ Модальное окно для добавления причины не выполнения -->
    <ManipulateDayFalseReason
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

    <!-- __ Журнал Событий -->
    <!--<ManageEventsAsync-->
    <!--    ref="manageEventsAsync"-->
    <!--    :cell="CELL_EVENT_BLOCK"-->
    <!--    :day-id="dayId"-->
    <!--/>-->

</template>

<script lang="ts" setup>
import { ref, computed, nextTick } from 'vue'

import { formatTimeWithLeadingZeros, splitDate } from '@/app/helpers/helpers_date'
import AppProgressBar from '@/components/ui/bars/AppProgressBar.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { CELL_EVENT_BLOCK } from '@/app/constants/cell_events.ts'
import BlockDesignDocumentAsync from '@/components/dashboard/manufacture/shared/block_design/BlockDesignDocumentAsync.vue'
import OrderItemInfo from '@/components/dashboard/manufacture/cells/assembly/common/OrderItemInfo.vue'
import ManipulateDayFalseReason from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulateDayFalseReason.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'
import ManageTaskManufLines from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskManufLines.vue'
import ManageEventsAsync from '@/components/dashboard/manufacture/events/ManageEventsAsync.vue'
import AppRangeModalAsyncTS from '@/components/ui/modals/AppRangeModalAsyncTS.vue'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import { TASK_TO_PRINT_KEY, TASK_TO_PRINT_META_KEY } from '@/app/constants/common.ts'
import type { IAssemblyTask, IAssemblyTaskOrderLine, IBlockTaskLinesGroupNames, IDividerItem, IMatrixManufactureTask } from '@/types'
import { LINE_1_NAME, LINE_2_NAME } from '@/app/constants/blocks.ts'
import { isTaskLineReset } from '@/app/helpers/manufacture/helpers_blocks.ts'
import { ASSEMBLY_UNION_TASK_NAME } from '@/app/constants/assembly.ts'


interface IProps {
    data: IMatrixManufactureTask
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

const collapsedGroupsState = ref(true)

const toggleGroups = () => {
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Побочка                      !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Проверка на то, что Заявка - объединенная
const isUnionTask = computed(() => assemblyTask.value.id === UNION_TASKS_ID)// __ Название заявок
const taskTitle = computed(() => {
    if (isUnionTask.value) {
        return ASSEMBLY_UNION_TASK_NAME
    }
    return `${assemblyTask.value.position}. ${assemblyTask.value.order.client.short_name} №${assemblyTask.value.order.order_no_num}`
})


// __ Выполнено
const completeSelected = async () => {
    const ids: number[] = []

    // __ Выбираем только задачи с нулевым статусом
    // blockLines.value.forEach((t) => {
    //     if (selectedIds.value.has(t.id)) {
    //         if (isTaskLineReset(t)) {
    //             ids.push(t.id)
    //         }
    //     }
    // })
    //
    // if (!ids.length) {
    //     return
    // }
    //
    // emits('setFinishStatus', ids)
    // showMenu.value = false
}

// __ Не Выполнено
const unCompleteSelected = async () => {
    const ids: number[] = []
    //
    // // __ Выбираем только задачи с нулевым статусом
    // blockLines.value.forEach((t) => {
    //     if (selectedIds.value.has(t.id)) {
    //         if (isTaskLineReset(t)) {
    //             ids.push(t.id)
    //         }
    //     }
    // })
    //
    // if (!ids.length) {
    //     return
    // }
    //
    // const answer = await manipulateDayFalseReason.value?.show()
    // if (answer) {
    //     falseReason.value = manipulateDayFalseReason.value?.falseReason ?? ''
    //
    //     if (!falseReason.value) {
    //         return
    //     }
    //
    //     emits('setFalseStatus', ids, falseReason.value)
    //     showMenu.value = false
    // }
}

// __ Сброс статуса
const resetStatus = async () => {

    // // __ Получаем флаг готовности к добавлению новых СЗ и если все в процессе выполнения - выходим
    // const isReady: boolean = await blockStore.readyGetBlockDay(splitDate(props.blockTask.action_at))
    // if (isReady) {
    //     await showError(TASK_READY_ADD_STATUS_MESSAGE)
    //     return
    // }
    //
    // const ids: number[] = []
    //
    // // __ Выбираем только задачи не с нулевым статусом
    // blockLines.value.forEach((t) => {
    //     if (selectedIds.value.has(t.id)) {
    //         if (!isTaskLineReset(t)) {
    //             ids.push(t.id)
    //         }
    //     }
    // })
    //
    // if (!ids.length) {
    //     return
    // }
    //
    // emits('resetStatus', ids)
    // showMenu.value = false
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
    // return blockLinesGroup.value.flatMap(subgroup => subgroup.lines.map(line => line.id))
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
    // if (action === 'done') {
    //     await completeSelected()
    // } else if (action === 'false') {
    //     await unCompleteSelected()
    // } else if (action === 'reset') {
    //     await resetStatus()
    // } else if (action === 'divide') {
    //     await divideElementAmount()
    // } else if (action === 'cancel') {
    //     selectedIds.value.clear()
    // } else if ([LINE_1_NAME, LINE_2_NAME].includes(action)) {
    //     await changeAssemblyLineByMenu(action as IBlockTaskLinesGroupNames)
    // }
    //
    // showMenu.value = false
}

</script>

<style scoped>

</style>
