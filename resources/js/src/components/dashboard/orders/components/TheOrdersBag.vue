<template>
    <!--    <keep-alive>-->
    <!--        <div class="border-2 border-gray-300 rounded-lg min-h-3">-->

    <DataStatusIndicator v-if="ordersIsChanged" @save="saveOrders" />

    <div class="">
        <TheOrderHeader
            v-for="order in orders"
            :key="order.id"
            :order="order"
            @delete-order="deleteOrder"
        />
    </div>
    <!--    </keep-alive>-->
</template>

<script setup>
import { ref, watch } from 'vue'
import { onBeforeRouteLeave } from 'vue-router'

import { storeToRefs } from 'pinia'
import { useOrdersStore } from '@/stores/OrdersStore.js'

import DataStatusIndicator from '@/components/dashboard/service/DataStatusIndicator.vue'
import TheOrderHeader from '@/components/dashboard/orders/components/TheOrderHeader.vue'

const ordersStore = useOrdersStore()
const { ordersShowIsChanged } = storeToRefs(ordersStore)
const ordersIsChanged = ordersShowIsChanged

const ordersLoad = ordersStore.ordersShow.value
// const ordersIsChanged = ref(ordersStore.ordersShowIsChanged)
console.log(ordersIsChanged)

const orders = ordersLoad.map((order) => ({ ...order, deleted: false }))

console.log(orders)

const deleteOrder = (id) => {
    // console.log(id)
    ordersStore.ordersShowIsChanged = true
    // ordersIsChanged.value = true
    // console.log(ordersStore.ordersShowIsChanged)

    const order = orders.find((order) => order.id === id)
    order.deleted = !order.deleted

    // console.log(orders)
}

// Удаление заказа/ов
const saveOrders = (title, id) => {
    // console.log(title, id)

    const delOrdersList = []
    orders.forEach((order) => {
        if (order.deleted) {
            delOrdersList.push(order.id)
        }
    })

    // Сохраняем с ключем 'ids'
    const delOrdersListObj = { ids: delOrdersList }

    // console.log(delOrdersList)
    ordersStore.deleteOrders(delOrdersListObj)
    ordersShowIsChanged.value = false
    // ordersIsChanged.value = false
}

// Скидываем состояние изменения заявок, чтобы при возвращении не выскакивала надпись Сохранить
onBeforeRouteLeave((to, from) => {
    ordersStore.ordersShowIsChanged = false
})

watch(ordersIsChanged, (newVal) => {
    ordersIsChanged.value = newVal
})
</script>

<style scoped></style>
