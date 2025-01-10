<template>
    <div>
        <div class="flex items-center">
            <AppInputButton
                :id="order.id"
                :title="collapseChar"
                :type="labelType"
                height="h-[30px]"
                width="w-[30px]"
                @click="collapseLines"
            />

            <AppLabel
                :bold="true"
                :text="order.client.short_name"
                :type="labelType"
                textSize="small"
                @click="collapseLines"
            />

            <AppLabel
                :bold="true"
                :text="'№ ' + orderNo"
                :type="labelType"
                align="center"
                textSize="small"
                width="w-[100px]"
            />

            <AppLabel
                :bold="true"
                :text="'∑ ' + getOrderTotalAmount()"
                align="center"
                width="w-[70px]"
            />

            <AppLabel
                :bold="true"
                :text="'∑ ' + getOrderManufTime().toFixed(3)"
                type="primary"
                align="center"
                width="w-[100px]"
            />


        </div>


        <!--        <div>-->
        <!--            {{ order.client.short_name }}-->
        <!--        </div>-->



        <div v-if="!collapse">
            <div v-for="cell in order.cells" :key="cell.id">
                <CellLine
                    :cell="cell"
                />
            </div>
        </div>



    </div>


</template>

<script setup>
import {ref} from "vue";
import AppInputButton from '/resources/js/src/components/ui/inputs/AppInputButton.vue'
import AppLabel from "/resources/js/src/components/ui/labels/AppLabel.vue"
import CellLine from "/resources/js/src/components/dashboard/manufacture/cells/sewing/components/CellLine.vue";

const props = defineProps({
    order: {
        type: Object,
        required: true
    }

})


const collapse = ref(true)                  // указатель на схлопывание lines
const collapseChar = ref('+')               // символ схлопывания lines

const labelType = ref('dark')


const collapseLines = () => {
    collapse.value = !collapse.value
    collapseChar.value = collapse.value ? '+' : '-'
}

// Обрабатываем красивый вывод номера заказа
// todo вынести в отдельный helper
let orderNo = props.order.no_num
if (orderNo.indexOf('.') !== -1) {

    const orderNoDec = orderNo.split('.')[0]
    const orderNoFloat = orderNo.split('.')[1]

    if (+orderNoFloat === 0) {
        orderNo = orderNoDec
    }
}

console.log(props.order)

// считаем кол-во матрасов в заказе по ПЯ
const getOrderTotalAmount = () => {
    return props.order.cells.reduce((acc, cell) => acc + cell.amount, 0)
}

// считаем кол-во трудозатрат в заказе по ПЯ
const getOrderManufTime = () => {
    return props.order.cells.reduce((acc, cell) => acc + cell.norm, 0)
}



</script>

<style scoped>

</style>
