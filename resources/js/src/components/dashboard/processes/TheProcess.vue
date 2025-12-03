<template>
    <div v-if="!isLoading" class="flex justify-center">

        <div class="w-3/4">
            <div class="sticky top-0 w-full flex justify-center my-4">
                <AppLabelTS
                    :text="businessProcesses.name"
                    align="center"
                    height="h-20"
                    text-size="huge"
                    type="primary"
                    width="w-full"
                />
            </div>

            <div v-for="(process, idx) of businessProcessesAdjacencyList" :key="idx" class="mb-3">

                <!--<div v-if="idx != 1" class="flex justify-center">-->
                <!--    <AppArrowDownTS/>-->
                <!--</div>-->

                <div class="flex justify-center">
                    <AppLabelTS
                        :text="(counter++).toString()"
                        align="center"
                        height="h-20"
                        rounded="3"
                        text-size="large"
                        type="indigo"
                        width="w-[100px]"
                    />

                    <AppLabelTS
                        :text="idx.toString()"
                        align="center"
                        height="h-20"
                        rounded="3"
                        text-size="huge"
                        type="orange"
                        width="w-[100px]"
                    />

                    <div class="w-full mr-1">
                        <AppLabelTS
                            :text="process.node.name"
                            align="center"
                            height="h-10"
                            rounded="4"
                            text-size="huge"
                            type="dark"
                            width="w-full"
                        />

                        <AppLabelTS
                            :text="getProcessDefaultSettingTitle(process)"
                            align="center"
                            height="h-9"
                            rounded="4"
                            text-size="normal"
                            type="info"
                            width="w-full"
                        />

                    </div>

                    <!-- __ План/Управление планом -->
                    <AppLabelTS
                        :text="process.node.has_module ? 'Управление планом' : 'План'"
                        align="center"
                        class="cursor-pointer"
                        height="h-20"
                        rounded="4"
                        text-size="huge"
                        type="stone"
                        width="w-[250px]"
                        @click="goToModule(process.node)"
                    />

                </div>

            </div>

            <div class="flex justify-center my-4">
                <AppLabelTS
                    align="center"
                    height="h-20"
                    text="Кастомизация"
                    text-size="huge"
                    type="indigo"
                    width="w-[200px]"
                    @click="router.push({name: 'business.process.customization.order-moving'})"
                />
            </div>

        </div>

    </div>
</template>

<script lang="ts" setup>
import type { IBusinessProcessItem, IBusinessProcessList, IBusinessProcessRender } from '@/types'

import { onMounted, ref } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useBusinessProcessesStore } from '@/stores/BusinessProcessesStore'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import { BUSINESS_PROCECC_DRAFT } from '@/app/constants/business_processes.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { formatDayString } from '@/app/helpers/helpers_date'
// import AppArrowDownTS from '@/components/ui/arrows/AppArrowDownTS.vue'

const DEBUG = true

const route = useRoute()
const router = useRouter()

const businessProcessesStore = useBusinessProcessesStore()

const isLoading = ref(false)

let paramId: number = -1
let counter: number

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


// __ Надпись переходов
const getProcessDefaultSettingTitle = (process: IBusinessProcessRender) => {
    const defaultIdx = 0
    let title: string = ''

    if (!process.node.defaults) {
        return title
    }

    if (process.node.defaults[defaultIdx].process_node_ref_id === process.node.id) {
        return title
    }

    if (process.node.defaults[defaultIdx].offset === 0) {
        title = 'В день узла: '
    } else {
        title = Math.abs(process.node.defaults[defaultIdx].offset).toString() + ' '
        title += formatDayString(process.node.defaults[defaultIdx].offset) + ' '
        title += (process.node.defaults[defaultIdx].offset > 0 ? 'после' : 'до') + ' узла: '
    }

    title += businessProcessesAdjacencyList.value[process.node.defaults[defaultIdx].process_node_ref_id].node.name

    return title
}

// __ Переход на модуль
const goToModule = (process: IBusinessProcessItem) => {
    console.log('goToModule:', process.route_name)
    if (process.has_module) {
        router.push({name: process.route_name})
        return
    }

    router.push({
        name: 'plan.business.process.node',
        params: {
            businessProcessId: businessProcesses.value.id,
            businessProcessNodeId: process.id
        }
    })
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

            counter = 1 // __ Счетчик для сквозной нумерации процессов

            if (DEBUG) console.log('businessProcessesAdjacencyList: ', businessProcessesAdjacencyList.value)
            if (DEBUG) console.log('businessProcesses: ', businessProcesses.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false

})


</script>

<style scoped>

</style>
