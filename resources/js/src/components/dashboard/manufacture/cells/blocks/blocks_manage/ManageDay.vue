<template>
    <div
        :class="[shadowColor, globalBlockTaskFullDaysShow ? 'min-w-[301px]' : 'min-w-[185px]']"
        class="m-1 pb-0.5 border-[1px] rounded border-slate-600 bg-slate-200 w-fit min-h-[129px] shadow-xl"
    >
        <!-- __ День недели -->
        <div class="mr-1">
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text="renderDate"
                :type="dateType"
                align="center"
                class="cursor-pointer"
                rounded="rounded-[4px]"
                text-size="small"
                title="Click + Ctrl - Выполнение СЗ"
                width="w-full"
                @click.ctrl="gotoExecute"
            />
        </div>

        <!-- __ Шапка -->
        <div
            v-if="hasDataChange_1 || hasDataChange_2"
            class="flex"
        >
            <!--&lt;!&ndash; __ Смена &ndash;&gt;-->
            <!--<AppLabelTS-->
            <!--    :height="DEFAULT_HEIGHT"-->
            <!--    :text-size="DATA_HEADER_TEXT_SIZE"-->
            <!--    :type="TOTALS_TYPE"-->
            <!--    :width="columnsWidth.change"-->
            <!--    align="center"-->
            <!--    class="uppercase"-->
            <!--    rounded="rounded-[4px]"-->
            <!--    text="См"-->
            <!--/>-->

            <!-- __ Клиент -->
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.client"
                align="center"
                class="uppercase"
                rounded="rounded-[4px]"
                text="Клиент"
            />

            <!-- __ Номер заказа -->
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.orderNo"
                align="center"
                rounded="rounded-[4px]"
                text="№"
            />

            <!-- __ Сумма -->
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                align="center"
                rounded="rounded-[4px]"
                text="Σ"
            />

            <!-- __ Линия 1 -->
            <AppLabelTS
                v-if="globalBlockTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.table"
                align="center"
                rounded="rounded-[4px]"
                text="1"
            />

            <!-- __ Линия 2 -->
            <AppLabelTS
                v-if="globalBlockTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.table"
                align="center"
                rounded="rounded-[4px]"
                text="2"
            />

            <!-- __ Неопознанные -->
            <AppLabelTS
                v-if="globalBlockTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.table"
                align="center"
                color="red"
                rounded="rounded-[4px]"
                text="??"
            />
        </div>

        <div v-for="(change, idx) of day" :key="idx">

            <div :class="change.length ? getChangeClass(idx) : ''">

                <!-- __ Смена -->
                <AppLabelTS
                    :height="DEFAULT_HEIGHT"
                    :text="getChangeTitle(idx === 0 ? CHANGE_1 : CHANGE_2)"
                    :type="getChangeType(idx === 0 ? CHANGE_1 : CHANGE_2)"
                    align="center"
                    class="uppercase cursor-pointer"
                    rounded="rounded-[4px]"
                    text-size="mini"
                    width="calc(w-full - 5px)"
                    @click.exact="actionDayMenu(idx === 0 ? CHANGE_1 : CHANGE_2)"
                />

                <!-- __ Сами СЗ с возможностью перетаскивания -->
                <draggable
                    :="dragOptions"
                    :disabled="!isDragging"
                    :list="change as unknown as IBlockTask[]"
                    :move="checkMove"
                    class="min-h-[25px]"
                    item-key="id"
                    tag="div"
                    @end="finishDrag"
                    @start="startDrag"
                >
                    <template #item="{ element, index }">
                        <div
                            @click="selectBlockTask(element)"
                            @dblclick="showBlockTaskMenu(element)"
                        >
                            <ManageItem
                                :amount-and-time="getBlockTaskAmountAndTime(element)"
                                :columns-width="columnsWidth"
                                :index="index"
                                :item="element"
                                :order-id="globalBlockTaskActiveOrderId"
                            />
                        </div>
                    </template>

                </draggable>

                <!-- __ Разделительная линия -->
                <div
                    v-if="(hasDataChange_1 && idx === 0) || (hasDataChange_2 && idx === 1)"
                    class="flex"
                >
                    <TheDividerLine/>
                </div>

                <!-- __ Итого -->
                <div
                    v-if="(hasDataChange_1 && idx === 0) || (hasDataChange_2 && idx === 1)"
                    class="flex"
                >
                    <!-- __ Всего: -->
                    <AppLabelTS
                        :height="heightTotals"
                        :type="getChangeType(idx === 0 ? CHANGE_1 : CHANGE_2)"
                        :width="columnsWidth.common"
                        align="center"
                        rounded="rounded-[4px]"
                        text="Всего:"
                        text-size="mini"
                    />

                    <!-- __ Количество + Трудозатраты Общие -->
                    <ManageItemDataLabel
                        :amount="getTotalAmountChange(change as unknown as IBlockTask[])"
                        :height="heightTotals"
                        :reference="REFERENCE_TIME * 2"
                        :time="getTotalTimeChange(change as unknown as IBlockTask[])"
                        :time-show="globalBlockTaskTimesShow"
                        :type="TOTALS_TYPE"
                        :width="columnsWidth.amount"
                        class="plan-item"
                    />

                    <!-- __ Количество + Трудозатраты Линия 1 -->
                    <ManageItemDataLabel
                        v-if="globalBlockTaskFullDaysShow"
                        :amount="amountAndTimeTotalsChanges[idx][BLOCK_MANUF_LINES.LINE_1].amount"
                        :height="heightTotals"
                        :reference="REFERENCE_TIME"
                        :time="amountAndTimeTotalsChanges[idx][BLOCK_MANUF_LINES.LINE_1].time"
                        :time-show="globalBlockTaskTimesShow"
                        :type="TOTALS_TYPE"
                        :width="columnsWidth.amount"
                        class="plan-item"
                    />

                    <!-- __ Количество + Трудозатраты Линия 2 -->
                    <ManageItemDataLabel
                        v-if="globalBlockTaskFullDaysShow"
                        :amount="amountAndTimeTotalsChanges[idx][BLOCK_MANUF_LINES.LINE_2].amount"
                        :height="heightTotals"
                        :reference="REFERENCE_TIME"
                        :time="amountAndTimeTotalsChanges[idx][BLOCK_MANUF_LINES.LINE_2].time"
                        :time-show="globalBlockTaskTimesShow"
                        :type="TOTALS_TYPE"
                        :width="columnsWidth.amount"
                        class="plan-item"
                    />

                    <!-- __ Количество + Трудозатраты Неопознанные -->
                    <ManageItemDataLabel
                        v-if="globalBlockTaskFullDaysShow"
                        :amount="amountAndTimeTotalsChanges[idx][BLOCK_MANUF_LINES.LINE_0].amount"
                        :color="amountAndTimeTotalsChanges[idx][BLOCK_MANUF_LINES.LINE_0].amount === 0 ? '' : 'red'"
                        :height="heightTotals"
                        :reference="null"
                        :time="amountAndTimeTotalsChanges[idx][BLOCK_MANUF_LINES.LINE_0].time"
                        :time-show="globalBlockTaskTimesShow"
                        :type="TOTALS_TYPE"
                        :width="columnsWidth.amount"
                        class="plan-item"
                    />
                </div>

                <!-- __ Двойная Разделительная линия -->
                <template v-for="i of [1, 2]" :key="i">
                    <div
                        v-if="(hasDataChange_1 && idx === 0)/* || (hasDataChange_2 && idx === 1)*/"
                        class="flex bg-stone-200"
                    >
                        <TheDividerLine/>
                    </div>
                </template>

            </div>
        </div>

    </div>

    <!-- __ Карточка СЗ -->
    <ManageTaskCard
        ref="manageTaskCard"
        :mode="modalMode"
        :task="taskCard"
        :text="modalText"
        :type="modalType"
    />

    <!-- __ Смена Производственной линии -->
    <ManageTaskManufLines
        ref="manageTaskManufLines"
        :mode="modalMode"
        :task="taskCard"
        :text="modalText"
        :type="modalType"
    />

    <!-- __ Модальное Меню -->
    <AppModalMenuTS
        ref="appModalMenuTS"
        :menu="modalMenu"
        :type="modalMenuType"
    />

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />

    <!-- __ Модальное окно для изменения/добавления комментария -->
    <CommentEdit
        ref="commentEdit"
        :comment="comment"
        label="Комментарий к производственному дню"
    />


</template>

<!--suppress PointlessBooleanExpressionJS, PointlessBooleanExpressionJS -->
<script lang="ts" setup>
import type {
    IColorTypes,
    IDay,
    IModalAsyncMenu,
    IPlanMatrix, IBlockDay,
    IBlockTask,
    IBlockTaskStatusesSet, IBlockTaskLine, IBlockLineSetData, DraggableHTMLElement, IAmountAndTimeBlock, IBlockTaskChangeKeys,
} from '@/types'

import { computed, inject, type Ref, ref, } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import draggable from 'vuedraggable'

import { usePlansStore } from '@/stores/PlansStore.ts'
import { useBlocksStore } from '@/stores/BlocksStore.ts'

import {
    additionDaysInStrFormat,
    formatDateInFullFormat,
    formatDateIntl,
    formatToYMD,
    getDayOfWeek,
    isHoliday,
    isToday,
    splitDate,
    getDaysDifferenceFromDates,
} from '@/app/helpers/helpers_date'
import {
    clearRenderMatrix,
    clearRenderMatrixDay,
    correctRenderMatrix,
    createAmountAndTimeObj,
    getDiffsWithPositions,
    getBlockTaskAmountAndTime,
    getBlockTasksDiff,
    getBlockTasksGroupedByOrder,
    getBlockTasksSameOrderInDay,
    isTaskAverage,
    isTaskStatusCreated, isTaskStatusRunning,
    orderBlockTasksByStatus,
    repositionBlockTaskLines,
    setTaskPositionInRenderMatrix, hasTaskUnknownManufLine, getOrderTitle, getIndexByChange, getChangeByName,
} from '@/app/helpers/manufacture/helpers_blocks.ts'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import { ifDateInPeriod } from '@/app/helpers/plan/helpers_plan.ts'

import { BLOCK_MANUF_LINES, BLOCK_TASK_DRAFT, BLOCK_TASK_STATUSES, CHANGE_1, CHANGE_2, CHANGES } from '@/app/constants/blocks.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import TheDividerLine from '@/components/ui/dividers/TheDividerLine.vue'
import AppModalMenuTS, { type IModalResponse } from '@/components/ui/modals/AppModalAsyncMenuTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'

import ManageTaskCard from '@/components/dashboard/manufacture/cells/blocks/blocks_manage/ManageTaskCard.vue'
import ManageItem from '@/components/dashboard/manufacture/cells/blocks/blocks_manage/ManageItem.vue'
import ManageItemDataLabel from '@/components/dashboard/manufacture/cells/blocks/blocks_manage/ManageItemDataLabel.vue'

import CommentEdit from '@/components/dashboard/manufacture/cells/blocks/common/CommentEdit.vue'
import ManageTaskManufLines from '@/components/dashboard/manufacture/cells/blocks/blocks_manage/ManageTaskManufLines.vue'


// type IDay = IBlockTask & IPlanMatrixDayItem

interface IProps {
    date: Date
    day?: IDay[],
}

const props = withDefaults(defineProps<IProps>(), {
    day: () => [],
})


// __ Класс для смен
const getChangeClass = (change: number) => {
    switch ((change + 1).toString()) {
        case CHANGES.CHANGE_1.NAME:
            return 'bg-indigo-200'
        case CHANGES.CHANGE_2.NAME:
            return 'bg-orange-200'
    }
}


// const emits = defineEmits<{
//     (e: 'drag-and-drop'): void,
// }>()

// console.log('day: ', props.day)

// __ Получаем данные из родителя
// const renderMatrix = inject('renderMatrix', [])
const renderMatrix     = inject<Ref<IPlanMatrix>>('renderMatrix', ref([]))
const renderMatrixCopy = inject<Ref<IPlanMatrix>>('renderMatrixCopy', ref([]))

// console.log('renderMatrix: ', renderMatrix)
// console.log('renderMatrixCopy: ', renderMatrixCopy)

// __ Данные из Хранилища
const blockStore = useBlocksStore()

const {
          globalBlockTaskTimesShow,
          globalBlockTaskFullDaysShow,
          /*globalDiffs,*/
          globalBlockTasks,
          globalBlockTaskActiveOrderId,
          globalBlockTaskStatuses,
      } = storeToRefs(blockStore)

const router               = useRouter()
const planStore            = usePlansStore()
const { planPeriodGlobal } = storeToRefs(planStore)

const DEFAULT_HEIGHT = 'h-[25px]'

const TOTALS_TYPE: IColorTypes = 'stone'
const DATA_HEADER_TEXT_SIZE    = 'mini'
// const CHANGE_1_TYPE            = 'indigo'
// const CHANGE_2_TYPE            = 'orange'
const REFERENCE_TIME = 10.5 // часы

// __ Высота под Итого
const heightTotals = computed(() => (globalBlockTaskTimesShow.value ? 'h-[80px]' : 'h-[40px]'))

// __ Дата
const renderDate = computed(() => formatDateInFullFormat(props.date, !globalBlockTaskFullDaysShow.value) + ` (${getDayOfWeek(props.date)})`)
// const renderDate = computed(() => getDateFromDateTimeString(props.date))

// TODO: Переписать все в dateType()
const isHolidayDay = computed(() => isHoliday(props.date))
const isTodayDay   = computed(() => isToday(props.date))
const isDayInRange = computed(() => ifDateInPeriod(props.date, planPeriodGlobal.value))
// const isActiveDay = computed(() => false)
const dateType     = computed((): IColorTypes => {
    if (isTodayDay.value) return 'success'
    if (!isDayInRange.value) return 'dark'
    if (isHolidayDay.value) return 'danger'
    return 'primary'
})
// ----------------------------------------

// __ Ширина колонок
const columnsWidth = {
    change : 'w-[30px]',
    client : 'w-[90px]',
    orderNo: 'w-[45px]',
    amount : 'w-[35px]',
    common : 'w-[139px]',
    table  : 'w-[35px]',
}

// __ Цвет тени
const shadowColor = computed(() => {
    switch (dateType.value) {
        case 'success':
            return 'shadow-green-300'
        case 'danger':
            return 'shadow-red-300'
        case 'primary':
            return 'shadow-blue-300'
        case 'dark':
            return 'shadow-slate-400'
        default:
            return 'shadow-slate-400'
    }
})

// __ Общее количество и время в виде Объекта
const amountAndTimeTotalsChanges = computed(() => {
    //  __ Создаем сам объект данных с ключами из BLOCK_MACHINES и {time: 0, amount: 0} и инициализируем его нулями

    const result: IAmountAndTimeBlock[] = []
    props.day.forEach(change => {
        const amountAndTimeObj = createAmountAndTimeObj()

        change.forEach((blockTask: IBlockTask) => {

            const amountAndTime = getBlockTaskAmountAndTime(blockTask as IBlockTask)

            Object.entries(amountAndTime).forEach(([key, value]) => {
                amountAndTimeObj[key].amount += value.amount
                amountAndTimeObj[key].time += value.time
            })
        })

        result.push(amountAndTimeObj)
    })

    return result
})

// __ Общее Количество за день
const getTotalAmountChange = (tasks: IBlockTask[]) => {
    return tasks.reduce((totalAcc, task) =>
        totalAcc + task.block_lines.reduce((acc: number, line: IBlockTaskLine) => acc + line.amount, 0), 0)
}
// const getTotalAmountDay = computed(() => props.day.reduce((totalAcc, task) =>
//     totalAcc + task.block_lines.reduce((acc: number, line: IBlockTaskLine) => acc + line.amount, 0), 0))

// __ Общие Трудозатраты за день
const getTotalTimeChange = (tasks: IBlockTask[]) => {
    return tasks.reduce((totalAcc, task) =>
        totalAcc + task.block_lines.reduce((acc: number, line: IBlockTaskLine) => acc + line.time, 0), 0)
}
// const getTotalTimeDay = computed(() =>
//     props.day.reduce((totalAcc, task) => totalAcc + task.block_lines.reduce((acc: number, line: IBlockTaskLine) => acc + line.time, 0), 0))

// __ Флаг отображения данных
const hasDataChange_1 = computed(() => getTotalAmountChange(props.day[0] as unknown as IBlockTask[]))
const hasDataChange_2 = computed(() => getTotalAmountChange(props.day[1] as unknown as IBlockTask[]))

// __ Получаем подсветку Смены
const getChangeType = (change: IBlockTaskChangeKeys) => {
    const findChange = getChangeByName(change)
    return findChange ? findChange.TYPE : 'dark'
}

// __ Получаем Название
const getChangeTitle = (change: IBlockTaskChangeKeys) => {
    return change === CHANGE_1 ? 'Смена: 1 (08:30 - 20:30)' : 'Смена: 2 (20:30 - 08:30)'
}


// __ Тип для Карточки и Изменения Производственной Линии
const modalType            = ref<IColorTypes>('primary')
const modalText            = ref<string>('')
const modalMode            = ref<'inform' | 'confirm'>('inform')
const manageTaskCard       = ref<InstanceType<typeof ManageTaskCard> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией
const manageTaskManufLines = ref<InstanceType<typeof ManageTaskManufLines> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального Меню
const modalMenuType  = ref<IColorTypes>('primary')
const modalMenu      = ref<IModalAsyncMenu>({ data: [] })
const appModalMenuTS = ref<InstanceType<typeof AppModalMenuTS> | null>(null)

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)

// __ Тип для модального окна изменения Комментария
const comment     = ref('')
const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null)

// __ Установка активного Заказа
const selectBlockTask = (blockTask: IBlockTask) => {
    globalBlockTaskActiveOrderId.value = blockTask.order.id
}

// __ Карточка СЗ
const taskCard = ref<IBlockTask>(BLOCK_TASK_DRAFT)

const showBlockTaskCard = async (blockTask: IBlockTask) => {
    taskCard.value = JSON.parse(JSON.stringify(blockTask)) // __ Копируем объект, чтобы не мутировал оригинал

    // __ Показываем модальное окно обработки СЗ
    const answer = await manageTaskCard.value!.show()
    if (!answer) {
        return
    }

    // __ Получаем ссылки на панели
    const leftPanel  = manageTaskCard.value!.leftPanel
    const rightPanel = manageTaskCard.value!.rightPanel

    // __ Если есть правая панель, то это создание нового СЗ
    if (rightPanel.length > 0) {
        // __ Создаем новое СЗ на основе копии
        const newBlockTask = JSON.parse(JSON.stringify(blockTask))

        // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
        newBlockTask.position += 0.1

        // __ Устанавливаем id
        // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
        newBlockTask.id = 0

        // __ Пересчитываем позиции для строк СЗ (BlockLines[])
        // leftPanel  = repositionBlockTaskLines(leftPanel)
        // rightPanel = repositionBlockTaskLines(rightPanel)

        // __ Обновляем глобальный state СЗ
        // blockTask.block_lines    = leftPanel              // __ Тут передача по ссылке, автоматическое изменение
        // newBlockTask.block_lines = rightPanel

        // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
        await blockStore.addBlockTaskToGlobal(blockTask, leftPanel, newBlockTask, rightPanel) // __ Тут реактивное перерисовывание

        // console.log(taskCard.value)
    } else {
        // __ Тут ситуация, когда изменился только левая панель (разделение количества и(или) порядка)

        // __ Пересчитываем позиции для строк СЗ (BlockLines[])
        // leftPanel = repositionBlockTaskLines(leftPanel)

        // __ Обновляем глобальный state СЗ
        await blockStore.addBlockTaskToGlobal(blockTask, leftPanel) // __ Тут реактивное перерисовывание
    }
}

// __ Изменение Производственной Линии
const showBlockTaskManufLines = async (blockTask: IBlockTask) => {
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
    const mutations                         = manageTaskManufLines.value!.mutations
    const setBlockData: IBlockLineSetData[] = mutations.map(line => ({ id: line.id, line: line.manuf_line, }))

    console.log('mutations: ', setBlockData)

    const result = await blockStore.taskLinesManufLineSet(setBlockData)
    if (checkCRUD(result)) {
        // __ Меняем глобальный стейт
        blockStore.setGlobalArrayChangeManufLines(setBlockData)
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'
        modalInfoText.value = 'Данные успешно обновлены'
        await appModalAsyncMultiline.value!.show()

    } else {
        await showError()
    }


    // // __ Если есть правая панель, то это создание нового СЗ
    // if (rightPanel.length > 0) {
    //     // __ Создаем новое СЗ на основе копии
    //     const newBlockTask = JSON.parse(JSON.stringify(blockTask))
    //
    //     // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
    //     newBlockTask.position += 0.1
    //
    //     // __ Устанавливаем id
    //     // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
    //     newBlockTask.id = 0
    //
    //     // __ Пересчитываем позиции для строк СЗ (BlockLines[])
    //     // leftPanel  = repositionBlockTaskLines(leftPanel)
    //     // rightPanel = repositionBlockTaskLines(rightPanel)
    //
    //     // __ Обновляем глобальный state СЗ
    //     // blockTask.block_lines    = leftPanel              // __ Тут передача по ссылке, автоматическое изменение
    //     // newBlockTask.block_lines = rightPanel
    //
    //     // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
    //     await blockStore.addBlockTaskToGlobal(blockTask, leftPanel, newBlockTask, rightPanel) // __ Тут реактивное перерисовывание
    //
    //     // console.log(taskCard.value)
    // } else {
    //     // __ Тут ситуация, когда изменился только левая панель (разделение количества и(или) порядка)
    //
    //     // __ Пересчитываем позиции для строк СЗ (BlockLines[])
    //     // leftPanel = repositionBlockTaskLines(leftPanel)
    //
    //     // __ Обновляем глобальный state СЗ
    //     await blockStore.addBlockTaskToGlobal(blockTask, leftPanel) // __ Тут реактивное перерисовывание
    // }
}


// __ Добавить комментарий
const addComment = async (task: IBlockTask) => {

    comment.value = task.comment ?? '' // __ Устанавливаем комментарий

    const answer = await commentEdit.value!.show()
    if (answer) {

        const newComment = commentEdit.value!.comment.trim()

        const result = await blockStore.setBlockTaskComment(task.id, newComment)

        if (!checkCRUD(result)) {

            modalInfoText.value = [
                'Упс! Что-то пошло не так.',
                'Попробуйте повторить операцию позже.',
            ]
            modalInfoType.value = 'danger'
            modalInfoMode.value = 'inform'
            await appModalAsyncMultiline.value!.show()

            return
        }

        // __ Обновляем комментарий в глобальном массиве
        blockStore.applyBlockTaskComment(task.id, newComment)

        // __ Обновляем комментарий в СЗ
        task.comment = newComment
    }
}

// __ Меняем смену
const modifyChange = async (task: IBlockTask) => {

    // __ Проверяем статус СЗ, если не Создано, то выходим
    if (!isTaskStatusCreated(task)) {
        await showError([
            'Изменить смену можно только у СЗ',
            'со статусом "Создано" или ',
            'со статусом "Создано при закрытии СЗ"!',
        ])

        return
    }

    const targetChange = task.change === CHANGES.CHANGE_1.NAME ? CHANGES.CHANGE_2 : CHANGES.CHANGE_1

    // __ Показываем предупреждение
    modalInfoType.value = 'primary'
    modalInfoMode.value = 'confirm'
    modalInfoText.value = [
        'Смена СЗ',
        getOrderTitle(task),
        `будет изменена на ${targetChange.TITLE}.`,
        'Продолжить?'
    ]

    const answer = await appModalAsyncMultiline.value!.show()
    if (!answer) {
        return
    }

    // __ Устанавливаем реактивно смену
    task.change = targetChange.NAME

    // __ Выясняем, что перетаскивали и куда перемещали и что устанавливали (смена)
    let renderMatrixCloned = JSON.parse(JSON.stringify(renderMatrix.value))
    renderMatrixCloned     = clearRenderMatrix(renderMatrixCloned)
    renderMatrixCloned     = setTaskPositionInRenderMatrix(renderMatrixCloned)

    // console.log('renderMatrixCleared: ', renderMatrixCloned)
    // console.log('renderMatrixCopy: ', renderMatrixCopy.value)

    // // __ Получаем разницу между матрицами
    const diffs = getDiffsWithPositions(renderMatrixCloned, renderMatrixCopy.value)
    console.log('diffs: ', diffs)

    // __ Если нет изменений - выходим, чтобы не было лишних телодвижений
    if (!diffs.length) {
        // __ Откатываем изменения
        renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
        return
    }

    // __ Перемещаем СЗ без вывода дополнительной информации
    await blockStore.applyChanges(diffs) // __ Применяем изменения

    // const answer = await appModalAsyncMultiline.value!.show()
    // if (answer) {
    //
    //     const result = await blockStore.modifyChange(task.id, targetChange.NAME) // __ Применяем изменения
    //     if (!checkCRUD(result)) {
    //         await showError()
    //         return
    //     }
    //
    //     // __ Устанавливаем реактивно смену
    //     task.change = targetChange.NAME
    //     return
    // }
}


// __ Меню при двойном клике на Заявке (Разделить количество + Изменить стол)
const showBlockTaskMenu = async (blockTask: IBlockTask) => {
    // __ Показываем модальное меню при двойном клике на Заявке обрабатываем результаты
    modalMenuType.value = 'indigo'

    const CANCEL_ID = 6
    modalMenu.value = {
        data: [
            { id: 1, title: 'Разделить количество' },
            { id: 2, title: 'Изменить смену СЗ' },
            { id: 3, title: 'Изменить Производственную линию' },
            { id: 4, title: 'Добавить / Изменить комментарий к СЗ' },
            { id: 5, title: 'Перейти в Карточку Заявки' },
            { id: CANCEL_ID, title: 'Отмена' },
        ],
    }

    const result = await appModalMenuTS.value!.show()
    // let result = { menuItem: 3, value: true } as IModalResponse

    // __ Отмена
    if (result.menuItem === CANCEL_ID && result.value) {
        return
    }

    // __ Разделить количество
    if (result.menuItem === 1 && result.value) {
        await showBlockTaskCard(blockTask)
        return
    }

    // __ Изменить Смену
    if (result.menuItem === 2 && result.value) {
        await modifyChange(blockTask)
        return
    }

    // __ Изменить Линию
    if (result.menuItem === 3 && result.value) {
        await showBlockTaskManufLines(blockTask)
        return
    }

    // __ Добавить комментарий к СЗ
    if (result.menuItem === 4 && result.value) {
        await addComment(blockTask)
        return
    }

    // __ Перейти в карточку Заявки
    if (result.menuItem === 5 && result.value) {
        await router.push({ name: 'orders.card', params: { id: blockTask.order.id } })
        return
    }

}


// --- ------------------------------------------------------
// --- ----------- Управление Druggable ---------------------
// --- ------------------------------------------------------
// __ Опции для draggable
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const dragOptions = computed(() => {
    return {
        animation  : 300,
        group      : 'orders',
        ghostClass : 'ghost',
        dragClass  : 'drag',
        chosenClass: 'chosen',
        // sort: true,
        // disabled: false, // Выносим в отдельное свойство
    }
})
const isDragging  = ref(true)

const checkMove = (evt: DraggableHTMLElement) => {
    // return true
    // console.log('checkMove: ', evt)
    const movedElement = evt.draggedContext.element as IBlockTask
    // console.log(movedElement)
    // return true
    // __ Проверяем, что перемещаемый элемент со статусом 'Создано' или 'Выполняется' но внутри одного дня
    if (!isTaskStatusCreated(movedElement) && !isTaskStatusRunning(movedElement)) {
        return false
    }

    // __ Проверяем, что перемещаемый элемент не в прошлом
    const nowDate  = formatToYMD(new Date())
    const dateDiff = getDaysDifferenceFromDates(movedElement.action_at, nowDate)

    // console.log('movedElement.action_at: ', movedElement.action_at)
    // console.log('nowDate: ', nowDate)
    // console.log('dateDiff: ', dateDiff)

    if (dateDiff < 0) {
        // await showError(['Ошибка!', 'Прошлое не ворошим!'])
        // renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
        return false
    }

    return true
}

// __ Начало перетаскивания СЗ
const startDrag = (/*evt: any*/) => {
    // const element = evt.item._underlying_vm_
    // console.log('startDrag: ', evt.oldIndex)
    // console.log('element: ', element)
}

// __ Окончание перетаскивания СЗ
const finishDrag = async (evt: DraggableHTMLElement) => {
    // const element = evt.item._underlying_vm_
    // console.log('evt: ', evt)


    // __ Выясняем, что перетаскивали и куда перемещали
    let renderMatrixCloned = JSON.parse(JSON.stringify(renderMatrix.value))
    renderMatrixCloned     = clearRenderMatrix(renderMatrixCloned)
    renderMatrixCloned     = setTaskPositionInRenderMatrix(renderMatrixCloned)

    console.log('renderMatrixCleared: ', renderMatrixCloned)
    console.log('renderMatrixCopy: ', renderMatrixCopy.value)

    // __ Получаем разницу между матрицами
    const diffs = getDiffsWithPositions(renderMatrixCloned, renderMatrixCopy.value)
    console.log('diffs: ', diffs)

    // __ Если нет изменений - выходим, чтобы не было лишних телодвижений
    if (!diffs.length) {
        // __ Откатываем изменения
        renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
        return
    }

    // __ Проверяем, переместились ли СЗ в рамках одного дня или нет
    const isOneDayAction = !diffs.some(diff => diff.isMoved)

    // __ Проверяем, переместились ли СЗ в рамках смены
    const isChangeModify = diffs.some(diff => diff.isChangeChanged)

    // __ Находим целевую смену (куда перемещаем)
    const targetChange = diffs.find(diff => diff.isChangeChanged)?.newChange


    console.log('isOneDayAction: ', isOneDayAction)
    console.log('isChangeModify: ', isChangeModify)

    // __ Получаем сам перемещаемый элемент
    const movedElement = evt.item._underlying_vm_ as IBlockTask

    if (isOneDayAction && !isChangeModify) {

        console.log('movedElement: ', movedElement)

        // __ Если перемещаемый элемент со статусом 'Выполняется', проверяем маячок,
        // __ который указывает на готовность к добавлению СЗ
        if (isTaskStatusRunning(movedElement)) {

            // __ Получаем флаг готовности к добавлению новых СЗ
            const isReady: IBlockDay = await blockStore.readyGetBlockDay(splitDate(movedElement.action_at))

            if (!isReady) {
                await showError([
                    'Ошибка!',
                    'Для перемещения СЗ со статусом "Выполняется"',
                    'необходимо приостановить выполнение СЗ',
                    'для добавления новых СЗ!',
                ])

                // __ Откатываем изменения
                renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
                return
            }
        }

        // __ Перемещаем СЗ без вывода дополнительной информации
        await blockStore.applyChanges(diffs) // __ Применяем изменения

    } else {

        // __ Проверяем, что перемещаемый элемент не со статусом 'Выполняется'
        // __ потому что здесь уже перемещение между днями, а с этим статусом только в рамках дня
        if (isTaskStatusRunning(movedElement)) {
            await showError([
                'Ошибка!',
                'Нельзя переместить СЗ со статусом "Выполняется"',
                'на другой день!',
            ])

            // __ Откатываем изменения
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }

        // __ Находим те изменения, которые относятся к перемещаемой СЗ
        const diffsForBlockTask = diffs.find(diff => diff.isMoved || diff.isChangeChanged)
        if (!diffsForBlockTask) {
            // __ Откатываем изменения
            console.error('Не найдено изменений для перемещения СЗ')
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }

        // __ Получаем СЗ, которое перемещаем, здесь не мутируем
        const blockTask = globalBlockTasks.value.find(task => task.id === diffsForBlockTask.taskId)
        if (!blockTask) {
            // __ Откатываем изменения
            console.error('Не найдено СЗ для перемещения')
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }


        // __ Получаем дату, на которую нужно переместить СЗ
        const targetDate = additionDaysInStrFormat(
            blockTask.action_at,
            (diffsForBlockTask.dayToOffset ?? 0) - (diffsForBlockTask.dayFromOffset ?? 0)
        )

        // __ Проверяем, на даты СЗ и отгрузки
        let dateDiff = getDaysDifferenceFromDates(blockTask.order.load_at ?? targetDate, targetDate)

        // console.log('targetDate: ', targetDate)
        // console.log('blockTask.order.load_at: ', blockTask.order.load_at)
        // console.log('dateDiff: ', dateDiff)

        if (dateDiff < 0) {
            await showError([
                'Ошибка!',
                'Дата СЗ не может быть позднее даты загрузки',
                'на складе!',
                `Дата загрузки на складе: ${formatDateIntl(splitDate(blockTask.order.load_at ?? targetDate), true)}`,
            ])
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }

        // __ Проверяем, на даты СЗ и текущую дату (чтобы не было в прошлом)
        const nowDate = formatToYMD(new Date())
        dateDiff      = getDaysDifferenceFromDates(targetDate, nowDate)

        // console.log('targetDate: ', targetDate)
        // console.log('nowDate: ', nowDate)
        // console.log('dateDiff: ', dateDiff)

        if (dateDiff < 0) {
            await showError(['Ошибка!', 'Дата СЗ не может быть в прошлом!'])
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }

        // __ Проверяем, что СЗ не находится в процессе выполнения
        if (await blockStore.checkBlockTasksByStatusOnDate(splitDate(targetDate), BLOCK_TASK_STATUSES.RUNNING.ID)) {

            // __ Получаем флаг готовности к добавлению новых СЗ
            const isReady: boolean = await blockStore.readyGetBlockDay(splitDate(targetDate))

            if (!isReady) {
                // __ Если в процессе выполнения и не установлен флаг "Разрешить добавление новых СЗ"
                await showError([
                    'Ошибка!',
                    'Нельзя переместить СЗ в день, в котором',
                    'есть СЗ в процессе выполнения!',
                    'Для такого перемещения необходимо',
                    'приостановить выполнение СЗ',
                    'для добавления новых СЗ!'
                ])
                renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
                return
            }

            // __ Показываем предупреждение
            modalInfoType.value = 'primary'
            modalInfoMode.value = 'confirm'
            modalInfoText.value = [
                'СЗ будет перемещено в день,',
                'в котором есть СЗ в процессе выполнения!',
                'Перемещаемому СЗ будет установлен статус "Выполняется".',
                'Отменить это действие нельзя!',
                'Продолжить?'
            ]

            const answer = await appModalAsyncMultiline.value!.show()
            if (answer) {

                // __ Задаем статус для перемещаемого СЗ (получен по ссылке), чтобу установить его на бэке
                diffsForBlockTask.statusId = BLOCK_TASK_STATUSES.RUNNING.ID
                // console.log('diffsForBlockTask: ', diffsForBlockTask)
                // console.log('diffs: ', diffs)

                const result = await blockStore.applyChanges(diffs) // __ Применяем изменения
                // console.log('result: ', result)

                if (!checkCRUD(result)) {
                    await showError()
                    renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
                    return
                }

                return
            }

            // console.log('isReady: ', isReady)
            // console.log('diffs: ', diffs)

            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return

        }

        // // __ Проверяем, что СЗ не находится в процессе выполнения (Старый вариант)
        // if (await blockStore.checkBlockTasksByStatusOnDate(splitDate(targetDate), BLOCK_TASK_STATUSES.RUNNING.ID)) {
        //     await showError([
        //         'Ошибка!',
        //         'Нельзя переместить СЗ в день, в котором',
        //         'есть СЗ в процессе выполнения!'
        //     ])
        //     renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
        //     return
        // }

        // console.log('targetDAte: ', targetDAte)

        // __ Получаем все СЗ в целевом дне с тем же Заказом, что и у перемещаемого СЗ для проверки на объединение
        // __ Проверяем также соответствие статусов. Если одинаковые статусы, то объединяем
        const existingBlockTasks = getBlockTasksSameOrderInDay(blockTask, globalBlockTasks.value, targetDate, targetChange || '', true)

        // __ Формируем текст для модального окна
        const orderInfo = `${blockTask.order.client.short_name} №${blockTask.order.order_no_str}`

        // __ Находим количество для формирования динамического меню
        const totalAmount = blockTask.block_lines.reduce((acc, item) => acc + item.amount, 0)

        // __ Показываем модальное меню и обрабатываем результаты
        modalMenuType.value = 'primary'
        modalMenu.value     = {
            data: [
                { id: 1, title: 'Переместить все' },
                { id: 2, title: 'Переместить часть' },
                { id: 3, title: 'Отмена' },
            ],
        }

        let result = { menuItem: 1, value: true } as IModalResponse

        // __ Если количество СЗ больше 1, то показываем меню, иначе сразу перемещаем
        if (totalAmount > 1) {
            // __ Показываем модальное меню
            result = await appModalMenuTS.value!.show()
        }

        // __ 'Отмена'
        if (result.value === false || result.menuItem === 3) {
            // __ Откатываем изменения
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        } else if (result.menuItem === 1 || totalAmount === 1) {
            // __ Перемещаем все СЗ
            // !!! Логика для доработки TODO: Тут проверка на даты на возможность перемещения СЗ

            // __ Проверяем, есть ли уже СЗ в целевом дне с тем же Заказом, что и у перемещаемого СЗ
            if (existingBlockTasks.length) {
                // __ Тут ситуация, когда в целевом дне есть уже СЗ для той же Заявки
                modalInfoType.value = 'success'
                modalInfoText.value = ['Объединить СЗ для', orderInfo, 'в одно?']
                modalInfoMode.value = 'confirm'

                const result = await appModalAsyncMultiline.value!.show()

                if (result) {
                    // __ С объединением
                    // console.warn('Union BlockTasks')

                    // !!! Важен порядок параметров в функции. Основное СЗ - Куда перемещаем
                    await blockStore.applyMergeTasks([existingBlockTasks[0], blockTask]) // __ Объединяем СЗ с первой
                    // blockStore.applyMergeTasks([blockTask, ...existingBlockTasks])   // __ Объединяем все остальные
                    return
                }
            }

            await blockStore.applyChanges(diffs) // __ Применяем изменения
        } else if (result.menuItem === 2) {
            // __ Перемещаем часть СЗ в другой день
            // !!! Логика для доработки TODO: Тут проверка на даты на возможность перемещения СЗ

            taskCard.value = JSON.parse(JSON.stringify(blockTask)) // __ Копируем объект, чтобы не мутировал оригинал

            // __ Показываем модальное окно обработки СЗ
            const answer = await manageTaskCard.value!.show()
            if (!answer) {
                // __ Откатываем изменения
                renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
                return
            }

            // __ Получаем правую и левую панели
            let leftPanel  = manageTaskCard.value!.leftPanel
            let rightPanel = manageTaskCard.value!.rightPanel

            // __ Если есть правая панель, то это создание нового СЗ
            if (rightPanel.length > 0) {
                // __ Создаем новое СЗ на основе копии
                const newBlockTask = JSON.parse(JSON.stringify(blockTask))

                // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
                newBlockTask.position = (diffsForBlockTask.newTaskPosition ?? 1) - 0.1

                // __ Устанавливаем новую дату, высчитываем новую дату по смещению
                newBlockTask.action_at = additionDaysInStrFormat(
                    newBlockTask.action_at,
                    (diffsForBlockTask.dayToOffset ?? 0) - (diffsForBlockTask.dayFromOffset ?? 0)
                )

                // __ Устанавливаем id
                // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
                newBlockTask.id = 0

                // __ Проверяем, есть ли уже СЗ в целевом дне с тем же Заказом, что и у перемещаемого СЗ
                if (existingBlockTasks.length) {
                    // __ Тут ситуация, когда в целевом дне есть уже СЗ для той же Заявки
                    modalInfoType.value = 'success'
                    modalInfoText.value = ['Объединить СЗ для', orderInfo, 'в одно?']
                    modalInfoMode.value = 'confirm'

                    const result = await appModalAsyncMultiline.value!.show()

                    if (result) {
                        // __ С объединением
                        console.warn('Union BlockTasks')

                        // __ Переносим правую панель в новый СЗ
                        rightPanel               = repositionBlockTaskLines(rightPanel)
                        newBlockTask.block_lines = rightPanel

                        // __ Изменяем содержимое в СЗ
                        leftPanel = repositionBlockTaskLines(leftPanel)
                        blockStore.setBlockTasksLines(blockTask, leftPanel) // __ Делаем это в родителе

                        // !!! Важен порядок параметров в функции. Основное СЗ - Куда перемещаем
                        await blockStore.applyMergeTasks([existingBlockTasks[0], newBlockTask]) // __ Объединяем СЗ с первой
                        // blockStore.applyMergeTasks([blockTask, ...existingBlockTasks])   // __ Объединяем все остальные
                        return
                    }
                }

                // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
                await blockStore.addBlockTaskToGlobal(blockTask, leftPanel, newBlockTask, rightPanel) // __ Тут реактивное перерисовывание
            } else {
                // __ Тут ситуация, когда изменился только левая панель (разделение количества и(или) порядка)
                // __ Игнорируем это поведение и просто показываем сообщение об ошибке
                await showError(['Ошибка!', 'Правая часть не может быть пустой!'])
                // modalInfoType.value = 'danger'
                // modalInfoText.value = ['Ошибка!', 'Правая часть не может быть пустой!']
                // modalInfoMode.value = 'inform'
                // await appModalAsyncMultiline.value!.show()

                // __ Откатываем изменения
                renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))

                return
            }
        }
    }
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
    await appModalAsyncMultiline.value!.show()
}

// __ Вспомогалка. Устанавливаем статусы для СЗ
const setStatuses = async (setStatuses: IBlockTaskStatusesSet[]) => {
    if (setStatuses.length) {
        // __ Отправляем запрос на сервер
        const result = await blockStore.setBlockTasksStatuses(setStatuses)

        // __ Установка статусов на лету
        if (checkCRUD(result)) {
            // __ Получаем статусы, если не получили их ранее
            if (!globalBlockTaskStatuses.value.length) {
                await blockStore.getBlockTaskStatuses()
            }

            setStatuses.forEach(item => {
                const task   = globalBlockTasks.value.find(task => task.id === item.task)
                const status = globalBlockTaskStatuses.value.find(status => status.id === item.status)

                if (task && status) {
                    task.current_status.id    = item.status
                    task.current_status.color = status.color
                }
            })
        } else {
            await showError()
        }
    }
}

// __ Вызываем меню для дня
const actionDayMenu = async (change: IBlockTaskChangeKeys) => {
    console.log('props.day: ', props.day)

    const clearDay = clearRenderMatrixDay(props.day) as IBlockTask[]  // __ Возвращаем новый массив без пустых элементов
    const idx      = getIndexByChange(change)

    // __ Проверяем, есть ли СЗ в дне
    if (clearDay[idx].length === 0) {
        return
    }

    // __ Показываем модальное меню и обрабатываем результаты
    modalMenuType.value = 'success'
    modalMenu.value     = {
        data: [
            { id: 1, title: 'Отправить на выполнение' },
            { id: 2, title: 'Вернуть для редактирования' },
            { id: 3, title: 'Объединить СЗ для одной Заявки' },
            { id: 4, title: 'Добавить/изменить комментарий ко всем СЗ' },
            { id: 6, title: 'Отмена' },
        ],
    }

    // let result = {menuItem: null, value: false} as IModalResponse

    // __ Показываем модальное меню
    const result = await appModalMenuTS.value!.show()

    // __ Отмена + terminate
    if (result.value === false || result.menuItem === 6) {
        return
    }

    // __ Отправка на выполнение
    if (result.menuItem === 1) {

        let isTasksHasUnknownLine = false
        const setStatusesData     = clearDay[idx].flatMap((task: IBlockTask) => {
            // return { task: task.id, status: 1 }
            // __ Отправляем на выполнение то, что создано или создано при закрытии смены
            // __ и не является AVERAGE и там нет нераспределенных Столов

            // console.log('task: ', task, '----------------')
            // console.log('isTaskStatusCreated(task): ', isTaskStatusCreated(task))
            // console.log('! isTaskAverage(task): ', !isTaskAverage(task))
            // console.log('! hasTaskUnknownTable(task): ', !hasTaskUnknownTable(task))

            isTasksHasUnknownLine ||= hasTaskUnknownManufLine(task)
            if (isTaskStatusCreated(task) && !isTaskAverage(task) && !hasTaskUnknownManufLine(task)) {
                return { task: task.id, status: BLOCK_TASK_STATUSES.PENDING.ID }
            } else {
                // __ Тут не асинхронный вывод ошибки
            }

            return []
        })

        // console.log('setStatusesData:', setStatusesData)
        // console.log('clearDay[idx]:', clearDay[idx])

        if (isTasksHasUnknownLine) {
            await showError([
                'В дне присутствуют расчетные СЗ или',
                'СЗ с неопределенной Линией производства!',
            ])
            return
        }

        // if (clearDay[idx].length !== setStatusesData.length) {
        //     await showError([
        //         'В дне все СЗ уже отправлены на выполнение или',
        //         'присутствуют расчетные СЗ или',
        //         'СЗ с неопределенной Линией производства!',
        //     ])
        // }

        await setStatuses(setStatusesData)

        // __ Отправляем СЗ со статусом Pending вверх списка
        const dayClone = JSON.parse(JSON.stringify(clearDay))

        const newStatusOrders = orderBlockTasksByStatus(dayClone)
        const diffsTask       = getBlockTasksDiff(newStatusOrders[idx], clearDay[idx] as unknown as IBlockTask[])

        // console.log('newStatusOrders: ', newStatusOrders)
        // console.log('diffsTask: ', diffsTask)

        // const result = await blockStore.saveChanges(newOrders, clearDay)

        // __ Меняем реактивно позиции в отображении
        if (checkCRUD(result)) {
            diffsTask.forEach(item => {
                const task = globalBlockTasks.value.find(task => task.id === item.taskId)
                if (task && item.taskChanges?.position?.new) {
                    task.position = item.taskChanges.position.new
                }
            })
        }

        return
    }

    // __ Возврат для редактирования
    if (result.menuItem === 2) {
        const setStatusesData = clearDay[idx].flatMap((task: IBlockTask) => {
            // return { task: task.id, status: 1 }
            // __ Отправляем на выполнение то, что создано или создано при закрытии смены
            if (task.current_status.id === BLOCK_TASK_STATUSES.PENDING.ID) {
                return { task: task.id, status: BLOCK_TASK_STATUSES.CREATED.ID }
            }

            return []
        })

        await setStatuses(setStatusesData)
        return
    }

    // __ Объединение СЗ для одной Заявки
    if (result.menuItem === 3) {
        const grouped = getBlockTasksGroupedByOrder(clearDay) // __ Получаем массив массивов СЗ по одинаковым Заявкам
        // console.log('target: ', grouped)
        await blockStore.applyMergeTasksGroups(grouped)
        return
    }

    // __ Сохранение комментария
    if (result.menuItem === 4) {
        // __ Получаем день
        const blockDay = await blockStore.getBlockDayByDateAndChange(formatToYMD(props.date))
        console.log('day: ', blockDay)

        comment.value = blockDay.comment ?? '' // __ Устанавливаем комментарий
        const answer  = await commentEdit.value!.show()
        if (answer) {
            const newComment = commentEdit.value!.comment.trim()
            const result     = await blockStore.setBlockDayComment(blockDay.id, newComment)
            if (!checkCRUD(result)) {
                await showError()
                return
            }
        }

        return
    }


    throw new Error('Unknown menu item!')
}

// __ Переход в Выполнение СЗ
const gotoExecute = async () => {
    await router.push({ name: 'manufacture.cell.blocks.tasks.execute' })
}


</script>

<style scoped>
/* Стили для draggable */
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}

.chosen {
    background: #e1f5fe;
}

.drag {
    opacity: 0.8;
    cursor: grabbing;
}

/* Убедимся, что draggable контейнер занимает пространство даже когда пустой */
:deep(.sortable-chosen) {
    background-color: rgba(226, 232, 240, 0.5);
}

:deep(.sortable-ghost) {
    opacity: 0.5;
}

.plan-item {
    @apply cursor-pointer;
}
</style>
