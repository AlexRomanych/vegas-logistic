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
                        :text="text"
                        :type="type"
                        align="center"
                        height="h-[200px]"
                        type="danger"
                        width="w-[500px]"
                    />
                </div>

                <!-- attract: Сами показатели -->
                <div class="m-2">

                    <!-- attract: Отстеганная длина, м.п. -->
                    <div class="flex">
                        <AppLabel
                            :type="type"
                            align="right"
                            text="Отстеганная длина, м.п."
                            width="w-[250px]"
                        />

                        <AppInputNumber
                            id="number"
                            v-model:input-number="rollingLength"
                            :type="type"
                            :value="round(rollingLength, 1).toString()"
                            align="center"
                            step="1"
                            width="w-[80px]"
                            @input="handleRollingLength"
                        />
                    </div>

                    <!-- attract: Отстеганная доля рулона, % -->
                    <div class="flex">
                        <AppLabel
                            :type="type"
                            align="right"
                            text="Отстеганная доля рулона, %"
                            width="w-[250px]"
                        />

                        <AppInputNumber
                            id="number"
                            v-model:input-number="rollingPart"
                            :type="type"
                            :value="rollingPart.toFixed(1)"
                            align="center"
                            step="1"
                            width="w-[80px]"
                            @input="handleRollingPart"
                        />
                    </div>

                    <!-- attract: Затраченное время, мин. -->
                    <div class="flex">
                        <AppLabel
                            align="right"
                            text="Затраченное время, мин."
                            type="dark"
                            width="w-[250px]"
                        />

                        <AppLabel
                            :text="rollingTime.toFixed(1)"
                            align="center"
                            text-size="small"
                            type="dark"
                            width="w-[80px]"
                        />

                    </div>


                    <!-- attract: Затраченное время, мин. -->
                    <!--                    <div class="flex">-->
                    <!--                        <AppLabel-->
                    <!--                            :type="type"-->
                    <!--                            align="right"-->
                    <!--                            text="Затраченное время, мин."-->
                    <!--                            width="w-[250px]"-->
                    <!--                        />-->

                    <!--                        <AppInputNumber-->
                    <!--                            id="number"-->
                    <!--                            v-model:input-number="rollingTime"-->
                    <!--                            :type="type"-->
                    <!--                            :value="rollingTime.toFixed(1)"-->
                    <!--                            align="center"-->
                    <!--                            step="1"-->
                    <!--                            width="w-[80px]"-->
                    <!--                        />-->
                    <!--                    </div>-->

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

import {round} from '/resources/js/src/app/helpers/helpers_lib.js'
import {colorsList} from '/resources/js/src/app/constants/colorsClasses.js'
import {getColorClassByType} from '/resources/js/src/app/helpers/helpers.js'

import AppInputButton from '/resources/js/src/components/ui/inputs/AppInputButton.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppInputNumber from '/resources/js/src/components/ui/inputs/AppInputNumber.vue'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'

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
        type: Array,
        required: false,
        default: () => [],
        validator: (text) => Array.isArray(text)
    },
    mode: {
        type: String,
        required: false,
        default: 'inform',
        validator: (mode) => ['inform', 'confirm'].includes(mode)
    },
    rollLength: {
        type: Number,
        required: false,
        default: 0,
    },
})


const emit = defineEmits(['select'])

const showModal = ref(false)                    // реактивность видимости модального окна

const rollingRoll = ref({})      // реактивность рулона

// attract: Получаем отстеганную длину
const getRollingLength = (rollingPart = 0, textileLength = 0) => rollingPart * textileLength / 100
const rollingLength = ref(getRollingLength())

// attract: Получаем отстеганную часть
const getRollingPart = (rollingLength = 0, textileLength = 0) => textileLength === 0 ? 0 : rollingLength / textileLength * 100
const rollingPart = ref(getRollingPart())

// attract: Получаем отстеганное время
const getRollingTime = (rollingLength = 0, productivity = 0) => productivity === 0 ? 0 : rollingLength / productivity * 60
const rollingTime = ref(getRollingTime())

const getSaveButtonState = () => true// targetText.value.trim() !== ''
const saveButtonState = ref(getSaveButtonState())      // реактивность состояния кнопки "Сохранить" в зависимости от area

// attract: Обработка асинхронности
let resolvePromise
const show = (inRollingRoll) => {                  // передаем сюда значение для area, потому что может быть не определен props на момент вызова. Это своего рода подстраховка

    // console.log('rollingRoll show: ', inRollingRoll.value)
    rollingRoll.value = inRollingRoll.value

    showModal.value = true;
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
    rollingLength: rollingLength,
})


watch(() => rollingRoll.value, (newRollingRoll, oldRollingRoll) => {
    rollingLength.value = getRollingLength(newRollingRoll.textile_length)

    rollingPart.value = getRollingPart(rollingLength.value, newRollingRoll.textile_length)
}, {deep: true, immediate: true})


// attract: Следим за отстеганной длиной
watch(() => rollingLength.value, (newRollingLength, oldRollingLength) => {
    rollingRoll.rolling_length = newRollingLength
    rollingPart.value = getRollingPart(newRollingLength, rollingRoll.value.textile_length)
    rollingTime.value = getRollingTime(newRollingLength, rollingRoll.value.productivity)
    saveButtonState.value = rollingLength.value >= 1 && rollingLength.value <= rollingRoll.value.textile_length        // флаг кнопки сохранения
})


// attract: Следим за частью рулона
watch(() => rollingPart.value, (newRollingPart, oldRollingPart) => {
    rollingLength.value = getRollingLength(newRollingPart, rollingRoll.value.textile_length)
    rollingRoll.rolling_length = rollingLength.value
    rollingTime.value = getRollingTime(rollingLength.value, rollingRoll.value.productivity)
    saveButtonState.value = rollingLength.value >= 1 && rollingLength.value <= rollingRoll.value.textile_length        // флаг кнопки сохранения
})


// attract: Следим за выходом за границы диапазона длины
const handleRollingLength = (event) => {
    rollingLength.value = event.target.value
    if (event.target.value > rollingRoll.value.textile_length) {
        rollingLength.value = rollingRoll.value.textile_length
    } else if (event.target.value < 0) {
        rollingLength.value = 0
    }
}


// attract: Следим за выходом за границы диапазона части
const handleRollingPart = (event) => {
    rollingPart.value = event.target.value
    if (event.target.value > 100) {
        rollingPart.value = 100
    } else if (event.target.value < 0) {
        rollingPart.value = 0
    }
}


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
