<template>
    <div class="flex justify-start items-end">

        <!-- __ Инициализирующая метка -->
        <div class="mb-[-2px]">
            <AppLabelTS
                :bold="true"
                :text="labelText"
                align="center"
                height="h-[38px]"
                text-size="small"
                type="dark"
                width="w-[100px]"

            />
        </div>

        <!-- __ Начало -->
        <div>
            <AppInputDateTS
                id="start"
                v-model="startDate"
                class="mr-1 ml-0.5"
                label="Начало:"
                type="light"
                @getInputDate="setPeriod"
            />
        </div>

        <!-- __ Окончание -->
        <div>
            <AppInputDateTS
                id="end"
                v-model="endDate"
                label="Окончание:"
                type="light"
                @getInputDate="setPeriod"
            />
        </div>

        <!-- __ Кнопка Показать -->
        <div class="mb-[-2px] ml-0.5 cursor-pointer">
            <AppLabelTS
                :bold="true"
                align="center"
                height="h-[38px]"
                text="Показать"
                text-size="small"
                type="dark"
                width="w-[100px]"
                @click="clickApply"
            />
        </div>

    </div>

</template>

<script lang="ts" setup>
import type { ITaskPeriod } from '@/types'

import { compareDatesLogic } from '@/app/helpers/helpers_date.js'

import AppInputDateTS from '@/components/ui/inputs/AppInputDateTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { ref, /*watchEffect, watch*/ } from 'vue'

interface IProps {
    labelText?: string
}

withDefaults(defineProps<IProps>(), {labelText: 'Архив:'})

const emits = defineEmits<{
    (e: 'clickApply', dateInterval: ITaskPeriod): void
    (e: 'apply', dateInterval: ITaskPeriod): void
}>()

// __ Модели
const startDate = ref<Date>(new Date())
const endDate = ref<Date>(new Date())
// __ интервал дат в виде объекта
const dateInterval: ITaskPeriod = {start: '', end: ''}

// __ Устанавливаем интервал
const setPeriod = (pointDate: { id: string, value: string }) => {
    console.log('pointDate: ', pointDate)

    if (pointDate.id === 'start') {
        dateInterval.start = pointDate.value
    } else if (pointDate.id === 'end') {
        dateInterval.end = pointDate.value
    }
}

// __ Нажимаем Показать
const clickApply = () => {

    // __ Подготавливаем данные
    dateInterval.start = startDate.value.toISOString().slice(0, 10)
    dateInterval.end = endDate.value.toISOString().slice(0, 10)

    // __ Инвертируем, если дата начала больше даты окончания
    if (!compareDatesLogic(dateInterval.start, dateInterval.end)) {
        const start = dateInterval.start
        dateInterval.start = dateInterval.end
        dateInterval.end = start
    }

    emits('clickApply', dateInterval)
    emits('apply', dateInterval)
}

// watchEffect(() => {
//     console.log('startDate: ', startDate.value)
// })

</script>

<style scoped>

</style>
