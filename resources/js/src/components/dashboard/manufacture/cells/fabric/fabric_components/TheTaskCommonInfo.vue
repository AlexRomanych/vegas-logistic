<template>
    <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">

        <!-- attract: Кем и когда создано задание -->
        <div class="mb-2">


            <!--  attract: descr: Группа: Сменное задание-->
            <div>
                <AppLabel
                    text="Сменное задание:"
                    type="success"
                />
            </div>

            <div class="flex">

                <!-- attract: Статус -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Статус:"
                        text-size="mini"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="getTitleByFabricTaskStatusCode(task.common.status)"
                        :type="getStyleTypeByFabricTaskStatusCode(task.common.status)"
                        text-size="mini"
                        type="warning"
                        width="w-[200px]"
                    />
                </div>

                <!-- attract: Ответственный -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Специалист:"
                        text-size="mini"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="task.common.created_by"
                        text-size="mini"
                        width="w-[200px]"
                    />
                </div>

            </div>


            <div class="flex">

                <!-- attract: Дата создания -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Дата создания СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatDateInFullFormat(task.common.created_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

                <!-- attract: Время создания -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Время создания СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatTimeInFullFormat(task.common.created_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

            </div>

            <!-- attract: Общий комментарий к дню -->
            <div v-if="task.common.description" class="flex items-start ml-3">
                <AppLabel
                    text="Общий комментарий:"
                    text-size="mini"
                    type="warning"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="task.common.description"
                    text-size="mini"
                    type="warning"
                    width="w-[570px]"
                />
            </div>

            <!-- attract: Если статус СЗ - выполнено или в процессе выполнения, то показываем Дату и Время старта СЗ -->
            <div
                v-if="task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE || task.common.status === FABRIC_TASK_STATUS.DONE.CODE"
                class="flex">

                <!-- attract: Дата начала выполнения -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Дата старта СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatDateInFullFormat(task.common.start_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

                <!-- attract: Время начала выполнения -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Время старта СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatTimeInFullFormat(task.common.start_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

            </div>

            <!-- attract: Если статус СЗ - выполнено, то показываем Дату и Время окончания СЗ -->
            <div
                v-if="task.common.status === FABRIC_TASK_STATUS.DONE.CODE"
                class="flex">

                <!-- attract: Дата окончания выполнения -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Дата окончания СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatDateInFullFormat(task.common.finish_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

                <!-- attract: Время окончания выполнения -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Время окончания СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatTimeInFullFormat(task.common.finish_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

            </div>

            <!-- attract: Если статус СЗ - выполнено или в процессе выполнения, то показываем длительность СЗ -->
            <div
                v-if="task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE || task.common.status === FABRIC_TASK_STATUS.DONE.CODE"
                class="flex">

                <!-- attract: Длительность выполнения СЗ-->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Длительность СЗ:"
                        text-size="mini"
                        type="warning"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="duration"
                        text-size="mini"
                        type="warning"
                        width="w-[200px]"
                    />
                </div>

            </div>

        </div>


        <TheDividerLine/>

        <!-- attract: Бригада -->
        <div class="mb-4">

            <div class="flex items-start">
                <AppLabel
                    text="Бригада:"
                    type="success"
                    width="w-[100px]"
                />
                <AppLabel
                    :text="'№ ' + task.common.team.toString()"
                    type="success"
                    width="w-[50px]"
                />
            </div>

            <div class="ml-3">
                <AppLabel
                    text="Иванов И. И."
                    text-size="mini"
                    width="w-[200px]"
                />
                <AppLabel
                    text="Петров П. П."
                    text-size="mini"
                    width="w-[200px]"
                />
                <AppLabel
                    text="Сдоров С. С."
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

        </div>

        <!-- attract: Разделительная линия -->
        <TheDividerLine/>

        <!-- attract: Трудозатраты -->
        <!-- attract: Показываем, если есть -->
        <div v-if="getTotalProductivity()" class="mb-2">
            <div>
                <AppLabel
                    text="Трудозатраты:"
                    type="success"
                />
            </div>

            <!-- attract: Трудозатраты на американце -->
            <div v-if="getProductivityAmerican()" class="flex items-start ml-3">
                <AppLabel
                    text="Американец:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="formatTimeWithLeadingZeros(getProductivityAmerican(), 'hour')"
                    :type="getStyleTypeByProductivity(getProductivityAmerican())"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <!-- attract: Трудозатраты на немце -->
            <div v-if="getProductivityGerman()" class="flex items-start ml-3">
                <AppLabel
                    text="Немец:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="formatTimeWithLeadingZeros(getProductivityGerman(), 'hour')"
                    :type="getStyleTypeByProductivity(getProductivityGerman())"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <!-- attract: Трудозатраты на китайце -->
            <div v-if="getProductivityChina()" class="flex items-start ml-3">
                <AppLabel
                    text="Китаец:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="formatTimeWithLeadingZeros(getProductivityChina(), 'hour')"
                    :type="getStyleTypeByProductivity(getProductivityChina())"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <!-- attract: Трудозатраты на корейце -->
            <div v-if="getProductivityKorean()" class="flex items-start ml-3">
                <AppLabel
                    text="Кореец:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="formatTimeWithLeadingZeros(getProductivityKorean(), 'hour')"
                    :type="getStyleTypeByProductivity(getProductivityKorean())"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <!-- attract: Разделительная линия -->
            <TheDividerLine/>

            <!-- attract: Общие трудозатраты  -->
            <div class="flex items-start ml-3">
                <AppLabel
                    text="Всего:"
                    text-size="normal"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="formatTimeWithLeadingZeros(getTotalProductivity(), 'hour')"
                    text-size="mini"
                    type="primary"
                    width="w-[200px]"
                />
            </div>

        </div>

    </div>

</template>

<script setup>

import {onMounted, onUnmounted, ref, watch} from 'vue'

import {
    FABRIC_WORKING_SHIFT_LENGTH,
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
    FABRICS_NULLABLE,
    FABRIC_TASKS_EXECUTE,
} from '/resources/js/src/app/constants/fabrics.js'

import {
    getTitleByFabricTaskStatusCode,
    getStyleTypeByFabricTaskStatusCode,
} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import {
    formatTimeWithLeadingZeros,
    formatDateInFullFormat,
    formatTimeInFullFormat
} from '/resources/js/src/app/helpers/helpers_date.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import TheDividerLine
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheDividerLine.vue'


const props = defineProps({
    task: {
        type: Object,
        required: false,
        default: () => ({})
    },
})

// console.log('task', props.task)

// attract: Получаем длительность СЗ
const getTaskDuration = (task) => {

    let tempDuration

    if (task.common.finish_at) {
        tempDuration = new Date(task.common.finish_at).getTime() - new Date(task.common.start_at).getTime()
    } else {
        tempDuration = new Date().getTime() - new Date(task.common.start_at).getTime()
    }

    tempDuration = Math.floor(tempDuration / 1000)
    return formatTimeWithLeadingZeros(tempDuration)
}
// attract: Длительность СЗ
const duration = ref(getTaskDuration(props.task))


// attract: Трудозатраты на американце
const getProductivityAmerican = () => props.task.machines.american.rolls.reduce((acc, roll) => {
    return acc + roll.average_textile_length * roll.rolls_amount / (roll.productivity)
    // return acc + roll.average_textile_length * roll.rolls_amount / (roll.productivity * roll.fabric_rate)
}, 0)

// attract: Трудозатраты на немце
const getProductivityGerman = () => props.task.machines.german.rolls.reduce((acc, roll) => {
    return acc + roll.average_textile_length * roll.rolls_amount / (roll.productivity)
    // return acc + roll.average_textile_length * roll.rolls_amount / (roll.productivity * roll.fabric_rate)
}, 0)

// attract: Трудозатраты на китайце
const getProductivityChina = () => props.task.machines.china.rolls.reduce((acc, roll) => {
    return acc + roll.average_textile_length * roll.rolls_amount / (roll.productivity)
    // return acc + roll.average_textile_length * roll.rolls_amount / (roll.productivity * roll.fabric_rate)
}, 0)

// attract: Трудозатраты на корейце
const getProductivityKorean = () => props.task.machines.korean.rolls.reduce((acc, roll) => {
    return acc + roll.average_textile_length * roll.rolls_amount / (roll.productivity)
    // return acc + roll.average_textile_length * roll.rolls_amount / (roll.productivity * roll.fabric_rate)
}, 0)

// attract: Общие трудозатраты: Американец + Немец + Китаец + Кореец
const getTotalProductivity = () => getProductivityAmerican() + getProductivityGerman() + getProductivityChina() + getProductivityKorean()

// attract: Получаем тип стиля по продолжительности трудозатрат
const getStyleTypeByProductivity = (productivity) => productivity <= FABRIC_WORKING_SHIFT_LENGTH ? 'success' : 'danger'

let intervalId = null   // Для очистки интервала, если он будет создан, чтобы убрать утечки памяти

// attract: Отслеживаем изменения задачи и вычисляем продолжительность СЗ
watch(() => props.task, (newTask) => {

    duration.value = getTaskDuration(newTask)

    if (newTask.common.status === FABRIC_TASK_STATUS.RUNNING.CODE) {

        if (!intervalId) {

            intervalId = setInterval(() => {
                duration.value = getTaskDuration(newTask)
            }, 1000)
        }
    } else if (newTask.common.status === FABRIC_TASK_STATUS.DONE.CODE) {
        if (intervalId !== null) {
            clearInterval(intervalId);
            duration.value = getTaskDuration(newTask)
        }
    }


}, {
    deep: true,
    immediate: true,
})

// onMounted(() => {
//     const interval = setInterval(() => duration.value = getTaskDuration(props.task), 1000)
//     console.log('interval', interval)
// })

onUnmounted(() => {
    if (intervalId !== null) {
        clearInterval(intervalId);
    }
});

</script>

<style scoped>

</style>
