<template>
    <div class="flex">

        <!-- __ Plug: -->
        <!--<AppLabelTS-->
        <!--    rounded="4"-->
        <!--    text=""-->
        <!--    text-size="mini"-->
        <!--    type="light"-->
        <!--    width="w-[30px]"-->
        <!--/>-->

        <!-- __ Надпись Всего: -->
        <AppLabelTS
            :height="height"
            :type="type"
            :width="width"
            align="center"
            rounded="rounded-[4px]"
            text="Всего:"
            text-size="mini"
        />

        <!-- __ Общее количество -->
        <AppLabelMultiLineTS
            :type="type"
            :height="height"
            :text="totalAmount.toString()"
            :width="fieldWidth"
            align="center"
            rounded="4"
            text-size="micro"
        />

        <!-- __ Суммарные Данные по каждому участку -->
        <div v-for="dataItem of totals" :key="dataItem.name" class="flex">
            <AppLabelMultiLineTS
                :color="dataItem.color"
                :height="height"
                :height-limit="height"
                :text="dataItem.titleArr"
                :width="fieldWidth"
                align="center"
                rounded="4"
                text-size="micro"
            />
        </div>

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IAssemblyTask, IAssemblyTaskLine, IColorTypes, ITotalsDataType } from '@/types'

import { DATA_TYPE_NOTHING } from '@/app/constants/assembly.ts'

import { getDataArrayTotal } from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'

interface IPops {
    tasks: IAssemblyTask[]
    height: string
    width: string
    type: IColorTypes
    fieldWidth?: string
    dataType?: ITotalsDataType
}

const props = withDefaults(defineProps<IPops>(), {
    fieldWidth: 'w-[40px]',
    dataType  : DATA_TYPE_NOTHING,
})

// __ Общий объект отображения
const totals = computed(() => getDataArrayTotal(props.tasks, props.dataType))

// __ Всего по количеству в СЗ в Дне в Смене
const totalAmount = computed(() => {
        return props.tasks.reduce((totalAcc, task) =>
            totalAcc + task.assembly_lines.reduce((acc: number, line: IAssemblyTaskLine) => acc + line.amount, 0), 0)
})

// console.log('totals: ', totals.value)

</script>

<style scoped>

</style>
