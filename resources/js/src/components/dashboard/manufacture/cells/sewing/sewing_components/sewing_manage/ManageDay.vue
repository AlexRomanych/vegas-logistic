<template>

    <div
        :class="[shadowColor, globalSewingTaskFullDaysShow ? 'min-w-[379px]' : 'min-w-[184px]']"
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
            />
        </div>

        <!-- __ Шапка -->
        <div v-if="getTotalAmountDay || getTotalTimeDay" class="flex">

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

            <!-- __ УШМ -->
            <AppLabelTS
                v-if="globalSewingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.machine"
                align="center"
                rounded="rounded-[4px]"
                text="У"
            />

            <!-- __ АШМ -->
            <AppLabelTS
                v-if="globalSewingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.machine"
                align="center"
                rounded="rounded-[4px]"
                text="А"
            />

            <!-- __ Глухие сложные -->
            <AppLabelTS
                v-if="globalSewingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.machine"
                align="center"
                rounded="rounded-[4px]"
                text="ГС"
            />

            <!-- __ Глухие простые -->
            <AppLabelTS
                v-if="globalSewingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.machine"
                align="center"
                rounded="rounded-[4px]"
                text="ГП"
            />

            <!-- __ Глухие Неопознанные -->
            <AppLabelTS
                v-if="globalSewingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.machine"
                align="center"
                color="red"
                rounded="rounded-[4px]"
                text="Н"
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
                    @click="selectSewingTask(element)"
                    @dblclick="showSewingTaskCard(element)"
                >
                    <ManageItem
                        :amount-and-time="getSewingTaskAmountAndTime(element)"
                        :columns-width="columnsWidth"
                        :index="index"
                        :item="element"
                        :order-id="globalSewingTaskActiveOrderId"
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
        <div v-if="getTotalAmountDay || getTotalTimeDay" class="flex">
            <TheDividerLine/>
        </div>

        <!-- __ Итого -->
        <div v-if="getTotalAmountDay || getTotalTimeDay" class="flex">
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
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"

                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты УШМ -->
            <ManageItemDataLabel
                v-if="globalSewingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[SEWING_MACHINES.UNIVERSAL].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[SEWING_MACHINES.UNIVERSAL].time"
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты АШМ -->
            <ManageItemDataLabel
                v-if="globalSewingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[SEWING_MACHINES.AUTO].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[SEWING_MACHINES.AUTO].time"
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты Solid Hard -->
            <ManageItemDataLabel
                v-if="globalSewingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[SEWING_MACHINES.SOLID_HARD].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[SEWING_MACHINES.SOLID_HARD].time"
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты Solid Lite -->
            <ManageItemDataLabel
                v-if="globalSewingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[SEWING_MACHINES.SOLID_LITE].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[SEWING_MACHINES.SOLID_LITE].time"
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты Неопознанные -->
            <ManageItemDataLabel
                v-if="globalSewingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[SEWING_MACHINES.UNDEFINED].amount"
                :color="amountAndTimeTotalDay[SEWING_MACHINES.UNDEFINED].amount === 0 ? '' : 'red'"
                :height="heightTotals"
                :reference="null"
                :time="amountAndTimeTotalDay[SEWING_MACHINES.UNDEFINED].time"
                :time-show="globalSewingTaskTimesShow"
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
    />


</template>

<!--suppress PointlessBooleanExpressionJS, PointlessBooleanExpressionJS -->
<script lang="ts" setup>
import type {
    IColorTypes, IModalAsyncMenu, IPlanMatrix,
    IPlanMatrixDayItem,
    ISewingTask,
    // ISewingTaskLine,
    // IAmountAndTime,
    // ISewingMachineKeys,
    // IPlanMatrixDay,
    // ISewingTaskLine
} from '@/types'

import { computed, inject, type Ref, ref, } from 'vue'
import { storeToRefs } from 'pinia'
import draggable from 'vuedraggable'

import { usePlansStore } from '@/stores/PlansStore.ts'
import { useSewingStore } from '@/stores/SewingStore.ts'

import { additionDaysInStrFormat, formatDateInFullFormat, isHoliday, isToday } from '@/app/helpers/helpers_date'
import { ifDateInPeriod } from '@/app/helpers/plan/helpers_plan.ts'
import {
    clearRenderMatrix,
    correctRenderMatrix,
    createAmountAndTimeObj,
    getDiffsWithPositions,
    getSewingTaskAmountAndTime,
    getSewingTasksSameOrderInDay,
    repositionSewingTaskLines,
    setTaskPositionInRenderMatrix
} from '@/app/helpers/manufacture/helpers_sewing.ts'

import { SEWING_MACHINES, SEWING_TASK_DRAFT } from '@/app/constants/sewing.ts'

import ManageItem from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageItem.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import TheDividerLine from '@/components/ui/dividers/TheDividerLine.vue'
import ManageItemDataLabel
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageItemDataLabel.vue'
import ManageTaskCard
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCard.vue'
import AppModalMenuTS, { type IModalResponse } from '@/components/ui/modals/AppModalAsyncMenuTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'


type IDay = ISewingTask & IPlanMatrixDayItem

interface IProps {
    date: Date,
    day?: IDay[]
}


const props = withDefaults(defineProps<IProps>(), {
    day: () => [],
})

// const emits = defineEmits<{
//     (e: 'drag-and-drop'): void,
// }>()

// __ Получаем данные из родителя
// const renderMatrix = inject('renderMatrix', [])
const renderMatrix     = inject<Ref<IPlanMatrix>>('renderMatrix', ref([]))
const renderMatrixCopy = inject<Ref<IPlanMatrix>>('renderMatrixCopy', ref([]))

// console.log('renderMatrix: ', renderMatrix)
// console.log('renderMatrixCopy: ', renderMatrixCopy)

// __ Данные из Хранилища
const sewingStore = useSewingStore()

const { globalSewingTaskTimesShow, globalSewingTaskFullDaysShow, /*globalDiffs,*/ globalSewingTasks, globalSewingTaskActiveOrderId } = storeToRefs(sewingStore)

const planStore            = usePlansStore()
const { planPeriodGlobal } = storeToRefs(planStore)


const DEFAULT_HEIGHT = 'h-[25px]'

const TOTALS_TYPE: IColorTypes = 'stone'
const DATA_HEADER_TEXT_SIZE    = 'mini'
const REFERENCE_TIME           = 10.5 * 60 * 60 // в секунды


// __ Высота под Итого
const heightTotals = computed(() => globalSewingTaskTimesShow.value ? 'h-[80px]' : 'h-[40px]')

// __ Дата
const renderDate = computed(() => formatDateInFullFormat(props.date, !globalSewingTaskFullDaysShow.value))
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
    client:  'w-[90px]',
    orderNo: 'w-[45px]',
    amount:  'w-[35px]',
    common:  'w-[139px]',
    machine: 'w-[35px]',
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

    //  __ Создаем сам объект данных с ключами из SEWING_MACHINES и {time: 0, amount: 0} и инициализируем его нулями
    const amountAndTimeObj = createAmountAndTimeObj()

    props.day.forEach(sewingTask => {
        const amountAndTime = getSewingTaskAmountAndTime(sewingTask)
        // const amountAndTime = getAmountAndTime(sewingTask)

        Object.entries(amountAndTime).forEach(([key, value]) => {
            amountAndTimeObj[key].amount += value.amount
            amountAndTimeObj[key].time += value.time
        })
    })

    return amountAndTimeObj
})

// __ Общее Количество за день
const getTotalAmountDay =
          computed(() => Object.values(amountAndTimeTotalDay.value).reduce((acc, item) => item.amount + acc, 0))

// __ Общие Трудозатраты за день
const getTotalTimeDay =
          computed(() => Object.values(amountAndTimeTotalDay.value).reduce((acc, item) => item.time + acc, 0))


// __ Тип для модального окна
const modalType      = ref<IColorTypes>('primary')
const modalText      = ref<string>('')
const modalMode      = ref<'inform' | 'confirm'>('inform')
const manageTaskCard = ref<InstanceType<typeof ManageTaskCard> | null>(null)         // Получаем ссылку на модальное окно с асинхронной функцией


// __ Тип для модального Меню
const modalMenuType  = ref<IColorTypes>('primary')
const modalMenu      = ref<IModalAsyncMenu>({ data: [] })
const appModalMenuTS = ref<InstanceType<typeof AppModalMenuTS> | null>(null)         // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией


// __ Установка активного Заказа
const selectSewingTask = (sewingTask: ISewingTask) => {
    globalSewingTaskActiveOrderId.value = sewingTask.order.id
}


// __ Карточка СЗ
const taskCard = ref<ISewingTask>(SEWING_TASK_DRAFT)

const showSewingTaskCard = async (sewingTask: ISewingTask) => {
    taskCard.value = JSON.parse(JSON.stringify(sewingTask))   // __ Копируем объект, чтобы не мутировал оригинал

    // __ Показываем модальное окно обработки СЗ
    const answer = await manageTaskCard.value!.show()
    if (!answer) {
        return
    }

    // __ Получаем ссылки на панели
    let leftPanel  = manageTaskCard.value!.leftPanel
    let rightPanel = manageTaskCard.value!.rightPanel


    // __ Если есть правая панель, то это создание нового СЗ
    if (rightPanel.length > 0) {

        // __ Создаем новое СЗ на основе копии
        const newSewingTask = JSON.parse(JSON.stringify(sewingTask))

        // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
        newSewingTask.position += 0.1

        // __ Устанавливаем id
        // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
        newSewingTask.id = 0

        // __ Пересчитываем позиции для строк СЗ (SewingLines[])
        // leftPanel  = repositionSewingTaskLines(leftPanel)
        // rightPanel = repositionSewingTaskLines(rightPanel)

        // __ Обновляем глобальный state СЗ
        // sewingTask.sewing_lines    = leftPanel              // __ Тут передача по ссылке, автоматическое изменение
        // newSewingTask.sewing_lines = rightPanel

        // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
        sewingStore.addSewingTaskToGlobal(sewingTask, leftPanel, newSewingTask, rightPanel)    // __ Тут реактивное перерисовывание

        // console.log(taskCard.value)
    } else {
        // __ Тут ситуация, когда изменился только левая панель (разделение количества и(или) порядка)

        // __ Пересчитываем позиции для строк СЗ (SewingLines[])
        // leftPanel = repositionSewingTaskLines(leftPanel)

        // __ Обновляем глобальный state СЗ
        sewingStore.addSewingTaskToGlobal(sewingTask, leftPanel)    // __ Тут реактивное перерисовывание

    }


}


// --- ------------------------------------------------------
// --- ----------- Управление Druggable ---------------------
// --- ------------------------------------------------------
// __ Опции для draggable
const dragOptions = computed(() => {
    return {
        animation:   300,
        group:       'orders',
        ghostClass:  'ghost',
        dragClass:   'drag',
        chosenClass: 'chosen',
        // sort: true,
        // disabled: false, // Выносим в отдельное свойство
    }
})
const isDragging  = ref(true)

const checkMove = (evt: any) => {
    return true
}


// __ Начало перетаскивания СЗ
const startDrag = (evt: any) => {
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


    // __ Проверяем, переместились ли СЗ в рамках одного дня или нет
    const isOneDayAction = !diffs.some(diff => diff.isMoved)

    console.log('isOneDayAction: ', isOneDayAction)

    if (isOneDayAction) {

        // __ Перемещаем СЗ без вывода дополнительной информации
        sewingStore.applyChanges(diffs)              // __ Применяем изменения

    } else {

        // __ Находим те изменения, которые относятся к перемещаемой СЗ
        const diffsForSewingTask = diffs.find(diff => diff.isMoved)
        if (!diffsForSewingTask) {
            // __ Откатываем изменения
            console.error('Не найдено изменений для перемещения СЗ')
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }

        // __ Получаем СЗ, которое перемещаем, здесь не мутируем
        const sewingTask = globalSewingTasks.value.find(task => task.id === diffsForSewingTask.taskId)
        if (!sewingTask) {
            // __ Откатываем изменения
            console.error('Не найдено СЗ для перемещения')
            renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))
            return
        }

        // __ Получаем дату, на которую нужно переместить СЗ
        const targetDAte = additionDaysInStrFormat(
            sewingTask.action_at,
            (diffsForSewingTask.dayToOffset ?? 0) - (diffsForSewingTask.dayFromOffset ?? 0))

        // __ Получаем все СЗ в целевом дне с тем же Заказом, что и у перемещаемого СЗ для проверки на объединение
        const existingSewingTasks = getSewingTasksSameOrderInDay(sewingTask, globalSewingTasks.value, targetDAte)

        // __ Формируем текст для модального окна
        const orderInfo = `${sewingTask.order.client.short_name} №${sewingTask.order.order_no_str}`

        // __ Находим количество для формирования динамического меню
        const totalAmount = sewingTask.sewing_lines.reduce((acc, item) => acc + item.amount, 0)

        // __ Показываем модальное меню и обрабатываем результаты
        modalMenuType.value = 'primary'
        modalMenu.value     = {
            data: [
                { id: 1, title: 'Переместить все' },
                { id: 2, title: 'Переместить часть' },
                { id: 3, title: 'Отмена' },
            ]
        }

        let result = {menuItem: 1, value: true} as IModalResponse

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
            if (existingSewingTasks.length) {

                // __ Тут ситуация, когда в целевом дне есть уже СЗ для той же Заявки
                modalInfoType.value = 'success'
                modalInfoText.value = ['Объединить СЗ для', orderInfo, 'в одно?']
                modalInfoMode.value = 'confirm'

                const result = await appModalAsyncMultiline.value!.show()

                if (result) {
                    // __ С объединением
                    console.warn('Union SewingTasks')

                    // !!! Важен порядок параметров в функции. Основное СЗ - Куда перемещаем
                    sewingStore.applyMergeTasks([existingSewingTasks[0], sewingTask])   // __ Объединяем СЗ с первой
                    // sewingStore.applyMergeTasks([sewingTask, ...existingSewingTasks])   // __ Объединяем все остальные
                    return
                }
            }

            sewingStore.applyChanges(diffs)         // __ Применяем изменения

        } else if (result.menuItem === 2) {

            // __ Перемещаем часть СЗ в другой день
            // !!! Логика для доработки TODO: Тут проверка на даты на возможность перемещения СЗ


            taskCard.value = JSON.parse(JSON.stringify(sewingTask)) // __ Копируем объект, чтобы не мутировал оригинал

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
                const newSewingTask = JSON.parse(JSON.stringify(sewingTask))

                // __ Увеличиваем позицию на 0.1 (смещаем вниз относительно предыдущего элемента)
                newSewingTask.position = (diffsForSewingTask.newTaskPosition ?? 1) - 0.1

                // __ Устанавливаем новую дату, высчитываем новую дату по смещению
                newSewingTask.action_at = additionDaysInStrFormat(
                    newSewingTask.action_at,
                    (diffsForSewingTask.dayToOffset ?? 0) - (diffsForSewingTask.dayFromOffset ?? 0))

                // __ Устанавливаем id
                // __ Тут именно 0, т.к. id = 0 - это заглушка для добавления нового элемента и там стоит проверка при рендере
                newSewingTask.id = 0

                // __ Проверяем, есть ли уже СЗ в целевом дне с тем же Заказом, что и у перемещаемого СЗ
                if (existingSewingTasks.length) {

                    // __ Тут ситуация, когда в целевом дне есть уже СЗ для той же Заявки
                    modalInfoType.value = 'success'
                    modalInfoText.value = ['Объединить СЗ для', orderInfo, 'в одно?']
                    modalInfoMode.value = 'confirm'

                    const result = await appModalAsyncMultiline.value!.show()

                    if (result) {
                        // __ С объединением
                        console.warn('Union SewingTasks')

                        // __ Переносим правую панель в новый СЗ
                        rightPanel = repositionSewingTaskLines(rightPanel)
                        newSewingTask.sewing_lines = rightPanel

                        // __ Изменяем содержимое в СЗ
                        leftPanel = repositionSewingTaskLines(leftPanel)
                        sewingStore.setSewingTasksLines(sewingTask, leftPanel)  // __ Делаем это в родителе

                        // !!! Важен порядок параметров в функции. Основное СЗ - Куда перемещаем
                        sewingStore.applyMergeTasks([existingSewingTasks[0], newSewingTask])   // __ Объединяем СЗ с первой
                        // sewingStore.applyMergeTasks([sewingTask, ...existingSewingTasks])   // __ Объединяем все остальные
                        return
                    }

                }

                // __ Добавляем СЗ в глобальный массив (Обновляем глобальный state СЗ)
                sewingStore.addSewingTaskToGlobal(sewingTask, leftPanel, newSewingTask, rightPanel)    // __ Тут реактивное перерисовывание

            } else {

                // __ Тут ситуация, когда изменился только левая панель (разделение количества и(или) порядка)
                // __ Игнорируем это поведение и просто показываем сообщение об ошибке
                modalInfoType.value = 'danger'
                modalInfoText.value = ['Ошибка!', 'Правая часть не может быть пустой!']
                modalInfoMode.value = 'inform'
                await appModalAsyncMultiline.value!.show()

                // __ Откатываем изменения
                renderMatrix.value = correctRenderMatrix(JSON.parse(JSON.stringify(renderMatrixCopy.value)))

                return
            }

        }


    }
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

