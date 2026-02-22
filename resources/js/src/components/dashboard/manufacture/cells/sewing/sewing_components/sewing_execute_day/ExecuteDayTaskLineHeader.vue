<template>
    <div class="flex items-center">

        <!-- __ ✓ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.check"
            rounded="4"
            text="✓"
        />

        <div :class="fieldWidths.space" class="ml-[-2px]"></div>

        <!-- __ Позиция -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.position"
            rounded="4"
            text="#"
        />

        <!-- __ Размер -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.size"
            rounded="4"
            text="Размер"
        />

        <!-- __ Название чехла -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.coverName"
            rounded="4"
            text="Модель чехла"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.amount"
            rounded="4"
            text="шт."
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.time"
            rounded="4"
            text="Время"
        />

        <!-- __ Швейная машина -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            text="ШМ"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.machine"
            align="center"
            rounded="4"
        />

        <!-- __ Ткань -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.textile"
            rounded="4"
            text="Ткань"
        />

        <!-- __ ТКЧ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.tkch"
            rounded="4"
            text="ТКЧ"
        />

        <!-- __ Кант -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.kant"
            rounded="4"
            text="Кант"
        />

        <!-- __ Состав -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.composition"
            class="truncate"
            rounded="4"
            text="Состав"
        />

        <!-- __ Примечание 1 -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.describe_1"
            class="truncate"
            rounded="4"
            text="Прим. 1"
        />

        <!-- __ Примечание 2 -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.describe_2"
            class="truncate"
            rounded="4"
            text="Прим. 2"
        />

        <!-- __ Примечание 3 -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.describe_3"
            class="truncate"
            rounded="4"
            text="Прим. 3"
        />

        <!-- __ finished_at -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.timeLabel"
            class="truncate"
            rounded="4"
            text="Отметка"
        />

        <!-- __ Причина не выполнения -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.reason"
            class="truncate"
            rounded="4"
            text="Причина"
        />

    </div>
</template>

<script lang="ts" setup>
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    fieldWidths: Record<string, string>
}

defineProps<IProps>()

const LINE_HEIGHT    = 'h-[25px]'
const LINE_TEXT_SIZE = 'micro'
const DEFAULT_TYPE   = 'primary'
const DEFAULT_ALIGN  = 'center'

</script>

<style scoped>

</style>
