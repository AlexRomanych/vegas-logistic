<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal" class="dark-container">

                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">

                    <div
                        class="w-full max-w-md bg-blue-950 border-2 border-blue-600 rounded-2xl shadow-[0_0_30px_rgba(37,99,235,0.3)] overflow-hidden">

                        <div class="px-6 py-4 bg-blue-600 flex items-center justify-between">
                            <h3 class="text-white font-black uppercase tracking-widest text-sm">
                                Выбор периода
                            </h3>
                            <button
                                @click="select(false)"
                                class="text-white hover:rotate-90 transition-transform"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                          d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6">
                            <div class="w-full flex justify-center items-center">

                                <!--<VueDatePicker-->
                                <!--    v-model="date"-->
                                <!--    :action-row="{-->
                                <!--        showPreview: false,-->
                                <!--        showNow: true,-->
                                <!--        selectBtnLabel: 'Выбор',-->
                                <!--        cancelBtnLabel: 'Отмена',-->
                                <!--        nowBtnLabel: 'Текущая',-->
                                <!--    }"-->
                                <!--    :disabled="disabled"-->
                                <!--    :formats="{input: 'dd.MM.yyyy г.'}"-->
                                <!--    :input-attrs="{-->
                                <!--        clearable: false,-->
                                <!--        /*hideInputIcon: true, убираем крестик*/-->
                                <!--        id,-->
                                <!--    }"-->
                                <!--    :locale="ru"-->
                                <!--    :max-date="maxDate"-->
                                <!--    :min-date="minDate"-->
                                <!--    :time-config="{enableTimePicker: false /*убираем выбор времени*/}"-->
                                <!--    :timePicker="false"-->
                                <!--    class="custom-datepicker"-->
                                <!--    dark-->
                                <!--    @update:model-value="selectDate"-->
                                <!--/>-->


                                <VueDatePicker
                                    v-model="date"
                                    :range="false"
                                    :dark="true"
                                    :locale="ru"
                                    inline
                                    auto-apply
                                    :enable-time-picker="false"
                                    calendar-class-name="custom-calendar"
                                    menu-class-name="custom-menu"
                                    class="mx-auto"
                                    :max-date="maxDate"
                                    :min-date="minDate"
                                    :timePicker="false"
                                />
                            </div>

                            <div class="mt-6 flex gap-3">
                                <button
                                    @click="select(false)"
                                    class="flex-1 px-4 py-2 border border-blue-600 text-blue-400 rounded-lg font-bold hover:bg-blue-600/10 transition-colors"
                                >
                                    ОТМЕНА
                                </button>
                                <button
                                    @click="select(true)"
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:bg-blue-500 transition-all"
                                >
                                    ПОДТВЕРДИТЬ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </Transition>
    </Teleport>

</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import type { IColorTypes } from '@/app/constants/colorsClasses.js'

import { VueDatePicker } from '@vuepic/vue-datepicker'
import { ru } from 'date-fns/locale'
import '@vuepic/vue-datepicker/dist/main.css'


interface IProps {
    id: string
    date?: string | Date | null
    type?: IColorTypes
    label?: string
    disabled?: boolean
    width?: string
    minDate?: Date | string | number,
    maxDate?: Date | string | number,
}

const props = withDefaults(defineProps<IProps>(), {
    type:     'dark',
    date:     '',
    label:    '',
    disabled: false,
    width:    'w-[150px]',
    minDate:  undefined,
    maxDate:  undefined,
})

const emits = defineEmits<{
    (e: 'update:model-value', payload: Date): void,
    (e: 'select'): void,
}>()

const dateFormatter = (value: Date | string | null): Date => {
    if (typeof value === 'string') {
        if (value === '') return new Date()
        if (!isNaN(new Date(value).getTime())) return new Date(value)
        return new Date()
    } else if (value) {
        if (!isNaN(value.getTime())) {
            return value
        } else {
            return new Date()
        }
    }
    return new Date()
}

const date = ref<Date>(dateFormatter(props.date))

const selectDate = () => emits('update:model-value', date.value)

// __ Реактивность видимости модального окна
const showModal = ref(false)

let resolvePromise: ((value: boolean) => void) | null
const show = () => {
    showModal.value = true
    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: boolean) => {
    if (resolvePromise) {
        selectDate()
        resolvePromise(value)
        showModal.value = false
        resolvePromise  = null
    }
}

defineExpose({
    show,
    get date() {
        return date.value
    },
})

watch(() => props.date, (newValue) => {
    // console.log('watch')
    date.value = dateFormatter(newValue)
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


    /* Стилизация шрифтов и сетки внутри календаря */
    .custom-calendar {
        font-family: 'JetBrains Mono', monospace;
    }

    .dp__calendar_header_item {
        color: #60a5fa !important; /* blue-400 для дней недели */
        font-weight: 800;
        text-transform: uppercase;
        font-size: 10px;
    }

    .dp__main {
        background: transparent;
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


    /* Убираем фиксированную ширину и центрируем меню */
    :deep(.custom-menu) {
        background: transparent !important;
        border: none !important;
        margin: 0 auto !important; /* Центрирование самого меню */
        width: 100% !important; /* Позволяем занимать доступную ширину */
        display: flex;
        justify-content: center;
    }

    /* Гарантируем, что обертка календаря тоже центрирована */
    :deep(.dp__instance_calendar) {
        margin: 0 auto;
    }

    /* Чтобы календарь не "прилипал" к левому краю внутри контейнера */
    :deep(.dp__menu) {
        background: transparent;
        border: none;
    }

    /* Переопределяем цвета VueDatePicker твой дизайн */
    /* Переменные :root лучше перенести в :deep(.dp__main),
       так как :root в scoped-стилях иногда ведет себя некорректно */
    :deep(.dp__main) {
        --dp-background-color: transparent;
        --dp-text-color: #ffffff;
        --dp-hover-color: #1e293b;
        --dp-hover-text-color: #ffffff;
        --dp-primary-color: #2563eb;
        --dp-border-color: #1e3a8a;
        font-family: 'JetBrains Mono', monospace;
        display: flex;
        justify-content: center;
    }

    /* Стилизация фона выбора времени и элементов управления */
    :deep(.dp__overlay) {
        background-color: #020617 !important; /* Очень темный синий (slate-950) под фон модалки */
    }

    :deep(.dp__time_picker_inline_container) {
        background-color: transparent !important;
    }

    /* Фон бара с кнопками выбора (если включен) */
    :deep(.dp__action_row) {
        background-color: rgba(23, 37, 84, 0.5) !important; /* blue-950/50 */
        border-top: 1px solid #1e3a8a; /* blue-900 */
    }

    /* Кнопки переключения времени (стрелочки вверх/вниз) */
    :deep(.dp__btn) {
        color: #60a5fa !important; /* blue-400 */
    }

    :deep(.dp__btn:hover) {
        background-color: rgba(37, 99, 235, 0.2) !important; /* blue-600/20 */
    }

    /* Если появится выбор месяца/года списком */
    :deep(.dp__selection_grid_header) {
        background-color: #172554 !important; /* blue-950 */
    }
</style>
