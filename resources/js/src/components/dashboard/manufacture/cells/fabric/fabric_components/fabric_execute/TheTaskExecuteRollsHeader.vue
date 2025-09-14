<template>

    <div class="flex">

        <!-- __ Позиция (№ по порядку) -->
        <AppLabelMultiLine
            v-if="rollsRender.position.show"
            :text="['№', 'п/п']"
            :type="getHeaderType()"
            :width="rollsRender.position.width"
            :align="rollsRender.position.align"
            :text-size="rollsRender.position.headerTextSize"
        />

        <!-- __ Маяк "Калькулятора" -->
        <AppLabelMultiLine
            v-if="rollsRender.isCalc.show"
            :text="['Кальк.', '+ / -']"
            :type="getHeaderType()"
            :width="rollsRender.isCalc.width"
            :align="rollsRender.isCalc.align"
            :text-size="rollsRender.isCalc.headerTextSize"
        />

        <!-- __ Номер рулона (id записи) -->
        <AppLabelMultiLine
            v-if="rollsRender.rollNumber.show"
            :text="['Номер', 'рулона']"
            :type="getHeaderType()"
            :width="rollsRender.rollNumber.width"
            :align="rollsRender.rollNumber.align"
            :text-size="rollsRender.rollNumber.headerTextSize"
        />

        <!-- __ Название ПС -->
        <AppLabelMultiLine
            v-if="rollsRender.fabricName.show"
            :text="['Полотно', 'стеганное']"
            :type="getHeaderType()"
            :width="rollsRender.fabricName.width"
            :align="rollsRender.fabricName.align"
            :text-size="rollsRender.fabricName.headerTextSize"
        />

        <!-- __ Средняя длина ткани -->
        <AppLabelMultiLine
            v-if="rollsRender.textileLength.show"
            :text="['Длина', 'ткани']"
            :type="getHeaderType()"
            :width="rollsRender.textileLength.width"
            :align="rollsRender.textileLength.align"
            :text-size="rollsRender.textileLength.headerTextSize"
        />

        <!-- __ Количество рулонов в ПС -->
        <AppLabelMultiLine
            v-if="rollsRender.textileLayersAmount.show"
            :text="['Рул.', 'в ПС']"
            :type="getHeaderType()"
            :width="rollsRender.textileLayersAmount.width"
            :align="rollsRender.textileLayersAmount.align"
            :text-size="rollsRender.textileLayersAmount.headerTextSize"
        />

        <!-- __ Средняя длина ПС -->
        <AppLabelMultiLine
            v-if="rollsRender.fabricLength.show"
            :text="['Длина', 'ПС']"
            :type="getHeaderType()"
            :width="rollsRender.fabricLength.width"
            :align="rollsRender.fabricLength.align"
            :text-size="rollsRender.fabricLength.headerTextSize"
        />

        <!-- __ Количество рулонов (всегда = 1) -->
        <AppLabelMultiLine
            v-if="rollsRender.rollsAmount.show"
            :text="['1', '']"
            :type="getHeaderType()"
            :width="rollsRender.rollsAmount.width"
            :align="rollsRender.rollsAmount.align"
            :text-size="rollsRender.rollsAmount.headerTextSize"
        />

        <!-- __ Средние трудозатраты на рулон -->
        <AppLabelMultiLine
            v-if="rollsRender.productivity.show"
            :text="['Плановые', 'трудозатраты']"
            :type="getHeaderType()"
            :width="rollsRender.productivity.width"
            :align="rollsRender.productivity.align"
            :text-size="rollsRender.productivity.headerTextSize"
        />

        <!-- __ Комментарий -->
        <AppLabelMultiLine
            v-if="rollsRender.description.show"
            :text="['Комментарий', '']"
            :type="getHeaderType()"
            :width="rollsRender.description.width"
            class="truncate"
            :align="rollsRender.description.align"
            :text-size="rollsRender.description.headerTextSize"
            title="Комментарий"
        />

        <!-- __ Статус -->
        <AppLabelMultiLine
            v-if="rollsRender.status.show"
            :text="['Статус', '']"
            :type="getHeaderType()"
            :width="rollsRender.status.width"
            :align="rollsRender.status.align"
            :text-size="rollsRender.status.headerTextSize"
        />

        <!-- __ Начало -->
        <AppLabelMultiLine
            v-if="rollsRender.startAt.show"
            :text="['Начало', 'стегания']"
            :type="getHeaderType()"
            :width="rollsRender.startAt.width"
            :align="rollsRender.startAt.align"
            :text-size="rollsRender.startAt.headerTextSize"
        />

        <!-- __ Окончание -->
        <AppLabelMultiLine
            v-if="rollsRender.finishAt.show"
            :text="['Окончание', 'стегания']"
            :type="getHeaderType()"
            :width="rollsRender.finishAt.width"
            :align="rollsRender.finishAt.align"
            :text-size="rollsRender.finishAt.headerTextSize"
        />

        <!-- __ Время стегания -->
        <AppLabelMultiLine
            v-if="rollsRender.rollTime.show"
            :text="['Время', 'стегания']"
            :type="getHeaderType()"
            :width="rollsRender.rollTime.width"
            :align="rollsRender.rollTime.align"
            :text-size="rollsRender.rollTime.headerTextSize"
        />

        <!-- __ Ответственный -->
        <AppLabelMultiLine
            v-if="rollsRender.finishBy.show"
            :text="['Ответственный', '']"
            :type="getHeaderType()"
            :width="rollsRender.finishBy.width"
            :align="rollsRender.finishBy.align"
            :text-size="rollsRender.finishBy.headerTextSize"
        />

        <!-- __ Причина невыполнения -->
        <AppLabelMultiLine
            v-if="rollsRender.reason.show"
            :text="['Причина', 'невыполнения']"
            :type="getHeaderType()"
            :width="rollsRender.reason.width"
            :align="rollsRender.reason.align"
            :text-size="rollsRender.reason.headerTextSize"
            title="Причина невыполнения"
        />

    </div>

</template>

<script setup lang="ts">

import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'

import type { IRenderData } from '@/types'
// import { deepCopy } from '@/app/helpers/helpers_lib.js'
// import { shallowRef } from 'vue'
// import AppLabel from '@/components/ui/labels/AppLabel.vue'


interface IProps {
    rollsRender: IRenderData,
}

/*const props =*/ defineProps<IProps>()

// const props = defineProps({
//     rollsRender: {
//         type: Object,
//         required: true
//     }
// })

// const localRender = deepCopy(props.rollsRender)
// const localRender = shallowRef(props.rollsRender)

// __ Получаем тип шапки таблицы
const getHeaderType = () => 'primary'

</script>

<style scoped>

</style>
