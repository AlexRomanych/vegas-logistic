<template>
    <!-- __ Менюха -->
    <div class="sticky top-0 p-1 mx-1 bg-blue-100 border-2 rounded-lg border-blue-400 w-[calc(100%-6px)]">
        <div>
            <div class="flex ml-0.5">

                <!-- __ Цвет ярлычка -->
                <AppLabelMultilineCheckBoxTS
                    :height="MENU_ITEM_HEIGHT"
                    :state="globalSewingTaskOrderTypeColor"
                    :state-false-char="['Цвет ярлычка', 'типа заявки']"
                    :state-true-char="['Цвет ярлычка', 'статуса заявки']"
                    :width="MENU_ITEM_LENGTH"
                    align="center"
                    class="cursor-pointer"
                    state-false-type="success"
                    state-true-type="primary"
                    text-size="mini"
                    @label-click="globalSewingTaskOrderTypeColor = !globalSewingTaskOrderTypeColor"
                />

                <!-- __ Показать / Скрыть детали ШМ -->
                <AppLabelMultilineCheckBoxTS
                    :height="MENU_ITEM_HEIGHT"
                    :state="globalSewingTaskFullDaysShow"
                    :state-false-char="['Показать', 'детали ШМ']"
                    :state-true-char="['Скрыть', 'детали ШМ']"
                    :width="MENU_ITEM_LENGTH"
                    align="center"
                    class="cursor-pointer"
                    state-false-type="success"
                    state-true-type="primary"
                    text-size="mini"
                    @label-click="globalSewingTaskFullDaysShow = !globalSewingTaskFullDaysShow"
                />

                <!-- __ Показать / Скрыть Трудозатраты -->
                <AppLabelMultilineCheckBoxTS
                    :height="MENU_ITEM_HEIGHT"
                    :state="globalSewingTaskTimesShow"
                    :state-false-char="['Показать', 'трудозатраты']"
                    :state-true-char="['Скрыть', 'трудозатраты']"
                    :width="MENU_ITEM_LENGTH"
                    align="center"
                    class="cursor-pointer"
                    state-false-type="success"
                    state-true-type="primary"
                    text-size="mini"
                    @label-click="globalSewingTaskTimesShow = !globalSewingTaskTimesShow"
                />


            </div>
        </div>

        <!--<div class="min-h-[2px]">            </div>-->
    </div>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'

import { useSewingStore } from '@/stores/SewingStore.ts'

import AppLabelMultilineCheckBoxTS from '@/components/ui/labels/AppLabelMultilineCheckBoxTS.vue'

const sewingStore = useSewingStore()

const { globalSewingTaskTimesShow, globalSewingTaskFullDaysShow, globalSewingTaskOrderTypeColor, } = storeToRefs(sewingStore)

const MENU_ITEM_HEIGHT = 'h-[20px]'
const MENU_ITEM_LENGTH = 'w-[150px]'
</script>

<style scoped>

</style>
