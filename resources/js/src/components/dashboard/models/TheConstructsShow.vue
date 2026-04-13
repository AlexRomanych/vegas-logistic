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
                            :render-object="render.collapsed_model"
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
                        <AppLabelMultilineTSWrapper :render-object="render.name"/>
                        <AppInputTextTSWrapper
                            v-model="nameFilter"
                            :render-object="render.name"
                        />
                    </div>

                    <!-- __ Единица измерения -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.unit"/>
                        <AppInputTextTSWrapper
                            v-model="unitFilter"
                            :render-object="render.unit"
                        />
                    </div>

                    <!-- __ Деталь -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.detail"/>
                        <AppInputTextTSWrapper
                            v-model="detailFilter"
                            :render-object="render.detail"
                        />
                    </div>

                    <!-- __ Высота детали -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.detail_height"/>
                        <AppInputTextTSWrapper
                            v-model="detailHeightFilter"
                            :render-object="render.detail_height"
                        />
                    </div>

                    <!-- __ Процедура расчета -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.procedure"/>
                        <AppInputTextTSWrapper
                            v-model="procedureFilter"
                            :render-object="render.procedure"
                        />
                    </div>

                    <!-- __ Количество -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.amount"/>
                        <AppInputTextTSWrapper
                            v-model="amountFilter"
                            :render-object="render.amount"
                        />
                    </div>

                    <!-- __ Номер п/п -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.position"/>
                        <!--<AppInputTextTSWrapper-->
                        <!--    v-model="positionFilter"-->
                        <!--    :render-object="render.position"-->
                        <!--/>-->
                    </div>

                </div>
            </div>
        </div>
        <div class="ml-2">

            <!-- __ Данные (Модели) -->
            <div
                v-for="model of entitiesRender"
                :key="model.code_1c"
                class="max-w-fit"
            >
                <template v-if="model.shown">

                    <div class="flex">

                        <!-- __ Collapsed -->
                        <template v-if="model.constructs.length > 0">
                            <AppLabelTSWrapper
                                :arg="model"
                                :render-object="render.collapsed_model"
                                @click="render.collapsed_model.click!(model)"
                            />
                        </template>
                        <template v-else>
                            <AppLabelTSWrapper
                                :arg="model"
                                :render-object="render.plug"
                            />
                        </template>

                        <!-- __ Код из 1С -->
                        <AppLabelTSWrapper
                            :arg="model"
                            :render-object="render.code_1c"
                            class="cursor-pointer"
                            @click="render.collapsed.click!(model)"
                        />

                        <!-- __ Название -->
                        <AppLabelTSWrapper
                            :arg="model"
                            :render-object="render.name"
                            class="cursor-pointer"
                            @click="render.collapsed.click!(model)"
                        />

                    </div>

                    <!-- __ Данные (Спецификации) -->
                    <div
                        v-for="construct of model.constructs"
                        :key="construct.code_1c"
                        class="ml-[34px] max-w-fit"
                    >
                        <template v-if="!model.collapsed && model.shown">

                            <div class="flex">

                                <!-- __ Collapsed -->
                                <AppLabelTSWrapper
                                    :arg="construct"
                                    :render-object="render.collapsed_construct"
                                    @click="render.collapsed_model.click!(construct)"
                                />

                                <!-- __ Код из 1С -->
                                <AppLabelTSWrapper
                                    :arg="construct"
                                    :render-object="render.code_1c"
                                    class="cursor-pointer"
                                />

                                <!-- __ Название -->
                                <AppLabelTSWrapper
                                    :arg="construct"
                                    :render-object="render.name_construct"
                                    class="cursor-pointer"
                                />

                            </div>

                            <!-- __ Данные (Элемент Спецификации) -->
                            <div
                                v-for="(item, index) of construct.items"
                                :key="index"
                                class="ml-[50px] max-w-fit"
                            >
                                <template v-if="!construct.collapsed">

                                    <div class="flex">

                                        <!-- __ Код из 1С -->
                                        <AppLabelTSWrapper
                                            :arg="item"
                                            :render-object="render.code_1c"
                                        />

                                        <!-- __ Название -->
                                        <AppLabelTSWrapper
                                            :arg="item"
                                            :render-object="render.name_item"
                                        />

                                        <!-- __ Единица измерения -->
                                        <AppLabelTSWrapper
                                            :arg="item"
                                            :render-object="render.unit"
                                        />

                                        <!-- __ Деталь -->
                                        <AppLabelTSWrapper
                                            :arg="item"
                                            :render-object="render.detail"
                                        />

                                        <!-- __ Высота детали -->
                                        <AppLabelTSWrapper
                                            :arg="item"
                                            :render-object="render.detail_height"
                                        />

                                        <!-- __ Процедура расчета -->
                                        <AppLabelTSWrapper
                                            :arg="item"
                                            :render-object="render.procedure"
                                        />

                                        <!-- __ Количество -->
                                        <AppLabelTSWrapper
                                            :arg="item"
                                            :render-object="render.amount"
                                        />

                                        <!-- __ Номер п/п -->
                                        <AppLabelTSWrapper
                                            :arg="item"
                                            :render-object="render.position"
                                        />

                                    </div>

                                </template>
                            </div>


                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

</template>

<script lang="ts" setup>
import type {
    IModelConstruct,
    IModelConstructItem,
    IModelSpecification,
    IRenderData
} from '@/types'

import { onMounted, reactive, ref, watchEffect } from 'vue'

import { useModelsStore } from '@/stores/ModelsStore'

import { isModelConstructItem } from '@/app/helpers/helpers_model.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppInputTextTSWrapper from '@/components/dashboard/models/components/AppInputTextTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/models/components/AppLabelTSWrapper.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/models/components/AppLabelMultilineTSWrapper.vue'

const isLoading = ref(false)

const modelsStore = useModelsStore()

let entities: IModelSpecification[] = []
const entitiesRender                = ref<IModelSpecification[]>([])

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
// const DATA_ALIGN_DEFAULT = 'center'

// __ Получаем тип раскраски
const getType = (entity: IModelSpecification | IModelConstruct | IModelConstructItem) => {
    if (!entity) return DEFAULT_TYPE
    if (Object.hasOwn(entity, 'constructs')) {
        return 'indigo'
    }
    if (!isModelConstructItem(entity)) return 'stone'
    return entity.material.in_base ? DEFAULT_TYPE : 'danger'
}

const render: IRenderData = reactive({
    collapsed_model    : {
        header        : ['▲', '▼'],
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : (model: IModelSpecification) => model.constructs.length > 1 ? 'danger' : 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : (model: IModelSpecification) => (model.collapsed ? '▲' : '▼'),
        click         : (model: IModelSpecification) => (model.collapsed = !model.collapsed),
        class         : 'cursor-pointer',
    },
    collapsed_construct: {
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
        data          : (construct: IModelConstruct) => (construct.collapsed ? '▲' : '▼'),
        click         : (construct: IModelConstruct) => (construct.collapsed = !construct.collapsed),
        class         : 'cursor-pointer',
    },
    plug               : {
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
    code_1c            : {
        id            : () => 'id-search',
        header        : ['Код', ''],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModelSpecification | IModelConstruct | IModelConstructItem) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Код...',
        data          : (entity: IModelSpecification | IModelConstruct | IModelConstructItem) =>
            isModelConstructItem(entity) ? entity.material.code_1c : entity.code_1c,
    },
    name               : {
        id            : () => 'name-search',
        header        : ['Название', 'модели/спецификации/материала',],
        width         : 'w-[434px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModelSpecification | IModelConstruct | IModelConstructItem) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Название...',
        data          : (entity: IModelSpecification | IModelConstruct | IModelConstructItem) =>
            isModelConstructItem(entity) ? entity.material.name : entity.name,
    },
    name_construct     : {
        id            : () => 'name-construct-search',
        header        : ['Название', 'модели/спецификации/материала',],
        width         : 'w-[400px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModelSpecification | IModelConstruct | IModelConstructItem) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Название...',
        data          : (entity: IModelSpecification | IModelConstruct | IModelConstructItem) =>
            isModelConstructItem(entity) ? entity.material.name : entity.name,
    },
    name_item          : {
        id            : () => 'name-item-search',
        header        : ['Название', 'модели/спецификации/материала',],
        width         : 'w-[384px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModelSpecification | IModelConstruct | IModelConstructItem) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Название...',
        data          : (entity: IModelSpecification | IModelConstruct | IModelConstructItem) =>
            isModelConstructItem(entity) ? entity.material.name : entity.name,
    },
    unit               : {
        id            : () => 'unit-search',
        header        : ['Ед.', 'изм.',],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModelConstructItem) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Ед...',
        data          : (entity: IModelConstructItem) => entity.material.unit ?? '',
    },
    detail             : {
        id            : () => 'detail-search',
        header        : ['Деталь', '',],
        width         : 'w-[120px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModelConstructItem) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Деталь...',
        data          : (entity: IModelConstructItem) => entity.detail ?? '',
    },
    detail_height      : {
        id            : () => 'detail-height-search',
        header        : ['Высота', 'детали',],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModelConstructItem) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Высота...',
        data          : (entity: IModelConstructItem) => entity.detail_height ? entity.detail_height.toFixed(3) : '',
    },
    procedure          : {
        id            : () => 'procedure-search',
        header        : ['Процедура', 'расчета',],
        width         : 'w-[300px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModelConstructItem) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Процедура...',
        data          : (entity: IModelConstructItem) => entity.procedure ? entity.procedure.name ?? '' : '',
    },
    amount             : {
        id            : () => 'amount-search',
        header        : ['Кол-', 'во',],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModelConstructItem) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Кол-во...',
        data          : (entity: IModelConstructItem) => entity.amount.toFixed(3),
    },
    position           : {
        id            : () => 'position-search',
        header        : ['#', '',],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IModelConstructItem) => getType(entity),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍#...',
        data          : (entity: IModelConstructItem) => entity.position.toFixed(0),
    },
})

// __ Фильтры
const nameFilter         = ref('')
const code_1cFilter      = ref('')
const unitFilter         = ref('')
const detailFilter       = ref('')
const detailHeightFilter = ref('')
const procedureFilter    = ref('')
const amountFilter       = ref('')
// const positionFilter     = ref('')

// __ Collapse/Expand all
const toggleCollapsed = () => {
    collapseAll.value = !collapseAll.value
    entitiesRender.value.forEach(entity => {
        entity.collapsed = collapseAll.value
        entity.constructs.forEach(construct => construct.collapsed = collapseAll.value)
    })
    console.log('reached')
}


// __ Получение данных
const getEntities = async () => {
    entities = await modelsStore.getConstructs()
    entities = entities.sort((model_1, model_2) => model_1.name.localeCompare(model_2.name))
    entities.forEach(entity => {
        entity.constructs = entity.constructs
            .map(construct => ({
                ...construct,
                items: construct.items
                    .map(item => ({
                        ...item,
                        detail       : item.detail ?? '',
                        detail_height: item.detail_height ?? 0,
                        material     : { ...item.material, unit: item.material.unit ?? '' },

                        // __ Безопасная проверка procedure
                        procedure: item.procedure
                            ? { ...item.procedure, name: item.procedure.name ?? '' }
                            : { name: '' },
                    }))
                    .sort((item_1, item_2) => item_1.position - item_2.position),
            }))
            .sort((construct_1, construct_2) => construct_1.name.localeCompare(construct_2.name))
    })


}

// __ Формирование данных для рендера
const getEntitiesRender = () => {
    const filter = entities.filter(entity => entity.code_1c.includes('3192'))
    console.log('filter: ', filter)

    entitiesRender.value = entities

    entitiesRender.value = entities.map(entity => {
        entity.constructs = entity.constructs.map(model => ({ ...model, collapsed: collapseAll.value, shown: true }))
        return { ...entity, collapsed: collapseAll.value, shown: true }
    })
}


watchEffect(() => {
    const nameSearch         = nameFilter.value.toLowerCase()
    const code_1cSearch      = code_1cFilter.value.toLowerCase()
    const unitSearch         = unitFilter.value.toLowerCase()
    const detailSearch       = detailFilter.value.toLowerCase()
    const detailHeightSearch = detailHeightFilter.value.toLowerCase()
    const procedureSearch    = procedureFilter.value.toLowerCase()
    const amountSearch       = amountFilter.value.toLowerCase()
    // const positionSearch     = positionFilter.value.toLowerCase()

    entitiesRender.value = entities.map(model => {
        // Создаем новый объект группы, чтобы не менять оригинал

        let isShown = false
        const constructs = model.constructs
            .map(construct => {
                const items = construct.items
                    .filter(item => item.material.code_1c.toLowerCase().includes(code_1cSearch))
                    .filter(item => item.material.name.toLowerCase().includes(nameSearch))
                    .filter(item => item.material.unit!.toLowerCase().includes(unitSearch))
                    .filter(item => item.detail!.toLowerCase().includes(detailSearch))
                    .filter(item => item.detail_height!.toFixed(3).includes(detailHeightSearch))
                    .filter(item => item.procedure!.name!.toLowerCase().includes(procedureSearch))
                    .filter(item => item.amount.toFixed(3).includes(amountSearch))

                isShown = isShown || items.length > 0

                return {
                    ...construct,
                    items,
                    collapsed: items.length === 0,
                    shown: items.length > 0,  // __ Убираем из показа группы, в которых нет элементов
                }
            })

        return {
            ...model,
            constructs,
            collapsed: !isShown,
            shown: isShown  // __ Убираем из показа группы, в которых нет моделей
        }

    })

    // console.log('filtered: ', entitiesRender.value)
})


onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getEntities()
            // console.log('constructs: ', entities)

            getEntitiesRender()
            console.log('constructsRender: ', entitiesRender.value)
        },
        undefined
        // false,
    )

    isLoading.value = false
})
</script>

<style scoped></style>
