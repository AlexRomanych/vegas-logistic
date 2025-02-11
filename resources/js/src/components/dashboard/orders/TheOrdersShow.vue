<template>
    <div class="flex justify-start items-end ml-3">

        <div>
            <AppLabel
                text="Дата отгрузки: "
                height="h-[35px]"
                width="w-[130px]"
                :bold="true"
            />
        </div>

        <div>
            <AppInputDate
                id="start"
                label="Начало"
                type="light"
                @getInputDate="setPeriod"
            />
        </div>

        <!-- todo Изменить границы дат -->
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

    <div class="ml-3 mt-3">
        <TheOrdersBag v-if="orders.length" :key="Date.now()"
        />
    </div>

    <div v-if="showEmptyMessage"
         class="ml-3 mt-3"
    >
        <AppLabel
            text="Данные не найдены..."
            height="h-[35px]"
            width="w-[250px]"
            type="info"
        />
    </div>

</template>

<script setup>
import {ref} from 'vue'
import {storeToRefs} from 'pinia'
import {useOrdersStore} from "/resources/js/src/stores/OrdersStore"
import {compareDatesLogic} from "/resources/js/src/app/helpers/helpers_date.js"
import {isResponseWithError} from "/resources/js/src/app/helpers/helpers_checks.js"



import TheOrdersBag from '/resources/js/src/components/dashboard/orders/components/TheOrdersBag.vue'

import AppInputDate from '/resources/js/src/components/ui/inputs/AppInputDate.vue'
import AppButton from '/resources/js/src/components/ui/buttons/AppButton.vue'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'


const ordersStore = useOrdersStore()
const {ordersShowIsChanged} = storeToRefs(ordersStore)  // реактивная, меняем в сторе - сохраняет реактивность

const dateInterval = {}
const orders = ref([])

const showEmptyMessage = ref(false)

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


    // console.log(orders.value.length)

    // todo Сделать вывод ошибки, если сервер криво ответил
    if (isResponseWithError(orders.value)) {
        orders.value = []
    }

    // console.log(orders.value)
    ordersShowIsChanged.value = false
    showEmptyMessage.value = orders.value.length === 0

    // console.log(orders.value)

}


</script>

<style scoped>

</style>
