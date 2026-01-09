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

                    <!-- __ Индекс типа Заявки -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.typeIndex"/>
                        <AppInputTextTSWrapper v-model="typeIndexFilter" :render-object="render.typeIndex"/>
                    </div>

                    <!-- __ Название -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.name"/>
                        <AppInputTextTSWrapper v-model="nameFilter" :render-object="render.name"/>
                    </div>

                    <!-- __ Отображаемое Название -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.displayName"/>
                        <AppInputTextTSWrapper v-model="displayNameFilter" :render-object="render.displayName"/>
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

                    <!-- __ Комментарий -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.comment"/>
                        <AppInputTextTSWrapper v-model="commentFilter" :render-object="render.comment"/>
                    </div>

                </div>
            </div>
        </div>

        <!-- __ Данные -->
        <div v-for="orderType of orderTypesRender" :key="orderType.id" class="ml-2 max-w-fit">
            <div class="flex ">

                <!-- __ id -->
                <AppLabelTSWrapper :arg="orderType" :render-object="render.id"/>

                <!-- __ Индекс типа Заявки -->
                <AppLabelTSWrapper :arg="orderType" :render-object="render.typeIndex"/>

                <!-- __ Название -->
                <AppLabelTSWrapper :arg="orderType" :render-object="render.name"/>

                <!-- __ Отображаемое Название -->
                <AppLabelTSWrapper :arg="orderType" :render-object="render.displayName"/>

                <!-- __ Цвет (Picker) -->
                <AppRGBPickerModalTS v-model="orderType.color" @confirm="saveOrderTypeColor($event, orderType)"/>

                <!-- __ Описание -->
                <AppLabelTSWrapper :arg="orderType" :render-object="render.description"/>

                <!-- __ Комментарий -->
                <AppLabelTSWrapper :arg="orderType" :render-object="render.comment"/>

            </div>
        </div>
    </div>

</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, watchEffect } from 'vue'

import type {
    IOrderType,
    IRenderData,
} from '@/types'

import { useOrdersStore } from '@/stores/OrdersStore.ts'

import AppLabelMultilineTSWrapper from '@/components/dashboard/orders/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
import AppInputTextTSWrapper from '@/components/dashboard/orders/components/AppInputTextTSWrapper.vue'
import AppRGBPickerModalTS from '@/components/ui/pickers/AppRGBPickerModalTS.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

const isLoading = ref(false)

const ordersStore = useOrdersStore()

const DEBUG = false

// __ Определяем переменные
const orderTypes       = ref<IOrderType[]>([])
const orderTypesRender = ref<IOrderType[]>([])

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
        color:          (order: IOrderType) => order.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍id...',
        data:           (order: IOrderType) => order.id.toString()
    },
    typeIndex:   {
        id:             () => 'type-index-search',
        header:         ['Шаблон', 'номера'],
        width:          'w-[100px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          (order: IOrderType) => order.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Idx...',
        data:           (order: IOrderType) => order.type_index
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
        color:          (order: IOrderType) => order.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Название...',
        data:           (order: IOrderType) => order.name
    },
    displayName: {
        id:             () => 'display-name-search',
        header:         ['Отображаемая', 'информация'],
        width:          'w-[200px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          (order: IOrderType) => order.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Название...',
        data:           (order: IOrderType) => order.display_name
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
        color:          (order: IOrderType) => order.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Цвет...',
        data:           (order: IOrderType) => order.color,
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
        color:          (order: IOrderType) => order.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Описание...',
        data:           (order: IOrderType) => order.description ?? ''
    },
    comment:     {
        id:             () => 'comment-search',
        header:         ['Комментарий', ''],
        width:          'w-[250px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        color:          (order: IOrderType) => order.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Комментарий...',
        data:           (order: IOrderType) => order.comment ?? ''
    },
})

// __ Фильтры
const idFilter          = ref('')
const typeIndexFilter   = ref('')
const nameFilter        = ref('')
const displayNameFilter = ref('')
const colorFilter       = ref('')
const commentFilter     = ref('')
const descriptionFilter = ref('')


// __ Получаем данные
const getOrderTypes = async () => {
    orderTypes.value = await ordersStore.getOrderTypes()
    orderTypes.value = orderTypes.value
        .map(orderType => ({...orderType, comment: orderType.comment ?? '', description: orderType.description ?? ''}))
        .sort((a, b) => a.id - b.id)
}

// __ Формируем отображение Заявок
const getOrderTypesRender = () => {
    orderTypesRender.value = orderTypes.value
}

// __ Сохраняем данные по цвету
const saveOrderTypeColor = async (event: string, orderType: IOrderType) => {
    await ordersStore.patchOrderTypeColor(orderType.id, event)
}


// __ Реализация фильтров
watchEffect(() => {
    orderTypesRender.value = orderTypes.value
        .filter(orderType => orderType.id.toString().toLowerCase().includes(idFilter.value.toLowerCase()))
        .filter(orderType => orderType.type_index.toLowerCase().includes(typeIndexFilter.value.toLowerCase()))
        .filter(orderType => orderType.name.toLowerCase().includes(nameFilter.value.toLowerCase()))
        .filter(orderType => orderType.display_name.toLowerCase().includes(displayNameFilter.value.toLowerCase()))
        .filter(orderType => orderType.color.toLowerCase().includes(colorFilter.value.toLowerCase()))
        .filter(orderType => orderType.comment?.toLowerCase().includes(commentFilter.value.toLowerCase()))
        .filter(orderType => orderType.description.toLowerCase().includes(descriptionFilter.value.toLowerCase()))
    return
})


onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getOrderTypes()
            if (DEBUG) console.log('orderTypes: ', orderTypes.value)

            getOrderTypesRender()
            if (DEBUG) console.log('orderTypesRender: ', orderTypesRender.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})

</script>

<style scoped>

</style>
