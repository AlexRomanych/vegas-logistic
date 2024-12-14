<template>
    <div class="flex justify-start items-end">
        <div>
            <AppInputDate
                id="start"
                label="Начало"
                type="light"
                @getInputDate="setPeriod"
            />
        </div>

        <div>
            <AppInputDate
                id="end"
                label="Окончание"
                type="light"
                @getInputDate="setPeriod"
            />
        </div>
        <div>
            <AppButton
                id="apply"
                title="Показать"
                type="dark"
                width="w-[150px]"
                @buttonClick="clickApply"
            />
        </div>




    </div>

    <TheOrdersBag v-if="orders.length" :key="Date.now()"
    />


</template>

<script setup>
import {ref} from 'vue'
import {useOrdersStore} from "@/src/stores/OrdersStore"
import {compareDatesLogic} from "@/src/app/helpers/helpers_date.js"
import {isResponseWithError} from "@/src/app/helpers/helpers_checks.js"

import TheOrdersBag from '@/src/components/dashboard/orders/components/TheOrdersBag.vue'

import AppInputDate from '@/src/components/ui/inputs/AppInputDate.vue'
import AppButton from '@/src/components/ui/buttons/AppButton.vue'


const ordersStore = useOrdersStore()

const dateInterval = {}
const orders = ref([])

// watch(ordersLength, (newValue, oldValue) => {
//     console.log('Count changed from', oldValue, 'to', newValue);
// });

const setPeriod = (pointDate) => {
    // console.log(pointDate.id, pointDate.value)

    if (pointDate.id === 'start') {
        dateInterval.start = pointDate.value
    } else if (pointDate.id === 'end') {
        dateInterval.end = pointDate.value
    }
}

const clickApply = async (id) => {

    // подготавливаем данные
    const formattedDate = new Date().toISOString().slice(0, 10)  // дата в формате YYYY-MM-DD
    dateInterval.start = dateInterval.start ?? formattedDate
    dateInterval.end = dateInterval.end ?? formattedDate

    // инвертируем, если дата начала больше даты окончания
    if (!compareDatesLogic(dateInterval.start, dateInterval.end)) {
        const start = dateInterval.start
        dateInterval.start = dateInterval.end
        dateInterval.end = start
    }

    // получаем данные
    const ordersStore = useOrdersStore()
    orders.value = await ordersStore.getOrders(dateInterval)

    // todo Сделать вывод ошибки, если сервер криво ответил
    if (isResponseWithError(orders.value)) {
        orders.value.data = []
    }

    console.log(orders.value)

}


</script>

<style scoped>

</style>
