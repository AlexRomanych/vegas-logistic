<template>
    <Teleport to="body">
        <Transition name="modal">

            <div v-if="showModal"
                 ref="mainDiv"
                 class="dark-container"
                 tabindex="-1"
                 @keydown.esc="select(false)"
            >

                <div :class="[width, height, borderColor, 'modal-container max-h-[90vh] overflow-hidden']">

                    <div class="flex justify-between w-full h-full items-center">

                        <!-- __ Меню Карточки Заявки  -->
                        <ManageTaskCardMenu
                            :active-panel="activePanel"
                            :sewing-lines="activePanel === LEFT_PANEL_ID ? sourceSewingLines : targetSewingLines"
                            :sewing-task="props.task"
                            :show-comments="showComments"
                            :show-details="showDetails"
                            @divide-element-amount="divideElementAmount"
                            @show-comments="showComments = !showComments"
                            @show-details="showDetails = !showDetails"
                            @reload-data="reloadData"
                            @move-to-panel="moveToPanel(activePanel, $event)"
                            @merge-lines="mergeLines(activePanel)"
                            @add-comment="addComment"
                        />

                        <!-- __ Крестик закрытия -->
                        <div class="m-1 p-1 ml-auto">
                            <AppInputButton
                                id="terminate"
                                :type="needForSave ? 'danger' : type"
                                height="w-5"
                                title="x"
                                width="w-[30px]"
                                @buttonClick="select(false)"
                            />
                        </div>
                    </div>

                    <!-- __ Панели с записями c возможностью перетаскивания и выбора активной -->
                    <div class="flex h-screen w-full    bg-slate-900 p-4 gap-4 overflow-x-auto">

                        <div v-for="panel in [LEFT_PANEL_ID, RIGHT_PANEL_ID]" :key="panel"
                             :class="[panel === activePanel ? 'border-[3px] border-blue-700' : 'border border-slate-700']"
                             class="flex flex-col flex-1 bg-slate-800 rounded-lg overflow-hidden"
                             @click="activePanel = panel"
                        >

                            <div class="flex-none bg-slate-700 shadow-md">

                                <!-- __ Заголовок (Шапка изделий) Панели -->
                                <ManageTaskCardItemsHeader
                                    :active-panel="activePanel"
                                    :panel="panel"
                                    :render-data="renderData"
                                    :show-comments="showComments"
                                    :show-details="showDetails"
                                    :sort-amount="sortAmount"
                                    :sort-auto="sortAuto"
                                    :sort-kant="sortKant"
                                    :sort-name="sortName"
                                    :sort-position="sortPosition"
                                    :sort-size="sortSize"
                                    :sort-solid-hard="sortSolidHard"
                                    :sort-solid-lite="sortSolidLite"
                                    :sort-textile="sortTextile"
                                    :sort-time="sortTime"
                                    :sort-tkch="sortTkch"
                                    :sort-universal="sortUniversal"
                                    @sort-by-field="sortByField(panel, $event)"
                                    @sort-by-size="sortBySize(panel)"
                                />

                            </div>

                            <div class="flex-grow overflow-y-auto custom-scrollbar">

                                <!-- __ Сами Записи (SewingLines) с возможностью перетаскивания -->
                                <draggable
                                    :="dragOptions"
                                    :disabled="!isDragging"
                                    :list="panel === LEFT_PANEL_ID ? sourceSewingLines : targetSewingLines"
                                    :move="checkMove"
                                    class="min-h-[25px]"
                                    item-key="position"
                                    tag="div"
                                    @end="finishDrag"
                                    @start="startDrag"
                                >
                                    <template #item="{ element, /*index*/ }">

                                        <div :key="element.id"
                                             @click="setActiveSewingLine(element, panel)"
                                             @dblclick="showLineInfo(element)"
                                        >

                                            <ManageTaskCardItem
                                                :render-data="renderData"
                                                :sewing-line="element"
                                                :show-comments="showComments"
                                                :show-details="showDetails"
                                            />

                                        </div>

                                    </template>

                                </draggable>

                            </div>

                            <div class="flex-none bg-slate-700 py-1 border-t border-slate-600">

                                <!-- __ Итого: -->
                                <ManageTaskCardTotals
                                    :amount-and-time="panel === LEFT_PANEL_ID ? leftPanelAmountAndTimeTotal : rightPanelAmountAndTimeTotal"
                                />

                            </div>

                        </div>

                    </div>

                    <div class="flex w-full items-center">

                        <div class="flex flex-1 justify-center w-full">

                            <!-- __ Название СЗ + Клиент + Дата отгрузки -->
                            <div>
                                <div class="text-blue-400 font-semibold text-center mx-2">
                                    СЗ от <span class="text-green-400">{{ footTitle.action_at }}</span>
                                    для Заявки <span class="text-green-400">{{ footTitle.order }}</span>
                                    (дата загрузки на складе: <span class="text-green-400"> {{
                                        footTitle.load_at
                                    }}</span>)
                                </div>
                            </div>

                        </div>

                        <div class="flex gap-1 shrink-0">
                            <div v-if="needForSave" class="my-1 py-1">
                                <AppInputButton
                                    id="confirm"
                                    title="Сохранить"
                                    type="danger"
                                    @buttonClick="select(true)"
                                />
                            </div>
                            <div class="my-1 py-1 pr-2">
                                <AppInputButton
                                    id="close"
                                    :type="type"
                                    title="Закрыть"
                                    @buttonClick="select(false)"
                                />
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </Transition>
    </Teleport>

    <!-- __ Разбить Количество Модальное окно  -->
    <AppRangeModalAsyncTS
        ref="appRangeModalAsyncTS"
        :item="dividerElement"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
    />

    <!-- __ Модальное окно для информации о записи -->
    <OrderItemInfo
        ref="orderItemInfo"
        :order-line="orderLine"
    />

    <!-- __ Модальное окно для изменения/добавления комментария -->
    <CommentEdit
        ref="commentEdit"
        :comment="comment"
        label="Комментарий к Сменному Заданию"
    />

</template>

<script lang="ts" setup>
import { computed, reactive, ref, watch, watchEffect, /*nextTick,*/ } from 'vue'
import draggable from 'vuedraggable'
import { storeToRefs } from 'pinia'

import type {
    ISewingTask,
    IColorTypes,
    ISewingTaskLine,
    IDividerItem,
    IAmountAndTime,
    ISewingLinesPanel,
    ISewingTaskCardSort,
    ISewingTaskOrderLine,
} from '@/types'

import { useSewingStore } from '@/stores/SewingStore.ts'

import { formatDateInFullFormat } from '@/app/helpers/helpers_date'
import {
    calculateDividedAmountAndTime,
    getCoverSizeString,
    getSewingTaskAmountAndTime,
    getSewingTaskModelCover, getSewingTaskModelCoverName,
    getSewingTimes,
    isAverage, mergeSewingLines,
    sortSewingTaskLinesBySize,
} from '@/app/helpers/manufacture/helpers_sewing.ts'
import { getColorClassByType } from '@/app/helpers/helpers.js'

import { round } from '@/app/helpers/helpers_lib.ts'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import ManageTaskCardItem
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCardItem.vue'
import ManageTaskCardItemsHeader
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCardItemsHeader.vue'
import AppRangeModalAsyncTS from '@/components/ui/modals/AppRangeModalAsyncTS.vue'
import ManageTaskCardMenu
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCardMenu.vue'
import ManageTaskCardTotals
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCardTotals.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import CommentEdit from '@/components/dashboard/manufacture/cells/sewing/sewing_components/common/CommentEdit.vue'
import OrderItemInfo from '@/components/dashboard/manufacture/cells/sewing/sewing_components/common/OrderItemInfo.vue'
// import ManageTaskCardItemInfo
//     from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/_ManageTaskCardItemInfo.vue'




interface IProps {
    type?: IColorTypes,
    width?: string,
    height?: string,
    text?: string,
    mode?: 'inform' | 'confirm'

    task: ISewingTask
}

interface IRenderSewingLineDataItem {
    width: string
}

export type IRenderSewingLineData = Record<string, IRenderSewingLineDataItem>

const props = withDefaults(defineProps<IProps>(), {
    type:   'primary',
    width:  'min-w-[1000px]',
    height: 'min-h-[800px]',
    mode:   'inform'
})

// const emits = defineEmits<{
//     (e: 'select'): void
// }>()

// __ Данные из Хранилища
const sewingStore = useSewingStore()

const { globalManageTaskCardActiveSewingLine } = storeToRefs(sewingStore)


// __ Данные (объект) правой панели
const targetSewingLines = ref<ISewingTaskLine[]>([])
const sourceSewingLines = ref<ISewingTaskLine[]>([])

// __ Копия входящих данных (объект левой панели) для отслеживания изменений
let taskMem: ISewingTask = JSON.parse(JSON.stringify(props.task))

// __ Маяк изменений (для сохранения состояния при перетаскивании)
const needForSave = ref(false)

// __ Инфа в нижней части
const footTitle = reactive({ action_at: '', order: '', load_at: '' })

// __ Переключатель панелей
const LEFT_PANEL_ID: ISewingLinesPanel  = 'left'
const RIGHT_PANEL_ID: ISewingLinesPanel = 'right'
const activePanel                       = ref<ISewingLinesPanel>(LEFT_PANEL_ID)

const leftPanelAmountAndTimeTotal  = ref<IAmountAndTime>()
const rightPanelAmountAndTimeTotal = ref<IAmountAndTime>()

// __ Главное окно
const mainDiv = ref<HTMLDivElement | null>(null)

// __ Тип для модального окна "Разбить количество"
const modalType            = ref<IColorTypes>('primary')
const modalText            = ref<string>('')
const modalMode            = ref<'inform' | 'confirm'>('inform')
const dividerElement       = ref<IDividerItem>({ name: '', amount: 0 })
const appRangeModalAsyncTS = ref<InstanceType<typeof AppRangeModalAsyncTS> | null>(null)         // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна информации о записи в Заявке
const orderLine     = ref<ISewingTaskOrderLine | null>(null)
const orderItemInfo = ref<InstanceType<typeof OrderItemInfo> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна изменения Комментария
const comment     = ref('')
const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null)

// __ Функционал меню + Сортировка
const showComments  = ref(false)
const showDetails   = ref(false)
const sortPosition  = ref<ISewingTaskCardSort>('none')
const sortName      = ref<ISewingTaskCardSort>('none')
const sortUniversal = ref<ISewingTaskCardSort>('none')
const sortAuto      = ref<ISewingTaskCardSort>('none')
const sortSolidHard = ref<ISewingTaskCardSort>('none')
const sortSolidLite = ref<ISewingTaskCardSort>('none')
const sortTextile   = ref<ISewingTaskCardSort>('none')
const sortKant      = ref<ISewingTaskCardSort>('none')
const sortTkch      = ref<ISewingTaskCardSort>('none')
const sortAmount    = ref<ISewingTaskCardSort>('none')
const sortTime      = ref<ISewingTaskCardSort>('none')
const sortSize      = ref<ISewingTaskCardSort>('none')


// __ Стилистика
const borderColor = computed(() => getColorClassByType(props.type, 'border'))


// __ Размеры колонок
const renderData = {
    position: { width: 'min-w-[25px] max-w-[25px]', },
    size:     { width: 'min-w-[70px] max-w-[70px]', },
    model:    { width: 'min-w-[150px] max-w-[150px]', },
    amount:   { width: 'min-w-[30px] max-w-[30px]', },
    time:     { width: 'min-w-[50px] max-w-[50px]', },
    textile:  { width: 'min-w-[50px] max-w-[50px]', },
    machine:  { width: 'min-w-[25px] max-w-[25px]', },
    describe: { width: 'min-w-[50px] max-w-[50px]', },
    tkch:     { width: 'min-w-[35px] max-w-[35px]', },
    kant:     { width: 'min-w-[60px] max-w-[60px]', },
}


// --- -------------------------------------------------------------------------------------
// --- ------------------------ Управление модальным окном ---------------------------------
// --- -------------------------------------------------------------------------------------

// __ реактивность видимости модального окна
const showModal = ref(false)

let resolvePromise: ((value: boolean) => void) | null
const show = async (/*sewingTask: ISewingTask | null = null*/) => {
    showModal.value = true

    // __ Для выхода по ESC
    // await nextTick()            // __ Ждем, пока отрисуется mainDiv
    // mainDiv.value?.focus()      // __ Перемещаем фокус на mainDiv
    // console.log(mainDiv)

    // __ Можно получить активную строку здесь
    // globalManageTaskCardActiveSewingLine.value = sewingTask?.sewing_lines[0]

    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

// __ Обработка нажатия выбора и выход из модального окна
const select = async (value: boolean) => {
    if (resolvePromise) {

        // __ Очищаем массив правой части, чтобы не было случайных данных при клике на другую Заявку
        if (!value) {

            // __ Проверяем, есть ли несохраненные изменения
            if (needForSave.value) {
                modalInfoText.value = ['В сменном задании есть несохраненные изменения.', 'Все изменения будут потеряны.', 'Продолжить?']
                modalInfoType.value = 'danger'
                modalInfoMode.value = 'confirm'
                const answer        = await appModalAsyncMultiline.value!.show()             // показываем модалку и ждем ответ
                if (!answer) {
                    return
                }
            }

            targetSewingLines.value = []
            sourceSewingLines.value = []
        } else {

            // __ Левая часть не должна быть пуста
            if (sourceSewingLines.value.length === 0) {
                modalInfoText.value = ['Левая часть должна содержать хотя бы одну строку!']
                modalInfoType.value = 'danger'
                modalInfoMode.value = 'inform'
                await appModalAsyncMultiline.value!.show()
                return
            }


            modalInfoText.value = ['Все изменения будут сохранены.', 'Продолжить?']
            modalInfoType.value = 'primary'
            modalInfoMode.value = 'confirm'
            const answer        = await appModalAsyncMultiline.value!.show()             // показываем модалку и ждем ответ
            if (!answer) {
                return
            }
        }

        resolvePromise(value)
        showModal.value = false
        resolvePromise  = null
    }
}

defineExpose({
    show,
    get leftPanel() {
        return sourceSewingLines.value
    },
    get rightPanel() {
        return targetSewingLines.value
    }
})
// --- -------------------------------------------------------------------------------------


// --- -------------------------------------------------------------------------------------
// --- ------------------------------- Функционал ------------------------------------------
// --- -------------------------------------------------------------------------------------

// __ Пересчитываем Итого
const calculateTotals = () => {
    leftPanelAmountAndTimeTotal.value  = getSewingTaskAmountAndTime(sourceSewingLines.value)
    rightPanelAmountAndTimeTotal.value = getSewingTaskAmountAndTime(targetSewingLines.value)
}

// __ Устанавливаем активную строку СЗ (клик по строке) + Переключаем панели, если строка в другой панели
const setActiveSewingLine = (sewingLine: ISewingTaskLine, panel: ISewingLinesPanel) => {
    globalManageTaskCardActiveSewingLine.value = sewingLine
    activePanel.value                          = panel
}

// __ Показать информацию о записи
const showLineInfo = async (sewingLine: ISewingTaskLine) => {
    orderLine.value = sewingLine.order_line
    await orderItemInfo.value!.show()             // показываем модалку и ждем ответ
}

// __ Разбить количество
const divideElementAmount = async () => {

    // __ Проверяем, есть ли активная строка
    if (!globalManageTaskCardActiveSewingLine.value) {
        return
    }

    // __ Проверяем, больше ли количество, чем 1
    if (globalManageTaskCardActiveSewingLine.value.amount <= 1) {
        return
    }

    // __ Копируем объект, чтобы не мутировать оригинал
    const activeSewingLineCopy = JSON.parse(JSON.stringify(globalManageTaskCardActiveSewingLine.value))

    const modelCoverExt = getSewingTaskModelCoverName(activeSewingLineCopy.order_line.model)

    dividerElement.value.name =
        getCoverSizeString(activeSewingLineCopy) + ' ' +
        modelCoverExt + ' ' +
        activeSewingLineCopy.order_line.amount.toString() + ' шт.'

    dividerElement.value.amount = activeSewingLineCopy.amount

    // console.log('dividerElement.value: ', dividerElement.value)

    const answer = await appRangeModalAsyncTS.value!.show()             // показываем модалку и ждем ответ
    if (answer) {

        // __ Получаем диапазон + проверяем его (страховочка)
        const range = appRangeModalAsyncTS.value!.range
        if (!range || range.take === 0 || range.keep === 0) {
            return
        }

        // console.log(range)

        // __ Логика разделения
        // __ Получаем целевой массив по ссылке
        const workArray           = activePanel.value === LEFT_PANEL_ID ? sourceSewingLines.value : targetSewingLines.value
        const dividerElementIndex = workArray.findIndex(item => item.id === activeSewingLineCopy.id && item.position === activeSewingLineCopy.position)
        if (dividerElementIndex === -1) {
            return // страховка
        }

        // __ Обрабатываем тот кейс, когда разделяем ужу разделенную строку
        // __ Десятичное значение позиции должно быть меньше xx.9
        const workElement = workArray[dividerElementIndex]

        // __ Ищем сразу по 2 массивам, потому что строка уже может быть перемещена в другую панель
        const filteredSourceSewingLines = sourceSewingLines.value.filter(item => item.id_ref === workElement.id_ref)
        const filteredTargetSewingLines = targetSewingLines.value.filter(item => item.id_ref === workElement.id_ref)

        const maxPosition = [...filteredSourceSewingLines, ...filteredTargetSewingLines]
            .filter(item => /*item.id === workElement.id ||*/ item.id_ref === workElement.id_ref)   // __ у новых строк ID = 0
            .reduce((acc, item) => (item.position > acc ? item.position : acc), -Infinity)

        let fraction = ((maxPosition - Math.trunc(maxPosition)) * 10 | 0) / 10

        // __ Если позиция больше 0.9, то не даем разделить
        if (fraction > 0.8) {
            modalInfoText.value = [
                'Максимальное количество разделений',
                'одной строки - 9.',
                'Для продолжения разделения',
                'необходимо сохранить промежуточные результаты.'
            ]
            modalInfoType.value = 'danger'
            modalInfoMode.value = 'inform'
            await appModalAsyncMultiline.value!.show()
            return
        }

        let newSewingLine      = { ...workArray[dividerElementIndex] }              // __ Копируем объект
        newSewingLine.id       = 0                                                  // __ Устанавливаем новый ID
        newSewingLine.position = round(maxPosition + 0.1, 1)      // __ Делаем новую строку ниже текущей позицию с шагом 0.1 (всего 9 разбиений)

        // __ Пересчитываем время и количество
        newSewingLine                  = calculateDividedAmountAndTime(newSewingLine, range.take)
        workArray[dividerElementIndex] = calculateDividedAmountAndTime(workArray[dividerElementIndex], range.keep)

        // __ Вставляем новую строку
        workArray.splice(dividerElementIndex + 1, 0, newSewingLine)

        // console.log(activeSewingLineCopy)
        // console.log(workArray[dividerElementIndex])
        // console.log(workArray[dividerElementIndex + 1])
    }
}

// __ Перезагрузить данные
const reloadData = () => {
    sourceSewingLines.value = JSON.parse(JSON.stringify(props.task.sewing_lines))
    targetSewingLines.value = []

    calculateTotals()
}

// __ Объединить строки
const mergeLines = (activePanel: ISewingLinesPanel) => {
    const workArray = activePanel === LEFT_PANEL_ID
        ? [...sourceSewingLines.value]
        : [...targetSewingLines.value]

    const mergedSewingLines = mergeSewingLines(workArray)

    if (activePanel === LEFT_PANEL_ID) {
        sourceSewingLines.value = [...mergedSewingLines]
    } else {
        targetSewingLines.value = [...mergedSewingLines]
    }
}


// __ Переместить в другую панель
const moveToPanel = (activePanel: ISewingLinesPanel, sewingType: string) => {
    let sourceArray, targetArray

    if (activePanel === LEFT_PANEL_ID) {
        sourceArray = [...sourceSewingLines.value]
        targetArray = [...targetSewingLines.value]
    } else {
        targetArray = [...sourceSewingLines.value]
        sourceArray = [...targetSewingLines.value]
    }

    for (let i = 0; i < sourceArray.length; i++) {
        let compareValue = false

        switch (sewingType) {
            case 'all':
                compareValue = true
                break
            case 'universal':
                compareValue = sourceArray[i].order_line.model.main.is_universal
                break
            case 'auto':
                compareValue = sourceArray[i].order_line.model.main.is_auto
                break
            case 'solid_hard':
                compareValue = sourceArray[i].order_line.model.main.is_solid_hard
                break
            case 'solid_lite':
                compareValue = sourceArray[i].order_line.model.main.is_solid_lite
                break
        }

        if (compareValue) {
            const moveElement = { ...sourceArray[i] }
            targetArray.push(moveElement)
            sourceArray[i].amount = 0
        }
    }

    sourceArray = sourceArray.filter(item => item.amount > 0)

    if (activePanel === LEFT_PANEL_ID) {
        sourceSewingLines.value = [...sourceArray]
        targetSewingLines.value = [...targetArray]
    } else {
        sourceSewingLines.value = [...targetArray]
        targetSewingLines.value = [...sourceArray]
    }

    calculateTotals()
}


// __ Добавить комментарий
const addComment = async () => {

    comment.value = props.task.comment ?? '' // __ Устанавливаем комментарий

    const answer = await commentEdit.value!.show()
    if (answer) {

        const newComment = commentEdit.value!.comment.trim()

        const result = await sewingStore.setSewingTaskComment(props.task.id, newComment)

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
        sewingStore.applySewingTaskComment(props.task.id, newComment)

        // __ Обновляем комментарий в текущей строке, потому что теряем где-то реактивность
        // __ из-за передачи параметров в SFC по глубокой копии
        props.task.comment = newComment
    }
}


// --- -------------------------------------------------------------------------------------
// --- ------------------------ Сортировка -------------------------------------------------
// --- -------------------------------------------------------------------------------------

// __ Меняем направление сортировки
const changeSortDirection = (sortDirection: ISewingTaskCardSort) => {
    sortPosition.value  = 'none'
    sortName.value      = 'none'
    sortUniversal.value = 'none'
    sortAuto.value      = 'none'
    sortSolidHard.value = 'none'
    sortSolidLite.value = 'none'
    sortTextile.value   = 'none'
    sortKant.value      = 'none'
    sortTkch.value      = 'none'
    sortAmount.value    = 'none'
    sortTime.value      = 'none'
    sortSize.value      = 'none'

    return ['none', 'desc'].includes(sortDirection) ? 'asc' : 'desc'
}

// __ Определяем допустимые типы данных для сортировки
type SortType = 'number' | 'string' | 'boolean'

interface SortConfig {
    type: SortType
    getValue: (item: ISewingTaskLine) => string | number | boolean
}

// __ Карта конфигураций. Ключи — это произвольные идентификаторы (ID колонок)
const sortConfigs: Record<string, SortConfig> = {
    position:    {
        type:     'number',
        getValue: (item) => item.position
    },
    amount:      {
        type:     'number',
        getValue: (item) => item.amount
    },
    name_report: {
        type:     'string',
        getValue: (item) => {
            const modelCover = getSewingTaskModelCover(item)    // __ Получаем название модели
            if (!modelCover) return ''

            return modelCover
                ? isAverage(modelCover)
                    ? 'Чехол для Планового матраса'
                    : modelCover.name_report
                : ''
        }
    },
    universal:   {
        type:     'boolean',
        getValue: (item) => item.order_line.model.main.is_universal
    },
    auto:        {
        type:     'boolean',
        getValue: (item) => item.order_line.model.main.is_auto
    },
    solid_hard:  {
        type:     'boolean',
        getValue: (item) => item.order_line.model.main.is_solid_hard
    },
    solid_lite:  {
        type:     'boolean',
        getValue: (item) => item.order_line.model.main.is_solid_lite
    },
    tkch:        {
        type:     'string',
        getValue: (item) => item.order_line.model.main.tkch ?? ''
    },
    kant:        {
        type:     'string',
        getValue: (item) => item.order_line.model.main.kant ?? ''
    },
    textile:     {
        type:     'string',
        getValue: (item) => item.order_line.textile ?? ''
    },
    time:        {
        type:     'number',
        getValue: (item) => Object.values(getSewingTimes(item)).reduce((acc, value) => acc + value.time, 0)
    }
}

// __ Helper
const compareValues = (a: any, b: any, type: SortType, modifier: number) => {
    if (a === b) return 0

    // Boolean и Number обрабатываются одинаково
    if (type === 'number' || type === 'boolean') {
        return (Number(a) - Number(b)) * modifier
    }

    // numeric: true позволяет правильно сортировать "Размер 2" и "Размер 10"
    return String(a).localeCompare(String(b), undefined, {
        numeric:     true,
        sensitivity: 'base'
    }) * modifier
}

// __ Сортировка
const sortByField = (panel: ISewingLinesPanel, configKey: string) => {
    const config = sortConfigs[configKey]
    if (!config) return

    let direction = 'none'

    switch (configKey) {
        case 'position':
            sortPosition.value = changeSortDirection(sortPosition.value)
            direction          = sortPosition.value
            break
        case 'amount':
            sortAmount.value = changeSortDirection(sortAmount.value)
            direction        = sortAmount.value
            break
        case 'name_report':
            sortName.value = changeSortDirection(sortName.value)
            direction      = sortName.value
            break
        case 'universal':
            sortUniversal.value = changeSortDirection(sortUniversal.value)
            direction           = sortUniversal.value
            break
        case 'auto':
            sortAuto.value = changeSortDirection(sortAuto.value)
            direction      = sortAuto.value
            break
        case 'solid_hard':
            sortSolidHard.value = changeSortDirection(sortSolidHard.value)
            direction           = sortSolidHard.value
            break
        case 'solid_lite':
            sortSolidLite.value = changeSortDirection(sortSolidLite.value)
            direction           = sortSolidLite.value
            break
        case 'textile':
            sortTextile.value = changeSortDirection(sortTextile.value)
            direction         = sortTextile.value
            break
        case 'tkch':
            sortTkch.value = changeSortDirection(sortTkch.value)
            direction      = sortTkch.value
            break
        case 'kant':
            sortKant.value = changeSortDirection(sortKant.value)
            direction      = sortKant.value
            break
        case 'time':
            sortTime.value = changeSortDirection(sortTime.value)
            direction      = sortTime.value
            break
    }

    const workArray = panel === LEFT_PANEL_ID
        ? [...sourceSewingLines.value]
        : [...targetSewingLines.value]

    const modifier = direction === 'asc' ? 1 : -1

    workArray.sort((a, b) => {
        return compareValues(config.getValue(a), config.getValue(b), config.type, modifier)
    })

    if (panel === LEFT_PANEL_ID) {
        sourceSewingLines.value = workArray
    } else {
        targetSewingLines.value = workArray
    }
}

// __ Сортировка по размеру
const sortBySize = (panel: ISewingLinesPanel) => {
    sortSize.value = changeSortDirection(sortSize.value)

    let sourceArray = panel === LEFT_PANEL_ID
        ? [...sourceSewingLines.value]
        : [...targetSewingLines.value]

    sourceArray = sortSewingTaskLinesBySize(sourceArray, sortSize.value)

    if (panel === LEFT_PANEL_ID) {
        sourceSewingLines.value = sourceArray
    } else {
        targetSewingLines.value = sourceArray
    }
}
// --- -------------------------------------------------------------------------------------

// --- -------------------------------------------------------------------------------------
// --- ------------------------------- Drag and Drop ---------------------------------------
// --- -------------------------------------------------------------------------------------
// __ Опции для draggable
const dragOptions = computed(() => {
    return {
        animation:   300,
        group:       'cards',
        ghostClass:  'ghost',
        dragClass:   'drag',
        chosenClass: 'chosen',
        // sort: true,
        // disabled: false, // Выносим в отдельное свойство
    }
})
const isDragging  = ref(true)

const checkMove  = (evt: any) => {
    return true
}
const startDrag  = (evt: any) => {
    // console.log('startDrag: evt: ', evt)
    // const element = evt.item._underlying_vm_
    // console.log('startDrag: ', evt.oldIndex)
    // console.log('element: ', element)
}
const finishDrag = (evt: any) => {
    calculateTotals()
    // const element = evt.item._underlying_vm_
    // emits('drag-and-drop')
    // console.log('finishDrag')
}
// --- -------------------------------------------------------------------------------------

// __ Следим за входящими данными
// __ При монтировании компонента, они еще undefined
watch(() => props.task, () => {

    // __ Задаем активную запись
    globalManageTaskCardActiveSewingLine.value = props.task?.sewing_lines[0]

    // __ Копируем входящие данные для отслеживания изменений
    taskMem = JSON.parse(JSON.stringify(props.task))

    // __ Обновляем инфу в нижней части
    footTitle.action_at = formatDateInFullFormat(props.task.action_at)
    footTitle.order     = props.task.order.client.short_name + ' №' + props.task.order.order_no_str
    footTitle.load_at   = formatDateInFullFormat(props.task.order.load_at)

    // __ Копируем данные для левой и правой панели
    sourceSewingLines.value = JSON.parse(JSON.stringify(props.task.sewing_lines))
    targetSewingLines.value = []

    // console.log(sourceSewingLines.value)
    // console.log(targetSewingLines.value)

    // __ Обновляем суммы
    calculateTotals()
})


// __ Обработка нажатия клавиши Esc
// const handleKeyDown = (event: KeyboardEvent) => {
//     if (event.key === 'Escape' && showModal.value) {
//         select(false) // Ваша функция закрытия
//     }
// }


// __ Добавляем обработчик для Esc
// watch(showModal, (isOpen) => {
//     if (isOpen) {
//         window.addEventListener('keydown', handleKeyDown)
//     } else {
//         window.removeEventListener('keydown', handleKeyDown)
//     }
// })


// __ Следим за необходимостью сохранения данных
watchEffect(() => {

    needForSave.value = true

    // __ Ситуация, когда мы перетаскиваем строки в правую часть
    if (targetSewingLines.value.length > 0) {
        return
    }

    // __ Ситуация, когда мы меняем порядок строк в левой части
    // __ Сравниваем длину массивов (исходного и копии)
    if (sourceSewingLines.value.length !== taskMem.sewing_lines.length) {
        return
    }

    // __ Сравниваем содержимое массивов
    for (let i = 0; i < sourceSewingLines.value.length; i++) {
        const isEqual = JSON.stringify(sourceSewingLines.value[i]) === JSON.stringify(taskMem.sewing_lines[i])
        if (!isEqual) {
            return
        }
    }

    needForSave.value = false
})

</script>

<style scoped>

.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between items-center border-l-8
}

/* __ Кастомизация скроллбара для темной темы */
.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    @apply bg-slate-900;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    @apply bg-slate-600 rounded-full;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    @apply bg-slate-500;
}

/* __ Стили для draggable */
.ghost {
    opacity: 0.5;
    background: #1E293B;
}

.chosen {
    background: #1E293B;
}

.drag {
    opacity: 0.8;
    cursor: grabbing;
}

.flex-1 {
    flex: 1 1 0;
    min-width: 0;
}

/* __ Кастомный скроллбар, как мы делали ранее */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px; /* __ Ширина вертикального */
    height: 6px; /* __ Высота горизонтального */
}

.custom-scrollbar::-webkit-scrollbar-track {
    @apply bg-slate-800;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-600 rounded-full;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    @apply bg-slate-500;
}

/* __ Стилизует стык двух скроллбаров */
.custom-scrollbar::-webkit-scrollbar-corner {
    @apply bg-slate-900;

}

.custom-scrollbar {
    /* __ Резервирует место под вертикальный скроллбар заранее.*/
    /* __ Ситуация когда появляется скроллбар справа и автоматически появляется внизу*/
    /* __ Это свойство автоматически резервирует месть под правый скроллбар*/
    scrollbar-gutter: stable;
}

/* __ Если нужно применить ко всем overflow-y-auto в компоненте */
.overflow-y-auto::-webkit-scrollbar,
.overflow-x-auto::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

/* Состояние появления и исчезновения */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.5s ease;
}

/* Стартовое состояние при появлении / Финальное при исчезновении */
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(1.10); /* Легкое увеличение для эффекта приближения */
}

</style>
