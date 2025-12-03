<template>
    <div v-for="(planLoadsWeek, idx) of renderMatrix" :key="idx">
        <PlanLoadsWeek
            :date="getStartWeekDate(idx)"
            :week="planLoadsWeek"
        />

    </div>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'

import type { IPeriod, IPlanLoad, IPlanLoadsMatrix } from '@/types'

import { usePlansStore } from '@/stores/PlansStore.ts'
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import {
    getRenderMatrixForPlan,
    getRenderPeriodForPlan,
} from '@/app/helpers/plan/helpers_plan.ts'
import PlanLoadsWeek from '@/components/dashboard/plans/plan_loads/PlanLoadsWeek.vue'
import { additionDays } from '@/app/helpers/helpers_date'
import { storeToRefs } from 'pinia'

const DEBUG = true
const isLoading = ref(false)

const planStore = usePlansStore()
const {planPeriodGlobal} = storeToRefs(planStore)

// __ Определяем переменные
const planLoads = ref<IPlanLoad[]>([])
let planPeriod: IPeriod = PERIOD_DRAFT          // Период плана загрузок
let renderPeriod: IPeriod = PERIOD_DRAFT        // Период для рендера
let renderMatrix = ref<IPlanLoadsMatrix>([])        // Матрица для рендера

// __ Получаем план загрузок
const getPlanLoads = async () => planLoads.value = await planStore.getPlanLoads(/*period*/)

// __ Получаем период плана загрузок с сервера
const getDefaultPeriod = async () => planPeriod = await planStore.getPlanLoadsDefaultPeriod()
const getPlanPeriod = async () => {
    // TODO: Доделать выбор периода
    await getDefaultPeriod()    //
    planPeriodGlobal.value = planPeriod
}


// __ Получаем период для рендера
const getRenderPeriod = () => renderPeriod = getRenderPeriodForPlan(planLoads.value)


// __ Получаем матрицу для рендера
const getRenderMatrix = () => renderMatrix.value = getRenderMatrixForPlan(planLoads.value, renderPeriod)



// __ Логика
const getStartWeekDate = (weekOrder: number /* порядковы номер недели */) => additionDays(new Date(renderPeriod.start), weekOrder * 7)




onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getPlanLoads()        // Получаем план загрузок
            await getPlanPeriod()       // Получаем период плана загрузок

            getRenderPeriod()
            getRenderMatrix()

            DEBUG && console.log('planLoads: ', planLoads.value)
            DEBUG && console.log('planPeriod: ', planPeriod)
            DEBUG && console.log('renderPeriod: ', renderPeriod)
            DEBUG && console.log('renderMatrix: ', renderMatrix.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})


</script>

<style scoped>

</style>
