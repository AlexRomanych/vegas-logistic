<template>
    <div class="flex">

        <!-- __ Collapsed -->
        <AppLabelTS
            :text="collapsed ? '▲' : '▼'"
            :type="isAllLinesDone ? 'success' : 'warning'"
            align="center"
            class="cursor-pointer"
            rounded="4"
            text-size="micro"
            width="w-[30px]"
            @click.exact="emits('toggleCollapse')"
        />

        <!-- __ Название SubCategory -->
        <AppLabelTS
            :text="subgroup.subgroupName"
            :type="isAllLinesDone ? 'success' : DEFAULT_TYPE"
            class="cursor-pointer"
            rounded="4"
            text-size="mini"
            title="Ctrl + Click - Выделить все элементы Коллекции Блоков"
            width="w-[367px]"
            @click.exact="emits('toggleCollapse')"
            @click.ctrl="emits('selectSubgroupItems')"
        />

        <!-- __ Кол-во -->
        <AppLabelTS
            :text="subgroup.amount.total.toString()"
            :type="DEFAULT_TYPE"
            align="center"
            class="cursor-pointer"
            rounded="4"
            text-size="mini"
            title="Ctrl + Click - Выделить все элементы Коллекции Блоков"
            width="w-[40px]"
            @click.exact="emits('toggleCollapse')"
            @click.ctrl="emits('selectSubgroupItems')"
        />

        <!-- __ Время -->
        <AppLabelTS
            :text="formatTimeWithLeadingZeros(subgroup.time.total, 'hour')"
            :type="DEFAULT_TYPE"
            align="center"
            class="cursor-pointer"
            rounded="4"
            text-size="mini"
            title="Ctrl + Click - Выделить все элементы Коллекции Блоков"
            width="w-[100px]"
            @click.exact="emits('toggleCollapse')"
            @click.ctrl="emits('selectSubgroupItems')"
        />

        <!-- __ Площадь -->
        <AppLabelTS
            :text="subgroup.square.total.toFixed(3)"
            :type="DEFAULT_TYPE"
            align="center"
            class="cursor-pointer"
            rounded="4"
            text-size="mini"
            title="Ctrl + Click - Выделить все элементы Коллекции Блоков"
            width="w-[70px]"
            @click.exact="emits('toggleCollapse')"
            @click.ctrl="emits('selectSubgroupItems')"
        />

        <!-- __ Приоритет -->
        <AppLabelTS
            :text="subgroup.priority.toString()"
            :type="DEFAULT_TYPE"
            align="center"
            class="cursor-pointer"
            rounded="4"
            text-size="mini"
            title="Ctrl + Click - Выделить все элементы Коллекции Блоков"
            width="w-[40px]"
            @click.exact="emits('toggleCollapse')"
            @click.ctrl="emits('selectSubgroupItems')"
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

        <!-- __ Всего, шт. -->
        <AppLabelTS
            :text="`Σ = ${subgroup.amount.total.toFixed(0)} шт. (${formatTimeWithLeadingZeros(subgroup.time.total, 'hour')})`"
            :type="subgroup.amount.total === 0 ? 'danger' : 'stone'"
            :width="FIELDS_AMOUNT_TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
        />

        <!-- __ Выполнено, шт. -->
        <AppLabelTS
            :text="`✓ = ${subgroup.amount.done.toFixed(0)} шт. (${formatTimeWithLeadingZeros(subgroup.time.done, 'hour')})`"
            :width="FIELDS_AMOUNT_TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="success"
        />

        <!-- __ Не Выполнено, шт. -->
        <AppLabelTS
            :text="`✗ = ${subgroup.amount.incomplete.toFixed(0)} шт. (${formatTimeWithLeadingZeros(subgroup.time.incomplete, 'hour')})`"
            :width="FIELDS_AMOUNT_TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="danger"
        />


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

import type { IBlockTaskLinesSubgroup, } from '@/types'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'
import { computed } from 'vue'
import { isTaskLineDone, } from '@/app/helpers/manufacture/helpers_blocks.ts'

interface IProps {
    subgroup: IBlockTaskLinesSubgroup
    collapsed?: boolean
}

const props = defineProps<IProps>()

const emits = defineEmits<{
    (e: 'toggleCollapse'): void
    (e: 'selectSubgroupItems'): void
}>()

const FIELDS_AMOUNT_TIME_WIDTH = 'w-[174px]'
const DEFAULT_TYPE             = 'primary'

// __ Проверяем, все ли Строки выполнены
const isAllLinesDone = computed(() => props.subgroup.lines.every(line => isTaskLineDone(line)))

</script>

<style scoped>

</style>
