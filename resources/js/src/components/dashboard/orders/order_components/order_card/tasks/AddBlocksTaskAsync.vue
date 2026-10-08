<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal"
                 class="dark-container">

                <div :class="[width, height, borderColor, 'modal-container']">

                    <!-- Шапка с кнопкой закрытия -->
                    <div class="flex justify-end w-full shrink-0">
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

                    <!-- __ Дата Сменного Задания -->
                    <div class="mr-0.5">
                        <div class="flex flex-col items-center mb-1"><span class="text-white font-semibold">Дата выполнения Сменного Задания:</span></div>
                        <InputDateTS
                            id="start"
                            v-model="actionAtDate"
                            :color="'#2563eb'"
                            :max-date="maxDate"
                            :min-date="minDate"
                            :only-time="false"
                            :time-enable="false"
                            :width="DEFAULT_WIDTH"
                            @update:modelValue="() => console.log(actionAtDate)"
                        />
                    </div>

                    <!-- __ Смена Сменного Задания -->
                    <div>
                        <div class="flex flex-col items-center mb-1 mt-3"><span class="text-white font-semibold">Смена Сменного Задания:</span></div>
                        <AppCheckboxTS
                            id="calc-mode"
                            :checkboxData="changeCheckboxData"
                            :width="DEFAULT_WIDTH"
                            dir="horizontal"
                            inputType="radio"
                            type="primary"
                            @checked="changeCheckedHandler"
                        />
                    </div>

                    <!-- __ Комментарий Сменного Задания -->
                    <div class="w-full">
                        <div class="flex flex-col items-center  mb-1 mt-3"><span class="text-white font-semibold">Комментарий к Сменному Заданию:</span></div>
                        <!--<label class="text-slate-500 text-[11px] uppercase font-semibold tracking-tight">-->
                        <!--    <span>Добавьте комментарий</span>-->
                        <!--</label>-->
                        <div class="relative group">
                                <textarea
                                    v-model="comment"
                                    class="w-full max-h-[100px] bg-[#161e2d] text-blue-400 text-sm font-mono leading-relaxed
                                           border border-slate-800/50 rounded-xl px-4 py-1
                                           focus:ring-1 focus:ring-blue-500 outline-none transition-all
                                           custom-scrollbar resize-none whitespace-pre-wrap"
                                    placeholder="Добавьте комментарий..."
                                    rows="8"
                                ></textarea>
                            <div class="absolute right-3 bottom-3 opacity-20 pointer-events-none">
                                <svg class="text-slate-400" fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                     stroke-width="2" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                                    <polyline points="16 18 22 12 16 6"></polyline>
                                    <polyline points="8 6 2 12 8 18"></polyline>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- __ Нижний блок с кнопками действий -->
                    <div class="w-full flex justify-end shrink-0 pt-2">
                        <div class="m-1 p-1">
                            <AppInputButton
                                id="confirm"
                                :type="type"
                                title="Создать"
                                @buttonClick="select(true)"
                            />
                        </div>

                        <div class="m-1 p-1">
                            <AppInputButton
                                id="cancel"
                                :type="type"
                                title="Отмена"
                                @buttonClick="select(false)"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script lang="ts" setup>
import { computed, ref, /*watch,*/ } from 'vue'
import type { IColorTypes, ICheckboxData, ICheckboxDataItem, IBlockTaskChangeKeys } from '@/types'

import { CHANGES } from '@/app/constants/blocks.ts'

import { getColorClassByType } from '@/app/helpers/helpers.js'
import { formatDateTime } from '@/app/helpers/helpers_date'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppCheckboxTS from '@/components/ui/checkboxes/AppCheckboxTS.vue'
import InputDateTS from '@/components/dashboard/orders/components/InputDateTS.vue'

// import AppInputDateTS from '@/components/ui/inputs/AppInputDateTS.vue'


interface IProps {
    type?: IColorTypes
    width?: string
    height?: string
    minDate?: Date | string
    maxDate?: Date | string
}

const props = withDefaults(defineProps<IProps>(), {
    type   : 'primary',
    width  : 'min-w-[500px]',
    height : 'min-h-[400px]',
    minDate: () => {
        const d = new Date()
        d.setHours(0, 0, 0, 0)
        return d
    },
    maxDate: () => {
        const d = new Date()
        d.setHours(23, 59, 59, 999)
        return d
    },
})

const actionAtDate                      = ref<string>(formatDateTime())
const change                            = ref<IBlockTaskChangeKeys>(CHANGES.CHANGE_1.NAME)
const comment                           = ref<string>('')
const DEFAULT_WIDTH                     = 'w-[500px]'
const changeCheckboxData: ICheckboxData = {
    name: 'change',
    data: [
        { id: 1, name: 'Смена 1', checked: true },
        { id: 2, name: 'Смена 2', checked: false },
    ],
}

// __ Обработчик чекбокса на Типе расчета
const changeCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        change.value = data.id === 1 ? CHANGES.CHANGE_1.NAME : CHANGES.CHANGE_2.NAME
    }
}

const showModal = ref(false)

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

let resolvePromise: ((value: boolean) => void) | null
const show = () => {
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

defineExpose({
    show,
    get taskData() {
        return {
            action_at: actionAtDate.value,
            change   : change.value,
            comment  : comment.value !== '' ? comment.value : null,
        }
    }
})

// watch(() => props.text, (value) => {
//     displayTextArray.value = getDisplayText(value)
// })

</script>

<style scoped>
.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center;
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between items-center border-l-8 p-4 box-border max-h-[90vh];
}

.text-container {
    @apply w-full flex-1 min-h-0 overflow-y-auto px-2 my-2;
}

.text-data {
    @apply w-full text-white space-y-1;
}

/* Кастомный темно-серый скроллбар */
.text-container::-webkit-scrollbar {
    width: 6px;
}

.text-container::-webkit-scrollbar-track {
    background: #1e293b;
    border-radius: 3px;
}

.text-container::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 3px;
}

.text-container::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

/* Состояние появления и исчезновения */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.5s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(1.10);
}
</style>
