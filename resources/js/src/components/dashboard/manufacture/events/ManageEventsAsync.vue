<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal" class="dark-container" @click.self="select(false)">

                <!-- Задаем контейнеру фиксированную высоту h-[800px] (из пропсов или класса) -->
                <div :class="[width, height, borderColor, 'modal-container']" class="h-[650px] max-h-[85vh]">

                    <!-- 1. Шапка модалки (всегда на месте) -->
                    <div class="close-cross-container">
                        <div class="m-1 p-1">
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

                    <div class="w-full pl-8 border-b border-slate-800/50 pb-4">
                        <h2 class="text-left text-white text-lg font-bold uppercase">
                            Изменение/добавление События
                        </h2>
                    </div>

                    <div class="ml-4 p-6 flex-shrink-0 flex">
                        <!-- __ Начало -->
                        <AppLabelTS
                            :height="LABEL_HEIGHT"
                            :width="DATE_LABEL_WIDTH"
                            align="center"
                            rounded="4"
                            text="НАЧАЛО"
                            text-size="mini"
                            type="primary"
                        />
                        <!-- __ Окончание -->
                        <AppLabelTS
                            :height="LABEL_HEIGHT"
                            :width="DATE_LABEL_WIDTH"
                            align="center"
                            rounded="4"
                            text="ОКОНЧАНИЕ"
                            text-size="mini"
                            type="primary"
                        />
                        <!-- __ Длительность -->
                        <AppLabelTS
                            :height="LABEL_HEIGHT"
                            :width="DATE_LABEL_WIDTH"
                            align="center"
                            rounded="4"
                            text="ДЛИТЕЛЬНОСТЬ"
                            text-size="mini"
                            type="primary"
                        />
                        <!-- __ Событие -->
                        <AppLabelTS
                            :height="LABEL_HEIGHT"
                            :width="EVENT_LABEL_WIDTH"
                            align="center"
                            rounded="4"
                            text="СОБЫТИЕ"
                            text-size="mini"
                            type="primary"
                        />
                        <!-- __ Добавить Событие -->
                        <AppLabelTS
                            :height="LABEL_HEIGHT"
                            :width="SERVICE_LABEL_WIDTH"
                            align="center"
                            rounded="4"
                            text="➕"
                            text-size="normal"
                            type="warning"
                            @click="addCellEvent"
                        />
                    </div>

                    <!-- 2. Скролл-зона для списка событий (занимает всё свободное место) -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar bg-[#161e2d] m-2 p-2">
                        <div v-for="cellEvent of cellEvents" :key="cellEvent.id" class="pl-6 flex">

                            <div class="mr-0.5">
                                <!-- __ Начало -->
                                <InputDateTS
                                    id="start"
                                    v-model="cellEvent.start_at"
                                    :color="getColor(cellEvent)"
                                    :only-time="true"
                                    :time-enable="true"
                                    :width="DATE_LABEL_WIDTH_PICKER"
                                    @update:modelValue="checkForSave(cellEvent)"
                                />
                            </div>

                            <div class="mr-0.5 z-40">
                                <!-- __ Окончание -->
                                <InputDateTS
                                    id="start"
                                    v-model="cellEvent.finish_at"
                                    :color="getColor(cellEvent)"
                                    :only-time="true"
                                    :time-enable="true"
                                    :width="DATE_LABEL_WIDTH_PICKER"
                                    @getInputDate="checkForSave(cellEvent)"
                                />
                            </div>

                            <!-- __ Длительность -->
                            <div class="mt-[-1px] ml-[-1px]">
                                <AppLabelTS
                                    :color="getColor(cellEvent)"
                                    :text="getDuration(cellEvent)"
                                    :width="DATE_LABEL_WIDTH"
                                    align="center"
                                    height="h-[35px]"
                                    rounded="4"
                                    text-size="mini"
                                />
                            </div>

                            <!-- __ Событие -->
                            <div>
                                <input
                                    :id="`event-${cellEvent.id}`"
                                    v-model="cellEvent.event"
                                    :class="[EVENT_LABEL_WIDTH]"
                                    class="mt-[1px] ml-[2px] h-[35px] px-3 py-1 text-xs text-white bg-[#4f46e5] rounded outline-none border border-transparent transition-all placeholder:text-indigo-200/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 disabled:opacity-50 disabled:cursor-not-allowed"
                                    type="text"
                                    @input="checkForSave(cellEvent)"
                                />
                            </div>

                            <!-- __ Удалить -->
                            <div class="mt-[-1px] ml-0.5">
                                <AppLabelTS
                                    :width="SERVICE_LABEL_WIDTH"
                                    align="center"
                                    height="h-[35px]"
                                    rounded="4"
                                    text="🗑️"
                                    text-size="normal"
                                    type="danger"
                                    @click="deleteCellEvent(cellEvent)"
                                />
                            </div>

                            <!-- __ Сохранить -->
                            <div v-if="cellEvent.readyToSave" class="mt-[-1px]">
                                <AppLabelTS
                                    :width="SERVICE_LABEL_WIDTH"
                                    align="center"
                                    height="h-[35px]"
                                    rounded="4"
                                    text="💾"
                                    text-size="normal"
                                    type="success"
                                    @click="saveCellEvent(cellEvent)"
                                />
                            </div>

                        </div>
                    </div>

                    <!-- 3. Фиксированный подвал (всегда прижат к низу и вправо) -->
                    <div class="w-full flex-shrink-0 flex justify-end gap-2 p-4 bg-slate-800/80 border-t border-slate-700/30 rounded-b-xl mt-auto">
                        <AppInputButton
                            id="confirm"
                            :type="type"
                            title="Закрыть"
                            @buttonClick="select(true)"
                        />
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />

</template>


<script lang="ts" setup>
import { computed, nextTick, ref } from 'vue'
import type { ICellEvent, ICellEventsCells, IColorTypes } from '@/types'

import { useCellEventsStore } from '@/stores/CellEventsStore.ts'

import { getColorClassByType } from '@/app/helpers/helpers.js'
import { formatDateTime, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import InputDateTS from '@/components/dashboard/manufacture/events/InputDateTS.vue'
import { CELL_EVENT_BLOCK, CELL_EVENT_DRAFT } from '@/app/constants/cell_events.ts'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'

interface IProps {
    cell: ICellEventsCells,
    dayId: number
    type?: IColorTypes,
    width?: string,
    height?: string,
    label?: string,
}

const props = withDefaults(defineProps<IProps>(), {
    type  : 'primary',
    width : 'min-w-[1000px] max-w-[1000px]',
    height: 'min-h-[800px]',
    label : 'Комментарий',
})

const cellEventsStore = useCellEventsStore()

const DATE_LABEL_WIDTH        = 'w-[120px]'
const DATE_LABEL_WIDTH_PICKER = 'w-[122px]'
const SERVICE_LABEL_WIDTH     = 'w-[40px]'
const EVENT_LABEL_WIDTH       = 'w-[450px]'
const LABEL_HEIGHT            = 'h-[40px]'

const isLoading = ref(false)
const showModal = ref(false)

const cellEvents                 = ref<ICellEvent[]>([])
let cellEventsCopy: ICellEvent[] = []

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)

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

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---               Основная логика                 !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Длительность События
const getDuration = (cellEvent: ICellEvent) => {
    const millis = new Date(cellEvent.finish_at).getTime() - new Date(cellEvent.start_at).getTime()
    return formatTimeWithLeadingZeros(millis / 1000,)
}

// __ Проверка Дат
const getColor = (cellEvent: ICellEvent) => {
    return (new Date(cellEvent.finish_at).getTime() - new Date(cellEvent.start_at).getTime() > 0) ? '#4f46e5' : '#ff0000'
}

// __ Добавляем Событие
const addCellEvent = () => {
    const newCellEvent     = JSON.parse(JSON.stringify(CELL_EVENT_DRAFT))
    newCellEvent.cell      = CELL_EVENT_BLOCK
    newCellEvent.start_at  = formatDateTime(new Date())
    newCellEvent.finish_at = formatDateTime(new Date())
    cellEvents.value.push(newCellEvent)
}

// __ Сохраняем Событие
const saveCellEvent = async (cellEvent: ICellEvent) => {
    if (!cellEvent.readyToSave) {
        return
    }

    // __ Создаем или Обновляем
    if (cellEvent.id === 0) {
        // __ Создаем
        const result = await cellEventsStore.createEvent(props.dayId, cellEvent)
        if (checkCRUD(result)) {
            // __ Обновляем реактивный Массив и Копию
            cellEvent.id = result.id
            cellEvent.readyToSave = false
            cellEventsCopy.push(JSON.parse(JSON.stringify(cellEvent)))
        } else {
            await showError()
        }

    } else {
        const result = await cellEventsStore.updateEvent(cellEvent)
        if (checkCRUD(result)) {
            // __ Обновляем реактивный Массив и Копию
            cellEvent.readyToSave = false
            cellEventsCopy = cellEventsCopy.filter(cellEventCopy => cellEventCopy.id !== cellEvent.id) // Удаляем
            cellEventsCopy.push(JSON.parse(JSON.stringify(cellEvent))) // Добавляем
        } else {
            await showError()
        }
    }
}

// __ Удаляем Событие
const deleteCellEvent = async (cellEvent: ICellEvent) => {
    modalInfoType.value = 'danger'
    modalInfoText.value = 'Событие будет удалено. Продолжить?'
    modalInfoMode.value = 'confirm'

    const answer = await appModalAsyncMultiline.value!.show()
    if (answer) {
        const result = await cellEventsStore.deleteEvent(cellEvent)
        if (checkCRUD(result)) {
            // __ Обновляем реактивный Массив и Копию
            cellEventsCopy = cellEventsCopy.filter(cellEventCopy => cellEventCopy.id !== cellEvent.id) // Удаляем
            cellEvents.value = cellEvents.value.filter(cellEventItem => cellEventItem.id !== cellEvent.id) // Удаляем
        } else {
            await showError()
        }
    }
}

// __ Проверка на возможность сохранения
const checkForSave = async (cellEvent: ICellEvent) => {
    const findCopyEvent = cellEventsCopy.find(cellEventCopy => cellEventCopy.id === cellEvent.id)

    if (findCopyEvent) {

        // __ Проверяем на корректность Дат Начала и Окончания
        if (new Date(cellEvent.finish_at).getTime() - new Date(cellEvent.start_at).getTime() <= 0) {

            // 1. Сначала превращаем в пустые строки, чтобы Vue гарантированно зафиксировал изменения
            // cellEvent.start_at  = ''
            // cellEvent.finish_at = ''

            // 2. Ждем, пока Vue отрендерит "пустоту" - Без этого не срабатывает реактивность
            // await nextTick()

            // cellEvent.start_at  = findCopyEvent.start_at
            // cellEvent.finish_at = findCopyEvent.finish_at
            return
        }

        // __ Проверяем сами Даты
        if (cellEvent.start_at !== findCopyEvent.start_at || cellEvent.finish_at !== findCopyEvent.finish_at) {
            cellEvent.readyToSave = true
            return
        }

        // __ Проверяем Сам Текст
        if (findCopyEvent.event.trim() !== cellEvent.event.trim()) {
            cellEvent.readyToSave = true
        }
    }

    // __ Тут по логике попадает добавленное
    if (cellEvent.event.trim() !== '') {
        cellEvent.readyToSave = true
    }
}

// __ Получаем Данные
const getData = async () => {
    isLoading.value = true

    cellEvents.value = await cellEventsStore.getEvents(props.dayId, props.cell)
    cellEvents.value = cellEvents.value.map(cellEvent => ({ ...cellEvent, readyToSave: false }))

    // __ Создаем копию для отслеживания изменений
    cellEventsCopy = JSON.parse(JSON.stringify(cellEvents.value))

    console.log('cellEvents: ', cellEvents.value)
    isLoading.value = false
}

// const formData = reactive({
//     comment: '',
//     // title: '',
//     // content: ''
// })

let resolvePromise: ((value: boolean) => void) | null

const show = async () => {
    await nextTick()
    await getData()
    showModal.value = true

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

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

defineExpose({
    show,
    // get comment() {
    //     return formData.comment
    // },
})

</script>

<style scoped>
/* Копируем твои стили скроллбара и анимаций */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-700 rounded-full;
}

.dark-container {
    @apply z-[999] bg-slate-500 fixed w-screen h-screen top-0 left-0 flex justify-center items-center backdrop-blur-sm
    /*@apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center*/
}

.modal-container {
    @apply bg-slate-800 rounded-xl flex flex-col border-l-8 shadow-2xl
}

.close-cross-container {
    @apply flex justify-end w-full
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

/*.modal-enter-active, .modal-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from, .modal-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
}*/
</style>


<!--<template>-->
<!--    <Teleport to="body">-->
<!--        <Transition name="modal">-->
<!--            <div v-if="showModal" class="dark-container" @click.self="select(false)">-->

<!--                <div :class="[width, height, borderColor, 'modal-container']">-->

<!--                    <div class="close-cross-container">-->
<!--                        <div class="m-1 p-1">-->
<!--                            <AppInputButton-->
<!--                                id="close"-->
<!--                                :type="type"-->
<!--                                height="w-5"-->
<!--                                title="x"-->
<!--                                width="w-[30px]"-->
<!--                                @buttonClick="select(false)"-->
<!--                            />-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="w-full pl-8 border-b border-slate-800/50 pb-4">-->
<!--                        &lt;!&ndash;<h3 class="text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold mb-1">&ndash;&gt;-->
<!--                        &lt;!&ndash;    Редактирование / добавление События&ndash;&gt;-->
<!--                        &lt;!&ndash;</h3>&ndash;&gt;-->
<!--                        <h2 class="text-left text-white text-lg font-bold uppercase">-->
<!--                            Изменение/добавление События-->
<!--                        </h2>-->
<!--                    </div>-->


<!--                    <div class="p-6 flex">-->

<!--                        &lt;!&ndash; __ Начало &ndash;&gt;-->
<!--                        <AppLabelTS-->
<!--                            :height="LABEL_HEIGHT"-->
<!--                            :width="DATE_LABEL_WIDTH"-->
<!--                            align="center"-->
<!--                            rounded="4"-->
<!--                            text="НАЧАЛО"-->
<!--                            text-size="mini"-->
<!--                            type="primary"-->
<!--                        />-->

<!--                        &lt;!&ndash; __ Окончание &ndash;&gt;-->
<!--                        <AppLabelTS-->
<!--                            :height="LABEL_HEIGHT"-->
<!--                            :width="DATE_LABEL_WIDTH"-->
<!--                            align="center"-->
<!--                            rounded="4"-->
<!--                            text="ОКОНЧАНИЕ"-->
<!--                            text-size="mini"-->
<!--                            type="primary"-->
<!--                        />-->

<!--                        &lt;!&ndash; __ Длительность &ndash;&gt;-->
<!--                        <AppLabelTS-->
<!--                            :height="LABEL_HEIGHT"-->
<!--                            :width="DATE_LABEL_WIDTH"-->
<!--                            align="center"-->
<!--                            rounded="4"-->
<!--                            text="ДЛИТЕЛЬНОСТЬ"-->
<!--                            text-size="mini"-->
<!--                            type="primary"-->
<!--                        />-->

<!--                        &lt;!&ndash; __ Событие &ndash;&gt;-->
<!--                        <AppLabelTS-->
<!--                            :height="LABEL_HEIGHT"-->
<!--                            :width="EVENT_LABEL_WIDTH"-->
<!--                            align="center"-->
<!--                            rounded="4"-->
<!--                            text="СОБЫТИЕ"-->
<!--                            text-size="mini"-->
<!--                            type="primary"-->
<!--                        />-->

<!--                        &lt;!&ndash; __ Добавить Событие &ndash;&gt;-->
<!--                        <AppLabelTS-->
<!--                            :height="LABEL_HEIGHT"-->
<!--                            :width="SERVICE_LABEL_WIDTH"-->
<!--                            align="center"-->
<!--                            rounded="4"-->
<!--                            text="➕"-->
<!--                            text-size="normal"-->
<!--                            type="warning"-->
<!--                            @click="addCellEvent"-->
<!--                        />-->

<!--                    </div>-->

<!--                    <div v-for="cellEvent of cellEvents" :key="cellEvent.id">-->
<!--                        <div class="pl-6 flex">-->

<!--                            <div class="mr-0.5">-->
<!--                                &lt;!&ndash; __ Начало &ndash;&gt;-->
<!--                                <InputDateTS-->
<!--                                    id="start"-->
<!--                                    v-model="cellEvent.start_at"-->
<!--                                    :color="getColor(cellEvent)"-->
<!--                                    :only-time="true"-->
<!--                                    :time-enable="true"-->
<!--                                    :width="DATE_LABEL_WIDTH_PICKER"-->
<!--                                    @update:modelValue="checkForSave(cellEvent)"-->
<!--                                />-->
<!--                            </div>-->

<!--                            <div class="mr-0.5">-->
<!--                                &lt;!&ndash; __ Окончание &ndash;&gt;-->
<!--                                <InputDateTS-->
<!--                                    id="start"-->
<!--                                    v-model="cellEvent.finish_at"-->
<!--                                    :color="getColor(cellEvent)"-->
<!--                                    :only-time="true"-->
<!--                                    :time-enable="true"-->
<!--                                    :width="DATE_LABEL_WIDTH_PICKER"-->
<!--                                    @getInputDate="checkForSave(cellEvent)"-->
<!--                                />-->
<!--                            </div>-->

<!--                            &lt;!&ndash; __ Длительность &ndash;&gt;-->
<!--                            <div class="mt-[-1px] ml-[-1px]">-->
<!--                                <AppLabelTS-->
<!--                                    :color="getColor(cellEvent)"-->
<!--                                    :text="getDuration(cellEvent)"-->
<!--                                    :width="DATE_LABEL_WIDTH"-->
<!--                                    align="center"-->
<!--                                    height="h-[35px]"-->
<!--                                    rounded="4"-->
<!--                                    text-size="mini"-->
<!--                                />-->
<!--                            </div>-->

<!--                            &lt;!&ndash; __ Событие &ndash;&gt;-->
<!--                            <div>-->
<!--                                <input-->
<!--                                    :id="`event-${cellEvent.id}`"-->
<!--                                    v-model="cellEvent.event"-->
<!--                                    :class="[EVENT_LABEL_WIDTH, ]"-->
<!--                                    class="mt-[1px] ml-[2px] h-[35px] px-3 py-1 text-xs text-white bg-[#4f46e5] rounded outline-none border border-transparent transition-all placeholder:text-indigo-200/60 focus:border-white/40 focus:ring-2 focus:ring-white/20 disabled:opacity-50 disabled:cursor-not-allowed"-->
<!--                                    type="text"-->
<!--                                    @input="checkForSave(cellEvent)"-->
<!--                                />-->
<!--                            </div>-->

<!--                            &lt;!&ndash; __ Удалить &ndash;&gt;-->
<!--                            <div class="mt-[-1px] ml-[2px] ">-->
<!--                                <AppLabelTS-->
<!--                                    :width="SERVICE_LABEL_WIDTH"-->
<!--                                    align="center"-->
<!--                                    height="h-[35px]"-->
<!--                                    rounded="4"-->
<!--                                    text="🗑️"-->
<!--                                    text-size="normal"-->
<!--                                    type="danger"-->
<!--                                />-->
<!--                            </div>-->

<!--                            &lt;!&ndash; __ Сохранить &ndash;&gt;-->
<!--                            <div v-if="cellEvent.readyToSave" class="mt-[-1px] ">-->
<!--                                <AppLabelTS-->
<!--                                    :width="SERVICE_LABEL_WIDTH"-->
<!--                                    align="center"-->
<!--                                    height="h-[35px]"-->
<!--                                    rounded="4"-->
<!--                                    text="💾"-->
<!--                                    text-size="normal"-->
<!--                                    type="success"-->
<!--                                />-->
<!--                            </div>-->

<!--                        </div>-->
<!--                    </div>-->

<!--                    &lt;!&ndash;<div class="p-6 flex flex-col space-y-6 overflow-y-auto custom-scrollbar">&ndash;&gt;-->

<!--                    &lt;!&ndash;    <div class="flex flex-col space-y-2">&ndash;&gt;-->
<!--                    &lt;!&ndash;        <label class="text-slate-500 text-[11px] uppercase font-semibold tracking-tight">&ndash;&gt;-->
<!--                    &lt;!&ndash;            {{ label }}&ndash;&gt;-->
<!--                    &lt;!&ndash;        </label>&ndash;&gt;-->
<!--                    &lt;!&ndash;        <div class="relative group">&ndash;&gt;-->
<!--                    &lt;!&ndash;            <textarea&ndash;&gt;-->
<!--                    &lt;!&ndash;                v-model="formData.comment"&ndash;&gt;-->
<!--                    &lt;!&ndash;                class="w-full bg-[#161e2d] text-blue-400 text-sm font-mono leading-relaxed&ndash;&gt;-->
<!--                    &lt;!&ndash;                       border border-slate-800/50 rounded-xl px-4 py-3&ndash;&gt;-->
<!--                    &lt;!&ndash;                       focus:ring-1 focus:ring-blue-500 outline-none transition-all&ndash;&gt;-->
<!--                    &lt;!&ndash;                       custom-scrollbar resize-none whitespace-pre-wrap"&ndash;&gt;-->
<!--                    &lt;!&ndash;                placeholder="Добавьте комментарий..."&ndash;&gt;-->
<!--                    &lt;!&ndash;                rows="8"&ndash;&gt;-->
<!--                    &lt;!&ndash;            ></textarea>&ndash;&gt;-->
<!--                    &lt;!&ndash;            <div class="absolute right-3 bottom-3 opacity-20 pointer-events-none">&ndash;&gt;-->
<!--                    &lt;!&ndash;                <svg class="text-slate-400" fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"&ndash;&gt;-->
<!--                    &lt;!&ndash;                     stroke-width="2" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">&ndash;&gt;-->
<!--                    &lt;!&ndash;                    <polyline points="16 18 22 12 16 6"></polyline>&ndash;&gt;-->
<!--                    &lt;!&ndash;                    <polyline points="8 6 2 12 8 18"></polyline>&ndash;&gt;-->
<!--                    &lt;!&ndash;                </svg>&ndash;&gt;-->
<!--                    &lt;!&ndash;            </div>&ndash;&gt;-->
<!--                    &lt;!&ndash;        </div>&ndash;&gt;-->
<!--                    &lt;!&ndash;    </div>&ndash;&gt;-->

<!--                    &lt;!&ndash;</div>&ndash;&gt;-->

<!--                    <div class="w-full flex justify-end gap-2 p-4 bg-slate-800/50 rounded-b-xl">-->
<!--                        &lt;!&ndash;<AppInputButton&ndash;&gt;-->
<!--                        &lt;!&ndash;    id="cancel"&ndash;&gt;-->
<!--                        &lt;!&ndash;    title="Отмена"&ndash;&gt;-->
<!--                        &lt;!&ndash;    type="stone"&ndash;&gt;-->
<!--                        &lt;!&ndash;    @buttonClick="select(false)"&ndash;&gt;-->
<!--                        &lt;!&ndash;/>&ndash;&gt;-->
<!--                        <AppInputButton-->
<!--                            id="confirm"-->
<!--                            :type="type"-->
<!--                            title="Закрыть"-->
<!--                            @buttonClick="select(true)"-->
<!--                        />-->
<!--                    </div>-->

<!--                </div>-->
<!--            </div>-->

<!--        </Transition>-->
<!--    </Teleport>-->
<!--</template>-->
