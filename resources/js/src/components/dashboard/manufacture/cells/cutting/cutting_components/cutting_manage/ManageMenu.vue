<template>
    <!-- __ Менюха -->
    <div class="sticky top-0 p-1 mx-1 bg-blue-100 border-2 rounded-lg border-blue-400 w-[calc(100%-6px)]">
        <div>
            <div class="flex ml-0.5">

                <!-- __ Цвет ярлычка -->
                <AppLabelMultilineCheckBoxTS
                    :height="MENU_ITEM_HEIGHT"
                    :state="globalCuttingTaskOrderTypeColor"
                    :state-false-char="['Цвет ярлычка', 'типа заявки']"
                    :state-true-char="['Цвет ярлычка', 'статуса заявки']"
                    :width="MENU_ITEM_LENGTH"
                    align="center"
                    class="cursor-pointer"
                    state-false-type="success"
                    state-true-type="primary"
                    text-size="mini"
                    @label-click="globalCuttingTaskOrderTypeColor = !globalCuttingTaskOrderTypeColor"
                />

                <!-- __ Показать / Скрыть детали ШМ -->
                <AppLabelMultilineCheckBoxTS
                    :height="MENU_ITEM_HEIGHT"
                    :state="globalCuttingTaskFullDaysShow"
                    :state-false-char="['Показать', 'детали ШМ']"
                    :state-true-char="['Скрыть', 'детали ШМ']"
                    :width="MENU_ITEM_LENGTH"
                    align="center"
                    class="cursor-pointer"
                    state-false-type="success"
                    state-true-type="primary"
                    text-size="mini"
                    @label-click="globalCuttingTaskFullDaysShow = !globalCuttingTaskFullDaysShow"
                />

                <!-- __ Показать / Скрыть Трудозатраты -->
                <AppLabelMultilineCheckBoxTS
                    :height="MENU_ITEM_HEIGHT"
                    :state="globalCuttingTaskTimesShow"
                    :state-false-char="['Показать', 'трудозатраты']"
                    :state-true-char="['Скрыть', 'трудозатраты']"
                    :width="MENU_ITEM_LENGTH"
                    align="center"
                    class="cursor-pointer"
                    state-false-type="success"
                    state-true-type="primary"
                    text-size="mini"
                    @label-click="globalCuttingTaskTimesShow = !globalCuttingTaskTimesShow"
                />


            </div>
        </div>

        <!--<div class="min-h-[2px]">            </div>-->
    </div>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import AppLabelMultilineCheckBoxTS from '@/components/ui/labels/AppLabelMultilineCheckBoxTS.vue'

const cuttingStore = useCuttingStore()

const { globalCuttingTaskTimesShow, globalCuttingTaskFullDaysShow, globalCuttingTaskOrderTypeColor, } = storeToRefs(cuttingStore)

const MENU_ITEM_HEIGHT = 'h-[20px]'
const MENU_ITEM_LENGTH = 'w-[150px]'
</script>

<style scoped>

</style>
