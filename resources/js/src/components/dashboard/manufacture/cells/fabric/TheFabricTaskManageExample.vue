<template>
    <CellDatesSelect
        @click-apply="getInterval"
    />

    <div class="ml-2 mt-2">


        <div class="sticky top-0 flex pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit">


            <AppLabelMultiline
                :text="['Дата', '']"
                :width="'w-[250px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Статус', '']"
                :width="'w-[200px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Редактировать', '']"
                :width="'w-[150px]'"
                align="center"
                class="border-2 rounded-lg border-yellow-800 cursor-pointer"
                type="warning"
            />

            <AppLabelMultiline
                :text="['Создать', '']"
                :width="'w-[150px]'"
                align="center"
                class="border-2 rounded-lg border-blue-800 cursor-pointer"
                type="info"
            />

            <AppLabelMultiline
                :text="['Удалить', '']"
                :width="'w-[150px]'"
                align="center"
                class="border-2 rounded-lg border-red-800 cursor-pointer"
                type="danger"
            />

        </div>

        <div class="ml-1">

            <div
                v-for="task in tasks" :key="task.id"
                :class="task.active ? 'bg-blue-200 border-2 rounded-lg border-blue-700' : '' "
                class="cursor-pointer max-w-fit"
                @click="setActive(task)"
            >
                <!--                <div class="hover:border-2 hover:rounded-lg hover:border-blue-700 cursor-pointer max-w-fit">-->

                <div class="flex">
                    <AppLabel
                        :text="task.date"
                        :width="'w-[250px]'"
                        align="center"
                        title="Всплывающая подсказка"
                    />

                    <AppLabel
                        :text="task.status"
                        :width="'w-[200px]'"
                        align="center"
                        title="Всплывающая подсказка"
                    />
                </div>

            </div>


        </div>


    </div>


</template>

<script setup>
import {reactive, ref, watch} from 'vue'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
// import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'
import AppCheckboxSimple from '/resources/js/src/components/ui/checkboxes/AppCheckboxSimple.vue'
import AppLabelMultiline from '/resources/js/src/components/ui/labels/AppLabelMultiline.vue'
import CellDatesSelect from '/resources/js/src/components/dashboard/manufacture/cells/components/CellDatesSelect.vue'

// Получаем ширину ячейки в нужном формате
const getMachineColWidthCSS = (colWidth) => `w-[${colWidth}px]`

const machineColWidth = ref(getMachineColWidthCSS(378))         // 378 под стегальную машину (указываем динамический стиль в <style> для загрузки)

const dateColWidth = ref(getMachineColWidthCSS(48))             // под дату
const leftDateColWidth = ref(getMachineColWidthCSS(50))         // под дату слева
const actionsColWidth = ref(getMachineColWidthCSS(150))          // под действия

const fabricColWidth = ref(getMachineColWidthCSS(250))          // название ПС
const amountRollsColWidth = ref(getMachineColWidthCSS(30))      // кол-во в рулонах
const amountMetersColWidth = ref(getMachineColWidthCSS(40))     // кол-во в погонных метрах
const laborsColWidth = ref(getMachineColWidthCSS(30))           // трудозатраты


// Получаем период архива
const getInterval = (interval) => {
    console.log(interval)
}

const tasks = reactive([
    {id: 1, date: '22.03.2025', status: 'Готов к выполнению', active: true},
    {id: 2, date: '23.03.2025', status: 'Создан', active: false,},
    {id: 3, date: '24.03.2025', status: 'Выполнен', active: false,},
    {id: 4, date: '25.03.2025', status: 'В процессе', active: false,},
    {id: 5, date: '26.03.2025', status: 'Готов к выполнению', active: false,},
    {id: 6, date: '27.03.2025', status: 'Готов к выполнению', active: false,},
])

const setActive = (task) => tasks.forEach(({id}, i, arr) => arr[i].active = id === task.id)
// const setActive = (task) => tasks.forEach(t => console.log(t))
// const setActive = (task) => {
//
//     console.log(tasks)
//     tasks.forEach(t => {return t.id === task.id ? task.active = true : task.active = false})
//     console.log(tasks)
//
// }

</script>

<style scoped>

.load-css {

    @apply
    w-[330px]
    w-[20px]
    w-[30px]
    w-[48px]
    w-[51px]
    w-[50px]
    w-[450px]
    w-[500px]
    w-[600px]
    w-[378px]
    w-[80px]
}

</style>
