<template>


    <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">

        <!--attract: Меню с кнопками управления записями -->
        <TheTaskRecordsMenu
            :machine="machine"
            :task-status="task.common.status"
            @add-roll="addRoll"
            @optimize-labor="optimizeLabor"
        />

        <div v-if="rolls.length">

            <!--attract: Разделительная линия -->
            <TheDividerLine/>

            <!--attract: Заголовки таблицы для записей с рулонами -->
            <TheTaskRecordsTitle
            />

            <TheTaskRecord
                v-for="(roll, index) in rolls"
                :key="index"
                :index="index"
                :machine="machine"
                :roll="roll"
                :task-status="task.common.status"
                @save-task-record="saveTaskRecord"
                @delete-task-record="deleteTaskRecord"
            />

            <!--attract: Разделительная линия -->
            <TheDividerLine/>

            <!--attract: Общий комментарий к сменному заданию -->
            <AppInputTextArea
                id="comment"
                v-model="taskDescription"
                :disabled="!getFunctionalByFabricTaskStatus(task.common.status)"
                :rows=2
                :value="taskDescription"
                class="cursor-pointer"
                height="min-h-[60px]"
                label="Общий комментарий к СЗ на данной машине:"
                placeholder="Введите комментарий"
                text-size="normal"
                width="w-[955px]"
            />


            <!--attract: Показываем, если статус "Готов к стежке", "Выполняется" и "Выполнено"-->
            <div v-if="!getFunctionalByFabricTaskStatus(task.common.status)">

                <!--attract: Разделительная линия -->
                <TheDividerLine/>

                <!--attract: Список рулонов -->
                <TheTaskRecordRolls
                    :rolls="rolls"
                />


            </div>

        </div>

    </div>

</template>


<script setup>

import {computed, ref, watch} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_MACHINES, NEW_ROLL,} from '/resources/js/src/app/constants/fabrics.js'
import {
    // filterFabricsByMachineId,
    // getAddFabricMode,
    getFunctionalByFabricTaskStatus,
} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import TheTaskRecordsMenu
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecordsMenu.vue'
import TheTaskRecordsTitle
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecordsTitle.vue'
import TheTaskRecord
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecord.vue'
import TheTaskRecordRolls
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecordRolls.vue'
import TheDividerLine
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheDividerLine.vue'

import AppInputTextArea from '/resources/js/src/components/ui/inputs/AppInputTextArea.vue'
// import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'

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
            FABRIC_MACHINES.AMERICAN,
            FABRIC_MACHINES.GERMAN,
            FABRIC_MACHINES.CHINA,
            FABRIC_MACHINES.KOREAN,
        ].includes(machine)
    }
})

// console.log('machine', props.task)

const emits = defineEmits(['addRoll', 'optimizeLabor', 'saveTaskRecord', 'deleteTaskRecord'])

const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory
fabrics[0].machines[0].id = props.machine.ID                                    // Добавляем ID машины в объект ПС с нулевым рулоном

const rolls = props.task.machines[props.machine.TITLE].rolls                    // Получаем рулоны из задания


const rollsIndexes = computed(() => rolls.map(roll => roll.editable ? roll.fabric_id : undefined).filter(roll => roll !== undefined))  // Получаем индексы рулонов, для того, чтобы их потом исключить из выбора ПС в самой записи


fabricsStore.globalRollsIndexes = rollsIndexes.value                            // сохраняем индексы рулонов в глобальном хранилище

// rolls.forEach((roll) => {
//     roll.rolls_exec.forEach((roll_exec) => {
//         console.log(roll_exec)
//     })
//
//     // console.log('rolls', roll)
// })

// console.log('rolls', rolls.rolls_exec)

fabricsStore.globalEditMode = false                                             // устанавливаем в false глобальный режим редактирования

// attract: Заполняем глобальный массив производительности в хранилище
const fillGlobalProductivity = () => {
    fabricsStore.clearTaskGlobalProductivity()
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
    // console.log('NEW_ROLL: ', NEW_ROLL)
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


// attract: При изменении самих данных, пересчитываем производительность
watch(() => props.task, () => {
    fillGlobalProductivity()
}, {deep: true})

</script>

<style scoped>

</style>
