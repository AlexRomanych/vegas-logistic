<template>
    <!--        <div class="w-full h-full bg-slate-600 text-white">-->
    <!--            The Models-->
    <!--        </div>-->
    <div v-if="modelsTableData">
        <AppTable :tableData="modelsTableData" textSize="mini" width="w-full" />
    </div>
</template>

<script setup>
// console.log('Here')
// import axios from 'axios'

// import TheDashboard from '@/src/components/dashboard/TheDashboard.vue'

import { useModelsStore } from '@/stores/ModelsStore'
import AppTable from '@/components/ui/tables/AppTable.vue'
// import {jwtGet} from "@/src/app/utils/jwt_api.js";

const modelsStore = useModelsStore()
const models = await modelsStore.getModels() // даем запрос на получение данных по моделям

console.log(models)

const modelsData = models.map((model) => {
    return [model.type, model.coll, model.name, model.textile, model.bh, model.ch, model.bc]
})

const modelsTableData = {}

console.log(modelsData)

modelsTableData.headers = [
    'Тип',
    'Коллекция',
    'Модель',
    'Ткань',
    'Выс. МЭ',
    'Выс. чехла',
    'Состав МЭ',
]
modelsTableData.data = modelsData

// const modelsTableData = {
//     headers: ['Тип', 'Коллекция', 'Модель', 'Ткань', 'Выс. МЭ', 'Выс. чехла', 'Состав МЭ'],
//     data: modelsData
// }

// console.log(models)
// console.log(modelsTableData)
</script>

<style scoped></style>
