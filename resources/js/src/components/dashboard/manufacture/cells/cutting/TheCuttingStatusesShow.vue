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
        <div v-for="sewingTaskStatus of sewingTaskStatusesRender" :key="sewingTaskStatus.id" class="ml-2 max-w-fit">
            <div class="flex ">

                <!-- __ id -->
                <AppLabelTSWrapper :arg="sewingTaskStatus" :render-object="render.id"/>

                <!-- __ Название -->
                <AppLabelTSWrapper :arg="sewingTaskStatus" :render-object="render.name"/>

                <!-- __ Порядковый номер -->
                <AppLabelTSWrapper :arg="sewingTaskStatus" :render-object="render.position"/>

                <!-- __ Цвет (Picker) -->
                <AppRGBPickerModalTS v-model="sewingTaskStatus.color" @confirm="saveSewingTaskStatusColor($event, sewingTaskStatus)"/>

                <!-- __ Описание -->
                <AppLabelTSWrapper :arg="sewingTaskStatus" :render-object="render.description"/>

            </div>
        </div>
    </div>

</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, watchEffect } from 'vue'

import type {
    IRenderData, ISewingTaskStatusEntity,
} from '@/types'

import { useSewingStore } from '@/stores/SewingStore.ts'

import AppLabelMultilineTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import AppInputTextTSWrapper from '@/components/dashboard/manufacture/cells/components/AppInputTextTSWrapper.vue'
import AppRGBPickerModalTS from '@/components/ui/pickers/AppRGBPickerModalTS.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

const isLoading = ref(false)

const sewingStore = useSewingStore()

// const DEBUG = false

// __ Определяем переменные
const sewingTaskStatuses       = ref<ISewingTaskStatusEntity[]>([])
const sewingTaskStatusesRender = ref<ISewingTaskStatusEntity[]>([])

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
        color:          (sewingTaskStatus: ISewingTaskStatusEntity) => sewingTaskStatus.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍id...',
        data:           (sewingTaskStatus: ISewingTaskStatusEntity) => sewingTaskStatus.id.toString()
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
        color:          (sewingTaskStatus: ISewingTaskStatusEntity) => sewingTaskStatus.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Название...',
        data:           (sewingTaskStatus: ISewingTaskStatusEntity) => sewingTaskStatus.name
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
        color:          (sewingTaskStatus: ISewingTaskStatusEntity) => sewingTaskStatus.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍№...',
        data:           (sewingTaskStatus: ISewingTaskStatusEntity) => sewingTaskStatus.position.toString()
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
        color:          (sewingTaskStatus: ISewingTaskStatusEntity) => sewingTaskStatus.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Цвет...',
        data:           (sewingTaskStatus: ISewingTaskStatusEntity) => sewingTaskStatus.color,
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
        color:          (sewingTaskStatus: ISewingTaskStatusEntity) => sewingTaskStatus.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Описание...',
        data:           (sewingTaskStatus: ISewingTaskStatusEntity) => sewingTaskStatus.description ?? ''
    },
})

// __ Фильтры
const idFilter          = ref('')
const nameFilter        = ref('')
const positionFilter     = ref('')
const colorFilter       = ref('')
const descriptionFilter = ref('')


// __ Получаем данные
const getSewingTaskStatuses = async () => {
    sewingTaskStatuses.value = await sewingStore.getSewingTaskStatuses()
    sewingTaskStatuses.value = sewingTaskStatuses.value
        .map(sewingTaskStatus => ({...sewingTaskStatus, description: sewingTaskStatus.description ?? ''}))
        .sort((a, b) => a.id - b.id)
}

// __ Формируем отображение Заявок
const getSewingTaskStatusesRender = () => {
    sewingTaskStatusesRender.value = sewingTaskStatuses.value
}

// __ Сохраняем данные по цвету
const saveSewingTaskStatusColor = async (event: string, sewingTaskStatus: ISewingTaskStatusEntity) => {
    await sewingStore.patchSewingTaskStatusColor(sewingTaskStatus.id, event)
}


// __ Реализация фильтров
watchEffect(() => {
    sewingTaskStatusesRender.value = sewingTaskStatuses.value
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

            await getSewingTaskStatuses()
            // if (DEBUG) console.log('sewingTaskStatuses: ', sewingTaskStatuses.value)
            getSewingTaskStatusesRender()
            // if (DEBUG) console.log('sewingTaskStatusesRender: ', sewingTaskStatusesRender.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})

</script>

<style scoped>

</style>
