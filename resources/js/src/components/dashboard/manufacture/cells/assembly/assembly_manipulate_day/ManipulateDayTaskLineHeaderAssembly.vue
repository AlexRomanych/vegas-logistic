<template>
    <div class="flex items-center">

        <!-- __ Collapsed -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['▲', '▼']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            class="cursor-pointer"
            rounded="4"
            width="w-[30px]"
            @click="emits('toggleAll')"
        />

        <!-- __ ✓ -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['✓', '']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.check"
            rounded="4"
        />

        <!-- __ Позиция -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['#', '']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.position"
            rounded="4"
        />

        <!-- __ Размер -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['Размер', '']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.size"
            rounded="4"
        />

        <!-- __ Название Элемента -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['Модель', '']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.name"
            rounded="4"
        />

        <!-- __ Количество -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['Кол-', 'во']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.amount"
            rounded="4"
        />

        <!-- __ Трудозатраты -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['Время', '']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.time"
            rounded="4"
        />

        <!-- __ Производственная Линия -->
        <AppLabelMultiLineTS
            :height="LINE_HEIGHT"
            :text="['Участок', '(Линия)']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.manuf_line"
            align="center"
            rounded="4"
        />

        <!-- __ finished_at -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['Отметка', '']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.timeLabel"
            class="truncate"
            rounded="4"
        />

        <!--__ Причина не выполнения-->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['Причина', '']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.false_reason"
            class="truncate"
            rounded="4"
        />

        <!-- __ Состав МЭ -->
        <!--__ Описание -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['Состав', 'мягкого элемента']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.base_composition"
            class="truncate"
            rounded="4"
        />

        <!--__ Описание -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['Комментарий', '']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.description"
            class="truncate"
            rounded="4"
        />

        <!-- __ Заявка -->
        <AppLabelMultiLineTS
            :align="DEFAULT_ALIGN"
            :height="LINE_HEIGHT"
            :text="['Заявка', '']"
            :text-size="LINE_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="fieldWidths.order"
            rounded="4"
        />

    </div>
</template>

<script lang="ts" setup>
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


interface IProps {
    fieldWidths: Record<string, string>
}

defineProps<IProps>()

const emits = defineEmits<{
    (e: 'toggleAll'): void,
}>()

const LINE_HEIGHT    = 'h-[25px]'
const LINE_TEXT_SIZE = 'mini'
const DEFAULT_TYPE   = 'primary'
const DEFAULT_ALIGN  = 'center'

</script>

<style scoped>

</style>
