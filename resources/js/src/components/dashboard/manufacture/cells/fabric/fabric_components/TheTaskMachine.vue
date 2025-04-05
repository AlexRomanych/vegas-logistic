<template>

    <div v-if="rolls.length">

        <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">


            <!--attract: Меню с кнопками управления записями -->
            <TheTaskRecordsMenu
                @add-roll="addRoll"
                @optimize-labor="optimizeLabor"
            />

            <!--attract: Разделительная линия -->
            <div class="mt-2 mb-2 bg-slate-400 min-h-[4px] rounded-lg"></div>

            <!--attract: Заголовки таблицы для записей с рулонами -->
            <TheTaskRecordsTitle

            />

            <TheTaskRecord
                v-for="roll in rolls"
                :key="roll.num"
                :roll="roll"
                :select-data="getSelectData(roll)"

            />


        </div>

    </div>

</template>


<script setup>

import {
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
} from '/resources/js/src/app/constants/fabrics.js'

import {filterFabricsByMachineId} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import TheTaskRecordsMenu
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecordsMenu.vue'
import TheTaskRecordsTitle
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecordsTitle.vue'
import TheTaskRecord
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecord.vue'
// import {unref} from "vue";


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

const emits = defineEmits(['addRoll', 'optimizeLabor'])

const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory

// Выбираем те ПС, которые стегаются на этой машине
let filteredFabrics = filterFabricsByMachineId(fabrics, props.machine.ID )
console.log(filteredFabrics)


const rolls = props.task.machines[props.machine.TITLE].rolls
// const rolls = []
console.log('task: ', props.task)
console.log('rolls: ', rolls)

// attract: Определяем объект с данными селекта для ПС в зависимости от выбранного рулона и передаем его в компонент
const getSelectData = (roll) => {
    const data = filteredFabrics.map(fabric => ({
        id: fabric.id,
        name: fabric.display_name,
        selected: fabric.id === roll.fabric_id
    }))
    return {name: 'fabrics', data}
}

// const selectData = {
//     name: 'fabrics',
//     data: [
//         // {id: 1, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
//         // {id: 2, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
//         // {id: 3, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)', selected: true},
//         // {id: 4, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)', disabled: true},
//         // {id: 5, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
//     ]
// }
// const getSelectData = (roll) => filteredFabrics.map(fabric => ({id: fabric.id, name: fabric.display_name, selected: fabric.id === roll.fabric_id}))
// selectData.data = filteredFabrics.map(fabric => ({id: fabric.id, name: fabric.display_name,}))

// Attract: Добавляем новый рулон
const addRoll = () => {
    // props.task.machines[props.machine.TITLE].rolls
    const defaultFabric = filteredFabrics[0]
    const newRoll = {
        average_length: defaultFabric.buffer.average_length,
        roll_id: 0,
        num: 0,
        fabric_id: defaultFabric.id,
        fabric: defaultFabric.display_name,
        rolls_amount: 0,
        length_amount: 0,
        descr: 'Нет описания'
    }

    // Передаем в родительский компонент новый рулон, стегальную машину и само задание как контекст
    emits('addRoll', newRoll, props.machine, props.task)

}

// attract: Оптимизируем трудозатраты
const optimizeLabor = () => {
    emits('optimizeLabor', props.machine, props.task)
}

// attract: Всплывающее событие при выборе ПС для рулона
// const fabricSelect = (fabric, roll) => {
//     console.log(fabric)
//     console.log(roll)
// }

</script>

<style scoped>

</style>
