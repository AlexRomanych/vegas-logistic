<template>

    <div v-if="selectData.data.length">

        <div :class="[width, height, bgColor, 'container', align]">

            <div>

                <!-- __ Убрал пока, так как глюки и надо разбираться :size="multiple ? size : ''"       -->
                <select :id="id"
                        :class="[bgColor, textColor, textSizeClass, semibold, 'select']"
                        :disabled="disabled"
                        :multiple="multiple"

                        :name="selectData.name"

                        @change="onChange">

                    <option v-for="item in selectData.data"
                            :key="item.id"
                            :disabled="item.disabled"
                            :selected="item.selected"
                            :value="item.id"

                    >
                        {{ item.name }}
                    </option>

                </select>

            </div>
        </div>
    </div>

</template>

<script setup>
import { computed, ref, watch } from 'vue'

import { colorsList } from '@/app/constants/colorsClasses.js'
import { fontSizesList } from '@/app/constants/fontSizes.js'
import {
    getColorClassByType,
    getFontSizeClass,
    getTextColorClassByType,
} from '@/app/helpers/helpers.js'

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
    height: {
        type: String,
        required: false,
        default: 'h-[30px]'
    },
    type: {
        type: String,
        required: false,
        default: 'dark',
        validator: (type) => colorsList.includes(type)
    },
    selectData: {
        type: Object,
        required: false,
        default: {name: Date.now().toString(), data: []},
    },
    label: {
        type: String,
        required: false,
        default: 'Выберите...'
    },
    multiple: {
        type: Boolean,
        required: false,
        default: false
    },
    size: {
        type: String,
        required: false,
        default: '4'
    },
    textSize: {
        type: String,
        required: false,
        default: 'normal',
        validator: (size) => fontSizesList.includes(size)
        // validator: (size) => ['micro', 'mini', 'normal', 'small', 'large', 'huge'].includes(size)
    },
    bold: {
        type: Boolean,
        required: false,
        default: true,
    },
    disabled: {
        type: Boolean,
        required: false,
        default: false
    },
    align: {
        type: String,
        required: false,
        default: 'between',
        validator: (align) => ['left', 'center', 'right', 'between'].includes(align)
    }


})

const emit = defineEmits(['change'])
const onChange = (e) => {
    const idx = props.selectData.data.findIndex(item => item.id.toString() === e.target.value)
    emit('change', props.selectData.data[idx])              // вызываем событие
}

let textSizeClass = ref(getFontSizeClass(props.textSize))

let bgColor = ref(getColorClassByType(props.type, 'bg'))                   // Получаем класс для цвета заднего фона
let textColor = ref(getTextColorClassByType(props.type))

// let bgColor = computed(() => getColorClassByType(props.type, 'bg'))                   // Получаем класс для цвета заднего фона
// let textColor = computed(() => getTextColorClassByType(props.type))

const semibold = props.bold ? 'font-semibold' : ''

const align = computed(() => {
    if (props.align === 'center') {
        return 'flex justify-center'
    } else if (props.align === 'right') {
        return 'justify-end'
    } else if (props.align === 'left') {
        return 'justify-start'
    }
    return '' // По умолчанию props.align === 'between'
})


// реактивный тип
watch(() => props.type, (newType) => {
    bgColor.value = getColorClassByType(newType, 'bg')                   // Получаем класс для цвета заднего фона
    textColor = getTextColorClassByType(newType)
})

// реактивный шрифт
watch(() => props.textSize, (newSize) => textSizeClass.value = getFontSizeClass(newSize))


</script>

<style scoped>
.container {
    @apply rounded-lg
}

.label {
    @apply text-mc mt-1 ml-1 font-semibold
}

.select {
    @apply h-full w-full rounded-lg
}
</style>
