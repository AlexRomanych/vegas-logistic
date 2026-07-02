<template>
    <!-- __ Менюха -->
    <div class="sticky top-0 p-1 mx-1 bg-blue-100 border-2 rounded-lg border-blue-400 w-[calc(100%-6px)]">
        <div>
            <div class="flex ml-0.5">

                <!-- __ Цвет ярлычка -->
                <AppLabelMultilineCheckBoxTS
                    :height="MENU_ITEM_HEIGHT"
                    :state="globalBlockTaskOrderTypeColor"
                    :state-false-char="['Цвет ярлычка', 'типа заявки']"
                    :state-true-char="['Цвет ярлычка', 'статуса заявки']"
                    :width="MENU_ITEM_LENGTH"
                    align="center"
                    class="cursor-pointer"
                    state-false-type="success"
                    state-true-type="primary"
                    text-size="mini"
                    @label-click="globalBlockTaskOrderTypeColor = !globalBlockTaskOrderTypeColor"
                />

                <!-- __ Показать / Скрыть детали для Линий -->
                <AppLabelMultilineCheckBoxTS
                    :height="MENU_ITEM_HEIGHT"
                    :state="globalBlockTaskFullDaysShow"
                    :state-false-char="['Показать', 'детали Линий']"
                    :state-true-char="['Скрыть', 'детали Линий']"
                    :width="MENU_ITEM_LENGTH"
                    align="center"
                    class="cursor-pointer"
                    state-false-type="success"
                    state-true-type="primary"
                    text-size="mini"
                    @label-click="globalBlockTaskFullDaysShow = !globalBlockTaskFullDaysShow"
                />

                <!-- __ Показать / Скрыть Трудозатраты -->
                <AppLabelMultilineCheckBoxTS
                    :height="MENU_ITEM_HEIGHT"
                    :state="globalBlockTaskTimesShow"
                    :state-false-char="['Показать', 'трудозатраты']"
                    :state-true-char="['Скрыть', 'трудозатраты']"
                    :width="MENU_ITEM_LENGTH"
                    align="center"
                    class="cursor-pointer"
                    state-false-type="success"
                    state-true-type="primary"
                    text-size="mini"
                    @label-click="globalBlockTaskTimesShow = !globalBlockTaskTimesShow"
                />


            </div>
        </div>

        <!--<div class="min-h-[2px]">            </div>-->
    </div>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

import AppLabelMultilineCheckBoxTS from '@/components/ui/labels/AppLabelMultilineCheckBoxTS.vue'

const blocksStore = useBlocksStore()

const { globalBlockTaskTimesShow, globalBlockTaskFullDaysShow, globalBlockTaskOrderTypeColor, } = storeToRefs(blocksStore)

const MENU_ITEM_HEIGHT = 'h-[20px]'
const MENU_ITEM_LENGTH = 'w-[150px]'
</script>

<style scoped>

</style>
