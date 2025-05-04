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

        <!-- attract: Средняя длина ПС -->
        <AppLabel
            v-if="rollsRender.fabricLength.show"
            :text="rollsRender.fabricLength.data(roll_exec)"
            :title="rollsRender.fabricLength.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.fabricLength.width"
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
            :title="rollsRender.description.title(roll_exec)"
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
        <!--        :text="rollsRender.rollTime.data(roll_exec)"-->
        <AppLabel
            v-if="rollsRender.rollTime.show"
            :text="duration"
            :title="rollsRender.rollTime.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.rollTime.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Ответственный -->
        <AppSelectSimple
            v-if="rollsRender.finishBy.show"
            :multiple="false"
            :selectData="rollsRender.finishBy.data(roll_exec)"
            :title="rollsRender.finishBy.title"
            :type="getTypeByStatusFinishBy(roll_exec)"
            :width="rollsRender.finishBy.width"
            text-size="mini"
            @change="getSelectedWorker"
            :disabled="roll_exec.status === FABRIC_ROLL_STATUS.CREATED.CODE"
        />


        <!-- attract: Причина невыполнения -->
        <AppLabel
            v-if="rollsRender.reason.show"
            :text="rollsRender.reason.data(roll_exec)"
            :title="rollsRender.reason.title(roll_exec)"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.reason.width"
            class="truncate"
            text-size="mini"
        />
        <!--        <div>-->
        <!--            {{roll_exec.position}}-->
        <!--        </div>-->

    </div>
</template>

<script setup>
import {onUnmounted, ref, watch} from 'vue'
import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {
    FABRIC_ROLL_STATUS,
    FABRIC_ROLL_STATUS_LIST,
    FABRIC_TASK_STATUS
} from '/resources/js/src/app/constants/fabrics.js'

import {getDuration} from '/resources/js/src/app/helpers/helpers_date.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppSelectSimple from '/resources/js/src/components/ui/selects/AppSelectSimple.vue'

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

const fabricsStore = useFabricsStore()

// attract: Получаем тип раскраски в зависимости от статуса выполнения рулона
const getTypeByStatus = (roll_exec) => {

    if (roll_exec.status === FABRIC_ROLL_STATUS.CREATED.CODE) return 'dark'
    if (roll_exec.status === FABRIC_ROLL_STATUS.RUNNING.CODE) return 'warning'
    if (roll_exec.status === FABRIC_ROLL_STATUS.PAUSED.CODE) return 'light'
    if (roll_exec.status === FABRIC_ROLL_STATUS.DONE.CODE) return 'success'
    if (roll_exec.status === FABRIC_ROLL_STATUS.FALSE.CODE) return 'danger'
    if (roll_exec.status === FABRIC_ROLL_STATUS.ROLLING.CODE) return 'orange'

    return 'dark'
}

// attract: Получаем тип раскраски в зависимости от наличия ответственного выполнения рулона
const getTypeByStatusFinishBy = (roll_exec) => {
    if (roll_exec.finish_by === 0 && roll_exec.status !== FABRIC_ROLL_STATUS.CREATED.CODE) return 'danger'
    return getTypeByStatus(roll_exec)
}



// attract: Добавляем часики выполнения рулона
let intervalId = null   // Для очистки интервала, если он будет создан, чтобы убрать утечки памяти
const duration = ref('')

watch(() => props.roll_exec, (newRoll, oldRoll) => {

    duration.value = getDuration(null, null, newRoll.duration)

    if (newRoll.status === FABRIC_ROLL_STATUS.RUNNING.CODE) {

        if (!intervalId) {

            intervalId = setInterval(() => {

                if (!newRoll.resume_at) {
                    duration.value = getDuration(newRoll.start_at)
                } else {
                    duration.value = getDuration(newRoll.resume_at, null, newRoll.duration)
                }

            }, 1000)
        }

    } else if (newRoll.status === FABRIC_ROLL_STATUS.DONE.CODE || newRoll.status === FABRIC_ROLL_STATUS.PAUSED.CODE) {

        if (intervalId !== null) {
            clearInterval(intervalId);
            intervalId = null
            duration.value = getDuration(null, null, newRoll.duration)
        }

    }
}, {deep: true, immediate: true})


const getSelectedWorker = (workerData) => {
    console.log(workerData)
    fabricsStore.globalSelectWorkerFlag = true
    fabricsStore.globalSelectWorkerId = workerData.id

}


onUnmounted(() => {
    if (intervalId !== null) {
        clearInterval(intervalId);
    }
});

</script>

<style scoped>

</style>
