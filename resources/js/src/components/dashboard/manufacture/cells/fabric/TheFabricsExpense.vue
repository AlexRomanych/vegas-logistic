<template>

    <div class="m-2 sticky top-0 flex bg-blue-200 border-2 rounded-lg border-blue-700 p-1 max-w-fit">

        <div class="flex">

            <div>

                <!-- attract: Шапка -->
                <div class="flex">

                    <AppLabelMultiLine
                        :text="['Полотно', 'стеганное']"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        height="h-[30px]"
                        type="primary"
                        width="w-[284px]"
                    />

                    <AppLabelMultiLine
                        :text="['Буф.', 'м.п.']"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        height="h-[30px]"
                        type="primary"
                        width="w-[60px]"
                    />

                    <AppLabelMultiLine
                        :text="['Расх.', 'м.п.']"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        height="h-[30px]"
                        type="primary"
                        width="w-[60px]"
                    />

                    <AppLabelMultiLine
                        :text="['Δ', 'м.п.']"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        height="h-[30px]"
                        type="primary"
                        width="w-[60px]"
                    />

                    <AppLabelMultiLine
                        :text="['СЗ', '']"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        height="h-[30px]"
                        type="primary"
                        width="w-[60px]"
                    />
                </div>

                <!-- attract: Навигация -->
                <div class="flex">

                    <div v-for="item in navigation" :key="item.link" class="w-full mr-1 cursor-pointer ">

                        <router-link :to="{name: item.link}">
                            <AppLabel
                                class="underline"
                                :text="item.title"
                                :type="item.type"
                                align="center"
                                height="h-[44px]"
                                text-size="mini"
                                width="w-full"
                            />
                        </router-link>

                    </div>

                </div>

            </div>


            <div>

                <AppLabel
                    align="center"
                    class="border-2 rounded-lg border-blue-700"
                    text="Заявки"
                    type="primary"
                    width="w-full"
                />

                <!-- attract: Блок с названиями заявок -->
                <div class="flex">

                    <div v-for="orderExpense in ordersExpense">

                        <!-- attract: Сама заявка -->
                        <AppLabelMultiLine
                            :text="[orderExpense.client.short_name, '№ ' + orderExpense.order_no, orderExpense.expense_date]"
                            :type="orderExpense.active ? 'warning' : 'dark'"
                            align="center"
                            class="cursor-pointer"
                            height="h-[15px]"
                            textSize="micro"
                            title="Всплывающая подсказка"
                            width="w-[100px]"
                            @click="addOrRemoveExpenseToCalc(orderExpense)"
                        />

                        <!-- attract: Блок сервисных кнопок -->
                        <div class="flex">

                            <!-- attract: +/- в расчетах -->
                            <AppLabel
                                :text="orderExpense.active ? '-' : '+'"
                                :type="orderExpense.active ? 'dark' : 'warning'"
                                align="center"
                                class="cursor-pointer"
                                height="h-[20px]"
                                width="w-full"
                                @click="addOrRemoveExpenseToCalc(orderExpense)"
                            />

                            <!-- attract: Убрать из расчета -->
                            <AppLabel
                                align="center"
                                class="cursor-pointer"
                                height="h-[20px]"
                                text="x"
                                type="danger"
                                width="w-full"
                                @click="closeOrderExpense(orderExpense)"
                            />
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- attract: Начало вывода расчета -->
    <div class="m-2">

        <!-- attract: Группировка по машинам -->
        <div v-for="machine in machines">

            <div class="flex">

                <!-- attract: Кнопка раскрытия -->
                <AppLabel
                    :text="machine.show ? '-' : '+'"
                    align="center"
                    class="cursor-pointer"
                    type="info"
                    width="w-[30px]"
                    @click="toggleMachineVisibility(machine)"
                />

                <!-- attract: Сама машина -->
                <AppLabel
                    :text="machine.name"
                    class="cursor-pointer"
                    text-size="normal"
                    type="info"
                    width="w-[512px]"
                    @click="toggleMachineVisibility(machine)"
                />

            </div>

            <div v-if="machine.show" class="flex ml-[34px]">

                <div>
                    <div v-for="fabricItem in ordersExpenseMatrix">

                        <div v-if="machine.id === fabricItem.fabric.machine" class="flex">

                            <!-- attract: Полотно стеганное -->
                            <AppLabel
                                :text="fabricItem.fabric.display_name"
                                :type="fabricItem.fabric.correct ? 'primary' : 'danger'"
                                align="left"
                                class="cursor-pointer"
                                textSize="micro"
                                title="Всплывающая подсказка"
                                width="w-[255px]"
                            />

                            <!-- attract: Буфер -->
                            <AppLabel
                                :text="fabricItem.fabric.buffer.toFixed(3)"
                                :type="getAmountWarningStatus(fabricItem.fabric.buffer, fabricItem.fabric.maxBuffer)"
                                align="center"
                                textSize="micro"
                                title="Всплывающая подсказка"
                                width="w-[60px]"
                            />

                            <!-- attract: Расход -->
                            <AppLabel
                                :text="fabricItem.expenseTotal ? fabricItem.expenseTotal.toFixed(3) : ''"
                                :type="fabricItem.expenseTotal ? 'warning' : 'light'"
                                align="center"
                                textSize="micro"
                                title="Всплывающая подсказка"
                                width="w-[60px]"
                            />

                            <!-- attract: Δ -->
                            <AppLabel
                                :text="fabricItem.delta.toFixed(3)"
                                :type="getAmountWarningStatus(fabricItem.delta, fabricItem.fabric.maxBuffer)"
                                align="center"
                                textSize="micro"
                                title="Всплывающая подсказка"
                                width="w-[60px]"
                            />

                            <!-- attract: СЗ -->
                            <AppLabel
                                :text="fabricItem.fabric.correct ? (fabricItem.taskContexts.length ? 'есть СЗ' : '--> СЗ') : ''"
                                :type="fabricItem.fabric.correct ? (fabricItem.taskContexts.length ? 'info' : 'warning') : 'danger'"
                                align="center"
                                class="cursor-pointer"
                                textSize="micro"
                                title="Всплывающая подсказка"
                                width="w-[60px]"
                                @click="fabricTaskAdd(fabricItem)"
                            />

                            <!-- attract: Расход по каждой заявке -->
                            <div class="ml-0.5 flex">

                                <div v-for="fabricExpense in fabricItem.expense">

                                    <AppLabel
                                        :text="fabricExpense.expense ? fabricExpense.expense.toFixed(3) : ''"
                                        :type="fabricExpense.expense ? (fabricExpense.active ? 'warning' : 'dark') : 'light'"
                                        align="center"
                                        class="border-2 rounded-lg border-blue-700"
                                        height="h-[30px]"
                                        textSize="mini"
                                        title="Всплывающая подсказка"
                                        width="w-[100px]"
                                    />

                                </div>

                            </div>

                        </div>

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

</template>

<script setup>

import {ref} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_MACHINES} from '/resources/js/src/app/constants/fabrics.js'

import {round} from '/resources/js/src/app/helpers/helpers_lib.js'
import {getISOFromLocaleDate} from '/resources/js/src/app/helpers/helpers_date.js'
import {getAmountWarningStatus} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '/resources/js/src/components/ui/modals/AppModalAsyncMultiline.vue'


const fabricsStore = useFabricsStore()

// attract: Получаем все активные полотна с API
const fabrics = await fabricsStore.getFabrics(true)

// attract: Получаем расходы на заявки
const getOrdersExpense = async () => await fabricsStore.getFabricsOrders()
const ordersExpense = ref(await getOrdersExpense())

// attract: Получаем все незакрытые СЗ, выставленные ОПП (FabricTaskContext)
const getTaskContexts = async () => await fabricsStore.getFabricTaskContextNotDone()
const taskContexts = ref(await getTaskContexts())


// console.log('fabrics: ', fabrics)
// console.log('ordersExpense: ', ordersExpense.value)
// console.log('taskContexts: ', taskContexts.value)

// TODO: Сделать проверку на присутствие расхода в расходах и отсутствия ПС в списке активных

// attract: Создаем матрицу отображения расхода по заявкам
const getOrdersExpenseMatrix = () => {
    const tempMatrix = []

    fabrics.forEach(fabric => {

        // attract: формируем массив расходов по каждой заявке для рендеринга в шаблоне
        const tempExpense = []
        let expenseTotal = 0

        ordersExpense.value.forEach(orderExpense => {

            const tempFabricExpense = orderExpense.fabricsExpense.find(expense => expense.fabric_id === fabric.id)
            const tempFabricExpenseAmount = tempFabricExpense ? tempFabricExpense.expense : 0

            tempExpense.push({
                expense: tempFabricExpenseAmount,
                active: orderExpense.active
            })
            expenseTotal += orderExpense.active ? tempFabricExpenseAmount : 0   // суммируем расходы по активным заявкам
        })


        // attract: подготавливаем инфу для кнопки СЗ
        const tempTaskContexts = taskContexts.value.filter(taskContext => taskContext.fabric_id === fabric.id)

        // attract: Подготавливаем объект ПС для рендеринга в шаблоне
        const fabricData = {
            fabric: {
                id: fabric.id,
                display_name: fabric.display_name,
                buffer: fabric.buffer.amount,
                maxBuffer: fabric.buffer.average_length * fabric.buffer.max_rolls,
                machine: fabric.machines[0].id,
                correct: fabric.correct,
            },

            taskContexts: tempTaskContexts,

            expense: tempExpense,
            expenseTotal: round(expenseTotal, 3),
            // expenseTotal: round(tempExpense.reduce((accumulator, currentValue) => accumulator + (orderExpense.active ? currentValue : 0), 0), 3),

            get delta() {
                return this.fabric.buffer - this.expenseTotal
            }
        }

        tempMatrix.push(fabricData)
    })

    return tempMatrix
}
let ordersExpenseMatrix = ref(getOrdersExpenseMatrix())
// console.log('ordersExpenseMatrix: ', ordersExpenseMatrix.value)


// attract: Получаем все машины для отображения и формируем объект для отображения
const getMachines = () => {

    const tempMachines = []

    Object.keys(FABRIC_MACHINES).forEach((machine) => {

        if (FABRIC_MACHINES[machine].ID !== 0) {

            // console.log(FABRIC_MACHINES[machine].NAME)
            tempMachines.push({
                id: FABRIC_MACHINES[machine].ID,
                name: FABRIC_MACHINES[machine].NAME,
                show: true,
            })
        }
    })

    // console.log(tempMachines)
    return tempMachines
}
const machines = ref(getMachines())


// attract: Объект навигации для отображения на странице
const navigation = [
    {title: 'Управление СЗ', link: 'manufacture.cell.fabric.tasks.manage', type: 'info'},
    {title: 'Выполнение СЗ', link: 'manufacture.cell.fabric.tasks.execute', type: 'info'},
    {title: 'Список ПС', link: 'manufacture.cell.fabrics.show', type: 'info'},
    {title: 'Учет рулонов', link: 'manufacture.cell.fabrics.movement', type: 'info'},
]


// attract: Получаем ссылку на модальное для подтверждений окно с асинхронной функцией
const appModalAsync = ref(null)
const modalText = ref([])
const modalType = ref('danger')


// attract: +/- в расчетах по заявкам. При изменении пересчитываем расходы по заявкам
// const calcButtonText = ref('')
const addOrRemoveExpenseToCalc = async (orderExpense) => {
    orderExpense.active = !orderExpense.active
    ordersExpenseMatrix.value = getOrdersExpenseMatrix()
    const res = await fabricsStore.setFabricOrderActive(orderExpense.id, orderExpense.active)
}


// attract: Закрываем заявку. При изменении пересчитываем расходы по заявкам
const closeOrderExpense = async (orderExpense) => {
    modalText.value = ['Заявка будет закрыта.', 'Продолжить?']
    modalType.value = 'danger'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        ordersExpense.value = ordersExpense.value.filter(expense => expense !== orderExpense)
        ordersExpenseMatrix.value = getOrdersExpenseMatrix()
        const res = await fabricsStore.closeFabricOrder(orderExpense.id)
    }
}


// attract: Добавляем СЗ по ПС и его расходу
const fabricTaskAdd = async (fabricItem) => {

    if (!fabricItem.fabric.correct) return      // если ткань неправильная, то выходим
    if (fabricItem.taskContexts.length) return  // если есть движение по СЗ, то выходим

    modalText.value = ['Полотно стеганное', fabricItem.fabric.display_name, 'будет добавлено в СЗ.', 'Продолжить?']
    modalType.value = 'warning'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
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
        const res = await fabricsStore.createContextExpense(fabricTaskContext)
        // console.log('res: ', res)

        // attract: Получаем обновленные данные для обновления матрицы
        taskContexts.value = await getTaskContexts()
        ordersExpenseMatrix.value = getOrdersExpenseMatrix()

    }

}


// attract: Управляем видимостью группы машин
const toggleMachineVisibility = (machine) => {
    machine.show = !machine.show
}


// Получаем ширину ячейки в нужном формате
// const getMachineColWidthCSS = (colWidth) => `w-[${colWidth}px]`

// const machineColWidth = ref(getMachineColWidthCSS(378))         // 378 под стегальную машину (указываем динамический стиль в <style> для загрузки)
//
// const dateColWidth = ref(getMachineColWidthCSS(48))             // под дату
// const leftDateColWidth = ref(getMachineColWidthCSS(50))         // под дату слева
// const actionsColWidth = ref(getMachineColWidthCSS(150))          // под действия
//
// const fabricColWidth = ref(getMachineColWidthCSS(250))          // название ПС
// const amountRollsColWidth = ref(getMachineColWidthCSS(30))      // кол-во в рулонах
// const amountMetersColWidth = ref(getMachineColWidthCSS(40))     // кол-во в погонных метрах
// const laborsColWidth = ref(getMachineColWidthCSS(30))           // трудозатраты


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

</style>
