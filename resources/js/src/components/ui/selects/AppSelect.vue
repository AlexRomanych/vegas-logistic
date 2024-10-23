<template>

    <div v-if="selectData.data.length"
         :class="[width, bgColor, 'container']">

        <label v-if="label"
               :for="selectData.name"
               :class="[textColor, 'label']">
            {{ label }}
        </label>

        <select :id="id"
                :multiple="multiple"
                :name="selectData.name"
                :size="multiple ? size : false"
                :class="[bgColor, textColor, 'select']"
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


</template>

<script setup>
import {colorsList} from "@/src/app/constants/colorsClasses.js"
import {getColorClassByType, getTextColorClassByType} from "@/src/app/helpers/helpers.js"
import {computed} from "vue";

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
    type: {
        type: String,
        required: false,
        default: 'secondary',
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
    }


})

const emit = defineEmits(['change'])
const onChange = (e) => {
    const idx = props.selectData.data.findIndex(item => item.id.toString() === e.target.value)
    emit('change', props.selectData.data[idx])              // вызываем событие
}

const bgColor = computed(() => getColorClassByType(props.type, 'bg'))                   // Получаем класс для цвета заднего фона
const textColor = computed(() => getTextColorClassByType(props.type))


</script>

<style scoped>
.container {
    @apply border-[2px] rounded-[6px] h-[58px] m-1 p-0 relative
}

.label {
    @apply absolute text-xs mt-1 ml-1 font-semibold
}

.select {
    @apply m-0 pt-4 h-full w-full rounded-[6px]
}
</style>
