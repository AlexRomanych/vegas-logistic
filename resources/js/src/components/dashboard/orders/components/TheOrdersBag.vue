<template>
    <div class="border-2 border-gray-300 rounded-lg min-h-3 width-full">

        <TheOrderHeader
            v-for="order in orders" :key="order.id"
            :order="order"
        />

    </div>
</template>

<script setup>
// import { storeToRefs } from 'pinia'
import {useOrdersStore} from "@/src/stores/OrdersStore"

import TheOrderHeader from '@/src/components/dashboard/orders/components/TheOrderHeader.vue'


const ordersStore = useOrdersStore()
const orders = ordersStore.ordersShow.value

// const {ordersShow} = storeToRefs(ordersStore)

console.log(orders)

</script>

<style scoped>

</style>
