<template>
    <Teleport to="body">
        <div v-if="showModal" class="dark-container">
            <div :class="[width, height, borderColor, 'modal-container']">

                <!-- __ Крестик закрытия окна -->
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

                <!-- __ Информационный текст -->
                <template v-if="styleLabel">
                    <div class="m-2 w-full px-4">
                        <AppLabelMultiLineTS
                            :text="displayTextArray"
                            :type="type"
                            align="center"
                            rounded="4"
                            width="w-full"
                        />
                    </div>
                </template>
                <template v-else>
                    <div class="text-white">
                        <div v-for="(text, idx) of displayTextArray" :key="idx">
                            {{ text }}
                        </div>
                    </div>
                </template>

                <!-- __ Поле ввода числа -->
                <div class="m-2">
                    <AppInputNumberTS
                        id="number"
                        v-model="targetNumber"
                        :max="effectiveMax"
                        :min="min"
                        :placeholder="placeholder"
                        :step="step"
                        :type="type"
                        :width="width"
                    />
                </div>

                <!-- __ Кнопки управления -->
                <div class="w-full flex justify-end p-2">
                    <div v-if="mode === 'confirm' && isValueValid" class="m-1">
                        <AppInputButton
                            id="save"
                            :type="type"
                            title="Сохранить"
                            @buttonClick="select(true)"
                        />
                    </div>

                    <div class="m-1">
                        <AppInputButton
                            id="cancel"
                            :title="mode === 'confirm' ? 'Отмена' : 'Закрыть'"
                            :type="type"
                            @buttonClick="select(false)"
                        />
                    </div>
                </div>

            </div>
        </div>
    </Teleport>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import type { IColorTypes } from '@/types'
import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppInputNumberTS from '@/components/ui/inputs/AppInputNumberTS.vue'

interface IProps {
    width?: string
    height?: string
    type?: IColorTypes
    text?: string | string[]
    val?: number
    mode?: 'inform' | 'confirm'
    placeholder?: string
    negative?: boolean
    maxValue?: number
    step?: string
    min?: number
    max?: number
    styleLabel?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    width      : 'min-w-[500px]',
    height     : 'min-h-[300px]',
    type       : 'primary',
    text       : 'This is a Modal Window.',
    val        : 0,
    mode       : 'confirm',
    placeholder: 'Введите значение...',
    negative   : false,
    maxValue   : -1,
    step       : '1',
    min        : 0,
    max        : 1_000_000,
    styleLabel : false
})

const showModal    = ref(false)
const targetNumber = ref<number>(props.val)

// Автоматический пересчет текста без применения watchers
const displayTextArray = computed(() => {
    return Array.isArray(props.text) ? props.text : [props.text]
})

// Определение эффективного максимального значения (приоритет у maxValue, если задан)
const effectiveMax = computed(() => {
    return props.maxValue !== -1 ? props.maxValue : props.max
})

// Проверка валидности введенного значения для отображения кнопки "Сохранить"
const isValueValid = computed(() => {
    if (targetNumber.value === null || targetNumber.value === undefined) return false

    if (!props.negative && targetNumber.value < 0) return false
    if (props.min !== undefined && targetNumber.value < props.min) return false
    if (effectiveMax.value !== undefined && targetNumber.value > effectiveMax.value) return false

    return true
})

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

// Управление Promise
let resolvePromise: ((value: boolean) => void) | null = null

const show = (initValue?: number) => {
    targetNumber.value = initValue !== undefined ? initValue : props.val
    showModal.value    = true
    return new Promise<boolean>((resolve) => {
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

// Синхронизация внешнего пропа val, если он меняется извне
watch(() => props.val, (newVal) => {
    targetNumber.value = newVal
})

defineExpose({
    show,
    get inputNumber() {
        return targetNumber.value
    },
    targetNumber,
})
</script>

<style scoped>
.dark-container {
    @apply z-[999] bg-slate-500/95 fixed inset-0 flex justify-center items-center;
}

.modal-container {
    @apply bg-slate-800 rounded-xl flex flex-col justify-between items-center border-l-8 shadow-2xl;
}

.close-cross-container {
    @apply flex justify-end w-full;
}
</style>
