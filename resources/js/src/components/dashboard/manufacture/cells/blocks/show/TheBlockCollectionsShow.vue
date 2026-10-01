<template>
    <div v-if="!isLoading" class="ml-2 mt-2">
        <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit">
            <div>
                <div class="flex ml-0.5">

                    <!-- __ collapsed -->
                    <div>
                        <AppLabelMultilineTSWrapper
                            :render-object="render.collapsed"
                            @click="toggleCollapsed"
                        />

                    </div>

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

                    <!-- __ Приоритет изготовления Линия 1 -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.priority"/>
                        <AppInputTextTSWrapper v-model="priorityFilter" :render-object="render.priority"/>
                    </div>

                    <!-- __ Приоритет изготовления Линия 2 -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.priority_2"/>
                        <AppInputTextTSWrapper v-model="priorityFilter_2" :render-object="render.priority_2"/>
                    </div>

                    <!-- __ Ширина блоков -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.width"/>
                        <!--<AppInputTextTSWrapper v-model="widthFilter" :render-object="render.width"/>-->
                    </div>

                    <!-- __ Длина блоков -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.length"/>
                        <!--<AppInputTextTSWrapper v-model="lengthFilter" :render-object="render.length"/>-->
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

                    <!-- __ Shown -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.shown"/>

                        <!-- __ Фильтр: Active -->
                        <AppSelectSimpleTS
                            v-if="render.shown.show"
                            id="shown"
                            :select-data="shownSelect"
                            :text-size="render.shown.headerTextSize"
                            :type="
                                shownFilter === 0
                                ? 'primary'
                                : shownFilter === 1
                                    ? 'success'
                                    : 'danger'
                            "
                            :width="render.shown.width"
                            align="center"
                            class="mt-[8px]"
                            height="h-[30px]"
                            @change="filterByShown"
                        />
                    </div>

                    <!-- __ Substitution -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.substitution"/>

                        <!--    &lt;!&ndash; __ Фильтр: Substitution &ndash;&gt;-->
                        <!--    <AppSelectSimpleTS-->
                        <!--        v-if="render.shown.show"-->
                        <!--        id="shown"-->
                        <!--        :select-data="shownSelect"-->
                        <!--        :text-size="render.shown.headerTextSize"-->
                        <!--        :type="-->
                        <!--            shownFilter === 0-->
                        <!--            ? 'primary'-->
                        <!--            : shownFilter === 1-->
                        <!--                ? 'success'-->
                        <!--                : 'danger'-->
                        <!--        "-->
                        <!--        :width="render.shown.width"-->
                        <!--        align="center"-->
                        <!--        class="mt-[8px]"-->
                        <!--        height="h-[30px]"-->
                        <!--        @change="filterByShown"-->
                        <!--    />-->
                    </div>

                    <!-- __ Название Блока Подмены -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.substitution_name"/>
                        <!--<AppInputTextTSWrapper v-model="lengthFilter" :render-object="render.length"/>-->
                    </div>

                    <!-- __ Описание -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.description"/>
                        <AppInputTextTSWrapper v-model="descriptionFilter" :render-object="render.description"/>
                    </div>

                    <div>
                        <div class="flex">
                            <!-- __ + Коллекция блоков -->
                            <router-link :to="{ name: 'manufacture.cell.blocks.collections.create' }">
                                <AppLabelMultiLineTS
                                    :text="['➕', 'Группа']"
                                    align="center"
                                    class="cursor-pointer"
                                    rounded="4"
                                    text-size="mini"
                                    type="warning"
                                    width="w-[64px]"
                                />
                            </router-link>

                            <!-- __ + Блок -->
                            <router-link :to="{ name: 'manufacture.cell.blocks.create' }">
                                <AppLabelMultiLineTS
                                    :text="['➕', 'Блок']"
                                    align="center"
                                    class="cursor-pointer"
                                    rounded="4"
                                    text-size="mini"
                                    type="warning"
                                    width="w-[64px]"
                                />
                            </router-link>

                            <!-- __ ⌛ Синхронизировать время -->
                            <AppLabelMultiLineTS
                                :text="['⌛🔄', 'Синхрон.']"
                                align="center"
                                class="cursor-pointer"
                                rounded="4"
                                text-size="mini"
                                type="indigo"
                                width="w-[64px]"
                                @click="productivitySync"
                            />
                        </div>
                        <!-- __ Сброс фильтров -->
                        <div class="mt-[6px]">
                            <AppLabelTS
                                id="filters-reset"
                                align="center"
                                class="cursor-pointer"
                                height="h-[29px]"
                                rounded="4"
                                text="Очистить фильтр"
                                text-size="mini"
                                type="orange"
                                width="w-[200px]"
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

                <!-- __ collapsed -->
                <template v-if="blockCollection.blocks.length">
                    <AppLabelTSWrapper
                        :arg="blockCollection"
                        :render-object="render.collapsed"
                        @click="render.collapsed.click!(blockCollection)"
                    />
                </template>
                <template v-else>
                    <AppLabelTSWrapper :arg="blockCollection" :render-object="render.plug"/>
                </template>


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

                <!-- __ Приоритет изготовления Линия 1 -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.priority"/>

                <!-- __ Приоритет изготовления Линия 2 -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.priority_2"/>

                <!-- __ Ширина -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.width"/>

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

                <!-- __ Shown -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.shown"/>

                <!-- __ Подмена -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.substitution"/>

                <!-- __ Название Блока для Подмены -->
                <AppLabelTSWrapper :arg="blockCollection" :render-object="render.substitution_name"/>

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

            <!-- __ Сами Блоки -->
            <div v-if="!collapsedMap.get(blockCollection.code_1c)" class="ml-[34px] mt-0.5 mb-2 bg-green-200">

                <div v-for="block of blockCollection.blocks" :key="block.id">
                    <div class="flex">

                        <!-- __ Код из 1С -->
                        <AppLabelTSWrapper :arg="block" :render-object="render.code_1c_block" c="italic"/>

                        <!-- __ Название -->
                        <AppLabelTSWrapper :arg="block" :render-object="render.name_block" c="italic"/>

                        <!-- __ Единица измерения -->
                        <AppLabelTSWrapper :arg="blockCollection" :render-object="render.unit" c="italic"/>

                        <!-- __ КДБ -->
                        <AppLabelTSWrapper
                            :arg="blockCollection"
                            :class="blockCollection.kdb_id && blockCollection.kdb_id !== 0 ? 'cursor-pointer' : ''"
                            :render-object="render.kdb"
                            c="italic"
                            @dblclick="showDocument(blockCollection)"
                        />

                        <!-- __ Линия -->
                        <AppLabelTSWrapper :arg="blockCollection" :render-object="render.line" c="italic"/>

                        <!-- __ Альтернативная Линия -->
                        <AppLabelTSWrapper :arg="blockCollection" :render-object="render.line_alt" c="italic"/>

                        <!-- __ Приоритет изготовления -->
                        <AppLabelTSWrapper :arg="blockCollection" :render-object="render.priority" c="italic"/>

                        <!-- __ Приоритет изготовления Линии 2 -->
                        <AppLabelTSWrapper :arg="blockCollection" :render-object="render.priority_2" c="italic"/>

                        <!-- __ Ширина -->
                        <AppLabelTSWrapper :arg="[blockCollection, block]" :render-object="render.width_block" c="italic"/>

                        <!-- __ Длина -->
                        <AppLabelTSWrapper :arg="[blockCollection, block]" :render-object="render.length_block" c="italic"/>

                        <!-- __ Высота -->
                        <AppLabelTSWrapper :arg="blockCollection" :render-object="render.height" c="italic"/>

                        <!-- __ Производительность -->
                        <AppLabelTSWrapper :arg="blockCollection" :render-object="render.productivity" c="italic"/>

                        <!-- __ Own - Собственное про-во -->
                        <AppLabelTSWrapper :arg="blockCollection" :render-object="render.own"/>

                        <!-- __ Active -->
                        <AppLabelTSWrapper :arg="block" :render-object="render.active_block"/>

                        <!-- __ Shown -->
                        <AppLabelTSWrapper :arg="block" :render-object="render.shown_block"/>

                        <!-- __ Подмена -->
                        <AppLabelTSWrapper :arg="block" :render-object="render.substitution_block"/>

                        <!-- __ Название Блока для Подмены -->
                        <AppLabelTSWrapper :arg="block" :render-object="render.substitution_block_name" c="italic"/>

                        <!-- __ Описание -->
                        <AppLabelTSWrapper :arg="block" :render-object="render.description_block" c="italic"/>

                        <!-- __ Удалить -->
                        <AppLabelTS
                            v-if="CAN_DELETE"
                            align="center"
                            rounded="4"
                            text="🗑️"
                            text-size="mini"
                            type="danger"
                            width="w-[30px]"
                            @click="deleteBlock(block)"
                        />

                        <!-- __ Редактировать -->
                        <router-link
                            :to="{ name: 'manufacture.cell.blocks.edit', params: { id: block.id } }">
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

        </div>
    </div>

    <!-- __ Просмотр PDF в модальном режиме -->
    <BlockDesignDocumentAsync
        ref="blockDesignDocumentAsync"
        :doc="doc"
        ok-word="Понятно"
        type="primary"
    />

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :align="modalInfoAlign"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        width="w-[600px]"
    />

</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, computed, watch } from 'vue'

import type {
    IRenderData, ISelectData, ISelectDataItem, IBlockCollection, IBlockDocument, IBlock, IColorTypes,
} from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'
import { useUserStore } from '@/stores/UserStore'

import { DEBUG } from '@/app/constants/common.ts'
import { LINE_0, LINE_1, LINE_1_NAME, LINE_2, LINE_2_NAME, UNIT_METERS } from '@/app/constants/blocks.ts'

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
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'


const isLoading = ref(false)

const userStore   = useUserStore()
const blocksStore = useBlocksStore()

// const DEBUG = true

const getRights = computed(() => {
    return userStore.canEditBlocksPermissionsRole()
})

// __ Права изменения
const CAN_EDIT   = getRights.value
const CAN_DELETE = true

// __ Поле LocalStorage для сохранения состояния Collapsed
const BLOCK_REFERENCE_COLLAPSED_STATE_FIELD = 'block_reference_collapsed_state'

// __ Определяем переменные
const blockCollections = ref<IBlockCollection[]>([])
// const blockCollectionsRender = ref<IBlockCollection[]>([])

// __ Collapsed
const collapsedMap = ref<Map<string, boolean>>(new Map())
const collapsed    = ref(true)

// __ Объект отображения данных
const DEFAULT_WIDTH_BOOL   = 'w-[70px]'
const BLOCK_NAME_WIDTH     = 'w-[300px]'
const DEFAULT_HEIGHT       = 'h-[30px]'
const HEADER_TYPE          = 'primary'
const DATA_TYPE            = 'primary'
const DEFAULT_TYPE         = 'dark'
const DEFAULT_TYPE_BLOCK   = 'stone'
const HEADER_TEXT_SIZE     = 'mini'
const DATA_TEXT_SIZE       = 'mini'
const DATA_TEXT_SIZE_BLOCK = 'micro'
const HEADER_ALIGN         = 'center'
const DATA_ALIGN           = 'left'
// const DEFAULT_WIDTH = 'w-[100px]'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const DEFAULT_WIDTH_DATE = 'w-[100px]'
// const DATA_ALIGN_DEFAULT = 'center'

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
        data          : (blockCollection: IBlockCollection) => collapsedMap.value.get(blockCollection.code_1c) ? '▲' : '▼',
        click         : (blockCollection: IBlockCollection) => collapsedMap.value.set(blockCollection.code_1c, !collapsedMap.value.get(blockCollection.code_1c)),
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
    id                     : {
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
    code_1c                : {
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
    code_1c_block          : {
        id            : () => 'code-1c-block-search',
        header        : ['Код', 'из 1С'],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE_BLOCK,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE_BLOCK,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Код...',
        data          : (block: IBlock) => block.code_1c
    },
    name                   : {
        id            : () => 'name-search',
        header        : ['Название', 'группы блоков'],
        width         : BLOCK_NAME_WIDTH,
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
    name_block             : {
        id            : () => 'name-block-search',
        header        : ['Название', 'блоков'],
        width         : 'w-[300px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE_BLOCK,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE_BLOCK,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Название...',
        data          : (block: IBlock) => block.name
    },
    unit                   : {
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
    kdb                    : {
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
    line                   : {
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
        data          : (blockCollection: IBlockCollection) => {
            if (blockCollection.line === LINE_1) return LINE_1_NAME
            if (blockCollection.line === LINE_2) return LINE_2_NAME
            return ''
        },
    },
    line_alt               : {
        id            : () => 'line-alt-search',
        header        : ['Альт.', 'линия'],
        width         : 'w-[70px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => {
            if (!blockCollection || !blockCollection.own || !blockCollection.line_alt) return DEFAULT_TYPE
            if (blockCollection.line_alt === LINE_1) return 'indigo'
            if (blockCollection.line_alt === LINE_2) return 'orange'
            return DEFAULT_TYPE
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍АЛ...',
        data          : (blockCollection: IBlockCollection) => {
            if (blockCollection.line_alt === LINE_1) return LINE_1_NAME
            if (blockCollection.line_alt === LINE_2) return LINE_2_NAME
            return ''
        },
    },
    priority               : {
        id            : () => 'priority-search',
        header        : ['Приори-', 'тет Л.1'],
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
    priority_2             : {
        id            : () => 'priority-2-search',
        header        : ['Приори-', 'тет Л.2'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => {
            if (!blockCollection || !blockCollection.own || blockCollection.priority_2 !== 0) return DEFAULT_TYPE
            return 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Пр-т...',
        data          : (blockCollection: IBlockCollection) => blockCollection.priority_2.toString()
    },
    width                  : {
        id            : () => 'width-search',
        header        : ['W', 'см'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍H...',
        data          : (/*blockCollection: IBlockCollection*/) => ''
        // data          : (blockCollection: IBlockCollection) => blockCollection.height.toString()
    },
    width_block            : {
        id            : () => 'width-block-search',
        header        : ['W', 'см'],
        width         : 'w-[60px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : ([blockCollection, block]) => {
            if (!blockCollection || !block) return DEFAULT_TYPE_BLOCK
            if (blockCollection.own && block.width === 0) return 'danger'
            return DEFAULT_TYPE_BLOCK
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍H...',
        data          : ([, block]) => block.width.toString()
    },
    length                 : {
        id        : () => 'length-search',
        header    : ['L', 'см'],
        width     : 'w-[60px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : () => DEFAULT_TYPE,

        // type          : (blockCollection: IBlockCollection) => {
        //     if (!blockCollection || !blockCollection.own || blockCollection.length !== 0) return DEFAULT_TYPE
        //     return 'danger'
        // },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍L...',
        data          : (/*blockCollection: IBlockCollection*/) => '',
        // data          : (blockCollection: IBlockCollection) => blockCollection.length.toString()
    },
    length_block           : {
        id        : () => 'length-block-search',
        header    : ['L', 'см'],
        width     : 'w-[60px]',
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : ([blockCollection, block]) => {
            if (!blockCollection || !block) return DEFAULT_TYPE_BLOCK
            if (blockCollection.own && block.length === 0) return 'danger'
            return DEFAULT_TYPE_BLOCK
        },

        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍L...',
        data          : ([, block]) => block.length.toString()
        // data          : (blockCollection: IBlockCollection) => blockCollection.length.toString()
    },
    height                 : {
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
    productivity           : {
        id            : () => 'productivity-search',
        header        : ['Произ-сть', 'm2/ч'],
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
    active                 : {
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
    active_block           : {
        id            : () => 'active-search',
        header        : ['Актуаль-', 'ность'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (block: IBlock) => block?.active ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Active...',
        data          : (block: IBlock) => block.active ? '✓' : '✗'
    },
    own                    : {
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
    shown                  : {
        id            : () => 'shown-search',
        header        : ['Видим.', 'в спр-ке'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => blockCollection.shown ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍...',
        data          : (blockCollection: IBlockCollection) => blockCollection.shown ? '✓' : '✗'
    },
    shown_block            : {
        id            : () => 'shown-block-search',
        header        : ['Вид.', 'в спр-ке'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (block: IBlock) => block.shown ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍...',
        data          : (block: IBlock) => block.shown ? '✓' : '✗'
    },
    substitution           : {
        id            : () => 'substitution-search',
        header        : ['Авто-', 'подмена'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockCollection: IBlockCollection) => blockCollection.substitution ? 'danger' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍...',
        data          : (blockCollection: IBlockCollection) => blockCollection.substitution ? '✓' : '✗',
    },
    substitution_block     : {
        id            : () => 'substitution-search',
        header        : ['Авто-', 'подмена'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (block: IBlock) => block.substitution?.substitution ? 'danger' : DEFAULT_TYPE_BLOCK,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍...',
        data          : (block: IBlock) => block.substitution?.substitution ? '✓' : '✗'
    },
    substitution_name      : {
        id            : () => 'substitution-block-search',
        header        : ['Блок для подмены', 'в сменном задании'],
        width         : BLOCK_NAME_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (/*blockCollection: IBlockCollection*/) => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍...',
        data          : (/*blockCollection: IBlockCollection*/) => ''
    },
    substitution_block_name: {
        id            : () => 'substitution-block-search',
        header        : ['Блок для подмены', 'в сменном задании'],
        width         : BLOCK_NAME_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (block: IBlock) => block.substitution?.name ? 'warning' : DEFAULT_TYPE_BLOCK,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍...',
        data          : (block: IBlock) => block.substitution?.name ?? ''
    },
    description            : {
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
    description_block      : {
        id            : () => 'description-block-search',
        header        : ['Описание', ''],
        width         : 'w-[450px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE_BLOCK,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Описание...',
        data          : (block: IBlock) => block.description ?? ''
    },
})

// __ Фильтры
const idFilter           = ref('')
const nameFilter         = ref('')
const code_1cFilter      = ref('')
const unitFilter         = ref('')
const kdbFilter          = ref('')
const priorityFilter     = ref('')
const priorityFilter_2   = ref('')
const widthFilter        = ref('')
const lengthFilter       = ref('')
const heightFilter       = ref('')
const productivityFilter = ref('')
const descriptionFilter  = ref('')
const lineFilter         = ref(0)
const lineAltFilter      = ref(0)
const activeFilter       = ref(0)
const ownFilter          = ref(0)
const shownFilter        = ref(1)


// __ Подготавливаем селекты
const activeSelect: ISelectData = {
    name: 'active',
    data: [
        { id: 0, name: 'Все', selected: activeFilter.value === 0, disabled: false },
        { id: 1, name: '✓', selected: activeFilter.value === 1, disabled: false },
        { id: 2, name: '✗', selected: activeFilter.value === 2, disabled: false },
    ],
}

const ownSelect: ISelectData = {
    name: 'own',
    data: [
        { id: 0, name: 'Все', selected: ownFilter.value === 0, disabled: false },
        { id: 1, name: '✓', selected: ownFilter.value === 1, disabled: false },
        { id: 2, name: '✗', selected: ownFilter.value === 2, disabled: false },
    ],
}

const lineSelect: ISelectData = {
    name: 'line',
    data: [
        { id: 0, name: 'Все', selected: lineFilter.value === 0, disabled: false },
        { id: 1, name: 'Линия 1', selected: lineFilter.value === 1, disabled: false },
        { id: 2, name: 'Линия 2', selected: lineFilter.value === 2, disabled: false },
    ],
}

const lineAltSelect: ISelectData = {
    name: 'line_alt',
    data: [
        { id: 0, name: 'Все', selected: lineAltFilter.value === 0, disabled: false },
        { id: 1, name: 'Линия 1', selected: lineAltFilter.value === 1, disabled: false },
        { id: 2, name: 'Линия 2', selected: lineAltFilter.value === 2, disabled: false },
    ],
}

const shownSelect: ISelectData = {
    name: 'shown',
    data: [
        { id: 0, name: 'Все', selected: shownFilter.value === 0, disabled: false },
        { id: 1, name: '✓', selected: shownFilter.value === 1, disabled: false },
        { id: 2, name: '✗', selected: shownFilter.value === 2, disabled: false },
    ],
}


// __ Обрабатываем селекты
const filterByActive  = (value: ISelectDataItem) => {
    activeFilter.value = value.id
}
const filterByOwn     = (value: ISelectDataItem) => {
    ownFilter.value = value.id
}
const filterByLine    = (value: ISelectDataItem) => {
    lineFilter.value = value.id
}
const filterByLineAlt = (value: ISelectDataItem) => {
    lineAltFilter.value = value.id
}

const filterByShown = (value: ISelectDataItem) => {
    shownFilter.value = value.id
}


// __ Обнуляем фильтры
const resetFilters = () => {
    idFilter.value          = ''
    nameFilter.value        = ''
    code_1cFilter.value     = ''
    unitFilter.value        = ''
    kdbFilter.value         = ''
    priorityFilter.value    = ''
    priorityFilter_2.value  = ''
    heightFilter.value      = ''
    widthFilter.value       = ''
    lengthFilter.value      = ''
    descriptionFilter.value = ''
    lineFilter.value        = 0
    lineAltFilter.value     = 0
    activeFilter.value      = 0
    ownFilter.value         = 0
    shownFilter.value       = 1
}

// __ Обновляем Карту Collapsed в Local Storage
const updateCollapseMap = () => {
    localStorage.setItem(BLOCK_REFERENCE_COLLAPSED_STATE_FIELD, JSON.stringify(Object.fromEntries(collapsedMap.value)))
}

// __ Устанавливаем Карту Collapsed
const setCollapsedMap = () => {
    const savedMapString = localStorage.getItem(BLOCK_REFERENCE_COLLAPSED_STATE_FIELD)
    const savedMap       = savedMapString
        ? new Map<string, boolean>(Object.entries(JSON.parse(savedMapString)))
        : new Map<string, boolean>()

    blockCollections.value.forEach(collection => {
        collapsedMap.value.set(collection.code_1c, Boolean(savedMap.get(collection.code_1c)))
    })

    // updateCollapseMap()
}

// __ Сворачиваем/Разворачиваем
const toggleCollapsed = () => {
    collapsed.value = !collapsed.value
    blockCollections.value.forEach(collection => collapsedMap.value.set(collection.code_1c, collapsed.value))

    // updateCollapseMap()
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
        file_path  : null,
        description: null,
    }
    await blockDesignDocumentAsync.value!.show()
    doc.value = null
    return
}


// __ Получаем данные
const getBlockCollections = async () => {
    const rawCollections: IBlockCollection[] = await blocksStore.getBlockCollections()

    blockCollections.value = rawCollections
        .map(blockCollection => ({
            ...blockCollection,
            kdb         : blockCollection.kdb ?? '',
            unit        : blockCollection.unit ?? '',
            description : blockCollection.description ?? '',
            line_alt    : blockCollection.line_alt ?? LINE_0,
            can_edit    : true,
            collapsed   : true,
            substitution: blockCollection.blocks.some(block => block.substitution)
        }))
        .sort((a, b) => {
            // __ 1. По собственному производству (own: true вверху)
            if (b.own !== a.own) {
                return Number(b.own) - Number(a.own)
            }

            // __ 2. По актуальности (active: true вверху)
            if (b.active !== a.active) {
                return Number(b.active) - Number(a.active)
            }

            // __ 3. По линии производства (по алфавиту / коду)
            const lineCmp = a.line.localeCompare(b.line)
            if (lineCmp !== 0) {
                return lineCmp
            }

            // __ 4. По приоритету на линии (по возрастанию)
            if (a.priority !== b.priority) {
                return a.priority - b.priority
            }

            // __ 5. По алфавиту (название)
            return a.name.localeCompare(b.name)
        })

    return blockCollections
}


// __ Реализация фильтров
const blockCollectionsRender = computed<IBlockCollection[]>(() => {
    const idFilterSearch          = idFilter.value.toLowerCase()
    const nameFilterSearch        = nameFilter.value.toLowerCase()
    const code_1cFilterSearch     = code_1cFilter.value.toLowerCase()
    const unitFilterSearch        = unitFilter.value.toLowerCase()
    const kdbFilterSearch         = kdbFilter.value.toLowerCase()
    const priorityFilterSearch    = priorityFilter.value.toLowerCase()
    const priorityFilterSearch_2  = priorityFilter_2.value.toLowerCase()
    const heightFilterSearch      = heightFilter.value.toLowerCase()
    const lengthFilterSearch      = lengthFilter.value.toLowerCase()
    const descriptionFilterSearch = descriptionFilter.value.toLowerCase()

    return blockCollections.value
        // __ Создаем копии объектов и фильтруем вложенные блоки без мутации исходника
        .map(blockCollection => {
            if (shownFilter.value === 0) return blockCollection

            const shown = shownFilter.value === 1
            return {
                ...blockCollection,
                blocks: blockCollection.blocks.filter(block => block.shown === shown)
            }
        })
        // __ Фильтруем сами коллекции
        .filter(blockCollection => {
            if (shownFilter.value !== 0) {
                const shown = shownFilter.value === 1
                if (blockCollection.shown !== shown /*|| blockCollection.blocks.length === 0*/) {
                    return false
                }
            }

            if (idFilterSearch && !blockCollection.id.toString().toLowerCase().includes(idFilterSearch)) return false
            if (nameFilterSearch && !blockCollection.name.toLowerCase().includes(nameFilterSearch)) return false
            if (code_1cFilterSearch && !blockCollection.code_1c.toLowerCase().includes(code_1cFilterSearch)) return false
            if (unitFilterSearch && !blockCollection.unit!.toLowerCase().includes(unitFilterSearch)) return false
            if (kdbFilterSearch && !blockCollection.kdb!.toLowerCase().includes(kdbFilterSearch)) return false
            if (priorityFilterSearch && !blockCollection.priority.toString().toLowerCase().includes(priorityFilterSearch)) return false
            if (priorityFilterSearch_2 && !blockCollection.priority_2.toString().toLowerCase().includes(priorityFilterSearch_2)) return false
            if (heightFilterSearch && !blockCollection.height.toString().toLowerCase().includes(heightFilterSearch)) return false
            if (lengthFilterSearch && !blockCollection.length.toString().toLowerCase().includes(lengthFilterSearch)) return false
            if (descriptionFilterSearch && !blockCollection.description!.toString().toLowerCase().includes(descriptionFilterSearch)) return false

            if (activeFilter.value === 1 && !blockCollection.active) return false
            if (activeFilter.value === 2 && blockCollection.active) return false

            if (ownFilter.value === 1 && !blockCollection.own) return false
            if (ownFilter.value === 2 && blockCollection.own) return false

            if (lineFilter.value === 1 && blockCollection.line !== LINE_1) return false
            if (lineFilter.value === 2 && blockCollection.line !== LINE_2) return false

            if (lineAltFilter.value === 1 && blockCollection.line_alt !== LINE_1) return false
            if (lineAltFilter.value === 2 && blockCollection.line_alt !== LINE_2) return false

            return true
        })
        .sort((a, b) => {
            // __ 1) По собственному производству (own: true вверху)
            if (b.own !== a.own) return Number(b.own) - Number(a.own)

            // __ 2) По актуальности (active: true вверху)
            if (b.active !== a.active) return Number(b.active) - Number(a.active)

            // __ 3) По линии производства
            const lineCmp = a.line.localeCompare(b.line)
            if (lineCmp !== 0) return lineCmp

            // __ 4) По приоритету
            if (a.priority !== b.priority) return a.priority - b.priority

            // __ 5) По алфавиту
            return a.name.localeCompare(b.name)
        })
    //
    //
    // return blockCollections.value
    //     .filter(blockCollection => {
    //         if (shownFilter.value === 0) return true
    //         const shown = shownFilter.value === 1 // __ true
    //
    //         // console.log(blockCollection.name, blockCollection.shown)
    //
    //         return blockCollection.shown === shown && blockCollection.blocks.length > 0
    //     })
    //     .filter(blockCollection => blockCollection.id.toString().toLowerCase().includes(idFilterSearch))
    //     .filter(blockCollection => blockCollection.name.toLowerCase().includes(nameFilterSearch))
    //     .filter(blockCollection => blockCollection.code_1c.toLowerCase().includes(code_1cFilterSearch))
    //     .filter(blockCollection => blockCollection.unit!.toLowerCase().includes(unitFilterSearch))
    //     .filter(blockCollection => blockCollection.kdb!.toLowerCase().includes(kdbFilterSearch))
    //     // .filter(blockCollection => blockCollection.line.toString().toLowerCase().includes(lineFilterSearch))
    //     // .filter(blockCollection => blockCollection.line_alt!.toString().toLowerCase().includes(lineAltFilterSearch))
    //     .filter(blockCollection => blockCollection.priority.toString().toLowerCase().includes(priorityFilterSearch))
    //     .filter(blockCollection => blockCollection.priority_2.toString().toLowerCase().includes(priorityFilterSearch_2))
    //     .filter(blockCollection => blockCollection.height.toString().toLowerCase().includes(heightFilterSearch))
    //     .filter(blockCollection => blockCollection.length.toString().toLowerCase().includes(lengthFilterSearch))
    //     .filter(blockCollection => blockCollection.description!.toString().toLowerCase().includes(descriptionFilterSearch))
    //     .filter(collection => {
    //         if (activeFilter.value === 0) return true
    //         else if (activeFilter.value === 1) return collection.active
    //         else if (activeFilter.value === 2) return !collection.active
    //     })
    //     .filter(collection => {
    //         if (ownFilter.value === 0) return true
    //         else if (ownFilter.value === 1) return collection.own
    //         else if (ownFilter.value === 2) return !collection.own
    //     })
    //     .filter(collection => {
    //         if (lineFilter.value === 0) return true
    //         else if (lineFilter.value === 1) return collection.line === LINE_1
    //         else if (lineFilter.value === 2) return collection.line === LINE_2
    //     })
    //     .filter(collection => {
    //         if (lineAltFilter.value === 0) return true
    //         else if (lineAltFilter.value === 1) return collection.line_alt === LINE_1
    //         else if (lineAltFilter.value === 2) return collection.line_alt === LINE_2
    //     })
    //     .sort((a, b) => {
    //         // 1) По собственному производству (own: true вверху)
    //         if (b.own !== a.own) return Number(b.own) - Number(a.own)
    //
    //         // 2) По актуальности (active: true вверху)
    //         if (b.active !== a.active) return Number(b.active) - Number(a.active)
    //
    //         // 3) По линии производства
    //         const lineCmp = a.line.localeCompare(b.line)
    //         if (lineCmp !== 0) return lineCmp
    //
    //         // 4) По приоритету
    //         if (a.priority !== b.priority) return a.priority - b.priority
    //
    //         // 5) По алфавиту
    //         return a.name.localeCompare(b.name)
    //     })
    //     // .sort((a, b) => a.name.localeCompare(b.name)) // по алфавиту
    //     // .sort((a, b) => a.priority - b.priority) // по приоритету на линии
    //     // .sort((a, b) => a.line.localeCompare(b.line)) // по линии пр-ва
    //     // .sort((a, b) => Number(b.active) - Number(a.active)) // по актуальности
    //     // .sort((a, b) => Number(b.own) - Number(a.own)) // по собственному производству

})

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const modalInfoAlign           = ref<'left' | 'right' | 'center'>('center')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                 Ошибки                      !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Показываем сообщение об ошибке
async function showError(error: string | string[] | null = null) {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'

    let renderError = ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    if (typeof error === 'string' && error.length > 0) {
        renderError = [error]
    } else if (Array.isArray(error) && error.length > 0) {
        renderError = error
    }

    modalInfoText.value  = renderError
    modalInfoAlign.value = 'center'
    await appModalAsyncMultilineTS.value!.show()
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---              Side Effects                   !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Удаляем Коллекцию блоков
const deleteBlockCollection = async (blockCollection: IBlockCollection) => {
    return blockCollection
}

// __ Удаляем блок
const deleteBlock = async (block: IBlock) => {
    return block
}

// __ Синхронизируем трудозатраты на сервере
const productivitySync = async () => {
    const result = await blocksStore.syncBlockTasksProductivity()
    console.log(result)
    if (checkCRUD(result)) {
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'
        modalInfoText.value = [result.payload]
        await appModalAsyncMultilineTS.value!.show()
    } else {
        await showError()
    }
}


// __ Формируем отображение Коллекций блоков
// const getBlockCollectionsRender = () => {
//     blockCollectionsRender.value = blockCollections.value
// }

watch(() => collapsedMap.value, () => {
    updateCollapseMap()
}, { deep: true })

onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getBlockCollections()
            // getBlockCollectionsRender()
            setCollapsedMap()
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
