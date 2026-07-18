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

        <!-- __ Название Блока -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.name"
            rounded="4"
            text="Модель Блока"
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

        <!-- __ Площадь -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.square"
            rounded="4"
            text="S, кв.м."
        />

        <!-- __ Производственная Линия -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.manuf_line"
            align="center"
            rounded="4"
            text="Л-я"
        />

        <!-- __ КДБ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.kdb"
            rounded="4"
            text="КДБ"
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
            :width="fieldWidths.false_reason"
            class="truncate"
            rounded="4"
            text="Причина"
        />

        <!-- __ Описание -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.description"
            class="truncate"
            rounded="4"
            text="Комментарий"
        />

        <!-- __ Заявка -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.order"
            rounded="4"
            text="Заявка"
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
const LINE_TEXT_SIZE = 'mini'
const DEFAULT_TYPE   = 'primary'
const DEFAULT_ALIGN  = 'center'

</script>

<style scoped>

</style>
