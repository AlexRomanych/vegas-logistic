<template>


    <div class="flex items-center">
        <div
            :class="[getCheckClass(cuttingLine)]"
            class="w-[25px] h-[30px] rounded flex items-center justify-center transition-all"
        >
            <span :class="getCheckClass(cuttingLine)" class="text-[12px] font-semibold text-white">
                {{ getCheckSymbol(cuttingLine) }}
            </span>
        </div>

        <div :class="fieldWidths.space"></div>

        <!-- __ Позиция -->
        <AppLabelTS
            :height="lineHeight"
            :text="ordering === 'position' ? cuttingLine.position.toString() : index.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.position"
            align="center"
            rounded="4"
        />

        <!-- __ Размер -->
        <AppLabelTS
            :height="lineHeight"
            :text="getCoverSizeString(cuttingLine)"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.size"
            align="center"
            rounded="4"
        />

        <!-- __ Название чехла -->
        <AppLabelTS
            :height="lineHeight"
            :text="getCuttingTaskModelCoverName(cuttingLine)"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.coverName"
            rounded="4"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :height="lineHeight"
            :text="cuttingLine.amount.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.amount"
            align="center"
            rounded="4"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :height="lineHeight"
            :text="time"
            :text-size="LINE_TEXT_SIZE"
            :type="time === '00с' ? 'danger' : getCheckType(cuttingLine)"
            :width="fieldWidths.time"
            align="center"
            rounded="4"
        />

        <!-- __ Элемент -->
        <AppLabelTS
            :height="lineHeight"
            :text="detail"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.detail"
            align="center"
            rounded="4"
        />

        <!-- __ Стол -->
        <AppLabelTS
            :height="lineHeight"
            :text="table"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.table"
            align="center"
            rounded="4"
        />


        <!-- __ Швейная машина -->
        <AppLabelTS
            :height="lineHeight"
            :text="cuttingMachine"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.machine"
            align="center"
            rounded="4"
        />

        <!--&lt;!&ndash; __ Ткань из Заявки &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :height="lineHeight"-->
        <!--    :text="cuttingLine.order_line.textile ?? ''"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="getCheckType(cuttingLine)"-->
        <!--    :width="fieldWidths.textile"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--/>-->


        <!-- __ Ткань из Спецификаций-->
        <AppLabelMultiLineTS
            :height="lineHeightFabric"
            :text="fabric"
            :text-size="fabricTextSize"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.textile"
            align="center"
            rounded="4"
        />

        <!-- __ КДЧ -->
        <AppLabelTS
            :class="kdchId ? 'cursor-pointer' : ''"
            :height="lineHeight"
            :text="kdch"
            :text-size="LINE_TEXT_SIZE"
            :type="kdchId ? 'indigo' : 'dark'"
            :width="fieldWidths.kdch"
            align="center"
            rounded="4"
            @click="showDoc"
        />

        <!-- __ Крой -->
        <AppLabelTS
            :height="lineHeight"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.cut"
            align="center"
            rounded="4"
            text="159x199"
        />

        <!-- __ Угол -->
        <AppLabelTS
            :height="lineHeight"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.angle"
            align="center"
            rounded="4"
            text="Угол"
        />

        <!-- __ Настил -->
        <AppLabelTS
            :height="lineHeight"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.layers"
            align="center"
            rounded="4"
            text="Настил"
        />

        <!-- __ Расход -->
        <AppLabelTS
            :height="lineHeight"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.expense"
            align="center"
            rounded="4"
            text="100.0"
        />

        <!-- __ Рулон -->
        <AppLabelTS
            :height="lineHeight"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.fabric_roll"
            align="center"
            rounded="4"
            text="15000"
        />

        <!-- __ Отметка Рулона -->
        <AppLabelTS
            :height="lineHeight"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.fabric_roll_at"
            align="center"
            rounded="4"
            text="10ч. 45м. 59с."
        />

        <!-- __ Брак -->
        <AppLabelTS
            :height="lineHeight"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.defects"
            align="center"
            rounded="4"
            text="Брак"
        />

        <!-- __ Причина брака -->
        <AppLabelTS
            :height="lineHeight"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(cuttingLine)"
            :width="fieldWidths.defects_reason"
            align="center"
            rounded="4"
            text="Причина брака"
        />

        <!--&lt;!&ndash; __ ТКЧ &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :height="lineHeight"-->
        <!--    :text="cuttingLine.order_line.model.main.tkch ?? ''"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="getCheckType(cuttingLine)"-->
        <!--    :width="fieldWidths.tkch"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--/>-->

        <!--&lt;!&ndash; __ Кант &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :height="lineHeight"-->
        <!--    :text="cuttingLine.order_line.model.main.kant ?? ''"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="getCheckType(cuttingLine)"-->
        <!--    :width="fieldWidths.kant"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--/>-->


        <!-- __ Состав -->
        <AppLabelTS
            :height="lineHeight"
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
            :height="lineHeight"
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
            :height="lineHeight"
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
            :height="lineHeight"
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
            :height="lineHeight"
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
            :height="lineHeight"
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
            :height="lineHeight"
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
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import { getDocFileKDCH, getKDCH } from '@/app/helpers/manufacture/helpers_textile.ts'


interface IProps {
    cuttingLine: ICuttingTaskLine
    fieldWidths: Record<string, string>
    index?: number
    ordering?: 'index' | 'position'
}

const props = withDefaults(defineProps<IProps>(), {
    index   : 0,
    ordering: 'position'
})


const emits = defineEmits<{
    (e: 'showDocument', payload: number): void
}>()

// const LINE_HEIGHT    = 'h-[25px]'
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
const fabric = computed(() => Array.from(new Set(props.cuttingLine.fabric_construct)))

// __ Возвращаем высоту строки в зависимости от количества ПС
const lineHeight = computed(() => {
    switch (fabric.value.length) {
        case 1:
            return 'h-[30px]'
        case 2:
            return 'h-[30px]'
        case 3:
            return 'h-[45px]'
        default:
            return 'h-[30px]'
    }
})

// __ Возвращаем высоту строки в зависимости от количества ПС для fabric
const lineHeightFabric = computed(() => fabric.value.length > 1 ? 'h-[15px]' : 'h-[30px]')

// __ Возвращаем размер шрифта для favbic в зависимости от количества ПС для fabric
const fabricTextSize = computed(() => fabric.value.length > 1 ? 'micro' : LINE_TEXT_SIZE)

const kdchId = computed(() => getDocFileKDCH(props.cuttingLine))
const kdch   = computed(() => getKDCH(props.cuttingLine) + (kdchId.value ? '🔍' : ''))

const showDoc = () => {
    if (!kdchId.value) {
        return
    }
    emits('showDocument', kdchId.value)
}

</script>

<style scoped>

</style>
