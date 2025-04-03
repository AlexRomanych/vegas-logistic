<template>

    <div v-if="rolls.length">

        <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">


            <!--attract: Меню с кнопками управления записями -->
            <TheTaskRecordsMenu

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
                :select-data="selectData"
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
import {unref} from "vue";



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

const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory

// Выбираем те ПС, которые стегаются на этой машине
let filteredFabrics = filterFabricsByMachineId(fabrics, props.machine.ID,  false)
console.log(filteredFabrics)


const rolls = props.task.machines[props.machine.TITLE].rolls
// const rolls = []
console.log('task: ', props.task)
console.log('rolls: ', rolls)

// attract: Определяем объект с данными селекта для ПС
const selectData = {
    name: 'fabrics',
    data: [
        // {id: 1, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
        // {id: 2, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
        // {id: 3, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)', selected: true},
        // {id: 4, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)', disabled: true},
        // {id: 5, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
    ]
}


selectData.data = filteredFabrics.map(fabric => ({id: fabric.id, name: fabric.display_name,}))

console.log(selectData)

</script>

<style scoped>

</style>
