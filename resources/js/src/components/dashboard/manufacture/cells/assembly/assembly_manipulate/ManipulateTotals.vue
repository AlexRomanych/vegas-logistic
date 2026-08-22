<template>
    <div class="flex">

        <!-- __ Plug: -->
        <AppLabelTS
            rounded="4"
            text=""
            text-size="mini"
            type="light"
            width="w-[30px]"
        />

        <!-- __ Всего: -->
        <AppLabelTS
            align="right"
            rounded="4"
            text="Всего:"
            text-size="mini"
            type="stone"
            width="w-[927px]"
        />

        <!-- __ Суммарные Данные по каждому участку -->
        <div v-for="dataItem of totals" :key="dataItem.name" class="flex">
            <AppLabelTS
                :color="dataItem.color"
                :text="dataItem.title"
                align="center"
                rounded="4"
                text-size="micro"
                :width="fieldWidth"
            />
        </div>

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IAssemblyTask } from '@/types'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { getDataArrayTotal } from '@/app/helpers/manufacture/helpers_assembly.ts'

interface IPops {
    tasks: IAssemblyTask[]
    fieldWidth?: string
}

const props = withDefaults(defineProps<IPops>(), {
    fieldWidth: 'w-[40px]',
})

const totals = computed(() => getDataArrayTotal(props.tasks))

// console.log('totals: ', totals.value)

</script>

<style scoped>

</style>
