<template>
    <div class="flex">

        <!-- __ № п/п -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.position.toString()"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.position.width"
            class="field"
        />

        <!-- __ Размер модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.order_line.size"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.size.width"
            class="field"
        />

        <!-- __ Название модели -->
        <AppLabelTS
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.order_line.model.main.name_report"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.model.width"
            class="field"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.amount.toString()"
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
            :type="getType"
            :width="renderData.time.width"
        />

        <!-- __ УШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(SEWING_MACHINES.UNIVERSAL)"
            :width="renderData.machine.width"
            class="field"
            text="У"
        />

        <!-- __ АШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(SEWING_MACHINES.AUTO)"
            :width="renderData.machine.width"
            class="field"
            text="А"
        />

        <!-- __ Глухие сложные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(SEWING_MACHINES.SOLID_HARD)"
            :width="renderData.machine.width"
            class="field"
            text="ГС"
        />

        <!-- __ Глухие простые -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(SEWING_MACHINES.SOLID_LITE)"
            :width="renderData.machine.width"
            class="field"
            text="ГП"
        />

        <!-- __ Неопознанные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(SEWING_MACHINES.UNKNOWN)"
            :width="renderData.machine.width"
            class="field"
            text="Н"
        />

        <!-- __ Ткань -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.order_line.textile ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.textile.width"
            class="field"
        />

        <!-- __ Состав -->
        <AppLabelTS
            v-if="describeShow"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.order_line.composition ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

        <!-- __ Примечание 1 -->
        <AppLabelTS
            v-if="describeShow"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.order_line.describe_1 ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

        <!-- __ Примечание 2 -->
        <AppLabelTS
            v-if="describeShow"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.order_line.describe_2 ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

        <!-- __ Примечание 3 -->
        <AppLabelTS
            v-if="describeShow"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.order_line.describe_3 ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

        <!-- __ Типовая конструкция чехла (ТКЧ) -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.order_line.model.main.tkch ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.tkch.width"
            class="field"
        />

        <!-- __ Кант -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingLine.order_line.model.main.kant ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.kant.width"
            class="field"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useSewingStore } from '@/stores/SewingStore.ts'
import type { ISewingMachineKeys, ISewingTaskLine } from '@/types'
import type {
    IRenderSewingLineData
} from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCard.vue'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { getSewingLineMachineType, getTimeString } from '@/app/helpers/manufacture/helpers_sewing.ts'
import { AVERAGE, SEWING_MACHINES } from '@/app/constants/sewing.ts'


interface IProps {
    sewingLine: ISewingTaskLine
    renderData: IRenderSewingLineData
    describeShow?: boolean,
}

const props = withDefaults(defineProps<IProps>(), {
    describeShow: false,
})

// __ Данные из Хранилища
const sewingStore = useSewingStore()

const { globalManageTaskCardActiveSewingLine } = storeToRefs(sewingStore)

const DEFAULT_ALIGN     = 'center'
const DEFAULT_TEXT_SIZE = 'micro'
const DEFAULT_ROUNDED   = '4'
const ACCENT_TYPE       = 'success'
const ACTIVE_TYPE       = 'primary'
// const DEFAULT_TYPE      = 'primary'

// __ Тип подсветки для основного элемента
const getType = computed(() =>
    props.sewingLine === globalManageTaskCardActiveSewingLine.value ? ACTIVE_TYPE : 'dark')

// __ Получаем тип стегальной машины
const machineType = computed(() => getSewingLineMachineType(props.sewingLine))

// __ Тип подсветки для стегальной машины элемента
const getTypeForMachine = (sewingMachineTarget: ISewingMachineKeys) => {

    // !!! Порядок важен !!!

    if (machineType.value === SEWING_MACHINES.AVERAGE) {
        if (sewingMachineTarget === SEWING_MACHINES.UNKNOWN) {
            return 'danger'
        }
        return ACCENT_TYPE
    }

    if (machineType.value === sewingMachineTarget) {
        if (sewingMachineTarget === SEWING_MACHINES.UNKNOWN) {
            return 'danger'
        }

        return ACCENT_TYPE
    }

    return props.sewingLine === globalManageTaskCardActiveSewingLine.value ? ACTIVE_TYPE : 'dark'
}

// __ Получаем трудозатраты
const getTime = computed(() => getTimeString(props.sewingLine, true))

// console.log('activeSewingLine: ', globalManageTaskCardActiveSewingLine.value)

</script>

<style scoped>
.describe {
    @apply truncate;
}

.field {
    @apply cursor-pointer;
}

</style>
