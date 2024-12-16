<template>
    <!--    <keep-alive>-->
    <!--        <div class="border-2 border-gray-300 rounded-lg min-h-3">-->

    <DataStatusIndicator v-if="ordersIsChanged"

    />

    <div class="">

        <TheOrderHeader
            v-for="order in orders" :key="order.id"
            :order="order"
            @delete-order="deleteOrder"
        />


    </div>
    <!--    </keep-alive>-->
</template>

<script setup>
import {ref} from 'vue'

import { storeToRefs } from 'pinia'
import {useOrdersStore} from "@/src/stores/OrdersStore"

import DataStatusIndicator from "@/src/components/dashboard/service/DataStatusIndicator.vue"
import TheOrderHeader from '@/src/components/dashboard/orders/components/TheOrderHeader.vue'


const ordersStore = useOrdersStore()
const ordersLoad = ordersStore.ordersShow.value
const ordersIsChanged = ref(ordersStore.ordersShowIsChanged)

const orders = ordersLoad.map((order) => ({...order, 'deleted': false}))

console.log(orders)

const deleteOrder = (id) => {
    console.log(id)
    ordersIsChanged.value = true
    ordersStore.ordersShowIsChanged = true
    console.log(ordersStore.ordersShowIsChanged)

    const order = orders.find(order => order.id === id)
    order.deleted = !order.deleted

    console.log(orders)
}


</script>

<style scoped>

</style>
