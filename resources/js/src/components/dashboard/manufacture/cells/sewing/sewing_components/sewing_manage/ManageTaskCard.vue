<template>
    <Teleport to="body">

        <div v-if="showModal" class="dark-container">

            <div :class="[width, height, borderColor, 'modal-container max-h-[90vh] overflow-hidden']">

                <div class="flex justify-between w-full h-full items-center">

                    <!-- __ Меню Карточки Заявки  -->
                    <ManageTaskCardMenu
                        :active-panel="activePanel"
                        @divide-element-amount="divideElementAmount"
                    />

                    <!-- __ Крестик закрытия -->
                    <div class="m-1 p-1 ml-auto">
                        <AppInputButton
                            id="terminate"
                            :type="type"
                            height="w-5"
                            title="x"
                            width="w-[30px]"
                            @buttonClick="select(false)"
                        />
                    </div>
                </div>


                <!--<div class="w-full flex-grow overflow-y-auto px-[4px] custom-scrollbar">-->

                <!-- __ Панели с записями c возможностью перетаскивания и выбора активной -->
                <div class="flex h-screen w-full bg-slate-900 p-4 gap-4 overflow-hidden">

                    <div v-for="panel in [LEFT_PANEL_ID, RIGHT_PANEL_ID]" :key="panel"
                         :class="[panel === activePanel ? 'border-[3px] border-blue-700' : 'border border-slate-700']"
                         class="flex flex-col flex-1 bg-slate-800 rounded-lg overflow-hidden"
                         @click="activePanel = panel"
                    >

                        <div class="flex-none bg-slate-700 shadow-md">
                            <!--<div class="flex justify-between text-slate-300 font-bold px-2">-->
                            <!--    <span class="w-1/3">Наименование</span>-->
                            <!--    <span class="w-1/3 text-center">Кол-во</span>-->
                            <!--    <span class="w-1/3 text-right">Статус</span>-->
                            <!--</div>-->

                            <ManageTaskCardItemsHeader
                                :render-data="renderData"
                            />

                        </div>

                        <div class="flex-grow overflow-y-auto custom-scrollbar">

                            <!-- __ Сами Записи (SewingLines) с возможностью перетаскивания -->
                            <draggable
                                :="dragOptions"
                                :disabled="!isDragging"
                                :list="panel === LEFT_PANEL_ID ? task.sewing_lines : targetSewingLines"
                                :move="checkMove"
                                class="min-h-[25px]"
                                item-key="id"
                                tag="div"
                                @end="finishDrag"
                                @start="startDrag"
                            >
                                <template #item="{ element, index }">

                                    <ManageTaskCardItem
                                        :render-data="renderData"
                                        :sewing-line="element"
                                        @click="setActiveSewingLine(element)"
                                    />

                                </template>

                            </draggable>


                            <!--<div v-for="n in 50" :key="n"-->
                            <!--     class="flex justify-between p-3 mb-2 bg-slate-750 hover:bg-slate-600 rounded border border-slate-700 text-slate-200 transition-colors">-->
                            <!--    <span class="w-1/3">Задача #{{ n }}</span>-->
                            <!--    <span class="w-1/3 text-center">{{ Math.floor(Math.random() * 100) }}</span>-->
                            <!--    <span class="w-1/3 text-right text-green-400">В работе</span>-->
                            <!--</div>-->


                        </div>

                        <div class="flex-none bg-slate-700 py-1 border-t border-slate-600">

                            <!-- __ Итого: -->
                            <ManageTaskCardTotals
                                :amount-and-time="panel === LEFT_PANEL_ID ? leftPanelAmountAndTimeTotal : rightPanelAmountAndTimeTotal"
                            />

                            <!--<div class="flex justify-between text-white font-semibold px-2">-->
                            <!--    <span>Всего позиций:</span>-->
                            <!--    <span>50 шт.</span>-->
                            <!--</div>-->
                        </div>

                    </div>


                </div>


                <div class="flex w-full items-center">

                    <div class="flex flex-1 justify-center w-full  ">

                        <!-- __ Название СЗ + Клиент + Дата отгрузки -->
                        <div>
                            <div class="text-blue-400 font-semibold text-center">
                                СЗ от <span class="text-green-400">{{ footTitle.action_at }}</span>
                                для Заявки <span class="text-green-400">{{ footTitle.order }}</span>
                                (дата загрузки на складе: <span class="text-green-400"> {{ footTitle.load_at }}</span>)
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

    </Teleport>

    <!-- __ Разбить Количество Модальное окно  -->
    <AppRangeModalAsyncTS
        ref="appRangeModalAsyncTS"
        :item="dividerElement"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />


</template>

<script lang="ts" setup>
import { computed, reactive, ref, watch, watchEffect, } from 'vue'
import { storeToRefs } from 'pinia'
import draggable from 'vuedraggable'

import type {
    ISewingTask, IColorTypes, ISewingTaskLine, IDividerItem, IAmountAndTime, ISewingLinesPanel
} from '@/types'

import { useSewingStore } from '@/stores/SewingStore.ts'

import { formatDateInFullFormat } from '@/app/helpers/helpers_date'
import { getSewingTaskAmountAndTime } from '@/app/helpers/manufacture/helpers_sewing.ts'
import { getColorClassByType } from '@/app/helpers/helpers.js'

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

// __ Тип для модального окна
const modalType            = ref<IColorTypes>('primary')
const modalText            = ref<string>('')
const modalMode            = ref<'inform' | 'confirm'>('inform')
const dividerElement       = ref<IDividerItem>({ name: '', amount: 0 })
const appRangeModalAsyncTS = ref<InstanceType<typeof AppRangeModalAsyncTS> | null>(null)         // Получаем ссылку на модальное окно с асинхронной функцией

// __ Стилистика
const borderColor = computed(() => getColorClassByType(props.type, 'border'))


// __ Размеры колонок
const renderData = {
    position: { width: 'w-[25px]', },
    size:     { width: 'w-[70px]', },
    model:    { width: 'w-[100px]', },
    amount:   { width: 'w-[30px]', },
    time:     { width: 'w-[50px]', },
    textile:  { width: 'w-[50px]', },
    machine:  { width: 'w-[25px]', },
    describe: { width: 'w-[50px]', },
    tkch:     { width: 'w-[35px]', },
    kant:     { width: 'w-[60px]', },
}


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

const checkMove  = (evt: any) => {
    return true
}
const startDrag  = (evt: any) => {
    // const element = evt.item._underlying_vm_
    // console.log('startDrag: ', evt.oldIndex)
    // console.log('element: ', element)
}
const finishDrag = (evt: any) => {
    // const element = evt.item._underlying_vm_
    // emits('drag-and-drop')

    // console.log('finishDrag')
    leftPanelAmountAndTimeTotal.value  = getSewingTaskAmountAndTime(props.task.sewing_lines)
    rightPanelAmountAndTimeTotal.value = getSewingTaskAmountAndTime(targetSewingLines.value)


}

const showModal = ref(false)           // реактивность видимости модального окна


let resolvePromise: ((value: boolean) => void) | null
const show = (sewingTask: ISewingTask | null = null) => {
    showModal.value = true

    // __ Можно получить активную строку здесь
    // globalManageTaskCardActiveSewingLine.value = sewingTask?.sewing_lines[0]

    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: boolean) => {
    if (resolvePromise) {

        // __ Очищаем массив правой части, чтобы не было случайных данных при клике на другую Заявку
        if (!value) {
            targetSewingLines.value = []
        }

        resolvePromise(value)
        showModal.value = false
        resolvePromise  = null
    }
}


defineExpose({
    show,
})


// __ Устанавливаем активную строку СЗ (клик по строке)
const setActiveSewingLine = (sewingLine: ISewingTaskLine) => {
    globalManageTaskCardActiveSewingLine.value = sewingLine
}


// __ Разбить количество
const divideElementAmount = async () => {
    if (globalManageTaskCardActiveSewingLine.value) {

        // __ Копируем объект, чтобы не мутировать оригинал
        const activeSewingLineCopy = JSON.parse(JSON.stringify(globalManageTaskCardActiveSewingLine.value))

        dividerElement.value.name =
            activeSewingLineCopy.order_line.size + ' ' +
            activeSewingLineCopy.order_line.model.main.name_report + ' ' +
            activeSewingLineCopy.order_line.amount.toString() + ' шт.'

        dividerElement.value.amount = activeSewingLineCopy.amount
    }

    console.log('dividerElement.value: ', dividerElement.value)

    if (dividerElement.value.amount > 1) {
        const answer = await appRangeModalAsyncTS.value!.show()             // показываем модалку и ждем ответ
        if (answer) {

            // __ Получаем диапазон
            const range = appRangeModalAsyncTS.value!.range
            console.log(range)
        }
    }
}

// __ Следим за входящими данными
// __ При монтировании компонента, они еще undefined
watch(() => props.task, (value) => {
    globalManageTaskCardActiveSewingLine.value = props.task?.sewing_lines[0]        // __ Задаем активную запись

    // __ Копируем входящие данные для отслеживания изменений
    taskMem = JSON.parse(JSON.stringify(props.task))

    // __ Обновляем инфу в нижней части
    footTitle.action_at = formatDateInFullFormat(props.task.action_at)
    footTitle.order     = props.task.order.client.short_name + ' №' + props.task.order.order_no_str
    footTitle.load_at   = formatDateInFullFormat(props.task.order.load_at)


    leftPanelAmountAndTimeTotal.value  = getSewingTaskAmountAndTime(props.task.sewing_lines)
    rightPanelAmountAndTimeTotal.value = getSewingTaskAmountAndTime(targetSewingLines.value)

    // console.log('leftPanelAmountAndTimeTotal.value: ', leftPanelAmountAndTimeTotal.value)
    // console.log('rightPanelAmountAndTimeTotal.value: ', rightPanelAmountAndTimeTotal.value)

    // footTitle.value =
    //     'СЗ от ' + formatDateInFullFormat(props.task.action_at) +
    //     ' для Заявки ' + props.task.order.client.short_name + ' №' + props.task.order.order_no_str +
    //     '(дата загрузки на складе: ' + formatDateInFullFormat(props.task.order.load_at) + ')'


    // console.log('activeSewingLine ++: ', globalManageTaskCardActiveSewingLine.value)
    // console.log(props.task)
})

// __ Следим за необходимостью сохранения данных
watchEffect(() => {

    needForSave.value = true

    // __ Ситуация, когда мы перетаскиваем строки в правую часть
    if (targetSewingLines.value.length > 0) {
        return
    }

    // __ Ситуация, когда мы меняем порядок строк в левой части
    // __ Сравниваем длину массивов (исходного и копии)
    if (props.task?.sewing_lines.length !== taskMem.sewing_lines.length) {
        return
    }

    // __ Сравниваем содержимое массивов
    for (let i = 0; i < props.task?.sewing_lines.length; i++) {
        const isEqual = JSON.stringify(props.task?.sewing_lines[i]) === JSON.stringify(taskMem.sewing_lines[i])
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
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    @apply bg-slate-800;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-600 rounded-full;
}


</style>
