<template>
    <div class="flex">

        <!-- attract: Номер рулона (id записи) -->
        <AppLabel
            v-if="rollsRender.rollNumber.show"
            :text="rollsRender.rollNumber.data(roll_exec)"
            :title="rollsRender.rollNumber.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.rollNumber.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Название ПС -->
        <AppLabel
            v-if="rollsRender.fabricName.show"
            :text="rollsRender.fabricName.data(roll_exec)"
            :title="rollsRender.fabricName.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.fabricName.width"
            text-size="mini"
        />

        <!-- attract: Средняя длина ткани -->
        <AppLabel
            v-if="rollsRender.textileLength.show"
            :text="rollsRender.textileLength.data(roll_exec)"
            :title="rollsRender.textileLength.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.textileLength.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Количество рулонов (всегда = 1) -->
        <AppLabel
            v-if="rollsRender.rollsAmount.show"
            :text="rollsRender.rollsAmount.data()"
            :title="rollsRender.rollsAmount.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.rollsAmount.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Средние трудозатраты на рулон -->
        <AppLabel
            v-if="rollsRender.productivity.show"
            :text="rollsRender.productivity.data(roll_exec)"
            :title="rollsRender.productivity.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.productivity.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Комментарий -->
        <AppLabel
            v-if="rollsRender.description.show"
            :text="rollsRender.description.data(roll_exec)"
            :title="rollsRender.description.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.description.width"
            class="truncate"
            text-size="mini"
        />

        <!-- attract: Статус -->
        <AppLabel
            v-if="rollsRender.status.show"
            :text="rollsRender.status.data(roll_exec)"
            :title="rollsRender.status.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.status.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Начало -->
        <AppLabel
            v-if="rollsRender.startAt.show"
            :text="rollsRender.startAt.data(roll_exec)"
            :title="rollsRender.startAt.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.startAt.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Окончание -->
        <AppLabel
            v-if="rollsRender.finishAt.show"
            :text="rollsRender.finishAt.data(roll_exec)"
            :title="rollsRender.finishAt.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.finishAt.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Время стегания -->
        <AppLabel
            v-if="rollsRender.rollTime.show"
            :text="rollsRender.rollTime.data(roll_exec)"
            :title="rollsRender.rollTime.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.rollTime.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Ответственный -->
        <AppLabel
            v-if="rollsRender.finishBy.show"
            :text="rollsRender.finishBy.data(roll_exec)"
            :title="rollsRender.finishBy.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.finishBy.width"
            text-size="mini"
        />

<!--        <div>-->
<!--            {{roll_exec.position}}-->
<!--        </div>-->

    </div>
</template>

<script setup>

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'

const props = defineProps({
    roll_exec: {
        type: Object,
        required: false,
        default: () => ({})
    },
    rollsRender: {
        type: Object,
        required: true
    }
})

// attract: Получаем тип раскраски в зависимости от статуса выполнения рулона
const getTypeByStatus = (roll_exec) => {
    return 'dark'
}

</script>

<style scoped>

</style>
