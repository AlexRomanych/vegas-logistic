<template>

    <div :class="changeRollsOrderFlag ? 'bg-red-100' : 'bg-slate-200'"
         class="border-2 rounded-lg border-slate-400 p-2 w-fit">

        <!-- __ Меню с пунктами действий над рулонами -->
        <div v-if="task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE">
            <TheTaskExecuteControls
                :machine="machine"
                :rolls="rolls"
                @start-execute-roll="startRollExecution"
            />

            <!-- __ Разделительная линия -->
            <TheDividerLine/>
        </div>

        <div v-if="rolls.length">

            <!-- __ Калькулятор параметров -->
            <TheTaskExecuteCalculator
                :machine="machine"
                :rolls="rolls"
                @calculator-action-handler="calculatorActionHandler"
            />

            <!-- __ Разделительная линия -->
            <TheDividerLine/>

            <!-- __ Общий комментарий к сменному заданию показываем, если он есть -->
            <div v-if="taskDescription">

                <!-- __ Заголовок -->
                <div class="ml-2 mt-3 font-semibold text-sm">
                    Комментарий к сменному заданию на этой стегальной машине:
                </div>

                <!-- __ Комментарий (текст) к сменному заданию -->
                <AppLabel
                    :text="taskDescription"
                    class="cursor-pointer"
                    height="min-h-[60px]"
                    text-size="normal"
                    width="w-[955px]"
                />

                <!-- __ Разделительная линия -->
                <TheDividerLine/>

            </div>

            <!-- __ Показываем, если статус "Готов к стежке", "Выполняется" и "Выполнено"-->
            <div v-if="!getFunctionalByFabricTaskStatus(task.common.status)">

                <!--__ Список рулонов -->
                <TheTaskExecuteRolls
                    :machine="machine"
                    :rolls="rolls"
                    :workers="task.workers"
                    @add-execute-roll="addExecuteRoll"
                    @change-calc-status="changeCalcStatus"
                    @save-exec-rolls-order="saveExecRollsOrder"
                />

            </div>

        </div>

    </div>

</template>


<script setup>

import { reactive, ref } from 'vue'


import { useFabricsStore } from '@/stores/FabricsStore.js'

import { FABRIC_MACHINES, NEW_ROLL, FABRIC_TASK_STATUS } from '@/app/constants/fabrics.js'
import {
    // filterFabricsByMachineId,
    // getAddFabricMode,
    getFunctionalByFabricTaskStatus,
} from '@/app/helpers/manufacture/helpers_fabric.js'


import TheTaskExecuteControls
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteControls.vue'
import TheTaskExecuteRolls
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteRolls.vue'
import TheDividerLine
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/TheDividerLine.vue'

// import AppInputTextArea from '@/components/ui/inputs/AppInputTextArea.vue'
// import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppLabel from '@/components/ui/labels/AppLabel.vue'
import TheTaskExecuteCalculator
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteCalculator.vue'
import { storeToRefs } from 'pinia'

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
        validator: (machine) => [
            FABRIC_MACHINES.AMERICAN.ID,
            FABRIC_MACHINES.GERMAN.ID,
            FABRIC_MACHINES.CHINA.ID,
            FABRIC_MACHINES.KOREAN.ID,
        ].includes(machine.ID)
    }
})

// console.log('machine: task: ', props.task)
// console.log('workers: ', props.task.workers)
// console.log('machine', props.machine)
// console.log('machine', FABRIC_MACHINES.AMERICAN)

const emits = defineEmits([
    'addRoll',
    'optimizeLabor',
    'saveTaskRecord',
    'deleteTaskRecord',
    'addExecuteRoll',
    'calculatorActionHandler',
    'changeCalcStatus',
    'saveExecRollsOrder',
])



const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory
fabrics[0].machines[0].id = props.machine.ID                                    // Добавляем ID машины в объект ПС с нулевым рулоном

const rolls = reactive(props.task.machines[props.machine.TITLE].rolls)                    // Получаем рулоны из задания
// const rollsIndexes = computed(() => rolls.map(roll => roll.fabric_id))  // Получаем индексы рулонов, для того, чтобы их потом исключить из выбора ПС в самой записи
// fabricsStore.globalRollsIndexes = rollsIndexes.value                            // сохраняем индексы рулонов в глобальном хранилище


fabricsStore.globalEditMode = false                                             // устанавливаем в false глобальный режим редактирования

// __ Флаг изменения порядка рулонов
const {globalOrderExecuteChangeFlag: changeRollsOrderFlag} = storeToRefs(fabricsStore)

// attract: Заполняем глобальный массив производительности в хранилище
const fillGlobalProductivity = () => {
    fabricsStore.clearTaskGlobalProductivity()
    rolls.forEach((roll, index, rolls) => {
        const fabric = fabrics.find(fabric => fabric.id === roll.fabric_id)
        // console.log(fabricsStore.globalTaskProductivity)
        fabricsStore.globalTaskProductivity[props.machine.TITLE][index] =
            fabric.buffer.productivity ? fabric.buffer.average_length * roll.rolls_amount / fabric.buffer.productivity : 0
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

//__ Удаляем запись
const deleteTaskRecord = (deleteData) => {
    emits('deleteTaskRecord', {...deleteData, machine: props.machine, task: props.task})
}

// __ Всплывающее событие "Добавить рулон" из выполнения СЗ
const addExecuteRoll = async (addingRollData) => {
    console.log('TaskMachine: addingRollData: ', {...addingRollData, taskId: props.task.common.id})
    // const res = await fabricsStore.addExecuteRoll({...addingRollData, taskId: props.task.common.id})
    emits('addExecuteRoll', addingRollData) // Передаем в родительский компонент, что рулон добавлен для обновления списка и перерисовки

}

// hr----------------------------------------------------
// attract: Начинаем выполнение рулона
const startRollExecution = () => {

}


// __ Калькулятор
const calculatorActionHandler = (handler, machine) => {
    emits('calculatorActionHandler', handler, machine)
}

// __ Всплывающее событие "Изменить статус калькулятора"
const changeCalcStatus = (exec_roll, machine) => emits('changeCalcStatus', exec_roll, machine)

// __ Всплывающее событие "Сохранить порядок выполняемых рулонов"
const saveExecRollsOrder = (rollsExec, machine) => emits('saveExecRollsOrder', rollsExec, machine)


</script>

<style scoped>

</style>
