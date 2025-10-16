<template>
    <div v-if="showModal" class="dark-container">

        <div :class="[width, height, borderColor, 'modal-container']">

            <div class="close-cross-container">
                <div class="m-1 p-1">
                    <AppLabelTS
                        :type="type"
                        align="center"
                        height="h-[30px]"
                        text="✗"
                        text-size="mini"
                        width="w-[30px]"
                        @click="select(false)"
                    />
                </div>
            </div>


            <div>
                <div class="flex">

                    <!-- __ Позиция (№ по порядку) новая -->
                    <AppLabelMultiLineTS
                        v-if="rollsRender.positionNew.show"
                        :align="rollsRender.positionNew.headerAlign"
                        :text="rollsRender.positionNew.header"
                        :text-size="rollsRender.positionNew.headerTextSize"
                        :type="getHeaderType()"
                        :width="rollsRender.positionNew.width"
                    />

                    <!-- __ Позиция (№ по порядку) текущая -->
                    <AppLabelMultiLineTS
                        v-if="rollsRender.positionOld.show"
                        :align="rollsRender.positionOld.headerAlign"
                        :text="rollsRender.positionOld.header"
                        :text-size="rollsRender.positionOld.headerTextSize"
                        :type="getHeaderType()"
                        :width="rollsRender.positionOld.width"
                    />

                    <!-- __ Название ПС -->
                    <AppLabelMultiLineTS
                        v-if="rollsRender.fabricName.show"
                        :align="rollsRender.fabricName.headerAlign"
                        :text="rollsRender.fabricName.header"
                        :text-size="rollsRender.fabricName.headerTextSize"
                        :type="getHeaderType()"
                        :width="rollsRender.fabricName.width"
                    />

                    <!-- __ Средняя длина ПС -->
                    <AppLabelMultiLineTS
                        v-if="rollsRender.fabricLength.show"
                        :align="rollsRender.fabricLength.headerAlign"
                        :text="rollsRender.fabricLength.header"
                        :text-size="rollsRender.fabricLength.headerTextSize"
                        :type="getHeaderType()"
                        :width="rollsRender.fabricLength.width"
                    />

                    <!-- __ Количество рулонов -->
                    <AppLabelMultiLineTS
                        v-if="rollsRender.rollsAmount.show"
                        :align="rollsRender.rollsAmount.headerAlign"
                        :text="rollsRender.rollsAmount.header"
                        :text-size="rollsRender.rollsAmount.headerTextSize"
                        :type="getHeaderType()"
                        :width="rollsRender.rollsAmount.width"
                    />

                    <!-- __ Средние трудозатраты на рулон -->
                    <AppLabelMultiLineTS
                        v-if="rollsRender.productivity.show"
                        :align="rollsRender.productivity.headerAlign"
                        :text="rollsRender.productivity.header"
                        :text-size="rollsRender.productivity.headerTextSize"
                        :type="getHeaderType()"
                        :width="rollsRender.productivity.width"
                    />

                    <!-- __ Комментарий -->
                    <AppLabelMultiLineTS
                        v-if="rollsRender.description?.show"
                        :align="rollsRender.description.headerAlign"
                        :text="rollsRender.description.header"
                        :text-size="rollsRender.description.headerTextSize"
                        :type="getHeaderType()"
                        :width="rollsRender.description.width"
                    />

                </div>

                <!-- hr----------------------------------------------------------------------------------------- -->

                <div v-for="roll in task.machines[machine.TITLE].rolls" :key="roll.id">
                    <div class="flex">

                        <!-- __ Позиция (№ по порядку) новая -->
                        <AppLabelTS
                            v-if="rollsRender.positionNew.show"
                            :align="rollsRender.positionNew.dataAlign"
                            :text="rollsRender.positionNew.data ? rollsRender.positionNew.data(roll) : ''"
                            :text-size="rollsRender.positionNew.dataTextSize"
                            :type="typeof rollsRender.positionNew.type === 'function' ? rollsRender.positionNew.type(roll) : rollsRender.positionNew.type"
                            :width="rollsRender.positionNew.width"
                        />

                        <!-- __ Позиция (№ по порядку) текущая -->
                        <AppLabelTS
                            v-if="rollsRender.positionOld.show"
                            :align="rollsRender.positionOld.dataAlign"
                            :text="rollsRender.positionOld.data ? rollsRender.positionOld.data(roll) : ''"
                            :text-size="rollsRender.positionOld.dataTextSize"
                            :type="typeof rollsRender.positionOld.type === 'function' ? rollsRender.positionOld.type(roll) : rollsRender.positionOld.type"
                            :width="rollsRender.positionOld.width"
                        />

                        <!-- __ Название ПС -->
                        <AppLabelTS
                            v-if="rollsRender.fabricName.show"
                            :align="rollsRender.fabricName.dataAlign"
                            :text="rollsRender.fabricName.data ? rollsRender.fabricName.data(roll) : ''"
                            :text-size="rollsRender.fabricName.dataTextSize"
                            :type="typeof rollsRender.fabricName.type === 'function' ? rollsRender.fabricName.type(roll) : rollsRender.fabricName.type"
                            :width="rollsRender.fabricName.width"
                            class="truncate"
                        />

                        <!-- __ Средняя длина ПС -->
                        <AppLabelTS
                            v-if="rollsRender.fabricLength.show"
                            :align="rollsRender.fabricLength.dataAlign"
                            :text="rollsRender.fabricLength.data ? rollsRender.fabricLength.data(roll) : ''"
                            :text-size="rollsRender.fabricLength.dataTextSize"
                            :type="typeof rollsRender.fabricLength.type === 'function' ? rollsRender.fabricLength.type(roll) : rollsRender.fabricLength.type"
                            :width="rollsRender.fabricLength.width"
                        />

                        <!-- __ Количество рулонов (всегда = 1) -->
                        <AppLabelTS
                            v-if="rollsRender.rollsAmount.show"
                            :align="rollsRender.rollsAmount.dataAlign"
                            :text="rollsRender.rollsAmount.data ? rollsRender.rollsAmount.data(roll) : ''"
                            :text-size="rollsRender.rollsAmount.dataTextSize"
                            :type="typeof rollsRender.rollsAmount.type === 'function' ? rollsRender.rollsAmount.type(roll) : rollsRender.rollsAmount.type"
                            :width="rollsRender.rollsAmount.width"
                        />

                        <!-- __ Средние трудозатраты на рулон -->
                        <AppLabelTS
                            v-if="rollsRender.productivity.show"
                            :align="rollsRender.productivity.dataAlign"
                            :text="rollsRender.productivity.data ? rollsRender.productivity.data(roll) : ''"
                            :text-size="rollsRender.productivity.dataTextSize"
                            :type="typeof rollsRender.productivity.type === 'function' ? rollsRender.productivity.type(roll) : rollsRender.productivity.type"
                            :width="rollsRender.productivity.width"
                        />

                        <!-- __ Комментарий -->
                        <AppLabelTS
                            v-if="rollsRender.description.show"
                            :align="rollsRender.description.dataAlign"
                            :text="rollsRender.description.data ? rollsRender.description.data(roll) : ''"
                            :text-size="rollsRender.description.dataTextSize"
                            :type="typeof rollsRender.description.type === 'function' ? rollsRender.description.type(roll) : rollsRender.description.type"
                            :width="rollsRender.description.width"
                            class="truncate"
                        />


                    </div>
                </div>

                <!-- __ Итого: -->
                <div class="mt-5">

                    <!-- __ Текущее время переналадки -->
                    <div class="flex justify-center">
                        <AppLabelTS
                            :align="TITLE_LABEL_ALIGN"
                            :text-size="TITLE_LABEL_TEXT_SIZE"
                            :type="'danger'"
                            :width="TITLE_LABEL_WIDTH"
                            text="Время переналадки до оптимизации:"
                        />
                        <AppLabelTS
                            :align="DATA_LABEL_ALIGN"
                            :text="'🕒' + formatTimeWithLeadingZeros(totalTuningProductivity, 'hour')"
                            :text-size="DATA_LABEL_TEXT_SIZE"
                            :type="DATA_LABEL_TYPE"
                            :width="DATA_LABEL_WIDTH"
                        />
                    </div>

                    <!-- __ Оптимизированное время переналадки -->
                    <div class="flex justify-center">
                        <AppLabelTS
                            :align="TITLE_LABEL_ALIGN"
                            :text-size="TITLE_LABEL_TEXT_SIZE"
                            :type="'warning'"
                            :width="TITLE_LABEL_WIDTH"
                            text="Время переналадки после оптимизации:"
                        />
                        <AppLabelTS
                            :align="DATA_LABEL_ALIGN"
                            :text="'🕒' + formatTimeWithLeadingZeros(totalTuningProductivityOptimized, 'hour')"
                            :text-size="DATA_LABEL_TEXT_SIZE"
                            :type="DATA_LABEL_TYPE"
                            :width="DATA_LABEL_WIDTH"
                        />
                    </div>

                    <!-- __ Дельта время переналадки -->
                    <div class="flex justify-center">
                        <AppLabelTS
                            :align="TITLE_LABEL_ALIGN"
                            :text-size="TITLE_LABEL_TEXT_SIZE"
                            :type="TITLE_LABEL_TYPE"
                            :width="TITLE_LABEL_WIDTH"
                            text="Оптимизация времени:"
                        />
                        <AppLabelTS
                            :align="DATA_LABEL_ALIGN"
                            :text="`🕒 ${formatTimeWithLeadingZeros(optimizedTime, 'hour')} | ${(100 * optimizedTime / FABRIC_WORKING_SHIFT_LENGTH).toFixed(FRACTION_DIGITS)}% от смены (${FABRIC_WORKING_SHIFT_LENGTH}ч.)`"
                            :text-size="DATA_LABEL_TEXT_SIZE"
                            :type="DATA_LABEL_TYPE"
                            :width="DATA_LABEL_WIDTH"
                        />
                    </div>

                </div>


            </div>

            <div class="w-full h-full flex justify-end">

                <div class="m-1 p-1 mr-0.5">
                    <AppLabelTS
                        :type="type"
                        align="center"
                        height="h-[50px]"
                        text="✓ Принять"
                        width="w-[150px]"
                        @click="select(true)"
                    />
                </div>

                <div class="m-1 p-1 ml-0.5">
                    <AppLabelTS
                        :type="'danger'"
                        align="center"
                        height="h-[50px]"
                        text="✗ Отмена"
                        width="w-[150px]"
                        @click="select(false)"
                    />
                </div>

            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'

import type { IColorTypes } from '@/app/constants/colorsClasses.js'
import type { IGlobalProductivity, IRenderData, IRoll, ITaskItem } from '@/types'
import { FABRIC_WORKING_SHIFT_LENGTH, type IConstFabricMachine } from '@/app/constants/fabrics.ts'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import { getColorClassByType } from '@/app/helpers/helpers.js'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'
import { getTotalTunningProductivityGlobal } from '@/app/helpers/manufacture/helpers_fabric'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    task: ITaskItem
    machine: IConstFabricMachine
    type?: IColorTypes
    width?: string
    height?: string
}

const props = withDefaults(defineProps<IProps>(), {
    type: 'primary',
    width: 'min-w-[1024px]',
    height: 'min-h-[800px]',
})

// __ Получаем данные из хранилища
const fabricsStore = useFabricsStore()

// __ Получаем тип данных
const getHeaderType = () => 'success' as IColorTypes
const getRollType = (roll: IRoll) => (roll.isTuning ? TUNING_TYPE : DATA_TYPE) as IColorTypes

const FRACTION_DIGITS = 2

const TITLE_LABEL_TYPE = 'success' as IColorTypes
const TITLE_LABEL_ALIGN = 'right'
const TITLE_LABEL_TEXT_SIZE = 'mini'
const TITLE_LABEL_WIDTH = 'w-[300px]'

const DATA_LABEL_TYPE = 'stone' as IColorTypes
const DATA_LABEL_ALIGN = 'center'
const DATA_LABEL_TEXT_SIZE = 'mini'
const DATA_LABEL_WIDTH = 'w-[300px]'

const DATA_TYPE = 'primary'
const TUNING_TYPE = 'stone'

const HEADER_ALIGN = 'center'
const HEADER_TEXT_SIZE = 'mini'
const DATA_ALIGN = 'center'
const DATA_TEXT_SIZE = 'mini'

let positionNew = 0

// __ Задаем глобальный объект для унификации отображения рулонов
const rollsRender: IRenderData = {
    positionNew: {
        header: ['№ п/п', 'нов.'],
        width: 'w-[60px]',
        type: (roll: IRoll) => getRollType(roll),
        show: true,
        headerAlign: HEADER_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataAlign: DATA_ALIGN,
        dataTextSize: DATA_TEXT_SIZE,
        data: (roll: IRoll) => roll.isTuning ? '⚙️' : (++positionNew).toString()
    },
    positionOld: {
        header: ['№ п/п', 'тек.'],
        width: 'w-[60px]',
        type: (roll: IRoll) => getRollType(roll),
        show: true,
        headerAlign: HEADER_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataAlign: DATA_ALIGN,
        dataTextSize: DATA_TEXT_SIZE,
        data: (roll: IRoll) => roll.isTuning ? '⚙️' : roll.roll_position.toString()
    },
    fabricName: {
        header: ['Полотно', 'стеганное'],
        width: 'w-[300px]',
        type: (roll: IRoll) => getRollType(roll),
        show: true,
        headerAlign: HEADER_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataAlign: 'left',
        dataTextSize: DATA_TEXT_SIZE,
        data: (roll: IRoll) => roll.fabric
    },
    fabricLength: {
        header: ['Длина', 'ПС'],
        width: 'w-[70px]',
        type: (roll: IRoll) => getRollType(roll),
        show: true,
        headerAlign: HEADER_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataAlign: DATA_ALIGN,
        dataTextSize: DATA_TEXT_SIZE,
        data: (roll: IRoll) => roll.isTuning ? '' : roll.average_fabric_length.toFixed(FRACTION_DIGITS)
    },
    rollsAmount: {
        header: ['Кол-во', 'рул.'],
        width: 'w-[50px]',
        type: (roll: IRoll) => getRollType(roll),
        show: true,
        headerAlign: HEADER_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataAlign: DATA_ALIGN,
        dataTextSize: DATA_TEXT_SIZE,
        data: (roll: IRoll) => roll.isTuning ? '' : roll.rolls_amount.toString()
    },
    productivity: {
        header: ['Трудозатраты', ''],
        width: 'w-[100px]',
        type: (roll: IRoll) => getRollType(roll),
        show: true,
        headerAlign: HEADER_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataAlign: DATA_ALIGN,
        dataTextSize: DATA_TEXT_SIZE,
        data: (roll: IRoll) => formatTimeWithLeadingZeros(roll.average_fabric_length / roll.productivity, 'hour')
    },
    description: {
        header: ['Комментарий', ''],
        width: 'w-[300px]',
        type: (roll: IRoll) => getRollType(roll),
        show: true,
        headerAlign: HEADER_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataAlign: 'left',
        dataTextSize: DATA_TEXT_SIZE,
        data: (roll: IRoll) => roll.isTuning ? '' : roll.descr ?? '',
    },
}


// __ Трудозатраты по переналадкам
const totalTuningProductivity = ref(getTotalTunningProductivityGlobal(fabricsStore.globalTaskProductivity as unknown as IGlobalProductivity, props.machine))
const totalTuningProductivityOptimized = computed(
    () =>
        props.task.machines[props.machine.TITLE].rolls
            .filter(roll => roll.isTuning)
            .reduce((acc, roll) => acc + roll.average_fabric_length / roll.productivity, 0)
)
const optimizedTime = ref(totalTuningProductivity.value - totalTuningProductivityOptimized.value)


const borderColor = computed(() => getColorClassByType(props.type, 'border'))

const showModal = ref(false)           // реактивность видимости модального окна

let resolvePromise: ((value: boolean) => void) | null
const show = () => {
    showModal.value = true
    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: boolean) => {
    positionNew = 0 // сбрасываем счетчик позиции рулона, потому что при повторном вызове модального окна он сохраняется
    if (resolvePromise) {
        resolvePromise(value)
        showModal.value = false
        resolvePromise = null
    }
}

defineExpose({
    show,
})

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
