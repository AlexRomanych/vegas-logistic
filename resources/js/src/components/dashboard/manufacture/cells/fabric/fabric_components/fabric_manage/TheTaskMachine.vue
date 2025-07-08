<template>
    <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">
        <!--attract: Меню с кнопками управления записями -->
        <TheTaskRecordsMenu
            :machine="machine"
            :task-status="task.common.status"
            @add-roll="addRoll"
            @optimize-labor="optimizeLabor"
            @save-rolls-order="saveRollsOrder"
        />

        <div v-if="rolls.length">
            <!--attract: Разделительная линия -->
            <TheDividerLine/>

            <!--attract: Заголовки таблицы для записей с рулонами -->
            <TheTaskRecordsTitle/>


            <!--            <div v-for="(roll, index) in rolls">-->

            <!--                &lt;!&ndash; __ Строка с рулонами &ndash;&gt;-->
            <!--                <TheTaskRecord-->

            <!--                    :key="index"-->
            <!--                    :index="index"-->
            <!--                    :machine="machine"-->
            <!--                    :roll="roll"-->
            <!--                    :task-status="task.common.status"-->
            <!--                    @save-task-record="saveTaskRecord"-->
            <!--                    @delete-task-record="deleteTaskRecord"-->
            <!--                />-->
            <!--            </div>-->


            <draggable
                :list="rolls"
                :="dragOptions"
                :disabled="task.common.status !== FABRIC_TASK_STATUS.CREATED.CODE"
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
                            :task-status="task.common.status"
                            @save-task-record="saveTaskRecord"
                            @delete-task-record="deleteTaskRecord"
                        />
                    </div>

                </template>
            </draggable>


            <!--attract: Разделительная линия -->
            <TheDividerLine/>

            <!--attract: Общий комментарий к сменному заданию -->
            <div class="flex items-end">
                <AppInputTextArea
                    id="comment"
                    v-model="taskDescription"
                    :disabled="!getFunctionalByFabricTaskStatus(task.common.status)"
                    :placeholder="
                        !getFunctionalByFabricTaskStatus(task.common.status)? '' : 'Введите комментарий'"
                    :rows="2"
                    :value="taskDescription"
                    class="cursor-pointer"
                    height="min-h-[60px]"
                    label="Комментарий к сменному заданию на этой стегальной машине:"
                    text-size="normal"
                    width="w-[955px]"
                />

                <!--attract: Кнопка сохранения комментария к сменному заданию -->
                <AppLabel
                    v-if="
                        task.common.status !== FABRIC_TASK_STATUS.DONE.CODE &&
                        task.common.status !== FABRIC_TASK_STATUS.PENDING.CODE
                    "
                    align="center"
                    class="cursor-pointer"
                    height="h-[60px]"
                    text="V"
                    text-size="huge"
                    type="success"
                    width="w-[50px]"
                    @click="updateTaskMachineDescription"
                />
            </div>

            <!--attract: Показываем, если статус "Готов к стежке", "Выполняется" и "Выполнено"-->
            <div v-if="!getFunctionalByFabricTaskStatus(task.common.status)">
                <!--attract: Разделительная линия -->
                <TheDividerLine/>

                <!--attract: Список рулонов -->
                <TheTaskRecordRolls :rolls="rolls"/>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, ref, watch} from 'vue'

import draggable from 'vuedraggable'

import {useFabricsStore} from '@/stores/FabricsStore.js'

import {
    FABRIC_MACHINES,
    FABRIC_TASK_STATUS,
    NEW_ROLL,
} from '@/app/constants/fabrics.js'
import {
    getFunctionalByFabricTaskStatus,
    // filterFabricsByMachineId,
    // getAddFabricMode,
} from '@/app/helpers/manufacture/helpers_fabric.js'

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

const props = defineProps({
    task: {
        type: Object,
        required: false,
        default: () => ({}),
    },
    machine: {
        type: Object,
        required: false,
        default: () => FABRIC_MACHINES.AMERICAN,
        validator: (machine) =>
            [
                FABRIC_MACHINES.AMERICAN,
                FABRIC_MACHINES.GERMAN,
                FABRIC_MACHINES.CHINA,
                FABRIC_MACHINES.KOREAN,
            ].includes(machine),
    },
})

// console.log('machine', props.task)

const emits = defineEmits([
    'addRoll',
    'optimizeLabor',
    'saveTaskRecord',
    'deleteTaskRecord',
    'saveMachineDescription',
    'changeRollsPosition',
    'saveRollsOrder',
])

const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory

// __ Опции для draggable
const dragOptions = computed(() => {
    return {
        animation: 300,
        group: 'description',
        // disabled: false, // Выносим в отдельное свойство
        ghostClass: 'ghost',
        sort: true
    };
})
const isDragging = ref(false);

const rolls = ref([])

// attract: Тут функционал, который дополняет функционал уникальности выбора ПС из выпадающего списка
const getRollsIndexes = () => {
    // attract: добавляем в нулевую ПС индекс текущей СМ, для того, чтобы она отображалась в выпадающем списке
    fabrics[0].machines[0].id = props.machine.ID // Добавляем ID машины в объект ПС с нулевым рулоном
    rolls.value = props.task.machines[props.machine.TITLE].rolls // Получаем рулоны из задания

    const rollsIndexes = rolls.value
        .map((roll) => (roll.editable ? roll.fabric_id : undefined))
        .filter((roll) => roll !== undefined)

    fabricsStore.globalRollsIndexes = rollsIndexes // сохраняем индексы рулонов в глобальном хранилище
    // attract: Получаем индексы рулонов, для того, чтобы их потом исключить из выбора ПС в самой записи
    return rollsIndexes
}
const rollsIndexes = ref(getRollsIndexes())
// const rollsIndexes = computed(() => rolls.map(roll => roll.editable ? roll.fabric_id : undefined).filter(roll => roll !== undefined))  // Получаем индексы рулонов, для того, чтобы их потом исключить из выбора ПС в самой записи

// attract: ---------------------------------------------------------------

fabricsStore.globalEditMode = false // устанавливаем в false глобальный режим редактирования

// attract: Заполняем глобальный массив производительности в хранилище
const fillGlobalProductivity = () => {
    fabricsStore.clearTaskGlobalProductivity()
    rolls.value.forEach((roll, index, rolls) => {
        const fabric = fabrics.find((fabric) => fabric.id === roll.fabric_id)
        fabricsStore.globalTaskProductivity[props.machine.TITLE][index] = fabric.buffer.productivity
            ? (fabric.buffer.average_length * roll.rolls_amount) / fabric.buffer.productivity
            : 0
        // console.log(fabric, roll.rolls_amount)
        // console.log(fabric.buffer.productivity ? fabric.buffer.average_length * roll.rolls_amount : 0)
    })
}

// attract: Общий комментарий к сменному заданию
const taskDescription = ref(props.task.machines[props.machine.TITLE].description)


// __ Начало перетаскивания
const checkForDrag = () => {
}

// __ Меняем позицию рулонов в СЗ
const changeRollsPosition = () => {
    if (props.task.common.status !== FABRIC_TASK_STATUS.CREATED.CODE) return    // Только для созданных СЗ
    fabricsStore.globalOrderManageChangeFlag = true // устанавливаем флаг для изменения порядка в глобальном хранилище
    emits('changeRollsPosition', props.machine, props.task)
}


// attract: Добавляем новый рулон
const addRoll = () => {
    // console.log('NEW_ROLL: ', NEW_ROLL)
    // Передаем в родительский компонент новый рулон, стегальную машину и само задание как контекст
    emits('addRoll', NEW_ROLL, props.machine, props.task)
}

// attract: Оптимизируем трудозатраты
const optimizeLabor = () => {
    emits('optimizeLabor', props.machine, props.task)
}

// __ Сохраняем порядок рулонов
const saveRollsOrder = () => {
    // console.log('saveRollsOrder: ')
    emits('saveRollsOrder', props.machine, props.task)
}

// attract: Сохраняем запись
const saveTaskRecord = (saveData) => {
    // console.log(saveData)
    emits('saveTaskRecord', {
        ...saveData,
        machine: props.machine,
        task: props.task,
        taskDescription: taskDescription.value,
    })
}

// attract: Удаляем запись
const deleteTaskRecord = (deleteData) => {
    emits('deleteTaskRecord', {...deleteData, machine: props.machine, task: props.task})
}

// attract: Обновляем общее описание к СМ
const updateTaskMachineDescription = () => {
    if (!taskDescription.value) return

    console.log(taskDescription.value)
    emits('saveMachineDescription', {
        machine: props.machine,
        task: props.task,
        taskDescription: taskDescription.value,
    })
}

// attract: При изменении самих данных, пересчитываем производительность
watch(
    () => props.task,
    () => {
        console.log('TaskMachine: Task changed: ', fabricsStore.globalRollsIndexes)
        // console.log('TaskMachine: Task changed: ', props.task)

        fillGlobalProductivity()
        // console.log('globalProductivity: TheTaskMachine: ', fabricsStore.globalTaskProductivity)

        rollsIndexes.value = getRollsIndexes() // Обновляем индексы рулонов, чтобы потом их исключить из выбора ПС в самой записи
    },
    {deep: true, immediate: true},
)
</script>

<style scoped>

.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}

</style>
