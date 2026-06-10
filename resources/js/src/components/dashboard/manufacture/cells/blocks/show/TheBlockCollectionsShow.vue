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

                    <!-- __ Код из 1С -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.code_1c"/>
                        <AppInputTextTSWrapper v-model="code_1cFilter" :render-object="render.code_1c"/>
                    </div>

                    <!-- __ Название -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.name"/>
                        <AppInputTextTSWrapper v-model="nameFilter" :render-object="render.name"/>
                    </div>

                    <!-- __ Единица измерения -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.unit"/>
                        <AppInputTextTSWrapper v-model="unitFilter" :render-object="render.unit"/>
                    </div>

                    <!-- __ КДБ -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.kdb"/>
                        <AppInputTextTSWrapper v-model="kdbFilter" :render-object="render.kdb"/>
                    </div>

                    <!-- __ Линия -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.line"/>
                        <!--<AppInputTextTSWrapper v-model="lineFilter" :render-object="render.line"/>-->

                        <!-- __ Фильтр: Линия -->
                        <AppSelectSimpleTS
                            v-if="render.line.show"
                            id="line"
                            :select-data="lineSelect"
                            :text-size="render.line.headerTextSize"
                            :type="
                                lineFilter === 0
                                ? 'primary'
                                : lineFilter === 1
                                    ? 'indigo'
                                    : 'orange'
                            "
                            :width="render.line.width"
                            align="center"
                            class="mt-[8px]"
                            height="h-[30px]"
                            @change="filterByLine"
                        />

                    </div>

                    <!-- __ Альтернативная Линия -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.line_alt"/>
                        <!--<AppInputTextTSWrapper v-model="lineAltFilter" :render-object="render.line_alt"/>-->
                        <!-- __ Фильтр: Альтернативная Линия -->
                        <AppSelectSimpleTS
                            v-if="render.line_alt.show"
                            id="line-alt"
                            :select-data="lineAltSelect"
                            :text-size="render.line_alt.headerTextSize"
                            :type="
                                lineAltFilter === 0
                                ? 'primary'
                                : lineAltFilter === 1
                                    ? 'indigo'
                                    : 'orange'
                            "
                            :width="render.line_alt.width"
                            align="center"
                            class="mt-[8px]"
                            height="h-[30px]"
                            @change="filterByLineAlt"
                        />


                    </div>

                    <!-- __ Приоритет изготовления -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.priority"/>
                        <AppInputTextTSWrapper v-model="priorityFilter" :render-object="render.priority"/>
                    </div>

                    <!-- __ Длина блоков -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.length"/>
                        <AppInputTextTSWrapper v-model="lengthFilter" :render-object="render.length"/>
                    </div>

                    <!-- __ Высота блоков -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.height"/>
                        <AppInputTextTSWrapper v-model="heightFilter" :render-object="render.height"/>
                    </div>

                    <!-- __ Производительность -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.productivity"/>
                        <AppInputTextTSWrapper v-model="productivityFilter" :render-object="render.productivity"/>
                    </div>

                    <!-- __ Own - Собственное производство -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.own"/>

                        <!-- __ Фильтр: Own -->
                        <AppSelectSimpleTS
                            v-if="render.own.show"
                            id="own"
                            :select-data="ownSelect"
                            :text-size="render.own.headerTextSize"
                            :type="
                                ownFilter === 0
                                ? 'primary'
                                : ownFilter === 1
                                    ? 'success'
                                    : 'danger'
                            "
                            :width="render.own.width"
                            align="center"
                            class="mt-[8px]"
                            height="h-[30px]"
                            @change="filterByOwn"
                        />
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

                    <!-- __ Описание -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.description"/>
                        <AppInputTextTSWrapper v-model="descriptionFilter" :render-object="render.description"/>
                    </div>

                    <div>
                        <!-- __ + Коллекция блоков -->
                        <router-link :to="{ name: 'manufacture.cell.blocks.collections.create' }">
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
        <div v-for="blockCollection of blockCollectionsRender" :key="blockCollection.id" class="ml-2 max-w-fit">
            <div class="flex ">

                <!-- __ id -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.id"/>

                <!-- __ Код из 1С -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.code_1c"/>

                <!-- __ Название -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.name"/>

                <!-- __ Единица измерения -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.unit"/>

                <!-- __ КДБ -->
                <AppLabelTSWrapper
                    :arg="blockCollection"
                    :class="blockCollection.kdb_id && blockCollection.kdb_id !== 0 ? 'cursor-pointer' : ''"
                    :render-object="render.kdb"
                    @dblclick="showDocument(blockCollection)"
                />

                <!-- __ Линия -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.line"/>

                <!-- __ Альтернативная Линия -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.line_alt"/>

                <!-- __ Приоритет изготовления -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.priority"/>

                <!-- __ Длина -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.length"/>

                <!-- __ Высота -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.height"/>

                <!-- __ Производительность -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.productivity"/>

                <!-- __ Own - Собственное про-во -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.own"/>

                <!-- __ Active -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.active"/>

                <!-- __ Описание -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.description"/>

                <!-- __ Удалить -->
                <AppLabelTS
                    v-if="CAN_DELETE"
                    align="center"
                    rounded="4"
                    text="🗑️"
                    text-size="mini"
                    type="danger"
                    width="w-[30px]"
                    @click="deleteBlockCollection(blockCollection)"
                />

                <!-- __ Редактировать -->
                <router-link
                    :to="{ name: 'manufacture.cell.blocks.collections.edit', params: { id: blockCollection.id } }">
                    <AppLabelTS
                        v-if="CAN_EDIT && blockCollection.id !== 1"
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

    <!-- __ Просмотр PDF в модальном режиме -->
    <BlockDesignDocumentAsync
        ref="blockDesignDocumentAsync"
        :doc="doc"
        ok-word="Понятно"
        type="primary"
    />

</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, watchEffect } from 'vue'

import type {
    IRenderData, ISelectData, ISelectDataItem, IBlockCollection, IBlockDocument,
} from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

import { DEBUG } from '@/app/constants/common.ts'
import { LINE_0, LINE_1, LINE_2, UNIT_METERS } from '@/app/constants/blocks.ts'

import AppLabelMultilineTSWrapper
    from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import AppInputTextTSWrapper from '@/components/dashboard/manufacture/cells/components/AppInputTextTSWrapper.vue'
import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import BlockDesignDocumentAsync from '@/components/dashboard/manufacture/shared/block_design/BlockDesignDocumentAsync.vue'
// import AppRGBPickerModalTS from '@/components/ui/pickers/AppRGBPickerModalTS.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'


const isLoading = ref(false)

const blocksStore = useBlocksStore()

// const DEBUG = true

// __ Права изменения
const CAN_EDIT   = true
const CAN_DELETE = true

// __ Определяем переменные
const blockCollections       = ref<IBlockCollection[]>([])
const blockCollectionsRender = ref<IBlockCollection[]>([])

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
    id          : {
        id            : () => 'id-search',
        header        : ['ID', ''],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (blockCollection: IBlockCollection) => blockCollection.id.toString()
    },
    name        : {
        id            : () => 'name-search',
        header        : ['Название', 'группы блоков'],
        width         : 'w-[300px]',
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
        data          : (blockCollection: IBlockCollection) => blockCollection.name
    },
    code_1c     : {
        id            : () => 'code-1c-search',
        header        : ['Код', 'из 1С'],
        width         : 'w-[100px]',
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
        data          : (blockCollection: IBlockCollection) => blockCollection.code_1c
    },
    unit        : {
        id            : () => 'unit-search',
        header        : ['Ед.', 'изм.'],
        width         : 'w-[70px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => {
            if (!blockCollection) return DEFAULT_TYPE
            if (!blockCollection.unit) return 'danger'
            if (blockCollection.unit === UNIT_METERS) return 'orange'
            return 'primary'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Ед...',
        data          : (blockCollection: IBlockCollection) => blockCollection.unit ?? ''
    },
    kdb         : {
        id            : () => 'kdb-search',
        header        : ['КДБ', ''],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => blockCollection?.kdb_id && blockCollection?.kdb_id !== 0 ? 'indigo' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍КДБ...',
        data          : (blockCollection: IBlockCollection) => blockCollection.kdb_id && blockCollection.kdb_id !== 0 ? `${blockCollection.kdb} 🔍` : '',
    },
    line        : {
        id            : () => 'line-search',
        header        : ['Линия', ''],
        width         : 'w-[70px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => {
            if (!blockCollection || !blockCollection.own) return DEFAULT_TYPE
            if (!blockCollection.line) return 'danger'
            if (blockCollection.line === LINE_2) return 'orange'
            return 'indigo'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Л...',
        data          : (blockCollection: IBlockCollection) => blockCollection.line.toString(),
    },
    line_alt    : {
        id            : () => 'line-alt-search',
        header        : ['Альт.', 'линия'],
        width         : 'w-[70px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => {
            if (!blockCollection || !blockCollection.own || !blockCollection.line_alt) return DEFAULT_TYPE
            if (blockCollection.line_alt === LINE_2) return 'orange'
            return 'indigo'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍АЛ...',
        data          : (blockCollection: IBlockCollection) => blockCollection.line_alt ? blockCollection.line_alt.toString() : '',
    },
    priority    : {
        id            : () => 'priority-search',
        header        : ['Приор-', 'тет'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => {
            if (!blockCollection || !blockCollection.own || blockCollection.priority !== 0) return DEFAULT_TYPE
            return 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Пр-т...',
        data          : (blockCollection: IBlockCollection) => blockCollection.priority.toString()
    },
    height      : {
        id            : () => 'height-search',
        header        : ['H', 'см'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => {
            if (!blockCollection || !blockCollection.own || blockCollection.height !== 0) return DEFAULT_TYPE
            return 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍H...',
        data          : (blockCollection: IBlockCollection) => blockCollection.height.toString()
    },
    length      : {
        id            : () => 'length-search',
        header        : ['L', 'см'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => {
            if (!blockCollection || !blockCollection.own || blockCollection.length !== 0) return DEFAULT_TYPE
            return 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍L...',
        data          : (blockCollection: IBlockCollection) => blockCollection.length.toString()
    },
    productivity: {
        id            : () => 'productivity-search',
        header        : ['Произ-сть', 'мп/ч'],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => {
            if (!blockCollection || !blockCollection.own || blockCollection.productivity !== 0) return DEFAULT_TYPE
            return 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Произ-сть...',
        data          : (blockCollection: IBlockCollection) => blockCollection.productivity.toFixed(3)
    },
    active      : {
        id            : () => 'active-search',
        header        : ['Актуаль-', 'ность'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => blockCollection.active ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Active...',
        data          : (blockCollection: IBlockCollection) => blockCollection.active ? '✓' : '✗'
    },
    own         : {
        id            : () => 'own-search',
        header        : ['Собств.', 'пр-во'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => blockCollection.own ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍...',
        data          : (blockCollection: IBlockCollection) => blockCollection.own ? '✓' : '✗'
    },
    description : {
        id            : () => 'description-search',
        header        : ['Описание', ''],
        width         : 'w-[450px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Описание...',
        data          : (blockCollection: IBlockCollection) => blockCollection.description ?? ''
    },
})

// __ Фильтры
const idFilter           = ref('')
const nameFilter         = ref('')
const code_1cFilter      = ref('')
const unitFilter         = ref('')
const kdbFilter          = ref('')
const priorityFilter     = ref('')
const lengthFilter       = ref('')
const heightFilter       = ref('')
const productivityFilter = ref('')
const descriptionFilter  = ref('')
const lineFilter         = ref(0)
const lineAltFilter      = ref(0)
const activeFilter       = ref(0)
const ownFilter          = ref(0)


// __ Подготавливаем селекты
const activeSelect: ISelectData = {
    name: 'active',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: '✓', selected: false, disabled: false },
        { id: 2, name: '✗', selected: false, disabled: false },
    ],
}

const ownSelect: ISelectData = {
    name: 'own',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: '✓', selected: false, disabled: false },
        { id: 2, name: '✗', selected: false, disabled: false },
    ],
}

const lineSelect: ISelectData = {
    name: 'line',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: 'Линия 1', selected: false, disabled: false },
        { id: 2, name: 'Линия 2', selected: false, disabled: false },
    ],
}

const lineAltSelect: ISelectData = {
    name: 'line_alt',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: 'Линия 1', selected: false, disabled: false },
        { id: 2, name: 'Линия 2', selected: false, disabled: false },
    ],
}


// __ Обрабатываем селекты
const filterByActive = (value: ISelectDataItem) => {
    activeFilter.value = value.id
}
const filterByOwn    = (value: ISelectDataItem) => {
    ownFilter.value = value.id
}
const filterByLine    = (value: ISelectDataItem) => {
    lineFilter.value = value.id
}
const filterByLineAlt    = (value: ISelectDataItem) => {
    lineAltFilter.value = value.id
}


// __ Обнуляем фильтры
const resetFilters = () => {
    idFilter.value          = ''
    nameFilter.value        = ''
    code_1cFilter.value     = ''
    unitFilter.value        = ''
    kdbFilter.value         = ''
    priorityFilter.value    = ''
    heightFilter.value      = ''
    lengthFilter.value      = ''
    descriptionFilter.value = ''
    lineFilter.value        = 0
    lineAltFilter.value     = 0
    activeFilter.value      = 0
    ownFilter.value         = 0
}


// __ Показываем КДБ
const blockDesignDocumentAsync = ref<InstanceType<typeof BlockDesignDocumentAsync> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией
const doc                      = ref<IBlockDocument | null>()

const showDocument = async (blockCollection: IBlockCollection) => {
    if (!blockCollection.kdb_id) {
        return
    }
    doc.value = {
        id         : blockCollection.kdb_id,
        kdb        : blockCollection.kdb ?? '',
        name       : blockCollection.kdb ?? '',
        file_path  : null,
        description: null,
    }
    await blockDesignDocumentAsync.value!.show()
    doc.value = null
    return
}


// __ Получаем данные
const getBlockCollections = async () => {
    blockCollections.value = await blocksStore.getBlockCollections()
    blockCollections.value = blockCollections.value
        .map(blockCollection => ({
            ...blockCollection,
            kdb        : blockCollection.kdb ?? '',
            unit       : blockCollection.unit ?? '',
            description: blockCollection.description ?? '',
            line_alt   : blockCollection.line_alt ?? LINE_0,
            can_edit   : true
        }))
        .sort((a, b) => a.name.localeCompare(b.name)) // по алфавиту
        .sort((a, b) => a.priority - b.priority) // по приоритету на линии
        .sort((a, b) => a.line - b.line) // по линии пр-ва
        .sort((a, b) => Number(b.own) - Number(a.own)) // по собственному производству


    return blockCollections
}


// __ Формируем отображение Коллекций блоков
const getBlockCollectionsRender = () => {
    blockCollectionsRender.value = blockCollections.value
}


// __ Удаляем Коллекцию блоков
const deleteBlockCollection = async (blockCollection: IBlockCollection) => {
    return blockCollection
}


// __ Реализация фильтров
watchEffect(() => {
    const idFilterSearch          = idFilter.value.toLowerCase()
    const nameFilterSearch        = nameFilter.value.toLowerCase()
    const code_1cFilterSearch     = code_1cFilter.value.toLowerCase()
    const unitFilterSearch        = unitFilter.value.toLowerCase()
    const kdbFilterSearch         = kdbFilter.value.toLowerCase()
    const priorityFilterSearch    = priorityFilter.value.toLowerCase()
    const heightFilterSearch      = heightFilter.value.toLowerCase()
    const lengthFilterSearch      = lengthFilter.value.toLowerCase()
    const descriptionFilterSearch = descriptionFilter.value.toLowerCase()
    // const lineFilterSearch        = lineFilter.value.toLowerCase()
    // const lineAltFilterSearch     = lineAltFilter.value.toLowerCase()

    blockCollectionsRender.value = blockCollections.value
        .filter(blockCollection => blockCollection.id.toString().toLowerCase().includes(idFilterSearch))
        .filter(blockCollection => blockCollection.name.toLowerCase().includes(nameFilterSearch))
        .filter(blockCollection => blockCollection.code_1c.toLowerCase().includes(code_1cFilterSearch))
        .filter(blockCollection => blockCollection.unit!.toLowerCase().includes(unitFilterSearch))
        .filter(blockCollection => blockCollection.kdb!.toLowerCase().includes(kdbFilterSearch))
        // .filter(blockCollection => blockCollection.line.toString().toLowerCase().includes(lineFilterSearch))
        // .filter(blockCollection => blockCollection.line_alt!.toString().toLowerCase().includes(lineAltFilterSearch))
        .filter(blockCollection => blockCollection.priority.toString().toLowerCase().includes(priorityFilterSearch))
        .filter(blockCollection => blockCollection.height.toString().toLowerCase().includes(heightFilterSearch))
        .filter(blockCollection => blockCollection.length.toString().toLowerCase().includes(lengthFilterSearch))
        .filter(blockCollection => blockCollection.description!.toString().toLowerCase().includes(descriptionFilterSearch))
        .filter(collection => {
            if (activeFilter.value === 0) return true
            else if (activeFilter.value === 1) return collection.active
            else if (activeFilter.value === 2) return !collection.active
        })
        .filter(collection => {
            if (ownFilter.value === 0) return true
            else if (ownFilter.value === 1) return collection.own
            else if (ownFilter.value === 2) return !collection.own
        })
        .filter(collection => {
            if (lineFilter.value === 0) return true
            else if (lineFilter.value === 1) return collection.line === LINE_1
            else if (lineFilter.value === 2) return collection.line === LINE_2
        })
        .filter(collection => {
            if (lineAltFilter.value === 0) return true
            else if (lineAltFilter.value === 1) return collection.line_alt === LINE_1
            else if (lineAltFilter.value === 2) return collection.line_alt === LINE_2
        })
        .sort((a, b) => a.name.localeCompare(b.name)) // по алфавиту
        .sort((a, b) => a.priority - b.priority) // по приоритету на линии
        .sort((a, b) => a.line - b.line) // по линии пр-ва
        .sort((a, b) => Number(b.own) - Number(a.own)) // по собственному производству
    return
})


onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getBlockCollections()
            getBlockCollectionsRender()
            if (DEBUG) console.log('blockCollections: ', blockCollections.value)

        },
        undefined,
        // false,
    )

    isLoading.value = false
})

</script>

<style scoped>

</style>
