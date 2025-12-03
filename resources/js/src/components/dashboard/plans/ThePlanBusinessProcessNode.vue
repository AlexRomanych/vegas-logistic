<template>
    <div v-if="!isLoading">

        <div class="w-fit">

            <div class="sticky top-0 flex justify-center w-full mt-0">
                <AppLabelTS
                    align="center"
                    class="cursor-pointer"
                    height="h-[40px]"
                    :text="`План бизнес-узла: ${businessProcessNode.name}`"
                    text-size="huge"
                    type="stone"
                    width="w-full"
                    rounded="4"
                />
            </div>


            <div v-for="(planWeek, idx) of renderMatrix" :key="idx">
                <PlanWeek
                    :date="getStartWeekDate(idx)"
                    :week="planWeek"
                />

            </div>

        </div>
    </div>
</template>

<script lang="ts" setup>
import type { IBusinessProcessNode, IPeriod, IPlan, IPlanMatrix } from '@/types'

import { onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute, useRouter } from 'vue-router'
import { usePlansStore } from '@/stores/PlansStore.ts'
import { useBusinessProcessesStore } from '@/stores/BusinessProcessesStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import {
    getRenderMatrixForPlan,
    getRenderPeriodForPlan
} from '@/app/helpers/plan/helpers_plan.ts'
import { additionDays } from '@/app/helpers/helpers_date'

import {
    BUSINESS_PROCESS_NODE_DRAFT,
    BUSINESS_PROCESS_NODES,
    BUSINESS_PROCESSES
} from '@/app/constants/business_processes.ts'
import { PERIOD_DRAFT } from '@/app/constants/shared.ts'

import PlanWeek from '@/components/dashboard/plans/plan_business_process/PlanWeek.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


// interface IProps {
//     businessProcessId?: number
//     processId?: number
// }

// const props: IProps = withDefaults(defineProps<IProps>(), {
//     businessProcessId: BUSINESS_PROCESSES.ORDER_MOVING.ID,              // БП - Движение заявки
//     businessProcessNodeId: BUSINESS_PROCESS_NODES.LOADS.ID              // Node - Загрузка на складе Вегас
// })

const DEBUG = true
const isLoading = ref(false)

const route = useRoute()
const router = useRouter()

const planStore = usePlansStore()
const businessProcessesStore = useBusinessProcessesStore()

const {planPeriodGlobal} = storeToRefs(planStore)

// __ Определяем переменные
const businessProcessNode = ref<IBusinessProcessNode>(BUSINESS_PROCESS_NODE_DRAFT)
const plan = ref<IPlan[]>([])
let planPeriod: IPeriod = PERIOD_DRAFT          // Период плана загрузок
let renderPeriod: IPeriod = PERIOD_DRAFT        // Период для рендера
let renderMatrix = ref<IPlanMatrix>([])        // Матрица для рендера

// __ Определяем переменные из маршрута
let businessProcessIdFromRoute: number = BUSINESS_PROCESSES.ORDER_MOVING.ID
let businessProcessNodeIdFromRoute: number = BUSINESS_PROCESS_NODES.LOADS.ID

// __ Получаем узел бизнес-процесса
const getBusinessProcessNode = async (id: number) => {
    businessProcessNode.value = await businessProcessesStore.getBusinessProcessNodeById(id)
}

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

// __ Логика
const getStartWeekDate = (weekOrder: number /* порядковы номер недели */) => additionDays(new Date(renderPeriod.start), weekOrder * 7)


onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            // warn: Порядок важен!
            await router.isReady().then(() => {
                businessProcessIdFromRoute = route.params.businessProcessId as unknown as number
                businessProcessNodeIdFromRoute = route.params.businessProcessNodeId as unknown as number
            })

            await getBusinessProcessNode(businessProcessNodeIdFromRoute)    // Получаем узел бизнес-процесса

            plan.value = await getPlan(businessProcessIdFromRoute, businessProcessNodeIdFromRoute)
            await getPlanPeriod()       // Получаем период плана загрузок

            getRenderPeriod()
            getRenderMatrix()


            if (DEBUG) console.log('businessProcessIdFromRoute:', businessProcessIdFromRoute)
            if (DEBUG) console.log('businessProcessNodeIdFromRoute:', businessProcessNodeIdFromRoute)
            if (DEBUG) console.log('plan:', plan.value)
            if (DEBUG) console.log('node:', businessProcessNode.value)

            // await getBusinessProcessAdjacencyList(paramId)
            // await getBusinessProcesses(paramId)

            // counter = 1 // __ Счетчик для сквозной нумерации процессов

            // console.log('businessProcessesAdjacencyList: ', businessProcessesAdjacencyList.value)
            // console.log('businessProcesses: ', businessProcesses.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false

})

</script>

<style scoped>

</style>
