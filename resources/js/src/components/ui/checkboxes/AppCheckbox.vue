<template>
    <div v-if="checkboxData.data.length" :class="[width, bgColor, 'check-box-data-container']">
        <fieldset class="fieldset-container">

            <legend>
                    <span  :class="[textColor, 'legent-text-container']">
                        {{ legend ? '&nbsp' : '' }}
                        {{ legend }}
                        {{ legend ? '&nbsp' : '' }}
                    </span>
            </legend>

            <div v-for="item in checkBoxObject" :key="item.uniqID">
                <input
                    :id="item.uniqID"
                    :checked="item.checked"
                    :disabled="item.disabled"
                    :name="item.name"
                    :value="item.uniqID"
                    @change="checked"
                    type="checkbox"
                />
                <label
                    :class="[textColor]"
                    :for="item.uniqID"
                    class="ml-1"
                >
                    {{ item.name }}
                </label>
            </div>

        </fieldset>
<!-- дополнительное расстояние снизу fieldset-->
    <div class="h-[6px]"></div>

    </div>
</template>

<script setup>
import {colorsList} from "@/src/app/constants/colorsClasses.js"
import {getColorClassByType, getTextColorClassByType} from "@/src/app/helpers/helpers.js"
import {computed, reactive} from "vue";

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
    checkboxData: {
        type: Object,
        required: false,
        default: {name: Date.now().toString(), data: []},
    },
    legend: {
        type: String,
        required: false,
        default: ''
    },


})

// добавляем uniqID, чтобы не было коллизий
const tempData = props.checkboxData.data.map((item, idx) => {
    return {
        ...item,
        uniqID: Date.now().toString() + '_' + item.id,
        checked: item.checked ?? false
    }
})

// создаем объект отслеживания изменений checkbox
const checkBoxObject = reactive(tempData)

console.log(checkBoxObject)

const emit = defineEmits(['checked'])
const checked = (e) => {
    const idx = checkBoxObject.findIndex(item => item.uniqID === e.target.value)
    checkBoxObject[idx].checked = !checkBoxObject[idx].checked

//    console.log(checkBoxObject[idx].name, checkBoxObject[idx].checked)

    emit('checked', checkBoxObject[idx])              // вызываем событие
}

const bgColor = computed(() => getColorClassByType(props.type, 'bg', 0, false))                   // Получаем класс для цвета заднего фона
const textColor = computed(() => getTextColorClassByType(props.type))

</script>

<style scoped>

.check-box-data-container {
    @apply m-1 rounded-lg;
}

.fieldset-container {
    @apply m-1 p-1 border-2 rounded-lg;
}

.legent-text-container {
    @apply text-xs font-semibold;
}
</style>
