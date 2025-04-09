<template>

    <div class="mt-2 ml-2">


        <!-- attract: Выводим даты + статусы + сервисные кнопки   -->
        <div class="flex h-[165px]">
            <div v-for="task in taskData" :key="task.date">

                <!-- attract: Рамка выбора даты -->
                <div
                    :class="task.active ? 'bg-blue-200 border-2 border-blue-400 rounded-lg' : ''"
                    class="flex flex-col p-0.5  h-full">

                    <!-- attract: Дата + день недели -->
                    <AppLabelMultiLine
                        :text="[formatDate(task.date), getDayOfWeek(task.date, false)]"
                        :type="dayOfWeekStyle(task.date)"
                        align="center"
                        class="cursor-pointer"
                        width="w-[150px]"
                        @click="changeActiveTask(task)"
                    />

                    <!-- attract: Статусы -->
                    <AppLabel
                        :text="getTitleByFabricTaskStatusCode(task.common.status)"
                        :type="getStyleTypeByFabricTaskStatusCode(task.common.status)"
                        align="center"
                        class="cursor-pointer"
                        width="w-[150px]"
                    />

                    <!-- attract: Первый ряд сервисных кнопок -->
                    <AppLabel
                        v-if="serviceBtnShowCondition(task.common.status)"
                        :text="serviceBtnTitle(task.common.status)"
                        align="center"
                        class="cursor-pointer"
                        textSize="small"
                        type="info"
                        width="w-[150px]"
                        @click="changeTaskStatus(task)"
                    />

                    <!-- attract: Второй ряд сервисных кнопок - кнопка "Удалить" (Только для статуса "Создано") -->
                    <AppLabel
                        v-if="task.common.status === FABRIC_TASK_STATUS.CREATED.CODE"
                        align="center"
                        class="cursor-pointer"
                        text="Удалить"
                        textSize="small"
                        type="danger"
                        width="w-[150px]"
                        @click="changeTaskStatus(task, 2)"
                    />

                </div>

            </div>
        </div>


        <!-- attract Выводим табы -->
        <div class="flex flex-row justify-start items-center m-3">
            <div v-for="tab in tabs" :key="tab.id">

                <AppLabelMultiLine
                    :bold="true"
                    :text="tab.name"
                    :type="tab.shown ? 'primary' : 'dark'"
                    align="center"
                    class="cursor-pointer"
                    width="w-[150px]"
                    @click="changeTab(tab)"
                />

            </div>
        </div>


        <div class="ml-3 mt-3">

            <!--attract: Общее-->
            <div v-if="tabs.common.shown">

                <TheTaskCommonInfo/>

            </div>

            <!--attract: Американец-->
            <!-- warning: key: - для реактивности -->
            <!-- todo: доработать, потому, что task будем получать в самом компоненте -->
            <div v-if="tabs.american.shown">
                <TheTaskMachine
                    :key="activeTask"
                    :task="activeTask"
                    :machine="FABRIC_MACHINES.AMERICAN"
                    @add-roll="addRoll"
                    @optimize-labor="optimizeLabor"
                />

            </div>

            <!--attract: Немец-->
            <div v-if="tabs.german.shown">
                <div>Немец</div>
            </div>

            <!--attract: Китаец-->
            <div v-if="tabs.china.shown">
                <div>Китаец</div>
            </div>

            <!--attract: Китаец-->
            <div v-if="tabs.korean.shown">
                <div>Кореец</div>
            </div>

        </div>

    </div>

    <AppModalAsync
        ref="appModalAsync"
        :text="modalText"
        mode="confirm"
        type="danger"
    />

</template>

<script setup>
import {ref, reactive} from 'vue'
import {useRoute, useRouter} from 'vue-router'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
    FABRICS_NULLABLE,
    TEST_FABRICS,
} from '/resources/js/src/app/constants/fabrics.js'

import {
    getTitleByFabricTaskStatusCode,
    getStyleTypeByFabricTaskStatusCode,
    getFabricTasksPeriod,
    addEmptyFabricTasks
} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import {
    getDayOfWeek,
    formatDate,
    isToday,
    isWorkingDay,
} from '/resources/js/src/app/helpers/helpers_date.js'

import TheTaskRecordTitle
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecordsTitle.vue'
import TheTaskRecord
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskRecord.vue'
import TheTaskCommonInfo
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskCommonInfo.vue'

import TheTaskMachine
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskMachine.vue'


import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsync from '/resources/js/src/components/ui/modals/AppModalAsync.vue'
import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'
import AppSelect from '/resources/js/src/components/ui/selects/AppSelect.vue'
import AppInputTextArea from '/resources/js/src/components/ui/inputs/AppInputTextArea.vue'
import AppModal from '/resources/js/src/components/ui/modals/AppModal.vue'
// import AppModalAsync from '/resources/js/src/components/ui/modals/AppModalAsync.vue'
// import AppButton from '/resources/js/src/components/ui/buttons/AppButton.vue'

const route = useRoute()
const router = useRouter()

const fabricsStore = useFabricsStore()

// Получаем период отображения сменного задания
const tasksPeriod = getFabricTasksPeriod()
console.log('tasksPeriod:', tasksPeriod)

// Получаем сами сменные задания
const tasks = await fabricsStore.getTasksByPeriod(tasksPeriod)
console.log('tasks:', tasks)

// Получаем все ткани и запоминаем в хранилище
const fabrics = await fabricsStore.getFabrics()
fabrics.unshift(FABRICS_NULLABLE)                   // добавляем пустой элемент в начало массива
fabricsStore.fabricsMemory = fabrics
console.log(fabrics)

// attract: Задаем отображение вкладок (Общие данные, Американец, Немец, Китаец, Кореец)
const tabs = reactive({
    common: {id: 1, shown: false, name: ['Общие', 'данные']},
    american: {id: 2, shown: false, name: ['Американец', 'LEGACY-4']},
    german: {id: 3, shown: false, name: ['Немец', 'CHAINTRONIC']},
    china: {id: 4, shown: false, name: ['Китаец', 'HY-W-DGW']},
    korean: {id: 5, shown: false, name: ['Кореец', 'МТ-94']},
    // oneNeedle: {id: 6, shown: false, name: ['Одноиголка', '']},
    // test: {id: 6, shown: false, name: ['Machine', 'Test']},
})

// переключаем выбранную вкладку
const changeTab = (selectedTab) => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = tabs[tab].id === selectedTab.id
        }
    }
    console.log(tabs)
}

// сбрасываем все вкладки в false, потому что в некоторых ситуациях появляется мультивыбор
const resetTabs = () => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = false
        }
    }
    // console.log(tabs)
}

resetTabs()                                 // сбрасываем все табы
tabs.common.shown = true                    // делаем вкладку "общие данные" активной, чтобы запустить реактивность

// attract: Определяем объект с данными чекбокса (доступность тканей - основные или все доступные)
const checkboxData = {
    name: 'Availability',
    data: [
        {id: 1, name: 'Основные ПС', checked: true},
        {id: 2, name: 'Все доступные ПС', checked: false},
    ]
}

// формируем тестовые данные
let taskData = reactive(TEST_FABRICS)
taskData = addEmptyFabricTasks(taskData)

// Ссылка на активное СЗ
let activeTask = reactive(taskData.find(t => t.active))

console.log(taskData)


// attract Получаем тип метки в зависимости от типа дня недели (выходной или рабочий)
const dayOfWeekStyle = (date) => {
    if (isToday(date)) return 'success'
    if (isWorkingDay(date)) return 'dark'
    return 'danger'
}

// attract Условие на отображение сервисных кнопок под статусами
const serviceBtnShowCondition = (status) => {
    return !(status === FABRIC_TASK_STATUS.DONE.CODE || status === FABRIC_TASK_STATUS.RUNNING.CODE)
}

// attract Получаем название сервисной кнопки в зависимости от статуса
const serviceBtnTitle = (status) => {
    const titles = ['Создать', 'На стежку', 'Вернуть статус', '', '']
    return titles[status]
}

const appModalAsync = ref(null)         // Получаем ссылку на модальное окно
const modalText = ref('')


// attract Меняем статус СЗ по сервисной кнопке
const changeTaskStatus = async (task, btnRow = 1) => {

    if (!task.active) return

    // Удалить сменное задание
    if (btnRow === 2 && task.common.status === FABRIC_TASK_STATUS.CREATED.CODE) {
        modalText.value = 'Вы уверены?'
        const result = await appModalAsync.value.show()             // показываем модалку и ждем ответ
        if (result) {
            task.common.status = FABRIC_TASK_STATUS.UNKNOWN.CODE
        }
        return
    }

    if (task.common.status === FABRIC_TASK_STATUS.UNKNOWN.CODE) {
        task.common.status = FABRIC_TASK_STATUS.CREATED.CODE
        return
    }

    if (task.common.status === FABRIC_TASK_STATUS.PENDING.CODE) {
        task.common.status = FABRIC_TASK_STATUS.CREATED.CODE
        return
    }

    if (task.common.status === FABRIC_TASK_STATUS.CREATED.CODE) {
        task.common.status = FABRIC_TASK_STATUS.PENDING.CODE
    }

}

// attract Меняем активный день по клику на нем
const changeActiveTask = (task) => {
    taskData.forEach((t) => t.active = t.date === task.date)
    activeTask = taskData.find(t => t.active)
    // console.log('active_task: ', activeTask)
}

// const taskRecordEditMode = ref(false)

// attract: Поднятое событие при клике на кнопку "Добавить рулон"
const addRoll = (newRoll, machine, task) => {

    const workTask = taskData.find(t => t.date === task.date)     // Получаем ссылку на СЗ на дату контекста

    workTask.machines[machine.TITLE].rolls.push(newRoll)

    // debugger

    console.log(newRoll)
    console.log(machine.TITLE)
    console.log(workTask)
    console.log(workTask.machines)
}

// attract: Поднятое событие при клике на кнопку "Оптимизировать трудозатраты"
const optimizeLabor = (machine, task) => {

}

</script>

<style scoped>

</style>





