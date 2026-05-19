<template>
    <div v-if="!isLoading" class="ml-2 mt-2">
        <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit">
            <div>
                <div class="flex ml-0.5">

                    <!-- __ id -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.id"/>
                        <AppInputTextTSWrapper v-model="idFilter" :render-object="render.id"/>
                    </div>

                    <!-- __ Название -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.name"/>
                        <AppInputTextTSWrapper v-model="nameFilter" :render-object="render.name"/>
                    </div>

                    <!-- __ Порядковый номер -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.position"/>
                        <AppInputTextTSWrapper v-model="positionFilter" :render-object="render.position"/>
                    </div>

                    <!-- __ Цвет -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.color"/>
                        <AppInputTextTSWrapper v-model="colorFilter" :render-object="render.color"/>
                    </div>

                    <!-- __ Описание -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.description"/>
                        <AppInputTextTSWrapper v-model="descriptionFilter" :render-object="render.description"/>
                    </div>

                </div>
            </div>
        </div>

        <!-- __ Данные -->
        <div v-for="cuttingTaskStatus of cuttingTaskStatusesRender" :key="cuttingTaskStatus.id" class="ml-2 max-w-fit">
            <div class="flex ">

                <!-- __ id -->
                <AppLabelTSWrapper :arg="cuttingTaskStatus" :render-object="render.id"/>

                <!-- __ Название -->
                <AppLabelTSWrapper :arg="cuttingTaskStatus" :render-object="render.name"/>

                <!-- __ Порядковый номер -->
                <AppLabelTSWrapper :arg="cuttingTaskStatus" :render-object="render.position"/>

                <!-- __ Цвет (Picker) -->
                <AppRGBPickerModalTS v-model="cuttingTaskStatus.color" @confirm="saveCuttingTaskStatusColor($event, cuttingTaskStatus)"/>

                <!-- __ Описание -->
                <AppLabelTSWrapper :arg="cuttingTaskStatus" :render-object="render.description"/>

            </div>
        </div>
    </div>

</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, watchEffect } from 'vue'

import type {
    IRenderData, ICuttingTaskStatusEntity,
} from '@/types'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import AppLabelMultilineTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import AppInputTextTSWrapper from '@/components/dashboard/manufacture/cells/components/AppInputTextTSWrapper.vue'
import AppRGBPickerModalTS from '@/components/ui/pickers/AppRGBPickerModalTS.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

const isLoading = ref(false)

const cuttingStore = useCuttingStore()

// const DEBUG = false

// __ Определяем переменные
const cuttingTaskStatuses       = ref<ICuttingTaskStatusEntity[]>([])
const cuttingTaskStatusesRender = ref<ICuttingTaskStatusEntity[]>([])

// __ Объект отображения данных
const DEFAULT_HEIGHT   = 'h-[30px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'
// const DEFAULT_WIDTH = 'w-[100px]'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const DEFAULT_WIDTH_DATE = 'w-[100px]'
// const DATA_ALIGN_DEFAULT = 'center'

const render: IRenderData = reactive({
    id:          {
        id:             () => 'id-search',
        header:         ['ID', ''],
        width:          'w-[50px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          (cuttingTaskStatus: ICuttingTaskStatusEntity) => cuttingTaskStatus.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍id...',
        data:           (cuttingTaskStatus: ICuttingTaskStatusEntity) => cuttingTaskStatus.id.toString()
    },
    name:        {
        id:             () => 'name-search',
        header:         ['Название', ''],
        width:          'w-[250px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          (cuttingTaskStatus: ICuttingTaskStatusEntity) => cuttingTaskStatus.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Название...',
        data:           (cuttingTaskStatus: ICuttingTaskStatusEntity) => cuttingTaskStatus.name
    },
    position:        {
        id:             () => 'position-search',
        header:         ['Порядковый', 'номер'],
        width:          'w-[100px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          (cuttingTaskStatus: ICuttingTaskStatusEntity) => cuttingTaskStatus.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍№...',
        data:           (cuttingTaskStatus: ICuttingTaskStatusEntity) => cuttingTaskStatus.position.toString()
    },
    color:       {
        id:             () => 'color-search',
        header:         ['Цвет', 'ярлычка'],
        width:          'w-[100px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          (cuttingTaskStatus: ICuttingTaskStatusEntity) => cuttingTaskStatus.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Цвет...',
        data:           (cuttingTaskStatus: ICuttingTaskStatusEntity) => cuttingTaskStatus.color,
        class:          'cursor-pointer'
    },
    description: {  // __ Описание Заявки
        id:             () => 'description-search',
        header:         ['Описание', ''],
        width:          'w-[450px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          (cuttingTaskStatus: ICuttingTaskStatusEntity) => cuttingTaskStatus.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Описание...',
        data:           (cuttingTaskStatus: ICuttingTaskStatusEntity) => cuttingTaskStatus.description ?? ''
    },
})

// __ Фильтры
const idFilter          = ref('')
const nameFilter        = ref('')
const positionFilter     = ref('')
const colorFilter       = ref('')
const descriptionFilter = ref('')


// __ Получаем данные
const getCuttingTaskStatuses = async () => {
    cuttingTaskStatuses.value = await cuttingStore.getCuttingTaskStatuses()
    cuttingTaskStatuses.value = cuttingTaskStatuses.value
        .map(cuttingTaskStatus => ({...cuttingTaskStatus, description: cuttingTaskStatus.description ?? ''}))
        .sort((a, b) => a.id - b.id)
}

// __ Формируем отображение Заявок
const getCuttingTaskStatusesRender = () => {
    cuttingTaskStatusesRender.value = cuttingTaskStatuses.value
}

// __ Сохраняем данные по цвету
const saveCuttingTaskStatusColor = async (event: string, cuttingTaskStatus: ICuttingTaskStatusEntity) => {
    await cuttingStore.patchCuttingTaskStatusColor(cuttingTaskStatus.id, event)
}


// __ Реализация фильтров
watchEffect(() => {
    cuttingTaskStatusesRender.value = cuttingTaskStatuses.value
        .filter(orderType => orderType.id.toString().toLowerCase().includes(idFilter.value.toLowerCase()))
        .filter(orderType => orderType.name.toLowerCase().includes(nameFilter.value.toLowerCase()))
        .filter(orderType => orderType.position.toString().toLowerCase().includes(positionFilter.value.toLowerCase()))
        .filter(orderType => orderType.color.toLowerCase().includes(colorFilter.value.toLowerCase()))
        .filter(orderType => orderType.description!.toLowerCase().includes(descriptionFilter.value.toLowerCase()))
    return
})


onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getCuttingTaskStatuses()
            // if (DEBUG) console.log('cuttingTaskStatuses: ', cuttingTaskStatuses.value)
            getCuttingTaskStatusesRender()
            // if (DEBUG) console.log('cuttingTaskStatusesRender: ', cuttingTaskStatusesRender.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})

</script>

<style scoped>

</style>
