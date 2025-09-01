<template>
    <div class="flex justify-start items-end">

        <!-- __ Инициализирующая метка -->
        <div>
            <AppLabel
                :bold="true"
                align="center"
                height="h-[35px]"
                :text="labelText"
                width="w-[100px]"
                type="dark"
            />
        </div>

        <!-- __ Начало -->
        <div>
            <AppInputDate
                id="start"
                class="mr-1 ml-0.5"
                label="Начало"
                type="light"
                @getInputDate="setPeriod"
            />
        </div>

        <!-- __ Окончание -->
        <div>
            <AppInputDate
                id="end"
                label="Окончание"
                type="light"
                @getInputDate="setPeriod"
            />
        </div>

        <!-- __ Кнопка Показать -->
        <div>
            <AppButton
                id="apply"
                title="Показать"
                type="dark"
                width="w-[150px]"
                @buttonClick="clickApply"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import type { ITaskPeriod } from '@/types'

import { compareDatesLogic } from '@/app/helpers/helpers_date.js'

import AppInputDate from '@/components/ui/inputs/AppInputDate.vue'
import AppButton from '@/components/ui/buttons/AppButton.vue'
import AppLabel from '@/components/ui/labels/AppLabel.vue'

interface IProps {
    labelText?: string
}

withDefaults(defineProps<IProps>(), {labelText: 'Архив:'})

const emits = defineEmits<{
    (e: 'clickApply', dateInterval: ITaskPeriod): void
    (e: 'apply', dateInterval: ITaskPeriod): void
}>()

// интервал дат в виде объекта
const dateInterval: ITaskPeriod = {start: '', end: ''}

// __ Устанавливаем интервал
const setPeriod = (pointDate: {id: string, value: string}) => {
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
    const formattedDate = new Date().toISOString().slice(0, 10)  // дата в формате YYYY-MM-DD
    dateInterval.start = dateInterval.start ?? formattedDate
    dateInterval.end = dateInterval.end ?? formattedDate

    // __ Инвертируем, если дата начала больше даты окончания
    if (!compareDatesLogic(dateInterval.start, dateInterval.end)) {
        const start = dateInterval.start
        dateInterval.start = dateInterval.end
        dateInterval.end = start
    }

    emits('clickApply', dateInterval)
    emits('apply', dateInterval)
}

</script>

<style scoped>

</style>
