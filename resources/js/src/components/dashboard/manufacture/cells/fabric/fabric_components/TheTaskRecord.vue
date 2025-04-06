<template>

    <!-- attract: Режим отображения + Режим редактирования -->
    <div class="flex">

        <!-- attract: Номер рулона -->
        <!--        <AppLabelMultiLine-->
        <!--            :text="!editMode ? roll.num.toString() : [roll.num.toString(), '']"-->
        <!--            align="center"-->
        <!--            height="h-[30px]"-->
        <!--            text-size="mini"-->
        <!--            width="w-[80px]"-->
        <!--        />-->

        <!-- attract: ПС -->
        <div v-if="!editMode">
            <AppLabelMultiLine
                :text="!editMode ? roll.fabric : [roll.fabric, '']"
                height="h-[30px]"
                text-size="mini"
                :type="typeForErrorsAndConstraintsForLabel"
                width="w-[300px]"
            />
        </div>
        <div v-else>
            <AppSelect
                :multiple="false"
                :selectData="selectData"
                height="h-[64px]"
                label="Выберите ПС:"
                text-size="mini"
                :type="typeForErrorsAndConstraintsForSelect"
                width="w-[304px]"
                @change="selectFabric"
            />
        </div>

        <!-- attract: Средняя длина рулона -->
        <!--        :text="!editMode ? roll.average_length.toFixed(2) : [roll.average_length.toFixed(2), '']"-->
        <AppLabelMultiLine
            :text="!editMode ? averageLength.toFixed(2) : [averageLength.toFixed(2), '']"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[100px]"
        />

        <!-- attract: Количество в рулонах -->
        <div v-if="!editMode">
            <AppLabelMultiLine
                :text="Number.isInteger(rollsAmount) ? rollsAmount.toFixed(0) : rollsAmount.toFixed(3)"
                :type="isRollsAmountFractional ? 'danger' : 'primary'"
                align="center"
                height="h-[30px]"
                text-size="mini"
                width="w-[70px]"
            />
        </div>
        <div v-else>
            <AppInputNumber
                id="rolls_amount"
                v-model:input-number="rollsAmount"
                :fraction-digits=2
                :type="isRollsAmountFractional ? 'danger' : 'primary'"
                :value=rollsAmount
                align="center"
                height="h-[60px]"
                text-size="mini"
                width="w-[70px]"
                @blur="getLengthAmount"
                @change="getLengthAmount"
                @input="getLengthAmount"
            />
        </div>

        <!-- attract: Количество в м.п. -->
        <div v-if="!editMode">
            <AppLabelMultiLine
                :text="lengthAmount.toFixed(2)"
                align="center"
                height="h-[30px]"
                text-size="mini"
                type="primary"
                width="w-[70px]"
            />
        </div>
        <div v-else>
            <AppInputNumber
                id="length_amount"
                v-model:input-number="lengthAmount"
                :fraction-digits=2
                :value=lengthAmount
                align="center"
                height="h-[60px]"
                step="0.01"
                text-size="mini"
                type="primary"
                width="w-[70px]"
                @change="getRollsAmount"
                @input="getRollsAmount"
            />
            <!-- warning: оставим только событие change  -->
        </div>

        <!--        <AppLabelMultiLine-->
        <!--            :text="!editMode ? '201.6' : ['201.6', '']"-->
        <!--            align="center"-->
        <!--            height="h-[30px]"-->
        <!--            text-size="mini"-->
        <!--            type="primary"-->
        <!--            width="w-[70px]"-->
        <!--        />-->

        <!-- attract: Трудозатраты -->
        <AppLabelMultiLine
            :text="!editMode ? '208' : ['208', '']"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[80px]"
        />

        <!-- attract: Комментарий -->
        <div v-if="!editMode">
            <AppLabel
                :text="roll.descr"
                class="truncate"
                height="h-[30px]"
                text-size="mini"
                type="primary"
                width="w-[300px]"
            />
        </div>
        <div v-else>
            <AppInputTextArea
                id="comment"
                :rows=2
                :value="roll.descr"
                class="cursor-pointer"
                height="min-h-[60px]"
                text-size="mini"
                type="primary"
                width="w-[304px]"
            />
        </div>

        <!-- attract: Управляющие кнопки -->
        <div v-if="!editMode" class="flex">

            <!-- attract: Редактировать -->
            <AppLabel
                align="center"
                class="cursor-pointer font-bold"
                height="h-[30px]"
                text="Ред."
                text-size="mini"
                type="warning"
                width="w-[50px]"
                @click="setEditMode"
            />

            <!-- attract: Удалить -->
            <AppLabel
                align="center"
                class="cursor-pointer font-bold"
                height="h-[30px]"
                text="Х"
                text-size="mini"
                type="danger"
                width="w-[50px]"
            />

        </div>

        <div v-else class="flex">

            <!-- attract: Отменить -->
            <AppLabelMultiLine
                :text="['Отмена', '']"
                align="center"
                class="cursor-pointer font-bold"
                height="h-[30px]"
                text-size="mini"
                type="warning"
                width="w-[50px]"
                @click="cancelEditMode"
            />

            <!-- attract: Сохранить -->
            <AppLabelMultiLine
                :text="['V', '']"
                align="center"
                class="cursor-pointer font-bold"
                height="h-[30px]"
                text-size="mini"
                type="success"
                width="w-[50px]"
                @click="saveTaskRecord"
            />

        </div>

    </div>

</template>

<script setup>

import {computed, reactive, ref, watch} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'
import {storeToRefs} from 'pinia';

import {
    filterFabricsByMachineId,
    getAddFabricMode
} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppInputTextArea from '/resources/js/src/components/ui/inputs/AppInputTextArea.vue'
import AppInputNumber from '/resources/js/src/components/ui/inputs/AppInputNumber.vue'
import AppSelect from '/resources/js/src/components/ui/selects/AppSelect.vue'
import {FABRIC_MACHINES} from '/resources/js/src/app/constants/fabrics.js';

const props = defineProps({
    roll: {
        type: Object,
        required: false,
        default: () => ({})
    },
    // editMode: {
    //     type: Boolean,
    //     required: false,
    //     default: false
    // },
    selectData: {
        type: Object,
        required: false,
        default: () => ({})
    },
    machine: {
        type: Object,
        required: false,
        default: () => FABRIC_MACHINES.AMERICAN,
        validator: (machine) => [
            FABRIC_MACHINES.AMERICAN,
            FABRIC_MACHINES.GERMAN,
            FABRIC_MACHINES.CHINA,
            FABRIC_MACHINES.KOREAN,
        ].includes(machine)
    }
})

const emits = defineEmits(['saveTaskRecord',])

// attract: Получаем данные из хранилища по ПС
const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory


// attract: Получаем тип раскраски ошибки и ограничения для рулона
const getTypeForErrorsAndConstraintsForLabel = () => {
    if (props.roll.fabric_id === 0) {
        return 'danger'
    }
    // if (props.roll.fabric_id === 0) return 'danger'

    // console.log(props.roll.fabric_id)

    // console.log(props.roll.fabric_id)


    const fabricMode = getAddFabricMode(fabrics, props.machine.ID, props.roll.fabric_id)

    // Проверяем, если режим выбора ПС не основной, то возвращаем тип ошибки
    if (fabricsStore.globalFabricsMode && !fabricMode) return 'warning'

    console.log('fabricMode: ', fabricMode)

    return 'primary'
}



// attract: Определяем переменные для стилей
const typeForErrorsAndConstraintsForSelect = ref('primary')
const typeForErrorsAndConstraintsForLabel = ref(getTypeForErrorsAndConstraintsForLabel())



// attract: Определяем объект selectData для ПС в зависимости от выбранного рулона
let selectData = {}
const getSelectData = () => {

    // console.log('getSelectData: ', fabricsStore.globalFabricsMode)

    // attract: Фильтруем ПС в зависимости от выбранного режима ПС
    const filteredFabrics = filterFabricsByMachineId(fabrics, props.machine.ID, fabricsStore.globalFabricsMode)

    const data = filteredFabrics.map(fabric => ({
        id: fabric.id,
        name: fabric.display_name,
        selected: fabric.id === props.roll.fabric_id
    }))

    // console.log(data)
    // const currItem = data.find(item => item.selected)
    // console.log(currItem)

    // if (currItem.id === 0) typeForErrorsAndConstraintsForSelect.value = 'danger'

    console.log('data: ', data)

    return {name: 'fabrics', data}
}

// attract: Обрабатываем событие выбора ПС. Ставим immediate: true, чтобы компонент срабатывал при рендеринге
// selectData = reactive(getSelectData())
watch(() => fabricsStore.globalFabricsMode, () => {
    // console.log(fabricsStore.globalFabricsMode)
    selectData = getSelectData()
    typeForErrorsAndConstraintsForLabel.value = getTypeForErrorsAndConstraintsForLabel()
}, {deep: true, immediate: true})


// attract: Определяем модели для передачи обмена данными
// Длина в рулонах
const rollsAmount = defineModel('rollsAmount', {
    // type: Number,
    required: false,
    default: 0
})
rollsAmount.value = props.roll.rolls_amount

// Длина в м.п.
const lengthAmount = defineModel('lengthAmount', {
    // type: Number,
    required: false,
    default: 0
})
lengthAmount.value = props.roll.rolls_amount * props.roll.average_length

// Флаг дробного количества в рулонах и нулевого количества
const isRollsAmountFractional = computed(() => !Number.isInteger(rollsAmount.value) || rollsAmount.value === 0)
const averageLengthComputed = computed(() => {
    const tempFabric = fabrics.find(fabric => fabric.id === props.roll.fabric_id)
    // console.log(tempFabric)
    // debugger
    return tempFabric.buffer.average_length
})
const averageLength = ref(averageLengthComputed.value)


// attract: Обрабатываем событие выбора ПС

const selectFabric = (item) => {
    const tempFabric = fabrics.find(fabric => fabric.id === item.id)
    averageLength.value = tempFabric.buffer.average_length
    if (item.id === 0) typeForErrorsAndConstraintsForSelect.value = 'danger'

    console.log(item)
    console.log(averageLength.value)
    // emits('selectHandler', item, props.roll)
}

const editMode = ref(false)                     // Переключатель режима редактирования
// attract: Сброс режима редактирования
const resetEditMode = () => {
    editMode.value = false
    fabricsStore.globalEditMode = false                 // Отключаем глобальное редактирование
}

// attract: Устанавливаем режим редактирования
const setEditMode = () => {
    // Проверяем на глобальную переменную редактирования
    if (!fabricsStore.globalEditMode) {
        editMode.value = true
        fabricsStore.globalEditMode = true
    }
}

// attract: Отменяем редактирование
const cancelEditMode = () => resetEditMode()

// attract: Сохраняем рулон
const saveTaskRecord = () => {

    console.log(rollsAmount.value)
    console.log(lengthAmount.value)

    resetEditMode()                                     // Отключаем глобальное редактирование
    emits('saveTaskRecord', false)
}


// attract: Пересчитываем количество в рулонах при изменении длины в м.п.
const getRollsAmount = () => {
    if (lengthAmount.value < 0) lengthAmount.value = 0
    rollsAmount.value = lengthAmount.value / props.roll.average_length
    isRollsAmountFractional.value = !Number.isInteger(rollsAmount.value) || rollsAmount.value === 0

    // console.log(rollsAmount.value)
    // console.log(isRollsAmountFractional.value)
}


// attract: Пересчитываем количество в рулонах при изменении длины в м.п.
const getLengthAmount = () => {
    // console.log(rollsAmount.value)
    if (rollsAmount.value.toString() === '') rollsAmount.value = 0
    if (rollsAmount.value < 0) rollsAmount.value = 0
    lengthAmount.value = rollsAmount.value * props.roll.average_length
    isRollsAmountFractional.value = !Number.isInteger(rollsAmount.value) || rollsAmount.value === 0

}



</script>

<style scoped>

</style>
