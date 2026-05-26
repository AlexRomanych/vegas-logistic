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
                        <ManageTaskTableMenu
                            :active-panel="activePanel"
                            :cutting-lines="getList(activePanel)"
                            :cutting-task="props.task"
                            :show-details="showDetails"
                            @show-details="showDetails = !showDetails"
                            @reload-data="reloadData"
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
                    <div class="flex h-screen w-full bg-slate-900 p-4 gap-2 overflow-x-auto">

                        <div v-for="panel in [TABLE_1_PANEL_ID, TABLE_2_PANEL_ID, TABLE_3_PANEL_ID, TABLE_UNDEFINED_PANEL_ID]" :key="panel"
                             :class="[panel === activePanel ? 'border-[3px] border-blue-700' : 'border border-slate-700']"
                             class="flex flex-col flex-1 bg-slate-800 rounded-lg overflow-hidden max-w-fit"
                             @click="activePanel = panel"
                        >
                            <!-- __ Название Стола -->
                            <AppLabelTS
                                :text="getTableName(panel).toUpperCase()"
                                :type="panel === TABLE_UNDEFINED_PANEL_ID ? 'danger' : panel === TABLE_3_PANEL_ID ? 'warning' : 'indigo'"
                                align="center"
                                rounded="4"
                                text-size="mini"
                                width="w-[99%]"
                            />

                            <div class="flex-none bg-slate-700 shadow-md overflow-y-auto">
                                <!-- __ Заголовок (Шапка изделий) Панели -->
                                <ManageTaskTableItemsHeader
                                    :active-panel="activePanel"
                                    :panel="panel"
                                    :render-data="renderData"
                                    :short="true"
                                    :show-details="showDetails"
                                    :sort-amount="sortAmount"
                                    :sort-detail="sortDetail"
                                    :sort-machine="sortMachine"
                                    :sort-name="sortName"
                                    :sort-order="sortOrder"
                                    :sort-position="sortPosition"
                                    :sort-size="sortSize"
                                    :sort-table_0="sortTable_0"
                                    :sort-table_1="sortTable_1"
                                    :sort-table_2="sortTable_2"
                                    :sort-table_3="sortTable_3"
                                    :sort-textile="sortTextile"
                                    :sort-time="sortTime"
                                    @sort-by-field="sortByField(panel, $event)"
                                    @sort-by-size="sortBySize(panel)"
                                />
                            </div>

                            <div class="flex-grow overflow-y-auto custom-scrollbar">

                                <!-- __ Сами Записи (CuttingLines) с возможностью перетаскивания -->
                                <draggable
                                    :="dragOptions"
                                    :disabled="!isDragging"
                                    :list="getList(panel)"
                                    :move="checkMove"
                                    class="min-h-[25px]"
                                    item-key="position"
                                    tag="div"
                                    @end="finishDrag"
                                    @start="startDrag"
                                >
                                    <template #item="{ element, /*index*/ }">

                                        <div :key="element.id"
                                             @click="setActiveCuttingLine(element, panel)"
                                             @dblclick="showLineInfo(element)"
                                        >

                                            <ManageTaskTableItem
                                                :cutting-line="element"
                                                :render-data="renderData"
                                                :short="true"
                                                :show-comments="showComments"
                                                :show-details="showDetails"
                                            />

                                        </div>

                                    </template>

                                </draggable>

                            </div>

                            <div class="flex-none bg-slate-700 py-1 border-t border-slate-600">

                                <!-- __ Итого: -->
                                <ManageTaskTableTotals
                                    :amount-and-time="getDataForTotals(panel)"
                                    :short="true"
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
        ok-word="Понятно"
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
    ICuttingTask,
    IColorTypes,
    ICuttingTaskLine,
    IDividerItem,
    IAmountAndTime,
    ICuttingTaskCardSort,
    ICuttingTaskOrderLine, ICuttingTablePanel,
} from '@/types'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import {
    TABLE_0,
    TABLE_1, TABLE_2, TABLE_3,
    // TABLE_1_TITLE,TABLE_2_TITLE,TABLE_3_TITLE,
    TABLE_1_TITLE_EXT, TABLE_2_TITLE_EXT, TABLE_3_TITLE_EXT,
    TABLE_UNDEFINED, TABLE_UNDEFINED_TITLE
} from '@/app/constants/cutting.ts'

import { formatDateInFullFormat } from '@/app/helpers/helpers_date'
import { getSewingMachineTitle } from '@/app/helpers/manufacture/helpers_textile.ts'
import {
    getCuttingTaskAmountAndTime,
    getCuttingTaskModelCover,
    isAverage,
    sortCuttingTaskLinesBySize,
} from '@/app/helpers/manufacture/helpers_cutting.ts'
import { getColorClassByType } from '@/app/helpers/helpers.js'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppRangeModalAsyncTS from '@/components/ui/modals/AppRangeModalAsyncTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import CommentEdit from '@/components/dashboard/manufacture/cells/cutting/cutting_components/common/CommentEdit.vue'
import OrderItemInfo from '@/components/dashboard/manufacture/cells/cutting/cutting_components/common/OrderItemInfo.vue'
import ManageTaskTableItem from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskTableItem.vue'
import ManageTaskTableTotals from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskTableTotals.vue'
import ManageTaskTableItemsHeader from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskTableItemsHeader.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import ManageTaskTableMenu from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskTableMenu.vue'


interface IProps {
    type?: IColorTypes,
    width?: string,
    height?: string,
    text?: string,
    mode?: 'inform' | 'confirm'

    task: ICuttingTask
}

// interface IRenderCuttingLineDataItem {
//     width: string
// }

// export type IRenderCuttingLineData = Record<string, IRenderCuttingLineDataItem>

const props = withDefaults(defineProps<IProps>(), {
    type  : 'primary',
    width : 'min-w-[1000px]',
    height: 'min-h-[800px]',
    mode  : 'inform'
})

// const emits = defineEmits<{
//     (e: 'select'): void
// }>()

// __ Данные из Хранилища
const cuttingStore = useCuttingStore()

const { globalManageTaskCardActiveCuttingLine } = storeToRefs(cuttingStore)

// __ Данные (объект) правой панели
const tableCuttingLines_1 = ref<ICuttingTaskLine[]>([])
const tableCuttingLines_2 = ref<ICuttingTaskLine[]>([])
const tableCuttingLines_3 = ref<ICuttingTaskLine[]>([])
const tableCuttingLines_0 = ref<ICuttingTaskLine[]>([])
const mutations           = ref<ICuttingTaskLine[]>([])

// __ Копия входящих данных (объект левой панели) для отслеживания изменений
let taskMem: ICuttingTask = JSON.parse(JSON.stringify(props.task))
taskMem.cutting_lines     = taskMem.cutting_lines.sort((a: ICuttingTaskLine, b: ICuttingTaskLine) => a.id - b.id)

// __ Маяк изменений (для сохранения состояния при перетаскивании)
const needForSave = ref(false)

// __ Инфа в нижней части
const footTitle = reactive({ action_at: '', order: '', load_at: '' })

// __ Переключатель панелей
const TABLE_1_PANEL_ID: ICuttingTablePanel         = TABLE_1
const TABLE_2_PANEL_ID: ICuttingTablePanel         = TABLE_2
const TABLE_3_PANEL_ID: ICuttingTablePanel         = TABLE_3
const TABLE_UNDEFINED_PANEL_ID: ICuttingTablePanel = TABLE_UNDEFINED
const TABLE_0_PANEL_ID                             = TABLE_UNDEFINED_PANEL_ID
const activePanel                                  = ref<ICuttingTablePanel>(TABLE_1_PANEL_ID)

let tableLengthMem_1 = 0
let tableLengthMem_2 = 0
let tableLengthMem_3 = 0
let tableLengthMem_0 = 0

// __ Получаем объект для Druggable
const getList = (panel: ICuttingTablePanel): ICuttingTaskLine[] => {
    switch (panel) {
        case TABLE_1_PANEL_ID:
            return tableCuttingLines_1.value
        case TABLE_2_PANEL_ID:
            return tableCuttingLines_2.value
        case TABLE_3_PANEL_ID:
            return tableCuttingLines_3.value
        case TABLE_UNDEFINED_PANEL_ID:
            return tableCuttingLines_0.value
    }
}

// __ Получаем Название Стола
const getTableName = (panel: ICuttingTablePanel): string => {
    switch (panel) {
        case TABLE_2_PANEL_ID:
            return TABLE_2_TITLE_EXT
        case TABLE_1_PANEL_ID:
            return TABLE_1_TITLE_EXT
        case TABLE_3_PANEL_ID:
            return TABLE_3_TITLE_EXT
        case TABLE_UNDEFINED_PANEL_ID:
            return TABLE_UNDEFINED_TITLE
    }
}

const tablePanelAmountAndTimeTotal_1 = ref<IAmountAndTime>()
const tablePanelAmountAndTimeTotal_2 = ref<IAmountAndTime>()
const tablePanelAmountAndTimeTotal_3 = ref<IAmountAndTime>()
const tablePanelAmountAndTimeTotal_0 = ref<IAmountAndTime>()

// __ Получаем объект для Totals
const getDataForTotals = (panel: ICuttingTablePanel) => {
    switch (panel) {
        case TABLE_1_PANEL_ID:
            return tablePanelAmountAndTimeTotal_1.value
        case TABLE_2_PANEL_ID:
            return tablePanelAmountAndTimeTotal_2.value
        case TABLE_3_PANEL_ID:
            return tablePanelAmountAndTimeTotal_3.value
        case TABLE_UNDEFINED_PANEL_ID:
            return tablePanelAmountAndTimeTotal_0.value
    }
}

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
const orderLine     = ref<ICuttingTaskOrderLine | null>(null)
const orderItemInfo = ref<InstanceType<typeof OrderItemInfo> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна изменения Комментария
const comment     = ref('')
const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null)

// __ Функционал меню + Сортировка
const showComments = ref(false)
const showDetails  = ref(false)
const sortPosition = ref<ICuttingTaskCardSort>('none')
const sortName     = ref<ICuttingTaskCardSort>('none')
const sortTable_1  = ref<ICuttingTaskCardSort>('none')
const sortTable_2  = ref<ICuttingTaskCardSort>('none')
const sortTable_3  = ref<ICuttingTaskCardSort>('none')
const sortTable_0  = ref<ICuttingTaskCardSort>('none')
const sortTextile  = ref<ICuttingTaskCardSort>('none')
const sortAmount   = ref<ICuttingTaskCardSort>('none')
const sortTime     = ref<ICuttingTaskCardSort>('none')
const sortSize     = ref<ICuttingTaskCardSort>('none')
const sortOrder    = ref<ICuttingTaskCardSort>('none')
const sortDetail   = ref<ICuttingTaskCardSort>('none')
const sortMachine  = ref<ICuttingTaskCardSort>('none')

// __ Стилистика
const borderColor = computed(() => getColorClassByType(props.type, 'border'))

// __ Размеры колонок
const renderData = {
    position   : { width: 'min-w-[25px] max-w-[25px]', },
    size       : { width: 'min-w-[70px] max-w-[70px]', },
    sizeShort  : { width: 'min-w-[65px] max-w-[65px]', },
    model      : { width: 'min-w-[150px] max-w-[150px]', },
    modelShort : { width: 'min-w-[130px] max-w-[130px]', },
    amount     : { width: 'min-w-[30px] max-w-[30px]', },
    time       : { width: 'min-w-[50px] max-w-[50px]', },
    textile    : { width: 'min-w-[50px] max-w-[50px]', },
    detail     : { width: 'min-w-[50px] max-w-[50px]', },
    detailShort: { width: 'min-w-[30px] max-w-[30px]', },
    machine    : { width: 'min-w-[30px] max-w-[30px]', },
    table      : { width: 'min-w-[25px] max-w-[25px]', },
    describe   : { width: 'min-w-[50px] max-w-[50px]', },
    tkch       : { width: 'min-w-[35px] max-w-[35px]', },
    kant       : { width: 'min-w-[60px] max-w-[60px]', },
    order      : { width: 'min-w-[100px] max-w-[10px]', },
}


// --- -------------------------------------------------------------------------------------
// --- ------------------------ Управление модальным окном ---------------------------------
// --- -------------------------------------------------------------------------------------

// __ реактивность видимости модального окна
const showModal = ref(false)

let resolvePromise: ((value: boolean) => void) | null
const show = async (/*cuttingTask: ICuttingTask | null = null*/) => {
    showModal.value = true

    // __ Для выхода по ESC
    // await nextTick()            // __ Ждем, пока отрисуется mainDiv
    // mainDiv.value?.focus()      // __ Перемещаем фокус на mainDiv
    // console.log(mainDiv)

    // __ Можно получить активную строку здесь
    // globalManageTaskCardActiveCuttingLine.value = cuttingTask?.cutting_lines[0]

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

            tableCuttingLines_1.value = []
            tableCuttingLines_2.value = []
            tableCuttingLines_3.value = []
            tableCuttingLines_0.value = []
        } else {
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
    get tablePanel_1() {
        return tableCuttingLines_1.value
    },
    get tablePanel_2() {
        return tableCuttingLines_2.value
    },
    get tablePanel_3() {
        return tableCuttingLines_3.value
    },
    get tablePanel_0() {
        return tableCuttingLines_0.value
    },
    get mutations() {
        return mutations.value
    },
})
// --- -------------------------------------------------------------------------------------


// --- -------------------------------------------------------------------------------------
// --- ----------------------------------- Ошибки ------------------------------------------
// --- -------------------------------------------------------------------------------------
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


// --- -------------------------------------------------------------------------------------
// --- ------------------------------- Функционал ------------------------------------------
// --- -------------------------------------------------------------------------------------

// __ Пересчитываем Итого
const calculateTotals = () => {
    tablePanelAmountAndTimeTotal_1.value = getCuttingTaskAmountAndTime(tableCuttingLines_1.value)
    tablePanelAmountAndTimeTotal_2.value = getCuttingTaskAmountAndTime(tableCuttingLines_2.value)
    tablePanelAmountAndTimeTotal_3.value = getCuttingTaskAmountAndTime(tableCuttingLines_3.value)
    tablePanelAmountAndTimeTotal_0.value = getCuttingTaskAmountAndTime(tableCuttingLines_0.value)
}

// __ Устанавливаем активную строку СЗ (клик по строке) + Переключаем панели, если строка в другой панели
const setActiveCuttingLine = (cuttingLine: ICuttingTaskLine, panel: ICuttingTablePanel) => {
    globalManageTaskCardActiveCuttingLine.value = cuttingLine
    activePanel.value                           = panel
}

// __ Показать информацию о записи
const showLineInfo = async (cuttingLine: ICuttingTaskLine) => {
    orderLine.value = cuttingLine.order_line
    await orderItemInfo.value!.show()             // показываем модалку и ждем ответ
}

// __ Запоминаем данные лоя сравнения
const setMemories = () => {
    tableLengthMem_1 = [...tableCuttingLines_1.value].length
    tableLengthMem_2 = [...tableCuttingLines_2.value].length
    tableLengthMem_3 = [...tableCuttingLines_3.value].length
    tableLengthMem_0 = [...tableCuttingLines_0.value].length
}

// __ Перезагрузить данные
const reloadData = () => {
    tableCuttingLines_1.value = JSON.parse(JSON.stringify(props.task.cutting_lines.filter(line => line.table === TABLE_1)))
    tableCuttingLines_2.value = JSON.parse(JSON.stringify(props.task.cutting_lines.filter(line => line.table === TABLE_2)))
    tableCuttingLines_3.value = JSON.parse(JSON.stringify(props.task.cutting_lines.filter(line => line.table === TABLE_3)))
    tableCuttingLines_0.value = JSON.parse(JSON.stringify(props.task.cutting_lines.filter(line => line.table === TABLE_0)))
    calculateTotals()
    setMemories()
}


// __ Добавить комментарий
const addComment = async () => {

    comment.value = props.task.comment ?? '' // __ Устанавливаем комментарий

    const answer = await commentEdit.value!.show()
    if (answer) {

        const newComment = commentEdit.value!.comment.trim()
        const result = await cuttingStore.setCuttingTaskComment(props.task.id, newComment)

        if (!checkCRUD(result)) {
           await showError()
            return
        }

        // __ Обновляем комментарий в глобальном массиве
        cuttingStore.applyCuttingTaskComment(props.task.id, newComment)

        // __ Обновляем комментарий в текущей строке, потому что теряем где-то реактивность
        // __ из-за передачи параметров в SFC по глубокой копии
        props.task.comment = newComment
    }
}


// --- -------------------------------------------------------------------------------------
// --- ------------------------ Сортировка -------------------------------------------------
// --- -------------------------------------------------------------------------------------

// __ Меняем направление сортировки
const changeSortDirection = (sortDirection: ICuttingTaskCardSort) => {
    sortPosition.value = 'none'
    sortName.value     = 'none'
    sortTable_1.value  = 'none'
    sortTable_2.value  = 'none'
    sortTable_3.value  = 'none'
    sortTable_0.value  = 'none'
    sortTextile.value  = 'none'
    sortAmount.value   = 'none'
    sortTime.value     = 'none'
    sortSize.value     = 'none'
    sortDetail.value   = 'none'
    sortOrder.value    = 'none'
    sortMachine.value  = 'none'

    return ['none', 'desc'].includes(sortDirection) ? 'asc' : 'desc'
}

// __ Определяем допустимые типы данных для сортировки
type SortType = 'number' | 'string' | 'boolean'

interface SortConfig {
    type: SortType
    getValue: (item: ICuttingTaskLine) => string | number | boolean
}

// __ Карта конфигураций. Ключи — это произвольные идентификаторы (ID колонок)
const sortConfigs: Record<string, SortConfig> = {
    position   : {
        type    : 'number',
        getValue: (item) => item.position
    },
    amount     : {
        type    : 'number',
        getValue: (item) => item.amount
    },
    name_report: {
        type    : 'string',
        getValue: (item) => {
            const modelCover = getCuttingTaskModelCover(item)    // __ Получаем название модели
            if (!modelCover) return ''

            return modelCover
                ? isAverage(modelCover)
                    ? 'Чехол для Планового матраса'
                    : modelCover.name_report
                : ''
        }
    },
    table      : {
        type    : 'string',
        getValue: (item) => item.table ?? ''
    },
    order      : {
        type    : 'string',
        getValue: (item) => item.order_meta ?? ''
    },
    detail     : {
        type    : 'boolean',
        getValue: (item) => item.is_panel
    },
    textile    : {
        type    : 'string',
        getValue: (item) => item.order_line.textile ?? ''
    },
    machine    : {
        type    : 'string',
        getValue: (item) => getSewingMachineTitle(item)
    },
    time       : {
        type    : 'number',
        getValue: (item) => item.time
        // getValue: (item) => Object.values(getCuttingTimes(item)).reduce((acc, value) => acc + value.time, 0)
    }
}

// __ Helper
const compareValues = (a: unknown, b: unknown, type: SortType, modifier: number) => {
    if (a === b) return 0

    // Boolean и Number обрабатываются одинаково
    if (type === 'number' || type === 'boolean') {
        return (Number(a) - Number(b)) * modifier
    }

    // numeric: true позволяет правильно сортировать "Размер 2" и "Размер 10"
    return String(a).localeCompare(String(b), undefined, {
        numeric    : true,
        sensitivity: 'base'
    }) * modifier
}


// __ Вспомогательный, возвращает обработанные данные в реактивный массив
const setDataToPanel = (panel: ICuttingTablePanel, data: ICuttingTaskLine[]) => {
    switch (panel) {
        case TABLE_1_PANEL_ID:
            tableCuttingLines_1.value = data
            break
        case TABLE_2_PANEL_ID:
            tableCuttingLines_2.value = data
            break
        case TABLE_3_PANEL_ID:
            tableCuttingLines_3.value = data
            break
        case TABLE_0_PANEL_ID:
            tableCuttingLines_0.value = data
            break
    }
}

// __ Сортировка
const sortByField = (panel: ICuttingTablePanel, configKey: string) => {
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
        case 'table_1':
            sortTable_1.value = changeSortDirection(sortTable_1.value)
            direction         = sortTable_1.value
            break
        case 'table_2':
            sortTable_2.value = changeSortDirection(sortTable_2.value)
            direction         = sortTable_2.value
            break
        case 'table_3':
            sortTable_3.value = changeSortDirection(sortTable_3.value)
            direction         = sortTable_3.value
            break
        case 'table_0':
            sortTable_0.value = changeSortDirection(sortTable_0.value)
            direction         = sortTable_0.value
            break
        case 'textile':
            sortTextile.value = changeSortDirection(sortTextile.value)
            direction         = sortTextile.value
            break
        case 'order':
            sortOrder.value = changeSortDirection(sortOrder.value)
            direction       = sortOrder.value
            break
        case 'detail':
            sortDetail.value = changeSortDirection(sortDetail.value)
            direction        = sortDetail.value
            break
        case 'machine':
            sortMachine.value = changeSortDirection(sortMachine.value)
            direction         = sortMachine.value
            break
        case 'time':
            sortTime.value = changeSortDirection(sortTime.value)
            direction      = sortTime.value
            break
    }

    const workArray = getList(panel)

    const modifier = direction === 'asc' ? 1 : -1

    workArray.sort((a, b) => {
        return compareValues(config.getValue(a), config.getValue(b), config.type, modifier)
    })

    // __ Возвращаем все обратно в реактивные переменные
    setDataToPanel(panel, workArray)
}

// __ Сортировка по размеру
const sortBySize = (panel: ICuttingTablePanel) => {
    sortSize.value = changeSortDirection(sortSize.value)

    let sourceArray = getList(panel)
    sourceArray     = sortCuttingTaskLinesBySize(sourceArray, sortSize.value)

    // __ Возвращаем все обратно в реактивные переменные
    setDataToPanel(panel, sourceArray)
}
// --- -------------------------------------------------------------------------------------

// --- -------------------------------------------------------------------------------------
// --- ------------------------------- Drag and Drop ---------------------------------------
// --- -------------------------------------------------------------------------------------
// __ Запоминаем для Undo
// let fromDataMem
// let toDataMem
let tableDataMem_1: ICuttingTaskLine[]
let tableDataMem_2: ICuttingTaskLine[]
let tableDataMem_3: ICuttingTaskLine[]
let tableDataMem_0: ICuttingTaskLine[]

// __ Опции для draggable
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const dragOptions = computed(() => {
    return {
        animation  : 300,
        group      : 'cards',
        ghostClass : 'ghost',
        dragClass  : 'drag',
        chosenClass: 'chosen',
        // sort: true,
        // disabled: false, // Выносим в отдельное свойство
    }
})
const isDragging  = ref(true)

const checkMove  = (/*evt: any*/) => {
    return true
}
const startDrag  = (/*evt: any*/) => {
    // console.log('startDrag: evt: ', evt)
    // const element = evt.item._underlying_vm_
    // console.log('startDrag: ', evt.oldIndex)
    // console.log('start element: ', element)

    // __ Запоминаем для Undo
    tableDataMem_1 = [...tableCuttingLines_1.value]
    tableDataMem_2 = [...tableCuttingLines_2.value]
    tableDataMem_3 = [...tableCuttingLines_3.value]
    tableDataMem_0 = [...tableCuttingLines_0.value]

    tableLengthMem_1 = tableDataMem_1.length
    tableLengthMem_2 = tableDataMem_2.length
    tableLengthMem_3 = tableDataMem_3.length
    tableLengthMem_0 = tableDataMem_0.length

}
const finishDrag = async (evt: any) => {
    // const element = evt.item._underlying_vm_
    // emits('drag-and-drop')
    // console.log('finishDrag')
    const element = evt.item._underlying_vm_
    // console.log('finish element: ', element)

    const getToTable = () => {
        if (tableLengthMem_1 < tableCuttingLines_1.value.length) {
            return TABLE_1_PANEL_ID
        }
        if (tableLengthMem_2 < tableCuttingLines_2.value.length) {
            return TABLE_2_PANEL_ID
        }
        if (tableLengthMem_3 < tableCuttingLines_3.value.length) {
            return TABLE_3_PANEL_ID
        }
        if (tableLengthMem_0 < tableCuttingLines_0.value.length) {
            return TABLE_0_PANEL_ID
        }
        return null
    }

    const getFromTable = () => {
        if (tableLengthMem_1 > tableCuttingLines_1.value.length) {
            return TABLE_1_PANEL_ID
        }
        if (tableLengthMem_2 > tableCuttingLines_2.value.length) {
            return TABLE_2_PANEL_ID
        }
        if (tableLengthMem_3 > tableCuttingLines_3.value.length) {
            return TABLE_3_PANEL_ID
        }
        if (tableLengthMem_0 > tableCuttingLines_0.value.length) {
            return TABLE_0_PANEL_ID
        }
        return null
    }

    const fromTable = getFromTable()    // Откуда перемещаем
    const toTable   = getToTable()      // Куда перемещаем

    // console.log('fromTable: ', fromTable)
    // console.log('toTable: ', toTable)

    // __ Проверяем, что что-то куда-то переместили
    if (!fromTable || !toTable) {
        return
    }

    // __ Проверяем, что целевой стол не undefined
    if (toTable === TABLE_0_PANEL_ID) {

        await showError([
            'Ошибка!',
            'Нельзя переместить строку',
            'непонятно куда!',
        ])

        // __ Возвращаем все в исходное состояние
        tableCuttingLines_1.value = tableDataMem_1
        tableCuttingLines_2.value = tableDataMem_2
        tableCuttingLines_3.value = tableDataMem_3
        tableCuttingLines_0.value = tableDataMem_0
        return
    }

    // __ Предупреждение о том, что детали кроятся на Столе 3
    if (element.table === TABLE_3_PANEL_ID && toTable !== TABLE_3_PANEL_ID && element.is_side) {
        modalInfoType.value = 'danger'
        modalInfoMode.value = 'confirm'

        modalInfoText.value = [
            'Боковины и детали кроятся на Столе 3!',
            'Запись будет отнесена к другому столу.',
            'Продолжить?'
        ]
        const answer        = await appModalAsyncMultiline.value!.show()
        if (!answer) {
            // __ Возвращаем все в исходное состояние
            tableCuttingLines_1.value = tableDataMem_1
            tableCuttingLines_2.value = tableDataMem_2
            tableCuttingLines_3.value = tableDataMem_3
            tableCuttingLines_0.value = tableDataMem_0
            return
        }
    }

    const targetPanelData = getList(toTable)
    const targetLine      = targetPanelData.find(line => line.id === element.id)
    if (targetLine) {
        targetLine.table = toTable
    }

    // console.log('targetLine: ', targetLine)

    calculateTotals()
    // setMemories()
}
// --- -------------------------------------------------------------------------------------


// __ Следим за входящими данными
// __ При монтировании компонента, они еще undefined
watch(() => props.task, () => {

    // __ Задаем активную запись
    globalManageTaskCardActiveCuttingLine.value = props.task?.cutting_lines[0]

    // __ Копируем входящие данные для отслеживания изменений
    taskMem               = JSON.parse(JSON.stringify(props.task))
    taskMem.cutting_lines = taskMem.cutting_lines.sort((a: ICuttingTaskLine, b: ICuttingTaskLine) => a.id - b.id)

    // __ Обновляем инфу в нижней части
    footTitle.action_at = formatDateInFullFormat(props.task.action_at)
    footTitle.order     = props.task.order.client.short_name + ' №' + props.task.order.order_no_str
    footTitle.load_at   = formatDateInFullFormat(props.task.order.load_at)

    // __ Создаем данные для панелей + Обновляем суммы
    reloadData()
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

    // __ Ищем мутировавшие элементы
    const linesActual = [
        ...tableCuttingLines_1.value,
        ...tableCuttingLines_2.value,
        ...tableCuttingLines_3.value,
        ...tableCuttingLines_0.value
    ]
    // __ Создаем Map, где ключом будет id, а значением — старое имя стола
    // __ Map в JS работает быстрее, чем поиск через .find() на каждой итерации
    const beforeTableMap = new Map(taskMem.cutting_lines.map(item => [item.id, item.table]))
    mutations.value      = linesActual.filter(line => {
        const oldTable = beforeTableMap.get(line.id)

        // __ Если элемент существовал ранее И его стол изменился — забираем его в результат
        return oldTable !== undefined && oldTable !== line.table
    })

    // console.log('mutations: ', mutations)

    if (mutations.value.length === 0) {
        needForSave.value = false
    }
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
