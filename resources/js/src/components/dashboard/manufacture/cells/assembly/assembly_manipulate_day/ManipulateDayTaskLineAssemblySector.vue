<template>

    <div class="flex flex items-center">

        <!-- __ Plug -->
        <div class="ml-[33px] mr-[2px]">
            <AppLabelTS
                :height="LINE_HEIGHT"
                rounded="4"
                text=""
                type="light"
                width="w-[30px]"
            />
        </div>

        <div
            :class="[checkClass]"
            class="w-[30px] h-[30px] rounded flex items-center justify-center transition-all mr-[2px]"
        >
            <span :class="checkClass" class="text-[12px] font-semibold text-white">
                {{ checkSymbol }}
            </span>
        </div>

        <!-- __ Размер -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="getSectorSize(sector)"
            :text-size="LINE_TEXT_SIZE"
            :type="sectorType"
            :width="fieldWidths.size"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Название материала -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sector.material_name"
            :text-size="LINE_TEXT_SIZE"
            :type="sectorType"
            :width="fieldWidths.name"
            align="left"
            class="truncate"
            rounded="4"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sector.amount.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="sectorType"
            :width="fieldWidths.amount"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            text=""
            :text-size="LINE_TEXT_SIZE"
            :type="sectorType"
            :width="fieldWidths.time"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Участок -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sectorName"
            :text-size="LINE_TEXT_SIZE"
            :type="sectorType"
            :width="fieldWidths.manuf_line"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ finished_at -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="
                sector.finished_at ?
                    formatTimeInFullFormat(sector.finished_at) :
                    sector.false_at ?
                        formatTimeInFullFormat(sector.false_at) :
                        ''
            "
            :text-size="LINE_TEXT_SIZE"
            :type="checkType"
            :width="fieldWidths.timeLabel"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Причина не выполнения -->
        <AppLabelTS
            :height="LINE_HEIGHT"
            :text="sector.false_reason ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="checkType"
            :width="fieldWidths.false_reason"
            align="left"
            class="truncate"
            rounded="4"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IAssemblyTaskLineSector } from '@/types'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { getSectorByName, getSectorSize, isTaskLineDone, isTaskLineFalse } from '@/app/helpers/manufacture/helpers_assembly.ts'
import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'

interface IProps {
    sector: IAssemblyTaskLineSector
    fieldWidths: Record<string, string>
}

const props = defineProps<IProps>()

// __ Возвращаем высоту строки в зависимости от количества ПС
const LINE_HEIGHT    = 'h-[30px]'
const LINE_TEXT_SIZE = 'micro'

// __ Получаем тип завершенности
const checkType = computed(() => {
    if (isTaskLineDone(props.sector)) {
        return 'success'
    }
    if (isTaskLineFalse(props.sector)) {
        return 'danger'
    }
    return 'dark'
})

// __ Получаем Участок
const sectorConst = computed(() => getSectorByName(props.sector.sector))

// __ Получаем раскраску участка
const sectorType = computed(() => sectorConst.value?.TYPE ?? 'dark')

// __ Получаем Название участка
const sectorName = computed(() => sectorConst.value?.TITLE ?? '')

// __ Получаем символ завершенности
const checkSymbol = computed(() => {
    if (isTaskLineDone(props.sector)) {
        return '✓'
    }
    if (isTaskLineFalse(props.sector)) {
        return '✘'
    }
    return ''
})

// __ Получаем класс завершенности
const checkClass = computed(() => {
    if (isTaskLineDone(props.sector)) {
        return 'bg-green-500'
    }
    if (isTaskLineFalse(props.sector)) {
        return 'bg-red-500'
    }
    return 'bg-slate-400'
})


</script>

<style scoped>

</style>
