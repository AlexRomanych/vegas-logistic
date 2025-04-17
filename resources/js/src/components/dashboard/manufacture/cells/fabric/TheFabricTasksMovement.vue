<template>

    <CellDatesSelect
        @click-apply="getInterval"
    />

    <div class="ml-2 mt-2">

        <div class="sticky top-0 flex pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit">

            <AppLabelMultiline
                :text="['Дата', '']"
                :width="dateColWidth"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Статус', '']"
                :width="statusColWidth"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <router-link :to="{name: 'manufacture.cell.fabrics.task.create'}">
                <AppLabelMultiline
                    :text="['Создать', '']"
                    :width="serviceColWidth"
                    align="center"
                    class="border-2 rounded-lg border-blue-800 cursor-pointer"
                    type="info"
                />
            </router-link>

        </div>

        <div class="ml-1">

            <div
                v-for="task in tasks" :key="task.id"
                class="cursor-pointer max-w-fit"
            >

                <div class="flex">
                    <AppLabel
                        :text="task.date"
                        :type="getStyleTypeByFabricTaskStatusCode(task.status)"
                        :width="dateColWidth"
                        align="center"
                        title="Дата выполнения сменного задания"
                        @click="showTaskCard(task)"
                    />

                    <AppLabel
                        :text="getTitleByFabricTaskStatusCode(task.status)"
                        :type="getStyleTypeByFabricTaskStatusCode(task.status)"
                        :width="statusColWidth"
                        align="center"
                        title="Статус выполнения сменного задания"
                        @click="showTaskCard(task)"
                    />

                    <router-link :to="{name:'manufacture.cell.fabrics.task.edit', params:{id: task.id}}">
                        <AppLabel
                            :title="'Редактировать сменное задание от ' + task.date "
                            :width="actionsColWidth"
                            align="center"
                            text="Ред."
                            type="warning"
                        />
                    </router-link>

                    <AppLabel
                        :title="'Удалить сменное задание от ' + task.date "
                        :width="actionsColWidth"
                        align="center"
                        text="Удалить"
                        type="danger"
                        @click="deleteTask(task)"

                    />

                </div>

            </div>

        </div>

    </div>

</template>

<script setup>
import {reactive, ref} from 'vue'

// import {FABRIC_TASK_STATUS} from '/resources/js/src/app/constants/fabrics.js'
import {
    getStyleTypeByFabricTaskStatusCode,
    getTitleByFabricTaskStatusCode
} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiline from '/resources/js/src/components/ui/labels/AppLabelMultiline.vue'
import CellDatesSelect from '/resources/js/src/components/dashboard/manufacture/cells/components/CellDatesSelect.vue'
// import AppCheckboxSimple from '/resources/js/src/components/ui/checkboxes/AppCheckboxSimple.vue'
// import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'

// Получаем ширину ячейки в нужном формате
const getMachineColWidthCSS = (colWidth) => `w-[${colWidth}px]`

const dateColWidth = ref(getMachineColWidthCSS(250))        // под дату
const statusColWidth = ref(getMachineColWidthCSS(200))      // под статус
const serviceColWidth = ref(getMachineColWidthCSS(200))     // под сервисную инфу
const actionsColWidth = ref(getMachineColWidthCSS(100))     // под действия

/*
const machineColWidth = ref(getMachineColWidthCSS(378))         // 378 под стегальную машину (указываем динамический стиль в <style> для загрузки)
const leftDateColWidth = ref(getMachineColWidthCSS(50))         // под дату слева
const fabricColWidth = ref(getMachineColWidthCSS(250))          // название ПС
const amountRollsColWidth = ref(getMachineColWidthCSS(30))      // кол-во в рулонах
const amountMetersColWidth = ref(getMachineColWidthCSS(40))     // кол-во в погонных метрах
const laborsColWidth = ref(getMachineColWidthCSS(30))           // трудозатраты
*/

// Получаем период архива
const getInterval = (interval) => {
    console.log(interval)
}

const tasks = reactive([
    {id: 1, date: '22.03.2025', status: 1},
    {id: 2, date: '23.03.2025', status: 2},
    {id: 3, date: '24.03.2025', status: 3},
    {id: 4, date: '25.03.2025', status: 4},
    {id: 5, date: '26.03.2025', status: 3},
    {id: 6, date: '27.03.2025', status: 2},
    {id: 7, date: '28.03.2025', status: 1},
])

// attract Выводим карточку сменного задания
const showTaskCard = (task) => console.log('TODO show task card', task)   // TODO: Сделать отображение СЗ в виде карточки

// attract Удаление СЗ
const deleteTask = (task) => console.log('Delete', task)
// const setActive = (task) => tasks.forEach(({id}, i, arr) => arr[i].active = id === task.id)
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
/*
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
*/


</style>
