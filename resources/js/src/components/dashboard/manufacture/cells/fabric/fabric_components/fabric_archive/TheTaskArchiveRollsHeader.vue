<template>

    <div class="flex">

        <!-- __ Позиция (№ по порядку) -->
        <AppLabelMultiLine
            v-if="rollsRender.position.show"
            :text="rollsRender.position.header"
            :type="getHeaderType()"
            :width="rollsRender.position.width"
            :align="rollsRender.position.headerAlign"
            :text-size="rollsRender.position.headerTextSize"
        />

        <!-- __ Номер рулона (id записи) -->
        <AppLabelMultiLine
            v-if="rollsRender.rollNumber.show"
            :text="rollsRender.rollNumber.header"
            :type="getHeaderType()"
            :width="rollsRender.rollNumber.width"
            :align="rollsRender.rollNumber.headerAlign"
            :text-size="rollsRender.rollNumber.headerTextSize"
        />

        <!-- __ Название ПС -->
        <AppLabelMultiLine
            v-if="rollsRender.fabricName.show"
            :text="rollsRender.fabricName.header"
            :type="getHeaderType()"
            :width="rollsRender.fabricName.width"
            :align="rollsRender.fabricName.headerAlign"
            :text-size="rollsRender.fabricName.headerTextSize"
        />

        <!-- __ Средняя длина ткани -->
        <AppLabelMultiLine
            v-if="rollsRender.textileLength.show"
            :text="rollsRender.textileLength.header"
            :type="getHeaderType()"
            :width="rollsRender.textileLength.width"
            :align="rollsRender.textileLength.headerAlign"
            :text-size="rollsRender.textileLength.headerTextSize"
        />

        <!-- __ Средняя длина ПС -->
        <AppLabelMultiLine
            v-if="rollsRender.fabricLength.show"
            :text="rollsRender.fabricLength.header"
            :type="getHeaderType()"
            :width="rollsRender.fabricLength.width"
            :align="rollsRender.fabricLength.headerAlign"
            :text-size="rollsRender.fabricLength.headerTextSize"
        />

        <!-- __ Количество рулонов (всегда = 1) -->
        <AppLabelMultiLine
            v-if="rollsRender.rollsAmount.show"
            :text="rollsRender.rollsAmount.header"
            :type="getHeaderType()"
            :width="rollsRender.rollsAmount.width"
            :align="rollsRender.rollsAmount.headerAlign"
            :text-size="rollsRender.rollsAmount.headerTextSize"
        />

        <!-- __ Средние трудозатраты на рулон -->
        <AppLabelMultiLine
            v-if="rollsRender.productivity.show"
            :text="rollsRender.productivity.header"
            :type="getHeaderType()"
            :width="rollsRender.productivity.width"
            :align="rollsRender.productivity.headerAlign"
            :text-size="rollsRender.productivity.headerTextSize"
        />

        <!-- __ Статус -->
        <AppLabelMultiLine
            v-if="rollsRender.status.show"
            :text="rollsRender.status.header"
            :type="getHeaderType()"
            :width="rollsRender.status.width"
            :align="rollsRender.status.headerAlign"
            :text-size="rollsRender.status.headerTextSize"
        />

        <!-- __ Начало -->
        <AppLabelMultiLine
            v-if="rollsRender.startAt.show"
            :text="rollsRender.startAt.header"
            :type="getHeaderType()"
            :width="rollsRender.startAt.width"
            :align="rollsRender.startAt.headerAlign"
            :text-size="rollsRender.startAt.headerTextSize"
        />

        <!-- __ Окончание -->
        <AppLabelMultiLine
            v-if="rollsRender.finishAt.show"
            :text="rollsRender.finishAt.header"
            :type="getHeaderType()"
            :width="rollsRender.finishAt.width"
            :align="rollsRender.finishAt.headerAlign"
            :text-size="rollsRender.finishAt.headerTextSize"
        />

        <!-- __ Время стегания -->
        <AppLabelMultiLine
            v-if="rollsRender.rollTime.show"
            :text="rollsRender.rollTime.header"
            :type="getHeaderType()"
            :width="rollsRender.rollTime.width"
            :align="rollsRender.rollTime.headerAlign"
            :text-size="rollsRender.rollTime.headerTextSize"
        />

        <!-- __ Ответственный -->
        <AppLabelMultiLine
            v-if="rollsRender.finishBy.show"
            :text="rollsRender.finishBy.header"
            :type="getHeaderType()"
            :width="rollsRender.finishBy.width"
            :align="rollsRender.finishBy.headerAlign"
            :text-size="rollsRender.finishBy.headerTextSize"
        />

        <!-- __ Учет в 1С -->
        <AppLabelMultiLine
            v-if="rollsRender.register1CAt.show"
            :text="rollsRender.register1CAt.header"
            :type="getHeaderType()"
            :width="rollsRender.register1CAt.width"
            :align="rollsRender.register1CAt.headerAlign"
            :text-size="rollsRender.register1CAt.headerTextSize"
        />

        <!-- __ Ответственный за Учет в 1С -->
        <AppLabelMultiLine
            v-if="rollsRender.register1CBy.show"
            :text="rollsRender.register1CBy.header"
            :type="getHeaderType()"
            :width="rollsRender.register1CBy.width"
            :align="rollsRender.register1CBy.headerAlign"
            :text-size="rollsRender.register1CBy.headerTextSize"
        />

        <!-- __ Перемещение на закрой -->
        <AppLabelMultiLine
            v-if="rollsRender.moveToCutAt.show"
            :text="rollsRender.moveToCutAt.header"
            :type="getHeaderType()"
            :width="rollsRender.moveToCutAt.width"
            :align="rollsRender.moveToCutAt.headerAlign"
            :text-size="rollsRender.moveToCutAt.headerTextSize"
        />

        <!-- __ Ответственный за Перемещение на закрой -->
        <AppLabelMultiLine
            v-if="rollsRender.moveToCutBy.show"
            :text="rollsRender.moveToCutBy.header"
            :type="getHeaderType()"
            :width="rollsRender.moveToCutBy.width"
            :align="rollsRender.moveToCutBy.headerAlign"
            :text-size="rollsRender.moveToCutBy.headerTextSize"
        />

        <!-- __ Комментарий -->
        <AppLabelMultiLine
            v-if="rollsRender.description.show"
            :text="rollsRender.description.header"
            :type="getHeaderType()"
            :width="rollsRender.description.width"
            class="truncate"
            :align="rollsRender.description.headerAlign"
            :text-size="rollsRender.description.headerTextSize"
            title="Комментарий"
        />

        <!-- __ Причина невыполнения -->
        <AppLabelMultiLine
            v-if="rollsRender.reason.show"
            :text="rollsRender.reason.header"
            :type="getHeaderType()"
            :width="rollsRender.reason.width"
            :align="rollsRender.reason.headerAlign"
            :text-size="rollsRender.reason.headerTextSize"
        />

    </div>

</template>

<script setup lang="ts">
import type { IRenderData } from '@/types'

import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'

interface IProps {
    rollsRender: IRenderData,
}

/*const props =*/ defineProps<IProps>()

const getHeaderType = () => 'primary'

</script>

<style scoped>

</style>
