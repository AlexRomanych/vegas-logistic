<template>

    <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">

        <!-- __ Меню с кнопками управления записями -->
        <TheTaskRecordsMenu
            :machine="machine"
            :task="task"
            @add-roll="addRoll"
            @optimize-labor="optimizeLabor"
            @save-rolls-order="saveRollsOrder"
        />

        <div v-if="rolls.length">
            <!-- __ Разделительная линия -->
            <TheDividerLine/>

            <!-- __ Заголовки таблицы для записей с рулонами -->
            <TheTaskRecordsTitle/>

            <!--:move="evt => !evt.draggedContext.element.isTuning && evt.draggedContext.element.editable"-->
            <div :class="fabricsStore.globalOrderManageChangeFlag ? 'opacity-50' : ''">
                <!-- __ Сами рулоны с возможностью перетаскивания -->
                <draggable
                    :="dragOptions"
                    :disabled="!isDragging"
                    :list="rolls"
                    :move="checkMove"
                    item-key="id"
                    tag="div"
                    @end="changeRollsPosition"
                    @start="checkForDrag"
                >
                    <template #item="{ element, index }">

                        <div>
                            <TheTaskRecord
                                :key="index"
                                :index="index"
                                :machine="machine"
                                :roll="element"
                                :task-status="task.common.status as unknown as TaskStatusUnionType"
                                @save-task-record="saveTaskRecord"
                                @delete-task-record="deleteTaskRecord"
                            />
                        </div>

                    </template>
                </draggable>
            </div>

            <!-- __ Разделительная линия -->
            <TheDividerLine/>

            <!-- __ Общий комментарий к сменному заданию -->
            <div class="flex items-end">
                <AppInputTextArea
                    id="comment"
                    v-model="taskDescription"
                    :disabled="!getFunctionalByFabricTaskStatus(task.common.status)"
                    :placeholder="
                        !getFunctionalByFabricTaskStatus(task.common.status)? '' : 'Введите комментарий'"
                    :rows="2"
                    :value="taskDescription ?? ''"
                    class="cursor-pointer"
                    height="min-h-[60px]"
                    label="Комментарий к сменному заданию на этой стегальной машине:"
                    text-size="normal"
                    width="w-[955px]"
                />

                <!-- __ Кнопка сохранения комментария к сменному заданию -->
                <AppLabel
                    v-if="
                        task.common.status !== FABRIC_TASK_STATUS.DONE.CODE &&
                        task.common.status !== FABRIC_TASK_STATUS.PENDING.CODE
                    "
                    align="center"
                    class="cursor-pointer"
                    height="h-[60px]"
                    text="💾"
                    text-size="huge"
                    type="success"
                    width="w-[50px]"
                    @click="updateTaskMachineDescription"
                />
            </div>

            <!-- __ Показываем, если статус "Готов к стежке", "Выполняется" и "Выполнено"-->
            <div v-if="!getFunctionalByFabricTaskStatus(task.common.status)">
                <!-- __ Разделительная линия -->
                <TheDividerLine/>

                <!-- __ Список рулонов -->
                <TheTaskRecordRolls :rolls="rolls"/>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'

import draggable from 'vuedraggable'

import type { FabricMachineTitles, IRoll, ITaskItem, TaskStatusUnionType } from '@/types'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    type IConstFabricMachine,
    FABRIC_TASK_STATUS, FABRICS_NULLABLE,
    NEW_ROLL,
    // FABRIC_MACHINES,
} from '@/app/constants/fabrics.js'
import {
    getFunctionalByFabricTaskStatus, getProductivityValueByRoll,
    // filterFabricsByMachineId,
    // getAddFabricMode,
} from '@/app/helpers/manufacture/helpers_fabric.js'
import { cloneDeep } from '@/app/helpers/helpers_lib.js'


import TheTaskRecordsMenu
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_manage/TheTaskRecordsMenu.vue'
import TheTaskRecordsTitle
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_manage/TheTaskRecordsTitle.vue'
import TheTaskRecord
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_manage/TheTaskRecord.vue'
import TheTaskRecordRolls
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_manage/TheTaskRecordRolls.vue'
import TheDividerLine
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/TheDividerLine.vue'
import AppInputTextArea from '@/components/ui/inputs/AppInputTextArea.vue'
import AppLabel from '@/components/ui/labels/AppLabel.vue'

// import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'


interface IProps {
    task: ITaskItem
    machine: IConstFabricMachine
}

const props = defineProps<IProps>()

// console.log('task from machine: ', props.task)
// console.log('machine from machine: ', props.machine)

const emits = defineEmits<{
    (e: 'addRoll', newRoll: IRoll, machine: IConstFabricMachine, task: ITaskItem): void,
    (e: 'optimizeLabor', machine: IConstFabricMachine, task: ITaskItem): void,
    (e: 'changeRollsPosition', machine: IConstFabricMachine, task: ITaskItem): void,
    (e: 'saveRollsPosition', machine: IConstFabricMachine, task: ITaskItem): void,
    (e: 'saveRollsOrder', machine: IConstFabricMachine, task: ITaskItem): void,
    (e: 'deleteTaskRecord', payload: IRoll & { machine: IConstFabricMachine, task: ITaskItem }): void,
    (e: 'saveTaskRecord', payload: {
        index: number,
        roll: IRoll,
        machine: IConstFabricMachine,
        task: ITaskItem,
        taskDescription: string | null
    }): void
    (e: 'saveMachineDescription', payload: {
        machine: IConstFabricMachine,
        task: ITaskItem,
        taskDescription: string | null
    }): void
}>()


const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory
const {globalRollsIndexes, globalEditMode } = storeToRefs(fabricsStore)

let rollsCopy: IRoll[]

// __ Опции для draggable
const dragOptions = computed(() => {
    return {
        animation: 300,
        group: 'description',
        ghostClass: 'ghost',
        sort: true,
        // disabled: false, // Выносим в отдельное свойство
    }
})

// __ Проверяем, можно ли менять порядок рулонов
const isDragging = ref(false)
const checkDruggable = () => {
    // console.log('checkDruggable: start')

    if (props.task.common.status !== FABRIC_TASK_STATUS.CREATED.CODE) return false

    const nullRoll = props.task.machines[props.machine.TITLE].rolls.find(roll => !roll.isTuning && roll.fabric_id === FABRICS_NULLABLE.id)
    if (nullRoll) return false

    if (fabricsStore.globalEditMode) return false// Если режим редактирования, то не меняем порядок рулонов

    return true
}


// __ Проверка возможности перетаскивания рулона
const checkMove = (evt: any) => {
    // console.log('roll: ', evt.draggedContext.element)
    // console.log('tuning: ', evt.draggedContext.element.isTuning)
    // console.log('editable: ', evt.draggedContext.element.editable)
    // console.log(!evt.draggedContext.element.isTuning && evt.draggedContext.element.editable)

    return !evt.draggedContext.element.isTuning &&
        evt.draggedContext.element.editable
}

const rolls = ref<IRoll[]>([])

// attract: Тут функционал, который дополняет функционал уникальности выбора ПС из выпадающего списка
const getRollsIndexes = () => {
    // attract: добавляем в нулевую ПС индекс текущей СМ, для того, чтобы она отображалась в выпадающем списке
    fabrics[0].machines[0].id = props.machine.ID // Добавляем ID машины в объект ПС с нулевым рулоном
    rolls.value = props.task.machines[props.machine.TITLE].rolls // Получаем рулоны из задания

    const rollsIndexes = rolls.value
        .map((roll) => (roll.editable ? roll.fabric_id : undefined))
        .filter((roll) => roll !== undefined)

    // console.log('rollsIndexes: ', rollsIndexes)

    globalRollsIndexes.value = rollsIndexes // сохраняем индексы рулонов в глобальном хранилище
    // fabricsStore.globalRollsIndexes.value = rollsIndexes // сохраняем индексы рулонов в глобальном хранилище
    // attract: Получаем индексы рулонов, для того, чтобы их потом исключить из выбора ПС в самой записи
    return rollsIndexes
}
const rollsIndexes = ref(getRollsIndexes())
// const rollsIndexes = computed(() => rolls.map(roll => roll.editable ? roll.fabric_id : undefined).filter(roll => roll !== undefined))  // Получаем индексы рулонов, для того, чтобы их потом исключить из выбора ПС в самой записи

// attract: ---------------------------------------------------------------

globalEditMode.value = false // устанавливаем в false глобальный режим редактирования
// fabricsStore.globalEditMode = false // устанавливаем в false глобальный режим редактирования

// __ Заполняем глобальный массив производительности в хранилище
const fillGlobalProductivity = () => {
    fabricsStore.clearTaskGlobalProductivity()
    rolls.value.forEach((roll, index, rolls) => {

        fabricsStore.globalTaskProductivity[props.machine.TITLE as FabricMachineTitles][index] = {
            time: getProductivityValueByRoll(roll),
            isTuning: roll.isTuning ?? false
        }

        // const fabric = fabrics.find((fabric) => fabric.id === roll.fabric_id)

        // globalTaskProductivity[props.machine.TITLE][index] = fabric.buffer.productivity
        // fabricsStore.globalTaskProductivity[props.machine.TITLE as FabricMachineTitles].push({
        //     time: getProductivityValueByRoll(roll),
        //     isTuning: roll.isTuning
        // })

        //     ? (fabric.buffer.average_length * roll.rolls_amount) / fabric.buffer.productivity
        //     : 0

        // console.log(fabric, roll.rolls_amount)
        // console.log(fabric.buffer.productivity ? fabric.buffer.average_length * roll.rolls_amount : 0)
    })

    // console.log('fillGlobalProductivity: ', fabricsStore.globalTaskProductivity[props.machine.TITLE as FabricMachineTitles])
    // debugger
}

// __ Общий комментарий к сменному заданию
const taskDescription = ref(props.task.machines[props.machine.TITLE].description)

// __ Начало перетаскивания
const checkForDrag = () => {
    rollsCopy = cloneDeep(props.task.machines[props.machine.TITLE].rolls)   // Сохраняем копию рулонов
}

// __ Меняем позицию рулонов в СЗ
const changeRollsPosition = () => {
    // console.log('rollsCopy: ', rollsCopy)
    // console.log('props.task.machines[props.machine.TITLE].rolls: ', props.task.machines[props.machine.TITLE].rolls)

    let findChanges = false
    for (let i = 0; i < rollsCopy.length; i++) {
        if (rollsCopy[i].roll_position !== props.task.machines[props.machine.TITLE].rolls[i].roll_position) {
            findChanges = true
            break
        }
    }

    if (!findChanges) return

    // console.log('from Machine: changeRollsPosition')

    fabricsStore.globalOrderManageChangeFlag = true // устанавливаем флаг для изменения порядка в глобальном хранилище
    emits('changeRollsPosition', props.machine, props.task)     // Меняем порядок рулонов в СЗ
    emits('saveRollsPosition', props.machine, props.task)       // Сохраняем порядок рулонов в СЗ

    // console.log('rolls: ', props.task.machines[props.machine.TITLE].rolls)
}


// __ Добавляем новый рулон
const addRoll = () => {
    console.log('addRoll: machine')
    // Передаем в родительский компонент новый рулон, стегальную машину и само задание как контекст
    emits('addRoll', NEW_ROLL, props.machine, props.task)
}

// __ Оптимизируем трудозатраты
const optimizeLabor = () => {
    emits('optimizeLabor', props.machine, props.task)
}

// __ Сохраняем порядок рулонов (Всплывающее по кнопке "Сохранить порядок")
const saveRollsOrder = () => {
    emits('saveRollsOrder', props.machine, props.task)
}

// __ Сохраняем запись
const saveTaskRecord = (saveData: { index: number, roll: IRoll }) => {
    emits('saveTaskRecord', {
        ...saveData,
        machine: props.machine,
        task: props.task,
        taskDescription: taskDescription.value,
    })
}

// __ Удаляем запись
const deleteTaskRecord = (deleteData: IRoll) => {
    emits('deleteTaskRecord', {...deleteData, machine: props.machine, task: props.task})
}

// __ Обновляем общее описание к СМ
const updateTaskMachineDescription = () => {
    if (!taskDescription.value) return
    // console.log(taskDescription.value)
    emits('saveMachineDescription', {
        machine: props.machine,
        task: props.task,
        taskDescription: taskDescription.value,
    })
}

// __ При изменении самих данных, пересчитываем производительность + возможность перетаскивания
watch(
    () => props.task,
    () => {
        fillGlobalProductivity()
        rollsIndexes.value = getRollsIndexes() // Обновляем индексы рулонов, чтобы потом их исключить из выбора ПС в самой записи
        isDragging.value = checkDruggable()

        // console.log('global productivity: ', fabricsStore.globalTaskProductivity[props.machine.TITLE as FabricMachineTitles])
        // console.log('TaskMachine: Task changed: ', fabricsStore.globalRollsIndexes)
        // console.log('TaskMachine: Task changed: ', props.task)
        // console.log('globalProductivity: TheTaskMachine: ', fabricsStore.globalTaskProductivity)
        // console.log('isDragging: ', isDragging.value)
        // console.log('globalEditMode: ', fabricsStore.globalEditMode)


    },
    {deep: true, immediate: true},
)

// __ Отдельно отслеживаем глобальное редактирование
watch(() => globalEditMode, () => isDragging.value = checkDruggable(), {deep: true, immediate: true})
// watch(() => fabricsStore.globalEditMode, () => isDragging.value = checkDruggable(), {deep: true, immediate: true})

</script>

<style scoped>

.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}

</style>
