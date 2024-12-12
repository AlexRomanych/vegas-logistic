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


</template>

<script setup>
import {useOrdersStore} from "@/src/stores/OrdersStore";
import {compareDatesLogic} from "@/src/app/helpers/helpers_date.js"
import AppInputDate from '@/src/components/ui/inputs/AppInputDate.vue'
import AppButton from '@/src/components/ui/buttons/AppButton.vue'

const ordersStore = useOrdersStore()

const dateInterval = {}

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


    const ordersStore = useOrdersStore()
    const data = await ordersStore.getOrders(dateInterval)

    console.log(data)
    // console.log(dateInterval)

}


// function setPeriod(pointDate) {
//
// }


// ordersStore.orders = [1,2,3]
//
// console.log(ordersStore.orders)

</script>

<style scoped>

</style>
