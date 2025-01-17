<template>
    <div class="flex flex-col"> <!--Общий контейнер для всего календаря-->

        <div v-for="(week, weekIdx) in dateIntervalMatrixRender" :key="weekIdx" class="flex flex-row">

            <!--Контейнер для недели-->
            <div
                v-for="day in week"
                :key="day.date"
                class="droppable flex flex-col border-2 rounded-lg mr-3 mb-3 bg-slate-200 border-slate-500 p-2"
                @drop="onDrop($event, weekIdx, day.date)"
                @dragover.prevent
                @dragenter.prevent
            >

                <!--Контейнер для дня-->
                <AppLabel
                    :text="formatDate(day.date) + ' (' + getDayOfWeek(day.date) + ')'"
                    :type="getDayType(day.date)"
                    align="center"
                    textSize="mini"
                    width="w-[278px]"
                />

                <div v-for="order in day.orders">
                    <div
                        class="draggable flex flex-row"
                        draggable="true"
                        @dragstart="onDragStart($event, order, day.date)"

                    >

                        <AppLabel
                            :text="order.order.client.short_name"
                            class="cursor-pointer"
                            textSize="mini"
                            width="w-[100px]"

                        />

                        <AppLabel
                            :text="'№ ' + getPrettyOrderNumber(order.order.no_num)"
                            class="cursor-pointer"
                            textSize="mini"
                            width="w-[70px]"

                        />

                        <AppLabel
                            :text="'∑ ' + getOrderElementsAmount(order.order)"
                            align="center"
                            class="cursor-pointer"
                            textSize="mini"
                            width="w-[45px]"

                        />

                        <AppLabel
                            :text="'∑ ' + getOrderTimesAmount(order.order).toFixed(2)"
                            align="center"
                            class="cursor-pointer"
                            textSize="mini"
                            type="primary"
                            width="w-[50px]"
                        />

                    </div>

                </div>


                <!-- Всего: -->
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
import {ref, reactive} from 'vue'
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
    getMaxOrdersInDay,
    getDateIntervalMatrixRender,
    moveOrder
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
console.log('date_interval', dateIntervalMatrix)

// attract Накладываем на матрицу календаря массив с данными о заказах
let dateIntervalMatrixRender = reactive(getDateIntervalMatrixRender(dateIntervalMatrix, props.cellsData), {deep: true})
console.log('matrix_render', dateIntervalMatrixRender)


const onDragStart = function (event, order, dateFrom) {
    event.dataTransfer.dropEffect = 'move'
    event.dataTransfer.effectAllowed = 'move'
    event.dataTransfer.setData('order', JSON.stringify({order, dateFrom}))
    console.log('drag', event)
}
const onDrop = function (event, weekIdx, dateTo) {
    const {order, dateFrom} = JSON.parse(event.dataTransfer.getData('order'))

    // console.log('drop', event)
    // console.log('drop_order', order, dateFrom)
    moveOrder(dateFrom, dateTo, order, dateIntervalMatrixRender)
}

</script>

<style scoped>

</style>
