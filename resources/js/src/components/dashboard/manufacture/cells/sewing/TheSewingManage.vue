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
                    @drag-and-drop="dragAndDropSewingTask"
                />
            </div>
        </div>

    </div>
</template>

<script lang="ts" setup>
import type { IPeriod, IPlanMatrix, ISewingTaskLine } from '@/types'

import { onMounted, provide, ref, watch, /*toRaw*/ } from 'vue'
import { storeToRefs } from 'pinia'

import { usePlansStore } from '@/stores/PlansStore.ts'
import { useSewingStore } from '@/stores/SewingStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import { PERIOD_DRAFT } from '@/app/constants/shared.ts'
import { SEWING_TASK_DRAFT } from '@/app/constants/sewing.ts'

import {
    getRenderMatrixForPlan,
    getRenderPeriodForPlan
} from '@/app/helpers/plan/helpers_plan.ts'
import { additionDays, formatToYMD } from '@/app/helpers/helpers_date'

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
let planPeriod: IPeriod = PERIOD_DRAFT                // Период плана загрузок
// let renderPeriod: IPeriod = PERIOD_DRAFT                // Период для рендера
let renderMatrix     = ref<IPlanMatrix>([]) // Матрица для рендера
let renderMatrixCopy = ref<IPlanMatrix>([]) // Копия Матрицы для рендера. Делаем реактивной, потому что прокидываем в дочерние компоненты

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

// __ Получаем матрицу для рендера
const getRenderMatrix = () => renderMatrix.value = getRenderMatrixForPlan(globalSewingTasks.value, globalRenderPeriod.value)


// __ Делаем глубокую копию объекта, чтобы сравнивать с предыдущим состоянием
// __ И отправлять на сервер только измененные данные
const copyRenderMatrix = () => {
    renderMatrixCopy.value = JSON.parse(JSON.stringify(renderMatrix.value))
    // renderMatrixCopy = structuredClone(toRaw(renderMatrix.value)) // __ Не работает с реактивными объектами Vue
}


// __ Очищаем матрицу рендера от пустых сменных заданий, которые добавляем для рендеринга
const clearRenderMatrix = () => {
    renderMatrix.value.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            renderMatrix.value[weekIndex][dayIndex] = day.filter(item => item.id > -1) // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
        })
    })
}


// __ Пересчитываем позиции СЗ в матрице рендера после перетаскивания мышью
const setTaskPositionInRenderMatrix = () => {
    renderMatrix.value.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            renderMatrix.value[weekIndex][dayIndex] = day.map((item, index) => ({ ...item, position: index + 1 })) // __ id пустых заданий меньше нуля
        })
    })
}


// __ Сортируем задания в матрице рендера по позиции
const sortRenderMatrixByTaskPosition = () => {
    renderMatrix.value.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            renderMatrix.value[weekIndex][dayIndex] = day.sort((a, b) => a.position - b.position)
            renderMatrix.value[weekIndex][dayIndex].forEach(sewingTask => {
                sewingTask.sewing_lines = sewingTask.sewing_lines.sort((a: ISewingTaskLine, b: ISewingTaskLine) => a.position - b.position)
            })
        })
    })
}


// __ Проблема с draggable
// __ Если день пустой, то перетаскивание не срабатывает
// __ Поэтому добавляем пустое задание в пустой день
const correctRenderMatrix = () => {
    let draftId = -100
    renderMatrix.value.forEach((week, weekIndex) => {

        week.forEach((day, dayIndex) => {
            let filteredDay = day.filter(item => item.id > -1)      // __ id === 0 (для добавленного СЗ)
            // let filteredDay = day.filter(item => item.id !== SEWING_TASK_DRAFT.id)
            if (filteredDay.length === 0) {
                const draft = { ...SEWING_TASK_DRAFT, id: draftId--, position: 100 }
                filteredDay.push(draft)
            } else {
                // __ Сортируем по позиции (по порядку)
                // filteredDay = filteredDay.sort((a, b) => a.position - b.position)
            }
            renderMatrix.value[weekIndex][dayIndex] = filteredDay
            // renderMatrix.value[weekIndex][dayIndex] = {...filteredDay, fullDay: true}
        })
    })

    // copyRenderMatrix()
}


// __ Разница между предыдущим и текущим состоянием
// __ Разница по задумке должна быть только в одной Заявке:
// __ Либо перемещение в рамках одного дня, либо из одного дня в другой
// __ Задача найти эти дни и эту Заявку
const getDiffsInRenderMatrix = () => {
    return

    const diffs = {
        dayFrom: '',
        dayTo:   '',
        task:    '',
    }

    console.log('catch diffs')
    // return

    // __ Получаем дату отсчета
    let workDay = new Date(globalRenderPeriod.value.start)
    // console.log(renderPeriod)

    for (let i = 0; i < renderMatrix.value.length; i++) {

        const weekBefore = renderMatrixCopy.value[i]
        const weekAfter  = JSON.parse(JSON.stringify(renderMatrix.value[i]))

        // console.log('weekAfter: ', weekAfter)
        // console.log('weekBefore: ', weekBefore)

        for (let j = 0; j < 7; j++) {

            const dayAfter  = weekAfter[j]
            const dayBefore = weekBefore[j]

            const maxDayIndex = Math.max(dayAfter.length, dayBefore.length)
            // for (let k = 0; k < maxDayIndex; k++) {
            //
            //     const taskBefore = dayBefore[k]
            //     const taskAfter  = dayAfter[k]
            //
            //
            //
            // }


            if (dayAfter.length < dayBefore.length) {
                diffs.dayFrom = formatToYMD(workDay)


            } else if (dayAfter.length > dayBefore.length) {
                diffs.dayTo = formatToYMD(workDay)
            } else {

            }


            workDay = additionDays(workDay, 1)
            console.log(formatToYMD(workDay))
        }


        // for (let j = 0; j < renderMatrix.value[i].length; j++) {
        //     const current = renderMatrix.value[i][j]
        //     const previous = renderMatrixCopy[i][j]
        //
        //     if (JSON.stringify(current) !== JSON.stringify(previous)) {
        //         diffs.push({ week: i, day: j, current, previous })
        //     }
        // }


    }


    return diffs
}

// __ Перетаскивание мышью СЗ
// __ Находим разницу между предыдущим и текущим состоянием и отправляем в хранилище
const dragAndDropSewingTask = () => {
    return
    const diffs = getDiffsInRenderMatrix()

    console.log('diffs: ', diffs)


    // correctRenderMatrix()
    // return

    clearRenderMatrix() // __ Очищаем матрицу рендера от пустых сменных заданий, которые добавляем для рендеринга
    setTaskPositionInRenderMatrix() // __ Перенумеровываем задания в матрице рендера

    // const differences = getDiffsInRenderMatrix()    // __ Получаем разницу между предыдущим и текущим состоянием
    // console.log('differences: ', differences)

    // saveDifferences(differences) // __ Сохраняем разницу между предыдущим и текущим состоянием

    copyRenderMatrix()
    correctRenderMatrix()


}


// __ Получаем дату начала недели
const getStartWeekDate = (weekOrder: number /* порядковы номер недели */) => additionDays(new Date(globalRenderPeriod.value.start), weekOrder * 7)


watch(() => renderMatrix.value, () => {

    // if (DEBUG) console.log('renderMatrix:', renderMatrix.value)

    getDiffsInRenderMatrix()

}, { immediate: true, deep: true })


// __ Тут следим за состоянием глобальных данных с сервера и обновляем локальные данные
watch(() => globalSewingTasks.value, () => {

    if (!globalSewingTasks.value) {
        return
    }

    // __ Выполняем всю подготовку и преобразование данных для рендера
    getRenderPeriod()
    getRenderMatrix()
    sortRenderMatrixByTaskPosition()
    copyRenderMatrix()
    correctRenderMatrix()

    if (DEBUG) console.log('renderMatrix:', renderMatrix.value)

}, { immediate: true, deep: true })


onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            // !!! Порядок важен
            await sewingStore.getSewingTasks()  // __ Получаем SewingTasks и записываем в глобальную переменную в SewingStore

            // console.log('globalSewingTasks: ', globalSewingTasks.value)

            await getPlanPeriod()               // __ Получаем период плана загрузок

            // __ Дальше все через watcher

            // getRenderPeriod()
            // getRenderMatrix()
            // sortRenderMatrixByTaskPosition()
            // copyRenderMatrix()
            // correctRenderMatrix()

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
