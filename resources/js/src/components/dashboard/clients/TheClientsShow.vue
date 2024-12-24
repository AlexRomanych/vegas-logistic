<template>
    <div v-if="clientsTableData">


        <AppTable
            :tableData="clientsTableData"

            width="w-full"
        />
    </div>
</template>

<script setup>

import {useClientsStore} from "@/src/stores/ClientsStore"
import AppTable from "@/src/components/ui/tables/AppTable.vue";

const clientsStore = useClientsStore()
const clients = await clientsStore.getClients();     // даем запрос на получение данных по клиентам

// console.log(clients)

const clientsData = clients.map(client => {
    return [client.sn, client.reg === 'west' ? 'Запад' : 'Восток', client.act ? 'Активный' : '']
})

const clientsTableData = {}
clientsTableData.headers = ['Клиент', 'Регион', 'Статус']
clientsTableData.data = clientsData

</script>

<style scoped>

</style>
