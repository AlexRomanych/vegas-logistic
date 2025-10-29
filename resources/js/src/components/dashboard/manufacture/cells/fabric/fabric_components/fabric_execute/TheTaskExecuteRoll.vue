<template>
    <div class="flex">

        <!-- __ Позиция (№ по порядку) -->
        <AppLabel
            v-if="rollsRender.position.show"
            :text="rollsRender.position.data(roll_exec)"
            :title="rollsRender.position.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.position.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Маяк Калькулятора -->
        <AppLabel
            v-if="rollsRender.isCalc.show"
            :text="rollsRender.isCalc.data(roll_exec)"
            :title="rollsRender.isCalc.title"
            :type="rollsRender.isCalc.type(roll_exec)"
            :width="rollsRender.isCalc.width"
            align="center"
            text-size="small"
            @click="changeCalcStatus"
        />

        <!-- __ Номер рулона (id записи) -->
        <AppLabel
            v-if="rollsRender.rollNumber.show"
            :text="rollsRender.rollNumber.data(roll_exec)"
            :title="rollsRender.rollNumber.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.rollNumber.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Название ПС -->
        <AppLabel
            v-if="rollsRender.fabricName.show"
            :text="rollsRender.fabricName.data(roll_exec)"
            :title="rollsRender.fabricName.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.fabricName.width"
            text-size="mini"
        />

        <!-- __ Средняя длина ткани -->
        <AppLabel
            v-if="rollsRender.textileLength.show"
            :text="rollsRender.textileLength.data(roll_exec)"
            :title="rollsRender.textileLength.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.textileLength.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Количество рулонов в ПС -->
        <AppLabel
            v-if="rollsRender.textileLayersAmount.show"
            :text="rollsRender.textileLayersAmount.data(roll_exec)"
            :title="rollsRender.textileLayersAmount.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.textileLayersAmount.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Средняя длина ПС -->
        <AppLabel
            v-if="rollsRender.fabricLength.show"
            :text="rollsRender.fabricLength.data(roll_exec)"
            :title="rollsRender.fabricLength.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.fabricLength.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Количество рулонов (всегда = 1) -->
        <AppLabel
            v-if="rollsRender.rollsAmount.show"
            :text="rollsRender.rollsAmount.data()"
            :title="rollsRender.rollsAmount.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.rollsAmount.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Средние трудозатраты на рулон -->
        <AppLabel
            v-if="rollsRender.productivity.show"
            :text="rollsRender.productivity.data(roll_exec)"
            :title="rollsRender.productivity.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.productivity.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Комментарий -->
        <AppLabel
            v-if="rollsRender.description.show"
            :text="rollsRender.description.data(roll_exec)"
            :title="rollsRender.description.title(roll_exec)"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.description.width"
            class="truncate"
            text-size="mini"
        />

        <!-- __ Статус -->
        <AppLabel
            v-if="rollsRender.status.show"
            :text="rollsRender.status.data(roll_exec)"
            :title="rollsRender.status.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.status.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Начало -->
        <AppLabel
            v-if="rollsRender.startAt.show"
            :text="rollsRender.startAt.data(roll_exec)"
            :title="rollsRender.startAt.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.startAt.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Окончание -->
        <AppLabel
            v-if="rollsRender.finishAt.show"
            :text="rollsRender.finishAt.data(roll_exec)"
            :title="rollsRender.finishAt.title"
            :type="getTypeByStatus(roll_exec)"
            :width="rollsRender.finishAt.width"
            align="center"
            text-size="mini"
        />

        <!-- __ Время стегания -->
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

        <!-- __ Ответственный -->
        <AppSelectSimple
            v-if="rollsRender.finishBy.show"
            :disabled="roll_exec.status === FABRIC_ROLL_STATUS.CREATED.CODE || roll_exec.status === FABRIC_ROLL_STATUS.DONE.CODE"
            :multiple="false"
            :selectData="rollsRender.finishBy.data(roll_exec)"
            :title="rollsRender.finishBy.title"
            :type="getTypeByStatusFinishBy(roll_exec)"
            :width="rollsRender.finishBy.width"
            text-size="mini"
            @change="getSelectedWorker"
        />


        <!-- __ Причина невыполнения -->
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
import { onUnmounted, ref, watch } from 'vue'
import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    FABRIC_ROLL_STATUS,
    // FABRIC_ROLL_STATUS_LIST,
    // FABRIC_TASK_STATUS
} from '@/app/constants/fabrics.js'

import { getDuration } from '@/app/helpers/helpers_date.js'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppSelectSimple from '@/components/ui/selects/AppSelectSimple.vue'
import { getTypeByRollStatus } from '@/app/helpers/manufacture/helpers_fabric.js'

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


const emit = defineEmits(['changeCalcStatus'])

// __ Передаем в родительский компонент событие для изменения статуса калькулятора
const changeCalcStatus = () => emit('changeCalcStatus')

const fabricsStore = useFabricsStore()

// __ Получаем тип раскраски в зависимости от статуса выполнения рулона
const getTypeByStatus = (roll_exec) => {
    return getTypeByRollStatus(roll_exec.status)
}

// __ Получаем тип раскраски в зависимости от наличия ответственного выполнения рулона
const getTypeByStatusFinishBy = (roll_exec) => {
    if (roll_exec.finish_by === 0 && roll_exec.status !== FABRIC_ROLL_STATUS.CREATED.CODE) return 'danger'
    return getTypeByStatus(roll_exec)
}


// __ Добавляем часики выполнения рулона
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
            clearInterval(intervalId)
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
        clearInterval(intervalId)
    }
})

</script>

<style scoped>

</style>
