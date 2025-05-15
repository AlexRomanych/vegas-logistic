<template>

    <div class="ml-2 mt-2">

        <div class="sticky top-0 flex pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit">

            <!-- attract: Дата -->
            <AppLabel
                :width="dateColWidth"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Дата"
                type="primary"
            />

            <!-- attract: СМ -->
            <div v-for="machine in machines" class="flex">

                <AppLabel
                    :text="machine.name"
                    :title="machine.name"
                    :width="machineColWidth"
                    align="center"
                    class="border-2 rounded-lg border-blue-700"
                    type="primary"
                />

            </div>

            <!-- attract: Модальное окно для подтверждений -->
            <AppModalAsyncMultiLine
                ref="appModalAsync"
                :text="modalText"
                :type="modalType"
                mode="confirm"
            />

            <!-- attract: Actions -->
            <AppLabel
                :type="calendarChangeFlag ? 'danger' : 'primary'"
                :width="actionsColWidth"
                align="center"
                class="border-2 rounded-lg border-blue-700 cursor-pointer"
                text="Сохранить"
                @click="saveCalendar()"
            />

        </div>


        <div v-for="task in tasksData">


            <div class="flex border-2 border-slate-400 bg-slate-200 rounded-lg p-1 mb-1 max-w-fit">
                <!-- Контейнер под горизонтальный блок -->

                <div :class="[leftDateColWidth, getDayOfWeekStyle(task.date)]"
                     class="text-sm flex items-center justify-center bg-slate-300 rounded-lg border-2 border-slate-400 font-semibold">
                    <!-- Дата -->

                    <div class="text-center">

                        <div class="">
                            {{ task.date.slice(8) + '.' }}
                        </div>

                        <div class="">
                            {{ task.date.slice(5, 7) + '.' }}
                        </div>

                        <div class="">
                            {{ task.date.slice(2, 4) }}
                        </div>

                        <div class="">
                            {{ '(' + getDayOfWeek(task.date) + '.)' }}
                        </div>

                    </div>


                </div>

                <!-- attract: Контейнер под горизонтальный блок -->
                <div v-for="machine in machines">

                    <!-- attract: Droppable Zone -->
                    <div
                        class="ml-1 border-2 border-slate-400 bg-slate-300 rounded-lg p-1 flex flex-col justify-between h-full"
                        @drop="onDrop($event, task, machine)"
                        @dragover.prevent
                        @dragenter.prevent
                    >

                        <!-- attract: Верхний блок с ПС -->
                        <div>

                            <div v-for="roll in task.machines[machine.index].rolls">

                                <!-- attract: Контейнер под ПС + кол-во в рулонах + кол-во в погонных метрах + трудозатраты -->
                                <!-- attract: Draggable Element-->
                                <div class="flex cursor-pointer"
                                     draggable="true"
                                     @dragstart="onDragStart($event, task, machine, roll)"
                                >

                                    <!-- attract: Название ПС -->
                                    <AppLabel
                                        :text="roll.fabric"
                                        :title="roll.fabric"
                                        :type="getTypeByTaskStatus(task.common.status)"
                                        :width="fabricColWidth"
                                        align="left"
                                        class="cursor-pointer"
                                        textSize="micro"
                                    />

                                    <!-- attract: Количество в рулонах -->
                                    <AppLabel
                                        :text="roll.rolls_amount.toString()"
                                        :type="getTypeByTaskStatus(task.common.status)"
                                        :width="amountRollsColWidth"
                                        align="center"
                                        textSize="micro"
                                        title="Количество в рулонах"
                                    />

                                    <!-- attract: Количество в погонных метрах -->
                                    <AppLabel
                                        :text="(roll.length_amount / roll.rate).toFixed(1)"
                                        :type="getTypeByTaskStatus(task.common.status)"
                                        :width="amountMetersColWidth"
                                        align="center"
                                        textSize="micro"
                                        title="Количество в погонных метрах"
                                    />

                                    <!-- attract: Трудозатраты -->
                                    <AppLabel
                                        :text="getProductivity(roll)"
                                        :type="getTypeByTaskStatus(task.common.status)"
                                        :width="laborsColWidth"
                                        align="center"
                                        textSize="micro"
                                        title="Трудозатраты, ч"
                                    />

                                </div>

                            </div>

                        </div>

                        <!-- attract: Нижний блок с итого и чертой -->
                        <div>

                            <!-- attract: Горизонтальная черта -->
                            <div
                                class="w-full h-[3px] border-b-2 border-slate-500 justify-self-center ml-0.5 mb-0.5">
                            </div>

                            <!-- attract: 'Итого' + кол-во в рулонах + кол-во в погонных метрах + трудозатраты -->
                            <div class="flex">

                                <!-- attract: 'Итого:' -->
                                <AppLabel
                                    :type="getOverflowProductivityType(task.machines[machine.index].rolls)"
                                    :width="fabricColWidth"
                                    align="right"
                                    class="cursor-pointer"
                                    text="Итого:"
                                    textSize="micro"
                                    title="Ткань + всякая нужна информация"
                                />

                                <!-- attract: Общие количество в рулонах -->
                                <AppLabel
                                    :text="task.machines[machine.index].rolls.reduce((acc, roll) => acc + roll.rolls_amount, 0).toString()"
                                    :type="getOverflowProductivityType(task.machines[machine.index].rolls)"
                                    :width="amountRollsColWidth"
                                    align="center"
                                    textSize="micro"
                                    title="Количество в рулонах"
                                />

                                <!-- attract: Общие количество в погонных метрах -->
                                <AppLabel
                                    :text="task.machines[machine.index].rolls.reduce((acc, roll) => acc + roll.length_amount / roll.rate, 0).toFixed(1)"
                                    :type="getOverflowProductivityType(task.machines[machine.index].rolls)"
                                    :width="amountMetersColWidth"
                                    align="center"
                                    textSize="micro"
                                    title="Количество в погонных метрах"
                                />

                                <!-- attract: Общие трудозатраты -->
                                <AppLabel
                                    :text="getProductivityTotal(task.machines[machine.index].rolls)"
                                    :type="getOverflowProductivityType(task.machines[machine.index].rolls)"
                                    :width="laborsColWidth"
                                    align="center"
                                    textSize="micro"
                                    title="Трудозатраты, ч"
                                />
                            </div>

                        </div>

                    </div>

                </div>

                <!-- attract: Сервис -->
                <div :class="actionsColWidth"
                     class="ml-1 mr-0.5 flex items-start justify-center bg-slate-300 rounded-lg border-2 border-slate-400 font-semibold">

                    <div class="text-center w-full">

                        <!-- attract: Инфо -->
                        <!-- передаем width="", чтобы ширина наследовалась -->
                        <AppLabel
                            align="center"
                            text="Инфо по СЗ:"
                            textSize="mini"
                            title="Всплывающая подсказка"
                            type="success"
                            width=""
                        />

                        <!-- attract: Статус -->
                        <AppLabel
                            :text="'Статус: ' + getFabricTaskStatusByCode(task.common.status).TITLE"
                            :type="getFabricTaskStatusByCode(task.common.status).TYPE"
                            align="center"
                            textSize="mini"
                            title="Разный статус - разными цветами"
                            width=""
                        />

                        <!-- attract: Трудозатраты -->
                        <AppLabel
                            align="center"
                            :text="'Всего: ' + getProductivityTotalTask(task)"
                            textSize="mini"
                            title="Всплывающая подсказка"
                            type="info"
                            width=""
                        />

                        <!-- attract: Переход к управлению -->
                        <router-link :to="{name: 'manufacture.cell.fabric.tasks.manage'}">
                            <AppLabel
                                align="center"
                                class="underline"
                                text="К управлению СЗ"
                                textSize="mini"
                                title="Всплывающая подсказка"
                                type="warning"
                                width=""
                            />
                        </router-link>

<!--                        <AppLabel-->
<!--                            align="center"-->
<!--                            text="Закрыть"-->
<!--                            textSize="mini"-->
<!--                            title="Всплывающая подсказка"-->
<!--                            type="danger"-->
<!--                            width=""-->
<!--                        />-->

<!--                        <AppLabel-->
<!--                            align="center"-->
<!--                            text="Детали"-->
<!--                            textSize="mini"-->
<!--                            title="Всплывающая подсказка"-->
<!--                            width=""-->
<!--                        />-->

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- attract: Модальное окно для подтверждений -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="confirm"
    />

    <!-- attract: Callout -->
    <AppCallout
        :show="calloutShow"
        :text="calloutText"
        :type="calloutType"
    />


</template>

<script setup>

import {reactive, ref, watch} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {
    FABRIC_ROLL_STATUS, FABRIC_TASK_STATUS,
    FABRIC_WORKING_SHIFT_LENGTH,
    FABRICS_NULLABLE
} from '/resources/js/src/app/constants/fabrics.js'

import {
    formatTimeWithLeadingZeros,
    getDayOfWeek,
    getDayOfWeekStyle
} from '/resources/js/src/app/helpers/helpers_date.js'

import {
    addEmptyFabricTasks,
    getMachines,
    getFabricTasksPeriod,
    getTitleByFabricTaskStatusCode,
    getStyleTypeByFabricTaskStatusCode,
    fillFabricsDisplayNames,
    getTypeByTaskStatus,
    getProductivity,
    getProductivityTotal,
    getFabricTaskStatusByCode,
    getProductivityTotalTask
} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppCallout from '/resources/js/src/components/ui/callouts/AppCallout.vue'
import AppModalAsyncMultiLine from '/resources/js/src/components/ui/modals/AppModalAsyncMultiline.vue'


const fabricsStore = useFabricsStore()

// attract: Флаг состояния изменений данных
const calendarChangeFlag = ref(false)

// attract: Получаем все ткани и запоминаем в хранилище
const getFabrics = async () => {
    const fabrics = await fabricsStore.getFabrics(true)
    fabrics.unshift(FABRICS_NULLABLE)                   // добавляем пустой элемент в начало массива
    fabricsStore.fabricsMemory = fabrics
    return fabrics
}
const fabrics = ref(await getFabrics())

// attract: Получаем период отображения сменного задания
const tasksPeriod = getFabricTasksPeriod()

// attract: Получаем сами сменные задания
const getFabricTasks = async (tasksPeriod) => {
    fabricsStore.globalCalendarChangeFlag = false           // attract: Сбрасываем флаг изменений
    return await fabricsStore.getTasksByPeriod(tasksPeriod)
}
const tasks = ref(await getFabricTasks(tasksPeriod))

// attract: формируем полный (дополненный) массив сменных заданий
const tasksData = reactive(addEmptyFabricTasks(tasks.value, tasksPeriod))

// attract: Получаем все машины для отображения и формируем объект для отображения
const machines = ref(getMachines())


console.log('tasksPeriod:', tasksPeriod)
console.log('fabrics: ', fabrics.value)
console.log('tasks: ', tasks.value)
console.log('tasksData: ', tasksData)
console.log('machines: ', machines.value)


// attract: Callout для вывода ошибок и предупреждений
const calloutType = ref('danger')
const calloutText = ref('')
const calloutShow = ref(false)
const calloutClose = (delay = 5000) => setTimeout(() => calloutShow.value = false, delay) // закрываем callout


// attract: Получаем ссылку на модальное для подтверждений окно с асинхронной функцией
const appModalAsync = ref(null)
const modalText = ref([])
const modalType = ref('danger')


// attract: Считаем общие трудозатраты по всем рулонам и определяем стиль для вывода
const getProductivityTotalHours = (rolls) => {
    if (rolls.lenght === 0) return 0
    return rolls.reduce((acc, roll) => acc + (roll.length_amount / roll.rate / roll.productivity), 0)
}

const getOverflowProductivityType = (rolls) => {
    const totalHours = getProductivityTotalHours(rolls)
    return totalHours > FABRIC_WORKING_SHIFT_LENGTH ? 'danger' : 'success'
}


// attract: API Drug & Drop

// attract: Берем ПС
const onDragStart = (event, task, machine, roll) => {
    event.dataTransfer.dropEffect = 'move'
    event.dataTransfer.effectAllowed = 'move'

    const transferObject = {
        taskDate: task.date,
        machineIndex: machine.index,
        machineId: machine.id,
        rollId: roll.id,
    }

    event.dataTransfer.setData('transferObject', JSON.stringify(transferObject))
}

// attract: Бросаем ПС
const onDrop = (event, task, machine) => {

    // Получаем объект передачи
    const transferObject = JSON.parse(event.dataTransfer.getData('transferObject'))

    // Находим СЗ откуда перемещаем
    const targetTask = tasksData.find(t => t.date === transferObject.taskDate)

    // Если перемещаем в ту же самую ячейку - не делаем ничего, чтобы не схлопывались данные
    // if (targetTask === task) return

    const allowedStatuses = [FABRIC_TASK_STATUS.UNKNOWN.CODE, FABRIC_TASK_STATUS.CREATED.CODE]

    if (!(allowedStatuses.includes(targetTask.common.status) && allowedStatuses.includes(task.common.status))) {

        calloutType.value = 'danger'
        calloutText.value = 'Невозможно переместить ПС. Статус СЗ должен быть "Не создано" или "Создано"'
        calloutShow.value = true
        calloutClose()
        return
    }

    // Находим рулон, который перемещаем
    const targetRoll = targetTask.machines[transferObject.machineIndex].rolls.find(r => r.id === transferObject.rollId)

    const fabric = fabrics.value.find(f => f.id === targetRoll.fabric_id)
    console.log('fabric: ', fabric)

    // Проверяем, можно ли переместить ПС на выбранную СМ
    const canMoveRoll = fabric.machines.some(m => m.id === machine.id)

    // console.log('machine.machineId: ', machine.id)
    // console.log('fabric machines: ', fabric.machines)
    // console.log('canMoveRoll: ', canMoveRoll)


    if (!canMoveRoll) {
        calloutType.value = 'danger'
        calloutText.value = 'Невозможно переместить ПС. Статус СЗ должен быть "Не создано" или "Создано"'
        calloutText.value = `${targetRoll.fabric} не может быть простегана на машине ${machine.name}`
        calloutShow.value = true
        calloutClose()
        return
    }

    // warning: Важен порядок операций, чтобы не схлопывались данные
    targetTask.machines[transferObject.machineIndex].rolls =
        targetTask.machines[transferObject.machineIndex].rolls.filter(r => r.id !== transferObject.rollId)
    // Добавляем рулон, который перемещаем (пока без учета позиции, пихаем в конец)
    task.machines[machine.index].rolls.push(targetRoll)
    // hr------------------------------------------------

    // console.log('onDrop:', transferObject)

    calendarChangeFlag.value = true          // устанавливаем флаг изменения данных
}


// attract: Сохраняем изменения
const saveCalendar = async () => {

    modalText.value = ['Данные будут сохранены.', 'Продолжить?']
    modalType.value = 'danger'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        calendarChangeFlag.value = false
    }

}


// Получаем ширину ячейки в нужном формате
const getMachineColWidthCSS = (colWidth) => `w-[${colWidth}px]`

const machineColWidth = ref(getMachineColWidthCSS(378))         // 378 под стегальную машину (указываем динамический стиль в <style> для загрузки)

const dateColWidth = ref(getMachineColWidthCSS(48))             // под дату
const leftDateColWidth = ref(getMachineColWidthCSS(50))         // под дату слева
const actionsColWidth = ref(getMachineColWidthCSS(150))          // под действия

const fabricColWidth = ref(getMachineColWidthCSS(200))          // название ПС
const amountRollsColWidth = ref(getMachineColWidthCSS(30))      // кол-во в рулонах
const amountMetersColWidth = ref(getMachineColWidthCSS(40))     // кол-во в погонных метрах
const laborsColWidth = ref(getMachineColWidthCSS(80))           // трудозатраты


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


.success {
    @apply bg-green-500 border-green-800 text-black
}

.danger {
    @apply bg-red-500 border-red-800 text-white
}

.dark {
    @apply bg-slate-500 border-slate-800 text-white
}

.primary {
    @apply bg-blue-500 border-blue-800 text-white
}

.warning {
    @apply bg-yellow-500 border-yellow-800 text-black
}


</style>
