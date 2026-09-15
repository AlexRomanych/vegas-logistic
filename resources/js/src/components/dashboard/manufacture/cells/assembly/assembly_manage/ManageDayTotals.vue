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
            :height="height"
            :text="totalAmount.toString()"
            :type="type"
            :width="fieldWidth"
            align="center"
            rounded="4"
            text-size="micro"
        />

        <!-- __ Суммарные Данные по каждому участку -->
        <div v-for="dataItem of totals" :key="dataItem.name" class="flex">
            <template v-if="shown.get(dataItem.name)">
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
            </template>
        </div>

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'

import type { IAssemblySectorKeys, IAssemblyTask, IAssemblyTaskLine, IColorTypes, ITotalsDataType } from '@/types'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'

import {
    ASSEMBLY_SECTORS,
    ASSEMBLY_TASK_SECTOR_COCONUT,
    ASSEMBLY_TASK_SECTOR_COMMON, ASSEMBLY_TASK_SECTOR_FOAM_LAYER, ASSEMBLY_TASK_SECTOR_FOAM_SIDE, ASSEMBLY_TASK_SECTOR_LAMIT,
    ASSEMBLY_TASK_SECTOR_LATEX, ASSEMBLY_TASK_SECTOR_LAYER, ASSEMBLY_TASK_SECTOR_TABLE,
    DATA_TYPE_NOTHING
} from '@/app/constants/assembly.ts'

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


// __ Данные из Хранилища
const assemblyStore = useAssemblyStore()

const {
          globalAssemblyTaskFullDaysShow,
          globalAssemblyTaskSectorsShow,
          // globalAssemblyTaskTimesShow,
      } = storeToRefs(assemblyStore)

// __ Общий объект отображения без Заявки_Ф
const totals = computed(() => getDataArrayTotal(props.tasks, props.dataType).filter(item => item.name !== ASSEMBLY_TASK_SECTOR_COMMON))

// __ Создаем объект отображения в зависимости от пунктов меню в Клендаре Сборки
const shown = computed<Map<IAssemblySectorKeys, boolean>>(() => {
    const views = new Map()

    Object.values(ASSEMBLY_SECTORS).forEach(sector => {
        if ([
            ASSEMBLY_TASK_SECTOR_COCONUT,
            ASSEMBLY_TASK_SECTOR_LATEX,
            ASSEMBLY_TASK_SECTOR_LAYER,
            ASSEMBLY_TASK_SECTOR_FOAM_LAYER,
            ASSEMBLY_TASK_SECTOR_FOAM_SIDE,
        ].includes(sector.NAME)) {
            views.set(sector.NAME, globalAssemblyTaskSectorsShow.value)
        } else if ([
            ASSEMBLY_TASK_SECTOR_LAMIT,
            ASSEMBLY_TASK_SECTOR_TABLE,
        ].includes(sector.NAME)) {
            views.set(sector.NAME, globalAssemblyTaskFullDaysShow.value)
        }
    })

    return views
})

// __ Всего по количеству в СЗ в Дне в Смене
const totalAmount = computed(() => {
    return props.tasks.reduce((totalAcc, task) =>
        totalAcc + task.assembly_lines.reduce((acc: number, line: IAssemblyTaskLine) => acc + line.amount, 0), 0)
})

// console.log(showns.value)
// console.log('totals: ', totals.value)

</script>

<style scoped>

</style>
