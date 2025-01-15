<template>
    <div class="flex flex-col"> <!--Общий контейнер для всего календаря-->

        <div v-for="(week, weekIdx) in dateIntervalMatrixRender" :key="weekIdx" class="flex flex-row">

            <!--Контейнер для недели-->
            <div v-for="day in week" :key="day.date"
                 class="flex flex-col border-2 rounded-lg mr-3 mb-3 bg-slate-200 border-slate-500 p-2">

                <!--Контейнер для дня-->
                <AppLabel
                    :text="formatDate(day.date) + ' (' + getDayOfWeek(day.date) + ')'"
                    :type="getDayType(day.date)"
                    align="center"
                    textSize="mini"
                    width="w-[278px]"
                />

                <div v-for="order in day.orders">
                    <div class="flex flex-row">

                        <AppLabel
                            :text="order.order.client.short_name"
                            textSize="mini"
                            width="w-[100px]"

                        />

                        <AppLabel
                            :text="'№ ' + getPrettyOrderNumber(order.order.no_num)"
                            textSize="mini"
                            width="w-[70px]"

                        />

                        <AppLabel
                            :text="'∑ ' + getOrderElementsAmount(order.order)"
                            align="center"
                            textSize="mini"
                            width="w-[45px]"

                        />

                        <AppLabel
                            :text="'∑ ' + getOrderTimesAmount(order.order).toFixed(2)"
                            align="center"
                            textSize="mini"
                            type="primary"
                            width="w-[50px]"
                        />

                    </div>

                </div>


                <div class="flex flex-col justify-end h-full">

                    <div class="w-[278px] h-[3px] border-b-2 border-slate-500 justify-self-center ml-0.5 mb-0.5"></div>


                    <div class="flex flex-row">

                        <AppLabel
                            :text="'Всего:'"
                            textSize="mini"
                            type="success"
                            width="w-[174px]"
                        />

                        <AppLabel
                            :text="'∑ ' + getDayOrdersElementsAmount(day)"
                            align="center"
                            textSize="mini"
                            width="w-[45px]"
                        />

                        <AppLabel
                            :text="'∑ ' + getDayOrdersTimesAmount(day).toFixed(2)"
                            align="center"
                            textSize="mini"
                            type="primary"
                            width="w-[50px]"
                        />

                    </div>
                </div>

            </div>

        </div>

    </div>
</template>

<script setup>
import {ref} from 'vue'
import {useCellsSewingStore} from '/resources/js/src/stores/cells/CellsSewingStore.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'

import {
    getDateIntervalMatrix,
    formatDate,
    getDayOfWeek,
    compareDatesLogic,
    isWorkingDay,
    isToday
} from '/resources/js/src/app/helpers/helpers_date.js'

import {
    getPrettyOrderNumber,
    getOrderElementsAmount,
    getOrderTimesAmount,
    getDayOrdersElementsAmount,
    getDayOrdersTimesAmount,
    getMaxOrdersInDay
} from '/resources/js/src/app/helpers/helpers_order.js'

const props = defineProps({
    cellsData: {
        type: Array,
        required: true
    }
})

const sewingStore = useCellsSewingStore()
const dateInterval = sewingStore.dateInterval       // восстанавливаем интервал выборки

// находим максимальное количество заказов в день
const maxOrdersInDay = getMaxOrdersInDay(props.cellsData)

console.log(props.cellsData)
console.log(dateInterval)
console.log(maxOrdersInDay)

// определяем тип label дня, в зависимости от даты
const getDayType = function (inDate) {

    if (isToday(inDate)) return 'warning'

    let resTypeBool =
        compareDatesLogic(dateInterval.start, inDate) && compareDatesLogic(inDate, dateInterval.end)

    resTypeBool ||= compareDatesLogic(dateInterval.start, inDate) === undefined
    resTypeBool ||= compareDatesLogic(inDate, dateInterval.end) === undefined

    if (resTypeBool && !isWorkingDay(inDate)) return 'danger'

    return resTypeBool ? 'info' : 'dark'
}

// attract Возвращаем матрицу календаря ([1..x] x [1..7]), где элемент массива - дата
const dateIntervalMatrix = getDateIntervalMatrix(dateInterval.start, dateInterval.end)

console.log(dateIntervalMatrix)
// console.log({...dateInterval})

// attract Накладываем на матрицу календаря массив с данными о заказах
const dateIntervalMatrixRender = dateIntervalMatrix.map((item, index) => {

    const weekOrders = []

    for (let i = 0; i < item.length; i++) {

        weekOrders[i] = {'date': item[i]}

        let orders = []

        for (let j = 0; j < props.cellsData.length; j++) {


            // if (props.cellsData[j].date.slice(0, 10) === item[i]) {
            //     weekOrders[i] = {...weekOrders[i], 'orders': props.cellsData[j].orders}
            // } else {
            //
            // }


            if (props.cellsData[j].date.slice(0, 10) === item[i]) {
                orders = props.cellsData[j].orders
                break       // это обязательный break
            }

            // console.log(weekOrders[j])
        }

        weekOrders[i] = {...weekOrders[i], 'orders': orders}

        // console.log(item[i])
    }
    return weekOrders
})

console.log(dateIntervalMatrixRender)

</script>

<style scoped>

</style>
