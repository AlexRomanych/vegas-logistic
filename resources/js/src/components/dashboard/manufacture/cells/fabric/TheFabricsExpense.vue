<template>

    <div class="m-2 sticky top-0 flex bg-blue-200 border-2 rounded-lg border-blue-700 p-1 max-w-fit">
        <!--    <div class="ml-2 mt-2 sticky top-0">-->

        <div class="flex">

            <!--        <div class="sticky top-0 flex pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit">-->
            <div>
                <div class="flex">
                    <AppLabel
                        :width="'w-[250px]'"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        text="Полотно стеганное"
                        type="primary"
                    />

                    <AppLabel
                        :width="'w-[60px]'"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        text="Буф."
                        type="primary"
                    />

                    <AppLabel
                        :width="'w-[60px]'"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        text="Расх."
                        type="primary"
                    />

                    <AppLabel
                        :width="'w-[60px]'"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        text="Δ"
                        type="primary"
                    />

                    <AppLabel
                        :width="'w-[60px]'"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        text="СЗ"
                        type="primary"
                    />
                </div>


                <AppLabel
                    :width="'w-[505px]'"
                    align="center"
                    class="border-2 rounded-lg border-blue-700"
                    height="h-[50px]"
                    text="Заявки:"
                    type="info"
                />

            </div>

            <div>

                <AppLabel
                    align="center"
                    class="border-2 rounded-lg border-blue-700"
                    text="Заявки"
                    type="primary"
                    width="w-full"
                />


                <div class="flex">

                    <AppLabelMultiLine
                        v-for="orderExpense in ordersExpense"
                        :text="[orderExpense.client.short_name, '№ ' + orderExpense.order_no, orderExpense.expense_date]"
                        align="center"
                        class="border-2 rounded-lg border-blue-700"
                        height="h-[15px]"
                        textSize="micro"
                        title="Всплывающая подсказка"
                        type="warning"
                        width="w-[100px]"
                    />

                </div>
            </div>


        </div>


    </div>

    <!-- Заглушка -->
    <!--    <div class="flex">-->

    <!--        <AppLabel-->
    <!--            :width="'w-[512px]'"-->
    <!--            align="center"-->
    <!--            class="border-2 rounded-lg border-blue-700"-->
    <!--            height="h-[50px]"-->
    <!--            text="Заявки:"-->
    <!--            type="info"-->
    <!--        />-->


    <!--    </div>-->
    <div class="m-2">

        <div class="flex">

            <div>

                <div v-for="fabricItem in ordersExpenseMatrix">

                    <div class="flex">

                        <!-- attract: Полотно стеганное -->
                        <AppLabel
                            :text="fabricItem.fabric.display_name"
                            :width="'w-[255px]'"
                            align="left"
                            class="cursor-pointer"
                            textSize="micro"
                            title="Всплывающая подсказка"
                            type="primary"
                        />

                        <!-- attract: Буфер -->
                        <AppLabel
                            :text="fabricItem.fabric.buffer.toFixed(3)"
                            :type="getAmountWarningStatus(fabricItem.fabric.buffer, fabricItem.fabric.maxBuffer)"
                            :width="'w-[60px]'"
                            align="center"
                            textSize="micro"
                            title="Всплывающая подсказка"
                        />

                        <!-- attract: Расход -->
                        <AppLabel
                            :text="fabricItem.expenseTotal.toFixed(3)"
                            :width="'w-[60px]'"
                            align="center"
                            textSize="micro"
                            title="Всплывающая подсказка"
                            type="warning"
                        />

                        <!-- attract: Δ -->
                        <AppLabel
                            :text="fabricItem.delta.toFixed(3)"
                            :type="getAmountWarningStatus(fabricItem.delta, fabricItem.fabric.maxBuffer)"
                            :width="'w-[60px]'"
                            align="center"
                            textSize="micro"
                            title="Всплывающая подсказка"
                        />

                        <!-- attract: СЗ -->
                        <AppLabel

                            :width="'w-[60px]'"
                            align="center"
                            text="СЗ"
                            textSize="micro"
                            type="info"
                        />

                        <!-- attract: СЗ -->
                        <div class="ml-0.5 flex">

                            <div v-for="fabricExpense in fabricItem.expense" >

                                <AppLabel


                                    :text="fabricExpense ? fabricExpense.toFixed(3) : ''"
                                    :type="fabricExpense ? 'dark' : 'light'"
                                    :width="'w-[100px]'"
                                    align="center"
                                    class="border-2 rounded-lg border-blue-700"
                                    height="h-[30px]"
                                    textSize="micro"
                                    title="Всплывающая подсказка"
                                />

                            </div>
                        </div>
                    </div>

                </div>

            </div>


        </div>

    </div>

    <!--    </div>-->


</template>

<script setup>

import {computed, ref, watch} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_MACHINES} from '/resources/js/src/app/constants/fabrics.js'

import {round} from '/resources/js/src/app/helpers/helpers_lib.js'
import {getAmountWarningStatus} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'


const fabricsStore = useFabricsStore()

// attract: Получаем все полотна с API
const fabrics = await fabricsStore.getFabrics()

// attract: Получаем расходы на заявки
const getOrdersExpense = async () => await fabricsStore.getFabricsOrders()
const ordersExpense = ref(await getOrdersExpense())

console.log('fabrics: ', fabrics)
console.log('ordersExpense: ', ordersExpense.value)

// attract: Создаем матрицу отображения расхода по заявкам
const getOrdersExpenseMatrix = () => {
    const tempMatrix = []

    // Object.keys(FABRIC_MACHINES).forEach((machine) => {
    //
    //     console.log(FABRIC_MACHINES[machine].NAME)
    //
    // })

    // ordersExpense.value[0].active = false

    fabrics.forEach(fabric => {

        const tempExpense = []

        let tempFabricExpenseAmount
        ordersExpense.value.forEach(orderExpense => {

            tempFabricExpenseAmount = 0

            if (orderExpense.active) {
                const tempFabricExpense = orderExpense.fabricsExpense.find(expense => expense.fabric_id === fabric.id)
                tempFabricExpenseAmount = tempFabricExpense ? tempFabricExpense.expense : 0
            }

            tempExpense.push(tempFabricExpenseAmount)
        })


        const fabricData = {
            fabric: {
                id: fabric.id,
                display_name: fabric.display_name,
                buffer: fabric.buffer.amount,
                maxBuffer: fabric.buffer.average_length * fabric.buffer.max_rolls,
                machine: fabric.machines[0].id,
            },

            expense: tempExpense,
            expenseTotal: round(tempExpense.reduce((accumulator, currentValue) => accumulator + currentValue, 0), 3),

            get delta() {
                return this.fabric.buffer - this.expenseTotal
            }
        }

        tempMatrix.push(fabricData)
    })


    return tempMatrix

}
const ordersExpenseMatrix = getOrdersExpenseMatrix()

console.log('ordersExpenseMatrix: ', ordersExpenseMatrix)


// Получаем ширину ячейки в нужном формате
const getMachineColWidthCSS = (colWidth) => `w-[${colWidth}px]`

const machineColWidth = ref(getMachineColWidthCSS(378))         // 378 под стегальную машину (указываем динамический стиль в <style> для загрузки)

const dateColWidth = ref(getMachineColWidthCSS(48))             // под дату
const leftDateColWidth = ref(getMachineColWidthCSS(50))         // под дату слева
const actionsColWidth = ref(getMachineColWidthCSS(150))          // под действия

const fabricColWidth = ref(getMachineColWidthCSS(250))          // название ПС
const amountRollsColWidth = ref(getMachineColWidthCSS(30))      // кол-во в рулонах
const amountMetersColWidth = ref(getMachineColWidthCSS(40))     // кол-во в погонных метрах
const laborsColWidth = ref(getMachineColWidthCSS(30))           // трудозатраты


const roundedRandom = () => Math.round(Math.random() * 10000) / 100


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
