<template>


    <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">

        <div v-if="task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE">
            <TheTaskExecuteControls
                :machine="machine"
                :rolls="rolls"
                @start-execute-roll="startRollExecution"

            />

            <!--attract: Разделительная линия -->
            <TheDividerLine/>

        </div>

        <div v-if="rolls.length">

            <!-- attract: Общий комментарий к сменному заданию показываем, если он есть -->
            <div v-if="taskDescription">

                <!--attract: Общий комментарий к сменному заданию -->
                <div class="ml-2 mt-3 font-semibold text-sm">
                    Комментарий к сменному заданию на этой стегальной машине:
                </div>

                <AppLabel
                    :text="taskDescription"
                    class="cursor-pointer"
                    height="min-h-[60px]"
                    text-size="normal"
                    width="w-[955px]"
                />

                <!--attract: Разделительная линия -->
                <TheDividerLine/>

            </div>

            <!--attract: Показываем, если статус "Готов к стежке", "Выполняется" и "Выполнено"-->
            <div v-if="!getFunctionalByFabricTaskStatus(task.common.status)">

                <!--attract: Список рулонов -->
                <TheTaskExecuteRolls
                    :machine="machine"
                    :rolls="rolls"
                    :workers="task.workers"
                />

            </div>

        </div>

    </div>

</template>


<script setup>

import {computed, ref} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_MACHINES, NEW_ROLL, FABRIC_TASK_STATUS} from '/resources/js/src/app/constants/fabrics.js'
import {
    // filterFabricsByMachineId,
    // getAddFabricMode,
    getFunctionalByFabricTaskStatus,
} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'


import TheTaskExecuteControls
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteControls.vue'
import TheTaskExecuteRolls
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteRolls.vue'
import TheDividerLine
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheDividerLine.vue'

import AppInputTextArea from '/resources/js/src/components/ui/inputs/AppInputTextArea.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'

const props = defineProps({
    task: {
        type: Object,
        required: false,
        default: () => ({})
    },
    machine: {
        type: Object,
        required: false,
        default: () => FABRIC_MACHINES.AMERICAN,
        // validator: (machine) => [
        //     FABRIC_MACHINES.AMERICAN,
        //     FABRIC_MACHINES.GERMAN,
        //     FABRIC_MACHINES.CHINA,
        //     FABRIC_MACHINES.KOREAN,
        // ].includes(machine)
    }
})


// console.log('workers: ', props.task.workers)
// console.log('machine', props.machine)
// console.log('machine', FABRIC_MACHINES.AMERICAN)

const emits = defineEmits(['addRoll', 'optimizeLabor', 'saveTaskRecord', 'deleteTaskRecord'])

const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory
fabrics[0].machines[0].id = props.machine.ID                                    // Добавляем ID машины в объект ПС с нулевым рулоном

const rolls = props.task.machines[props.machine.TITLE].rolls                    // Получаем рулоны из задания
const rollsIndexes = computed(() => rolls.map(roll => roll.fabric_id))  // Получаем индексы рулонов, для того, чтобы их потом исключить из выбора ПС в самой записи
fabricsStore.globalRollsIndexes = rollsIndexes.value                            // сохраняем индексы рулонов в глобальном хранилище


fabricsStore.globalEditMode = false                                             // устанавливаем в false глобальный режим редактирования

// attract: Заполняем глобальный массив производительности в хранилище
const fillGlobalProductivity = () => {
    rolls.forEach((roll, index, rolls) => {
        const fabric = fabrics.find(fabric => fabric.id === roll.fabric_id)
        // console.log(fabricsStore.globalTaskProductivity)
        fabricsStore.globalTaskProductivity[props.machine.TITLE][index] =
            fabric.buffer.productivity ? fabric.buffer.average_length * roll.rolls_amount : 0
        // console.log(fabric)
    })
}
fillGlobalProductivity()

// attract: Общий комментарий к сменному заданию
const taskDescription = ref(props.task.machines[props.machine.TITLE].description)

// Attract: Добавляем новый рулон
const addRoll = () => {
// Передаем в родительский компонент новый рулон, стегальную машину и само задание как контекст
    emits('addRoll', NEW_ROLL, props.machine, props.task)
}

// attract: Оптимизируем трудозатраты
const optimizeLabor = () => {
    emits('optimizeLabor', props.machine, props.task)
}

// attract: Сохраняем запись
const saveTaskRecord = (saveData) => {
    // console.log(saveData)
    emits('saveTaskRecord',
        {
            ...saveData,
            machine: props.machine,
            task: props.task,
            taskDescription: taskDescription.value
        })
}

// attract: Удаляем запись
const deleteTaskRecord = (deleteData) => {
    emits('deleteTaskRecord', {...deleteData, machine: props.machine, task: props.task})
}


// hr----------------------------------------------------
// attract: Начинаем выполнение рулона
const startRollExecution = () => {

}

// consfinishRollExecution


</script>

<style scoped>

</style>
