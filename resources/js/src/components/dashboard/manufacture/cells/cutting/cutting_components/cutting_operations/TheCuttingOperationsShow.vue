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

                    <!-- __ Оборудование -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.machine"/>
                        <AppInputTextTSWrapper v-model="machineFilter" :render-object="render.machine"/>
                    </div>

                    <!-- __ Active -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.active"/>

                        <!-- __ Фильтр: Active -->
                        <AppSelectSimpleTS
                            v-if="render.active.show"
                            id="active"
                            :select-data="activeSelect"
                            :text-size="render.active.headerTextSize"
                            :type="
                                activeFilter === 0
                                ? 'primary'
                                : activeFilter === 1
                                    ? 'success'
                                    : 'danger'
                            "
                            :width="render.active.width"
                            align="center"
                            class="mt-[8px]"
                            height="h-[30px]"
                            @change="filterByActive"
                        />
                    </div>

                    <!-- __ Тип расчета (Динамический, Статический, ...) -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.type"/>
                        <AppInputTextTSWrapper v-model="typeFilter" :render-object="render.type"/>
                    </div>

                    <!-- __ Время операции -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.time"/>
                        <AppInputTextTSWrapper v-model="timeFilter" :render-object="render.time"/>
                    </div>

                    <!-- __ Цвет -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.color"/>
                        <AppInputTextTSWrapper v-model="colorFilter" :render-object="render.color"/>
                    </div>

                    <!-- __ Тип чехла -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.cover_type"/>
                        <AppInputTextTSWrapper v-model="coverTypeFilter" :render-object="render.cover_type"/>
                    </div>

                    <!-- __ Стол -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.table"/>
                        <AppInputTextTSWrapper v-model="tableFilter" :render-object="render.table"/>
                    </div>

                    <!-- __ Деталь, к которой применяется операция -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.detail"/>
                        <AppInputTextTSWrapper v-model="detailFilter" :render-object="render.detail"/>
                    </div>

                    <!-- __ Описание -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.description"/>
                        <AppInputTextTSWrapper v-model="descriptionFilter" :render-object="render.description"/>
                    </div>

                    <div>
                        <!-- __ + Типовая операция -->
                        <router-link :to="{ name: 'manufacture.cell.cutting.operations.create' }">
                            <AppLabelMultiLineTS
                                :text="['➕', '']"
                                align="center"
                                class="cursor-pointer"
                                rounded="4"
                                text-size="large"
                                type="warning"
                                width="w-[64px]"
                            />
                        </router-link>

                        <!-- __ Сброс фильтров -->
                        <div class=" mt-[8px]">
                            <AppLabelTS
                                id="filters-reset"
                                align="center"
                                class="cursor-pointer"
                                height="h-[29px]"
                                rounded="4"
                                text="Очистить"
                                text-size="mini"
                                type="orange"
                                width="w-[64px]"
                                @click="resetFilters"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- __ Данные -->
        <div v-for="cuttingOperation of cuttingOperationsRender" :key="cuttingOperation.id" class="ml-2 max-w-fit">
            <div class="flex ">

                <!-- __ id -->
                <AppLabelTSWrapper :arg="cuttingOperation" :render-object="render.id"/>

                <!-- __ Название -->
                <AppLabelTSWrapper :arg="cuttingOperation" :render-object="render.name"/>

                <!-- __ Оборудование -->
                <AppLabelTSWrapper :arg="cuttingOperation" :render-object="render.machine"/>

                <!-- __ Active -->
                <AppLabelTSWrapper :arg="cuttingOperation" :render-object="render.active"/>

                <!-- __ Тип расчета (Динамический, Статический, ...) -->
                <AppLabelTSWrapper :arg="cuttingOperation" :render-object="render.type"/>

                <!-- __ Время операции -->
                <AppLabelTSWrapper :arg="cuttingOperation" :render-object="render.time"/>

                <!-- __ Цвет (Picker) -->
                <AppRGBPickerModalTS
                    v-if="render.color.show"
                    v-model="cuttingOperation.color"
                    @confirm="saveCuttingOperationColor($event, cuttingOperation)"
                />

                <!-- __ Тип чехла -->
                <AppLabelTSWrapper :arg="cuttingOperation" :render-object="render.cover_type"/>

                <!-- __ Стол -->
                <AppLabelTSWrapper :arg="cuttingOperation" :render-object="render.table"/>

                <!-- __ Тип детали -->
                <AppLabelTSWrapper :arg="cuttingOperation" :render-object="render.detail"/>

                <!-- __ Описание -->
                <AppLabelTSWrapper :arg="cuttingOperation" :render-object="render.description"/>

                <!-- __ Удалить -->
                <AppLabelTS
                    v-if="CAN_DELETE"
                    align="center"
                    rounded="4"
                    text="🗑️"
                    text-size="mini"
                    type="danger"
                    width="w-[30px]"
                    @click="deleteOperation(cuttingOperation)"
                />

                <!-- __ Редактировать -->
                <router-link
                    :to="{ name: 'manufacture.cell.cutting.operations.edit', params: { id: cuttingOperation.id } }">
                    <AppLabelTS
                        v-if="CAN_EDIT"
                        align="center"
                        rounded="4"
                        text="✏️"
                        text-size="mini"
                        type="warning"
                        width="w-[30px]"
                    />
                </router-link>

            </div>
        </div>
    </div>

</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, watchEffect } from 'vue'

import type {
    IRenderData, ISelectData, ISelectDataItem, ICuttingOperation,
} from '@/types'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import AppLabelMultilineTSWrapper
    from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import AppInputTextTSWrapper from '@/components/dashboard/manufacture/cells/components/AppInputTextTSWrapper.vue'
import AppRGBPickerModalTS from '@/components/ui/pickers/AppRGBPickerModalTS.vue'
import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { DETAIL_PANEL, DETAIL_SIDE } from '@/app/constants/cutting.ts'


const isLoading = ref(false)

const cuttingStore = useCuttingStore()

// const DEBUG = true

// __ Права изменения
const CAN_EDIT   = true
const CAN_DELETE = true

// __ Определяем переменные
const cuttingOperations       = ref<ICuttingOperation[]>([])
const cuttingOperationsRender = ref<ICuttingOperation[]>([])


// __ Получаем раскраску операции
const getOperationType = (operation: ICuttingOperation) => {
    if (!operation) {
        return 'dark'
    }
    if (!operation.active) {
        return 'danger'
    } else if (operation.detail === DETAIL_SIDE) {
        return 'warning'
    } else if (operation.detail === DETAIL_PANEL) {
        return 'indigo'
    } else {
        return 'stone'
    }

}


// __ Объект отображения данных
const DEFAULT_WIDTH_BOOL = 'w-[70px]'
const DEFAULT_HEIGHT     = 'h-[30px]'
const HEADER_TYPE        = 'primary'
const DATA_TYPE          = 'primary'
const DEFAULT_TYPE       = 'dark'
const HEADER_TEXT_SIZE   = 'mini'
const DATA_TEXT_SIZE     = 'mini'
const HEADER_ALIGN       = 'center'
const DATA_ALIGN         = 'left'
// const DEFAULT_WIDTH = 'w-[100px]'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const DEFAULT_WIDTH_DATE = 'w-[100px]'
// const DATA_ALIGN_DEFAULT = 'center'

const render: IRenderData = reactive({
    id         : {
        id        : () => 'id-search',
        header    : ['ID', ''],
        width     : 'w-[50px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : () => DEFAULT_TYPE,
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (cuttingOperation: ICuttingOperation) => cuttingOperation.id.toString()
    },
    name       : {
        id        : () => 'name-search',
        header    : ['Название', 'типовой операции'],
        width     : 'w-[300px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : () => DEFAULT_TYPE,
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Название...',
        data          : (cuttingOperation: ICuttingOperation) => cuttingOperation.name
    },
    machine    : {
        id        : () => 'machine-search',
        header    : ['Оборудо-', 'вание'],
        width     : 'w-[100px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : () => DEFAULT_TYPE,
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Обор-ние...',
        data          : (cuttingOperation: ICuttingOperation) => cuttingOperation.machine ?? ''
    },
    active     : {
        id        : () => 'active-search',
        header    : ['Актуаль-', 'ность'],
        width     : DEFAULT_WIDTH_BOOL,
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : (cuttingOperation: ICuttingOperation) => cuttingOperation.active ? 'success' : 'danger',
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Active...',
        data          : (cuttingOperation: ICuttingOperation) => cuttingOperation.active ? '✓' : '✗'
    },
    type       : {
        id        : () => 'type-search',
        header    : ['Тип', 'расчета'],
        width     : 'w-[140px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : (cuttingOperation: ICuttingOperation) => {
            if (!cuttingOperation) {
                return 'dark'
            }
            switch (cuttingOperation.type) {
                case 'dynamic':
                    return 'indigo'
                case 'static':
                    return 'warning'
            }
        },
        // type:       () => DEFAULT_TYPE,
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Тип...',
        data          : (cuttingOperation: ICuttingOperation) => {
            switch (cuttingOperation.type) {
                case 'dynamic':
                    return 'Динамический'
                case 'static':
                    return 'Статический'
            }
        }
    },
    time       : {
        id        : () => 'time-search',
        header    : ['Время', 'операции, сек.'],
        width     : 'w-[100px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : (cuttingOperation: ICuttingOperation) => {
            if (!cuttingOperation) {
                return 'dark'
            }
            switch (cuttingOperation.type) {
                case 'dynamic':
                    return 'indigo'
                case 'static':
                    return 'warning'
            }
        },
        // type:       () => DEFAULT_TYPE,
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Время...',
        data          : (cuttingOperation: ICuttingOperation) => cuttingOperation.time.toString()
    },
    color      : {
        id        : () => 'color-search',
        header    : ['Цвет', 'ярлычка'],
        width     : 'w-[100px]',
        height    : DEFAULT_HEIGHT,
        show      : false,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : () => DEFAULT_TYPE,
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Цвет...',
        data          : (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        class         : 'cursor-pointer'
    },
    cover_type : {
        id        : () => 'cover-type-search',
        header    : ['Тип', 'чехла'],
        width     : 'w-[100px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : () => DEFAULT_TYPE,
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Чехол...',
        data          : (cuttingOperation: ICuttingOperation) => cuttingOperation.cover_type ?? ''
    },
    table      : {
        id        : () => 'table-search',
        header    : ['Стол', ''],
        width     : 'w-[100px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : () => DEFAULT_TYPE,
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Стол...',
        data          : (cuttingOperation: ICuttingOperation) => cuttingOperation.table ?? ''
    },
    detail     : {
        id        : () => 'detail-search',
        header    : ['Деталь', ''],
        width     : 'w-[100px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : (cuttingOperation: ICuttingOperation) => getOperationType(cuttingOperation),
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Деталь...',
        data          : (cuttingOperation: ICuttingOperation) => getDetailTitle(cuttingOperation),
        // data          : (cuttingOperation: ICuttingOperation) => cuttingOperation.detail ?? '',
    },
    description: {
        id        : () => 'description-search',
        header    : ['Описание', ''],
        width     : 'w-[450px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : () => DEFAULT_TYPE,
        // color:          (cuttingOperation: ICuttingOperation) => cuttingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Описание...',
        data          : (cuttingOperation: ICuttingOperation) => cuttingOperation.description ?? ''
    },
})

// __ Фильтры
const idFilter          = ref('')
const nameFilter        = ref('')
const machineFilter     = ref('')
const typeFilter        = ref('')
const timeFilter        = ref('')
const colorFilter       = ref('')
const descriptionFilter = ref('')
const activeFilter      = ref(0)
const tableFilter       = ref('')
const detailFilter      = ref('')
const coverTypeFilter   = ref('')

// __ Подготавливаем селекты
const activeSelect: ISelectData = {
    name: 'order-active',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: '✓', selected: false, disabled: false },
        { id: 2, name: '✗', selected: false, disabled: false },
    ],
}

// __ Обрабатываем селекты
const filterByActive = (value: ISelectDataItem) => {
    activeFilter.value = value.id
}

// __ Обнуляем фильтры
const resetFilters = () => {
    idFilter.value          = ''
    nameFilter.value        = ''
    machineFilter.value     = ''
    typeFilter.value        = ''
    timeFilter.value        = ''
    colorFilter.value       = ''
    descriptionFilter.value = ''
    activeFilter.value      = 0
    tableFilter.value       = ''
    detailFilter.value      = ''
    coverTypeFilter.value   = ''
}

// __ Получаем название детали
function getDetailTitle(cuttingOperation: ICuttingOperation) {
    if (!cuttingOperation || !cuttingOperation.detail) {
        return ''
    }
    switch (cuttingOperation.detail) {
        case DETAIL_PANEL:
            return 'Крышка'
        case DETAIL_SIDE:
            return 'Боковина    '
        default:
            return ''
    }
}

// __ Получаем данные
const getCuttingOperations = async () => {
    cuttingOperations.value = await cuttingStore.getCuttingOperations()
    cuttingOperations.value = cuttingOperations.value
        .map(cuttingOperation => ({
            ...cuttingOperation,
            description: cuttingOperation.description ?? '',
            machine    : cuttingOperation.machine ?? '',
            cover_type : cuttingOperation.cover_type ?? '',
            table      : cuttingOperation.table ?? '',
            // detail     : getDetailTitle(cuttingOperation),
            can_edit   : true
        }))
        .sort((a, b) => a.id - b.id)
}


// __ Формируем отображение Типовых операций
const getCuttingOperationsRender = () => {
    cuttingOperationsRender.value = cuttingOperations.value
}

// __ Удаляем типовую операцию
const deleteOperation = async (cuttingOperation: ICuttingOperation) => {
    return
}

// __ Сохраняем данные по цвету
const saveCuttingOperationColor = async (event: string, cuttingOperation: ICuttingOperation) => {
    return
    // await cuttingStore.patchCuttingOperationColor(cuttingOperation.id, event)
}


// __ Реализация фильтров
watchEffect(() => {
    cuttingOperationsRender.value = cuttingOperations.value
        .filter(orderType => orderType.id.toString().toLowerCase().includes(idFilter.value.toLowerCase()))
        .filter(orderType => orderType.name.toLowerCase().includes(nameFilter.value.toLowerCase()))
        .filter(orderType => orderType.machine!.toLowerCase().includes(machineFilter.value.toLowerCase()))
        .filter(orderType => {
            const calcType = orderType.type === 'dynamic' ? 'Динамический' : 'Статический'
            return calcType.toLowerCase().includes(typeFilter.value.toLowerCase())
        })
        .filter(orderType => orderType.time.toString().includes(timeFilter.value.toLowerCase()))
        .filter(orderType => orderType.color.toLowerCase().includes(colorFilter.value.toLowerCase()))
        .filter(orderType => orderType.description!.toLowerCase().includes(descriptionFilter.value.toLowerCase()))
        .filter(orderType => orderType.table!.toLowerCase().includes(tableFilter.value.toLowerCase()))
        .filter(orderType => orderType.cover_type!.toLowerCase().includes(coverTypeFilter.value.toLowerCase()))
        .filter(orderType => orderType.detail!.toLowerCase().includes(detailFilter.value.toLowerCase()))
        .filter(order => {
            if (activeFilter.value === 0) return true
            else if (activeFilter.value === 1) return order.active
            else if (activeFilter.value === 2) return !order.active
        })
    return
})


onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getCuttingOperations()
            getCuttingOperationsRender()
            // if (DEBUG) console.log('cuttingOperations: ', cuttingOperations.value)

        },
        undefined,
        // false,
    )

    isLoading.value = false
})

</script>

<style scoped>

</style>
