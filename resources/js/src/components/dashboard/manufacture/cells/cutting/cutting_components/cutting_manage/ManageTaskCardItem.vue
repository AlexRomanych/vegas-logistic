<template>

    <!-- __ Отрисовываем только те строки, у которых есть чехол -->
    <div v-if="modelCover" class="flex">
        <!--<div class="flex">-->

        <!-- __ № п/п -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.position.toString()"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.position.width"
            class="field"
        />

        <!-- __ Размер модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="getSize"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.size.width"
            class="field"
        />

        <!-- __ Название модели -->
        <AppLabelTS
            :rounded="DEFAULT_ROUNDED"
            :text="coverName"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.model.width"
            class="field"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.amount.toString()"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.amount.width"
            class="field"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="getTime"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTime === '00с' ? 'danger' : getType"
            :width="renderData.time.width"
        />

        <!-- __ УШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(CUTTING_MACHINES.UNIVERSAL)"
            :width="renderData.machine.width"
            class="field"
            text="У"
        />

        <!-- __ АШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(CUTTING_MACHINES.AUTO)"
            :width="renderData.machine.width"
            class="field"
            text="А"
        />

        <!-- __ Глухие сложные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(CUTTING_MACHINES.SOLID_HARD)"
            :width="renderData.machine.width"
            class="field"
            text="ГС"
        />

        <!-- __ Глухие простые -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(CUTTING_MACHINES.SOLID_LITE)"
            :width="renderData.machine.width"
            class="field"
            text="ГП"
        />

        <!-- __ Неопознанные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(CUTTING_MACHINES.UNKNOWN)"
            :width="renderData.machine.width"
            class="field"
            text="Н"
        />

        <!-- __ Ткань -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.textile ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.textile.width"
            class="field"
        />

        <!-- __ Типовая конструкция чехла (ТКЧ) -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.model.main.tkch ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.tkch.width"
            class="field"
        />

        <!-- __ Кант -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.model.main.kant ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.kant.width"
            class="field"
        />

        <!-- __ Состав -->
        <AppLabelTS
            v-if="showComments"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.composition ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

        <!-- __ Примечание 1 -->
        <AppLabelTS
            v-if="showComments"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.describe_1 ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

        <!-- __ Примечание 2 -->
        <AppLabelTS
            v-if="showComments"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.describe_2 ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

        <!-- __ Примечание 3 -->
        <AppLabelTS
            v-if="showComments"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.describe_3 ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { ICuttingMachineKeys, ICuttingTaskLine } from '@/types'
import type {
    IRenderCuttingLineData
} from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskCard.vue'

import { CUTTING_MACHINES } from '@/app/constants/cutting.ts'


import { storeToRefs } from 'pinia'
import { useCuttingStore } from '@/stores/CuttingStore.ts'
import {
    getCoverSizeString,
    getCuttingLineMachineType,
    getCuttingTaskModelCover,
    getCuttingTaskModelCoverName,
    getTimeString
} from '@/app/helpers/manufacture/helpers_cutting.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


interface IProps {
    cuttingLine: ICuttingTaskLine
    renderData: IRenderCuttingLineData
    showComments?: boolean,
    showDetails?: boolean,
}


const props = withDefaults(defineProps<IProps>(), {
    showComments: false,
    showDetails:  false,
})

// console.log('props.cuttingLine: ', props.cuttingLine)

// __ Данные из Хранилища
const cuttingStore = useCuttingStore()

const { globalManageTaskCardActiveCuttingLine } = storeToRefs(cuttingStore)

const DEFAULT_ALIGN     = 'center'
const DEFAULT_TEXT_SIZE = 'micro'
const DEFAULT_ROUNDED   = '4'
const ACCENT_TYPE       = 'success'
const ACTIVE_TYPE       = 'primary'
// const DEFAULT_TYPE      = 'primary'


// __ Получаем чехол модели
// const modelCover = null
const modelCover = computed(() => getCuttingTaskModelCover(props.cuttingLine))

// __ Получаем название модели
const coverName = computed(() => getCuttingTaskModelCoverName(props.cuttingLine))

// const coverName = computed(() => {
//     return modelCover.value
//         ? isAverage(props.cuttingLine)
//             ? 'Чехол для Планового матраса'
//             : modelCover.value.name_report
//         : ''
// })

// __ Тип подсветки для основного элемента
const getType = computed(() =>
    props.cuttingLine === globalManageTaskCardActiveCuttingLine.value ? ACTIVE_TYPE : 'dark')

// __ Получаем тип стегальной машины
const machineType = computed(() => getCuttingLineMachineType(props.cuttingLine))

// __ Тип подсветки для стегальной машины элемента
const getTypeForMachine = (cuttingMachineTarget: ICuttingMachineKeys) => {

    // !!! Порядок важен !!!

    if (machineType.value === CUTTING_MACHINES.AVERAGE) {
        if (cuttingMachineTarget === CUTTING_MACHINES.UNKNOWN) {
            return 'danger'
        }
        return ACCENT_TYPE
    }

    if (machineType.value === cuttingMachineTarget) {
        if (cuttingMachineTarget === CUTTING_MACHINES.UNKNOWN) {
            return 'danger'
        }

        return ACCENT_TYPE
    }

    return props.cuttingLine === globalManageTaskCardActiveCuttingLine.value ? ACTIVE_TYPE : 'dark'
}

// __ Получаем трудозатраты
const getTime = computed(() => getTimeString(props.cuttingLine, true).replaceAll('.', ''))

// __ Получаем размер чехла (Высота из размеров чехла модели)
const getSize = computed(() => getCoverSizeString(props.cuttingLine))

// console.log('activeCuttingLine: ', globalManageTaskCardActiveCuttingLine.value)


</script>

<style scoped>
.describe {
    @apply truncate;
}

.field {
    @apply cursor-pointer;
}

</style>
