<template>
    <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">

        <!-- attract: Кем и когда создано задание -->
        <div class="mb-2">


            <!--  attract: descr: Группа: Сменное задание-->
            <div>
                <AppLabel
                    text="Сменное задание:"
                    type="success"
                />
            </div>

            <div class="flex">

                <!-- attract: Статус -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Статус:"
                        text-size="mini"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="getTitleByFabricTaskStatusCode(task.common.status)"
                        :type="getStyleTypeByFabricTaskStatusCode(task.common.status)"
                        text-size="mini"
                        width="w-[200px]"
                    />
                </div>

                <!-- attract: Ответственный -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Специалист:"
                        text-size="mini"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="task.common.created_by"
                        text-size="mini"
                        width="w-[200px]"
                    />
                </div>

            </div>


            <div class="flex">

                <!-- attract: Дата создания -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Дата создания СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatDateInFullFormat(task.common.created_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

                <!-- attract: Время создания -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Время создания СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatTimeInFullFormat(task.common.created_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

            </div>

            <!-- attract: Общий комментарий ко дню -->
            <div
                v-if="task.common.status === FABRIC_TASK_STATUS.PENDING.CODE || task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE || task.common.status === FABRIC_TASK_STATUS.DONE.CODE">

                <div v-if="task.common.description" class="flex items-start ml-3">
                    <AppLabel
                        text="Общий комментарий:"
                        text-size="mini"
                        type="warning"
                        width="w-[150px]"
                    />

                    <AppLabel
                        :text="task.common.description"
                        text-size="mini"
                        type="warning"
                        width="w-[570px]"
                    />
                </div>

            </div>

            <div v-else class="flex items-end">

                <!--attract: Общий комментарий к дню сменных заданий -->
                <AppInputTextArea
                    id="comment"
                    v-model.trim="taskCommonDescription"
                    :disabled="!getFunctionalByFabricTaskStatus(task.common.status)"
                    :rows=2
                    :value="taskCommonDescription"
                    class="cursor-pointer"
                    height="min-h-[60px]"
                    label="Общий комментарий:"
                    placeholder="Введите комментарий"
                    text-size="normal"
                    width="w-[740px]"
                />

                <AppLabel
                    align="center"
                    class="cursor-pointer"
                    height="h-[60px]"
                    text="💾"
                    text-size="huge"
                    type="success"
                    width="w-[50px]"
                    @click="updateTaskCommonDescription"
                />


            </div>


            <!-- attract: Если статус СЗ - выполнено или в процессе выполнения, то показываем Дату и Время старта СЗ -->
            <div
                v-if="task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE || task.common.status === FABRIC_TASK_STATUS.DONE.CODE"
                class="flex">

                <!-- attract: Дата начала выполнения -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Дата старта СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatDateInFullFormat(task.common.start_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

                <!-- attract: Время начала выполнения -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Время старта СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatTimeInFullFormat(task.common.start_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

            </div>

            <!-- attract: Если статус СЗ - выполнено, то показываем Дату и Время окончания СЗ -->
            <div
                v-if="task.common.status === FABRIC_TASK_STATUS.DONE.CODE"
                class="flex">

                <!-- attract: Дата окончания выполнения -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Дата окончания СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatDateInFullFormat(task.common.finish_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

                <!-- attract: Время окончания выполнения -->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Время окончания СЗ:"
                        text-size="mini"
                        type="info"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="formatTimeInFullFormat(task.common.finish_at)"
                        text-size="mini"
                        type="info"
                        width="w-[200px]"
                    />
                </div>

            </div>

            <!-- attract: Если статус СЗ - выполнено или в процессе выполнения, то показываем длительность СЗ -->
            <div
                v-if="task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE || task.common.status === FABRIC_TASK_STATUS.DONE.CODE"
                class="flex">

                <!-- attract: Длительность выполнения СЗ-->
                <div class="flex items-start ml-3">
                    <AppLabel
                        text="Длительность СЗ:"
                        text-size="mini"
                        type="warning"
                        width="w-[150px]"
                    />
                    <AppLabel
                        :text="duration"
                        text-size="mini"
                        type="warning"
                        width="w-[200px]"
                    />
                </div>

            </div>

        </div>


        <TheDividerLine/>

        <!-- attract: Бригада -->
        <div class="mb-4">

            <div class="flex items-start">

                <AppLabel
                    text="Бригада:"
                    type="success"
                    width="w-[100px]"
                />

                <AppLabel
                    :text="'№ ' + task.common.team.toString()"
                    type="success"
                    width="w-[50px]"
                />

                <!-- attract: Кнопка персонала (неактивно для завершенного СЗ) -->
                <div v-if="task.common.status !== FABRIC_TASK_STATUS.DONE.CODE" class="ml-3">

                    <AppLabel
                        :type="task.workers.length ? 'info' : 'danger'"
                        align="center"
                        class="cursor-pointer"
                        text="Персонал"
                        @click="selectWorkers"
                    />

                </div>

            </div>

            <div class="ml-3">

                <div v-for="(worker, idx) in task.workers" :key="idx">
                    <AppLabel
                        :text="getFormatFIO(worker)"
                        text-size="mini"
                        width="w-[200px]"
                    />

                </div>

            </div>

        </div>

        <!-- attract: Разделительная линия -->
        <TheDividerLine/>

        <!-- attract: Трудозатраты -->
        <!-- attract: Показываем, если есть -->
        <div v-if="totalProductivityRolls" class="mb-2">
            <div>
                <AppLabel
                    text="Трудозатраты:"
                    type="success"
                />
            </div>

            <!-- __ Трудозатраты на американце -->
            <div v-if="americanProductivityRolls" class="flex items-start ml-3">
                <AppLabel
                    text="Американец:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="'🕒 ' + formatTimeWithLeadingZeros(americanProductivityRolls, 'hour')"
                    :type="getStyleTypeByProductivity(americanProductivityRolls)"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <!-- __ Трудозатраты на немце -->
            <div v-if="germanProductivityRolls" class="flex items-start ml-3">
                <AppLabel
                    text="Немец:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="'🕒 ' + formatTimeWithLeadingZeros(germanProductivityRolls, 'hour')"
                    :type="getStyleTypeByProductivity(germanProductivityRolls)"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <!-- __ Трудозатраты на китайце -->
            <div v-if="chinaProductivityRolls" class="flex items-start ml-3">
                <AppLabel
                    text="Китаец:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="'🕒 ' + formatTimeWithLeadingZeros(chinaProductivityRolls, 'hour')"
                    :type="getStyleTypeByProductivity(chinaProductivityRolls)"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <!-- __ Трудозатраты на корейце -->
            <div v-if="koreanProductivityRolls" class="flex items-start ml-3">
                <AppLabel
                    text="Кореец:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="'🕒 ' + formatTimeWithLeadingZeros(koreanProductivityRolls, 'hour')"
                    :type="getStyleTypeByProductivity(koreanProductivityRolls)"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <!-- attract: Разделительная линия -->
            <TheDividerLine/>

            <!-- attract: Общие трудозатраты  -->
            <div class="flex items-start ml-3">
                <AppLabel
                    text="Всего:"
                    text-size="normal"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="'🕒 ' + formatTimeWithLeadingZeros(totalProductivityRolls, 'hour')"
                    text-size="mini"
                    type="primary"
                    width="w-[200px]"
                />
            </div>

        </div>

    </div>

    <!-- attract: Модальное окно выбора персонала   -->
    <!--    <div v-if="appModalAsyncCheckboxShown">-->
    <AppModalAsyncCheckbox
        ref="appModalAsyncCheckbox"
        :checkboxData="checkboxData"
        :legend="modalLegendCheckbox"
        :text="modalTextCheckbox"
        :type="modalTypeCheckbox"
    />
    <!--    </div>-->

</template>

<script setup lang="ts">

import { computed, /*onMounted,*/ onUnmounted, reactive, ref, watch } from 'vue'

import { useWorkersStore } from '@/stores/WorkersStore.ts'
import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    FABRIC_WORKING_SHIFT_LENGTH,
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
    // FABRICS_NULLABLE,
    // FABRIC_TASKS_EXECUTE,
} from '@/app/constants/fabrics.js'

import {
    getTitleByFabricTaskStatusCode,
    getStyleTypeByFabricTaskStatusCode,
    getFunctionalByFabricTaskStatus,
    getTotalRollsProductivity, getTotalExecRollsProductivityFromContextRolls,
} from '@/app/helpers/manufacture/helpers_fabric.js'

import {
    formatTimeWithLeadingZeros,
    formatDateInFullFormat,
    formatTimeInFullFormat
} from '@/app/helpers/helpers_date.js'

import {
    getFormatFIO,
    getFormatFIOFromFullNameString
} from '@/app/helpers/workers/helpers_workers.js'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import TheDividerLine
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/TheDividerLine.vue'
import AppModalAsyncCheckbox from '@/components/ui/modals/AppModalAsyncCheckbox.vue'
import AppInputTextArea from '@/components/ui/inputs/AppInputTextArea.vue'


const props = defineProps({
    task: {
        type: Object,
        required: false,
        default: () => ({})
    },
})

const emits = defineEmits(['selectWorkers', 'saveTaskDescription'])

const workersStore = useWorkersStore()
const fabricsStore = useFabricsStore()


// attract: Определяем модель для Общего комментария к дню СЗ
const taskCommonDescription = ref(props.task.common.description)

// attract: Получаем длительность СЗ
const getTaskDuration = (task) => {

    let tempDuration

    if (task.common.finish_at) {
        tempDuration = new Date(task.common.finish_at).getTime() - new Date(task.common.start_at).getTime()
    } else {
        tempDuration = new Date().getTime() - new Date(task.common.start_at).getTime()
    }

    tempDuration = Math.floor(tempDuration / 1000)
    return formatTimeWithLeadingZeros(tempDuration)
}
// attract: Длительность СЗ
const duration = ref(getTaskDuration(props.task))


// __ Трудозатраты на СМ
// __ Если статус СЗ - "Выполнено", "Выполняется" или "Готов к стежке"
// __ то есть, когда есть физические рулоны, то считаем по физическим рулонам
// __ иначе считаем по контекстным рулонам

/**
 *
 * @param {IConstFabricMachine} machine
 * @returns {number}
 */
const getProductivityByMachine = (machine) => {
    if (
        props.task.common.status === FABRIC_TASK_STATUS.DONE.CODE ||
        props.task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE ||
        props.task.common.status === FABRIC_TASK_STATUS.PENDING.CODE
    ) {
        return getTotalRollsProductivity(props.task.machines[machine.TITLE].rolls)
    }

    return getTotalExecRollsProductivityFromContextRolls(props.task.machines[machine.TITLE].rolls)
}

// __ Трудозатраты на СМ
const americanProductivityRolls = computed(() => getProductivityByMachine(FABRIC_MACHINES.AMERICAN))
const germanProductivityRolls = computed(() => getProductivityByMachine(FABRIC_MACHINES.GERMAN))
const chinaProductivityRolls = computed(() => getProductivityByMachine(FABRIC_MACHINES.CHINA))
const koreanProductivityRolls = computed(() => getProductivityByMachine(FABRIC_MACHINES.KOREAN))

// TODO: Пока не делаю, потому что переналадка не сохраняется в базе, а формируется динамически
// TODO: здесь выполнение и нет запросов к базе со временем переналадки
// const americanProductivityTuning = computed(() => getTotalTuningProductivity(props.task.machines[FABRIC_MACHINES.AMERICAN.TITLE].rolls))
// const germanProductivityTuning = computed(() => getTotalTuningProductivity(props.task.machines[FABRIC_MACHINES.GERMAN.TITLE].rolls))
// const chinaProductivityTuning = computed(() => getTotalTuningProductivity(props.task.machines[FABRIC_MACHINES.CHINA.TITLE].rolls))
// const koreanProductivityTuning = computed(() => getTotalTuningProductivity(props.task.machines[FABRIC_MACHINES.KOREAN.TITLE].rolls))

// console.log(americanProductivityTuning.value, germanProductivityTuning.value, chinaProductivityTuning.value, koreanProductivityTuning.value)

const totalProductivityRolls = computed(() =>
    americanProductivityRolls.value +
    germanProductivityRolls.value +
    chinaProductivityRolls.value +
    koreanProductivityRolls.value
)


// attract: Получаем тип стиля по продолжительности трудозатрат
const getStyleTypeByProductivity = (productivity) => productivity <= FABRIC_WORKING_SHIFT_LENGTH ? 'success' : 'danger'

let intervalId = null   // Для очистки интервала, если он будет создан, чтобы убрать утечки памяти

// attract: Отслеживаем изменения задачи и вычисляем продолжительность СЗ
watch(() => props.task, (newTask) => {

    duration.value = getTaskDuration(newTask)

    if (newTask.common.status === FABRIC_TASK_STATUS.RUNNING.CODE) {

        if (!intervalId) {

            intervalId = setInterval(() => {
                duration.value = getTaskDuration(newTask)
            }, 1000)
        }
    } else if (newTask.common.status === FABRIC_TASK_STATUS.DONE.CODE) {
        if (intervalId !== null) {
            clearInterval(intervalId)
            duration.value = getTaskDuration(newTask)
        }
    }


}, {
    deep: true,
    immediate: true,
})


// attract: Получаем ссылку на модальное окно для ввода данных о персонале с асинхронной функцией
const appModalAsyncCheckbox = ref(null)
const modalTypeCheckbox = ref('success')
const modalTextCheckbox = ref(['Выберите сотрудников:', ''])
const modalLegendCheckbox = ref('Список сотрудников:')


// attract: Выбираем персонал
// attract: --------------------------------------------------
let workers
const checkboxData = reactive({name: '', data: []})
const selectData = reactive({name: '', data: []})

// attract: Подготавливаем данные для отображения выбора персонала и ответственного в рулонах
const prepareWorkersData = async () => {
    // Получаем персонал, убираем нулевого сотрудника из персонала и сортируем по ФИО
    workers = await workersStore.getWorkers()
    workers = workers.filter((worker) => worker.id !== 0)
    workers.sort((a, b) => (a.surname + a.name + a.patronymic).localeCompare((b.surname + b.name + b.patronymic)))

    // console.log('props.task.machines: ', props.task.machines)
    // console.log(Object.keys(props.task.machines))

    // attract: Получаем список всех сотрудников, которые уже есть в упоминании к ответственному в рулонах
    // attract: чтобы их сделать не доступными для выбора
    const workersAlreadyExistsInExecuteRolls = new Set()
    // Собираем список сотрудников, которые уже есть в упоминании к ответственному в рулонах только в нужных режимах
    // потому что в других не доступно
    if (props.task.common.status === FABRIC_TASK_STATUS.RUNNING.CODE ||
        props.task.common.status === FABRIC_TASK_STATUS.DONE.CODE ||
        props.task.common.status === FABRIC_TASK_STATUS.PENDING.CODE) {
        Object.keys(props.task.machines).forEach((machine) => {
            props.task.machines[machine].rolls.forEach((roll) => {
                roll?.rolls_exec.forEach((rollExec) => {
                    workersAlreadyExistsInExecuteRolls.add(rollExec.finish_by)
                })
            })
        })
    }
    // console.log('set: ', workersAlreadyExistsInExecuteRolls)

    // отмечаем сотрудников, которые уже есть в списке
    let isFind, recordId, currWorkerIdx
    const getCheckedWorkers = workers.map((worker) => {
        isFind = props.task.workers.some((taskWorker) => taskWorker.id === worker.id)

        // Warning: Здесь очень важно. Таскаем за собой запись этого работника в таблице worker_records
        // warning: чтобы обновлять на сервере эти записи
        currWorkerIdx = props.task.workers.findIndex((taskWorker) => taskWorker.id === worker.id)
        recordId = currWorkerIdx !== -1 ? props.task.workers[currWorkerIdx].record_id : 0

        return {
            id: worker.id,
            record_id: recordId,
            name: `${worker.surname} ${worker.name} ${worker.patronymic}`,
            checked: isFind,
            disabled: workersAlreadyExistsInExecuteRolls.has(worker.id), // если работник уже есть в упоминании к ответственному в рулонах, то делаем его не доступным для выбора
        }
    })

    // attract: Подготавливаем данные для отображения в чекбоксе выбора персонала
    checkboxData.name = 'workers'
    checkboxData.data = getCheckedWorkers

    // attract: Подготавливаем данные для отображения в селекте выбора ответственного
    let selectWorkers = getCheckedWorkers.map((worker) => {
        if (worker.checked) {
            const tempWorker = {...worker}
            delete tempWorker.checked
            tempWorker.name = getFormatFIOFromFullNameString(tempWorker.name)
            tempWorker.disabled = false
            return tempWorker
        }
    })

    selectWorkers = selectWorkers.filter((worker) => worker)    // убираем элементы, которые не выбраны в персонале
    selectWorkers.unshift({id: 0, name: 'Не выбран', disabled: true})


    selectData.name = 'workers'
    selectData.data = selectWorkers

    // attract: записываем в глобальный объект данные для чекбокса
    fabricsStore.globalSelectWorkers.value = selectData

    // console.log('selectData: ', selectData)
}

// attract: Выбираем персонал
const selectWorkers = async () => {

    await prepareWorkersData()

    const answer = await appModalAsyncCheckbox.value.show() // показываем модалку и ждем ответ
    if (answer) {
        const newWorkers = appModalAsyncCheckbox.value.checkData
        emits('selectWorkers', newWorkers)
    }

}

// attract: Подготавливаем данные для отображения выбора персонала и ответственного в рулонах
watch(() => props.task, async () => {
    await prepareWorkersData()
}, {deep: true, immediate: true})

// attract: --------------------------------------------------

// attract: Обновляем общее описание ко дню СЗ
const updateTaskCommonDescription = () => {
    // if (!taskCommonDescription.value) return
    // console.log(taskCommonDescription.value)
    emits('saveTaskDescription', taskCommonDescription.value)
}


// onMounted(() => {
//     const interval = setInterval(() => duration.value = getTaskDuration(props.task), 1000)
//     console.log('interval', interval)
// })

// const team = await fabricsStore.getFabricTeamNumberByDate('2025-04-22')
// const team = await fabricsStore.getFabricTeamNumberByDate('2025-05-27')
// console.log('team: ', team)

onUnmounted(async () => {
    if (intervalId !== null) {
        clearInterval(intervalId)
    }

    // attract: Вызываем пересчет данных о работниках, чтобы определить все глобальное
    // await prepareWorkersData()
})

</script>

<style scoped>

</style>
