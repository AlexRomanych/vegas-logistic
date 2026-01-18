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

        <!-- __ УШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(sewingLine.order_line.model.main.is_universal)"
            :width="renderData.machine.width"
            text="У"
            class="field"
        />

        <!-- __ АШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(sewingLine.order_line.model.main.is_auto)"
            :width="renderData.machine.width"
            text="А"
            class="field"
        />

        <!-- __ Глухие сложные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(sewingLine.order_line.model.main.is_solid_hard)"
            :width="renderData.machine.width"
            text="ГС"
            class="field"
        />

        <!-- __ Глухие простые -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForMachine(sewingLine.order_line.model.main.is_solid_lite)"
            :width="renderData.machine.width"
            text="ГП"
            class="field"
        />

        <!-- __ Неопознанные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :width="renderData.machine.width"
            text="Н"
            :type="getTypeForMachine()"
            class="field"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useSewingStore } from '@/stores/SewingStore.ts'
import type { ISewingTaskLine } from '@/types'
import type {
    IRenderSewingLineData
} from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCard.vue'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


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
const DEFAULT_TYPE      = 'primary'

// __ Тип для основного элемента
const getType = computed(() =>
    props.sewingLine === globalManageTaskCardActiveSewingLine.value ? ACTIVE_TYPE : 'dark')

// __ Тип для стегальной машины элемента
const getTypeForMachine = (condition: boolean = false) => {
    if (condition) {
        return ACCENT_TYPE
    }
    return props.sewingLine === globalManageTaskCardActiveSewingLine.value ? ACTIVE_TYPE : 'dark'
}
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
