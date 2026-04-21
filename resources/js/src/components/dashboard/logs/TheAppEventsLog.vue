<template>
    In the TheAppEventsLog.vue
</template>

<script lang="ts" setup>
import type { IEventLog } from '@/types'

import { onMounted, ref } from 'vue'

import { useLogsStore } from '@/stores/LogsStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'


const logsStore = useLogsStore()

const isLoading = ref(false)

let entities: IEventLog[] = []
const entitiesRender        = ref<IEventLog[]>([])

const getEntities = async () => {
    entities = await logsStore.getLogsAppEvents()
}

const getEntitiesRender = () => {
    entitiesRender.value = entities
}

onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getEntities()
            console.log('EventLogs: ', entities)

            getEntitiesRender()
            // console.log('materialsRender: ', entitiesRender.value)
        },
        undefined
        // false,
    )

    isLoading.value = false
})
</script>

<style scoped>

</style>
