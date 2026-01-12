<template>
    <div v-if="!isLoading">
        <div v-for="(planWeek, idx) of renderMatrix" :key="idx">
            <ManageWeek
                :date="getStartWeekDate(idx)"
                :week="planWeek"
                @drag-and-drop="correctRenderMatrix"
            />
        </div>
    </div>
</template>

<script lang="ts" setup>
import type { IPeriod, IPlan, IPlanMatrix } from '@/types'

import { onMounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { usePlansStore } from '@/stores/PlansStore.ts'
// import { useRoute, useRouter } from 'vue-router'
// import { useBusinessProcessesStore } from '@/stores/BusinessProcessesStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import {
    getRenderMatrixForPlan,
    getRenderPeriodForPlan
} from '@/app/helpers/plan/helpers_plan.ts'
import { additionDays } from '@/app/helpers/helpers_date'

import {
    BUSINESS_PROCESS_NODES,
    BUSINESS_PROCESSES
} from '@/app/constants/business_processes.ts'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'

import ManageWeek from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageWeek.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { PLAN_DRAFT } from '@/app/constants/plans.ts'
import { useSewingStore } from '@/stores/SewingStore.ts'


const DEBUG = true
const isLoading = ref(false)

// const route = useRoute()
// const router = useRouter()

const planStore = usePlansStore()
const sewingStore = useSewingStore()

const {planPeriodGlobal} = storeToRefs(planStore)

// __ Определяем переменные
const plan = ref<IPlan[]>([])
let planPeriod: IPeriod = PERIOD_DRAFT          // Период плана загрузок
let renderPeriod: IPeriod = PERIOD_DRAFT        // Период для рендера
let renderMatrix = ref<IPlanMatrix>([])        // Матрица для рендера

// __ Определяем переменные из маршрута
let businessProcessIdFromRoute: number = BUSINESS_PROCESSES.ORDER_MOVING.ID
let businessProcessNodeIdFromRoute: number = BUSINESS_PROCESS_NODES.SEWING.ID

// __ Получаем план загрузок
const getPlan = async (
    businessProcessId: number = BUSINESS_PROCESSES.ORDER_MOVING.ID,
    businessProcessNodeId: number = BUSINESS_PROCESS_NODES.LOADS.ID,
) => plan.value = await planStore.getPlanBusinessProcessNode(businessProcessId, businessProcessNodeId)


// __ Получаем период плана загрузок с сервера
const getDefaultPeriod = async () => planPeriod = await planStore.getPlanLoadsDefaultPeriod()
const getPlanPeriod = async () => {
    // TODO: Доделать выбор периода
    await getDefaultPeriod()    //
    planPeriodGlobal.value = planPeriod
}

// __ Получаем период для рендера
const getRenderPeriod = () => renderPeriod = getRenderPeriodForPlan(plan.value)

// __ Получаем матрицу для рендера
const getRenderMatrix = () => renderMatrix.value = getRenderMatrixForPlan(plan.value, renderPeriod)

// __ Проблема с draggable
// __ Если день пустой, то перетаскивание не срабатывает
// __ Поэтому добавляем пустой день
const correctRenderMatrix = () => {
    let draftId = -100
    renderMatrix.value.forEach((week, weekIndex) => {

        week.forEach((day, dayIndex) => {
            const filteredDay = day.filter(item => item.id !== PLAN_DRAFT.id)
            if (filteredDay.length === 0) {
                const draft = {...PLAN_DRAFT, id: draftId--}
                filteredDay.push(draft)
            }
            renderMatrix.value[weekIndex][dayIndex] = filteredDay
        })

    })
}

// __ Логика
const getStartWeekDate = (weekOrder: number /* порядковы номер недели */) => additionDays(new Date(renderPeriod.start), weekOrder * 7)

watch(() => renderMatrix.value, () => {

    // if (DEBUG) console.log('renderMatrix:', renderMatrix.value)

}, {immediate: true, deep: true})



onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            // warn: Порядок важен!
            // await router.isReady().then(() => {
            //     businessProcessIdFromRoute = route.params.businessProcessId as unknown as number
            //     businessProcessNodeIdFromRoute = route.params.businessProcessNodeId as unknown as number
            // })

            // await getBusinessProcessNode(businessProcessNodeIdFromRoute)    // Получаем узел бизнес-процесса

            const sewingTasks = ref(await sewingStore.getSewingTasks())
            sewingTasks.value.sort((a, b) => a.id - b.id)

            console.log('sewingTasks: ', sewingTasks.value)



            // plan.value = await getPlan(businessProcessIdFromRoute, businessProcessNodeIdFromRoute)
            // await getPlanPeriod()       // Получаем период плана загрузок
            //
            // getRenderPeriod()
            // getRenderMatrix()
            // correctRenderMatrix()
            //
            // // if (DEBUG) console.log('businessProcessIdFromRoute:', businessProcessIdFromRoute)
            // // if (DEBUG) console.log('businessProcessNodeIdFromRoute:', businessProcessNodeIdFromRoute)
            // // if (DEBUG) console.log('plan:', plan.value)
            // if (DEBUG) console.log('renderMatrix:', renderMatrix.value)


        },
        undefined,
        // false,
    )

    isLoading.value = false

})

</script>

<style scoped>

</style>
