<template>


    <div class="flex items-center">
        <div
            :class="[ getCheckClass(sewingLine)]"
            class="w-[25px] h-[25px] rounded flex items-center justify-center transition-all"
        >
                        <span :class="getCheckClass(sewingLine)"
                              class="text-[12px] font-semibold text-white">{{ getCheckSymbol(sewingLine) }}</span>

            <!--<span v-if="sewingLine.completed" class="text-[10px] text-white">✔</span>-->
            <!--<span v-else class="text-[10px] text-white">✘</span>-->

        </div>

        <!--<span class="m-2 text-xs font-bold text-slate-300 w-6">{{ sewingLine.position }}</span>-->

        <div class="min-w-[10px]"></div>

        <!-- __ Позиция -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.position.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            rounded="4"
            width="w-[30px]"
        />

        <!-- __ Размер -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="getCoverSizeString(sewingLine)"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            rounded="4"
            width="w-[80px]"
        />

        <!-- __ Название чехла -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="getSewingTaskModelCoverName(sewingLine)"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            rounded="4"
            width="w-[250px]"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.amount.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            rounded="4"
            width="w-[30px]"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="time"
            :text-size="LINE_TEXT_SIZE"
            :type="time === '00с' ? 'danger' : getCheckType(sewingLine)"
            align="center"
            rounded="4"
            width="w-[70px]"
        />

        <!-- __ Ткань -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.textile ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            rounded="4"
            width="w-[70px]"
        />

        <!-- __ ТКЧ -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.model.main.tkch ?? '' ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            rounded="4"
            width="w-[70px]"
        />

        <!-- __ Кант -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.model.main.kant ?? '' ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            rounded="4"
            width="w-[70px]"
        />

        <!-- __ Состав -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.composition ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            class="truncate"
            rounded="4"
            :width="DESCRIBE_WIDTH"
        />

        <!-- __ Примечание 1 -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.describe_1 ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            class="truncate"
            rounded="4"
            :width="DESCRIBE_WIDTH"
        />

        <!-- __ Примечание 2 -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.describe_2 ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            class="truncate"
            rounded="4"
            :width="DESCRIBE_WIDTH"
        />

        <!-- __ Примечание 3 -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.order_line.describe_3 ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            class="truncate"
            rounded="4"
            :width="DESCRIBE_WIDTH"
        />

        <!-- __ finished_at -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.finished_at ? formatTimeInFullFormat(sewingLine.finished_at) : ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="center"
            class="truncate"
            rounded="4"
            width="w-[80px]"
        />

        <!-- __ Причина не выполнения -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sewingLine.false_reason ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(sewingLine)"
            align="left"
            class="truncate"
            rounded="4"
            width="w-[250px]"
        />

    </div>



</template>

<script setup lang="ts">
import { computed } from 'vue'

import type { IColorTypes, ISewingTaskLine } from '@/types'

import {
    getCoverSizeString,
    getSewingTaskModelCoverName,
    getTimeString
} from '@/app/helpers/manufacture/helpers_sewing.ts'
import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


interface IProps {
    sewingLine: ISewingTaskLine
}

const props = defineProps<IProps>()

const LINE_HEIGHT    = 'h-[25px]'
const LINE_TYPE      = 'dark'
const LINE_TEXT_SIZE = 'micro'
const DESCRIBE_WIDTH = 'w-[70px]'




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

</script>

<style scoped>

</style>
