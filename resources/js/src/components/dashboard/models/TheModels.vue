<template>

    <!--        <div class="w-full h-full bg-slate-600 text-white">-->
    <!--            The Models-->
    <!--        </div>-->
    <div>

    </div>
    <AppTable
        :tableData="modelsTableData"
        textSize="mini"
        width="w-full"
    />

</template>

<script setup>
// console.log('Here')
// import axios from 'axios'

// import TheDashboard from '@/src/components/dashboard/TheDashboard.vue'

import AppTable from "@/src/components/ui/tables/AppTable.vue";

import {jwtGet} from "@/src/app/utils/jwt_api.js";

const models = await jwtGet('/models');                    // даем запрос на получение данных по моделям

const modelsData = models.models.map(model => {
    return [model.type,model.coll, model.name, model.textile, model.bh, model.ch, model.bc]
})

const modelsTableData = {
    headers: ['Тип', 'Коллекция', 'Модель', 'Ткань', 'Выс. МЭ', 'Выс. чехла', 'Состав МЭ'],
    data: modelsData
}

console.log(models)
console.log(modelsTableData)


</script>

<style scoped>

</style>
