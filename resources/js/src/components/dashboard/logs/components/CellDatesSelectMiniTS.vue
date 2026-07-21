<template>
    <div class="flex justify-start gap-y-0">
        <div>
            <div class="flex gap-x-0">
                <!-- __ Инициализирующие метки начала -->
                <AppLabelTS
                    :bold="true"
                    :height="LABEL_HEIGHT"
                    :text="labelTextStart"
                    :text-size="LABEL_TEXT_SIZE"
                    :type="LABEL_TYPE"
                    :width="LABEL_WIDTH"
                    align="center"
                    rounded="4"
                />

                <div class="mt-[0px]">
                    <!-- __ Начало -->
                    <AppInputDateMiniTS
                        id="start"
                        v-model="startDate"
                        :width="dateWidth"
                        @getInputDate="setPeriod"
                    />
                </div>
            </div>

            <div class="flex gap-x-0 mt-[-2px]">
                <!-- __ Инициализирующие метки окончания -->
                <AppLabelTS
                    :bold="true"
                    :height="LABEL_HEIGHT"
                    :text="labelTextEnd"
                    :text-size="LABEL_TEXT_SIZE"
                    :type="LABEL_TYPE"
                    :width="LABEL_WIDTH"
                    align="center"
                    rounded="4"
                />

                <!-- __ Окончание -->
                <AppInputDateMiniTS
                    id="end"
                    v-model="endDate"
                    :width="dateWidth"
                    @getInputDate="setPeriod"
                />
            </div>
        </div>

        <!-- __ Кнопка Показать -->
        <div class="mb-[-2px] cursor-pointer">
            <AppLabelTS
                :bold="true"
                :text="labelTextSearch"
                align="center"
                height="h-[50px]"
                rounded="4"
                text-size="huge"
                type="warning"
                width="w-[50px]"
                @click="clickApply"
            />
        </div>

    </div>

</template>

<script lang="ts" setup>
import { ref } from 'vue'

import type { ITaskPeriod } from '@/types'

import { compareDatesLogic } from '@/app/helpers/helpers_date.js'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppInputDateMiniTS from '@/components/ui/inputs/AppInputDateMiniTS.vue'

interface IProps {
    period?: ITaskPeriod | null
    labelTextStart?: string
    labelTextEnd?: string
    labelTextSearch?: string
    dateWidth?: string,
}

const props = withDefaults(defineProps<IProps>(), {
    labelTextStart : '🚀',
    labelTextEnd   : '🏁',
    labelTextSearch: '🔍',
    dateWidth      : 'w-[120px]',
    period         : () => ({start: '', end: ''})
})

const emits = defineEmits<{
    (e: 'clickApply', dateInterval: ITaskPeriod): void
    (e: 'apply', dateInterval: ITaskPeriod): void
}>()


const LABEL_WIDTH     = 'w-[30px]'
const LABEL_HEIGHT    = 'h-[24px]'
const LABEL_TYPE      = 'dark'
const LABEL_TEXT_SIZE = 'mini'

// __ Модели
const startDate = ref<Date>(props.period?.start ? new Date(props.period.start) : new Date())
const endDate   = ref<Date>(props.period?.end ? new Date(props.period.end) : new Date())

// __ интервал дат в виде объекта
const dateInterval: ITaskPeriod = { start: '', end: '' }

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
    dateInterval.end   = endDate.value.toISOString().slice(0, 10)

    // __ Инвертируем, если дата начала больше даты окончания
    if (!compareDatesLogic(dateInterval.start, dateInterval.end)) {
        const start        = dateInterval.start
        dateInterval.start = dateInterval.end
        dateInterval.end   = start
    }

    emits('clickApply', dateInterval)
    emits('apply', dateInterval)
}

</script>

<style scoped>

</style>
