<template>

    <div class="flex">

        <!-- __ Свернуть/развернуть -->
        <AppLabel
            :text="collapsed ? '▼' : '▲'"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="GLOBAL_TYPE"
            align="center"
            width="w-[30px]"
            @click="emit('toggleWorkers')"
        />

        <!-- __ Смена -->
        <AppLabel
            :align="GLOBAL_ALIGN"
            :text="`Смена: ${team}`"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="GLOBAL_TYPE"
            width="w-[100px]"
        />

        <!-- __ Количество рабочих -->
        <AppLabel
            :align="GLOBAL_ALIGN"
            :text="`Рабочих: ${workers.length} чел.`"
            :text-size="GLOBAL_TEXT_SIZE"
            :type="GLOBAL_TYPE"
            width="w-[150px]"
        />

    </div>

    <div v-if="!collapsed" class="ml-[34px] mb-3">

        <div v-for="worker of workers">

            <div class="flex">

                <!-- __ Фамилия -->
                <AppLabel
                    :align="WORKER_ALIGN"
                    :text="worker.surname"
                    :text-size="WORKER_TEXT_SIZE"
                    :type="WORKER_TYPE"
                    width="w-[150px]"
                />

                <!-- __ Имя -->
                <AppLabel
                    :align="WORKER_ALIGN"
                    :text="worker.name"
                    :text-size="WORKER_TEXT_SIZE"
                    :type="WORKER_TYPE"
                    width="w-[150px]"
                />

                <!-- __ Отчество -->
                <AppLabel
                    :align="WORKER_ALIGN"
                    :text="worker.patronymic"
                    :text-size="WORKER_TEXT_SIZE"
                    :type="WORKER_TYPE"
                    width="w-[150px]"
                />

            </div>

        </div>

    </div>


</template>

<script lang="ts" setup>

import type { IWorkerData } from '@/types'

import AppLabel from '@/components/ui/labels/AppLabel.vue'

interface Props {
    workers: IWorkerData[]
    collapsed: boolean
    team: number
}

/*const props =*/ defineProps<Props>()

const emit = defineEmits<{
    (e: 'toggleWorkers'): void
}>()

const GLOBAL_TYPE = 'stone'
const GLOBAL_TEXT_SIZE = 'small'
const GLOBAL_ALIGN = 'center'

const WORKER_TYPE = 'success'
const WORKER_ALIGN = 'left'
const WORKER_TEXT_SIZE = 'mini'

</script>

<style scoped>

</style>
