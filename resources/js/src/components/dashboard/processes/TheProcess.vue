<template>
    <div v-if="!isLoading">
        <div class="flex justify-center my-4">
            <AppLabelTS
                :text="businessProcesses.name"
                align="center"
                height="h-20"
                text-size="huge"
                type="primary"
                width="w-1/2"
            />
        </div>

        <div v-for="(process, idx) of businessProcessesAdjacencyList" :key="idx">

            <div v-if="idx != 1" class="flex justify-center">
                <AppArrowDownTS/>
            </div>

            <div class="flex justify-center">
                <AppLabelTS
                    :text="idx.toString()"
                    align="center"
                    height="h-10"
                    text-size="huge"
                    type="orange"
                    width="w-[100px]"
                    rounded="3"
                />

                <AppLabelTS
                    :text="process.node.name"
                    align="center"
                    height="h-10"
                    text-size="huge"
                    type="dark"
                    width="w-1/4"
                    rounded="4"
                />
            </div>

        </div>


    </div>
</template>

<script lang="ts" setup>

import { useBusinessProcessesStore } from '@/stores/BusinessProcessesStore'
import { onMounted, ref } from 'vue'
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { useRoute, useRouter } from 'vue-router'
import type { IBusinessProcessList, IBusinessProcessRender } from '@/types'
import { BUSINESS_PROCECC_DRAFT } from '@/app/constants/business_processes.ts'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppArrowDownTS from '@/components/ui/arrows/AppArrowDownTS.vue'

const route = useRoute()
const router = useRouter()                 // Определяем роутер

const businessProcessesStore = useBusinessProcessesStore()


const isLoading = ref(false)

let paramId: number = -1

// __ Определяем переменные
const businessProcesses = ref<IBusinessProcessList>(BUSINESS_PROCECC_DRAFT)
const businessProcessesAdjacencyList = ref<IBusinessProcessRender[]>([])

// __ Получаем данные
const getBusinessProcesses = async (id: number) => {
    businessProcesses.value = await businessProcessesStore.getBusinessProcessById(id)
}
const getBusinessProcessAdjacencyList = async (id: number) => {
    businessProcessesAdjacencyList.value = await businessProcessesStore.getBusinessProcessAdjacencyList(id)
}


onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            // warn: Порядок важен!
            await router.isReady().then(() => {
                paramId = route.params.id as unknown as number
            })

            await getBusinessProcessAdjacencyList(paramId)
            await getBusinessProcesses(paramId)

            console.log('businessProcessesAdjacencyList: ', businessProcessesAdjacencyList.value)
            console.log('businessProcesses: ', businessProcesses.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false

})


</script>

<style scoped>

</style>
