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

        <!--<div :class="fieldWidths.space" class="ml-[-2px]"></div>-->

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

        <!-- __ Материалы -->
        <div v-for="material in materials" :key="material.code_1c">
            <AppLabelMultiLineTS
                :align="DEFAULT_ALIGN"
                :height="LINE_HEIGHT_MATERIAL"
                :text="splitStringByWeight(material.name, 35)"
                :text-size="MATERIAL_TEXT_SIZE"
                :type="MATERIAL_TYPE"
                :width="fieldWidths.material"
                class="truncate"
                height-limit="h-[50px]"
                rounded="4"

            />
        </div>

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

        <!-- __ Причина не выполнения -->
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

        <!--__ Описание -->
        <!--<AppLabelMultiLineTS-->
        <!--    :align="DEFAULT_ALIGN"-->
        <!--    :height="LINE_HEIGHT"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="DEFAULT_TYPE"-->
        <!--    :width="fieldWidths.description"-->
        <!--    class="truncate"-->
        <!--    rounded="4"-->
        <!--    text="Комментарий"-->
        <!--/>-->

        <!-- __ Заявка -->
        <!--<AppLabelMultiLineTS-->
        <!--    :align="DEFAULT_ALIGN"-->
        <!--    :height="LINE_HEIGHT"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="DEFAULT_TYPE"-->
        <!--    :width="fieldWidths.order"-->
        <!--    rounded="4"-->
        <!--    text="Заявка"-->
        <!--/>-->


        <!-- __ Производственная Линия -->
        <!--<AppLabelMultiLineTS-->
        <!--    :height="LINE_HEIGHT"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="DEFAULT_TYPE"-->
        <!--    :width="fieldWidths.manuf_line"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--    text="Л-я"-->
        <!--/>-->

        <!-- __ КДБ -->
        <!--<AppLabelMultiLineTS-->
        <!--    :align="DEFAULT_ALIGN"-->
        <!--    :height="LINE_HEIGHT"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="DEFAULT_TYPE"-->
        <!--    :width="fieldWidths.kdb"-->
        <!--    rounded="4"-->
        <!--    text="КДБ"-->
        <!--/>-->
    </div>
</template>

<script lang="ts" setup>
import type { MatrixManufactureGroupMaterial } from '@/types'
import { splitStringByWeight } from '@/app/helpers/helpers.ts'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'


interface IProps {
    fieldWidths: Record<string, string>
    materials: MatrixManufactureGroupMaterial[]
}

defineProps<IProps>()

const emits = defineEmits<{
    (e: 'toggleAll'): void,
}>()

const LINE_HEIGHT_MATERIAL = 'h-[15px]'
const LINE_HEIGHT          = 'h-[25px]'
const LINE_TEXT_SIZE       = 'mini'
const MATERIAL_TEXT_SIZE   = 'micro'
const DEFAULT_TYPE         = 'primary'
const MATERIAL_TYPE        = 'indigo'
const DEFAULT_ALIGN        = 'center'

</script>

<style scoped>

</style>
