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
                        <ManageTaskManufLineMenu
                            :active-panel="activePanel"
                            :assembly-lines="getList(activePanel)"
                            :assembly-task="props.task"
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

                        <div v-for="panel of panelList" :key="panel"
                             :class="[panel === activePanel ? 'border-[3px] border-blue-700' : 'border border-slate-700']"
                             class="flex flex-col flex-1 bg-slate-800 rounded-lg overflow-hidden max-w-fit"
                             @click="activePanel = panel"
                        >
                            <!-- __ Название Стола -->
                            <AppLabelTS
                                :text="getTableName(panel).toUpperCase()"
                                :type="panel === LINE_0_PANEL_ID ? 'danger' : panel === LINE_1_PANEL_ID ? 'indigo' : 'orange'"
                                align="center"
                                rounded="4"
                                text-size="mini"
                                width="w-[99%]"
                            />

                            <div class="flex-none bg-slate-700 shadow-md overflow-y-auto">
                                <!-- __ Заголовок (Шапка изделий) Панели -->
                                <ManageTaskManufLineItemsHeader
                                    :active-panel="activePanel"
                                    :panel="panel"
                                    :render-data="renderData"
                                    :short="false"
                                    :show-details="showDetails"
                                    :sort-amount="sortAmount"
                                    :sort-lamit="sortLamit"
                                    :sort-name="sortName"
                                    :sort-order="sortOrder"
                                    :sort-position="sortPosition"
                                    :sort-table="sortTable"
                                    :sort-time="sortTime"
                                    :sort-type="sortType"
                                    @sort-by-field="sortByField(panel, $event)"
                                    @sort-by-size="sortBySize(panel)"
                                />
                            </div>

                            <div class="flex-grow overflow-y-auto custom-scrollbar">

                                <!-- __ Сами Записи (AssemblyLines) с возможностью перетаскивания -->
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
                                             @click="setActiveAssemblyLine(element, panel)"
                                             @dblclick="moveAssemblyLine(element, panel)"
                                        >
                                            <ManageTaskManufLineItem
                                                :assembly-line="element"
                                                :panel="panel"
                                                :render-data="renderData"
                                                :short="false"
                                                :show-comments="showComments"
                                                :show-details="showDetails"
                                            />

                                        </div>

                                    </template>

                                </draggable>

                            </div>

                            <div class="flex-none bg-slate-700 py-1 border-t border-slate-600">

                                <!-- __ Итого: -->
                                <ManageTaskManufLineTotals
                                    :amount-and-time="getDataForTotals(panel)"
                                    :short="false"
                                    :show-details="showDetails"
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

    <!--&lt;!&ndash; __ Модальное окно для информации о записи &ndash;&gt;-->
    <!--<OrderItemInfo-->
    <!--    ref="orderItemInfo"-->
    <!--    :order-line="orderLine"-->
    <!--/>-->

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
    IAssemblyTask,
    IColorTypes,
    IAssemblyTaskLine,
    IDividerItem,
    IAssemblyTaskCardSort,
    IAssemblyManufLinesPanel,
    DraggableHTMLElement,
    IAmountAndTimeAssemblyLines,
} from '@/types'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'

import {
    ASSEMBLY_LINE_LAMIT_TITLE, ASSEMBLY_LINE_TABLE_TITLE, ASSEMBLY_LINE_UNDEFINED_TITLE,
    ASSEMBLY_LINES,
} from '@/app/constants/assembly.ts'

import { formatDateInFullFormat } from '@/app/helpers/helpers_date'
import {
    getAssemblyTaskAmountAndTimeLines,
    sortAssemblyTaskLinesBySize,
} from '@/app/helpers/manufacture/helpers_assembly.ts'
import { getColorClassByType } from '@/app/helpers/helpers.js'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppRangeModalAsyncTS from '@/components/ui/modals/AppRangeModalAsyncTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

import CommentEdit from '@/components/dashboard/manufacture/cells/assembly/common/CommentEdit.vue'
import ManageTaskManufLineItem from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskManufLineItem.vue'
import ManageTaskManufLineTotals from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskManufLineTotals.vue'
import ManageTaskManufLineItemsHeader from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskManufLineItemsHeader.vue'
import ManageTaskManufLineMenu from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskManufLineMenu.vue'

// import OrderItemInfo from '@/components/dashboard/manufacture/cells/assemblys/common/OrderItemInfo.vue'


interface IProps {
    type?: IColorTypes,
    width?: string,
    height?: string,
    text?: string,
    mode?: 'inform' | 'confirm'

    task: IAssemblyTask
}

// interface IRenderAssemblyLineDataItem {
//     width: string
// }

// export type IRenderAssemblyLineData = Record<string, IRenderAssemblyLineDataItem>

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
const assemblyStore = useAssemblyStore()

const { globalManageTaskCardActiveAssemblyLine } = storeToRefs(assemblyStore)

// __ Данные (объект) панелей
const panelList = computed(() => {
    if (lineAssemblyLines_0.value.length !== 0) {
        return [LINE_1_PANEL_ID, LINE_2_PANEL_ID, LINE_0_PANEL_ID]
    }
    return [LINE_1_PANEL_ID, LINE_2_PANEL_ID]
})

const lineAssemblyLines_1 = ref<IAssemblyTaskLine[]>([])
const lineAssemblyLines_2 = ref<IAssemblyTaskLine[]>([])
const lineAssemblyLines_0 = ref<IAssemblyTaskLine[]>([])

const mutations = ref<IAssemblyTaskLine[]>([])

// __ Копия входящих данных (объект левой панели) для отслеживания изменений
let taskMem: IAssemblyTask = JSON.parse(JSON.stringify(props.task))
taskMem.assembly_lines     = taskMem.assembly_lines.sort((a: IAssemblyTaskLine, b: IAssemblyTaskLine) => a.id - b.id)

// __ Маяк изменений (для сохранения состояния при перетаскивании)
const needForSave = ref(false)

// __ Инфа в нижней части
const footTitle = reactive({ action_at: '', order: '', load_at: '' })

// __ Переключатель панелей
const LINE_1_PANEL_ID: IAssemblyManufLinesPanel = ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT
const LINE_2_PANEL_ID: IAssemblyManufLinesPanel = ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE
const LINE_0_PANEL_ID: IAssemblyManufLinesPanel = ASSEMBLY_LINES.ASSEMBLY_LINE_UNDEFINED

const activePanel = ref<IAssemblyManufLinesPanel>(LINE_1_PANEL_ID)

let lineLengthMem_1 = 0
let lineLengthMem_2 = 0
let lineLengthMem_0 = 0

// __ Получаем объект для Druggable
const getList = (panel: IAssemblyManufLinesPanel): IAssemblyTaskLine[] => {
    switch (panel) {
        case LINE_1_PANEL_ID:
            return lineAssemblyLines_1.value
        case LINE_2_PANEL_ID:
            return lineAssemblyLines_2.value
        case LINE_0_PANEL_ID:
            return lineAssemblyLines_0.value
    }
    // return []
}

// __ Получаем Название Стола
const getTableName = (panel: IAssemblyManufLinesPanel): string => {
    switch (panel) {
        case LINE_1_PANEL_ID:
            return ASSEMBLY_LINE_LAMIT_TITLE
        case LINE_2_PANEL_ID:
            return ASSEMBLY_LINE_TABLE_TITLE
        case LINE_0_PANEL_ID:
            return ASSEMBLY_LINE_UNDEFINED_TITLE
    }
    // return ''
}

const linePanelAmountAndTimeTotal_1 = ref<IAmountAndTimeAssemblyLines>()
const linePanelAmountAndTimeTotal_2 = ref<IAmountAndTimeAssemblyLines>()
const linePanelAmountAndTimeTotal_0 = ref<IAmountAndTimeAssemblyLines>()

// __ Получаем объект для Totals
const getDataForTotals = (panel: IAssemblyManufLinesPanel) => {
    switch (panel) {
        case LINE_1_PANEL_ID:
            return linePanelAmountAndTimeTotal_1.value
        case LINE_2_PANEL_ID:
            return linePanelAmountAndTimeTotal_2.value
        case LINE_0_PANEL_ID:
            return linePanelAmountAndTimeTotal_0.value
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
// const orderLine     = ref<IAssemblyTaskOrderLine | null>(null)
// const orderItemInfo = ref<InstanceType<typeof OrderItemInfo> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

// __ Тип для модального окна изменения Комментария
const comment     = ref('')
const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null)

// __ Функционал меню + Сортировка
const showComments = ref(false)
const showDetails  = ref(false)
const sortPosition = ref<IAssemblyTaskCardSort>('none')
const sortName     = ref<IAssemblyTaskCardSort>('none')
const sortType     = ref<IAssemblyTaskCardSort>('none')
const sortLamit    = ref<IAssemblyTaskCardSort>('none')
const sortTable    = ref<IAssemblyTaskCardSort>('none')
const sortAmount   = ref<IAssemblyTaskCardSort>('none')
const sortTime     = ref<IAssemblyTaskCardSort>('none')
const sortOrder    = ref<IAssemblyTaskCardSort>('none')
const sortSize     = ref<IAssemblyTaskCardSort>('none')

// __ Стилистика
const borderColor = computed(() => getColorClassByType(props.type, 'border'))

// __ Размеры колонок
const renderData = {
    position  : { width: 'min-w-[25px] max-w-[25px]', },
    model     : { width: 'min-w-[250px] max-w-[250px]', },
    modelShort: { width: 'min-w-[130px] max-w-[130px]', },
    amount    : { width: 'min-w-[30px] max-w-[30px]', },
    time      : { width: 'min-w-[50px] max-w-[50px]', },
    size      : { width: 'min-w-[70px] max-w-[70px]', },
    modelType : { width: 'min-w-[100px] max-w-[100px]', },
    line      : { width: 'min-w-[25px] max-w-[25px]', },
    describe  : { width: 'min-w-[50px] max-w-[50px]', },
    order     : { width: 'min-w-[100px] max-w-[10px]', },
}


// --- -------------------------------------------------------------------------------------
// --- ------------------------ Управление модальным окном ---------------------------------
// --- -------------------------------------------------------------------------------------

// __ реактивность видимости модального окна
const showModal = ref(false)

let resolvePromise: ((value: boolean) => void) | null
const show = async (/*assemblyTask: IAssemblyTask | null = null*/) => {
    showModal.value = true

    // __ Для выхода по ESC
    // await nextTick()            // __ Ждем, пока отрисуется mainDiv
    // mainDiv.value?.focus()      // __ Перемещаем фокус на mainDiv
    // console.log(mainDiv)

    // __ Можно получить активную строку здесь
    // globalManageTaskCardActiveAssemblyLine.value = assemblyTask?.assembly_lines[0]

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

            lineAssemblyLines_1.value = []
            lineAssemblyLines_2.value = []
            lineAssemblyLines_0.value = []
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
        return lineAssemblyLines_1.value
    },
    get linePanel_2() {
        return lineAssemblyLines_2.value
    },
    get linePanel_0() {
        return lineAssemblyLines_0.value
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
    linePanelAmountAndTimeTotal_1.value = getAssemblyTaskAmountAndTimeLines(lineAssemblyLines_1.value)
    linePanelAmountAndTimeTotal_2.value = getAssemblyTaskAmountAndTimeLines(lineAssemblyLines_2.value)
    linePanelAmountAndTimeTotal_0.value = getAssemblyTaskAmountAndTimeLines(lineAssemblyLines_0.value)
}

// __ Устанавливаем активную строку СЗ (клик по строке) + Переключаем панели, если строка в другой панели
const setActiveAssemblyLine = (assemblyLine: IAssemblyTaskLine, panel: IAssemblyManufLinesPanel) => {
    globalManageTaskCardActiveAssemblyLine.value = assemblyLine
    activePanel.value                            = panel
}

// __ Перемещаем строку СЗ в другую панель при двойном клике
const moveAssemblyLine = async (assemblyLine: IAssemblyTaskLine, panel: IAssemblyManufLinesPanel) => {

    // __ Перемещаем по двойному клику только тогда, когда нет Неопознанной Линии
    if (lineAssemblyLines_0.value.length !== 0) {
        return
    }

    if (panel === ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE &&
        assemblyLine.order_line.model.assembly_line !== ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT) {

        await showError([
            'Ошибка!',
            'Нельзя произвести блок',
            `${assemblyLine.order_line.model.name_report}`,
            'на этой Линии Сборки!',
        ])
        return
    }

    const panelFrom = panel === LINE_1_PANEL_ID ? lineAssemblyLines_1 : lineAssemblyLines_2
    const panelTo   = panel === LINE_1_PANEL_ID ? lineAssemblyLines_2 : lineAssemblyLines_1

    const targetLine = panel === LINE_1_PANEL_ID ? ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE : ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT

    panelFrom.value            = panelFrom.value.filter(line => line.id !== assemblyLine.id)
    assemblyLine.assembly_line = targetLine
    panelTo.value.push(assemblyLine)

    globalManageTaskCardActiveAssemblyLine.value = null
    activePanel.value                            = panel

    calculateTotals()
}

// // __ Показать информацию о записи
// const showLineInfo = async (assemblyLine: IAssemblyTaskLine) => {
//     orderLine.value = assemblyLine.order_line
//     await orderItemInfo.value!.show()             // показываем модалку и ждем ответ
// }

// __ Запоминаем данные лоя сравнения
const setMemories = () => {
    lineLengthMem_1 = [...lineAssemblyLines_1.value].length
    lineLengthMem_2 = [...lineAssemblyLines_2.value].length
    lineLengthMem_0 = [...lineAssemblyLines_0.value].length
}

// __ Перезагрузить данные
const reloadData = () => {
    lineAssemblyLines_1.value = JSON.parse(JSON.stringify(props.task.assembly_lines.filter(line => line.assembly_line === ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT)))
    lineAssemblyLines_2.value = JSON.parse(JSON.stringify(props.task.assembly_lines.filter(line => line.assembly_line === ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE)))
    lineAssemblyLines_0.value = JSON.parse(JSON.stringify(props.task.assembly_lines.filter(line => line.assembly_line === ASSEMBLY_LINES.ASSEMBLY_LINE_UNDEFINED)))
    calculateTotals()
    setMemories()
}


// __ Добавить комментарий
const addComment = async () => {

    comment.value = props.task.comment ?? '' // __ Устанавливаем комментарий

    const answer = await commentEdit.value!.show()
    if (answer) {

        const newComment = commentEdit.value!.comment.trim()
        const result     = await assemblyStore.setAssemblyTaskComment(props.task.id, newComment)

        if (!checkCRUD(result)) {
            await showError()
            return
        }

        // __ Обновляем комментарий в глобальном массиве
        assemblyStore.applyAssemblyTaskComment(props.task.id, newComment)

        // __ Обновляем комментарий в текущей строке, потому что теряем где-то реактивность
        // __ из-за передачи параметров в SFC по глубокой копии
        // !!!
        // eslint-disable-next-line vue/no-mutating-props
        props.task.comment = newComment
    }
}


// --- -------------------------------------------------------------------------------------
// --- ------------------------ Сортировка -------------------------------------------------
// --- -------------------------------------------------------------------------------------

// __ Меняем направление сортировки
const changeSortDirection = (sortDirection: IAssemblyTaskCardSort) => {
    sortPosition.value = 'none'
    sortName.value     = 'none'
    sortType.value     = 'none'
    sortLamit.value    = 'none'
    sortTable.value    = 'none'
    sortAmount.value   = 'none'
    sortTime.value     = 'none'
    sortOrder.value    = 'none'

    return ['none', 'desc'].includes(sortDirection) ? 'asc' : 'desc'
}

// __ Определяем допустимые типы данных для сортировки
type SortType = 'number' | 'string' | 'boolean'

interface SortConfig {
    type: SortType
    getValue: (item: IAssemblyTaskLine) => string | number | boolean
}

// __ Карта конфигураций. Ключи — это произвольные идентификаторы (ID колонок)
const sortConfigs: Record<string, SortConfig> = {
    position     : {
        type    : 'number',
        getValue: (item) => item.position
    },
    amount       : {
        type    : 'number',
        getValue: (item) => item.amount
    },
    name         : {
        type    : 'string',
        getValue: (item) => {
            return item.order_line.model.name_report    // __ Получаем название модели
        }
    },
    model_type   : {
        type    : 'string',
        getValue: (item) => {
            return item.order_line.model.model_type    // __ Получаем тип изделия
        }
    },
    assembly_line: {
        type    : 'string',
        getValue: (item) => item.assembly_line
    },
    order        : {
        type    : 'string',
        getValue: (item) => item.order_meta ?? ''
    },
    time         : {
        type    : 'number',
        getValue: (item) => item.time
        // getValue: (item) => Object.values(getAssemblyTimes(item)).reduce((acc, value) => acc + value.time, 0)
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
const setDataToPanel = (panel: IAssemblyManufLinesPanel, data: IAssemblyTaskLine[]) => {
    switch (panel) {
        case LINE_1_PANEL_ID:
            lineAssemblyLines_1.value = data
            break
        case LINE_2_PANEL_ID:
            lineAssemblyLines_2.value = data
            break
        case LINE_0_PANEL_ID:
            lineAssemblyLines_0.value = data
            break
    }
}

// __ Сортировка
const sortByField = (panel: IAssemblyManufLinesPanel, configKey: string) => {
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
        case 'name':
            sortName.value = changeSortDirection(sortName.value)
            direction      = sortName.value
            break
        case 'lamit':
            sortLamit.value = changeSortDirection(sortLamit.value)
            direction       = sortLamit.value
            break
        case 'table':
            sortTable.value = changeSortDirection(sortTable.value)
            direction       = sortTable.value
            break
        case 'line_0':
            sortType.value = changeSortDirection(sortType.value)
            direction      = sortType.value
            break
        case 'order':
            sortOrder.value = changeSortDirection(sortOrder.value)
            direction       = sortOrder.value
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
const sortBySize = (panel: IAssemblyManufLinesPanel) => {
    sortSize.value = changeSortDirection(sortSize.value)

    let sourceArray = panel === LINE_1_PANEL_ID
        ? [...lineAssemblyLines_1.value]
        : [...lineAssemblyLines_2.value]

    sourceArray = sortAssemblyTaskLinesBySize(sourceArray, sortSize.value)

    if (panel === LINE_1_PANEL_ID) {
        lineAssemblyLines_1.value = sourceArray
    } else {
        lineAssemblyLines_2.value = sourceArray
    }
}


// --- -------------------------------------------------------------------------------------

// --- -------------------------------------------------------------------------------------
// --- ------------------------------- Drag and Drop ---------------------------------------
// --- -------------------------------------------------------------------------------------
// __ Запоминаем для Undo
// let fromDataMem
// let toDataMem
let lineDataMem_1: IAssemblyTaskLine[]
let lineDataMem_2: IAssemblyTaskLine[]
let lineDataMem_0: IAssemblyTaskLine[]

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
    lineDataMem_1 = [...lineAssemblyLines_1.value]
    lineDataMem_2 = [...lineAssemblyLines_2.value]
    lineDataMem_0 = [...lineAssemblyLines_0.value]

    lineLengthMem_1 = lineDataMem_1.length
    lineLengthMem_2 = lineDataMem_2.length
    lineLengthMem_0 = lineDataMem_0.length

}
const finishDrag = async (evt: DraggableHTMLElement) => {
    // const element = evt.item._underlying_vm_
    // emits('drag-and-drop')
    // console.log('finishDrag')

    const element = evt.item._underlying_vm_ as IAssemblyTaskLine
    // console.log('finish element: ', element)

    const getToLine = () => {
        if (lineLengthMem_1 < lineAssemblyLines_1.value.length) {
            return LINE_1_PANEL_ID
        }
        if (lineLengthMem_2 < lineAssemblyLines_2.value.length) {
            return LINE_2_PANEL_ID
        }
        if (lineLengthMem_0 < lineAssemblyLines_0.value.length) {
            return LINE_0_PANEL_ID
        }
        return null
    }

    const getFromLine = () => {
        if (lineLengthMem_1 > lineAssemblyLines_1.value.length) {
            return LINE_1_PANEL_ID
        }
        if (lineLengthMem_2 > lineAssemblyLines_2.value.length) {
            return LINE_2_PANEL_ID
        }
        if (lineLengthMem_0 > lineAssemblyLines_0.value.length) {
            return LINE_0_PANEL_ID
        }
        return null
    }

    const fromLine = getFromLine()    // Откуда перемещаем
    const toLine   = getToLine()      // Куда перемещаем

    // console.log('fromTable: ', fromTable)
    // console.log('toTable: ', toTable)

    // __ Проверяем, что что-то куда-то переместили
    if (!fromLine || !toLine) {
        return
    }

    // __ Проверяем, что целевой стол не undefined
    if (toLine === LINE_0_PANEL_ID) {

        await showError([
            'Ошибка!',
            'Нельзя переместить строку',
            'непонятно куда!',
        ])

        // __ Возвращаем все в исходное состояние
        lineAssemblyLines_1.value = lineDataMem_1
        lineAssemblyLines_2.value = lineDataMem_2
        lineAssemblyLines_0.value = lineDataMem_0
        return
    }

    // __ Проверяем, что то, что перетакскиваем соответствует Линии производства
    console.log('toLine: ', toLine)
    console.log('element: ', element)

    if (toLine === ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT && toLine !== element.order_line.model.assembly_line) {
        // if (toLine !== element.assembly.collection.manuf_line && toLine !== element.assembly.collection.manuf_line_alt) {

        await showError([
            'Ошибка!',
            'Нельзя произвести блок',
            `${element.order_line.model.name_report}`,
            'на этой Линии Сборки!',
        ])

        // __ Возвращаем все в исходное состояние
        lineAssemblyLines_1.value = lineDataMem_1
        lineAssemblyLines_2.value = lineDataMem_2
        lineAssemblyLines_0.value = lineDataMem_0
        return
    }


    // __ Предупреждение о том, что детали кроятся на Столе 3
    // if (element.table === LINE_3_PANEL_ID && toLine !== LINE_3_PANEL_ID && element.is_side) {
    //     modalInfoType.value = 'danger'
    //     modalInfoMode.value = 'confirm'
    //
    //     modalInfoText.value = [
    //         'Боковины и детали кроятся на Столе 3!',
    //         'Запись будет отнесена к другому столу.',
    //         'Продолжить?'
    //     ]
    //     const answer        = await appModalAsyncMultiline.value!.show()
    //     if (!answer) {
    //         // __ Возвращаем все в исходное состояние
    //         lineAssemblyLines_1.value = lineDataMem_1
    //         lineAssemblyLines_2.value = lineDataMem_2
    //         lineAssemblyLines_0.value = lineDataMem_0
    //         return
    //     }
    // }

    const targetPanelData = getList(toLine)
    const targetLine      = targetPanelData.find(line => line.id === element.id)
    if (targetLine) {
        targetLine.assembly_line = toLine
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
    globalManageTaskCardActiveAssemblyLine.value = props.task?.assembly_lines[0]

    // __ Копируем входящие данные для отслеживания изменений
    taskMem                = JSON.parse(JSON.stringify(props.task))
    taskMem.assembly_lines = taskMem.assembly_lines.sort((a: IAssemblyTaskLine, b: IAssemblyTaskLine) => a.id - b.id)

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
        ...lineAssemblyLines_1.value,
        ...lineAssemblyLines_2.value,
        ...lineAssemblyLines_0.value
    ]
    // __ Создаем Map, где ключом будет id, а значением — старое имя стола
    // __ Map в JS работает быстрее, чем поиск через .find() на каждой итерации
    const beforeTableMap = new Map(taskMem.assembly_lines.map(item => [item.id, item.assembly_line]))
    mutations.value      = linesActual.filter(line => {
        const oldTable = beforeTableMap.get(line.id)

        // __ Если элемент существовал ранее И его стол изменился — забираем его в результат
        return oldTable !== undefined && oldTable !== line.assembly_line
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
