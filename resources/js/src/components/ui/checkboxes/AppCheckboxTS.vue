<template>

    <div v-if="checkboxData.data.length" :class="[width, bgColor, 'check-box-data-container']">

        <!-- дополнительное расстояние вверху fieldset, если нет легенды-->
        <div v-if="!legend" class="h-[6px]"></div>

        <fieldset class="fieldset-container">

            <legend>
                    <span :class="[textColor, 'legend-text-container']">
                        {{ legend ? '&nbsp' : '' }}
                        {{ legend }}
                        {{ legend ? '&nbsp' : '' }}
                    </span>
            </legend>

            <div :class="[semibold, dir === 'horizontal' ? 'flex items-center justify-around' : '']">
                <div v-for="item in checkBoxObject" :key="item.uniqID">
                    <input
                        :id="item.uniqID"
                        :checked="item.checked"
                        :disabled="disabled ? disabled : item.disabled"
                        :name="checkboxData.name"
                        :type="inputType"
                        :value="item.uniqID"
                        class="cursor-pointer"
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

<script lang="ts" setup>
import { computed, reactive } from 'vue'

import type { ICheckboxData, ICheckboxDataItem } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.js'
import type { IFontsType } from '@/app/constants/fontSizes.js'

import {
    getColorClassByType,
    getFontSizeClass,
    getTextColorClassByType
} from '@/app/helpers/helpers.js'


interface IProps {
    id?: string
    width?: string
    inputType?: 'checkbox' | 'radio'
    dir?: 'vertical' | 'horizontal'
    type?: IColorTypes
    checkboxData?: ICheckboxData
    legend?: string
    textSize?: IFontsType
    bold?: boolean,
    disabled?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    id: () => Date.now().toString(),
    width: 'w-[300px]',
    inputType: 'checkbox',
    dir: 'vertical',
    type: 'dark',
    checkboxData: () => ({name: Date.now().toString(), data: []}),
    legend: '',
    textSize: 'small',
    bold: true,
    disabled: false
})


const emits = defineEmits<{
    (e: 'checked', payload: ICheckboxDataItem | ICheckboxDataItem[]): void,
}>()

// __ добавляем uniqID, чтобы не было коллизий
// __ и добавляем checked в зависимости от типа кнопок

const tempData = props.checkboxData.data.map((item: ICheckboxDataItem, /*idx: number*/) => {
    return {
        ...item,
        uniqID: props.id + '_' + item.id,
        checked: item.checked ?? false
        // uniqID: Date.now().toString() + '_' + item.id,
    }
})

// если это radio и есть несколько checked, то выбираем первый
if (props.inputType === 'radio') {

    let trueIndex = tempData.findIndex(item => item.checked)
    if (trueIndex === -1) trueIndex = 0

    // const initValue = -1
    // const trueIndex = tempData.reduce(
    //     (acc, item, idx) => (item.checked && acc === initValue) ? acc = idx : acc = acc, initValue
    // )

    tempData.forEach((item, idx) => {
        item.checked = idx === trueIndex
    })
}

// __ Создаем объект отслеживания изменений checkbox
const checkBoxObject = reactive(tempData)
// console.log(checkBoxObject)

const bgColor = computed(() => getColorClassByType(props.type, 'bg', 0, false))                   // Получаем класс для цвета заднего фона
const textColor = computed(() => getTextColorClassByType(props.type))

const textSizeClass = computed(() => getFontSizeClass(props.textSize))
const semibold = computed(() => props.bold ? 'font-semibold' : '')

// __ Обработчик события checked
const checked = (e: Event) => {
    const target = e.target as HTMLInputElement

    const idx = checkBoxObject.findIndex(item => item.uniqID === target.value)

    if (props.inputType === 'checkbox') {
        checkBoxObject[idx].checked = !checkBoxObject[idx].checked
    } else {
        // сбрасываем все, кроме выбранного элемента
        checkBoxObject.forEach(item => item.checked = item.uniqID === target.value)
        // console.log(e.target.value)
    }

    // console.log(checkBoxObject[idx].name, checkBoxObject[idx].checked)

    // возвращаем в зависимости от типа кнопок: checkbox (весь объект) или radio (выбранный элемент)
    emits('checked', props.inputType === 'checkbox' ? checkBoxObject : checkBoxObject[idx])              // вызываем событие
}


</script>

<style scoped>

.check-box-data-container {
    @apply m-0.5 rounded-lg cursor-pointer;
}

.fieldset-container {
    @apply ml-1 mr-1 p-1 border-2 rounded-lg cursor-pointer;
}

.legend-text-container {
    @apply text-xs font-semibold cursor-pointer;
}
</style>
