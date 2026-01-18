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
        />

        <!-- __ Размер модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.size.width"
            text="Размер"
        />

        <!-- __ Название модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.model.width"
            text="Модель"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.amount.width"
            text="шт."
        />

        <!-- __ Ткань -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.textile.width"
            text="Ткань"
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
        />

        <!-- __ Типовая конструкция чехла (ТКЧ) -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.tkch.width"
            text="ТКЧ"
        />

        <!-- __ Кант -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.kant.width"
            text="Кант"
        />

        <!-- __ УШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            text="У"
        />

        <!-- __ АШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            text="А"
        />

        <!-- __ Глухие сложные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            text="ГС"
        />

        <!-- __ Глухие простые -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            text="ГП"
        />

        <!-- __ Неопознанные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            text="Н"
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

</style>
