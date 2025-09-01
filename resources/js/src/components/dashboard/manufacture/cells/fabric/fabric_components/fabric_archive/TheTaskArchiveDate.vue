<template>

    <div class="flex">

        <!-- __ Свернуть/развернуть -->
        <AppLabel
            :text="task.collapsed ? '▼' : '▲'"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="GLOBAL_TYPE"
            align="center"
            width="w-[30px]"
            @click="emit('toggleTask')"
        />

        <!-- __ Дата -->
        <AppLabel
            :align="GLOBAL_ALIGN"
            :text="formatDateInFullFormat(task.date)"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="dateType"
            width="w-[200px]"
        />

        <!-- __ День недели -->
        <AppLabel
            :align="GLOBAL_ALIGN"
            :text="getDayOfWeek(new Date(task.date), false)"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="dateType"
            width="w-[100px]"
        />

        <!-- __ Начало смены -->
        <AppLabel
            v-if="task.common.start_at"
            :align="GLOBAL_ALIGN"
            :text="`Старт: ${formatDateAndTimeInShortFormat(task.common.start_at)}`"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="dateType"
            width="w-[220px]"
        />

        <!-- __ Окончание смены -->
        <AppLabel
            v-if="task.common.finish_at"
            :align="GLOBAL_ALIGN"
            :text="`Финиш: ${formatDateAndTimeInShortFormat(task.common.finish_at)}`"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="dateType"
            width="w-[220px]"
        />

        <!-- __ Длительность смены -->
        <AppLabel
            v-if="task.common.start_at && task.common.finish_at"
            :align="GLOBAL_ALIGN"
            :text="`Время: ${taskDuration}`"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="dateType"
            width="w-[220px]"
        />

    </div>

</template>

<script lang="ts" setup>
import type { ITaskDataItem } from '@/components/dashboard/manufacture/cells/fabric/TheFabricsArchive.vue'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import {
    formatDateAndTimeInShortFormat,
    formatDateInFullFormat, formatTimeWithLeadingZeros,
    getDayOfWeek,
    isWorkingDay
} from '@/app/helpers/helpers_date'
import { computed } from 'vue'


interface Props {
    task: ITaskDataItem
}

const props = defineProps<Props>()

const emit = defineEmits<{
    (e: 'toggleTask'): void
}>()

const GLOBAL_TYPE = 'dark'
const GLOBAL_TEXT_SIZE = 'small'
const GLOBAL_ALIGN = 'center'
const dateType = computed(() => isWorkingDay(new Date(props.task.date)) ? 'dark' : 'danger')
const taskDuration = computed(() => {
    if (!props.task.common.start_at) return '00:00:00'
    let tempDuration
    if (props.task.common.finish_at) {
        tempDuration = new Date(props.task.common.finish_at).getTime() - new Date(props.task.common.start_at).getTime()
    } else {
        tempDuration = new Date().getTime() - new Date(props.task.common.start_at).getTime()
    }

    tempDuration = Math.floor(tempDuration / 1000)
    return formatTimeWithLeadingZeros(tempDuration)
})


</script>

<style scoped>

</style>
