<template>

    <div class="flex items-center">

        <AppInputButton
            :id="item.date"
            :title="collapseChar"
            :type="labelType"
            height="h-[30px]"
            width="w-[30px]"
            @click="collapseLines"
        />

        <AppLabel
            :bold="true"
            :text="formattedDate(item.date)"
            :type="labelType"
            align="center"
            width="w-[100px]"
        />

        <AppLabel
            :bold="true"
            :text="'∑ ' + getDayTotalAmount().toString()"
            align="center"
            width="w-[100px]"
        />

        <AppLabel
            :bold="true"
            :text="'∑ ' + getDayManufTime().toFixed(3)"
            type="primary"
            align="center"
            width="w-[100px]"
        />

    </div>

    <div v-if="!collapse" class="ml-8">

        <div v-for="order in item.orders" :key="order.order.id">

            <div>
                <CellOrder
                    :order="order.order"
                />

            </div>

        </div>
    </div>

</template>

<script setup>
import CellOrder from '/resources/js/src/components/dashboard/manufacture/cells/sewing/components/CellOrder.vue'
import AppInputButton from "/resources/js/src/components/ui/inputs/AppInputButton.vue";
import AppLabel from "/resources/js/src/components/ui/labels/AppLabel.vue";

import {ref} from "vue";
// import CellLine from '/resources/js/src/components/dashboard/manufacture/cells/components/CellLine.vue'


const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

// console.log('item', props.item)

const collapse = ref(true)                  // указатель на схлопывание lines
const collapseChar = ref('+')               // символ схлопывания lines

const labelType = ref('dark')


const collapseLines = () => {
    collapse.value = !collapse.value
    collapseChar.value = collapse.value ? '+' : '-'
}

const formattedDate = (date) => {
    return new Date(date).toLocaleDateString()
}


// считаем общее количество изделий в день
const getDayTotalAmount = () => {
    const dayTotalAmount = props.item.orders.reduce((acc, order) => {
        let orderTotalAmount = order.order.cells.reduce((acc2, cell) => acc2 + cell.amount, 0)
        return acc + orderTotalAmount
    }, 0)

    return dayTotalAmount
}


// считаем время по участку за производственный день
const getDayManufTime = () => {
    const dayManufTime = props.item.orders.reduce((acc, order) => {
        let orderManufTime = order.order.cells.reduce((acc2, cell) => acc2 + cell.norm, 0)
        return acc + orderManufTime
    }, 0)

    return dayManufTime
}

console.log('dayTime', getDayManufTime())

// console.log(props.orders)


</script>

<style scoped>

</style>
