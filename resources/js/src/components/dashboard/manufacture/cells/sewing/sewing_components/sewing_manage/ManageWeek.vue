<template>
    <div class="flex m-1 w-fit border-[1px] rounded border-slate-600 bg-slate-300">

        <!-- __ Номер недели -->
        <div
            class="font-bold text-slate-700 [text-shadow:2px_2px_4px_rgba(0,0,0,0.5)] m-1 border-[1px] rounded border-slate-600 bg-slate-200 min-w-[50px] min-h-[50px] flex items-center justify-center">
            {{ weekNumber }}
        </div>

        <!-- __ Дни отгрузки -->
        <div class="flex">

            <div v-for="idx of [0, 1, 2, 3, 4, 5, 6]" :key="idx">

                <ManageDay
                    :date="getDate(idx)"
                    :day="(week[idx].length ? week[idx] : []) as ISewingTask[]"
                />

            </div>

        </div>
    </div>

</template>

<script lang="ts" setup>
import type { IPlanMatrixWeek, ISewingTask } from '@/types'

import { computed } from 'vue'

import { additionDays, getWeekNumber } from '@/app/helpers/helpers_date'

import ManageDay from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageDay.vue'

interface IProps {
    date: Date                      // __ Дата начала недели
    week?: IPlanMatrixWeek
}

const props = withDefaults(defineProps<IProps>(), {
    week: () => []
})

// const emits = defineEmits<{
//     (e: 'drag-and-drop'): void,
// }>()


// __ Логика
const getDate = (dayNumber: number /* порядковый день недели*/) => additionDays(props.date, dayNumber)
const weekNumber = computed(() => getWeekNumber(props.date).toString())

// const dragAndDrop = () => emits('drag-and-drop')


</script>

<style scoped>

</style>
