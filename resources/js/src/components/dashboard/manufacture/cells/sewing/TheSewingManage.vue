<template>
    <div v-if="!isLoading">

        <!-- __ Меню -->
        <TheSewingManageMenu/>

        <!-- __ Вход данных -->
        <div>
            <div v-for="(planWeek, idx) of renderMatrix" :key="idx">
                <ManageWeek
                    :date="getStartWeekDate(idx)"
                    :week="planWeek"
                />
            </div>
        </div>

    </div>
</template>

<script lang="ts" setup>
import type { IPeriod, IPlanMatrix, } from '@/types'

import { onMounted, provide, ref, watch, /*toRaw*/ } from 'vue'
import { storeToRefs } from 'pinia'

import { usePlansStore } from '@/stores/PlansStore.ts'
import { useSewingStore } from '@/stores/SewingStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import { PERIOD_DRAFT } from '@/app/constants/shared.ts'

import {
    getRenderMatrixForPlan,
    getRenderPeriodForPlan
} from '@/app/helpers/plan/helpers_plan.ts'
import { additionDays, formatToYMD, getSundayAfter } from '@/app/helpers/helpers_date'
import { correctRenderMatrix, sortRenderMatrixByTaskPosition } from '@/app/helpers/manufacture/helpers_sewing.ts'

import ManageWeek from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageWeek.vue'
import TheSewingManageMenu
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageMenu.vue'


const DEBUG     = true
const isLoading = ref(false)


const planStore   = usePlansStore()
const sewingStore = useSewingStore()

const { planPeriodGlobal } = storeToRefs(planStore)

const {
          globalSewingTasks,        // __ Все задания (Global State)
          globalRenderPeriod,       // __ Период для рендера
      } = storeToRefs(sewingStore)

// __ Определяем переменные
let planPeriod: IPeriod = PERIOD_DRAFT             // __ Период плана загрузок
let renderMatrix     = ref<IPlanMatrix>([]) // __ Матрица для рендера
let renderMatrixCopy = ref<IPlanMatrix>([]) // __ Копия Матрицы для рендера. Делаем реактивной, потому что прокидываем в дочерние компоненты

// __ Передаем в дочерние компоненты
provide('renderMatrix', renderMatrix)
provide('renderMatrixCopy', renderMatrixCopy)

// __ Получаем период плана загрузок с сервера
const getDefaultPeriod = async () => planPeriod = await planStore.getPlanLoadsDefaultPeriod()

const getPlanPeriod = async () => {
    // TODO: Доделать выбор периода
    await getDefaultPeriod()    //
    planPeriodGlobal.value = planPeriod
}

// __ Получаем период для рендера
const getRenderPeriod = () => globalRenderPeriod.value = getRenderPeriodForPlan(globalSewingTasks.value)

// __ Корректируем период для рендера. находим самую позднюю дату отгрузки и пересчитываем конец периода до воскресенья
const correctRenderPeriod = () => {
    if (!globalSewingTasks.value.length) {
        return
    }
    const maxDateObj = globalSewingTasks.value.reduce((maxDateObj, task) => {
        if (task.order.load_at && new Date(task.order.load_at).getTime() > maxDateObj.value) {
            maxDateObj.value = new Date(task.order.load_at).getTime()
            maxDateObj.date = task.order.load_at
        }
        return maxDateObj
    }, {value: 0, date: ''})

    globalRenderPeriod.value.end = formatToYMD(getSundayAfter(new Date(maxDateObj.date)))
}

// __ Получаем матрицу для рендера
const getRenderMatrix = () => renderMatrix.value = getRenderMatrixForPlan(globalSewingTasks.value, globalRenderPeriod.value)


// __ Делаем глубокую копию объекта, чтобы сравнивать с предыдущим состоянием
// __ И отправлять на сервер только измененные данные
const copyRenderMatrix = () => {
    renderMatrixCopy.value = JSON.parse(JSON.stringify(renderMatrix.value))
    // renderMatrixCopy = structuredClone(toRaw(renderMatrix.value)) // __ Не работает с реактивными объектами Vue
}


// __ Получаем дату начала недели
const getStartWeekDate = (weekOrder: number /* порядковы номер недели */) => additionDays(new Date(globalRenderPeriod.value.start), weekOrder * 7)


// watch(() => renderMatrix.value, () => {
// }, { immediate: true, deep: true })


// __ Тут следим за состоянием глобальных данных с сервера и обновляем локальные данные
watch(() => globalSewingTasks.value, () => {

    if (!globalSewingTasks.value) {
        return
    }

    // __ Выполняем всю подготовку и преобразование данных для рендера
    // __ !!! Порядок важен
    getRenderPeriod()
    console.log(globalRenderPeriod.value)
    correctRenderPeriod()
    getRenderMatrix()
    renderMatrix.value = sortRenderMatrixByTaskPosition(renderMatrix.value)
    copyRenderMatrix()
    renderMatrix.value = correctRenderMatrix(renderMatrix.value)

    if (DEBUG) console.log('renderMatrix:', renderMatrix.value)
    if (DEBUG) console.log('globalSewingTasks:', globalSewingTasks.value)

}, { immediate: true, deep: true })


onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            // !!! Порядок важен
            await sewingStore.getSewingTasks()  // __ Получаем SewingTasks и записываем в глобальную переменную в SewingStore
            await getPlanPeriod()               // __ Получаем период плана загрузок

            // __ Дальше все через watcher
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
