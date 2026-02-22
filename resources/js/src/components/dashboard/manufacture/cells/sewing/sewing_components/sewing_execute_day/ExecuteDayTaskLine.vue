<template>


    <div class="flex items-center">
        <div
            :class="[getCheckClass(sewingLine)]"
            class="w-[25px] h-[25px] rounded flex items-center justify-center transition-all"
        >
            <span :class="getCheckClass(sewingLine)" class="text-[12px] font-semibold text-white">
                {{ getCheckSymbol(sewingLine) }}
            </span>
        </div>

        <div :class="fieldWidths.space"></div>

        <!-- __ Позиция -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.position.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.position"
            align="center"
            rounded="4"
        />

        <!-- __ Размер -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="getCoverSizeString(sewingLine)"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.size"
            align="center"
            rounded="4"
        />

        <!-- __ Название чехла -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="getSewingTaskModelCoverName(sewingLine)"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.coverName"
            rounded="4"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.amount.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.amount"
            align="center"
            rounded="4"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="time"
            :text-size="LINE_TEXT_SIZE"
            :type="time === '00с' ? 'danger' : getCheckType(sewingLine)"
            :width="fieldWidths.time"
            align="center"
            rounded="4"
        />

        <!-- __ Швейная машина -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingMachine"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.machine"
            align="center"
            rounded="4"
        />

        <!-- __ Ткань -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.textile ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.textile"
            align="center"
            rounded="4"
        />

        <!-- __ ТКЧ -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.model.main.tkch ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.tkch"
            align="center"
            rounded="4"
        />

        <!-- __ Кант -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.model.main.kant ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.kant"
            align="center"
            rounded="4"
        />

        <!-- __ Состав -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.composition ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.composition"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Примечание 1 -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.describe_1 ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.describe_1"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Примечание 2 -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.describe_2 ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.describe_2"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Примечание 3 -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.describe_3 ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.describe_3"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ finished_at -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="
                sewingLine.finished_at ?
                    formatTimeInFullFormat(sewingLine.finished_at) :
                    sewingLine.false_at ?
                        formatTimeInFullFormat(sewingLine.false_at) :
                        ''
            "
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.timeLabel"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Причина не выполнения -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.false_reason ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            :width="fieldWidths.reason"
            align="left"
            class="truncate"
            rounded="4"
        />

    </div>


</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IColorTypes, ISewingTaskLine } from '@/types'

import { SEWING_MACHINES } from '@/app/constants/sewing.ts'

import {
    getCoverSizeString, getSewingLineMachineType,
    getSewingTaskModelCoverName,
    getTimeString
} from '@/app/helpers/manufacture/helpers_sewing.ts'
import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'



interface IProps {
    sewingLine: ISewingTaskLine
    fieldWidths: Record<string, string>
}

const props = defineProps<IProps>()

const LINE_HEIGHT    = 'h-[25px]'
const LINE_TYPE      = 'dark'
const LINE_TEXT_SIZE = 'micro'


// __ Получаем символ завершенности
const getCheckSymbol = (sewingLine: ISewingTaskLine) => {
    if (!sewingLine.finished_at && !sewingLine.false_at) {
        return ''
    }

    if (sewingLine.finished_at) {
        return '✓'
    }

    return '✘'
}


// __ Получаем класс завершенности
const getCheckClass = (sewingLine: ISewingTaskLine) => {
    if (!sewingLine.finished_at && !sewingLine.false_at) {
        return 'bg-slate-400'
    }

    if (sewingLine.finished_at) {
        return 'bg-green-500'
    }

    return 'bg-red-500'
}


// __ Получаем тип завершенности
const getCheckType = (sewingLine: ISewingTaskLine): IColorTypes => {
    if (!sewingLine.finished_at && !sewingLine.false_at) {
        return LINE_TYPE
    }

    if (sewingLine.finished_at) {
        return 'success'
    }

    return 'danger'
}


// __ Получаем трудозатраты
const time = computed(() => getTimeString(props.sewingLine, true).replaceAll('.', ''))

const sewingMachine = computed(() => {
    const machine = getSewingLineMachineType(props.sewingLine)

    switch (machine) {
        case SEWING_MACHINES.UNIVERSAL:
            return 'У'
        case SEWING_MACHINES.AUTO:
            return 'А'
        case SEWING_MACHINES.SOLID_HARD:
            return 'ГС'
        case SEWING_MACHINES.SOLID_LITE:
            return 'ГП'
    }

    return '??'
})


</script>

<style scoped>

</style>
