<template>
    <Teleport to="body">


        <div v-if="showModal"


             class="dark-container">


            <div :class="[width, height, borderColor, 'modal-container max-h-[90vh] overflow-hidden']">

                <!--<div class="close-cross-container">-->
                <div class="flex justify-between w-full h-full items-center">
                    <div class="flex h-full  ml-[20px]">

                        <!-- __ Переместить все УШМ в другую группу -->
                        <AppLabelTS
                            :align="MENU_ITEMS_ALIGN"
                            :height="MENU_ITEMS_HEIGHT"
                            :text="activePanel === LEFT_PANEL_ID ? ' УШМ ▶' : '◀ УШМ'"
                            :text-size="MENU_ITEMS_TEXT_SIZE"
                            :type="MENU_ITEMS_TYPE"
                            :width="MENU_ITEMS_WIDTH"
                        />

                        <!-- __ Переместить все АШМ в другую группу -->
                        <AppLabelTS
                            :align="MENU_ITEMS_ALIGN"
                            :height="MENU_ITEMS_HEIGHT"
                            :text="activePanel === LEFT_PANEL_ID ? ' АШМ ▶' : '◀ АШМ'"
                            :text-size="MENU_ITEMS_TEXT_SIZE"
                            :type="MENU_ITEMS_TYPE"
                            :width="MENU_ITEMS_WIDTH"
                        />

                        <!-- __ Переместить все ГС в другую группу -->
                        <AppLabelTS
                            :align="MENU_ITEMS_ALIGN"
                            :height="MENU_ITEMS_HEIGHT"
                            :text="activePanel === LEFT_PANEL_ID ? ' ГС ▶' : '◀ ГС'"
                            :text-size="MENU_ITEMS_TEXT_SIZE"
                            :type="MENU_ITEMS_TYPE"
                            :width="MENU_ITEMS_WIDTH"
                        />

                        <!-- __ Переместить все ГП в другую группу -->
                        <AppLabelTS
                            :align="MENU_ITEMS_ALIGN"
                            :height="MENU_ITEMS_HEIGHT"
                            :text="activePanel === LEFT_PANEL_ID ? ' ГП ▶' : '◀ ГП'"
                            :text-size="MENU_ITEMS_TEXT_SIZE"
                            :type="MENU_ITEMS_TYPE"
                            :width="MENU_ITEMS_WIDTH"
                        />


                        <!-- __ Разбить количество -->
                        <AppLabelTS
                            :align="MENU_ITEMS_ALIGN"
                            :height="MENU_ITEMS_HEIGHT"
                            :text-size="MENU_ITEMS_TEXT_SIZE"
                            :type="MENU_ITEMS_TYPE"
                            text="◀ Разбить кол-во ▶"
                            width="w-[150px]"
                            @click="divideElementAmount"
                        />

                        <!-- __ Упорядочить -->
                        <AppLabelTS
                            :align="MENU_ITEMS_ALIGN"
                            :height="MENU_ITEMS_HEIGHT"
                            :text-size="MENU_ITEMS_TEXT_SIZE"
                            :type="MENU_ITEMS_TYPE"
                            text="Упорядочить"
                            width="w-[150px]"
                        />

                        <!-- __ Схлопнуть -->
                        <AppLabelTS
                            :align="MENU_ITEMS_ALIGN"
                            :height="MENU_ITEMS_HEIGHT"
                            :text-size="MENU_ITEMS_TEXT_SIZE"
                            :type="MENU_ITEMS_TYPE"
                            text="Схлопнуть"
                            width="w-[150px]"
                        />

                    </div>

                    <div class="m-1 p-1 ml-auto">
                        <AppInputButton
                            id="close"
                            :type="type"
                            height="w-5"
                            title="x"
                            width="w-[30px]"
                            @buttonClick="select(false)"
                        />
                    </div>
                </div>
                <!--</div>-->


                <!--<div class="w-full flex-grow overflow-y-auto px-[4px] custom-scrollbar">-->


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

                        <div class="flex-none bg-slate-700 p-3 border-t border-slate-600">
                            <div class="flex justify-between text-white font-semibold px-2">
                                <span>Всего позиций:</span>
                                <span>50 шт.</span>
                            </div>
                        </div>

                    </div>


                </div>


                <div class="w-full h-full flex justify-end">

                    <div v-if="mode === 'confirm'"
                         class="m-1 p-1">
                        <AppInputButton
                            id="confirm"
                            :type="type"
                            title="Да"
                            @buttonClick="select(true)"
                        />
                    </div>

                    <div
                        class="m-1 p-1">
                        <AppInputButton
                            id="confirm"
                            :title="mode === 'confirm' ? 'Отмена' : 'Закрыть'"
                            :type="type"

                            @buttonClick="select(false)"
                        />
                    </div>

                </div>
            </div>
        </div>


    </Teleport>

    <AppRangeModalAsyncTS
        ref="appRangeModalAsyncTS"
        :item="dividerElement"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />


</template>

<script lang="ts" setup>
import { computed, onMounted, ref, watch, } from 'vue'
import { storeToRefs } from 'pinia'
import draggable from 'vuedraggable'

import type { ISewingTask, IColorTypes, ISewingTaskLine, IDividerItem } from '@/types'

import { useSewingStore } from '@/stores/SewingStore.ts'

import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import ManageTaskCardItem
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCardItem.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import ManageTaskCardItemsHeader
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCardItemsHeader.vue'
import AppRangeModalAsyncTS from '@/components/ui/modals/AppRangeModalAsyncTS.vue'
import logs from '@/router/routes_logs.ts'

// import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
// import ManageItem from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageItem.vue'

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
const targetSewingLines = ref<ISewingTask['sewing_lines']>([])

// __ Переключатель панелей
const LEFT_PANEL_ID  = 'left'
const RIGHT_PANEL_ID = 'right'
const activePanel    = ref(LEFT_PANEL_ID)

// __ Константы меню
const MENU_ITEMS_WIDTH     = 'w-[60px]'
const MENU_ITEMS_HEIGHT    = 'h-[35px]'
const MENU_ITEMS_TYPE      = 'primary'
const MENU_ITEMS_ALIGN     = 'center'
const MENU_ITEMS_TEXT_SIZE = 'mini'


// __ Тип для модального окна
const modalType            = ref<IColorTypes>('primary')
const modalText            = ref<string>('')
const modalMode            = ref<'inform' | 'confirm'>('inform')
const dividerElement       = ref<IDividerItem>({ name: '', amount: 0 })
const appRangeModalAsyncTS = ref<InstanceType<typeof AppRangeModalAsyncTS> | null>(null)         // Получаем ссылку на модальное окно с асинхронной функцией


const borderColor = computed(() => getColorClassByType(props.type, 'border'))


// __ Размеры колонок
const renderData = {
    position: {
        width: 'w-[25px]',
    },
    size:     {
        width: 'w-[70px]',
    },
    model:    {
        width: 'w-[100px]',
    },
    amount:   {
        width: 'w-[30px]',
    },
    textile:  {
        width: 'w-[50px]',
    },
    machine:  {
        width: 'w-[25px]',
    },
    describe: {
        width: 'w-[50px]',
    },
    tkch:     {
        width: 'w-[35px]',
    },
    kant:     {
        width: 'w-[60px]',
    },


}


// __ Опции для draggable
const dragOptions = computed(() => {
    return {
        animation: 300,
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
        dividerElement.value.name   = globalManageTaskCardActiveSewingLine.value.order_line.model.main.name_report
        dividerElement.value.amount = globalManageTaskCardActiveSewingLine.value.amount
    }

    console.log('dividerElement.value: ', dividerElement.value)

    if (dividerElement.value.amount > 1) {
        const answer = await appRangeModalAsyncTS.value!.show()             // показываем модалку и ждем ответ
        if (answer) {
            const range = appRangeModalAsyncTS.value!.range
            console.log(range)
        }
    }
}

// __ Следим за входящими данными
// __ При монтировании компонента, они еще undefined
watch(() => props.task, (value) => {
    globalManageTaskCardActiveSewingLine.value = props.task?.sewing_lines[0]

    // console.log('activeSewingLine ++: ', globalManageTaskCardActiveSewingLine.value)
    // console.log(props.task)
})


</script>

<style scoped>

.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between items-center border-l-8
}

.close-cross-container {
    @apply flex justify-end w-full h-full
}

.text-container {
    @apply flex items-end
}

.text-data {
    @apply border-2 border-slate-800 w-full h-full text-white
}


/* Кастомизация скроллбара для темной темы */
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

/* Стили для draggable */
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

/* Кастомный скроллбар, как мы делали ранее */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    @apply bg-slate-800;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-600 rounded-full;
}

/*
.close-button-container {
    @apply w-full h-full flex justify-end
}
*/
</style>
