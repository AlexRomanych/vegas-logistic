<template>


    <div class="flex items-center">
        <div
            :class="[getCheckClass(cuttingLine)]"
            class="w-[25px] h-[25px] rounded flex items-center justify-center transition-all"
        >
            <span :class="getCheckClass(cuttingLine)" class="text-[12px] font-semibold text-white">
                {{ getCheckSymbol(cuttingLine) }}
            </span>
        </div>

        <div :class="fieldWidths.space"></div>

        <!-- __ Позиция -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingLine.position.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.position"
            align="center"
            rounded="4"
        />

        <!-- __ Размер -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="getCoverSizeString(cuttingLine)"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.size"
            align="center"
            rounded="4"
        />

        <!-- __ Название чехла -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="getCuttingTaskModelCoverName(cuttingLine)"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.coverName"
            rounded="4"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingLine.amount.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.amount"
            align="center"
            rounded="4"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="time"
            :text-size="LINE_TEXT_SIZE"
            :type="time === '00с' ? 'danger' : getCheckType(cuttingLine)"
            :width="fieldWidths.time"
            align="center"
            rounded="4"
        />

        <!-- __ Элемент -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="detail"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.detail"
            align="center"
            rounded="4"
        />

        <!-- __ Стол -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="table"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.table"
            align="center"
            rounded="4"
        />


        <!-- __ Швейная машина -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingMachine"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.machine"
            align="center"
            rounded="4"
        />

        <!-- __ Ткань -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingLine.order_line.textile ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.textile"
            align="center"
            rounded="4"
        />

        <!-- __ КДЧ -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingLine.order_line.model.main.kdch ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.kdch"
            align="center"
            rounded="4"
        />

        <!-- __ Крой -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.cut"
            align="center"
            rounded="4"
            text="159x199"
        />

        <!-- __ Угол -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.angle"
            align="center"
            rounded="4"
            text="Угол"
        />

        <!-- __ Настил -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.layers"
            align="center"
            rounded="4"
            text="Настил"
        />

        <!-- __ Расход -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.expense"
            align="center"
            rounded="4"
            text="100.0"
        />

        <!-- __ Рулон -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.fabric_roll"
            align="center"
            rounded="4"
            text="15000"
        />

        <!-- __ Отметка Рулона -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.fabric_roll_at"
            align="center"
            rounded="4"
            text="28.05.2026"
        />

        <!-- __ Брак -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.defects"
            align="center"
            rounded="4"
            text="Брак"
        />

        <!-- __ Причина брака -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.defects_reason"
            align="center"
            rounded="4"
            text="Причина брака"
        />

        <!--&lt;!&ndash; __ ТКЧ &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :height="LINE_HEIGHT"-->
        <!--    :text="cuttingLine.order_line.model.main.tkch ?? ''"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="getCheckType(cuttingLine)"-->
        <!--    :width="fieldWidths.tkch"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--/>-->

        <!--&lt;!&ndash; __ Кант &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :height="LINE_HEIGHT"-->
        <!--    :text="cuttingLine.order_line.model.main.kant ?? ''"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="getCheckType(cuttingLine)"-->
        <!--    :width="fieldWidths.kant"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--/>-->


        <!-- __ Состав -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingLine.order_line.composition ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.composition"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Примечание 1 -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingLine.order_line.describe_1 ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.describe_1"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Примечание 2 -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingLine.order_line.describe_2 ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.describe_2"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Примечание 3 -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingLine.order_line.describe_3 ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.describe_3"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ finished_at -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="
                cuttingLine.finished_at ?
                    formatTimeInFullFormat(cuttingLine.finished_at) :
                    cuttingLine.false_at ?
                        formatTimeInFullFormat(cuttingLine.false_at) :
                        ''
            "
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.timeLabel"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Причина не выполнения -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingLine.false_reason ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.reason"
            align="left"
            class="truncate"
            rounded="4"
        />

        <!-- __ Заявка -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="cuttingLine.groupAttr"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.order"
            align="left"
            rounded="4"
        />

    </div>


</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IColorTypes, ICuttingTaskLine } from '@/types'

import { CUTTING_MACHINES } from '@/app/constants/cutting.ts'

import {
    getCoverSizeString, getCuttingLineMachineType,
    getCuttingTaskModelCoverName, getDetailTitle, getTableTitle,
    getTimeString
} from '@/app/helpers/manufacture/helpers_cutting.ts'
import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


interface IProps {
    cuttingLine: ICuttingTaskLine
    fieldWidths: Record<string, string>
}

const props = defineProps<IProps>()

const LINE_HEIGHT    = 'h-[25px]'
const LINE_TYPE      = 'dark'
const LINE_TEXT_SIZE = 'mini'

// __ Получаем символ завершенности
const getCheckSymbol = (cuttingLine: ICuttingTaskLine) => {
    if (!cuttingLine.finished_at && !cuttingLine.false_at) {
        return ''
    }

    if (cuttingLine.finished_at) {
        return '✓'
    }

    return '✘'
}


// __ Получаем класс завершенности
const getCheckClass = (cuttingLine: ICuttingTaskLine) => {
    if (!cuttingLine.finished_at && !cuttingLine.false_at) {
        return 'bg-slate-400'
    }

    if (cuttingLine.finished_at) {
        return 'bg-green-500'
    }

    return 'bg-red-500'
}


// __ Получаем тип завершенности
const getCheckType = (cuttingLine: ICuttingTaskLine): IColorTypes => {
    if (!cuttingLine.finished_at && !cuttingLine.false_at) {
        return LINE_TYPE
    }

    if (cuttingLine.finished_at) {
        return 'success'
    }

    return 'danger'
}


// __ Получаем трудозатраты
const time = computed(() => getTimeString(props.cuttingLine, true).replaceAll('.', ''))

const cuttingMachine = computed(() => {
    const machine = getCuttingLineMachineType(props.cuttingLine)

    switch (machine) {
        case CUTTING_MACHINES.UNIVERSAL:
            return 'У'
        case CUTTING_MACHINES.AUTO:
            return 'А'
        case CUTTING_MACHINES.SOLID_HARD:
            return 'ГС'
        case CUTTING_MACHINES.SOLID_LITE:
            return 'ГП'
    }

    return '??'
})


const detail = computed(() => getDetailTitle(props.cuttingLine, true))
const table  = computed(() => getTableTitle(props.cuttingLine, true))


</script>

<style scoped>

</style>
