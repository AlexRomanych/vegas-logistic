<template>
    <div
        v-if="!isLoading"
        class="ml-2 mt-2"
    >
        <div class="sticky top-0 p-1 mb-1 bg-slate-200 border-2 rounded-lg border-slate-300 max-w-fit">
            <div class="ml-0.5">
                <div class="flex">
                    <!-- __ Collapsed -->
                    <div>
                        <AppLabelMultilineTSWrapper
                            :render-object="render.collapsed"
                            @click="toggleCollapsed"
                        />
                    </div>

                    <!-- __ Код из 1С -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.code_1c"/>
                        <AppInputTextTSWrapper
                            v-model="code_1cFilter"
                            :render-object="render.code_1c"
                        />
                    </div>

                    <!-- __ Название -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.collection_name"/>
                        <AppInputTextTSWrapper
                            v-model="nameFilter"
                            :render-object="render.collection_name"
                        />
                    </div>

                    <!-- __ Тип изделия -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.model_type"/>
                        <AppInputTextTSWrapper
                            v-model="manufactureTypeFilter"
                            :render-object="render.model_type"
                        />
                    </div>

                    <!-- __ Серия -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.serial"/>
                        <AppInputTextTSWrapper
                            v-model="serialFilter"
                            :render-object="render.serial"
                        />
                    </div>

                    <!-- __ Имя.Отчеты -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.name_report"/>
                        <AppInputTextTSWrapper
                            v-model="nameReportFilter"
                            :render-object="render.name_report"
                        />
                    </div>

                    <!-- __ Высота матраса -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.base_height"/>
                        <AppInputTextTSWrapper
                            v-model="baseHeightFilter"
                            :render-object="render.base_height"
                        />
                    </div>

                    <!-- __ Высота чехла -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.cover_height"/>
                        <AppInputTextTSWrapper
                            v-model="coverHeightFilter"
                            :render-object="render.cover_height"
                        />
                    </div>

                    <!-- __ Ткань -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.textile"/>
                        <AppInputTextTSWrapper
                            v-model="textileFilter"
                            :render-object="render.textile"
                        />
                    </div>

                    <!-- __ Состав ткани -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.textile_composition"/>
                        <AppInputTextTSWrapper
                            v-model="textileCompositionFilter"
                            :render-object="render.textile_composition"
                        />
                    </div>

                    <!-- __ Состав мягкого элемента -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.base_composition"/>
                        <AppInputTextTSWrapper
                            v-model="baseCompositionFilter"
                            :render-object="render.base_composition"
                        />
                    </div>

                    <!-- __ Базовый блок -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.base_block"/>
                        <AppInputTextTSWrapper
                            v-model="baseBlockFilter"
                            :render-object="render.base_block"
                        />
                    </div>

                    <!-- __ ППУ бортов -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.side_foam"/>
                        <AppInputTextTSWrapper
                            v-model="sideFoamFilter"
                            :render-object="render.side_foam"
                        />
                    </div>

                    <!-- __ Высота бортов -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.side_height"/>
                        <AppInputTextTSWrapper
                            v-model="sideHeightFilter"
                            :render-object="render.side_height"
                        />
                    </div>

                    <!-- __ Ламит -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.lamit"/>
                    </div>


                    <!-- __ Группы ДСЗ (Швейная машина) -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.sewing_machine"/>
                        <AppInputTextTSWrapper
                            v-model="sewingMachineFilter"
                            :render-object="render.sewing_machine"
                        />
                    </div>

                    <!-- __ Тип Чехла -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.cover_type"/>
                        <AppInputTextTSWrapper
                            v-model="coverTypeFilter"
                            :render-object="render.cover_type"
                        />
                    </div>

                    <!-- __ Кант -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.kant"/>
                        <AppInputTextTSWrapper
                            v-model="kantFilter"
                            :render-object="render.kant"
                        />
                    </div>

                    <!-- __ ТКЧ -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.tkch"/>
                        <AppInputTextTSWrapper
                            v-model="tkchFilter"
                            :render-object="render.tkch"
                        />
                    </div>

                    <!-- __ Штрихкод -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.barcode"/>
                        <AppInputTextTSWrapper
                            v-model="barcodeFilter"
                            :render-object="render.barcode"
                        />
                    </div>

                    <!-- __ Тип производства -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.manufacture_type"/>
                        <AppInputTextTSWrapper
                            v-model="manufactureTypeFilter"
                            :render-object="render.manufacture_type"
                        />
                    </div>

                    <!-- __ Группа сортировки СЗ -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.manufacture_group"/>
                        <AppInputTextTSWrapper
                            v-model="manufactureGroupFilter"
                            :render-object="render.manufacture_group"
                        />
                    </div>

                    <!-- __ Статус производства -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.manufacture_status"/>
                        <AppInputTextTSWrapper
                            v-model="manufactureStatusFilter"
                            :render-object="render.manufacture_status"
                        />
                    </div>

                    <!-- __ Тип производства -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.sewing_operation_schema"/>
                        <AppInputTextTSWrapper
                            v-model="sewingOperationSchemaFilter"
                            :render-object="render.sewing_operation_schema"
                        />
                    </div>

                </div>
            </div>
        </div>

        <div class="ml-2">
            <!-- __ Данные (Коллекции) -->
            <div
                v-for="collection of entitiesRender"
                :key="collection.code_1c"
                class="max-w-fit"
            >
                <template v-if="collection.shown">

                    <div class="flex">
                        <!-- __ Collapsed -->
                        <AppLabelTSWrapper
                            :arg="collection"
                            :render-object="render.collapsed"
                            @click="render.collapsed.click!(collection)"
                        />

                        <!-- __ Код из 1С -->
                        <AppLabelTSWrapper
                            :arg="collection"
                            :render-object="render.code_1c"
                            class="cursor-pointer"
                            @click="render.collapsed.click!(collection)"
                        />

                        <!-- __ Название -->
                        <AppLabelTSWrapper
                            :arg="collection"
                            :render-object="render.collection_name"
                            @click="render.collapsed.click!(collection)"
                        />

                    </div>

                    <!-- __ Данные (Модели) -->
                    <div
                        v-for="model of collection.models"
                        :key="model.code_1c"
                        class="ml-[34px] max-w-fit"
                    >
                        <template v-if="!collection.collapsed">

                            <template v-for="i of 2" :key="i">

                                <div class="flex">

                                    <template v-if="i === 1">
                                        <!-- __ Cover Collapsed -->
                                        <div v-if="model.cover">
                                            <AppLabelTSWrapper
                                                :arg="model"
                                                :render-object="render.cover_collapsed"
                                                @click="render.cover_collapsed.click!(model)"
                                            />
                                        </div>
                                        <div v-else>
                                            <AppLabelTSWrapper
                                                :arg="model"
                                                :render-object="render.plug"
                                            />
                                        </div>
                                    </template>
                                    <template v-else-if="i === 2 && model.cover && !model.cover_collapsed">
                                        <AppLabelTSWrapper
                                            :arg="model"
                                            :render-object="render.plug"
                                        />
                                    </template>

                                    <template v-if="i === 1 || (i === 2 && model.cover && !model.cover_collapsed)">

                                        <!-- __ Код из 1С -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :class="i === 2 ? 'italic' : ''"
                                            :render-object="render.code_1c"
                                        />

                                        <!-- __ Название -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :class="i === 2 ? 'italic' : ''"
                                            :render-object="render.model_name"
                                        />

                                        <!-- __ Тип изделия -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.model_type"
                                        />

                                        <!-- __ Серия -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.serial"
                                        />

                                        <!-- __ Имя.Отчеты -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.name_report"
                                        />

                                        <!-- __ Высота матраса -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.base_height"
                                        />

                                        <!-- __ Высота чехла -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.cover_height"
                                        />

                                        <!-- __ Ткань -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.textile"
                                        />

                                        <!-- __ Состав ткани -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.textile_composition"
                                        />

                                        <!-- __ Состав мягкого элемента -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.base_composition"
                                        />

                                        <!-- __ Базовый блок -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.base_block"
                                        />

                                        <!-- __ ППУ бортов -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.side_foam"
                                        />

                                        <!-- __ Высота бортов -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.side_height"
                                        />

                                        <!-- __ Ламит -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.lamit"
                                        />

                                        <!-- __ Группы ДСЗ (Швейная машина) -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.sewing_machine"
                                        />

                                        <!-- __ Тип Чехла -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.cover_type"
                                        />

                                        <!-- __ Кант -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.kant"
                                        />

                                        <!-- __ ТКЧ -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.tkch"
                                        />

                                        <!-- __ Штрихкод -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.barcode"
                                        />

                                        <!-- __ Тип производства -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.manufacture_type"
                                        />

                                        <!-- __ Группа сортировки СЗ -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.manufacture_group"
                                        />

                                        <!-- __ Статус производства -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.manufacture_status"
                                        />

                                        <!-- __ Тип производства -->
                                        <AppLabelTSWrapper
                                            :arg="i === 1 ? model : model.cover"
                                            :render-object="render.sewing_operation_schema"
                                        />

                                    </template>

                                </div>
                            </template>

                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>


</template>

<script lang="ts" setup>
import type {
    IMaterialGroup, IModel, IModelCollection, IRenderData
} from '@/types'

import { onMounted, reactive, ref, watchEffect } from 'vue'

import { useModelsStore } from '@/stores/ModelsStore'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppInputTextTSWrapper from '@/components/dashboard/models/components/AppInputTextTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/models/components/AppLabelTSWrapper.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/models/components/AppLabelMultilineTSWrapper.vue'


const isLoading = ref(false)

const modelsStore = useModelsStore()

let entities: IModelCollection[] = []
const entitiesRender             = ref<IModelCollection[]>([])


// __ Глобальный Collapse
const collapseAll = ref(true)

// __ Объект отображения данных
// const DEFAULT_WIDTH = 'w-[100px]'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const DEFAULT_WIDTH_DATE = 'w-[100px]'
const DEFAULT_HEIGHT   = 'h-[20px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'
const CHECK_PROPERTY   = 'cover_collapsed'
// const DATA_ALIGN_DEFAULT = 'center'

// __ Получаем тип раскраски
const getType = (entity: IModel | IModelCollection) => {
    if (!entity) return DEFAULT_TYPE
    if (Object.hasOwn(entity, 'models')) {
        return 'stone'
    }
    return CHECK_PROPERTY in entity ? DEFAULT_TYPE : 'indigo'
}

const render: IRenderData = reactive({
    collapsed              : {
        header        : ['▲', '▼'],
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : (collection: IModelCollection) => (collection.collapsed ? '▲' : '▼'),
        click         : (collection: IModelCollection) => (collection.collapsed = !collection.collapsed),
        class         : 'cursor-pointer',
    },
    plug                   : {
        header        : ['', ''],
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : () => 'light',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : () => '',
    },
    cover_collapsed        : {
        header        : ['▲', '▼'],
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : (model: IModel) => (model.cover_collapsed ? '▲' : '▼'),
        click         : (model: IModel) => (model.cover_collapsed = !model.cover_collapsed),
        class         : 'cursor-pointer',
    },
    code_1c                : {
        id            : () => 'id-search',
        header        : ['Код', ''],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel | IModelCollection) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Код...',
        data          : (group: IMaterialGroup) => group.code_1c,

    },
    collection_name        : {
        id            : () => 'name-search',
        header        : ['Название', 'модели'],
        width         : 'w-[334px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel | IModelCollection) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Название...',
        data          : (collection: IModelCollection) => collection.name,
        class         : 'cursor-pointer',
        // color: (order: IRenderOrder) => order.order_type.color,
        // title: (order: IRenderOrder) => order.order_type.display_name,
    },
    model_name             : {
        id            : () => 'model-name-search',
        header        : ['Название', 'модели'],
        width         : 'w-[300px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Название...',
        data          : (model: IModel) => model.name,
    },
    model_type             : {
        id            : () => 'model-type-search',
        header        : ['Тип', 'изделия'],
        width         : 'w-[150px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Тип изделия...',
        data          : (model: IModel) => model.model_type ? model.model_type.name : '',
        title         : (model: IModel) => model.model_type ? model.model_type.name : '',
        class         : 'truncate'
    },
    serial                 : {
        id            : () => 'serial-name-search',
        header        : ['Серия', ''],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍...',
        data          : (model: IModel) => model.serial ?? '',
    },
    name_report            : {
        id            : () => 'name-report-search',
        header        : ['Имя для', 'отчетов'],
        width         : 'w-[200px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Имя для отчетов...',
        data          : (model: IModel) => model.name_report ?? '',
    },
    base_height            : {
        id            : () => 'base-height-search',
        header        : ['Высота', 'матраса'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍h(м)',
        data          : (model: IModel) => model.base_height === 0 ? '' : model.base_height.toFixed(3),
    },
    cover_height           : {
        id            : () => 'cover-height-search',
        header        : ['Высота', 'чехла'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍h(ч)',
        data          : (model: IModel) => model.cover_height === 0 ? '' : model.cover_height.toFixed(3),
    },
    textile                : {
        id            : () => 'textile-search',
        header        : ['Ткань', ''],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Ткань...',
        data          : (model: IModel) => model.textile ?? '',
    },
    textile_composition    : {
        id            : () => 'textile-composition-search',
        header        : ['Состав', 'ткани'],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Состав ткани...',
        data          : (model: IModel) => model.textile_composition ?? '',
        class         : 'truncate',
    },
    base_composition       : {
        id            : () => 'base-composition-search',
        header        : ['Состав', 'мягкого элемента'],
        width         : 'w-[300px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Состав мягкого элемента...',
        data          : (model: IModel) => model.base_composition ?? '',
        title         : (model: IModel) => model.base_composition ?? '',
        class         : 'truncate',
    },
    base_block             : {
        id            : () => 'base-block-search',
        header        : ['Базовый', 'блок'],
        width         : 'w-[180px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Базовый блок...',
        data          : (model: IModel) => model.base_block ?? '',
    },
    side_foam              : {
        id            : () => 'side-foam-search',
        header        : ['ППУ', 'бортов'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍ППУ',
        data          : (model: IModel) => model.side_foam ?? '',
    },
    side_height            : {
        id            : () => 'side-height-search',
        header        : ['Высота', 'бортов'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍h(б)',
        data          : (model: IModel) => model.side_height ?? '',
    },
    lamit                  : {
        id            : () => 'lamit-search',
        header        : ['Линия', ''],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍...',
        data          : (model: IModel) => (model.lamit ? '✓' : '✗'),
    },
    sewing_machine         : {
        id            : () => 'sewing-machine-search',
        header        : ['Группы', 'ДСЗ'],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍ДСЗ',
        data          : (model: IModel) => model.sewing_machine ?? '',
    },
    cover_type             : {
        id            : () => 'cover-type-search',
        header        : ['Тип', 'чехла'],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Тип ч...',
        data          : (model: IModel) => model.cover_type ?? '',
    },
    kant                   : {
        id            : () => 'kant-search',
        header        : ['Кант', ''],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Кант...',
        data          : (model: IModel) => model.kant ?? '',
    },
    tkch                   : {
        id            : () => 'tkch-search',
        header        : ['ТКЧ', ''],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍ТКЧ',
        data          : (model: IModel) => model.tkch ?? '',
    },
    barcode                : {
        id            : () => 'barcode-search',
        header        : ['Штрих', 'код'],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍ШК... ',
        data          : (model: IModel) => model.barcode ?? '',
    },
    manufacture_type       : {
        id            : () => 'manufacture-type-search',
        header        : ['Тип', 'пр-ва'],
        width         : 'w-[120px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Тип пр-ва...',
        data          : (model: IModel) => model.manufacture_type ? model.manufacture_type.name : '',
        title         : (model: IModel) => model.manufacture_type ? model.manufacture_type.name : '',
        class         : 'truncate',
    },
    manufacture_group      : {
        id            : () => 'manufacture-group-search',
        header        : ['Группа', 'СЗ'],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Группа...',
        data          : (model: IModel) => model.manufacture_group ? model.manufacture_group.name : '',
        title         : (model: IModel) => model.manufacture_group ? model.manufacture_group.name : '',
    },
    manufacture_status     : {
        id            : () => 'manufacture-status-search',
        header        : ['Статус', 'пр-ва'],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Статус...',
        data          : (model: IModel) => model.manufacture_status ? model.manufacture_status.name : '',
        title         : (model: IModel) => model.manufacture_status ? model.manufacture_status.name : '',
        class         : 'truncate',
    },
    sewing_operation_schema: {
        id            : () => 'sewing-operation-schema-search',
        header        : ['Схема', 'пошива'],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModel) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Схема...',
        data          : (model: IModel) => model.sewing_operation_schema ? model.sewing_operation_schema.name : '',
        class         : 'truncate',
    },

    // stitch_pattern: string | null
    // description: string | null
})

// __ Фильтры
const nameFilter                  = ref('')
const code_1cFilter               = ref('')
const serialFilter                = ref('')
const nameReportFilter            = ref('')
const baseHeightFilter            = ref('')
const coverHeightFilter           = ref('')
const textileFilter               = ref('')
const textileCompositionFilter    = ref('')
const baseCompositionFilter       = ref('')
const baseBlockFilter             = ref('')
const sideFoamFilter              = ref('')
const sideHeightFilter            = ref('')
const sewingMachineFilter         = ref('')
const coverTypeFilter             = ref('')
const kantFilter                  = ref('')
const tkchFilter                  = ref('')
const barcodeFilter               = ref('')
const manufactureTypeFilter       = ref('')
const manufactureGroupFilter      = ref('')
const manufactureStatusFilter     = ref('')
const sewingOperationSchemaFilter = ref('')

// __ Collapse/Expand all
const toggleCollapsed = () => {
    collapseAll.value = !collapseAll.value
    entitiesRender.value.forEach(entity => {
        entity.collapsed = collapseAll.value

    })
}


// __ Получение данных
const getEntities = async () => {
    entities = await modelsStore.getModels()
    entities.forEach(entity => {
        entity.models = entity.models
            .map(model => ({
                ...model,
                serial                 : model.serial ?? '',
                name_report            : model.name_report ?? '',
                textile                : model.textile ?? '',
                textile_composition    : model.textile_composition ?? '',
                stitch_pattern         : model.stitch_pattern ?? '',
                base_composition       : model.base_composition ?? '',
                side_foam              : model.side_foam ?? '',
                side_height            : model.side_height ?? '',
                base_block             : model.base_block ?? '',
                sewing_machine         : model.sewing_machine ?? '',
                kant                   : model.kant ?? '',
                tkch                   : model.tkch ?? '',
                barcode                : model.barcode ?? '',
                description            : model.description ?? '',
                manufacture_type       : model.manufacture_type ?? { name: '', code_1c: '' },
                manufacture_group      : model.manufacture_group ?? { name: '', id: 0, group_number: 0 },
                manufacture_status     : model.manufacture_status ?? { name: '', id: 0 },
                sewing_operation_schema: model.sewing_operation_schema ?? { name: '', id: 0 },
            }))
            .sort((model_1, model_2) => model_1.name.localeCompare(model_2.name))
    })

}

// __ Формирование данных для рендера
const getEntitiesRender = () => {
    entitiesRender.value = entities

    entitiesRender.value = entities.map(entity => {
        entity.models = entity.models.map(model => ({ ...model, cover_collapsed: true }))
        return { ...entity, collapsed: true, shown: true }
    })
}


watchEffect(() => {
    const nameSearch                  = nameFilter.value.toLowerCase()
    const code_1cSearch               = code_1cFilter.value.toLowerCase()
    const serialSearch                = serialFilter.value.toLowerCase()
    const nameReportSearch            = nameReportFilter.value.toLowerCase()
    const baseHeightSearch            = baseHeightFilter.value.toLowerCase()
    const coverHeightSearch           = coverHeightFilter.value.toLowerCase()
    const textileSearch               = textileFilter.value.toLowerCase()
    const textileCompositionSearch    = textileCompositionFilter.value.toLowerCase()
    const baseCompositionSearch       = baseCompositionFilter.value.toLowerCase()
    const baseBlockSearch             = baseBlockFilter.value.toLowerCase()
    const sideFoamSearch              = sideFoamFilter.value.toLowerCase()
    const sideHeightSearch            = sideHeightFilter.value.toLowerCase()
    const sewingMachineSearch         = sewingMachineFilter.value.toLowerCase()
    const kantSearch                  = kantFilter.value.toLowerCase()
    const tkchSearch                  = tkchFilter.value.toLowerCase()
    const barcodeSearch               = barcodeFilter.value.toLowerCase()
    const manufactureTypeSearch       = manufactureTypeFilter.value.toLowerCase()
    const manufactureGroupSearch      = manufactureGroupFilter.value.toLowerCase()
    const manufactureStatusSearch     = manufactureStatusFilter.value.toLowerCase()
    const sewingOperationSchemaSearch = sewingOperationSchemaFilter.value.toLowerCase()


    entitiesRender.value = entities.map(collection => {
        // Создаем новый объект группы, чтобы не менять оригинал
        const models = collection.models
            .filter(model => model.name.toLowerCase().includes(nameSearch))
            .filter(model => model.code_1c.toLowerCase().includes(code_1cSearch))
            .filter(model => model.serial!.toLowerCase().includes(serialSearch))
            .filter(model => model.name_report!.toLowerCase().includes(nameReportSearch))
            .filter(model => model.base_height.toFixed(3).toLowerCase().includes(baseHeightSearch))
            .filter(model => model.cover_height.toFixed(3).toLowerCase().includes(coverHeightSearch))
            .filter(model => model.textile!.toLowerCase().includes(textileSearch))
            .filter(model => model.textile_composition!.toLowerCase().includes(textileCompositionSearch))
            .filter(model => model.base_composition!.toLowerCase().includes(baseCompositionSearch))
            .filter(model => model.base_block!.toLowerCase().includes(baseBlockSearch))
            .filter(model => model.side_foam!.toLowerCase().includes(sideFoamSearch))
            .filter(model => model.side_height!.toLowerCase().includes(sideHeightSearch))
            .filter(model => model.sewing_machine!.toLowerCase().includes(sewingMachineSearch))
            .filter(model => model.kant!.toLowerCase().includes(kantSearch))
            .filter(model => model.tkch!.toLowerCase().includes(tkchSearch))
            .filter(model => model.barcode!.toLowerCase().includes(barcodeSearch))
            .filter(model => model.manufacture_type!.name.toLowerCase().includes(manufactureTypeSearch))
            .filter(model => model.manufacture_group!.name.toLowerCase().includes(manufactureGroupSearch))
            .filter(model => model.manufacture_status!.name.toLowerCase().includes(manufactureStatusSearch))
            .filter(model => model.sewing_operation_schema!.name.toLowerCase().includes(sewingOperationSchemaSearch))

        return {
            ...collection,
            models,
            // collapsed: models.length === 0,
            shown: models.length !== 0  // __ Убираем из показа группы, в которых нет моделей
        }

    })
})


onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getEntities()
            console.log('models: ', entities)

            getEntitiesRender()
            // console.log('materialsRender: ', entitiesRender.value)
        },
        undefined
        // false,
    )

    isLoading.value = false
})
</script>

<style scoped></style>
