<template>

    <Teleport to="body">

        <div v-if="showModal"
             class="dark-container">

            <div :class="[width, height, borderColor, 'modal-container']">

                <!-- attract: Крестик закрытия окна -->
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

                <!-- attract: Информационный текст -->
                <div class="m-2">

                    <AppLabelMultiLine
                        :text="displayTextArray"
                        :type="type"
                        :width="width"
                        align="center"
                    />

                </div>

                <!-- attract: Само поле для ввода -->
                <div class="m-2">

                    <AppInputNumber
                        id="number"
                        v-model:input-number="targetNumber"
                        :placeholder="placeholder"
                        :step="step"
                        :type="type"
                        :value="targetNumber.toString()"
                        :width="width"
                        @input="updateNumber"
                    />

                </div>

                <div class="w-full h-full flex justify-end">

                    <div v-if="mode === 'confirm'" class="m-1 p-1">

                        <AppInputButton
                            v-if="saveButtonState"
                            id="confirm"
                            :type="type"
                            title="Сохранить"
                            @buttonClick="select(true)"
                        />

                    </div>

                    <div class="m-1 p-1">

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

</template>

<script setup>

import {computed, ref, watch,} from 'vue'

import {colorsList} from '@/app/constants/colorsClasses.js'

import {getColorClassByType} from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppInputNumber from '@/components/ui/inputs/AppInputNumber.vue'

// import {isNumeric} from '/resources/js/src/app/helpers/helpers_lib.js'
// import AppInputTextAreaSimple from '/resources/js/src/components/ui/inputs/AppInputTextAreaSimple.vue'

const props = defineProps({
    width: {
        type: String,
        required: false,
        default: 'min-w-[500px]',
    },
    height: {
        type: String,
        required: false,
        default: 'min-h-[300px]',
    },
    type: {
        type: String,
        required: false,
        default: 'primary',
        validator: (type) => colorsList.includes(type)
    },
    text: {
        type: [String, Array],
        required: false,
        default: 'This is a Modal Window. Type text here',
        validator: (text) => Array.isArray(text) || typeof text === 'string'
    },
    value: {
        type: Number,
        required: false,
        default: 0,
    },
    mode: {
        type: String,
        required: false,
        default: 'inform',
        validator: (mode) => ['inform', 'confirm'].includes(mode)
    },
    placeholder: {
        type: String,
        required: false,
        default: 'Введите значение...',
    },
    negative: {             // Возможность ввода отрицательных чисел
        type: Boolean,
        required: false,
        default: false,
    },
    maxValue: {
        type: Number,
        required: false,
        default: -1,
    },
    step: {
        type: String,
        required: false,
        default: '1',
    }
})

const emit = defineEmits(['select'])

// attract: Вычисляемые свойства
const getDisplayText = (text) => Array.isArray(text) ? text : [text]
let displayTextArray = ref(getDisplayText(props.text))

const showModal = ref(false)                    // реактивность видимости модального окна

// attract: Если поле числовое
const targetNumber = ref(props.value)

// attract: реактивность состояния кнопки "Сохранить" в зависимости от
const getSaveButtonState = () => true
const saveButtonState = ref(getSaveButtonState())

// attract: Обработка асинхронности
let resolvePromise
const show = (initValue = 0) => { // Передаем сюда значение для поля ввода, потому что может быть не определен props на момент вызова. Это своего рода подстраховка
    targetNumber.value = initValue
    showModal.value = true
    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value) => {
    if (resolvePromise) {
        resolvePromise(value)
        // resolvePromise( {value, text: targetText.value})    // возвращает объект, можно так возвращать результаты
        showModal.value = false
        resolvePromise = null
    }
}

// attract: даем родителю доступ к методам и свойствам компонента
defineExpose({
    show,
    // inputNumber: () => targetNumber.value,      // работает, нужно вызывать как функцию
    get inputNumber() {
        return parseFloat(targetNumber.value)
    },
    // inputNumber: ()=>toValue(targetNumber),
    // inputNumber: parseFloat(targetNumber.value),
    x: 100,
})


// attract: Обработка события изменения числа в поле ввода
const updateNumber = (event) => {

    console.log('number: ', event.target.value)
    return

    targetNumber.value = event.target.value


    // проверка на отрицательность
    if (!props.negative) {
        if (targetNumber.value < 0) {
            targetNumber.value = 0
            return
        }
    }

    // проверка на максимальное значение
    if (props.maxValue !== -1) {
        if (targetNumber.value > props.maxValue) targetNumber.value = props.maxValue
    }
}


// attract: Следим за отображением текста в модальном окне
watch(() => props.text, (value) => {
    displayTextArray.value = getDisplayText(value)
})

// attract: Следим за состоянием числа в appInputNumber
watch(() => targetNumber.value, (newValue) => {

    // проверка на максимальное значение
    if (props.maxValue !== -1) {
        if (newValue > props.maxValue) {
            targetNumber.value = props.maxValue
            saveButtonState.value = true
            return
        }
    }

    // проверка на отрицательность
    if (!props.negative) {
        if (targetNumber.value < 0) {
            targetNumber.value = 0
            saveButtonState.value = true
            return
        }
    }

    saveButtonState.value = newValue > 0 && newValue <= props.maxValue
})

// attract: Следим за инициализирующим значением
watch(() => props.value, (newValue) => {
    targetNumber.value = newValue
}, {deep: true, immediate: true})

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

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

</style>
