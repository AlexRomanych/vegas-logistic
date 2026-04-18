<template>
    <div
        v-if="!isLoading"
        class="mx-2 mt-2"
    >
        <div class="sticky top-0 p-1 mb-1 bg-slate-200 border-2 rounded-lg border-slate-300 max-w">
            <div class="mx-0.5">
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
                        <AppLabelMultilineTSWrapper :render-object="render.name"/>
                        <AppInputTextTSWrapper
                            v-model="nameFilter"
                            :render-object="render.name"
                        />
                    </div>

                    <!-- __ Код объекта процедуры -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.object_code"/>
                        <AppInputTextTSWrapper
                            v-model="objectCodeFilter"
                            :render-object="render.object_code"
                        />
                    </div>

                    <!-- __ Тип объекта процедуры -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.object_name"/>
                        <AppInputTextTSWrapper
                            v-model="objectNameFilter"
                            :render-object="render.object_name"
                        />
                    </div>

                </div>
            </div>
        </div>

        <div class="ml-2">
            <!-- __ Данные (Коллекции) -->
            <div
                v-for="procedure of entitiesRender"
                :key="procedure.code_1c"
                class="max-w-fit"
            >

                <div class="flex">
                    <!-- __ Collapsed -->
                    <AppLabelTSWrapper
                        :arg="procedure"
                        :render-object="render.collapsed"
                        @click="render.collapsed.click!(procedure)"
                    />

                    <!-- __ Код из 1С -->
                    <AppLabelTSWrapper
                        :arg="procedure"
                        :render-object="render.code_1c"
                        class="cursor-pointer"
                        @click="render.collapsed.click!(procedure)"
                    />

                    <!-- __ Название -->
                    <AppLabelTSWrapper
                        :arg="procedure"
                        :render-object="render.name"
                        @click="render.collapsed.click!(procedure)"
                    />

                    <!-- __ Код объекта процедуры -->
                    <AppLabelTSWrapper
                        :arg="procedure"
                        :render-object="render.object_code"
                        @click="render.collapsed.click!(procedure)"
                    />

                    <!-- __ Тип объекта процедуры -->
                    <AppLabelTSWrapper
                        :arg="procedure"
                        :render-object="render.object_name"
                        @click="render.collapsed.click!(procedure)"
                    />

                </div>

                <!-- __ Процедуры. VBA показываем только для админа -->
                <div v-if="!procedure.collapsed">
                    <div class="flex gap-x-1">
                        <pre><code contenteditable="false">{{ procedure.text }}</code></pre>
                        <pre v-if="userStore.hasAdminRole()"><code contenteditable="false">{{ procedure.text_vba }}</code></pre>
                    </div>
                    <div class="mt-2"></div>
                </div>


            </div>
        </div>
    </div>

</template>

<script lang="ts" setup>
import type { IModelProcedure, IRenderData } from '@/types'
import { onMounted, reactive, ref, watchEffect } from 'vue'

import { useModelsStore } from '@/stores/ModelsStore.ts'
import { useUserStore } from '@/stores/UserStore.js'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppInputTextTSWrapper from '@/components/dashboard/models/components/AppInputTextTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/models/components/AppLabelTSWrapper.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/models/components/AppLabelMultilineTSWrapper.vue'

const modelsStore = useModelsStore()
const userStore   = useUserStore()


const isLoading = ref(false)

let entities: IModelProcedure[] = []
const entitiesRender            = ref<IModelProcedure[]>([])

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
    collapsed  : {
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
        data          : (procedure: IModelProcedure) => (procedure.collapsed ? '▲' : '▼'),
        click         : (procedure: IModelProcedure) => (procedure.collapsed = !procedure.collapsed),
        class         : 'cursor-pointer',
    },
    code_1c    : {
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
        data          : (procedure: IModelProcedure) => procedure.code_1c,
        class         : 'cursor-pointer',
    },
    name       : {
        id            : () => 'name-search',
        header        : ['Название', 'процедуры'],
        width         : 'w-[400px]',
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
        data          : (procedure: IModelProcedure) => procedure.name,
        class         : 'cursor-pointer',
    },
    object_code: {
        id            : () => 'object-code-search',
        header        : ['Код объекта', 'процедуры'],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Код объекта...',
        data          : (procedure: IModelProcedure) => procedure.object_code_1c ?? '',
        class         : 'cursor-pointer',
    },
    object_name: {
        id            : () => 'object-name-search',
        header        : ['Тип объекта', 'процедуры'],
        width         : 'w-[200px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Тип объекта...',
        data          : (procedure: IModelProcedure) => procedure.object_name ?? '',
        class         : 'cursor-pointer',
        // data          : (material: IMaterial) => (material.properties ? '✓' : '✗'),
    },
})

// __ Фильтры
const nameFilter       = ref('')
const code_1cFilter    = ref('')
const objectCodeFilter = ref('')
const objectNameFilter = ref('')

// __ Collapse/Expand all
const toggleCollapsed = () => {
    collapseAll.value = !collapseAll.value
    entitiesRender.value.forEach(entity => entity.collapsed = collapseAll.value)
}

watchEffect(() => {

    const nameSearch       = nameFilter.value.toLowerCase()
    const code_1cSearch    = code_1cFilter.value.toLowerCase()
    const objectCodeSearch = objectCodeFilter.value.toLowerCase()
    const objectNameSearch = objectNameFilter.value.toLowerCase()

    entitiesRender.value = entities
        .filter(entity => entity.name.toLowerCase().includes(nameSearch))
        .filter(entity => entity.code_1c.toLowerCase().includes(code_1cSearch))
        .filter(entity => entity.object_code_1c!.toLowerCase().includes(objectCodeSearch))
        .filter(entity => entity.object_name!.toLowerCase().includes(objectNameSearch))
})

// __ Получение данных
const getEntities = async () => {
    entities = await modelsStore.getProcedures()
    entities = entities
        .map(procedure => ({
            ...procedure,
            object_code_1c: procedure.object_code_1c ?? '',
            object_name   : procedure.object_name ?? '',
            collapsed     : true
        }))
        .sort((a, b) => a.name.localeCompare(b.name))
}

// __ Формирование данных для рендера
const getEntitiesRender = () => {
    entitiesRender.value = entities
}

onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getEntities()
            console.log('procedures: ', entities)

            getEntitiesRender()
            // console.log('materialsRender: ', entitiesRender.value)
        },
        undefined
        // false,
    )

    isLoading.value = false
})


</script>

<style scoped>
pre {
    @apply
    border-2 border-slate-900
    bg-white
    font-semibold
    p-3
    max-w-[1024px]
    max-h-[600px]
    text-xs;
    /* Ограничиваем ширину контейнера, чтобы он не растягивался бесконечно */
    /* max-width: 30%;*/
    /*    max-width: 1024px;
        max-height: 600px;*/

    /* Делаем содержимое прокручиваемым по горизонтали, если оно выходит за max-width */
    overflow-x: auto;
    overflow-y: auto;

    /* Это важно: отключает автоматический перенос строк, чтобы текст прокручивался */
    /* white-space: pre;*/

    /* Сохраняет пробелы, но позволяет строкам переноситься, когда они достигают границы */
    white-space: pre-wrap;

    /* Необязательно: если слово (например, очень длинное имя файла) не помещается,
       оно будет "разбито" для переноса (работает в паре с pre-wrap). */
    word-wrap: break-word;


}

code {
    font-family: monospace; /* Использование моноширинного шрифта */
    color: #333; /* Цвет текста */
}
</style>
