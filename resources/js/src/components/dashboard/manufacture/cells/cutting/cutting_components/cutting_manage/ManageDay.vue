<template>
    <div
        :class="[shadowColor, globalCuttingTaskFullDaysShow ? 'min-w-[341px]' : 'min-w-[184px]']"
        class="m-1 pb-0.5 border-[1px] rounded border-slate-600 bg-slate-200 w-fit min-w-[184px] min-h-[129px] shadow-xl"
    >
        <!-- __ День недели -->
        <div class="mr-1">
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text="renderDate"
                :type="dateType"
                align="center"
                rounded="rounded-[4px]"
                text-size="small"
                width="w-full"
                @click="actionDayMenu"
            />
        </div>

        <!-- __ Шапка -->
        <div
            v-if="getTotalAmountDay || getTotalTimeDay"
            class="flex"
        >
            <!-- __ Клиент -->
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.client"
                align="center"
                rounded="rounded-[4px]"
                text="Клиент:"
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

            <!-- __ Стол 1 -->
            <AppLabelTS
                v-if="globalCuttingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.table"
                align="center"
                rounded="rounded-[4px]"
                text="1"
            />

            <!-- __ Стол 2 -->
            <AppLabelTS
                v-if="globalCuttingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.table"
                align="center"
                rounded="rounded-[4px]"
                text="2"
            />

            <!-- __ Стол 3 -->
            <AppLabelTS
                v-if="globalCuttingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.table"
                align="center"
                rounded="rounded-[4px]"
                text="3"
            />

            <!-- __ Неопознанные -->
            <AppLabelTS
                v-if="globalCuttingTaskFullDaysShow"
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

        <!-- __ Загрузки -->
        <!--<div v-for="(load, idx) of day" :key="idx">-->
        <!--    <PlanItem-->
        <!--        :columns-width="columnsWidth"-->
        <!--        :load="load"-->
        <!--    />-->
        <!--</div>-->

        <!--<div :class="fabricsStore.globalOrderManageChangeFlag ? 'opacity-50' : ''">-->
        <!-- __ Сами СЗ с возможностью перетаскивания -->
        <draggable
            :="dragOptions"
            :disabled="!isDragging"
            :list="day"
            :move="checkMove"
            class="min-h-[25px]"
            item-key="id"
            tag="div"
            @end="finishDrag"
            @start="startDrag"
        >
            <template #item="{ element, index }">
                <div
                    @click="selectCuttingTask(element)"
                    @dblclick="showCuttingTaskMenu(element)"
                >
                    <ManageItem
                        :amount-and-time="getCuttingTaskAmountAndTime(element)"
                        :columns-width="columnsWidth"
                        :index="index"
                        :item="element"
                        :order-id="globalCuttingTaskActiveOrderId"
                    />
                </div>
            </template>

            <!--<template #header>-->
            <!--    <div-->
            <!--        v-if="!day || day.length === 0"-->
            <!--        class="text-center p-4 text-slate-400 border-dashed border-2 border-slate-300 rounded m-1 cursor-pointer hover:bg-slate-50 transition-colors"-->
            <!--    >-->
            <!--        Перетащите сюда-->
            <!--    </div>-->
            <!--</template>-->

            <!--<template #footer>-->
            <!--    <div-->
            <!--        v-if="day && day.length > 0"-->
            <!--        class="text-center p-2 text-slate-400 border-dashed border-2 border-slate-300 rounded m-1 cursor-pointer hover:bg-slate-50 transition-colors"-->
            <!--    >-->
            <!--        Добавить ещё-->
            <!--    </div>-->
            <!--</template>-->
        </draggable>

        <!--</div>-->

        <!-- __ Разделительная линия -->
        <div
            v-if="getTotalAmountDay || getTotalTimeDay"
            class="flex"
        >
            <TheDividerLine/>
        </div>

        <!-- __ Итого -->
        <div
            v-if="getTotalAmountDay || getTotalTimeDay"
            class="flex"
        >
            <AppLabelTS
                :height="heightTotals"
                :type="TOTALS_TYPE"
                :width="columnsWidth.common"
                align="center"
                rounded="rounded-[4px]"
                text="Всего:"
                text-size="mini"
            />

            <!-- __ Количество + Трудозатраты Общие -->
            <ManageItemDataLabel
                :amount="getTotalAmountDay"
                :height="heightTotals"
                :reference="REFERENCE_TIME * 4"
                :time="getTotalTimeDay"
                :time-show="globalCuttingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты Стол 1 -->
            <ManageItemDataLabel
                v-if="globalCuttingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[CUTTING_TABLES.TABLE_1].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[CUTTING_TABLES.TABLE_1].time"
                :time-show="globalCuttingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты Стол 2 -->
            <ManageItemDataLabel
                v-if="globalCuttingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[CUTTING_TABLES.TABLE_2].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[CUTTING_TABLES.TABLE_2].time"
                :time-show="globalCuttingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты Стол 3 -->
            <ManageItemDataLabel
                v-if="globalCuttingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[CUTTING_TABLES.TABLE_3].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[CUTTING_TABLES.TABLE_3].time"
                :time-show="globalCuttingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты Неопознанные -->
            <ManageItemDataLabel
                v-if="globalCuttingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[CUTTING_TABLES.UNDEFINED].amount"
                :color="amountAndTimeTotalDay[CUTTING_TABLES.UNDEFINED].amount === 0 ? '' : 'red'"
                :height="heightTotals"
                :reference="null"
                :time="amountAndTimeTotalDay[CUTTING_TABLES.UNDEFINED].time"
                :time-show="globalCuttingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />
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

    <!-- __ Карточка СЗ -->
    <ManageTaskTables
        ref="manageTaskTables"
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
    IPlanMatrix, ICuttingDay,
    ICuttingTask,
    ICuttingTaskStatusesSet, ICuttingTaskLine, ICuttingLineTableSetData,
} from '@/types'

import { computed, inject, type Ref, ref } from 'vue'
import { storeToRefs } from 'pinia'
import draggable from 'vuedraggable'

import { usePlansStore } from '@/stores/PlansStore.ts'
import { useCuttingStore } from '@/stores/CuttingStore.ts'

import {
    additionDaysInStrFormat,
    formatDateInFullFormat,
    formatDateIntl,
    formatToYMD,
    getDayOfWeek,
    isHoliday,
    isToday,
    splitDate,
} from '@/app/helpers/helpers_date'
import {
    clearRenderMatrix,
    clearRenderMatrixDay,
    correctRenderMatrix,
    createAmountAndTimeObj,
    getDaysDifferenceFromCuttingDates,
    getDiffsWithPositions,
    getCuttingTaskAmountAndTime,
    getCuttingTasksDiff,
    getCuttingTasksGroupedByOrder,
    getCuttingTasksSameOrderInDay,
    isTaskAverage,
    isTaskStatusCreated, isTaskStatusRunning,
    orderCuttingTasksByStatus,
    repositionCuttingTaskLines,
    setTaskPositionInRenderMatrix, hasTaskUnknownTable,
} from '@/app/helpers/manufacture/helpers_cutting.ts'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import { ifDateInPeriod } from '@/app/helpers/plan/helpers_plan.ts'

import { CUTTING_TABLES, CUTTING_TASK_DRAFT, CUTTING_TASK_STATUSES } from '@/app/constants/cutting.ts'

import ManageItem from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageItem.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import TheDividerLine from '@/components/ui/dividers/TheDividerLine.vue'
import ManageItemDataLabel from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageItemDataLabel.vue'
import ManageTaskCard from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskCard.vue'
import AppModalMenuTS, { type IModalResponse } from '@/components/ui/modals/AppModalAsyncMenuTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'

import CommentEdit from '@/components/dashboard/manufacture/cells/cutting/cutting_components/common/CommentEdit.vue'
import ManageTaskTables from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskTables.vue'

// type IDay = ICuttingTask & IPlanMatrixDayItem

interface IProps {
    date: Date
    day?: IDay[],
}

const props = withDefaults(defineProps<IProps>(), {
    day: () => [],
})

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
const cuttingStore = useCuttingStore()

const {
          globalCuttingTaskTimesShow,
          globalCuttingTaskFullDaysShow,
          /*globalDiffs,*/
          globalCuttingTasks,
          globalCuttingTaskActiveOrderId,
          globalCuttingTaskStatuses,
      } = storeToRefs(cuttingStore)

const planStore            = usePlansStore()
const { planPeriodGlobal } = storeToRefs(planStore)

const DEFAULT_HEIGHT = 'h-[25px]'

const TOTALS_TYPE: IColorTypes = 'stone'
const DATA_HEADER_TEXT_SIZE    = 'mini'
const REFERENCE_TIME           = 10.5 * 60 * 60 // в секунды

// __ Высота под Итого
const heightTotals = computed(() => (globalCuttingTaskTimesShow.value ? 'h-[80px]' : 'h-[40px]'))

// __ Дата
const renderDate = computed(() => formatDateInFullFormat(props.date, !globalCuttingTaskFullDaysShow.value) + ` (${getDayOfWeek(props.date)})`)
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
const amountAndTimeTotalDay = computed(() => {
    //  __ Создаем сам объект данных с ключами из CUTTING_MACHINES и {time: 0, amount: 0} и инициализируем его нулями
    const amountAndTimeObj = createAmountAndTimeObj()

    props.day.forEach(cuttingTask => {
        const amountAndTime = getCuttingTaskAmountAndTime(cuttingTask as ICuttingTask)

        Object.entries(amountAndTime).forEach(([key, value]) => {
            amountAndTimeObj[key].amount += value.amount
            amountAndTimeObj[key].time += value.time
        })
    })

    return amountAndTimeObj
})

// __ Общее Количество за день
const getTotalAmountDay = computed(() => props.day.reduce((totalAcc, task) =>
    totalAcc + task.cutting_lines.reduce((acc: number, line: ICuttingTaskLine) => acc + line.amount, 0), 0))

// __ Общие Трудозатраты за день
const getTotalTimeDay = computed(() =>
    props.day.reduce((totalAcc, task) => totalAcc + task.cutting_lines.reduce((acc: number, line: ICuttingTaskLine) => acc + line.time, 0), 0))

// __ Тип для Каротчки и Изменения стола
const modalType        = ref<IColorTypes>('primary')
const modalText        = ref<string>('')
const modalMode        = ref<'inform' | 'confirm'>('inform')
const manageTaskCard   = ref<InstanceType<typeof ManageTaskCard> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией
const manageTaskTables = ref<InstanceType<typeof ManageTaskTables> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального Меню
const modalMenuType  = ref<IColorTypes>('primary')
const modalMenu      = ref<IModalAsyncMenu>({ data: [] })
const appModalMenuTS = ref<InstanceType<typeof AppModalMenuTS> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна изменения Комментария
const comment     = ref('')
const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Установка активного Заказа
const selectCuttingTask = (cuttingTask: ICuttingTask) => {
    globalCuttingTaskActiveOrderId.value = cuttingTask.order.id
}

// __ Карточка СЗ
const taskCard = ref<ICuttingTask>(CUTTING_TASK_DRAFT)

const showCuttingTaskCard = async (cuttingTask: ICuttingTask) => {
    taskCard.value = JSON.parse(JSON.stringify(cuttingTask)) // __ Копируем объект, чтобы не мутировал оригинал

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
        const newCuttingTask = JSON.parse(JSON.stringify(cuttingTask))

        // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
        newCuttingTask.position += 0.1

        // __ Устанавливаем id
        // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
        newCuttingTask.id = 0

        // __ Пересчитываем позиции для строк СЗ (CuttingLines[])
        // leftPanel  = repositionCuttingTaskLines(leftPanel)
        // rightPanel = repositionCuttingTaskLines(rightPanel)

        // __ Обновляем глобальный state СЗ
        // cuttingTask.cutting_lines    = leftPanel              // __ Тут передача по ссылке, автоматическое изменение
        // newCuttingTask.cutting_lines = rightPanel

        // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
        await cuttingStore.addCuttingTaskToGlobal(cuttingTask, leftPanel, newCuttingTask, rightPanel) // __ Тут реактивное перерисовывание

        // console.log(taskCard.value)
    } else {
        // __ Тут ситуация, когда изменился только левая панель (разделение количества и(или) порядка)

        // __ Пересчитываем позиции для строк СЗ (CuttingLines[])
        // leftPanel = repositionCuttingTaskLines(leftPanel)

        // __ Обновляем глобальный state СЗ
        await cuttingStore.addCuttingTaskToGlobal(cuttingTask, leftPanel) // __ Тут реактивное перерисовывание
    }
}

// __ Изменение стола
const showCuttingTaskTables = async (cuttingTask: ICuttingTask) => {
    // __ Копируем объект, чтобы не мутировал оригинал
    taskCard.value = JSON.parse(JSON.stringify(cuttingTask))
    // __ Добавляем метаданные Заявки в каждую строку
    taskCard.value.cutting_lines.forEach(line => line.order_meta = `${taskCard.value.order.client.short_name} №${taskCard.value.order.order_no_str}`)


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
        await appModalAsyncMultiline.value!.show()

    } else {
        await showError()
    }





    // // __ Если есть правая панель, то это создание нового СЗ
    // if (rightPanel.length > 0) {
    //     // __ Создаем новое СЗ на основе копии
    //     const newCuttingTask = JSON.parse(JSON.stringify(cuttingTask))
    //
    //     // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
    //     newCuttingTask.position += 0.1
    //
    //     // __ Устанавливаем id
    //     // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
    //     newCuttingTask.id = 0
    //
    //     // __ Пересчитываем позиции для строк СЗ (CuttingLines[])
    //     // leftPanel  = repositionCuttingTaskLines(leftPanel)
    //     // rightPanel = repositionCuttingTaskLines(rightPanel)
    //
    //     // __ Обновляем глобальный state СЗ
    //     // cuttingTask.cutting_lines    = leftPanel              // __ Тут передача по ссылке, автоматическое изменение
    //     // newCuttingTask.cutting_lines = rightPanel
    //
    //     // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
    //     await cuttingStore.addCuttingTaskToGlobal(cuttingTask, leftPanel, newCuttingTask, rightPanel) // __ Тут реактивное перерисовывание
    //
    //     // console.log(taskCard.value)
    // } else {
    //     // __ Тут ситуация, когда изменился только левая панель (разделение количества и(или) порядка)
    //
    //     // __ Пересчитываем позиции для строк СЗ (CuttingLines[])
    //     // leftPanel = repositionCuttingTaskLines(leftPanel)
    //
    //     // __ Обновляем глобальный state СЗ
    //     await cuttingStore.addCuttingTaskToGlobal(cuttingTask, leftPanel) // __ Тут реактивное перерисовывание
    // }
}


// __ Меню при двойном клике на Заявке (Разделить количество + Изменить стол)
const showCuttingTaskMenu = async (cuttingTask: ICuttingTask) => {
    // __ Показываем модальное меню при двойном клике на Заявке обрабатываем результаты
    modalMenuType.value = 'indigo'
    modalMenu.value     = {
        data: [
            { id: 1, title: 'Разделить количество' },
            { id: 2, title: 'Изменить стол' },
            { id: 3, title: 'Отмена' },
        ],
    }

    const result = await appModalMenuTS.value!.show()
    // let result = { menuItem: 3, value: true } as IModalResponse

    // __ Отмена
    if (result.menuItem === 3 && result.value) {
        return
    }

    // __ Разделить количество
    if (result.menuItem === 1 && result.value) {
        await showCuttingTaskCard(cuttingTask)
        return
    }

    // __ Изменить стол
    if (result.menuItem === 2 && result.value) {
        await showCuttingTaskTables(cuttingTask)
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

const checkMove = (evt: any) => {
    // return true
    // console.log('checkMove: ', evt)
    const movedElement = evt.draggedContext.element
    // console.log(movedElement)
    // return true
    // __ Проверяем, что перемещаемый элемент со статусом 'Создано' или 'Выполняется' но внутри одного дня
    if (!isTaskStatusCreated(movedElement) && !isTaskStatusRunning(movedElement)) {
        return false
    }

    // __ Проверяем, что перемещаемый элемент не в прошлом
    const nowDate  = formatToYMD(new Date())
    const dateDiff = getDaysDifferenceFromCuttingDates(movedElement.action_at, nowDate)

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
const finishDrag = async (evt: any) => {
    // const element = evt.item._underlying_vm_
    // console.log('evt: ', evt)

    // __ Выясняем, что перетаскивали и куда перемещали
    let renderMatrixCloned = JSON.parse(JSON.stringify(renderMatrix.value))
    renderMatrixCloned     = clearRenderMatrix(renderMatrixCloned)
    renderMatrixCloned     = setTaskPositionInRenderMatrix(renderMatrixCloned)

    // console.log('renderMatrixCleared: ', renderMatrixCloned)
    // console.log('renderMatrixCopy: ', renderMatrixCopy.value)

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

    // console.log('isOneDayAction: ', isOneDayAction)

    // __ Получаем сам перемещаемый элемент
    const movedElement = evt.item._underlying_vm_

    if (isOneDayAction) {

        console.log('movedElement: ', movedElement)

        // __ Если перемещаемый элемент со статусом 'Выполняется', проверяем маячок,
        // __ который указывает на готовность к добавлению СЗ
        if (isTaskStatusRunning(movedElement)) {

            // __ Получаем флаг готовности к добавлению новых СЗ
            const isReady: ICuttingDay = await cuttingStore.readyGetCuttingDay(splitDate(movedElement.action_at))

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
        await cuttingStore.applyChanges(diffs) // __ Применяем изменения

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
        const diffsForCuttingTask = diffs.find(diff => diff.isMoved)
        if (!diffsForCuttingTask) {
            // __ Откатываем изменения
            console.error('Не найдено изменений для перемещения СЗ')
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }

        // __ Получаем СЗ, которое перемещаем, здесь не мутируем
        const cuttingTask = globalCuttingTasks.value.find(task => task.id === diffsForCuttingTask.taskId)
        if (!cuttingTask) {
            // __ Откатываем изменения
            console.error('Не найдено СЗ для перемещения')
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }

        // __ Получаем дату, на которую нужно переместить СЗ
        const targetDate = additionDaysInStrFormat(
            cuttingTask.action_at,
            (diffsForCuttingTask.dayToOffset ?? 0) - (diffsForCuttingTask.dayFromOffset ?? 0)
        )

        // __ Проверяем, на даты СЗ и отгрузки
        let dateDiff = getDaysDifferenceFromCuttingDates(cuttingTask.order.load_at ?? targetDate, targetDate)

        // console.log('targetDate: ', targetDate)
        // console.log('cuttingTask.order.load_at: ', cuttingTask.order.load_at)
        // console.log('dateDiff: ', dateDiff)

        if (dateDiff < 0) {
            await showError([
                'Ошибка!',
                'Дата СЗ не может быть позднее даты загрузки',
                'на складе!',
                `Дата загрузки на складе: ${formatDateIntl(splitDate(cuttingTask.order.load_at ?? targetDate), true)}`,
            ])
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }

        // __ Проверяем, на даты СЗ и текущую дату (чтобы не было в прошлом)
        const nowDate = formatToYMD(new Date())
        dateDiff      = getDaysDifferenceFromCuttingDates(targetDate, nowDate)

        // console.log('targetDate: ', targetDate)
        // console.log('nowDate: ', nowDate)
        // console.log('dateDiff: ', dateDiff)

        if (dateDiff < 0) {
            await showError(['Ошибка!', 'Дата СЗ не может быть в прошлом!'])
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }

        // __ Проверяем, что СЗ не находится в процессе выполнения
        if (await cuttingStore.checkCuttingTasksByStatusOnDate(splitDate(targetDate), CUTTING_TASK_STATUSES.RUNNING.ID)) {

            // __ Получаем флаг готовности к добавлению новых СЗ
            const isReady: boolean = await cuttingStore.readyGetCuttingDay(splitDate(targetDate))

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
                diffsForCuttingTask.statusId = CUTTING_TASK_STATUSES.RUNNING.ID
                // console.log('diffsForCuttingTask: ', diffsForCuttingTask)
                // console.log('diffs: ', diffs)

                const result = await cuttingStore.applyChanges(diffs) // __ Применяем изменения
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
        // if (await cuttingStore.checkCuttingTasksByStatusOnDate(splitDate(targetDate), CUTTING_TASK_STATUSES.RUNNING.ID)) {
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
        const existingCuttingTasks = getCuttingTasksSameOrderInDay(cuttingTask, globalCuttingTasks.value, targetDate, true)

        // __ Формируем текст для модального окна
        const orderInfo = `${cuttingTask.order.client.short_name} №${cuttingTask.order.order_no_str}`

        // __ Находим количество для формирования динамического меню
        const totalAmount = cuttingTask.cutting_lines.reduce((acc, item) => acc + item.amount, 0)

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
            if (existingCuttingTasks.length) {
                // __ Тут ситуация, когда в целевом дне есть уже СЗ для той же Заявки
                modalInfoType.value = 'success'
                modalInfoText.value = ['Объединить СЗ для', orderInfo, 'в одно?']
                modalInfoMode.value = 'confirm'

                const result = await appModalAsyncMultiline.value!.show()

                if (result) {
                    // __ С объединением
                    // console.warn('Union CuttingTasks')

                    // !!! Важен порядок параметров в функции. Основное СЗ - Куда перемещаем
                    await cuttingStore.applyMergeTasks([existingCuttingTasks[0], cuttingTask]) // __ Объединяем СЗ с первой
                    // cuttingStore.applyMergeTasks([cuttingTask, ...existingCuttingTasks])   // __ Объединяем все остальные
                    return
                }
            }

            await cuttingStore.applyChanges(diffs) // __ Применяем изменения
        } else if (result.menuItem === 2) {
            // __ Перемещаем часть СЗ в другой день
            // !!! Логика для доработки TODO: Тут проверка на даты на возможность перемещения СЗ

            taskCard.value = JSON.parse(JSON.stringify(cuttingTask)) // __ Копируем объект, чтобы не мутировал оригинал

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
                const newCuttingTask = JSON.parse(JSON.stringify(cuttingTask))

                // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
                newCuttingTask.position = (diffsForCuttingTask.newTaskPosition ?? 1) - 0.1

                // __ Устанавливаем новую дату, высчитываем новую дату по смещению
                newCuttingTask.action_at = additionDaysInStrFormat(
                    newCuttingTask.action_at,
                    (diffsForCuttingTask.dayToOffset ?? 0) - (diffsForCuttingTask.dayFromOffset ?? 0)
                )

                // __ Устанавливаем id
                // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
                newCuttingTask.id = 0

                // __ Проверяем, есть ли уже СЗ в целевом дне с тем же Заказом, что и у перемещаемого СЗ
                if (existingCuttingTasks.length) {
                    // __ Тут ситуация, когда в целевом дне есть уже СЗ для той же Заявки
                    modalInfoType.value = 'success'
                    modalInfoText.value = ['Объединить СЗ для', orderInfo, 'в одно?']
                    modalInfoMode.value = 'confirm'

                    const result = await appModalAsyncMultiline.value!.show()

                    if (result) {
                        // __ С объединением
                        console.warn('Union CuttingTasks')

                        // __ Переносим правую панель в новый СЗ
                        rightPanel                   = repositionCuttingTaskLines(rightPanel)
                        newCuttingTask.cutting_lines = rightPanel

                        // __ Изменяем содержимое в СЗ
                        leftPanel = repositionCuttingTaskLines(leftPanel)
                        cuttingStore.setCuttingTasksLines(cuttingTask, leftPanel) // __ Делаем это в родителе

                        // !!! Важен порядок параметров в функции. Основное СЗ - Куда перемещаем
                        await cuttingStore.applyMergeTasks([existingCuttingTasks[0], newCuttingTask]) // __ Объединяем СЗ с первой
                        // cuttingStore.applyMergeTasks([cuttingTask, ...existingCuttingTasks])   // __ Объединяем все остальные
                        return
                    }
                }

                // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
                await cuttingStore.addCuttingTaskToGlobal(cuttingTask, leftPanel, newCuttingTask, rightPanel) // __ Тут реактивное перерисовывание
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
const setStatuses = async (setStatuses: ICuttingTaskStatusesSet[]) => {
    if (setStatuses.length) {
        // __ Отправляем запрос на сервер
        const result = await cuttingStore.setCuttingTasksStatuses(setStatuses)

        // __ Установка статусов на лету
        if (checkCRUD(result)) {
            // __ Получаем статусы, если не получили их ранее
            if (!globalCuttingTaskStatuses.value.length) {
                await cuttingStore.getCuttingTaskStatuses()
            }

            setStatuses.forEach(item => {
                const task   = globalCuttingTasks.value.find(task => task.id === item.task)
                const status = globalCuttingTaskStatuses.value.find(status => status.id === item.status)

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
const actionDayMenu = async () => {
    console.log('props.day: ', props.day)

    const clearDay = clearRenderMatrixDay(props.day) as ICuttingTask[]  // __ Возвращаем новый массив без пустых элементов

    // __ Проверяем, есть ли СЗ в дне
    if (!clearDay.length) {
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
            { id: 5, title: 'Отмена' },
        ],
    }

    // let result = {menuItem: null, value: false} as IModalResponse

    // __ Показываем модальное меню
    const result = await appModalMenuTS.value!.show()

    // __ Отмена + terminate
    if (result.value === false || result.menuItem === 5) {
        return
    }

    // __ Отправка на выполнение
    if (result.menuItem === 1) {
        const setStatusesData = clearDay.flatMap(task => {
            // return { task: task.id, status: 1 }
            // __ Отправляем на выполнение то, что создано или создано при закрытии смены
            // __ и не является AVERAGE и там нет нераспределенных Столов
            if (isTaskStatusCreated(task) && !isTaskAverage(task) && !hasTaskUnknownTable(task)) {
                return { task: task.id, status: CUTTING_TASK_STATUSES.PENDING.ID }
            } else {
                // __ Тут не асинхронный вывод ошибки
            }

            return []
        })

        if (clearDay.length !== setStatusesData.length) {
            await showError([
                'В дне присутствуют расчетные СЗ или',
                'СЗ с неопределенным раскройным столом!',
            ])
        }

        await setStatuses(setStatusesData)

        // __ Отправляем СЗ со статусом Pending вверх списка
        const dayClone = JSON.parse(JSON.stringify(clearDay))

        const newOrders = orderCuttingTasksByStatus(dayClone)
        const diffsTask = getCuttingTasksDiff(newOrders, clearDay)

        const result = await cuttingStore.saveChanges(newOrders, clearDay)

        // __ Меняем реактивно позиции в отображении
        if (checkCRUD(result)) {
            diffsTask.forEach(item => {
                const task = globalCuttingTasks.value.find(task => task.id === item.taskId)
                if (task && item.taskChanges?.position?.new) {
                    task.position = item.taskChanges.position.new
                }
            })
        }

        return
    }

    // __ Возврат для редактирования
    if (result.menuItem === 2) {
        const setStatusesData = clearDay.flatMap(task => {
            // return { task: task.id, status: 1 }
            // __ Отправляем на выполнение то, что создано или создано при закрытии смены
            if (task.current_status.id === CUTTING_TASK_STATUSES.PENDING.ID) {
                return { task: task.id, status: CUTTING_TASK_STATUSES.CREATED.ID }
            }

            return []
        })

        await setStatuses(setStatusesData)
        return
    }

    // __ Объединение СЗ для одной Заявки
    if (result.menuItem === 3) {
        const grouped = getCuttingTasksGroupedByOrder(clearDay) // __ Получаем массив массивов СЗ по одинаковым Заявкам
        // console.log('target: ', grouped)
        await cuttingStore.applyMergeTasksGroups(grouped)
        return
    }

    // __ Сохранение комментария
    if (result.menuItem === 4) {
        // __ Получаем день
        const cuttingDay = await cuttingStore.getCuttingDayByDateAndChange(formatToYMD(props.date))
        console.log('day: ', cuttingDay)

        comment.value = cuttingDay.comment ?? '' // __ Устанавливаем комментарий
        const answer  = await commentEdit.value!.show()
        if (answer) {
            const newComment = commentEdit.value!.comment.trim()
            const result     = await cuttingStore.setCuttingDayComment(cuttingDay.id, newComment)
            if (!checkCRUD(result)) {
                await showError()
                return
            }
        }

        return
    }

    throw new Error('Unknown menu item!')
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
