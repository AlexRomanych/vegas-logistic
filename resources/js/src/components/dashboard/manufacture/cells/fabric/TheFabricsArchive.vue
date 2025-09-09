<template>
    <div class="ml-2 mt-2 mr-2">

        <div class="sticky top-0 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 w-full">
            <!-- __ Выбор дат -->
            <CellDatesSelect
                @apply="selectDates"
            />
        </div>

        <!-- __ Разделительная линия -->
        <TheDividerLine/>

        <div v-for="taskData of tasksData" :key="taskData.id">

            <div v-if="hasData(taskData)">

                <!-- __ Заголовок даты -->
                <TheTaskArchiveDate
                    :task="taskData"
                    @toggle-task="taskData.collapsed = !taskData.collapsed"
                />

                <div v-if="!taskData.collapsed" class="ml-[34px]">

                    <div v-if="taskData.workers.length">

                        <!-- __ Персонал -->
                        <TheTaskArchiveWorkers
                            :collapsed="taskData.collapsedWorkers"
                            :team="taskData.common.team"
                            :workers="taskData.workers"
                            @toggle-workers="taskData.collapsedWorkers = !taskData.collapsedWorkers"
                        />

                    </div>

                    <div v-for="machineKey in Object.keys(taskData.machines)">

                        <div v-if="taskData.machines[machineKey].rollsRender.length">

                            <!-- __ Машины -->
                            <TheTaskArchiveMachine
                                :machine="taskData.machines[machineKey]"
                                :render="rollsRender"
                            />

                        </div>

                    </div>

                </div>

                <!-- __ Разделительная линия -->
                <TheDividerLine/>

            </div>

        </div>
    </div>
</template>


<script lang="ts" setup>
import { ref, reactive, onMounted, /*watch,*/ } from 'vue'

import type {
    IFabric,
    ITaskItem,
    ITaskPeriod,
    IMachineData,
    IRollExec,
    FabricMachineTitles,
    IRenderData,
} from '@/types'

import { useFabricsStore } from '@/stores/FabricsStore'

import { FABRIC_MACHINES, FABRIC_ROLL_STATUS_LIST } from '@/app/constants/fabrics.ts'

import { getFabricTasksPeriod, getTypeByRollStatus } from '@/app/helpers/manufacture/helpers_fabric'
import { formatDateAndTimeInShortFormat, formatTimeWithLeadingZeros, getDuration } from '@/app/helpers/helpers_date'
import { getFormatFIO } from '@/app/helpers/workers/helpers_workers'

import TheTaskArchiveMachine
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_archive/TheTaskArchiveMachine.vue'
import TheTaskArchiveDate
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_archive/TheTaskArchiveDate.vue'
import TheTaskArchiveWorkers
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_archive/TheTaskArchiveWorkers.vue'
import TheDividerLine from '@/components/dashboard/manufacture/cells/fabric/fabric_components/TheDividerLine.vue'
import CellDatesSelect from '@/components/dashboard/manufacture/cells/components/CellDatesSelect.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler, log } from '@/app/helpers/helpers.ts'


// line -----------------------------------------------------------------------------------------------------------
// line ------------- Объявление типов ----------------------------------------------------------------------------
// line -----------------------------------------------------------------------------------------------------------

export type IRollExecExtended = IRollExec & { storedStatus: number }

// 1. Создаем расширенный тип для данных машины + добавляем отображаемые рулоны
export interface IMachineDataExtended extends IMachineData {
    machineName: string                 // Название машины
    rollsRender: IRollExecExtended[]    // Отображаемые рулоны + поле для хранения оригинального статуса рулона
    collapsed: boolean                  // Поле для хранения состояния сворачивания рулонов в стегальной машине
}

// 2. Создаем новый тип ITaskItem, который включает расширенные данные
// Мы пересекаем исходный ITaskItem с объектом, который переопределяет machines
export type ITaskDataItem = ITaskItem & {
    machines: Record<FabricMachineTitles, IMachineDataExtended>
    collapsed: boolean                  // Поле для хранения состояния сворачивания дня СЗ
    collapsedWorkers: boolean           // Поле для хранения состояния сворачивания персонала
};


// line -----------------------------------------------------------------------------------------------------------


const isLoading = ref(true)

const fabricsStore = useFabricsStore()

const FABRIC_ROLL_STATUS_ARRAY = Object.values(FABRIC_ROLL_STATUS_LIST)
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE = 'mini'
const HEADER_ALIGN = 'center'
const DATA_ALIGN = 'center'
// const GLOBAL_TYPE = 'primary'

// __ Подготавливаем переменные
let fabrics: IFabric[] = []                                             // __ Получаем ПС
let tasksPeriod: ITaskPeriod = {start: '', end: ''}
let tasks: ITaskItem[] = []
const tasksData = ref<ITaskDataItem[]>([])
// const workersMap = new Map<number, IWorkerData>()                    // __ Создаем карту для хранения данных о сотрудниках

// __ Задаем глобальный объект для унификации отображения рулонов
const rollsRender: IRenderData = reactive({
    position: {
        header: ['№', 'п/п'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[60px]',
        show: true,
        title: '№ п/п',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => roll_exec.position.toString()
    },

    rollNumber: {
        header: ['Номер', 'рулона'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[60px]',
        show: true,
        title: '№ рулона',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => roll_exec.id.toString()
    },
    fabricName: {
        header: ['Полотно', 'стеганное'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[290px]',
        show: true,
        title: 'Название ПС',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'left',
        align: 'center',
        data: (roll_exec) => {
            const fabric = fabrics.find((fabric) => fabric.id === roll_exec.fabric_id)
            return fabric ? fabric.display_name : ''
        }
    },
    textileLength: {
        header: ['Длина', 'ткани'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[50px]',
        show: true,
        title: 'Средняя длина ткани, м.п.',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => roll_exec.textile_length.toFixed(2)
    },
    fabricLength: {
        header: ['Длина', 'ПС'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[50px]',
        show: true,
        title: 'Средняя длина ПС, м.п.',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => (roll_exec.textile_length / roll_exec.rate).toFixed(2)
    },
    rollsAmount: {
        header: ['1', ''],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[30px]',
        show: false,
        title: 'Кол-во рулонов, шт.',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: () => '1'
    },
    productivity: {
        header: ['Плановые', 'трудозатраты'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[90px]',
        show: false,
        title: 'Средние трудозатраты, ч.',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => formatTimeWithLeadingZeros(roll_exec.textile_length / roll_exec.productivity, 'hour')
    },
    description: {
        header: ['Комментарий', ''],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[200px]',
        show: true,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        title: (roll_exec) => roll_exec.descr ?? 'Комментарий',
        data: (roll_exec) => roll_exec.descr
    },
    status: {
        header: ['Статус', ''],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[100px]',
        show: true,
        title: 'Статус',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => {
            const rollStatus = FABRIC_ROLL_STATUS_ARRAY.find((fabricRollStatus) => fabricRollStatus.CODE === roll_exec.status)
            return rollStatus ? rollStatus.TITLE : ''
        }
    },
    startAt: {
        header: ['Начало', 'стегания'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[125px]',
        show: true,
        title: 'Начало стегания рулона',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => roll_exec.start_at ? formatDateAndTimeInShortFormat(roll_exec.start_at) : ''
    },
    finishAt: {
        header: ['Окончание', 'стегания'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[125px]',
        show: true,
        title: 'Окончание стегания рулона',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => roll_exec.finish_at ? formatDateAndTimeInShortFormat(roll_exec.finish_at) : ''
    },
    rollTime: {
        header: ['Время', 'стегания'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[90px]',
        show: true,
        title: 'Время стегания',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => getDuration(roll_exec.start_at, roll_exec.finish_at)
    },
    finishBy: {
        header: ['Ответственный', 'за производство'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[150px]',
        show: true,
        title: 'Ответственный',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => {
            if (!roll_exec.responsible.finishBy.id) return ''
            return getFormatFIO(roll_exec.responsible.finishBy)
        }
        // data: (roll_exec) => {
        //     if (!roll_exec.finish_by) return ''
        //     const responsibleWorker = workersMap.get(roll_exec.finish_by)
        //     return responsibleWorker ? getFormatFIO(responsibleWorker) : ''
        // }
    },
    register1CAt: {
        header: ['Учет', 'в 1С'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[125px]',
        show: true,
        title: 'Окончание стегания рулона',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => roll_exec.registration_1C_at ? formatDateAndTimeInShortFormat(roll_exec.registration_1C_at) : ''
    },
    register1CBy: {
        header: ['Ответственный', 'за учет в 1С'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[150px]',
        show: true,
        title: 'Ответственный',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => {
            if (!roll_exec.responsible.register1CBy.id) return ''
            return getFormatFIO(roll_exec.responsible.register1CBy)
        }
    },
    moveToCutAt: {
        header: ['Перемещение', 'на закрой'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[125px]',
        show: true,
        title: 'Окончание стегания рулона',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => roll_exec.move_to_cut_at ? formatDateAndTimeInShortFormat(roll_exec.move_to_cut_at) : ''
    },
    moveToCutBy: {
        header: ['Ответственный за', '--> закрой'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[150px]',
        show: true,
        title: 'Ответственный',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => {
            // console.log(roll_exec)
            // return ''
            if (!roll_exec.responsible.moveToCutBy.id) return ''
            return getFormatFIO(roll_exec.responsible.moveToCutBy)
        }
    },
    receiptToCutAt: {
        header: ['Прием', 'на закрое'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[125px]',
        show: false,
        title: 'Окончание стегания рулона',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => roll_exec.receipt_to_cut_at ? formatDateAndTimeInShortFormat(roll_exec.receipt_to_cut_at) : ''
    },
    receiptToCutBy: {
        header: ['Ответственный за', 'прием на закрое'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[150px]',
        show: false,
        title: 'Ответственный',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => {
            if (!roll_exec.responsible.receiptToCutBy.id) return ''
            return getFormatFIO(roll_exec.responsible.receiptToCutBy)
        }
    },
    closeAt: {
        header: ['Списание', 'рулона'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[125px]',
        show: false,
        title: 'Окончание стегания рулона',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (roll_exec) => roll_exec.close_at ? formatDateAndTimeInShortFormat(roll_exec.close_at) : ''
    },
    closeBy: {
        header: ['Ответственный за', 'списание рулона'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[150px]',
        show: false,
        title: 'Ответственный',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        data: (/*roll_exec*/) => {
            return ''
            // if (!roll_exec.responsible.closeBy.id) return ''
            // return getFormatFIO(roll_exec.responsible.closeBy)
        }
    },
    reason: {
        header: ['Причина', 'изменения статуса'],
        type: (roll_exec) => getTypeByRollStatus(roll_exec.status),
        width: 'w-[300px]',
        show: true,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        title: (roll_exec) => roll_exec.false_reason ?? 'Причина не выполнения',
        data: (roll_exec) => roll_exec.false_reason
    },
    isCalc: {
        header: ['Калькулятор', ''],
        width: 'w-[60px]',
        show: false,
        title: '+ / -',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        align: 'center',
        type: (roll_exec) => roll_exec.isCalc ? 'success' : 'danger',
        data: (roll_exec) => (roll_exec.isCalc ? '✓' : '✗'),
    },
})


// __ Получаем все ткани
const getFabrics = async () => {
    fabrics = await fabricsStore.getFabrics(true)
}

// __ Получаем период отображения сменного задания
const getTasksPeriod = () => tasksPeriod = getFabricTasksPeriod()

// __ Получаем сами сменные задания
const getTasks = async () => tasks = await fabricsStore.getTasksByPeriod(tasksPeriod)


// __ Подготавливаем данные для отображения
const prepareTasksData = () => {
    tasksData.value = tasks.map(task => ({...task, collapsed: true, collapsedWorkers: true})) as ITaskDataItem[]  // Копируем данные для отображения

    // workersMap.clear()

    // __ Сортируем массив рулонов ОПП + сортируем массив физических рулонов СМ по позиции
    tasksData.value.forEach(task => {

        // task.workers.forEach(worker => workersMap.set(worker.id, worker))   // Добавляем в карту сотрудников

        Object.keys(task.machines).forEach(machine => {

            task.machines[machine].rollsRender = [] // Обнуляем массив для отображаемых рулонов
            task.machines[machine].collapsed = true // Сворачиваем рулоны по умолчанию

            const findMachine = Object.keys(FABRIC_MACHINES).find(key => FABRIC_MACHINES[key].TITLE === machine)
            task.machines[machine].machineName = findMachine ? FABRIC_MACHINES[findMachine].NAME : '' // Название машины

            // по ОПП
            task.machines[machine].rolls = task.machines[machine].rolls.sort((a, b) => a.roll_position - b.roll_position)
            // по позиции
            task.machines[machine].rolls.forEach(roll => {

                roll.rolls_exec = roll.rolls_exec.sort((a, b) => a.position - b.position)

                roll.rolls_exec.forEach(roll_exec => {
                    // добавляем поле для хранения оригинального статуса рулона
                    const workRoll: IRollExecExtended = {...roll_exec, storedStatus: roll_exec.status}
                    task.machines[machine].rollsRender.push(workRoll)
                })

            })

            task.machines[machine].rollsRender = task.machines[machine].rollsRender.sort((a, b) => a.position - b.position)
        })
    })
}


// __ Проверяет, есть ли данные для отображения
const hasData = (taskData: ITaskDataItem) => Object.values(taskData.machines).some(machine => machine.rollsRender.length > 0)


// __ Выбираем даты для отображения и посылаем запрос на получение данных
const selectDates = async (period: ITaskPeriod) => {
    tasksPeriod = period
    await getTasks()
    prepareTasksData()                              // Подготавливаем СЗ для отображения

    console.log('tasks: ', tasks)
    console.log('taskData: ', tasksData.value)
}


onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getFabrics()                              // Получаем список ПС
            getTasksPeriod()
            await getTasks()
            prepareTasksData()                              // Подготавливаем СЗ для отображения

            console.log('tasks: ', tasks)
            console.log('taskData: ', tasksData.value)

        },
        undefined,
        // false,
    )
    isLoading.value = false
})

</script>


<style scoped>

</style>

