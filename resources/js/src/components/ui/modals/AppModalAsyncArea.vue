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

                    <AppInputTextAreaSimple
                        id="text-area"
                        v-model.trim="targetText"
                        :placeholder="placeholder"
                        :rows=4
                        :type="type"
                        :value="targetText"
                        :width="width"
                        height="h-[150px]"
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
import {isNumeric} from '/resources/js/src/app/helpers/helpers_lib.js'
import {colorsList} from '/resources/js/src/app/constants/colorsClasses.js'
import {getColorClassByType} from '/resources/js/src/app/helpers/helpers.js'
import AppInputButton from '/resources/js/src/components/ui/inputs/AppInputButton.vue'
import AppInputTextAreaSimple from '/resources/js/src/components/ui/inputs/AppInputTextAreaSimple.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppInputNumber from '/resources/js/src/components/ui/inputs/AppInputNumber.vue'

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
    value: {                    // текст внутри самого area
        type: String,
        required: false,
        default: '',
    },
    placeholder: {
        type: String,
        required: false,
        default: 'Введите текст...',
    },
    mode: {
        type: String,
        required: false,
        default: 'inform',
        validator: (mode) => ['inform', 'confirm'].includes(mode)
    },
})

const emit = defineEmits(['select'])


const getDisplayText = (text) => Array.isArray(text) ? text : [text]
let displayTextArray = ref(getDisplayText(props.text))

const showModal = ref(false)                    // реактивность видимости модального окна

// attract: Если поле текстовое
const targetText = ref(props.value)                    // реактивность текста сообщения в area

const getSaveButtonState = () => targetText.value.trim() !== ''
const saveButtonState = ref(getSaveButtonState())      // реактивность состояния кнопки "Сохранить" в зависимости от area

// attract: Обработка асинхронности
let resolvePromise
const show = (initTextValue = '') => {                  // Передаем сюда значение для area, потому что может быть не определен props на момент вызова. Это своего рода подстраховка
    targetText.value = initTextValue                                    // для текстовой модели
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
    inputText: targetText,
 })

// attract: Следим за отображением текста в модальном окне
watch(() => props.text, (value) => {
    displayTextArray.value = getDisplayText(value)
})

// attract: Следим за состоянием текста в area
watch(() => targetText.value, (newValue) => {
    saveButtonState.value = getSaveButtonState()
})


// attract: Следим за инициализирующим значением
watch(() => props.value, (newValue) => {
    targetText.value = newValue
}, {deep:true, immediate: true})

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

.text-container {
    @apply flex items-end
}

.text-data {
    @apply border-2 border-slate-800 w-full h-full text-white
}

.close-button-container {
    @apply w-full h-full flex justify-end
}
</style>
