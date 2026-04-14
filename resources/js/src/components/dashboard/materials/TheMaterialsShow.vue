<template>
    <div
        v-if="!isLoading"
        class="ml-2 mt-2"
    >
        <div class="sticky top-0 p-1 mb-1 bg-slate-200 border-2 rounded-lg border-slate-300 max-w-fit">
            <div>
                <div class="flex ml-0.5">
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
                        <AppLabelMultilineTSWrapper :render-object="render.group_name"/>
                        <AppInputTextTSWrapper
                            v-model="nameFilter"
                            :render-object="render.group_name"
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

                    <!-- __ Альтернативная Единица измерения -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.alt_unit"/>
                        <AppInputTextTSWrapper
                            v-model="altUnitFilter"
                            :render-object="render.alt_unit"
                        />
                    </div>

                    <!-- __ Вид объекта -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.object_name"/>
                        <AppInputTextTSWrapper
                            v-model="objectNameFilter"
                            :render-object="render.object_name"
                        />
                    </div>

                    <!-- __ Наличие характеристик -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.has_properties"/>
                    </div>

                    <!-- __ Описание -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.description"/>
                        <AppInputTextTSWrapper
                            v-model="descriptionFilter"
                            :render-object="render.description"
                        />
                    </div>

                    <!-- __ Допущенные поставщики -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.supplier"/>
                        <AppInputTextTSWrapper
                            v-model="supplierFilter"
                            :render-object="render.supplier"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="ml-2">
            <!-- __ Данные (Группы) -->
            <div
                v-for="group of entitiesRender"
                :key="group.code_1c"
                class="max-w-fit"
            >
                <div class="flex">
                    <!-- __ Collapsed -->
                    <AppLabelTSWrapper
                        :arg="group"
                        :render-object="render.collapsed"
                        @click="render.collapsed.click!(group)"
                    />

                    <!-- __ Код из 1С -->
                    <AppLabelTSWrapper
                        :arg="group"
                        :render-object="render.code_1c"
                        @click="render.collapsed.click!(group)"
                    />

                    <!-- __ Название -->
                    <AppLabelTSWrapper
                        :arg="group"
                        :render-object="render.group_name"
                        @click="render.collapsed.click!(group)"
                    />
                </div>

                <!-- __ Данные (Категории) -->
                <div
                    v-for="category of group.categories"
                    :key="category.code_1c"
                    class="ml-[34px] max-w-fit"
                >
                    <template v-if="!group.collapsed">
                        <div class="flex">
                            <!-- __ Collapsed -->
                            <AppLabelTSWrapper
                                :arg="category"
                                :render-object="render.collapsed"
                                @click="render.collapsed.click!(category)"
                            />

                            <!-- __ Код из 1С -->
                            <AppLabelTSWrapper
                                :arg="category"
                                :render-object="render.code_1c"
                                @click="render.collapsed.click!(category)"
                            />

                            <!-- __ Название -->
                            <AppLabelTSWrapper
                                :arg="category"
                                :render-object="render.category_name"
                                @click="render.collapsed.click!(category)"
                            />
                        </div>

                        <!-- __ Данные (Материалы) -->
                        <div
                            v-for="material of category.materials"
                            :key="material.code_1c"
                            class="ml-[50px] max-w-fit"
                        >
                            <template v-if="!category.collapsed">
                                <div class="flex">
                                    <!-- __ Код из 1С -->
                                    <AppLabelTSWrapper
                                        :arg="material"
                                        :render-object="render.code_1c"
                                    />

                                    <!-- __ Название -->
                                    <AppLabelTSWrapper
                                        :arg="material"
                                        :render-object="render.material_name"
                                    />

                                    <!-- __ Единица измерения -->
                                    <AppLabelTSWrapper
                                        :arg="material"
                                        :render-object="render.unit"
                                    />

                                    <!-- __ Альтернативная Единица измерения -->
                                    <AppLabelTSWrapper
                                        :arg="material"
                                        :render-object="render.alt_unit"
                                    />

                                    <!-- __ Вид объекта -->
                                    <AppLabelTSWrapper
                                        :arg="material"
                                        :render-object="render.object_name"
                                    />

                                    <!-- __ Наличие характеристик -->
                                    <AppLabelTSWrapper
                                        :arg="material"
                                        :class="material.properties ? 'cursor-pointer' : ''"
                                        :render-object="render.has_properties"
                                        @dblclick="showProperties(material)"
                                    />

                                    <!-- __ Описание -->
                                    <AppLabelTSWrapper
                                        :arg="material"
                                        :render-object="render.description"
                                    />

                                    <!-- __ Допущенные поставщики -->
                                    <AppLabelTSWrapper
                                        :arg="material"
                                        :render-object="render.supplier"
                                    />
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- __ Модальное окно для информации о свойствах материала __ -->
    <MaterialPropertiesInfo
        ref="materialPropertiesInfo"
        :material="infoMaterial"
    />
</template>

<script lang="ts" setup>
import type { IMaterial, IMaterialGroup, IRenderData } from '@/types'

import { onMounted, reactive, ref, watchEffect } from 'vue'
import { useMaterialsStore } from '@/stores/MaterialsStore.ts'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppInputTextTSWrapper from '@/components/dashboard/materials/components/AppInputTextTSWrapper.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/materials/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/materials/components/AppLabelTSWrapper.vue'
import MaterialPropertiesInfo from '@/components/dashboard/materials/materials_components/MaterialPropertiesInfo.vue'

const isLoading = ref(false)

const materialsStore = useMaterialsStore()

let entities: IMaterialGroup[] = []
const entitiesRender           = ref<IMaterialGroup[]>([])

// __ Тип для модального окна информации о записи в Заявке
const infoMaterial           = ref<IMaterial | null>(null)
const materialPropertiesInfo = ref<InstanceType<typeof MaterialPropertiesInfo> | null>(null)

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

const render: IRenderData = reactive({
    collapsed     : {
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
        data          : (group: IMaterialGroup) => (group.collapsed ? '▲' : '▼'),
        click         : (group: IMaterialGroup) => (group.collapsed = !group.collapsed),
        class         : 'cursor-pointer',
    },
    code_1c       : {
        id            : () => 'id-search',
        header        : ['Код', ''],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Код...',
        data          : (group: IMaterialGroup) => group.code_1c,
        class         : 'cursor-pointer',
    },
    group_name    : {
        id            : () => 'name-search',
        header        : ['Название', 'Группы/категории/материала'],
        width         : 'w-[434px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Название...',
        data          : (group: IMaterialGroup) => group.name,
        class         : 'cursor-pointer',
        // color: (order: IRenderOrder) => order.order_type.color,
        // title: (order: IRenderOrder) => order.order_type.display_name,
    },
    category_name : {
        id            : () => 'name-search',
        header        : ['Название', 'Категории материалов'],
        width         : 'w-[400px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => 'stone',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Название...',
        data          : (category: IMaterialGroup) => category.name,
        class         : 'cursor-pointer',
    },
    material_name : {
        id            : () => 'name-search',
        header        : ['Название', 'Категории материалов'],
        width         : 'w-[384px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Название...',
        data          : (material: IMaterial) => material.name,
    },
    unit          : {
        id            : () => 'unit-search',
        header        : ['Ед.', 'изм.'],
        width         : 'w-[70px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Ед...',
        data          : (material: IMaterial) => material.unit ?? '',
    },
    alt_unit      : {
        id            : () => 'alt-unit-search',
        header        : ['Альт. ед.', 'изм.'],
        width         : 'w-[70px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Ед...',
        data          : (material: IMaterial) => material.alt_unit ?? '',
    },
    description   : {
        id            : () => 'description-search',
        header        : ['Описание', ''],
        width         : 'w-[200px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Описание...',
        data          : (material: IMaterial) => material.description ?? '',
    },
    supplier      : {
        id            : () => 'supplier-search',
        header        : ['Допущенные', 'поставщики'],
        width         : 'w-[200px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Поставщики...',
        data          : (material: IMaterial) => material.supplier ?? '',
    },
    object_name   : {
        id            : () => 'object-name-search',
        header        : ['Вид', 'объекта'],
        width         : 'w-[150px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Вид объекта...',
        data          : (material: IMaterial) => material.object_name ?? '',
    },
    has_properties: {
        id            : () => 'properties-name-search',
        header        : ['Хар-', 'ки'],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (material: IMaterial) => (material.properties ? 'success' : DEFAULT_TYPE),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍...',
        data          : (material: IMaterial) => (material.properties ? '✓' : '✗'),
    },
})

// __ Фильтры
const nameFilter        = ref('')
const code_1cFilter     = ref('')
const unitFilter        = ref('')
const altUnitFilter     = ref('')
const supplierFilter    = ref('')
const descriptionFilter = ref('')
const objectNameFilter  = ref('')

// __ Collapse/Expand all
const toggleCollapsed = () => {
    collapseAll.value = !collapseAll.value
    entitiesRender.value.forEach(entity => {
        entity.collapsed = collapseAll.value
        entity.categories.forEach(category => (category.collapsed = collapseAll.value))
    })
}

// __ Показать свойства материала
const showProperties = async (material: IMaterial) => {
    if (material.properties) {
        infoMaterial.value = material
        await materialPropertiesInfo.value!.show() // показываем модалку и ждем ответ
    }
}

watchEffect(() => {
    const objectNameSearch  = objectNameFilter.value.toLowerCase()
    const nameSearch        = nameFilter.value.toLowerCase()
    const code_1cSearch     = code_1cFilter.value.toLowerCase()
    const unitSearch        = unitFilter.value.toLowerCase()
    const altUnitSearch     = altUnitFilter.value.toLowerCase()
    // const supplierSearch = supplierFilter.value.toLowerCase()
    const descriptionSearch = descriptionFilter.value.toLowerCase()

    entitiesRender.value = entities.map(group => {
        // Создаем новый объект группы, чтобы не менять оригинал
        return {
            ...group,
            categories: group.categories.map(category => {
                // Создаем новый объект категории с отфильтрованными материалами
                return {
                    ...category,
                    materials: category.materials
                        .filter(material => material.name.toLowerCase().includes(nameSearch))
                        .filter(material => material.code_1c.toLowerCase().includes(code_1cSearch))
                        .filter(material => material.unit!.toLowerCase().includes(unitSearch))
                        .filter(material => material.alt_unit!.toLowerCase().includes(altUnitSearch))
                        // .filter(material => material.supplier!.toLowerCase().includes(supplierSearch))
                        .filter(material => material.object_name!.toLowerCase().includes(objectNameSearch))
                        .filter(material => material.description!.toLowerCase().includes(descriptionSearch)),
                }
            }),
        }
    })
})

// __ Получение данных
const getEntities = async () => {
    entities = await materialsStore.getMaterials()
    entities.forEach(group => {
        group.categories = group.categories.sort((cat_1, cat_2) => cat_1.name.localeCompare(cat_2.name))
        group.categories.forEach(category => {
            category.materials = category.materials
                .map(material => ({
                    ...material,
                    unit       : material.unit ?? '',
                    alt_unit   : material.alt_unit ?? '',
                    description: material.description ?? '',
                    object_name: material.object_name ?? '',
                }))
                .sort((mat_1, mat_2) => mat_1.name.localeCompare(mat_2.name))
        })
    })
}

// __ Формирование данных для рендера
const getEntitiesRender = () => {
    entitiesRender.value = entities.map(group => {
        group.categories = group.categories.map(category => ({ ...category, collapsed: true }))
        return { ...group, collapsed: true }
    })
}

onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getEntities()
            console.log('materials: ', entities)

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
