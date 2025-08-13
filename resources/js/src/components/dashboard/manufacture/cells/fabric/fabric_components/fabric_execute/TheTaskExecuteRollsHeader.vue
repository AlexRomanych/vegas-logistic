<template>

    <div class="flex">

        <!-- __ Позиция (№ по порядку) -->
        <AppLabelMultiLine
            v-if="rollsRender.position.show"
            :text="['№', 'п/п']"
            :title="rollsRender.position.title"
            :type="getHeaderType()"
            :width="rollsRender.position.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Номер рулона (id записи) -->
        <AppLabelMultiLine
            v-if="rollsRender.rollNumber.show"
            :text="['Номер', 'рулона']"
            :title="rollsRender.rollNumber.title"
            :type="getHeaderType()"
            :width="rollsRender.rollNumber.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Название ПС -->
        <AppLabelMultiLine
            v-if="rollsRender.fabricName.show"
            :text="['Полотно', 'стеганное']"
            :title="rollsRender.fabricName.title"
            :type="getHeaderType()"
            :width="rollsRender.fabricName.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Средняя длина ткани -->
        <AppLabelMultiLine
            v-if="rollsRender.textileLength.show"
            :text="['Длина', 'ткани']"
            :title="rollsRender.textileLength.title"
            :type="getHeaderType()"
            :width="rollsRender.textileLength.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Средняя длина ПС -->
        <AppLabelMultiLine
            v-if="rollsRender.fabricLength.show"
            :text="['Длина', 'ПС']"
            :title="rollsRender.fabricLength.title"
            :type="getHeaderType()"
            :width="rollsRender.fabricLength.width"
            align="center"
            text-size="mini"
        />
        <!-- __ Количество рулонов (всегда = 1) -->
        <AppLabelMultiLine
            v-if="rollsRender.rollsAmount.show"
            :text="['1', '']"
            :title="rollsRender.rollsAmount.title"
            :type="getHeaderType()"
            :width="rollsRender.rollsAmount.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Средние трудозатраты на рулон -->
        <AppLabelMultiLine
            v-if="rollsRender.productivity.show"
            :text="['Плановые', 'трудозатраты']"
            :title="rollsRender.productivity.title"
            :type="getHeaderType()"
            :width="rollsRender.productivity.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Комментарий -->
        <AppLabelMultiLine
            v-if="rollsRender.description.show"
            :text="['Комментарий', '']"
            :type="getHeaderType()"
            :width="rollsRender.description.width"
            align="center"
            class="truncate"
            text-size="mini"
            title="Комментарий"
        />

        <!-- __ Статус -->
        <AppLabelMultiLine
            v-if="rollsRender.status.show"
            :text="['Статус', '']"
            :title="rollsRender.status.title"
            :type="getHeaderType()"
            :width="rollsRender.status.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Начало -->
        <AppLabelMultiLine
            v-if="rollsRender.startAt.show"
            :text="['Начало', 'стегания']"
            :title="rollsRender.startAt.title"
            :type="getHeaderType()"
            :width="rollsRender.startAt.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Окончание -->
        <AppLabelMultiLine
            v-if="rollsRender.finishAt.show"
            :text="['Окончание', 'стегания']"
            :title="rollsRender.finishAt.title"
            :type="getHeaderType()"
            :width="rollsRender.finishAt.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Время стегания -->
        <AppLabelMultiLine
            v-if="rollsRender.rollTime.show"
            :text="['Время', 'стегания']"
            :title="rollsRender.rollTime.title"
            :type="getHeaderType()"
            :width="rollsRender.rollTime.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Ответственный -->
        <AppLabelMultiLine
            v-if="rollsRender.finishBy.show"
            :text="['Ответственный', '']"
            :title="rollsRender.finishBy.title"
            :type="getHeaderType()"
            :width="rollsRender.finishBy.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Причина невыполнения -->
        <AppLabelMultiLine
            v-if="rollsRender.reason.show"
            :text="['Причина', 'невыполнения']"
            :type="getHeaderType()"
            :width="rollsRender.reason.width"
            align="center"
            text-size="mini"
            title="Причина невыполнения"
        />

    </div>

</template>

<script setup>

import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
// import AppLabel from '@/components/ui/labels/AppLabel.vue'

const props = defineProps({
    rollsRender: {
        type: Object,
        required: true
    }
})

// __ Получаем тип шапки таблицы
const getHeaderType = () => 'primary'

</script>

<style scoped>

</style>
