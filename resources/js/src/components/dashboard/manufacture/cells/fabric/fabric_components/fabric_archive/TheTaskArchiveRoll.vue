<template>

    <div class="flex">

        <!-- __ Позиция (№ по порядку) -->
        <AppLabel
            v-if="rollsRender.position.show"
            :text="roll.position.toString()"
            :type="typeof rollsRender.position.type === 'function' ? rollsRender.position.type(roll) : rollsRender.position.type"
            :width="rollsRender.position.width"
            :align="rollsRender.position.dataAlign"
            :text-size="rollsRender.position.dataTextSize"
        />

        <!-- __ Номер рулона (id записи) -->
        <AppLabel
            v-if="rollsRender.rollNumber.show"
            :text="roll.id.toString()"
            :type="typeof rollsRender.rollNumber.type === 'function' ? rollsRender.rollNumber.type(roll) : rollsRender.rollNumber.type"
            :width="rollsRender.rollNumber.width"
            :align="rollsRender.rollNumber.dataAlign"
            :text-size="rollsRender.rollNumber.dataTextSize"
        />

        <!-- __ Название ПС -->
        <AppLabel
            v-if="rollsRender.fabricName.show"
            :text="rollsRender.fabricName.data ? rollsRender.fabricName.data(roll) : ''"
            :type="typeof rollsRender.fabricName.type === 'function' ? rollsRender.fabricName.type(roll) : rollsRender.fabricName.type"
            :width="rollsRender.fabricName.width"
            :align="rollsRender.fabricName.dataAlign"
            :text-size="rollsRender.fabricName.dataTextSize"
        />

        <!-- __ Средняя длина ткани -->
        <AppLabel
            v-if="rollsRender.textileLength.show"
            :text="rollsRender.textileLength.data ? rollsRender.textileLength.data(roll) : ''"
            :type="typeof rollsRender.textileLength.type === 'function' ? rollsRender.textileLength.type(roll) : rollsRender.textileLength.type"
            :width="rollsRender.textileLength.width"
            :align="rollsRender.textileLength.dataAlign"
            :text-size="rollsRender.textileLength.dataTextSize"
        />

        <!-- __ Средняя длина ПС -->
        <AppLabel
            v-if="rollsRender.fabricLength.show"
            :text="rollsRender.fabricLength.data ? rollsRender.fabricLength.data(roll) : ''"
            :type="typeof rollsRender.fabricLength.type === 'function' ? rollsRender.fabricLength.type(roll) : rollsRender.fabricLength.type"
            :width="rollsRender.fabricLength.width"
            :align="rollsRender.fabricLength.dataAlign"
            :text-size="rollsRender.fabricLength.dataTextSize"
        />

        <!-- __ Количество рулонов (всегда = 1) -->
        <AppLabel
            v-if="rollsRender.rollsAmount.show"
            :text="rollsRender.rollsAmount.data ? rollsRender.rollsAmount.data(roll) : ''"
            :type="typeof rollsRender.rollsAmount.type === 'function' ? rollsRender.rollsAmount.type(roll) : rollsRender.rollsAmount.type"
            :width="rollsRender.rollsAmount.width"
            :align="rollsRender.rollsAmount.dataAlign"
            :text-size="rollsRender.rollsAmount.dataTextSize"
        />

        <!-- __ Средние трудозатраты на рулон -->
        <AppLabel
            v-if="rollsRender.productivity.show"
            :text="rollsRender.productivity.data ? rollsRender.productivity.data(roll) : ''"
            :type="typeof rollsRender.productivity.type === 'function' ? rollsRender.productivity.type(roll) : rollsRender.productivity.type"
            :width="rollsRender.productivity.width"
            :align="rollsRender.productivity.dataAlign"
            :text-size="rollsRender.productivity.dataTextSize"
        />

        <!-- __ Статус -->
        <AppLabel
            v-if="rollsRender.status.show"
            :text="rollsRender.status.data ? rollsRender.status.data(roll) : ''"
            :type="typeof rollsRender.status.type === 'function' ? rollsRender.status.type(roll) : rollsRender.status.type"
            :width="rollsRender.status.width"
            :align="rollsRender.status.dataAlign"
            :text-size="rollsRender.status.dataTextSize"
        />

        <!-- __ Начало -->
        <AppLabel
            v-if="rollsRender.startAt.show"
            :text="rollsRender.startAt.data ? rollsRender.startAt.data(roll) : ''"
            :type="typeof rollsRender.startAt.type === 'function' ? rollsRender.startAt.type(roll) : rollsRender.startAt.type"
            :width="rollsRender.startAt.width"
            :align="rollsRender.startAt.dataAlign"
            :text-size="rollsRender.startAt.dataTextSize"
        />

        <!-- __ Окончание -->
        <AppLabel
            v-if="rollsRender.finishAt.show"
            :text="rollsRender.finishAt.data ? rollsRender.finishAt.data(roll) : ''"
            :type="typeof rollsRender.finishAt.type === 'function' ? rollsRender.finishAt.type(roll) : rollsRender.finishAt.type"
            :width="rollsRender.finishAt.width"
            :align="rollsRender.finishAt.dataAlign"
            :text-size="rollsRender.finishAt.dataTextSize"
        />

        <!-- __ Время стегания -->
        <AppLabel
            v-if="rollsRender.rollTime.show"
            :text="rollsRender.rollTime.data ? rollsRender.rollTime.data(roll) : ''"
            :type="typeof rollsRender.rollTime.type === 'function' ? rollsRender.rollTime.type(roll) : rollsRender.rollTime.type"
            :width="rollsRender.rollTime.width"
            :align="rollsRender.rollTime.dataAlign"
            :text-size="rollsRender.rollTime.dataTextSize"
        />

        <!-- __ Плановое время стегания -->
        <AppLabel
            v-if="rollsRender.rollTimePlan.show"
            :text="rollsRender.rollTimePlan.data ? rollsRender.rollTimePlan.data(roll) : ''"
            :type="typeof rollsRender.rollTimePlan.type === 'function' ? rollsRender.rollTimePlan.type(roll) : rollsRender.rollTimePlan.type"
            :width="rollsRender.rollTimePlan.width"
            :align="rollsRender.rollTimePlan.dataAlign"
            :text-size="rollsRender.rollTimePlan.dataTextSize"
        />

        <!-- __ Ответственный -->
        <AppLabel
            v-if="rollsRender.finishBy.show"
            :text="rollsRender.finishBy.data ? rollsRender.finishBy.data(roll) : ''"
            :type="typeof rollsRender.finishBy.type === 'function' ? rollsRender.finishBy.type(roll) : rollsRender.finishBy.type"
            :width="rollsRender.finishBy.width"
            :align="rollsRender.finishBy.dataAlign"
            :text-size="rollsRender.finishBy.dataTextSize"
        />

        <!-- __ Учет в 1С -->
        <AppLabel
            v-if="rollsRender.register1CAt.show"
            :text="rollsRender.register1CAt.data ? rollsRender.register1CAt.data(roll) : ''"
            :type="typeof rollsRender.register1CAt.type === 'function' ? rollsRender.register1CAt.type(roll) : rollsRender.register1CAt.type"
            :width="rollsRender.register1CAt.width"
            :align="rollsRender.register1CAt.dataAlign"
            :text-size="rollsRender.register1CAt.dataTextSize"
        />

        <!-- __ Ответственный за Учет в 1С -->
        <AppLabel
            v-if="rollsRender.register1CBy.show"
            :text="rollsRender.register1CBy.data ? rollsRender.register1CBy.data(roll) : ''"
            :type="typeof rollsRender.register1CBy.type === 'function' ? rollsRender.register1CBy.type(roll) : rollsRender.register1CBy.type"
            :width="rollsRender.register1CBy.width"
            :align="rollsRender.register1CBy.dataAlign"
            :text-size="rollsRender.register1CBy.dataTextSize"
        />

        <!-- __ Перемещение на закрой -->
        <AppLabel
            v-if="rollsRender.moveToCutAt.show"
            :text="rollsRender.moveToCutAt.data ? rollsRender.moveToCutAt.data(roll) : ''"
            :type="typeof rollsRender.moveToCutAt.type === 'function' ? rollsRender.moveToCutAt.type(roll) : rollsRender.moveToCutAt.type"
            :width="rollsRender.moveToCutAt.width"
            :align="rollsRender.moveToCutAt.dataAlign"
            :text-size="rollsRender.moveToCutAt.dataTextSize"
        />

        <!-- __ Ответственный за Перемещение на закрой -->
        <AppLabel
            v-if="rollsRender.moveToCutBy.show"
            :text="rollsRender.moveToCutBy.data ? rollsRender.moveToCutBy.data(roll) : ''"
            :type="typeof rollsRender.moveToCutBy.type === 'function' ? rollsRender.moveToCutBy.type(roll) : rollsRender.moveToCutBy.type"
            :width="rollsRender.moveToCutBy.width"
            :align="rollsRender.moveToCutBy.dataAlign"
            :text-size="rollsRender.moveToCutBy.dataTextSize"
        />

        <!-- __ Комментарий -->
        <AppLabel
            v-if="rollsRender.description.show"
            :text="rollsRender.description.data ? rollsRender.description.data(roll) : ''"
            :type="typeof rollsRender.description.type === 'function' ? rollsRender.description.type(roll) : rollsRender.description.type"
            :width="rollsRender.description.width"
            class="truncate"
            :align="rollsRender.description.dataAlign"
            :text-size="rollsRender.description.dataTextSize"
        />

        <!-- __ Причина невыполнения -->
        <AppLabel
            v-if="rollsRender.reason.show"
            :text="rollsRender.reason.data ? rollsRender.reason.data(roll) : ''"
            :type="typeof rollsRender.reason.type === 'function' ? rollsRender.reason.type(roll) : rollsRender.reason.type"
            :width="rollsRender.reason.width"
            :align="rollsRender.reason.dataAlign"
            :text-size="rollsRender.reason.dataTextSize"
        />



    </div>

</template>

<script setup lang="ts">
import type { IRenderData } from '@/types'
import type { IRollExecExtended } from '@/components/dashboard/manufacture/cells/fabric/TheFabricsArchive.vue'

import AppLabel from '@/components/ui/labels/AppLabel.vue'

interface IProps {
    rollsRender: IRenderData
    roll: IRollExecExtended
}

const props = defineProps<IProps>()

console.log('props.roll: ', props.roll)

</script>

<style scoped>

</style>
