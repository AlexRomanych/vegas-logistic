<template>


    <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">

        <!--attract: Меню с кнопками управления записями -->
        <TheTaskRecordsMenu
            :machine="machine"
            @add-roll="addRoll"
            @optimize-labor="optimizeLabor"
        />

        <div v-if="rolls.length">

            <!--attract: Разделительная линия -->
            <div class="mt-2 mb-2 bg-slate-400 min-h-[4px] rounded-lg"></div>

            <!--attract: Заголовки таблицы для записей с рулонами -->
            <TheTaskRecordsTitle
            />

            <TheTaskRecord
                v-for="(roll, index) in rolls"
                :key="index"
                :index="index"
                :machine="machine"
                :roll="roll"
                @save-task-record="saveTaskRecord"
            />

            <!--attract: Разделительная линия -->
            <div class="mt-2 mb-2 bg-slate-400 min-h-[4px] rounded-lg"></div>

            <AppInputTextArea
                id="comment"
                v-model="taskDescription"
                :rows=2
                :value="taskDescription"
                class="cursor-pointer"
                height="min-h-[60px]"
                label="Общий комментарий к сменному заданию:"
                text-size="normal"
                width="w-[955px]"
            />

        </div>

    </div>

</template>


<script setup>

import {computed, ref} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_MACHINES, NEW_ROLL,} from '/resources/js/src/app/constants/fabrics.js'

import TheTaskRecordsMenu
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecordsMenu.vue'
import TheTaskRecordsTitle
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecordsTitle.vue'
import TheTaskRecord
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecord.vue'


import AppInputTextArea from '/resources/js/src/components/ui/inputs/AppInputTextArea.vue'

const props = defineProps({
    task: {
        type: Object,
        required: false,
        default: {}
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

// console.log(props.task)

const emits = defineEmits(['addRoll', 'optimizeLabor', 'saveTaskRecord'])

const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory
fabrics[0].machines[0].id = props.machine.ID                                    // Добавляем ID машины в объект ПС с нулевым рулоном

const rolls = props.task.machines[props.machine.TITLE].rolls                    // Получаем рулоны из задания
const rollsIndexes = computed(() => rolls.map(roll => roll.fabric_id))  // Получаем индексы рулонов, для того, чтобы их потом исключить из выбора ПС в самой записи
fabricsStore.globalRollsIndexes = rollsIndexes.value                            // сохраняем индексы рулонов в глобальном хранилище

fabricsStore.globalEditMode = false                                             // устанавливаем в false глобальный режим редактирования

// Заполняем глобальный массив производительности в хранилище
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

const taskDescription = ref()

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
    console.log('machine')

    emits('saveTaskRecord', {...saveData, machine: props.machine, task: props.task})
}
</script>

<style scoped>

</style>
