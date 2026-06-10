<template>
    <div v-if="checkboxData.data.length" :class="[width, bgColor, 'check-box-data-container']">

        <div v-if="!legend" class="h-[6px]"></div>

        <fieldset class="fieldset-container">
            <legend>
                <span :class="[textColor, 'legend-text-container']">
                    {{ legend ? '&nbsp;' : '' }}
                    {{ legend }}
                    {{ legend ? '&nbsp;' : '' }}
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

        <div class="h-[6px]"></div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

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

// __ Превращаем локальный объект в динамический computed
const checkBoxObject = computed(() => {
    // Карту данных строим на основании актуальных props
    const tempData = props.checkboxData.data.map((item: ICheckboxDataItem) => {
        return {
            ...item,
            uniqID: props.id + '_' + item.id,
            checked: item.checked ?? false
        }
    })

    // Если это radio и есть несколько checked, оставляем только первый
    if (props.inputType === 'radio') {
        let trueIndex = tempData.findIndex(item => item.checked)
        if (trueIndex === -1) trueIndex = 0

        tempData.forEach((item, idx) => {
            item.checked = idx === trueIndex
        })
    }

    return tempData
})

const bgColor = computed(() => getColorClassByType(props.type, 'bg', 0, false))
const textColor = computed(() => getTextColorClassByType(props.type))
const textSizeClass = computed(() => getFontSizeClass(props.textSize))
const semibold = computed(() => props.bold ? 'font-semibold' : '')

// __ Обработчик события checked
const checked = (e: Event) => {
    const target = e.target as HTMLInputElement

    // Делаем глубокую копию текущего состояния из computed, чтобы не мутировать props напрямую
    const updatedData = JSON.parse(JSON.stringify(checkBoxObject.value))
    const idx = updatedData.findIndex((item: any) => item.uniqID === target.value)

    if (props.inputType === 'checkbox') {
        updatedData[idx].checked = !updatedData[idx].checked

        // Для чекбоксов возвращаем весь массив (очистив от служебных uniqID)
        const originalFormatArray = updatedData.map(({ uniqID, ...rest }: any) => rest)
        emits('checked', originalFormatArray)
    } else {
        // Для radio проставляем checked только одному
        updatedData.forEach((item: any) => item.checked = item.uniqID === target.value)

        // Вытаскиваем обратно оригинальный объект без uniqID
        const { uniqID, ...originalFormatItem } = updatedData[idx]
        emits('checked', originalFormatItem)
    }
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
