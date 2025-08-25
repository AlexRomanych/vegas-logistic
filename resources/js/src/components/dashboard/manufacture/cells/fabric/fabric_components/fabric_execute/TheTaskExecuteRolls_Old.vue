<template>

    <!-- __ Заголовок таблицы выполняемых рулонов -->
    <TheTaskExecuteRollsHeader
        :rollsRender="rollsRender"
    />

    <!-- __ Сам список рулонов -->




    <!--<div :class="fabricsStore.globalOrderManageChangeFlag ? 'opacity-50' : ''">-->
    <!--    &lt;!&ndash; __ Сами рулоны с возможностью перетаскивания &ndash;&gt;-->
    <!--    <draggable-->
    <!--        :="dragOptions"-->
    <!--        :disabled="!isDragging"-->
    <!--        :list="rolls"-->
    <!--        item-key="id"-->
    <!--        :move="evt => evt.draggedContext.element.editable"-->
    <!--        tag="div"-->
    <!--        @end="changeRollsPosition"-->
    <!--        @start="checkForDrag"-->
    <!--    >-->
    <!--        <template #item="{ element, index }">-->

    <!--            <div>-->
    <!--                <TheTaskRecord-->
    <!--                    :key="index"-->
    <!--                    :index="index"-->
    <!--                    :machine="machine"-->
    <!--                    :roll="element"-->
    <!--                    :task-status="task.common.status"-->
    <!--                    @save-task-record="saveTaskRecord"-->
    <!--                    @delete-task-record="deleteTaskRecord"-->
    <!--                />-->
    <!--            </div>-->

    <!--        </template>-->
    <!--    </draggable>-->
    <!--</div>-->












    <div v-for="(roll, index) in rolls" :key="index">

        <div v-for="(roll_exec) in roll.rolls_exec" :key="roll_exec.id">

            <!--<div-->
            <!--    :class="roll_exec.active ? 'pt-0.5 pb-0.5 bg-blue-400 rounded-lg' : ''"-->
            <!--    class="cursor-pointer">-->

            <!-- __ Сам рулон  -->
            <TheTaskExecuteRoll
                :class="roll_exec.active ? 'pt-0.5 pb-0.5 bg-blue-400 rounded-lg' : ''"
                :roll_exec="roll_exec"
                :rollsRender="rollsRender"
                class="cursor-pointer"
                @click="changeActiveRoll(roll_exec)"
                @change-calc-status="changeCalcStatus(roll_exec)"
            />

            <!--</div>-->

        </div>

    </div>

</template>

<script setup>
import { reactive, ref, watch } from 'vue'

import draggable from 'vuedraggable'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import { FABRIC_MACHINES, FABRIC_ROLL_STATUS, FABRIC_ROLL_STATUS_LIST } from '@/app/constants/fabrics.js'

import {
    formatTimeWithLeadingZeros,
    formatDateAndTimeInShortFormat,
} from '@/app/helpers/helpers_date.js'

import TheTaskExecuteRollsHeader
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteRollsHeader.vue'
import TheTaskExecuteRoll
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteRoll.vue'
import TheTaskRecord
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_manage/TheTaskRecord.vue'



// import AppLabel from '@/components/ui/labels/AppLabel.vue'
// import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'


const props = defineProps({
    rolls: {
        type: Array,
        required: false,
        default: () => ([])
    },
    machine: {
        type: Object,
        required: false,
        default: () => FABRIC_MACHINES.AMERICAN,
    },
    execute: {                      // Режим отображения (в режиме демонстрации или в режиме выполнения)
        type: Boolean,
        required: false,
        default: true
    },
    workers: {
        type: Array,
        required: false,
        default: () => ([])
    }
})

const emits = defineEmits(['add-execute-roll', 'changeCalcStatus'])






// __ Передаем в родительский компонент изменение калькулятора
const changeCalcStatus = (roll_exec) => {
    emits('changeCalcStatus', roll_exec, props.machine)
}


// __ Получаем данные из хранилища по ПС
const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory

// __ Получаем список ответственных за выполнение
const getResponsibleWorkers = () => fabricsStore.globalSelectWorkers
const responsibleWorkers = ref(getResponsibleWorkers())

// attract: Сортируем рулоны по позиции
const sortExecRollsByPosition = () => {
    props.rolls.forEach((roll) => {
        roll.rolls_exec.sort((a, b) => a.position - b.position)
    })
}
sortExecRollsByPosition()

// console.log(props.rolls)
// attract: Добавляем флаг активности
const resetActiveFlag = () => {
    props.rolls.forEach((roll) => {
        roll.rolls_exec.forEach((roll_exec) => {
            roll_exec['active'] = false
        })
    })
}
resetActiveFlag()

// attract: Устанавливаем активным первый элемент - активный рулон
let activeRoll = props.rolls[0].rolls_exec[0]
if (activeRoll !== undefined) activeRoll['active'] = true // устанавливаем активным первый элемент, если массив не пустой
fabricsStore.globalActiveRolls[props.machine.TITLE] = activeRoll

console.log('props.rolls: ', props.rolls)


const FABRIC_ROLL_STATUS_ARRAY = Object.values(FABRIC_ROLL_STATUS_LIST)
console.log(FABRIC_ROLL_STATUS_ARRAY)


// attract: Определяем, что в рулонах будет полная инфа
fabricsStore.globalExecuteRollsInfo = true

// attract: Получаем ПС по ID
// const getFabricById = (fabric_id) => fabrics.find((fabric) => fabric.id === fabric_id)

// attract: Получаем тип раскраски в зависимости от статуса выполнения рулона
const getTypeByStatus = (roll_exec) => {
    return 'dark'
}

// attract: Получаем тип шапки таблицы
const getHeaderType = () => 'primary'

// attract: Задаем глобальный объект для унификации отображения рулонов
const rollsRender = reactive({
    position: {
        width: 'w-[60px]',
        show: true,
        title: '№ п/п',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => roll_exec.position.toString()
    },
    isCalc: {
        width: 'w-[60px]',
        show: true,
        title: '+ / -',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        type: (roll_exec) => roll_exec.isCalc ? 'success' : 'danger',
        data: (roll_exec) => (roll_exec.isCalc ? '✓' : '✗'),
    },
    rollNumber: {
        width: 'w-[60px]',
        show: true,
        title: '№ рулона',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => roll_exec.id.toString()
    },
    fabricName: {
        width: 'w-[290px]',
        show: true,
        title: 'Название ПС',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => fabrics.find((fabric) => fabric.id === roll_exec.fabric_id).display_name
    },
    textileLength: {
        width: 'w-[50px]',
        show: true,
        title: 'Средняя длина ткани, м.п.',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => roll_exec.textile_length.toFixed(2)
    },
    fabricLength: {
        width: 'w-[50px]',
        show: true,
        title: 'Средняя длина ПС, м.п.',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => (roll_exec.textile_length / roll_exec.rate).toFixed(2)
    },
    rollsAmount: {
        width: 'w-[30px]',
        show: false,
        title: 'Кол-во рулонов, шт.',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: () => '1'
    },
    productivity: {
        width: 'w-[90px]',
        show: true,
        title: 'Средние трудозатраты, ч.',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => formatTimeWithLeadingZeros(roll_exec.textile_length / roll_exec.productivity, 'hour')
    },
    description: {
        width: props.execute ? 'w-[200px]' : 'w-[100px]',
        show: true,
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        title: (roll_exec) => roll_exec.descr ?? 'Комментарий',
        data: (roll_exec) => roll_exec.descr
    },
    status: {
        width: 'w-[100px]',
        show: true,
        title: 'Статус',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => FABRIC_ROLL_STATUS_ARRAY.find((fabricRollStatus) => fabricRollStatus.CODE === roll_exec.status).TITLE
    },
    startAt: {
        width: 'w-[125px]',
        show: props.execute,
        title: 'Начало стегания рулона',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => roll_exec.start_at ? formatDateAndTimeInShortFormat(roll_exec.start_at) : ''
    },
    finishAt: {
        width: 'w-[125px]',
        show: props.execute,
        title: 'Окончание стегания рулона',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => roll_exec.finish_at ? formatDateAndTimeInShortFormat(roll_exec.finish_at) : ''
    },
    rollTime: {
        width: 'w-[90px]',
        show: props.execute,
        title: 'Время стегания',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => 'Not used'
    },
    finishBy: {
        width: 'w-[150px]',
        show: props.execute,
        title: 'Ответственный',
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        data: (roll_exec) => {
            const responsibleWorkersCopy = JSON.parse(JSON.stringify(responsibleWorkers.value))

            // console.log('rol: ', responsibleWorkersCopy)

            const responsibleWorker =
                responsibleWorkersCopy.data.find((responsibleWorker) => responsibleWorker.id === roll_exec.finish_by)
            if (responsibleWorker) {
                responsibleWorker.selected = true
            } else {
                responsibleWorkersCopy.data[0].selected = true
            }
            return responsibleWorkersCopy
        }
    },
    reason: {
        width: props.execute ? 'w-[200px]' : 'w-[100px]',
        show: props.execute,
        headerTextSize: 'mini',
        dataTextSize: 'mini',
        align: 'center',
        title: (roll_exec) => roll_exec.false_reason ?? 'Причина не выполнения',
        data: (roll_exec) => roll_exec.false_reason
    },
})

// attract: Обработчик активного рулона (подсвечиваем активный рулон)
const changeActiveRoll = (roll_exec) => {
    resetActiveFlag()
    activeRoll = roll_exec
    activeRoll.active = true
    fabricsStore.globalActiveRolls[props.machine.TITLE] = activeRoll
    // console.log(fabricsStore.globalActiveRolls)
}

//hr------------------------------------------------------------------------
// info: Отслеживание элементов управления состоянием выполнения рулона
const toggleFabricExecuteInfo = () => {
    rollsRender.description.show = !rollsRender.description.show
    // rollsRender.finishBy.show = !rollsRender.finishBy.show
    rollsRender.reason.show = !rollsRender.reason.show
}

// attract: Отслеживаем изменение значения в хранилище по отображению инфы о рулонах
watch(() => fabricsStore.globalExecuteRollsInfo, (newValue) => {
    toggleFabricExecuteInfo(newValue)
})

// attract: Отслеживаем изменение Изменить комментарий
watch(() => fabricsStore.globalExecuteRollChangeDescription, async (newValue) => {
    activeRoll.descr = fabricsStore.globalExecuteRollChangeDescriptionText
    const res = await fabricsStore.updateExecuteRoll(activeRoll)
})

// attract: Отслеживаем изменение Изменить длину ткани
watch(() => fabricsStore.globalExecuteRollChangeTextile, async (newValue) => {
    activeRoll.textile_length = fabricsStore.globalExecuteRollChangeTextileLength
    const res = await fabricsStore.updateExecuteRoll(activeRoll)
})


// attract: Отслеживаем изменение "Не выполнено"/"Создано"
watch(() => fabricsStore.globalExecuteMarkRollFalse, async (newState) => {

    if (activeRoll.status === FABRIC_ROLL_STATUS.CREATED.CODE) {
        activeRoll.status_prev = activeRoll.status
        activeRoll.status = FABRIC_ROLL_STATUS.FALSE.CODE
        activeRoll.false_reason = fabricsStore.globalExecuteMarkRollFalseReason
        fabricsStore.globalExecuteMarkRollFalseReason = ''
    } else if (activeRoll.status === FABRIC_ROLL_STATUS.FALSE.CODE) {
        activeRoll.status_prev = activeRoll.status
        activeRoll.status = FABRIC_ROLL_STATUS.CREATED.CODE
        activeRoll.false_reason = ''
        fabricsStore.globalExecuteMarkRollFalseReason = ''
    }

    const res = await fabricsStore.updateExecuteRoll(activeRoll)
    // console.log(res)

})


// attract: Отслеживаем изменение "Отменено"/"Создано"
watch(() => fabricsStore.globalExecuteMarkRollCancel, async (newState) => {

    if (activeRoll.status === FABRIC_ROLL_STATUS.CREATED.CODE) {
        activeRoll.status_prev = activeRoll.status
        activeRoll.status = FABRIC_ROLL_STATUS.CANCELLED.CODE
        activeRoll.false_reason = fabricsStore.globalExecuteMarkRollCancelReason
        fabricsStore.globalExecuteMarkRollCancelReason = ''
    } else if (activeRoll.status === FABRIC_ROLL_STATUS.CANCELLED.CODE) {
        activeRoll.status_prev = activeRoll.status
        activeRoll.status = FABRIC_ROLL_STATUS.CREATED.CODE
        activeRoll.false_reason = ''
        fabricsStore.globalExecuteMarkRollCancelReason = ''
    }

    const res = await fabricsStore.updateExecuteRoll(activeRoll)
    // console.log(res)

})


// attract: Отслеживаем изменение маркировки переходящего рулона
watch(() => fabricsStore.globalExecuteMarkRollRolling, async (newState) => {

    if (activeRoll.status === FABRIC_ROLL_STATUS.PAUSED.CODE) {
        activeRoll.status_prev = activeRoll.status
        activeRoll.status = FABRIC_ROLL_STATUS.ROLLING.CODE
        activeRoll.false_reason = fabricsStore.globalExecuteMarkRollFalseReason
        fabricsStore.globalExecuteMarkRollFalseReason = ''
        activeRoll.rolling = true
    } else if (activeRoll.status === FABRIC_ROLL_STATUS.ROLLING.CODE) {
        activeRoll.status_prev = activeRoll.status
        activeRoll.status = FABRIC_ROLL_STATUS.PAUSED.CODE
        activeRoll.false_reason = fabricsStore.globalExecuteMarkRollFalseReason
        fabricsStore.globalExecuteMarkRollFalseReason = ''
        activeRoll.rolling = false
    }

    const res = await fabricsStore.updateExecuteRoll(activeRoll)
})

// attract: Переменная-флаг нажатия кнопки "Начать выполнение" рулона
watch(() => fabricsStore.globalStartExecuteRoll, async (newValue) => {
    if (activeRoll.status !== FABRIC_ROLL_STATUS.CREATED.CODE) return   // если статус != "Создан"
    fabricsStore.globalStartExecuteRoll = false                         // сбрасываем значение флага
    activeRoll.status_prev = activeRoll.status
    activeRoll.status = FABRIC_ROLL_STATUS.RUNNING.CODE                 // меняем статус на "Выполняется"
    activeRoll.start_at = new Date()
    activeRoll.paused_at = null
    activeRoll.resume_at = null
    activeRoll.finish_at = null
    activeRoll.duration = 0

    // console.log('activeRoll.start_at: ', activeRoll.start_at)

    const res = await fabricsStore.updateExecuteRoll(activeRoll)
})

// attract: Переменная-флаг нажатия кнопки "Приостановить выполнение" рулона
watch(() => fabricsStore.globalPauseExecuteRoll, async (newValue) => {
    if (activeRoll.status !== FABRIC_ROLL_STATUS.RUNNING.CODE) return   // если статус != "Выполняется"
    fabricsStore.globalPauseExecuteRoll = false                         // сбрасываем значение флага
    activeRoll.status_prev = activeRoll.status
    activeRoll.status = FABRIC_ROLL_STATUS.PAUSED.CODE                  // меняем статус на "Приостановлено"
    activeRoll.paused_at = new Date()

    if (activeRoll.resume_at === null) {
        activeRoll.duration = (new Date(activeRoll.paused_at)).getTime() - (new Date(activeRoll.start_at)).getTime()
    } else {
        activeRoll.duration += (new Date(activeRoll.paused_at)).getTime() - (new Date(activeRoll.resume_at)).getTime()
    }

    const res = await fabricsStore.updateExecuteRoll(activeRoll)
})

// attract: Переменная-флаг нажатия кнопки "Возобновить выполнение" рулона
watch(() => fabricsStore.globalResumeExecuteRoll, async (newValue) => {
    if (activeRoll.status !== FABRIC_ROLL_STATUS.PAUSED.CODE) return   // если статус != "Приостановлено"
    fabricsStore.globalResumeExecuteRoll = false                       // сбрасываем значение флага
    activeRoll.status_prev = activeRoll.status
    activeRoll.status = FABRIC_ROLL_STATUS.RUNNING.CODE                // меняем статус на "Выполняется"
    activeRoll.resume_at = new Date()

    const res = await fabricsStore.updateExecuteRoll(activeRoll)
})

// attract: Переменная-флаг нажатия кнопки "Закончить выполнение" рулона
watch(() => fabricsStore.globalFinishExecuteRoll, async (newValue) => {
    if (activeRoll.finish_by === 0) return                              // если ответственный за выполнение рулона не назначен
    if (activeRoll.status !== FABRIC_ROLL_STATUS.RUNNING.CODE) return   // если статус != "Выполняется"
    fabricsStore.globalFinishExecuteRoll = false                        // сбрасываем значение флага
    activeRoll.status_prev = activeRoll.status
    activeRoll.status = FABRIC_ROLL_STATUS.DONE.CODE                    // меняем статус на "Выполнено"
    activeRoll.finish_at = new Date()

    if (activeRoll.resume_at === null) {
        activeRoll.duration = (new Date(activeRoll.finish_at)).getTime() - (new Date(activeRoll.start_at)).getTime()
    } else {
        activeRoll.duration += (new Date(activeRoll.finish_at)).getTime() - (new Date(activeRoll.resume_at)).getTime()
    }

    const res = await fabricsStore.updateExecuteRoll(activeRoll)
})

// attract: Переменная-флаг нажатия кнопки "Выбрать ответственного" за выполнение рулона
watch(() => fabricsStore.globalSelectWorkerFlag, async (newValue) => {
    if (activeRoll.status === FABRIC_ROLL_STATUS.DONE.CODE) return      // если статус = "Выполнено" - не меняем, потому что будет задвоение буфера
    if (activeRoll.status === FABRIC_ROLL_STATUS.CREATED.CODE) return   // если статус = "Создан"
    fabricsStore.globalSelectWorkerFlag = false                         // сбрасываем значение флага
    activeRoll.finish_by = fabricsStore.globalSelectWorkerId

    const res = await fabricsStore.updateExecuteRoll(activeRoll)
})

// attract: Переменная-флаг нажатия кнопки "Добавить рулон"
watch(() => fabricsStore.globalExecuteRollAdd, async (newValue) => {

    if (!fabricsStore.globalExecuteRollAdd) return                      // для избежания рекурсивного вызова

    fabricsStore.globalExecuteRollAdd = false                           // сбрасываем значение флага
    const addingRoll = fabricsStore.globalExecuteRollAddData
    console.log('addingRoll: ', addingRoll)

    emits('add-execute-roll', {...addingRoll, falseReason: fabricsStore.globalExecuteRollAddReason})
    // const res = await fabricsStore.updateExecuteRoll(activeRoll)
})

</script>

<style scoped>

</style>
