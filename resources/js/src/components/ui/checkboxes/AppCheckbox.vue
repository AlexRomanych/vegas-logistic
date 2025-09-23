<template>

    <div v-if="checkboxData.data.length" :class="[width, bgColor, 'check-box-data-container']">

        <!-- дополнительное расстояние вверху fieldset, если нет легенды-->
        <div v-if="!legend" class="h-[6px]"></div>

        <fieldset class="fieldset-container">

            <legend>
                    <span :class="[textColor, 'legent-text-container']">
                        {{ legend ? '&nbsp' : '' }}
                        {{ legend }}
                        {{ legend ? '&nbsp' : '' }}
                    </span>
            </legend>

            <div :class="[semibold, dir === 'horizontal' ? 'flex items-center justify-around' : '']">
                <div v-for="item in checkBoxObject" :key="item.uniqID" >
                    <input
                        class="cursor-pointer"
                        :id="item.uniqID"
                        :checked="item.checked"
                        :disabled="disabled ? disabled : item.disabled"
                        :name="checkboxData.name"
                        :type="inputType"
                        :value="item.uniqID"
                        @change="checked"
                    />
                    <label
                        :class="[textColor, textSizeClass]"
                        :for="item.uniqID"
                        class="ml-1 cursor-pointer"
                    >
                        {{ item.name }}
                    </label>
                </div>
            </div>
        </fieldset>

        <!-- дополнительное расстояние снизу fieldset-->
        <div class="h-[6px]"></div>

    </div>
</template>

<script setup>
import {colorsList} from '/resources/js/src/app/constants/colorsClasses.js'
import {getColorClassByType, getFontSizeClass, getTextColorClassByType} from '/resources/js/src/app/helpers/helpers.js'
import {computed, reactive, ref} from 'vue'
import {fontSizesList} from '/resources/js/src/app/constants/fontSizes.js'

const props = defineProps({
    id: {
        type: String,
        required: false,
        default: Date.now().toString()
    },
    width: {
        type: String,
        required: false,
        default: 'w-[300px]'
    },
    inputType: {
        type: String,
        required: false,
        default: 'checkbox',
        validator: (type) => ['checkbox', 'radio'].includes(type)
    },
    dir: {
        type: String,
        required: false,
        default: 'vertical',
        validator: (dir) => ['vertical', 'horizontal'].includes(dir)
    },
    type: {
        type: String,
        required: false,
        default: 'dark',
        validator: (type) => colorsList.includes(type)
    },
    checkboxData: {
        type: Object,
        required: false,
        default: () => ({name: Date.now().toString(), data: []}),
    },
    legend: {
        type: String,
        required: false,
        default: ''
    },
    textSize: {
        type: String,
        required: false,
        default: 'small',
        validator: (size) => fontSizesList.includes(size)
    },
    bold: {
        type: Boolean,
        required: false,
        default: true
    },
    disabled: {
        type: Boolean,
        required: false,
        default: false
    }
})


const emit = defineEmits(['checked'])

// добавляем uniqID, чтобы не было коллизий
// и добавляем checked в зависимости от типа кнопок

const tempData = props.checkboxData.data.map((item, idx) => {
    return {
        ...item,
        uniqID: props.id + '_' + item.id,
        // uniqID: Date.now().toString() + '_' + item.id,
        checked: item.checked ?? false
    }
})

// если это radio и есть несколько checked, то выбираем первый
if (props.inputType === 'radio') {

    const initValue = -1
    const trueIndex = tempData.reduce(
        (acc, item, idx) => (item.checked && acc === initValue) ? acc = idx : acc = acc,
        initValue
    )

    tempData.forEach((item, idx) => { item.checked = idx === trueIndex})
}


// создаем объект отслеживания изменений checkbox
const checkBoxObject = reactive(tempData)

// console.log(checkBoxObject)


const checked = (e) => {
    const idx = checkBoxObject.findIndex(item => item.uniqID === e.target.value)

    if (props.inputType === 'checkbox') {
        checkBoxObject[idx].checked = !checkBoxObject[idx].checked
    } else {
        // сбрасываем все, кроме выбранного элемента
        checkBoxObject.forEach(item => item.checked = item.uniqID === e.target.value)
        // console.log(e.target.value)
    }

//    console.log(checkBoxObject[idx].name, checkBoxObject[idx].checked)

    // возвращаем в зависимости от типа кнопок: checkbox (весь объект) или radio (выбранный элемент)
    emit('checked', props.inputType === 'checkbox' ? checkBoxObject : checkBoxObject[idx])              // вызываем событие
}

const bgColor = computed(() => getColorClassByType(props.type, 'bg', 0, false))                   // Получаем класс для цвета заднего фона
const textColor = computed(() => getTextColorClassByType(props.type))

const textSizeClass = ref(getFontSizeClass(props.textSize))
const semibold = props.bold ? 'font-semibold' : ''

</script>

<style scoped>

.check-box-data-container {
    @apply m-0.5 rounded-lg cursor-pointer;
}

.fieldset-container {
    @apply ml-1 mr-1 p-1 border-2 rounded-lg cursor-pointer;
}

.legent-text-container {
    @apply text-xs font-semibold cursor-pointer;
}
</style>
