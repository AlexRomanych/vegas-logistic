<template>
    <Teleport to="body">
        <div v-if="showModal" class="dark-container">
            <div :class="[width, height, borderColor, 'modal-container']">

                <div class="close-cross-container flex-none">
                    <div class="m-1 p-1">
                        <AppInputButton
                            id="close" :type="type" height="w-5" title="x" width="w-[30px]"
                            @buttonClick="select(false)"
                        />
                    </div>
                </div>

                <div class="px-6 pt-2 flex-none w-full">
                    <AppLabelMultiLine
                        :text="displayTextArray"
                        :type="type"
                        align="center"
                        width="w-full"
                    />
                    <h3 v-if="legend" class="text-slate-400 text-sm font-semibold mt-4 uppercase tracking-wider">
                        {{ legend }}
                    </h3>
                </div>

                <div class="flex-1 w-full overflow-y-auto overflow-x-hidden custom-scrollbar px-6 py-2">
                    <AppCheckbox
                        :checkboxData="checkboxData"
                        :dir="dir"
                        :inputType="inputType"
                        :type="type"
                        :textSize="textSize"
                        width="w-full"
                        @checked="selectCheckbox"
                    />
                </div>

                <div class="w-full flex justify-end p-4 border-t border-slate-700/30 flex-none bg-slate-800/50 rounded-b-xl">
                    <div class="flex gap-2">
                        <AppInputButton
                            v-if="saveButtonState"
                            id="confirm" :type="type" title="Сохранить"
                            @buttonClick="select(true)"
                        />
                        <AppInputButton
                            id="cancel" :type="type" title="Закрыть"
                            @buttonClick="select(false)"
                        />
                    </div>
                </div>

            </div>
        </div>
    </Teleport>
</template>

<script lang="ts" setup>
import { computed, ref, watch, } from 'vue'

import { colorsList } from '@/app/constants/colorsClasses.js'
import { fontSizesList } from '@/app/constants/fontSizes.js'

import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppCheckbox from '@/components/ui/checkboxes/AppCheckbox.vue'

const props = defineProps({
    width       : {
        type    : String,
        required: false,
        default : 'min-w-[500px]',
    },
    height      : {
        type    : String,
        required: false,
        default : 'min-h-[300px]',
    },
    type        : {
        type     : String,
        required : false,
        default  : 'primary',
        validator: (type) => colorsList.includes(type)
    },
    text        : {
        type     : [String, Array],
        required : false,
        default  : 'This is a Modal Window. Type text here',
        validator: (text) => Array.isArray(text) || typeof text === 'string'
    },
    checkboxData: {
        type    : Object,
        required: false,
        default : () => ({ name: Date.now().toString(), data: [] }),
    },
    legend      : {
        type    : String,
        required: false,
        default : ''
    },
    inputType   : {
        type     : String,
        required : false,
        default  : 'checkbox',
        validator: (type) => ['checkbox', 'radio'].includes(type)
    },
    dir         : {
        type     : String,
        required : false,
        default  : 'vertical',
        validator: (dir) => ['vertical', 'horizontal'].includes(dir)
    },
    textSize    : {
        type     : String,
        required : false,
        default  : 'small',
        validator: (size) => fontSizesList.includes(size)
    },

})

const emit = defineEmits(['select'])

// attract: Это те данные, которые будут возвращены при выборе чекбоксов
const checkData = ref(props.checkboxData.data)

// console.log('chb', checkData.value)

const getDisplayText   = (text) => Array.isArray(text) ? text : [text]
const displayTextArray = ref(getDisplayText(props.text))

// attract: реактивность видимости модального окна
const showModal = ref(false)

// attract: реактивность состояния кнопки "Сохранить" в зависимости от checkboxData
const getSaveButtonState = (checkedData) => checkedData.some(item => item.checked === true)
const saveButtonState    = ref(getSaveButtonState(checkData.value))

// attract: Обработка асинхронности
let resolvePromise
const show = () => {
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
        resolvePromise  = null
    }
}

// attract: Обработка выбора чекбоксов
const selectCheckbox = (data) => {
    // console.log('data: ', checkData.value)
    checkData.value = data
}

// attract: даем родителю доступ к методам и свойствам компонента
defineExpose({
    show,
    checkData,
})

// attract: Следим за отображением текста в модальном окне
watch(() => props.text, (value) => {
    displayTextArray.value = getDisplayText(value)
})

// attract: Следим за состоянием кнопки "Сохранить" по состоянию checkboxData
watch(() => checkData.value, (value) => {
    // console.log('changed: ', value)
    saveButtonState.value = getSaveButtonState(value)
}, { deep: true, immediate: true })

// attract: Следим за изменением самих данных чекбокса
watch(() => props.checkboxData, (value) => {
    checkData.value       = value.data
    saveButtonState.value = getSaveButtonState(checkData.value)
}, { deep: true })

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

</script>

<style scoped>
/*

.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between items-center border-l-8
}

.close-cross-container {
    @apply flex justify-end w-full h-full
}
*/



.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center backdrop-blur-sm;
}

.modal-container {
    /* Добавляем max-height, чтобы модалка не уходила за пределы экрана */
    @apply bg-slate-800 rounded-xl flex flex-col border-l-8 shadow-2xl max-h-[90vh];
}

.close-cross-container {
    @apply flex justify-end w-full;
}

/* Стилизация скроллбара */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    @apply bg-transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-600 rounded-full hover:bg-slate-500 transition-colors;
}

/* Фикс для Firefox */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #475569 transparent;
}
</style>
