<template>

    <div v-if="!isLoading" class="m-2 sticky top-0 flex bg-blue-200 border-2 rounded-lg border-blue-700 p-1 max-w-fit">

        <div class="flex">

            <div>

                <!-- __ Навигация -->
                <div class="flex">

                    <div v-for="item in navigation" :key="item.link" class="w-full mr-1 cursor-pointer ">

                        <router-link :to="{name: item.link}">
                            <AppLabel
                                :align="render.link.headerAlign"
                                :height="render.link.height"
                                :text="item.title"
                                :text-size="render.link.headerTextSize"
                                :type="item.type"
                                :width="render.link.width"
                                class="underline"
                            />
                        </router-link>

                    </div>

                </div>

                <!-- __ Шапка -->
                <div class="flex">

                    <!-- __ Полотно стеганное -->
                    <AppLabelMultiLine
                        v-if="render.fabric.show"
                        :align="render.fabric.headerAlign"
                        :height="render.fabric.height"
                        :text="render.fabric.header"
                        :text-size="render.fabric.headerTextSize"
                        :type="typeof render.fabric.type === 'function' ? render.fabric.type() : render.fabric.type"
                        :width="render.fabric.width"
                        class="header-item"
                    />

                    <!-- __ Буфер -->
                    <AppLabelMultiLine
                        v-if="render.buffer.show"
                        :align="render.buffer.headerAlign"
                        :height="render.buffer.height"
                        :text="render.buffer.header"
                        :text-size="render.buffer.headerTextSize"
                        :type="typeof render.buffer.type === 'function' ? render.buffer.type() : render.buffer.type"
                        :width="render.buffer.width"
                        class="header-item"
                    />

                    <!-- __ Незавершенное пр-во -->
                    <AppLabelMultiLine
                        v-if="render.shouldBuffer.show"
                        :align="render.shouldBuffer.headerAlign"
                        :height="render.shouldBuffer.height"
                        :text="render.shouldBuffer.header"
                        :text-size="render.shouldBuffer.headerTextSize"
                        :type="typeof render.shouldBuffer.type === 'function' ? render.shouldBuffer.type() : render.shouldBuffer.type"
                        :width="render.shouldBuffer.width"
                        class="header-item"
                    />

                    <!-- __ Расход -->
                    <AppLabelMultiLine
                        v-if="render.expense.show"
                        :align="render.expense.headerAlign"
                        :height="render.expense.height"
                        :text="render.expense.header"
                        :text-size="render.expense.headerTextSize"
                        :type="typeof render.expense.type === 'function' ? render.expense.type() : render.expense.type"
                        :width="render.expense.width"
                        class="header-item"
                    />

                    <!-- __ Δ -->
                    <AppLabelMultiLine
                        v-if="render.delta.show"
                        :align="render.delta.headerAlign"
                        :height="render.delta.height"
                        :text="render.delta.header"
                        :text-size="render.delta.headerTextSize"
                        :type="typeof render.delta.type === 'function' ? render.delta.type() : render.delta.type"
                        :width="render.delta.width"
                        class="header-item"
                    />

                    <!-- __ СЗ -->
                    <AppLabelMultiLine
                        v-if="render.tasks.show"
                        :align="render.tasks.headerAlign"
                        :height="render.tasks.height"
                        :text="render.tasks.header"
                        :text-size="render.tasks.headerTextSize"
                        :type="typeof render.tasks.type === 'function' ? render.tasks.type() : render.tasks.type"
                        :width="render.tasks.width"
                        class="header-item"
                    />
                </div>

            </div>

            <div>

                <!-- __ Надпись "Заявки" -->
                <AppLabel
                    align="center"
                    class="header-item"
                    text="Заявки"
                    type="primary"
                    width="w-full"
                />

                <!-- __ Блок с названиями заявок + Draggable -->
                <draggable
                    :="dragOptions"
                    :list="ordersExpense"
                    :move="checkForDrag"
                    class="flex"
                    item-key="id"
                    tag="div"
                    @end="changeOrdersPosition"
                    @start="startDrag"
                >
                    <!--suppress VueUnrecognizedSlot -->
                    <template #item="{ element, /*index*/ }">
                        <div>

                            <!-- __ Сама заявка -->
                            <AppLabelMultiLine
                                v-if="render.orderExpense.show"
                                :align="render.orderExpense.headerAlign"
                                :text="[element.client.short_name, '№ ' + element.order_no, element.expense_date]"
                                :text-size="render.orderExpense.headerTextSize"
                                :type="element.active ? 'warning' : 'dark'"
                                :width="render.orderExpense.width"
                                class="cursor-pointer"
                                height="h-[15px]"
                                title="Заявка"
                                @click="addOrRemoveExpenseToCalc(element)"
                            />

                            <!-- __ Блок сервисных кнопок -->
                            <div class="flex">

                                <!-- __ +/- в расчетах -->
                                <AppLabel
                                    :text="element.active ? '-' : '+'"
                                    :type="element.active ? 'dark' : 'warning'"
                                    align="center"
                                    class="cursor-pointer"
                                    height="h-[20px]"
                                    width="w-full"
                                    @click="addOrRemoveExpenseToCalc(element)"
                                />

                                <!-- __ Убрать из расчета -->
                                <AppLabel
                                    align="center"
                                    class="cursor-pointer"
                                    height="h-[20px]"
                                    text="x"
                                    type="danger"
                                    width="w-full"
                                    @click="closeOrderExpense(element)"
                                />
                            </div>

                        </div>
                    </template>
                </draggable>

            </div>

        </div>

    </div>


    <!-- __ Начало вывода расчета -->
    <div class="m-2">

        <!-- __ Группировка по машинам -->
        <div v-for="machine in machines">

            <div class="flex">

                <!-- __ Кнопка раскрытия -->
                <AppLabel
                    :text="machine.show ? '▼' : '▲'"
                    align="center"
                    class="cursor-pointer"
                    text-size="small"
                    type="info"
                    width="w-[30px]"
                    @click="toggleMachineVisibility(machine)"
                />

                <!-- __ Сама машина -->
                <AppLabel
                    :text="machine.name"
                    class="cursor-pointer"
                    text-size="small"
                    type="info"
                    width="w-[625px]"
                    @click="toggleMachineVisibility(machine)"
                />

            </div>

            <div v-if="machine.show" class="flex ml-[34px]">

                <div>
                    <div v-for="fabricItem in ordersExpenseMatrix">

                        <div v-if="machine.id === fabricItem.fabric.machine" class="flex">

                            <!-- __ Полотно стеганное -->
                            <AppLabel
                                v-if="render.fabric.show"
                                :text="fabricItem.fabric.display_name"
                                :type="fabricItem.fabric.correct ? 'primary' : 'danger'"
                                align="left"
                                class="cursor-pointer"
                                textSize="mini"
                                width="w-[305px]"
                            />

                            <!-- __ Буфер -->
                            <AppLabel
                                v-if="render.buffer.show"
                                :align="render.buffer.dataAlign"
                                :text="fabricItem.fabric.buffer.toFixed(PRECISION)"
                                :text-size="render.buffer.dataTextSize"
                                :type="getAmountWarningStatus(fabricItem.fabric.buffer, fabricItem.fabric.maxBuffer)"
                                :width="render.buffer.width"
                            />

                            <!-- __ Незавершенное пр-во -->
                            <AppLabel
                                v-if="render.shouldBuffer.show"
                                :align="render.shouldBuffer.dataAlign"
                                :text="fabricItem.fabric.shouldBuffer ? fabricItem.fabric.shouldBuffer.toFixed(PRECISION) : ''"
                                :text-size="render.shouldBuffer.dataTextSize"
                                :type="fabricItem.fabric.shouldBuffer ? 'success' : 'light'"
                                :width="render.shouldBuffer.width"
                            />

                            <!-- __ Расход -->
                            <AppLabel
                                v-if="render.expense.show"
                                :align="render.expense.dataAlign"
                                :text="fabricItem.expenseTotal ? fabricItem.expenseTotal.toFixed(PRECISION) : ''"
                                :text-size="render.expense.dataTextSize"
                                :type="fabricItem.expenseTotal ? 'warning' : 'light'"
                                :width="render.expense.width"
                            />

                            <!-- __ Δ -->
                            <AppLabel
                                :align="render.delta.dataAlign"
                                :text="fabricItem.delta.toFixed(PRECISION)"
                                :text-size="render.delta.dataTextSize"
                                :type="getAmountWarningStatus(fabricItem.delta, fabricItem.fabric.maxBuffer)"
                                :width="render.delta.width"
                            />

                            <!-- __ СЗ -->
                            <AppLabel
                                :align="render.tasks.dataAlign"
                                :text="fabricItem.fabric.correct ? (fabricItem.taskContexts.length ? '✓' : '➕') : ''"
                                :text-size="render.tasks.dataTextSize"
                                :type="fabricItem.fabric.correct ? (fabricItem.taskContexts.length ? 'info' : 'warning') : 'danger'"
                                :width="render.tasks.width"
                                class="cursor-pointer"
                                @click="fabricTaskAdd(fabricItem)"
                            />

                            <!-- __ Расход по каждой заявке -->
                            <div class="ml-0.5 flex">

                                <div v-for="fabricExpense in fabricItem.expense">

                                    <AppLabel
                                        :align="render.orderExpense.dataAlign"
                                        :height="render.orderExpense.height"
                                        :text="fabricExpense.expense ? fabricExpense.expense.toFixed(PRECISION) : ''"
                                        :text-size="render.orderExpense.dataTextSize"
                                        :type="getExpenseCellType(fabricExpense)"
                                        :width="render.orderExpense.width"
                                    />

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- __ Модальное окно для подтверждений -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />

</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, computed } from 'vue'

import draggable from 'vuedraggable'

import type { IFabric, IFabricExpenseOrder, IRenderData } from '@/types'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import { type IMachineKey, FABRIC_MACHINES, FABRIC_TASK_STATUS } from '@/app/constants/fabrics.js'

import { round } from '@/app/helpers/helpers_lib'
import { formatDate, getISOFromLocaleDate } from '@/app/helpers/helpers_date.js'
import { getAmountWarningStatus } from '@/app/helpers/manufacture/helpers_fabric.js'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'
// __ End Loader

// line -----------------------------------------------------------------------------------------------------------
// line ------------- Объявление типов ----------------------------------------------------------------------------
// line -----------------------------------------------------------------------------------------------------------
interface INavigationItem {
    title: string,
    link: string,
    type: string
}

interface IExpenseMachine {
    id: number,
    name: string,
    show: boolean
}

interface IOrderExpenseItem {
    expense: number
    active: boolean
    enough: boolean
}

interface IOrderExpenseFabric {
    buffer: number
    correct: boolean
    display_name: string
    id: number
    machine: number
    maxBuffer: number
    shouldBuffer: number
}

interface ITaskContextItem {
    average_textile_length: number
    average_fabric_length: number
    comment: null | string
    description: null | string
    fabric_id: number
    id: number
    note: null | string
    productivity: number
    roll_position: number
    rolls_amount: number
    task: {
        id: number
        task_status: number
        task_date: string
        fabric_machine_id: number
    }
    translate_rate: number
}

interface IOrdersExpenseMatrixItem {
    delta: number
    expense: IOrderExpenseItem[]
    expenseTotal: number
    fabric: IOrderExpenseFabric
    taskContexts: ITaskContextItem[]
}

// line -----------------------------------------------------------------------------------------------------------

const fabricsStore = useFabricsStore()


// __ Опции для draggable
const dragOptions = computed(() => {
    return {
        animation: 300,
        group: 'description',
        ghostClass: 'ghost',
        sort: true,
        disabled: false,
    }
})


const PRECISION = 2
const DATA_TEXT_SIZE = 'mini'
const HEADER_TEXT_SIZE = 'small'
const HEADER_ALIGN = 'center'
const DATA_ALIGN = 'center'
const CELL_HEIGHT = 'h-[30px]'

// const renderData: IRenderData = {
const render: IRenderData = reactive({
    fabric: {
        header: ['Полотно', 'стеганное'],
        width: 'w-[334px]',
        show: true,
        type: 'primary',
        headerAlign: HEADER_ALIGN,
        dataAlign: 'left',
        height: CELL_HEIGHT,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
    },
    buffer: {
        header: ['Буфер', 'м.п.'],
        width: 'w-[60px]',
        show: true,
        type: 'primary',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        height: CELL_HEIGHT,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
    },
    shouldBuffer: {
        header: ['Нез.пр.', 'м.п.'],
        width: 'w-[60px]',
        show: true,
        type: 'primary',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        height: CELL_HEIGHT,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
    },
    expense: {
        header: ['Расход', 'м.п.'],
        width: 'w-[60px]',
        show: true,
        type: 'primary',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        height: CELL_HEIGHT,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
    },
    delta: {
        header: ['Δ', 'м.п.'],
        width: 'w-[60px]',
        show: true,
        type: 'primary',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        height: CELL_HEIGHT,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
    },
    tasks: {
        header: ['СЗ', ''],
        width: 'w-[60px]',
        show: true,
        type: 'primary',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        height: CELL_HEIGHT,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
    },
    orderExpense: {
        header: '',
        width: 'w-[100px]',
        show: true,
        type: () => 'dark',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        height: CELL_HEIGHT,
        headerTextSize: 'micro',
        dataTextSize: DATA_TEXT_SIZE,
    },
    link: {
        header: '',
        width: 'w-full',
        show: true,
        type: () => 'dark',
        headerAlign: 'center',
        height: 'h-[44px]',
        headerTextSize: 'mini',
    },


})

// __ Маяк лоадера
const isLoading = ref(true)                             // Показываем лоадер

// __ Объект навигации для отображения на странице
const navigation: INavigationItem[] = [
    {title: 'Управление СЗ', link: 'manufacture.cell.fabric.tasks.manage', type: 'info'},
    {title: 'Выполнение СЗ', link: 'manufacture.cell.fabric.tasks.execute', type: 'info'},
    {title: 'Список ПС', link: 'manufacture.cell.fabrics.show', type: 'info'},
    {title: 'Учет рулонов', link: 'manufacture.cell.fabrics.movement', type: 'info'},
]

// __ Получаем ссылку на модальное для подтверждений окно с асинхронной функцией
const appModalAsync = ref(null)
const modalText = ref<string[]>([])
const modalType = ref('danger')
const modalMode = ref('confirm')


// __ Подготавливаем переменные для логики
let fabrics: IFabric[] = []                                            // Получаем все активные полотна с API
const ordersExpense = ref<IFabricExpenseOrder[]>([])            // Получаем расходы на заявки
const taskContexts = ref<ITaskContextItem[]>([])                // Получаем все незакрытые СЗ, выставленные ОПП (FabricTaskContext)
const ordersExpenseMatrix = ref<IOrdersExpenseMatrixItem[]>([]) // Подготавливаем матрицу для отображения расхода по заявкам
const machines = ref<IExpenseMachine[]>([])                     // Получаем все машины для отображения

// __ Получаем все активные полотна с API
const getFabrics = async () => {
    fabrics = await fabricsStore.getFabrics(true)
}

// __ Получаем расходы на заявки + сортируем по позиции
const getOrdersExpense = async () => {
    ordersExpense.value = await fabricsStore.getFabricsOrders()
    ordersExpense.value.sort((a, b) => a.position - b.position)
}

// __ Получаем все незакрытые СЗ, выставленные ОПП (FabricTaskContext)
const getTaskContexts = async () => {
    taskContexts.value = await fabricsStore.getFabricTaskContextNotDone()
}


// TODO: Сделать проверку на присутствие расхода в расходах и отсутствия ПС в списке активных

// __ Создаем матрицу отображения расхода по заявкам
const getOrdersExpenseMatrix = () => {
    const tempMatrix: IOrdersExpenseMatrixItem[] = []

    fabrics.forEach(fabric => {

        // __ подготавливаем инфу для кнопки СЗ
        const tempTaskContexts = taskContexts.value.filter(taskContext => taskContext.fabric_id === fabric.id)

        // __ считаем незавершенное производство
        const shouldBuffer = tempTaskContexts.reduce((accumulator, currentValue) => accumulator + currentValue.average_fabric_length, 0)

        // __ формируем массив расходов по каждой заявке для рендеринга в шаблоне
        const tempExpense: IOrderExpenseItem[] = []

        let expenseTotal = 0                                            // общий расход по заявкам
        let currentOrderBuffer = fabric.buffer.amount + shouldBuffer    // текущий буфер заявки для отображения в шаблоне разными цветами (на сколько заявок хватает)
        let isEnough = true                                             // флаг для отображения в шаблоне, что заявка хватает на сколько ПС в буфере

        ordersExpense.value.forEach(orderExpense => {

            const tempFabricExpense = orderExpense.fabricsExpense.find(expense => expense.fabric_id === fabric.id)
            const tempFabricExpenseAmount = tempFabricExpense ? tempFabricExpense.expense : 0

            if (orderExpense.active) {
                isEnough = currentOrderBuffer >= tempFabricExpenseAmount
                currentOrderBuffer -= tempFabricExpenseAmount
            }

            tempExpense.push({
                expense: tempFabricExpenseAmount,
                active: orderExpense.active,
                enough: isEnough,
            })
            expenseTotal += orderExpense.active ? tempFabricExpenseAmount : 0   // суммируем расходы по активным заявкам
        })


        // __ Подготавливаем объект ПС для рендеринга в шаблоне
        const fabricData = {
            fabric: {
                id: fabric.id,
                display_name: fabric.display_name,
                buffer: fabric.buffer.amount,
                shouldBuffer: shouldBuffer,
                maxBuffer: fabric.buffer.average_length * fabric.buffer.max_rolls,
                machine: fabric.machines[0].id,
                correct: fabric.correct,
            },

            taskContexts: tempTaskContexts,

            expense: tempExpense,
            expenseTotal: round(expenseTotal, PRECISION),
            // expenseTotal: round(tempExpense.reduce((accumulator, currentValue) => accumulator + (orderExpense.active ? currentValue : 0), 0), PRECISION),
            shouldBuffer: round(shouldBuffer, PRECISION),

            get delta() {
                return this.fabric.buffer - this.expenseTotal
            },
        }

        tempMatrix.push(fabricData)
    })

    ordersExpenseMatrix.value = tempMatrix
    // return tempMatrix
}


// __ Получаем все машины для отображения и формируем объект для отображения
const getMachines = () => {

    const tempMachines: IExpenseMachine[] = []

    Object.keys(FABRIC_MACHINES).forEach((machine)=> {

        if (FABRIC_MACHINES[machine as IMachineKey].ID !== 0) {
            tempMachines.push({
                id: FABRIC_MACHINES[machine as IMachineKey].ID,
                name: FABRIC_MACHINES[machine as IMachineKey].NAME,
                show: true,
            })
        }
    })

    machines.value = tempMachines
}


// __ +/- в расчетах по заявкам. При изменении пересчитываем расходы по заявкам
const addOrRemoveExpenseToCalc = async (orderExpense: IFabricExpenseOrder) => {
    orderExpense.active = !orderExpense.active
    getOrdersExpenseMatrix()
    /*const res =*/ await fabricsStore.setFabricOrderActive(orderExpense.id, orderExpense.active)
}


// __ Закрываем заявку. При изменении пересчитываем расходы по заявкам
const closeOrderExpense = async (orderExpense: IFabricExpenseOrder) => {
    modalText.value = ['Заявка будет закрыта.', 'Продолжить?']
    modalType.value = 'danger'

    const answer = appModalAsync.value ? await (appModalAsync.value as typeof AppModalAsyncMultiLine).show() : false // показываем модалку и ждем ответ
    if (answer) {
        ordersExpense.value = ordersExpense.value.filter(expense => expense !== orderExpense)
        getOrdersExpenseMatrix()
        /*const res =*/ await fabricsStore.closeFabricOrder(orderExpense.id)
    }
}


// __ Добавляем СЗ по ПС и его расходу
const fabricTaskAdd = async (fabricItem: IOrdersExpenseMatrixItem) => {

    if (!fabricItem.fabric.correct) return      // если ткань неправильная, то выходим
    if (fabricItem.taskContexts.length) {       // если есть движение по СЗ, то выводим модалку
        // console.log('fabricItem:', fabricItem)

        const modalTextInform: string[] = []
        fabricItem.taskContexts.forEach(taskContext => {
            const taskDate = formatDate(new Date(taskContext.task.task_date))
            const taskMachineKey = Object.keys(FABRIC_MACHINES).find(machine => FABRIC_MACHINES[machine as IMachineKey].ID === taskContext.task.fabric_machine_id)
            const taskMachine = taskMachineKey ? FABRIC_MACHINES[taskMachineKey as IMachineKey].NAME : ''
            const taskStatusKey = Object.keys(FABRIC_TASK_STATUS).find(status => FABRIC_TASK_STATUS[status as keyof typeof FABRIC_TASK_STATUS].CODE === taskContext.task.task_status)
            const taskStatus = taskStatusKey ? FABRIC_TASK_STATUS[taskStatusKey as keyof typeof FABRIC_TASK_STATUS].TITLE : ''
            const fabricLength = taskContext.average_fabric_length
            // const fabricLength = taskContext.translate_rate ? taskContext.average_textile_length / taskContext.translate_rate : 0

            modalTextInform.push(
                `СЗ (${taskStatus}): ${taskDate}, ${taskMachine}, ${taskContext.rolls_amount}рул., ${fabricLength.toFixed(PRECISION)}мп.`,
            )
        })

        modalText.value = modalTextInform
        modalType.value = 'warning'
        modalMode.value = 'inform'
        /*const answer =*/ appModalAsync.value ? await (appModalAsync.value as typeof AppModalAsyncMultiLine).show() : false // показываем модалку и ждем ответ

        return
    }

    modalText.value = ['Полотно стеганное', fabricItem.fabric.display_name, 'будет добавлено в СЗ.', 'Продолжить?']
    modalType.value = 'warning'
    modalMode.value = 'confirm'

    const answer = appModalAsync.value ? await (appModalAsync.value as typeof AppModalAsyncMultiLine).show() : false // показываем модалку и ждем ответ
    if (answer) {
        const fabricTaskContext = {
            date: getISOFromLocaleDate(),
            fabric_id: fabricItem.fabric.id,
            fabric_expense: fabricItem.expenseTotal,
            // fabric_delta: fabricItem.delta,
            // fabric_buffer: fabricItem.fabric.buffer,
            // fabric_max_buffer: fabricItem.fabric.maxBuffer,
            // fabric_correct: fabricItem.fabric.correct,
        }

        // TODO: Добавить Callout по добавлению СЗ
        /*const res =*/ await fabricsStore.createContextExpense(fabricTaskContext)

        // __ Получаем обновленные данные для обновления матрицы
        await getTaskContexts()
        getOrdersExpenseMatrix()
    }

}


// __ Получаем тип для рендера расхода в ячейке таблицы (пересечение заявка-ПС)
const getExpenseCellType = (fabricExpense: IOrderExpenseItem) => {
    if (!fabricExpense.expense) return 'light'
    if (!fabricExpense.active) return 'dark'
    return fabricExpense.enough ? 'success' : 'orange'
    // :type="fabricExpense.expense ? (fabricExpense.active ? 'warning' : 'dark') : 'light'"
}

// __ Начало перетаскивания
const startDrag = () => {
}

// __ Перетаскивание
const checkForDrag = () => true

// __ Сортируем по позиции и сохраняемся
const changeOrdersPosition = async () => {
    if (!ordersExpense.value.length || ordersExpense.value.length === 1) return

    const orders = ordersExpense.value.map(order => order.position) // Получаем массив позиций заявок
    orders.sort((a, b) => a - b) // Сортируем по возрастанию))

    ordersExpense.value.forEach((order, index) => {
        order.position = orders[index]
    })
    const mappedOrders = ordersExpense.value.map(order => ({order_id: order.id, position: order.position}))
    /*const res =*/ await fabricsStore.saveFabricsOrdersOrder(mappedOrders)

    // Обновляем матрицу
    await getOrdersExpense()
    getOrdersExpenseMatrix()
}


// __ Управляем видимостью группы машин
const toggleMachineVisibility = (machine: IExpenseMachine) => {
    machine.show = !machine.show
}


onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getFabrics()
            await getOrdersExpense()
            await getTaskContexts()
            getMachines()
            getOrdersExpenseMatrix()
            // console.log('fabrics: ', fabrics)
            // console.log('ordersExpense: ', ordersExpense.value)
            // console.table(ordersExpense.value)
            console.log('taskContexts: ', taskContexts.value)
            // console.log('machines: ', machines.value)
            console.log('ordersExpenseMatrix: ', ordersExpenseMatrix.value)
        },
        undefined,
        // false,
    )
    isLoading.value = false

})

</script>

<style scoped>

.header-item {
    @apply border-2 rounded-lg border-blue-700;
}

</style>
