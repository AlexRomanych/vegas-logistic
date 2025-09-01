<template>


    <div class="flex">

        <!-- __ Свернуть/развернуть -->
        <AppLabel
            :text="machine.collapsed ? '▼' : '▲'"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="GLOBAL_TYPE"
            align="center"
            width="w-[30px]"
            @click="machine.collapsed = !machine.collapsed"
        />

        <!-- __ Название машины -->
        <AppLabel
            :align="GLOBAL_ALIGN"
            :text="machine.machineName"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="GLOBAL_TYPE"
            width="w-[200px]"
            @click="machine.collapsed = !machine.collapsed"
        />

        <!-- __ Общая длина ткани -->
        <AppLabel
            :align="GLOBAL_ALIGN"
            :text="`Ткань: ${totalLength.toFixed(2)}м.п.`"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="GLOBAL_TYPE"
            width="w-[150px]"
        />

        <!-- __ Общая ПС -->
        <AppLabel
            :align="GLOBAL_ALIGN"
            :text="`ПС: ${totalFabricLength.toFixed(2)}м.п.`"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="GLOBAL_TYPE"
            width="w-[150px]"
        />

        <!-- __ Общие трудозатраты -->
        <AppLabel
            :align="GLOBAL_ALIGN"
            :text="`Труд-ты: ${totalProductivity}`"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="GLOBAL_TYPE"
            width="w-[200px]"
        />

    </div>

    <div v-if="!machine.collapsed" class="ml-[34px] mb-4">
        <!-- __ Шапка -->
        <TheTaskArchiveRollsHeader
            :rollsRender="render"
        />

        <!-- __ Рулоны -->
        <div v-for="roll of machine.rollsRender" :key="roll.id">
            <TheTaskArchiveRoll
                :roll="roll"
                :rollsRender="render"
            />
        </div>
    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IRenderData } from '@/types'
import type { IMachineDataExtended } from '@/components/dashboard/manufacture/cells/fabric/TheFabricsArchive.vue'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import TheTaskArchiveRollsHeader
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_archive/TheTaskArchiveRollsHeader.vue'
import TheTaskArchiveRoll
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_archive/TheTaskArchiveRoll.vue'
import { FABRIC_ROLL_STATUS } from '@/app/constants/fabrics.ts'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

interface Props {
    machine: IMachineDataExtended
    render: IRenderData
}

const props = defineProps<Props>()

/*
const emit = defineEmits<{
    (e: 'toggleMachine'): void
}>()
*/

const GLOBAL_TYPE = 'warning'
const GLOBAL_TEXT_SIZE = 'small'
const GLOBAL_ALIGN = 'center'


// __ Общая длина ткани
const totalLength = computed(() => {
    return props.machine.rollsRender.reduce((acc, roll) => {
        if (roll.status !== FABRIC_ROLL_STATUS.CANCELLED.CODE && roll.status !== FABRIC_ROLL_STATUS.FALSE.CODE) {
            return acc + roll.textile_length
        } else {
            return acc
        }
    }, 0)
})

// __ Общая длина ткани
const totalFabricLength = computed(() => {
    return props.machine.rollsRender.reduce((acc, roll) => {
        if (roll.status !== FABRIC_ROLL_STATUS.CANCELLED.CODE && roll.status !== FABRIC_ROLL_STATUS.FALSE.CODE) {
            return acc + roll.textile_length / roll.rate
        } else {
            return acc
        }
    }, 0)
})

// __ Общие трудозатраты
const totalProductivity = computed(() => {
    const totalTime = props.machine.rollsRender.reduce((acc, roll) => {
        if (roll.status !== FABRIC_ROLL_STATUS.CANCELLED.CODE && roll.status !== FABRIC_ROLL_STATUS.FALSE.CODE) {
            if (roll.start_at && roll.finish_at) {
                return acc + (new Date(roll.finish_at).getTime() - new Date(roll.start_at).getTime()) / 1000
            } else {
                return acc
            }
        } else {
            return acc
        }
    }, 0)

    return formatTimeWithLeadingZeros(totalTime)
})

</script>

<style scoped>

</style>
