<template>
    <div class="flex py-[3px]">

        <!-- __ № п/п -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.position.width"
            text="#"
            class="field"
        />

        <!-- __ Размер модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.size.width"
            text="Размер"
            class="field"
        />

        <!-- __ Название модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.model.width"
            text="Модель"
            class="field"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.amount.width"
            text="Шт."
            class="field"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.time.width"
            text="Тр-ты"
            class="field"
        />
        <!-- __ УШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            text="У"
            class="field"
        />

        <!-- __ АШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            text="А"
            class="field"
        />

        <!-- __ Глухие сложные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            text="ГС"
            class="field"
        />

        <!-- __ Глухие простые -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            text="ГП"
            class="field"
        />

        <!-- __ Неопознанные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            text="Н"
            class="field"
        />

        <!-- __ Ткань -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.textile.width"
            text="Ткань"
            class="field"
        />

        <!-- __ Состав -->
        <AppLabelTS
            v-if="describeShow"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.describe.width"
            text="Состав"
            class="field"
        />

        <!-- __ Примечание 1 -->
        <AppLabelTS
            v-if="describeShow"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.describe.width"
            text="Прим. 1"
            class="field"
        />

        <!-- __ Примечание 2 -->
        <AppLabelTS
            v-if="describeShow"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.describe.width"
            text="Прим. 2"
            class="field"
        />

        <!-- __ Примечание 3 -->
        <AppLabelTS
            v-if="describeShow"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.describe.width"
            text="Прим. 3"
            class="field"
        />

        <!-- __ Типовая конструкция чехла (ТКЧ) -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.tkch.width"
            text="ТКЧ"
            class="field"
        />

        <!-- __ Кант -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.kant.width"
            text="Кант"
            class="field"
        />

    </div>

</template>

<script lang="ts" setup>
import type {
    IRenderSewingLineData
} from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCard.vue'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


interface IProps {
    renderData: IRenderSewingLineData
    describeShow?: boolean,
}

const props = withDefaults(defineProps<IProps>(), {
    describeShow: false,
})

const DEFAULT_ALIGN     = 'center'
const DEFAULT_TEXT_SIZE = 'micro'
const DEFAULT_ROUNDED   = '4'
const DEFAULT_TYPE      = 'primary'


</script>

<style scoped>
.field {
    @apply cursor-pointer;
}
</style>
