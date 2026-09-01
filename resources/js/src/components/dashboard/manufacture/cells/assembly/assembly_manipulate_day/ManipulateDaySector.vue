<template>

    <!-- __ Табы -->
    <div class="flex m-2">
        <div
            v-for="tab of tabs"
            :key="tab.position"
        >
            <!-- __ Таб: TODO: !!! Доделать крестики и галочки на выполненных задачах !!!   -->
            <AppLabelMultiLineTS
                v-if="tab.show"
                :line-through="tab.task ? !tab.task.active : false"
                :text="tab.label"
                :title="tab.position !== UNION_TASKS_POSITION ? 'Ctrl + Click - Добавить/Убрать из Объединения СЗ' : null"
                :type="getTabType(tab)"
                :width="MENU_LABEL_WIDTH"
                align="center"
                class="start-group cursor-pointer"
                rounded="4"
                text-size="mini"
                @click.exact="activeTabPosition = tab.position"
                @click.ctrl="setTaskInActive(tab)"
            />
        </div>
    </div>

    <!-- __ Вкладки -->
    <div class="m-2">
        <template v-if="activeTabPosition === infoTabPosition">
            <!-- __ Общая инфа -->
            <div class="ml-8">
                <ManipulateDayInfo
                    :assembly-day="assemblyDay!"
                    :matrix="matrix"
                    :sector="sector"
                />
            </div>
        </template>
        <template v-else-if="activeTabPosition === personalTabPosition">
            <!-- __ Персонал -->
            <div class="ml-8">
                <ManipulatePersonal
                    :assembly-day="assemblyDay!"
                    :can-edit="true "
                    @add-worker="addWorker"
                    @add-workers="addWorkers"
                    @remove-worker="removeWorker"
                    @add-responsible="addResponsible"
                />
            </div>
        </template>
        <template v-else>
            <!-- __ Сами СЗ -->
            <div v-for="data of matrix" :key="data.task.id">
                <template v-if="data.task.id === getTab().task!.id">
                    <ManipulateDayTask
                        :data="data"
                        :day="assemblyDay"
                        @set-finish-status="setFinishStatus"
                        @set-false-status="setFalseStatus"
                        @reset-status="resetStatus"
                        @divide-line="divideLine"
                    />
                </template>
            </div>
        </template>
    </div>

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />

</template>

<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue'

import type {
    IAssemblyDay,
    IAssemblySector,
    IAssemblyTask,
    IAssemblyTaskLineSector,
    IAssemblyDayWorker,
    IColorTypes,
    IMatrixManufactureTask
} from '@/types'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'

import { formatDateInFullFormat } from '@/app/helpers/helpers_date'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import ManipulateDayTask from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulateDayTask.vue'
import ManipulateDayInfo from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulateDayInfo.vue'
import ManipulatePersonal from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulatePersonal.vue'


interface ITab {
    show: boolean
    active: boolean
    label: string[]
    position: number
    type: IColorTypes
    typeActive: IColorTypes
    task: IAssemblyTask | null
}

interface IProps {
    sector: IAssemblySector
    matrix: IMatrixManufactureTask[]
    assemblyDay: IAssemblyDay
    activeTaskId: number | null
}

const props = defineProps<IProps>()

const assemblyStore = useAssemblyStore()

console.log('props.matrix: ', props.matrix)
console.log('props.assemblyDay: ', props.assemblyDay)

// __ Константы
// const DEBUG = true
const MENU_LABEL_WIDTH = 'w-[160px]'

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

// __ Показываем сообщение об ошибке
async function showError(error: string | string[] | null = null) {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'

    let renderError = ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    if (typeof error === 'string' && error.length > 0) {
        renderError = [error]
    } else if (Array.isArray(error) && error.length > 0) {
        renderError = error
    }

    modalInfoText.value = renderError
    await appModalAsyncMultiline.value!.show()
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                   Логика                      !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Получаем название СЗ
const getOrderTitle = (task: IAssemblyTask) => {
    if (task.position === 0) {
        return ['Объединение', 'СЗ']
    }
    return [
        `${task.position}. ${task.order.client.short_name} №${task.order.order_no_num}`,
        formatDateInFullFormat(task.order.load_at, true),
    ]
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---    Табы для группировки отображения           !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Организация Tabs
const infoTabPosition      = -2
const personalTabPosition  = -1
const UNION_TASKS_POSITION = 0
const UNION_TASKS_ID       = 0
const activeTabPosition    = ref(infoTabPosition)

const tabs = ref<ITab[]>([])

const setTabs = () => {
    tabs.value = []
    tabs.value.push({
        show      : true,
        label     : ['Инфо', ''],
        position  : infoTabPosition,
        type      : 'dark',
        typeActive: 'info',
        task      : null,
        active    : true,
    })
    tabs.value.push({
        show      : true,
        label     : ['Персонал', ''],
        position  : personalTabPosition,
        type      : 'dark',
        typeActive: 'warning',
        task      : null,
        active    : true,
    })
    props.matrix.forEach(item =>
        tabs.value.push({
            show      : true,
            label     : getOrderTitle(item.task),
            position  : item.task.position,
            type      : item.task.id === UNION_TASKS_ID ? 'orange' : 'dark',
            typeActive: 'primary',
            task      : item.task,
            active    : true,
        })
    )
    tabs.value.sort((a, b) => a.position - b.position)

    // __ Устанавливаем Активное СЗ после перехода
    const findTab = tabs.value.find(tab => tab.task?.id === props.activeTaskId)
    if (findTab) {
        activeTabPosition.value = findTab.position
    }
}

// __ Получаем раскраску Таба
const getTabType = (tab: ITab) => {
    if (tab.task) {
        if (!tab.task.active) {
            return 'danger'
        }
    }

    return activeTabPosition.value === tab.position ? tab.typeActive : tab.type
}

// __ Включаем и выключаем СЗ из Объединенного СЗ
const setTaskInActive = (tab: ITab) => {

    // __ Если Объединенного СЗ - выходим
    if (tab.position === UNION_TASKS_POSITION) {
        return
    }

    // __ Проверяем, что должен остаться как минимум 1 СЗ (2 =  1 СЗ + Объединении СЗ)
    // __ чтобы не получить ошибку в Объединении СЗ
    const count = tabs.value.reduce((acc, tab) => tab.task?.active ? acc + 1 : acc, 0)

    // console.log('count: ', count)

    if (tab && tab.task) {
        if (count === 2 && tab.task.active) {
            return
        }
        tab.task.active = !tab.task.active
    }

    assemblyStore.setAssemblyTaskActive(tab.task!.id, tab.task!.active)
}


// __ Находим id таба по activeTabIndex
const getTab = () => {
    const tab = tabs.value.find(tab => tab.position === activeTabPosition.value)
    if (tab) {
        return tab
    }
    throw new Error('Tab not found')
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---   Функционал для выполнения дня Записи        !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Устанавливаем статус Выполнено IAssemblyTaskLineSector
const setFinishStatus = async (sectorLinesIds: number[]) => {
    const result = (await assemblyStore.setAssemblyTaskLinesSectorDone(sectorLinesIds)) as IAssemblyTaskLineSector[]

    if (checkCRUD(result)) {
        assemblyStore.setAssemblyTaskLinesSectorsToGlobal(result)
    } else {
        await showError()
    }
}

// __ Устанавливаем статус Не Выполнено IAssemblyTaskLineSector
const setFalseStatus = async (sectorLinesIds: number[], falseReason: string) => {
    const result = (await assemblyStore.setAssemblyTaskLinesSectorFalse(sectorLinesIds, falseReason)) as IAssemblyTaskLineSector[]

    if (checkCRUD(result)) {
        assemblyStore.setAssemblyTaskLinesSectorsToGlobal(result)
    } else {
        await showError()
    }
}

// __ Сбрасываем статус IAssemblyTaskLineSector
const resetStatus = async (sectorLinesIds: number[]) => {
    const result = (await assemblyStore.setAssemblyTaskLinesSectorReset(sectorLinesIds)) as IAssemblyTaskLineSector[]

    if (checkCRUD(result)) {
        assemblyStore.setAssemblyTaskLinesSectorsToGlobal(result)
    } else {
        await showError()
    }
}

// __ Разделяем строку
const divideLine = async (taskId: number, assemblyLineId: number, range: { take: number; keep: number }) => {
    //
    // // __ Старый вариант, когда нельзя было разбить в Объединении СЗ
    // // const findTask = assemblyDay.value!.assembly_tasks.find(task => task.id === taskId)
    //
    // // __ Новый вариант, когда можно разбить в Объединении СЗ, в принципе taskId не нужен
    // let findTask: IAssemblyTask | undefined = undefined
    // for (const task of assemblyDay.value!.assembly_tasks) {
    //     for (const line of task.assembly_lines) {
    //         if (line.id === assemblyLineId) {
    //             findTask = task
    //             break
    //         }
    //     }
    //     if (findTask) {
    //         break
    //     }
    // }
    //
    // if (!findTask) {
    //     return // страховка
    // }
    //
    // const dividerElementIndex = findTask.assembly_lines.findIndex(line => line.id === assemblyLineId)
    // const newAssemblyLine        = { ...findTask.assembly_lines[dividerElementIndex] } // __ Копируем объект
    // newAssemblyLine.id           = 0 // __ Устанавливаем новый ID
    // newAssemblyLine.position     = round(newAssemblyLine.position + 0.1, 1) // __ Делаем новую строку ниже текущей позицию с шагом 0.1 (всего 9 разбиений)
    //
    // newAssemblyLine.amount                              = range.take
    // findTask.assembly_lines[dividerElementIndex].amount = range.keep
    //
    // // __ Вставляем новую строку
    // findTask.assembly_lines.splice(dividerElementIndex + 1, 0, newAssemblyLine)
    // findTask.assembly_lines.sort((a, b) => a.position - b.position) // !!! Обязательно
    //
    // const result = await assemblyStore.divideLineInAssemblyTaskPending(findTask, { start: executeDate, end: executeDate })
    //
    // // await assemblyStore.getAssemblyTasks({ start: executeDate, end: executeDate })
    // // await nextTick() // __ Ждем, пока все отрендерится
    // // prepareData()
    // // setTabs()
    // // await nextTick() // __ Ждем, пока все отрендерится
    //
    // // console.log('result: ', result)
    // // console.log('tabs: ', tabs.value)
    // // console.log('activeTabPosition: ', activeTabPosition)
    //
    // if (!checkCRUD(result)) {
    //     await showError()
    // } else {
    //     return
    // }
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Персонал                       !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// !!! Warning!!! --- Тут по-хорошему нужно прокинуть выше или менять глобальный в AssemblyStore

// __ Добавляем работника
const addWorker = (worker: IAssemblyDayWorker) => {
    const existWorker = props.assemblyDay!.workers.find(w => w.id === worker.id)
    if (!existWorker) {
        props.assemblyDay!.workers.push(worker)
    }
}

// __ Добавляем список работников
const addWorkers = (workers: IAssemblyDayWorker[]) => {
    workers.forEach(worker => {
        const existWorker = props.assemblyDay!.workers.find(w => w.id === worker.id)
        if (!existWorker) {
            console.log('push: ', worker)
            props.assemblyDay!.workers.push(worker)
        }
    })
}

// __ Удаляем работника
const removeWorker = (worker: IAssemblyDayWorker) => {
    const findIndex = props.assemblyDay!.workers.findIndex(w => w.id === worker.id)
    if (findIndex !== -1) {
        props.assemblyDay!.workers.splice(findIndex, 1)
        if (props.assemblyDay!.responsible && props.assemblyDay!.responsible.id === worker.id) {
            props.assemblyDay!.responsible = null
        }
    }
}

// __ Добавляем Ответственного
const addResponsible = (worker: IAssemblyDayWorker) => {
    props.assemblyDay!.responsible = worker
}


watch(() => props.matrix, () => {
    console.log('props.matrix: ', props.matrix)
    // console.log('props.sector: ', props.sector.NAME)
    setTabs()
})

onMounted(() => {
    setTabs()
})
</script>

<style scoped>

</style>
