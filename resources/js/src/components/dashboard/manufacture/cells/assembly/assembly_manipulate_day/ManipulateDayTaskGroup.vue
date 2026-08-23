<template>
    <div class="flex">

        <!-- __ Collapsed -->
        <AppLabelTS
            :text="collapsed ? '▲' : '▼'"
            :type="targetColor"
            align="center"
            class="cursor-pointer"
            rounded="4"
            text-size="micro"
            width="w-[30px]"
            @click.exact="emits('toggleCollapse')"
        />

        <!-- __ Номер группы -->
        <AppLabelTS
            :text="group.group.group_number.toString()"
            :type="targetColor"
            align="center"
            class="cursor-pointer"
            rounded="4"
            text-size="mini"
            title="Ctrl + Click - Выделить все элементы Коллекции Блоков"
            width="w-[60px]"
            @click.exact="emits('toggleCollapse')"
            @click.ctrl="emits('selectGroupItems')"
        />

        <!-- __ Название SubCategory -->
        <AppLabelTS
            :text="group.group.name"
            :type="targetColor"
            class="cursor-pointer"
            rounded="4"
            text-size="mini"
            title="Ctrl + Click - Выделить все элементы Коллекции Блоков"
            width="w-[303px]"
            @click.exact="emits('toggleCollapse')"
            @click.ctrl="emits('selectGroupItems')"
        />

        <!-- __ Кол-во -->
        <AppLabelTS
            :text="amount.toString()"
            :type="DEFAULT_TYPE"
            align="center"
            class="cursor-pointer"
            rounded="4"
            text-size="mini"
            title="Ctrl + Click - Выделить все элементы Коллекции Блоков"
            width="w-[40px]"
            @click.exact="emits('toggleCollapse')"
            @click.ctrl="emits('selectGroupItems')"
        />

        <!-- __ Время -->
        <AppLabelTS
            :text="formatTimeWithLeadingZeros(totalTime)"
            :type="totalTime === 0 ? 'orange' : DEFAULT_TYPE"
            align="center"
            class="cursor-pointer"
            rounded="4"
            text-size="mini"
            title="Ctrl + Click - Выделить все элементы Коллекции Блоков"
            width="w-[100px]"
            @click.exact="emits('toggleCollapse')"
            @click.ctrl="emits('selectGroupItems')"
        />


        <!-- __ Длина м.п. -->
        <!--<AppLabelTS-->
        <!--    :text="`${subgroup.cutLengthTotal.toFixed(3)} м.п.`"-->
        <!--    :type="subgroup.cutLengthTotal === 0 ? 'danger' : DEFAULT_TYPE"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--    text-size="mini"-->
        <!--    width="w-[100px]"-->
        <!--/>-->

        <!--&lt;!&ndash; __ Всего, шт. &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :text="`Σ = ${subgroup.amount.total.toFixed(0)} шт. (${formatTimeWithLeadingZeros(subgroup.time.total, 'hour')})`"-->
        <!--    :type="subgroup.amount.total === 0 ? 'danger' : 'stone'"-->
        <!--    :width="FIELDS_AMOUNT_TIME_WIDTH"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--    text-size="mini"-->
        <!--/>-->

        <!--&lt;!&ndash; __ Выполнено, шт. &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :text="`✓ = ${subgroup.amount.done.toFixed(0)} шт. (${formatTimeWithLeadingZeros(subgroup.time.done, 'hour')})`"-->
        <!--    :width="FIELDS_AMOUNT_TIME_WIDTH"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--    text-size="mini"-->
        <!--    type="success"-->
        <!--/>-->

        <!--&lt;!&ndash; __ Не Выполнено, шт. &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :text="`✗ = ${subgroup.amount.incomplete.toFixed(0)} шт. (${formatTimeWithLeadingZeros(subgroup.time.incomplete, 'hour')})`"-->
        <!--    :width="FIELDS_AMOUNT_TIME_WIDTH"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--    text-size="mini"-->
        <!--    type="danger"-->
        <!--/>-->


    </div>

    <!--<div class="ml-2 pt-1 font-semibold italic underline">-->
    <!--    {{ subgroup.subgroupOrderTitle }}: {{ subgroup.subgroupName }} - <span-->
    <!--    class="text-blue-600">Всего: {{ subgroup.amount.total }} шт. ({{ formatTimeWithLeadingZeros(subgroup.time.total) }})</span> / <span-->
    <!--    class="text-green-600">Выполнено: {{ subgroup.amount.done }} шт. ({{ formatTimeWithLeadingZeros(subgroup.time.done) }})</span> / <span-->
    <!--    class="text-red-600">Не выполнено: {{ subgroup.amount.incomplete }} шт. ({{-->
    <!--        formatTimeWithLeadingZeros(subgroup.time.incomplete)-->
    <!--    }})</span>-->
    <!--    &lt;!&ndash;<span class="font-semibold italic underline">{{ subgroup.subgroupOrderTitle }}: {{ subgroup.subgroupName }}</span>&ndash;&gt;-->
    <!--    &lt;!&ndash;<span class="font-semibold italic underline">{{ getSubgroupTitle(subgroup) }}</span>&ndash;&gt;-->
    <!--</div>-->

</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IColorTypes, IMatrixManufactureGroup } from '@/types'

// import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'
// import { isTaskLineDone, isTaskLineFalse, } from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

interface IProps {
    group: IMatrixManufactureGroup
    collapsed?: boolean
}

const props = defineProps<IProps>()

const emits = defineEmits<{
    (e: 'toggleCollapse'): void
    (e: 'selectGroupItems'): void
}>()

const DEFAULT_TYPE = 'primary'
// const FIELDS_AMOUNT_TIME_WIDTH = 'w-[174px]'

// __ Количество
const amount = computed(() => props.group.group_lines.reduce((acc, line) => acc + line.order_line.amount, 0))

// __ Общее время
const totalTime = computed(() => (0))

// // __ Проверяем, все ли Строки выполнены
// const isAllLinesDone = computed(() => props.subgroup.lines.every(line => isTaskLineDone(line)))
//
// // __ Проверяем, все ли Строки Невыполнены
// const isAllLinesFalse = computed(() => props.subgroup.lines.every(line => isTaskLineFalse(line)))
//
// __ Получаем Раскраску
const targetColor = computed<IColorTypes>(() => {
    // if (isAllLinesDone.value) return 'success'
    // if (isAllLinesFalse.value) return 'danger'
    return DEFAULT_TYPE
})

</script>

<style scoped>

</style>
