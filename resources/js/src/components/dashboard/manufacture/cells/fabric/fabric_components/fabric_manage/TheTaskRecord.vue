<template>

    <!-- attract: Режим отображения + Режим редактирования -->
    <div v-if="!isLoading" class="flex">

        <!-- attract: Номер рулона -->
        <!--        <AppLabelMultiLine-->
        <!--            :text="!editMode ? roll.num.toString() : [roll.num.toString(), '']"-->
        <!--            align="center"-->
        <!--            height="h-[30px]"-->
        <!--            text-size="mini"-->
        <!--            width="w-[80px]"-->
        <!--        />-->

        <!-- __ Позиция рулона -->
        <AppLabelMultiLine
            :text="!editMode ? roll.roll_position.toString() : [roll.roll_position.toString(), '']"
            align="center"
            height="h-[30px]"
            text-size="mini"
            type="primary"
            width="w-[50px]"
        />

        <!--__ ПС -->
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

        <!--__ Буфер ПС -->
        <AppLabelMultiLine
            :text="!editMode ? buffer.toFixed(PRECISION) : [buffer.toFixed(PRECISION), '']"
            :type="buffer ? 'dark' : 'danger'"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[80px]"
        />

        <!--__ Средняя длина рулона -->
        <AppLabelMultiLine
            :text="!editMode ? averageLength.toFixed(PRECISION) : [averageLength.toFixed(PRECISION), '']"
            :type="averageLength ? 'dark' : 'danger'"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[80px]"
        />

        <!--__ Средняя длина ПС -->
        <AppLabelMultiLine
            :text="!editMode ? averageLengthFabric.toFixed(PRECISION) : [averageLengthFabric.toFixed(PRECISION), '']"
            :type="averageLengthFabric ? 'dark' : 'danger'"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[80px]"
        />

        <!--__ Количество в рулонах -->
        <div v-if="!editMode">
            <AppLabelMultiLine
                :text="Number.isInteger(rollsAmount) ? rollsAmount.toFixed(0) : rollsAmount.toFixed(5)"
                :type="isRollsAmountFractional || !rollsAmount ? 'danger' : 'primary'"
                align="center"
                height="h-[30px]"
                text-size="mini"
                width="w-[80px]"
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
                width="w-[80px]"
                @blur="getLengthAmount"
                @change="getLengthAmount"
                @input="getLengthAmount"
            />
        </div>

        <!--__ Количество ткани в м.п. -->
        <div v-if="!editMode">
            <AppLabelMultiLine
                :text="lengthAmount.toFixed(PRECISION)"
                :type="lengthAmount ? 'primary' : 'danger'"
                align="center"
                height="h-[30px]"
                text-size="mini"
                width="w-[80px]"
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
                width="w-[80px]"
                @blur="getRollsAmount"
                @change="getRollsAmount"
                @input="getRollsAmount"
            />
            <!-- warning: оставим только событие change  -->
        </div>

        <!--__ Количество ПС в м.п. -->
        <AppLabelMultiLine
            :text="!editMode ? (averageLengthFabric*rollsAmount).toFixed(PRECISION) : [(averageLengthFabric*rollsAmount).toFixed(PRECISION), '']"
            :type="averageLengthFabric ? 'dark' : 'danger'"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[80px]"
        />

        <!--__ Трудозатраты -->
        <AppLabelMultiLine
            :text="!editMode ? formatTimeWithLeadingZeros(productivityAmount, 'hour') : [formatTimeWithLeadingZeros(productivityAmount, 'hour'), '']"
            :type="productivityAmount ? 'dark' : 'danger'"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[90px]"
        />

        <!--__ Комментарий -->
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


        <!--__ Показываем кнопки Редактировать и Удалить только для тех СЗ, где есть возможность менять данные -->
        <div v-if="getFunctionalByFabricTaskStatus(taskStatus)">

            <!--__ Управляющие кнопки -->
            <div v-if="!editMode" class="flex">


                <!--__ Удалить -->
                <AppLabel
                    v-if="funcButtonsConstraints && workRoll?.rolls_exec[0]?.status !== FABRIC_ROLL_STATUS.ROLLING.CODE"
                    align="center"
                    class="cursor-pointer font-bold"
                    height="h-[30px]"
                    text="Х"
                    text-size="mini"
                    type="danger"
                    width="w-[50px]"
                    @click="deleteTaskRecord"
                />

                <!--__ Редактировать -->
                <AppLabel
                    v-if="funcButtonsConstraints && workRoll.editable"
                    align="center"
                    class="cursor-pointer font-bold"
                    height="h-[30px]"
                    text="Ред."
                    text-size="mini"
                    type="warning"
                    width="w-[50px]"
                    @click="setEditMode"
                />

            </div>

            <div v-else class="flex">

                <!--__ Отменить -->
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

                <!--__ Сохранить -->
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

        <!--__ Комментарий -->
        <div v-if="workRoll.note">
            <AppLabel
                :text="workRoll.note"
                :title="workRoll.note"
                :type="typeForErrorsAndConstraintsForLabel"
                class="truncate"
                height="h-[30px]"
                text-size="mini"
                width="w-[300px]"
            />
        </div>

        <!--        &lt;!&ndash;__ Комментарий &ndash;&gt;-->
        <!--        <div v-if="workRoll?.rolls_exec[0]?.false_reason">-->
        <!--            <AppLabel-->
        <!--                :text="workRoll?.rolls_exec[0]?.false_reason"-->
        <!--                class="truncate"-->
        <!--                height="h-[30px]"-->
        <!--                text-size="mini"-->
        <!--                :type="typeForErrorsAndConstraintsForLabel"-->
        <!--                width="w-[300px]"-->
        <!--                :title="workRoll?.rolls_exec[0]?.false_reason"-->
        <!--            />-->
        <!--        </div>-->

    </div>

    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />

</template>

<script lang="ts" setup>
import { computed, onMounted, ref, watch } from 'vue'
import { onBeforeRouteLeave, onBeforeRouteUpdate } from 'vue-router'

import type { IFabric, IRoll, ISelectData, ISelectDataItem, MachineUnionType, TaskStatusUnionType } from '@/types'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    FABRIC_MACHINES,
    FABRIC_ROLL_STATUS,
    NEW_ROLL,
    // FABRIC_TASK_STATUS,
    // FABRIC_ROLL_STATUS_LIST
} from '@/app/constants/fabrics.js'

import {
    filterFabricsByMachineId,
    getAddFabricMode,
    getFunctionalByFabricTaskStatus,
} from '@/app/helpers/manufacture/helpers_fabric.js'

import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date.js'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppInputTextArea from '@/components/ui/inputs/AppInputTextArea.vue'
import AppInputNumber from '@/components/ui/inputs/AppInputNumber.vue'
import AppSelect from '@/components/ui/selects/AppSelect.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import { storeToRefs } from 'pinia'


// line -----------------------------------------------------------------------------------------------------------
// line ------------- Объявление типов ----------------------------------------------------------------------------
// line -----------------------------------------------------------------------------------------------------------

interface IProps {
    index: number
    taskStatus: TaskStatusUnionType
    roll?: IRoll
    machine?: MachineUnionType,
}

// line -----------------------------------------------------------------------------------------------------------


const props = withDefaults(defineProps<IProps>(), {
    roll: () => NEW_ROLL,
    machine: () => FABRIC_MACHINES.AMERICAN
})


// const props = defineProps({
//     roll: {
//         type: Object,
//         required: false,
//         default: () => ({})
//     },
//     index: {                            // для расчета и обмена информацией по трудозатратам
//         type: Number,
//         required: true,
//     },
//     machine: {
//         type: Object,
//         required: false,
//         default: () => FABRIC_MACHINES.AMERICAN,
//         validator: (machine) => [
//             FABRIC_MACHINES.AMERICAN.ID,
//             FABRIC_MACHINES.GERMAN.ID,
//             FABRIC_MACHINES.CHINA.ID,
//             FABRIC_MACHINES.KOREAN.ID,
//         ].includes(machine.ID)
//     },
//     taskStatus: {
//         type: Number,
//         required: true,
//         validator: (taskStatus) => [
//             FABRIC_TASK_STATUS.UNKNOWN.CODE,
//             FABRIC_TASK_STATUS.CREATED.CODE,
//             FABRIC_TASK_STATUS.PENDING.CODE,
//             FABRIC_TASK_STATUS.RUNNING.CODE,
//             FABRIC_TASK_STATUS.DONE.CODE,
//         ].includes(taskStatus)
//     }
// })


// console.log('props.roll !!!: ', props.roll)

const emits = defineEmits<{
    (e: 'saveTaskRecord', payload: { index: number, roll: object }): void
    (e: 'deleteTaskRecord', workRoll: IRoll): void
}>()
// const emits = defineEmits(['saveTaskRecord', 'deleteTaskRecord'])

const fabricsStore = useFabricsStore()
const {globalFabricsMode} = storeToRefs(fabricsStore)

const PRECISION = 2 // точность для округления

const isLoading = ref(false)

// __ Объявляем дублирующую переменную для рулона, потому что state нельзя изменять в дочернем элементе
let workRoll = ref<IRoll>({...props.roll})
let memRoll = {...props.roll}     // для возврата состояния по кнопке отмены

// __ Подготавливаем переменные
let selectData: ISelectData                                        // __ Определяем переменную для отображения выбора ПС (selectData), определяем в watch с флагом immediate: true - запуск при монтировании
let fabrics: IFabric[] = []                                        // __ Получаем данные из хранилища по ПС
let rollsIndexes: any[] = []                                       // __ Получаем список индексов для исключения их из selectData
const averageLength = ref(0)                                // __ Определяем переменные для средней длины ткани
const averageLengthFabric = ref(0)                          // __ Определяем переменные для средней длины ПС
const buffer = ref(0)                                       // __ Определяем переменные для буфера ПС
const productivity = ref(0)                                 // __ Определяем переменные для трудозатрат
const fabricMode = ref(false)                               // __ Дорабатываем входные данные. Получаем fabricMode для ПС (Основная или альтернативная)
const description = ref(workRoll.value.descr)                      // __ Описание
const typeForErrorsAndConstraintsForSelect = ref('primary') // __ Определяем переменные для стилей
const productivityAmount = ref(0)                           // __ Время стегания в часах
const isRollsAmountFractional = ref(false)                  // __ Флаг дробного количества в рулонах и нулевого количества

// __ Получаем тип раскраски ошибки и ограничения для рулона
const typeForErrorsAndConstraintsForLabel = computed(() => {
    if (workRoll.value?.rolls_exec[0]?.status === FABRIC_ROLL_STATUS.FALSE.CODE) return FABRIC_ROLL_STATUS.FALSE.TYPE
    if (workRoll.value?.rolls_exec[0]?.status === FABRIC_ROLL_STATUS.ROLLING.CODE) return FABRIC_ROLL_STATUS.ROLLING.TYPE

    if (workRoll.value.fabric_id === 0) return 'danger' // Проверяем, если выбран рулон с нулевым id, то возвращаем тип ошибки

    if (globalFabricsMode && !fabricMode.value) return 'warning'   // Проверяем, если режим выбора ПС не основной, то возвращаем тип предупреждения

    return 'primary'
})

// __ Маячок для отображения сервисных кнопок
const funcButtonsConstraints = computed(() => !(globalFabricsMode && !fabricMode.value) || !workRoll.value.fabric_id)
// const getFuncButtonsConstraints = () => !(fabricsStore.globalFabricsMode && !fabricMode.value) || !workRoll.value.fabric_id
// const funcButtonsConstraints = ref(getFuncButtonsConstraints())


// __ Определяем модели для передачи обмена данными
// __ Длина ткани в рулонах
const rollsAmount = defineModel('rollsAmount', {
    // type: Number,
    required: false,
    default: 0
})

// __ Длина ткани в м.п.
const lengthAmount = defineModel('lengthAmount', {
    // type: Number,
    required: false,
    default: 0
})


// __ Получаем данные из хранилища по ПС
const getFabrics = () => fabrics = fabricsStore.fabricsMemory

// __ Получаем список индексов для исключения их из selectData
const getRollsIndexes = () => rollsIndexes = fabricsStore.globalRollsIndexes

// __ Дорабатываем входные данные. Получаем fabricMode для ПС (Основная или альтернативная)
const getFabricMode = () => fabricMode.value = getAddFabricMode(fabrics, props.machine.ID, workRoll.value.fabric_id) as boolean

// __ Определяем переменные для средней длины ткани
const getAverageLength = () => {
    const tempFabric = fabrics.find(fabric => fabric.id === workRoll.value.fabric_id)
    averageLength.value = tempFabric ? tempFabric.buffer.average_length : 0
    // return tempFabric ? tempFabric.buffer.average_length : 0
}

// __ Определяем переменные для средней длины ПС
const getAverageLengthFabric = () => {
    const tempFabric = fabrics.find(fabric => fabric.id === workRoll.value.fabric_id)
    averageLengthFabric.value = tempFabric ? (tempFabric.buffer.rate ? tempFabric.buffer.average_length / tempFabric.buffer.rate : 0) : 0
    // return tempFabric.buffer.average_length / tempFabric.buffer.rate
}

// __ Определяем переменные для буфера ПС
const getBuffer = () => {
    const tempFabric = fabrics.find(fabric => fabric.id === workRoll.value.fabric_id)
    buffer.value = tempFabric ? tempFabric.buffer.amount : 0
    // return tempFabric.buffer.average_length / tempFabric.buffer.rate
}

// __ Определяем переменные для трудозатрат
const getProductivity = () => {
    const tempFabric = fabrics.find(fabric => fabric.id === workRoll.value.fabric_id)
    productivity.value = tempFabric ? tempFabric.buffer.productivity : 0
    // return tempFabric.buffer.productivity
}

// __ Время стегания в часах
const getProductivityAmount = () => {
    // console.log('productivity.value: ', productivity.value)
    // console.log('lengthAmount.value: ', lengthAmount.value)

    const tempProductivityAmount = (productivity.value ? lengthAmount.value / productivity.value : 0) as number
    (fabricsStore.globalTaskProductivity[props.machine.TITLE] as number[])[props.index] = tempProductivityAmount
    productivityAmount.value = tempProductivityAmount
    // return tempProductivityAmount
}

// __ Флаг дробного количества в рулонах и нулевого количества
const getIsRollsAmountFractional = () => isRollsAmountFractional.value = !Number.isInteger(rollsAmount.value)

// __ Текст модального окна + Получаем ссылку на модальное окно
const modalText = ref<string[]>([])
const modalType = ref('danger')
const modalMode = ref('confirm')
const appModalAsync = ref<any>(null)

// todo: Сделать функционал, чтобы нельзя было выбирать на новом рулоне уже существующие ПС и нельзя было бы добавлять новый рулон, если есть незакрытые

// __ Определяем объект selectData для ПС в зависимости от выбранного рулона
const getSelectData = () => {

    // __ Фильтруем ПС в зависимости от выбранного режима ПС
    const filteredFabrics = filterFabricsByMachineId(fabrics, props.machine.ID, fabricsStore.globalFabricsMode)

    // console.log('filteredFabrics: ', filteredFabrics)


    // __ Делаем разные selectData для рулонов (убираем существующие в СЗ ПС) и для остальных

    // console.log('rollsIndexes: ', rollsIndexes)
    // console.log('workRoll.fabric_id: ', workRoll.fabric_id)

    let data = filteredFabrics.map(fabric => {

        if (!rollsIndexes.includes(fabric.id) || fabric.id === workRoll.value.fabric_id) {
            // if (!rollsIndexes.includes(fabric.id) && rollsIndexes.includes(workRoll.fabric_id)) {

            if ((workRoll.value.fabric_id !== 0 && fabric.id !== 0) || (workRoll.value.fabric_id === 0)) {

                return {
                    id: fabric.id,
                    name: fabric.display_name,
                    selected: fabric.id === workRoll.value.fabric_id
                }
            }
        }
    })

    data = data.filter((item) => typeof item !== 'undefined')                     // удаляем пустые объекты

    // console.log('data__: ', data)


    // if (workRoll.fabric_id !== 0) {
    //
    //     data = filteredFabrics.map(fabric => {
    //         if (fabric.id !== 0) {
    //             return {
    //                 id: fabric.id,
    //                 name: fabric.display_name,
    //                 selected: fabric.id === workRoll.fabric_id
    //             }
    //         }
    //     })
    //
    //     data = data.filter((item) => typeof item !== "undefined")
    //
    //     console.log('data_1: ', data)
    // } else {
    //
    //     data = filteredFabrics.map(fabric => {
    //         if (!rollsIndexes.includes(fabric.id) || fabric.id === 0) {
    //             return {
    //                 id: fabric.id,
    //                 name: fabric.display_name,
    //                 selected: fabric.id === workRoll.fabric_id
    //             }
    //         }
    //     })
    //
    //     data = data.filter((item) => typeof item !== "undefined")                     // удаляем пустые объекты
    //
    //     console.log('data_2: ', data)
    // }
    selectData = {name: 'fabrics', data: data as ISelectDataItem[]}
    // return {name: 'fabrics', data} as ISelectData
}


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


// attract: Выделяем в отдельную функцию все общие реактивные методы
const reactiveActions = () => {

    // warning: Порядок важен
    getSelectData()                                                            // Вычисляем данные для селекта ПС
    // selectData = getSelectData()                                                            // Вычисляем данные для селекта ПС

    const tempFabric = fabrics.find(fabric => fabric.id === workRoll.value.fabric_id)       // Получаем объект ПС

    if (tempFabric) {
        workRoll.value.fabric = tempFabric.display_name                                               // Меняем название ПС
        workRoll.value.buffer = tempFabric.buffer.amount                                              // Меняем Буфер
        workRoll.value.average_textile_length = tempFabric.buffer.average_length                      // Меняем среднюю длину ткани
        workRoll.value.average_fabric_length
            = tempFabric.buffer.average_length / tempFabric.buffer.rate                           // Меняем среднюю длину ПС
        workRoll.value.productivity = tempFabric.buffer.productivity                                  // Меняем производительность
        workRoll.value.rate = tempFabric.buffer.rate                                                  // Меняем коэффициент перевода ткани в ПС
        workRoll.value.descr = description.value

        averageLength.value = workRoll.value.average_textile_length                                   // Получаем среднюю длину ткани
        averageLengthFabric.value = workRoll.value.average_fabric_length                              // Получаем среднюю длину ПС
        buffer.value = tempFabric.buffer.amount                                                 // Меняем Буфер

        productivity.value = workRoll.value.productivity                                              // Получаем производительность
        lengthAmount.value = workRoll.value.rolls_amount * workRoll.value.average_textile_length
        rollsAmount.value = workRoll.value.rolls_amount
        getProductivityAmount()                                      // Получаем трудозатраты
        // productivityAmount.value = getProductivityAmount()                                      // Получаем трудозатраты
        fabricMode.value = getAddFabricMode(fabrics, props.machine.ID, workRoll.value.fabric_id) as boolean     // Меняем режим выбора ПС

        // typeForErrorsAndConstraintsForLabel.value = getTypeForErrorsAndConstraintsForLabel()    // Меняем тип для стилей


        isRollsAmountFractional.value = getIsRollsAmountFractional()
        // averageLength.value = getAverageLength()                                            // Получаем среднюю длину ПС
        // productivity.value = getProductivity()                                              // Получаем производительность
        // productivity.value = getProductivity()                                              // Получаем производительность
    }
}

// attract: Обрабатываем событие выбора ПС
const selectFabric = (item: ISelectDataItem) => {

    workRoll.value.fabric_id = item.id                                                  // Меняем id ПС

    console.log('workRoll', workRoll)

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
    workRoll.value = {...memRoll}     // Возвращаем состояние по кнопке отмены
    // typeForErrorsAndConstraintsForLabel.value = getTypeForErrorsAndConstraintsForLabel()// Меняем тип для стилей

    reactiveActions()
}

// __ Сохраняем рулон
const saveTaskRecord = () => {

    resetEditMode()                                     // Отключаем глобальное редактирование

    // console.log(rollsAmount.value)
    // console.log(lengthAmount.value)
    // console.log(workRoll.fabric_id)
    // console.log(workRoll.descr)

    const saveRollData: IRoll = {
        id: workRoll.value.id,
        average_textile_length: averageLength.value,
        productivity: productivity.value,
        textile_length: lengthAmount.value,
        fabric_id: workRoll.value.fabric_id,
        fabric_name: workRoll.value.fabric,
        rolls_amount: rollsAmount.value,
        descr: description.value,
        fabric_mode: fabricMode.value,
        rate: workRoll.value.rate,
        rolls_exec: [],                         // Нужно для правильной работы компонента
        editable: true,                         // Нужно для правильной работы компонента
        roll_position: props.roll.roll_position,// Нужно для правильной работы компонента

        average_fabric_length: workRoll.value.average_fabric_length,
        buffer: workRoll.value.buffer,
        length_amount: workRoll.value.length_amount,
        fabric: workRoll.value.fabric,
        fabric_rate: workRoll.value.fabric_rate,
        correct: workRoll.value.correct,
        note: workRoll.value.note,
    }

    // console.log('saveRollData: ', saveRollData)

    emits('saveTaskRecord', {index: props.index, roll: saveRollData})
}

// __ Удаляем рулон
const deleteTaskRecord = async () => {
    resetEditMode()                                     // Отключаем глобальное редактирование

    modalText.value = ['Запись будет удалена.', 'Продолжить?']
    const result = appModalAsync.value ? await appModalAsync.value.show() : false             // показываем модалку и ждем ответ

    if (!result) return

    emits('deleteTaskRecord', workRoll.value)
    // console.log(workRoll)
}

// attract: Общие действия при пересчете рулонов в штуках в метры и наоборот
const amountActions = () => {
    if (rollsAmount.value.toString() === '' || rollsAmount.value < 0) rollsAmount.value = 0
    if (lengthAmount.value.toString() === '' || lengthAmount.value < 0) lengthAmount.value = 0
    isRollsAmountFractional.value = getIsRollsAmountFractional()
    getProductivityAmount()
    // productivityAmount.value = getProductivityAmount()
    workRoll.value.rolls_amount = rollsAmount.value

    // console.log('ln: ', lengthAmount.value)
    // console.log('rolls: ', rollsAmount.value)
    // console.log('isRollsAmountFractional: ', isRollsAmountFractional.value)
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

    return workRoll.value.fabric_id
        && rollsAmount.value
        && !isRollsAmountFractional.value
        && averageLength.value
        && productivity.value
        && workRoll.value.rate
}

// __ Определяем переменную, указывающую, что рулон готов к сохранению
const saveRollFlag = ref(getSaveRollFlag())

// __ Обрабатываем редактирование записи с ПС, чтобы дать доступ к сохранению
watch([
    () => workRoll.value,
    () => rollsAmount,
    () => averageLength,
    () => productivity,
    () => isRollsAmountFractional], () => {
    saveRollFlag.value = getSaveRollFlag()
}, {deep: true})


// __ Обрабатываем событие выбора ПС глобальное. Ставим immediate: true, чтобы компонент срабатывал при рендеринге
// selectData = reactive(getSelectData())
watch(() => globalFabricsMode, () => {
    reactiveActions()
    getSelectData()     // Вычисляем данные для селекта ПС
    // Вычисляем тип для стилей
    // typeForErrorsAndConstraintsForLabel.value = getTypeForErrorsAndConstraintsForLabel()
    // Пересчитываем ограничения для сервисных кнопок
    // funcButtonsConstraints.value = getFuncButtonsConstraints()
    // funcButtonsConstraints.value = typeForErrorsAndConstraintsForLabel.value === 'primary'
}, {deep: true, immediate: true})


onMounted(() => {
    isLoading.value = true

    getFabrics()                    // __ Получаем ПС
    getRollsIndexes()               // __ Получаем список индексов для исключения их из selectData
    getAverageLength()              // __ Получаем среднюю длину ткани
    getAverageLengthFabric()        // __ Получаем среднюю длину ПС
    getBuffer()                     // __ Получаем буфер ПС
    getFabricMode()                 // __ Получаем режим выбора ПС
    getProductivity()               // __ Определяем переменные для трудозатрат
    getSelectData()                 // __ Вычисляем данные для селекта ПС
    // selectData = getSelectData()

    // __ Задаем начальные значения для моделей
    rollsAmount.value = workRoll.value.rolls_amount
    lengthAmount.value = workRoll.value.rolls_amount * averageLength.value

    getIsRollsAmountFractional()    // __ Важен порядок
    getProductivityAmount()         // __ Важен порядок

    // console.log('fabricsStore.globalFabricsMode: ', fabricsStore.globalFabricsMode)
    // console.log('fabrics: ', fabrics)
    isLoading.value = false
})


// attract: сбрасываем глобальное редактирование
// Warning: Этот код не работает (Блокирует переход на другую страницу)
// onBeforeRouteLeave(() => fabricsStore.globalEditMode = false)
// onBeforeRouteUpdate(() => fabricsStore.globalEditMode = false)

// Warning: А этот код работает (Не Блокирует переход на другую страницу) Не знаю причин
onBeforeRouteLeave(() => {
    // console.log('onBeforeRouteLeave')
    fabricsStore.globalEditMode = false
    fabricsStore.globalOrderManageChangeFlag = false
})
onBeforeRouteUpdate(() => {
    // console.log('onBeforeRouteUpdate')
    fabricsStore.globalEditMode = false
    fabricsStore.globalOrderManageChangeFlag = false
})

</script>

<style scoped>

</style>
