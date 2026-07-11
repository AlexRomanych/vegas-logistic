<template>
    <div
        v-if="!isLoading"
        class="m-2 flex flex-col h-[calc(100vh-120px)] select-none"
    >
        <!-- __ Выводим Табы и закрепляем их сверху -->
        <div class="flex-none">
            <div class="flex items-center">
                <div v-for="tab in tabs" :key="tab.manufLine" class="mr-2">
                    <div :class="{'p-0.5 bg-blue-200 border-2 rounded-lg border-blue-700': tab.active}">
                        <AppLabelMultiLineTS
                            :text="[tab.manufLineTitle, '']"
                            :type="tab.type"
                            align="center"
                            height="h-[30px]"
                            text-size="small"
                            width="w-[150px]"
                            @click="setActiveTab(tab)"
                        />
                    </div>
                </div>
            </div>
            <TheDividerLine class="mt-2"/>
        </div>

        <div class="flex-1 max-h-[calc(100vh-200px)] max-w-full overflow-auto custom-scrollbar border border-slate-200 rounded-lg bg-slate-50">
            <div class="inline-block min-w-full align-middle">

                <!-- __ Выводим время переналадки (верхняя строка) -->
                <div class="sticky top-0 z-20 flex bg-slate-100 border-b border-slate-200">

                    <div class="sticky left-0 z-30 bg-slate-100 flex-none">
                        <AppLabelTS
                            :height="TITLE_HEIGHT"
                            :width="TITLE_WIDTH"
                            align="center"
                            direction="column"
                            rounded="0"
                            text=""
                            text-size="mini"
                            type="light"
                        />
                    </div>

                    <div v-for="time of renderTimes" :key="'col-' + time.id" class="flex-none">
                        <div v-if="time.id !== 0">
                            <AppLabelTS
                                :height="TITLE_HEIGHT"
                                :text="time.name"
                                :width="CELL_WIDTH"
                                align="center"
                                direction="column"
                                rounded="0"
                                text-size="mini"
                                type="primary"
                            />
                        </div>
                    </div>
                </div>

                <!--__ Левый столбец + данные -->
                <div>
                    <div v-for="(time, index) of renderTimes" :key="'row-' + time.id" class="flex hover:bg-slate-50/40">

                        <!-- __ Левый столбец -->
                        <div class="sticky left-0 z-10 bg-white shadow-[3px_0_6px_-3px_rgba(0,0,0,0.08)] flex-none">
                            <AppLabelTS
                                :height="CELL_HEIGHT"
                                :text="time.name"
                                :width="TITLE_WIDTH"
                                align="center"
                                rounded="0"
                                text-size="mini"
                                type="primary"
                            />
                        </div>

                        <!-- __ Коррекция верстки -->
                        <div class="ml-[-2px]"></div>

                        <!--__ Данные -->
                        <div class="flex items-center pl-1">
                            <div v-for="(subTime, subIndex) of time.collections_to!" :key="'cell-' + subTime.id" class="flex-none">
                                <AppInputNumberSingleTS
                                    :id="'t' + index.toString() + 's' + subIndex.toString()"
                                    v-model:inputNumber.number="subTime.tuning_time!"
                                    :bg-color="true"
                                    :disabled="getDisabled(time, subTime)"
                                    :height="CELL_HEIGHT"
                                    :show-spins="false"
                                    :show-zeros="showZeros === null ? subTime.db : showZeros"
                                    :type="getType(time, subTime)"
                                    :width="CELL_WIDTH"
                                    align="center"
                                    class="mr-[4px] mt-[2px]"
                                    placeholder=""
                                    text-size="mini"
                                    @take-focus="takeFocus"
                                    @leave-focus="handleTime(time, subTime)"
                                    @keyup.enter="handleTime(time, subTime)"
                                    @keydown.ctrl.delete="eraseTime(time, subTime)"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>

import { onMounted, reactive, ref } from 'vue'

import type { IBlockCollectionTime, IBlockManufLine, IColorTypes, } from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.js'

import {
    BLOCK_COLLECTION_TIME_DRAFT,
    LINE_0, LINE_1, LINE_2,
    LINE_1_NAME, LINE_2_NAME,
    LINE_1_TYPE, LINE_2_TYPE
} from '@/app/constants/blocks.ts'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import TheDividerLine from '@/components/dashboard/manufacture/cells/components/TheDividerLine.vue'
import AppInputNumberSingleTS from '@/components/ui/inputs/AppInputNumberSingleTS.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'

const isLoading = ref(true)
// __ End Loader

interface ITab {
    shown: boolean,
    typePassive: IColorTypes
    type: IColorTypes
    active: boolean
    manufLine: IBlockManufLine
    manufLineTitle: string
}

type ITabKey = 'common' | 'line_1' | 'line_2'
type ITabs = Record<ITabKey, ITab>

const blocksStore  = useBlocksStore()

// __ Подготавливаем переменные
const tuningTimes = ref<IBlockCollectionTime[]>([])
const renderTimes = ref<IBlockCollectionTime[]>([])

const showZeros = ref<null | boolean>(null) // Показывать нули в таблице


// __ Задаем отображение вкладок СМ
const TYPE_PASSIVE = 'dark'

// __ Константы
const CELL_HEIGHT  = 'min-h-[40px]'
const CELL_WIDTH   = 'w-[43px]'
const TITLE_HEIGHT = 'h-[220px]'
const TITLE_WIDTH  = 'w-[220px]'

// __ Вкладки
const tabs: ITabs = reactive({
    common: {
        shown         : true,
        active        : false,
        typePassive   : TYPE_PASSIVE,
        type          : 'primary',
        manufLine     : LINE_0,
        manufLineTitle: 'Все Линии',
    },
    line_1: {
        shown         : true,
        active        : false,
        typePassive   : TYPE_PASSIVE,
        type          : LINE_1_TYPE,
        manufLine     : LINE_1,
        manufLineTitle: LINE_1_NAME,
    },
    line_2: {
        shown         : true,
        active        : false,
        typePassive   : TYPE_PASSIVE,
        type          : LINE_2_TYPE,
        manufLine     : LINE_2,
        manufLineTitle: LINE_2_NAME,
    },
})


// __ Получаем время переналадки
const getTuningTime = async () => {
    tuningTimes.value = await blocksStore.getBlockCollectionsTuningTime() as IBlockCollectionTime[]
    // console.log('blockTimes: ', JSON.parse(JSON.stringify(tuningTimes.value.sort((a, b) => a.id - b.id))))

    tuningTimes.value.forEach((time, /*index*/) => {

        if (!time.collections_to) {
            time.collections_to = []
        }

        tuningTimes.value.forEach((subTime, /*subIndex*/) => {

            const findTime = time.collections_to?.find(item => item.id === subTime.id)
            if (!findTime) {
                const dubNullCollection = JSON.parse(JSON.stringify(BLOCK_COLLECTION_TIME_DRAFT)) as IBlockCollectionTime

                dubNullCollection.id          = subTime.id
                dubNullCollection.name        = subTime.name
                dubNullCollection.line        = subTime.line
                dubNullCollection.line_alt    = subTime.line_alt
                dubNullCollection.tuning_time = 0

                time.collections_to!.push(dubNullCollection)
            } else {
                findTime.db = true
            }
        })

        // Сортируем время по id, а потом по СМ, считая, что сортировка в javascript является стабильной
        time.collections_to = time.collections_to!
            .sort((a, b) => a.id - b.id)
            .sort((a, b) => Number(a.line) - Number(b.line))
    })

    // Делаем то же самое для всего массива
    tuningTimes.value = tuningTimes.value
        .sort((a, b) => a.id - b.id)
        .sort((a, b) => Number(a.line) - Number(b.line))

}


// __ Устанавливаем активную вкладку
const setActiveTab = (tab: ITab) => {

    let key: ITabKey
    for (key in tabs) {
        tabs[key].active = (tabs[key] === tab)
    }

    // Обновляем время в зависимости от выбранной вкладки
    renderTimes.value = JSON.parse(JSON.stringify(tuningTimes.value))

    // Если выбрана общая вкладка, то просто возвращаем исходный массив
    if (tab === tabs.common) {
        return
    }

    // Отфильтровываем массив по выбранной вкладке (СМ)
    renderTimes.value = renderTimes.value.filter(item => item.line === tab.manufLine)
    renderTimes.value.forEach(time => {
        time.collections_to = time.collections_to?.filter(collection => collection.line === tab.manufLine)
    })

}

// __ Получаем разукрашку для отображения в шаблоне
const getDisabled = (time: IBlockCollectionTime, subTime: IBlockCollectionTime) => {
    if (time.id === subTime.id) return true
    if (time.line === subTime.line || time.line_alt === subTime.line) {
        return false
    }
    // if (time.line === subTime.line) return false
    // if (time.line === LINE_1 && subTime.line === LINE_2 && time.line_alt === LINE_2) return false
    // if (time.line === LINE_2 && subTime.line === LINE_1 && time.line_alt === LINE_1) return false
    return true
}

// __ Получаем разукрашку для отображения в шаблоне
const getType = (time: IBlockCollectionTime, subTime: IBlockCollectionTime) => {
    if (getDisabled(time, subTime)) return 'light'

    if (time.line === subTime.line) {
        switch (time.line) {
            case LINE_1:
                return LINE_1_TYPE
            case LINE_2:
                return LINE_2_TYPE
        }
    }

    return 'dark'
}


// __ Обработчик изменения времени
const handleTime = async (time: IBlockCollectionTime, subTime: IBlockCollectionTime) => {
    console.log('time.id:', time.id)
    console.log('subTime.id:', subTime.id)
    console.log('tuning_time:', subTime.tuning_time)

    showZeros.value = subTime.db!

    // Находим исходник, из которого все копируется
    const findTime    = tuningTimes.value.find(item => item.id === time.id)
    const findSubTime = findTime?.collections_to?.find(item => item.id === subTime.id)

    // Выходим, если не заполнено время, возвращая копию из массива
    if (subTime.tuning_time === null) {
        if (findSubTime) {
            subTime.tuning_time = findSubTime.tuning_time
        }
        return
    }

    if (subTime.tuning_time === findSubTime?.tuning_time && subTime.db) return    // Выходим, если время не изменилось

    /*const res =*/
    await blocksStore.setBlockPicturesTuningTime(time.id, subTime.id, subTime.tuning_time) // Обновляем время

    if (findSubTime) {
        findSubTime.tuning_time = subTime.tuning_time   // Обновляем время в исходнике без перезагрузки
        findSubTime.db          = true   // Обновляем признак, что время из БД на сервере, а не сгенерировано на фронте
    }

}


// __ Обработчик изменения времени
const eraseTime = async (time: IBlockCollectionTime, subTime: IBlockCollectionTime) => {
    console.log('er time.id:', time.id)
    console.log('subTime.id:', subTime.id)
    console.log('tuning_time:', subTime.tuning_time)

    // Находим исходник, из которого все копируется
    const findTime = tuningTimes.value.find(item => item.id === time.id)
    if (!findTime) return

    const findSubTime = findTime.collections_to?.find(item => item.id === subTime.id)
    if (!findSubTime) return

    await blocksStore.deleteBlockPicturesTuningTime(time.id, subTime.id)

    subTime.tuning_time     = 0
    findSubTime.tuning_time = subTime.tuning_time // Обновляем время в исходнике без перезагрузки
    findSubTime.db          = false // Обновляем признак, что время из БД на сервере, а не сгенерировано на фронте
}


const takeFocus = (/*time: ITimeItem, subTime: ITimePictureSchema*/) => {
    showZeros.value = true
    // console.log('takeFocus:')
}

onMounted(async () => {

    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getTuningTime()
            setActiveTab(tabs.common)
        },
        undefined,
        // false,
    )
    isLoading.value = false
    console.log('tuningTime:', tuningTimes.value)
})

</script>

<style scoped>

</style>
