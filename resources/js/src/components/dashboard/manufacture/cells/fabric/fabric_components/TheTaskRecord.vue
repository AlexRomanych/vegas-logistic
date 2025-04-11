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
                :text="!editMode ? workRoll.fabric : [workRoll.fabric, '']"
                :type="typeForErrorsAndConstraintsForLabel"
                height="h-[30px]"
                text-size="mini"
                width="w-[300px]"
            />
        </div>
        <div v-else>
            <AppSelect
                :multiple="false"
                :selectData="selectData"
                :type="typeForErrorsAndConstraintsForSelect"
                height="h-[64px]"
                label="Выберите ПС:"
                text-size="mini"
                width="w-[304px]"
                @change="selectFabric"
            />
        </div>

        <!-- attract: Средняя длина рулона -->
        <!--        :text="!editMode ? roll.average_length.toFixed(2) : [roll.average_length.toFixed(2), '']"-->
        <AppLabelMultiLine
            :text="!editMode ? averageLength.toFixed(2) : [averageLength.toFixed(2), '']"
            :type="averageLength ? 'dark' : 'danger'"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[100px]"
        />

        <!-- attract: Количество в рулонах -->
        <div v-if="!editMode">
            <AppLabelMultiLine
                :text="Number.isInteger(rollsAmount) ? rollsAmount.toFixed(0) : rollsAmount.toFixed(5)"
                :type="isRollsAmountFractional || !rollsAmount ? 'danger' : 'primary'"
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
                :type="isRollsAmountFractional || !rollsAmount ? 'danger' : 'primary'"
                :value=Math.round(rollsAmount*100000)/100000
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
                :type="lengthAmount ? 'primary' : 'danger'"
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
                :type="lengthAmount ? 'primary' : 'danger'"
                :value=Math.round(lengthAmount*100)/100
                align="center"
                height="h-[60px]"
                step="0.01"
                text-size="mini"
                width="w-[70px]"
                @blur="getRollsAmount"
                @change="getRollsAmount"
                @input="getRollsAmount"
            />
            <!-- warning: оставим только событие change  -->
        </div>


        <!-- attract: Трудозатраты -->
        <AppLabelMultiLine
            :text="!editMode ? formatTimeWithLeadingZeros(productivityAmount, 'hour') : [formatTimeWithLeadingZeros(productivityAmount, 'hour'), '']"
            :type="productivityAmount ? 'dark' : 'danger'"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[90px]"
        />

        <!-- attract: Комментарий -->
        <div v-if="!editMode">
            <AppLabel
                :text="description"
                class="truncate"
                height="h-[30px]"
                text-size="mini"
                type="primary"
                width="w-[300px]"
            />
        </div>

        <!-- v-model:input-textarea="workRoll.descr"       -->
        <div v-else>
            <AppInputTextArea
                id="comment"
                v-model="description"
                :rows=2
                :value="description"
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
                v-if="funcButtonsConstraints"
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
                v-if="funcButtonsConstraints"
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
                v-if="saveRollFlag"
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

import {reactive, ref, watch} from 'vue'
import {onBeforeRouteLeave, onBeforeRouteUpdate} from 'vue-router'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_MACHINES} from '/resources/js/src/app/constants/fabrics.js'

import {
    filterFabricsByMachineId,
    getAddFabricMode
} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import {formatTimeWithLeadingZeros} from '/resources/js/src/app/helpers/helpers_date.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppInputTextArea from '/resources/js/src/components/ui/inputs/AppInputTextArea.vue'
import AppInputNumber from '/resources/js/src/components/ui/inputs/AppInputNumber.vue'
import AppSelect from '/resources/js/src/components/ui/selects/AppSelect.vue'

const props = defineProps({
    roll: {
        type: Object,
        required: false,
        default: () => ({})
    },
    index: {                            // для расчета и обмена информацией по трудозатратам
        type: Number,
        required: true,
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

// attract: Объявляем дублирующую переменную для рулона, потому что state нельзя изменять в дочернем элементе
let workRoll = reactive({...props.roll})
let memRoll = {...props.roll}     // для возврата состояния по кнопке отмены

// attract: Получаем данные из хранилища по ПС
const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory
const rollsIndexes = fabricsStore.globalRollsIndexes        // для исключения их из selectData

// attract: Дорабатываем входные данные. Получаем fabricMode для ПС (Основная или альтернативная)
const fabricMode = ref(getAddFabricMode(fabrics, props.machine.ID, workRoll.fabric_id))
// console.log('fabricMode: ', fabricMode.value)
// console.log('roll: ', props.roll)

// attract: Определяем переменную для отображения выбора ПС (selectData)
let selectData  // определяем в watch с флагом immediate: true - запуск при монтировании

// attract: Маячок для отображения сервисных кнопок
const getFuncButtonsConstraints = () => !(fabricsStore.globalFabricsMode && !fabricMode.value) || !workRoll.fabric_id
const funcButtonsConstraints = ref(getFuncButtonsConstraints())

// attract: Определяем переменные для средней длины
const getAverageLength = () => {
    const tempFabric = fabrics.find(fabric => fabric.id === workRoll.fabric_id)
    return tempFabric.buffer.average_length
}
const averageLength = ref(getAverageLength())


// attract: Получаем тип раскраски ошибки и ограничения для рулона
const getTypeForErrorsAndConstraintsForLabel = () => {

    // Проверяем, если выбран рулон с нулевым id, то возвращаем тип ошибки
    if (workRoll.fabric_id === 0) {
        return 'danger'
    }

    // Проверяем, если режим выбора ПС не основной, то возвращаем тип предупреждения
    if (fabricsStore.globalFabricsMode && !fabricMode.value) return 'warning'

    return 'primary'
}


// attract: Определяем переменные для стилей
const typeForErrorsAndConstraintsForSelect = ref('primary')
const typeForErrorsAndConstraintsForLabel = ref(getTypeForErrorsAndConstraintsForLabel())

// todo: Сделать функционал, чтобы нельзя было выбирать на новом рулоне уже существующие ПС и нельзя было бы добавлять новый рулон, если есть незакрытые

// attract: Определяем объект selectData для ПС в зависимости от выбранного рулона
const getSelectData = () => {

    // attract: Фильтруем ПС в зависимости от выбранного режима ПС
    const filteredFabrics = filterFabricsByMachineId(fabrics, props.machine.ID, fabricsStore.globalFabricsMode)

    // attract: Делаем 2 разных selectData для рулонов с нулевым id (убираем существующие в СЗ ПС) и для остальных
    let data

    if (workRoll.fabric_id !== 0) {

        data = filteredFabrics.map(fabric => {
            return {
                id: fabric.id,
                name: fabric.display_name,
                selected: fabric.id === workRoll.fabric_id
            }
        })

    } else {

        const dataRaw = filteredFabrics.map(fabric => {
            if (!rollsIndexes.includes(fabric.id)) {
                return {
                    id: fabric.id,
                    name: fabric.display_name,
                    selected: fabric.id === workRoll.fabric_id
                }
            }
        })

        data = dataRaw.filter((item) => typeof item !== "undefined")                     // удаляем пустые объекты
    }

    // console.log('select_data: ', data)
    // console.log('id: : ', workRoll.fabric_id)

    return {name: 'fabrics', data}
}

// attract: Обрабатываем событие выбора ПС глобальное. Ставим immediate: true, чтобы компонент срабатывал при рендеринге
// selectData = reactive(getSelectData())
watch(() => fabricsStore.globalFabricsMode, () => {

    // Вычисляем данные для селекта ПС
    selectData = getSelectData()
    // Вычисляем тип для стилей
    typeForErrorsAndConstraintsForLabel.value = getTypeForErrorsAndConstraintsForLabel()
    // Пересчитываем ограничения для сервисных кнопок
    funcButtonsConstraints.value = getFuncButtonsConstraints()
    // funcButtonsConstraints.value = typeForErrorsAndConstraintsForLabel.value === 'primary'
}, {deep: true, immediate: true})

// attract: Обрабатываем событие выбора ПС в компоненте. Ставим immediate: true, чтобы компонент срабатывал при рендеринге
// watch(() => workRoll, (newRoll) => {
//     console.log('a im workRoll')

//
//     const tempFabric = fabrics.find(fabric => fabric.id === newRoll.fabric_id)                    // Получаем объект ПС
//     averageLength.value = tempFabric.buffer.average_length                              // Получаем среднюю длину ПС
//
//     workRoll.fabric = tempFabric.display_name                                           // Меняем название ПС
//     fabricMode.value = getAddFabricMode(fabrics, props.machine.ID, newRoll.fabric_id) // Меняем режим выбора ПС
//
//
//     // Вычисляем данные для селекта ПС
//     selectData = getSelectData()
//     // Вычисляем тип для стилей
//     typeForErrorsAndConstraintsForLabel.value = getTypeForErrorsAndConstraintsForLabel()
//     // Пересчитываем ограничения для сервисных кнопок
//     // funcButtonsConstraints.value = getFuncButtonsConstraints()
// }, {deep: true, immediate: true})

// attract: Определяем модели для передачи обмена данными
// Длина в рулонах
const rollsAmount = defineModel('rollsAmount', {
    // type: Number,
    required: false,
    default: 0
})
rollsAmount.value = workRoll.rolls_amount

// Длина в м.п.
const lengthAmount = defineModel('lengthAmount', {
    // type: Number,
    required: false,
    default: 0
})
lengthAmount.value = rollsAmount.value * averageLength.value

// attract: Описание
const description = ref(workRoll.descr)
// const description = defineModel('description', {})

// attract: Определяем переменные для трудозатрат
const getProductivity = () => {
    const tempFabric = fabrics.find(fabric => fabric.id === workRoll.fabric_id)
    return tempFabric.buffer.productivity
}
const productivity = ref(getProductivity())

// attract: Время стегания в часах
const getProductivityAmount = () => {
    const tempProductivityAmount = productivity.value ? lengthAmount.value / productivity.value : 0
    fabricsStore.globalTaskProductivity[props.machine.TITLE][props.index] = tempProductivityAmount
    return tempProductivityAmount
}
const productivityAmount = ref(getProductivityAmount())


// attract: Флаг дробного количества в рулонах и нулевого количества
const getIsRollsAmountFractional = () => !Number.isInteger(rollsAmount.value)
const isRollsAmountFractional = ref(getIsRollsAmountFractional())


// attract: Выделяем в отдельную функцию все общие реактивные методы
const reactiveActions = () => {

    // warning: Порядок важен
    selectData = getSelectData()                                                        // Вычисляем данные для селекта ПС

    const tempFabric = fabrics.find(fabric => fabric.id === workRoll.fabric_id)         // Получаем объект ПС

    workRoll.fabric = tempFabric.display_name                                           // Меняем название ПС
    workRoll.average_textile_length = tempFabric.buffer.average_length                  // Меняем среднюю длину ПС
    workRoll.descr = description.value

    averageLength.value = workRoll.average_textile_length                               // Получаем среднюю длину ПС
    productivity.value = tempFabric.buffer.productivity                                 // Получаем производительность
    lengthAmount.value = workRoll.rolls_amount * workRoll.average_textile_length
    rollsAmount.value = workRoll.rolls_amount
    productivityAmount.value = getProductivityAmount()                                  // Получаем трудозатраты
    fabricMode.value = getAddFabricMode(fabrics, props.machine.ID, workRoll.fabric_id)  // Меняем режим выбора ПС

    typeForErrorsAndConstraintsForLabel.value = getTypeForErrorsAndConstraintsForLabel()// Меняем тип для стилей


    isRollsAmountFractional.value = getIsRollsAmountFractional()
    // averageLength.value = getAverageLength()                                            // Получаем среднюю длину ПС
    // productivity.value = getProductivity()                                              // Получаем производительность
    // productivity.value = getProductivity()                                              // Получаем производительность
}

// attract: Обрабатываем событие выбора ПС
const selectFabric = (item) => {

    workRoll.fabric_id = item.id                                                  // Меняем id ПС
    reactiveActions()

    // const tempFabric = fabrics.find(fabric => fabric.id === item.id)                    // Получаем объект ПС
    // averageLength.value = tempFabric.buffer.average_length                              // Получаем среднюю длину ПС
    //
    //
    // workRoll.fabric = tempFabric.display_name                                           // Меняем название ПС
    // fabricMode.value = getAddFabricMode(fabrics, props.machine.ID, workRoll.fabric_id) // Меняем режим выбора ПС
    // averageLength.value = getAverageLength()                              // Получаем среднюю длину ПС
    // productivity.value = getProductivity()                              // Получаем производительность
    // typeForErrorsAndConstraintsForLabel.value = getTypeForErrorsAndConstraintsForLabel()// Меняем тип для стилей
    //
    // selectData = getSelectData()                                                        // Вычисляем данные для селекта ПС

    if (item.id === 0) {
        typeForErrorsAndConstraintsForSelect.value = 'danger'
    } else {
        typeForErrorsAndConstraintsForSelect.value = 'primary'
    }

    // console.log(item)
    // console.log(averageLength.value)
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
const cancelEditMode = () => {
    resetEditMode()
    workRoll = {...memRoll}     // Возвращаем состояние по кнопке отмены
    // typeForErrorsAndConstraintsForLabel.value = getTypeForErrorsAndConstraintsForLabel()// Меняем тип для стилей

    reactiveActions()
}

// attract: Сохраняем рулон
const saveTaskRecord = () => {

    resetEditMode()                                     // Отключаем глобальное редактирование

    console.log(rollsAmount.value)
    console.log(lengthAmount.value)
    console.log(workRoll.fabric_id)
    console.log(workRoll.descr)

    const saveRollData = {
        average_textile_length: averageLength.value,
        textile_length: lengthAmount.value,
        fabric_id: workRoll.fabric_id,
        fabric_name: workRoll.fabric,
        rolls_amount: rollsAmount.value,
        descr: description.value,
        fabric_mode: fabricMode.value,
    }

    console.log(saveRollData)

    emits('saveTaskRecord', {index: props.index, roll: saveRollData})
}


const amountActions = () => {
    if (rollsAmount.value.toString() === '' || rollsAmount.value < 0) rollsAmount.value = 0
    if (lengthAmount.value.toString() === '' || lengthAmount.value < 0) lengthAmount.value = 0
    isRollsAmountFractional.value = getIsRollsAmountFractional()
    productivityAmount.value = getProductivityAmount()
    workRoll.rolls_amount = rollsAmount.value

    console.log('ln: ', lengthAmount.value)
    console.log('rolls: ', rollsAmount.value)
    console.log('isRollsAmountFractional: ', isRollsAmountFractional.value)
}

// attract: Пересчитываем количество в рулонах при изменении длины в м.п.
const getRollsAmount = () => {
    // warning: Порядок важен
    if (averageLength.value) {
        rollsAmount.value = lengthAmount.value / averageLength.value
    } else {
        rollsAmount.value = 0
    }
    amountActions()
}


// attract: Пересчитываем количество в рулонах при изменении длины в м.п.
const getLengthAmount = () => {
    lengthAmount.value = rollsAmount.value * averageLength.value
    amountActions()
}


// attract: Пересчитываем ограничения для кнопки сохранения
const getSaveRollFlag = () => {
    // console.log('workRoll.fabric_id: ', workRoll.fabric_id)
    // console.log('rollsAmount.value: ', rollsAmount.value)
    // console.log('averageLength.value: ', averageLength.value)
    // console.log('isRollsAmountFractional.value: ', isRollsAmountFractional.value)
    // console.log('productivity.value: ', productivity.value)


    return workRoll.fabric_id
        && rollsAmount.value
        && !isRollsAmountFractional.value
        && averageLength.value
        && productivity.value
}

// attract: Определяем переменную, указывающую, что рулон готов к сохранению
const saveRollFlag = ref(getSaveRollFlag())

// attract: Обрабатываем редактирование записи с ПС, чтобы дать доступ к сохранению
watch([
    () => workRoll,
    () => rollsAmount,
    () => averageLength,
    () => productivity,
    () => isRollsAmountFractional], () => {
    saveRollFlag.value = getSaveRollFlag()
}, {deep: true})

// attract: сбрасываем глобальное редактирование
onBeforeRouteLeave(() => fabricsStore.globalEditMode = false)
onBeforeRouteUpdate(() => fabricsStore.globalEditMode = false)

</script>

<style scoped>

</style>
