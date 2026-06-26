<template>
    <div
        v-if="!isLoading"
        class="mx-2 mt-2"
    >
        <div class="sticky top-0 p-1 mb-1 bg-slate-200 border-2 rounded-lg border-slate-300 max-w-fit">
            <div class="mx-0.5">
                <div class="flex">

                    <!-- __ code_1c -->
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

                    <!-- __ Количества Слоев -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.layers"/>
                        <AppInputTextTSWrapper
                            v-model="layersFilter"
                            :render-object="render.layers"
                        />
                    </div>

                    <!-- __ Ширина ткани -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.width"/>
                        <AppInputTextTSWrapper
                            v-model="widthFilter"
                            :render-object="render.width"
                        />
                    </div>

                    <!-- __ Рабочая Ширина ткани -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.widthWork"/>
                        <AppInputTextTSWrapper
                            v-model="widthWorkFilter"
                            :render-object="render.widthWork"
                        />
                    </div>

                    <!-- __ Описание -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.description"/>
                        <AppInputTextTSWrapper
                            v-model="descriptionFilter"
                            :render-object="render.description"
                        />
                    </div>

                    <div>
                        <!-- __ + Ткань -->
                        <router-link :to="{ name: 'manufacture.cell.cutting.textiles.create' }">
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

        <div class="ml-2">
            <!-- __ Данные (Ткани) -->
            <div
                v-for="textile of entitiesRender"
                :key="textile.code_1c"
                class="max-w-fit"
            >
                <div class="flex">

                    <!-- __ Code_1c -->
                    <AppLabelTSWrapper
                        :arg="textile"
                        :render-object="render.code_1c"
                        class="cursor-pointer"
                    />

                    <!-- __ Название -->
                    <AppLabelTSWrapper
                        :arg="textile"
                        :render-object="render.name"
                    />

                    <!-- __ Active -->
                    <AppLabelTSWrapper
                        :arg="textile"
                        :render-object="render.active"
                    />

                    <!-- __ Количества Слоев -->
                    <AppLabelTSWrapper
                        :arg="textile"
                        :render-object="render.layers"
                    />

                    <!-- __ Ширина ткани -->
                    <AppLabelTSWrapper
                        :arg="textile"
                        :render-object="render.width"
                    />

                    <!-- __ Рабочая Ширина ткани -->
                    <AppLabelTSWrapper
                        :arg="textile"
                        :render-object="render.widthWork"
                    />

                    <!-- __ Описание -->
                    <AppLabelTSWrapper
                        :arg="textile"
                        :render-object="render.description"
                    />

                    <!-- __ Удалить -->
                    <AppLabelTS
                        v-if="CAN_DELETE"
                        align="center"
                        rounded="4"
                        text="🗑️"
                        text-size="mini"
                        type="danger"
                        width="w-[30px]"
                        @click="deleteTextile(textile)"
                    />

                    <!-- __ Редактировать -->
                    <router-link
                        :to="{ name: 'manufacture.cell.cutting.textiles.edit', params: { code_1c: textile.code_1c } }">
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

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
    />

</template>

<script lang="ts" setup>
import type { IColorTypes, ICuttingTextile, IRenderData, ISelectData, ISelectDataItem } from '@/types'
import { onMounted, reactive, ref, watchEffect } from 'vue'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppInputTextTSWrapper from '@/components/dashboard/models/components/AppInputTextTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/models/components/AppLabelTSWrapper.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/models/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'


const cuttingStore = useCuttingStore()

const isLoading = ref(false)

const CAN_EDIT   = true
const CAN_DELETE = true

let entities: ICuttingTextile[] = []
const entitiesRender            = ref<ICuttingTextile[]>([])

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null)

// __ Показываем сообщение об ошибке
const showError = async (error: string | null = null) => {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'
    modalInfoText.value = error ? [error] : ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    await appModalAsyncMultilineTS.value!.show()
}


// __ Объект отображения данных
const DEFAULT_HEIGHT   = 'h-[30px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'indigo'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'mini'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'

const render: IRenderData = reactive({
    code_1c    : {
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
        data          : (textile: ICuttingTextile) => textile.code_1c,
    },
    name       : {
        id            : () => 'name-search',
        header        : ['Название', 'Ткани'],
        width         : 'w-[400px]',
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
        data          : (textile: ICuttingTextile) => textile.name,
    },
    active     : {
        id            : () => 'active-search',
        header        : ['Актуаль-', 'ность'],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (textile: ICuttingTextile) => textile.active ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Название...',
        data          : (textile: ICuttingTextile) => textile.active ? '✓' : '✗',
    },
    layers     : {
        id            : () => 'layers-search',
        header        : ['Кол-во', 'настилов'],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (textile: ICuttingTextile) => textile?.active && textile?.layers === 0 ? 'danger' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Слои...',
        data          : (textile: ICuttingTextile) => textile.layers.toString(),
    },
    width      : {
        id            : () => 'width-search',
        header        : ['Ширина', 'ткани, см'],
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
        placeholder   : '🔍Шир...',
        data          : (textile: ICuttingTextile) => textile.width.toString(),
    },
    widthWork  : {
        id            : () => 'width-work-search',
        header        : ['Раб. ширина', 'ткани, см'],
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
        placeholder   : '🔍Раб.шир...',
        data          : (textile: ICuttingTextile) => textile.width_work.toString(),
    },
    description: {
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
        data          : (textile: ICuttingTextile) => textile.description ?? ''
    },
})

// __ Фильтры
const nameFilter        = ref('')
const code_1cFilter     = ref('')
const descriptionFilter = ref('')
const layersFilter      = ref('')
const widthFilter       = ref('')
const widthWorkFilter   = ref('')
const activeFilter      = ref(0)

// __ Подготавливаем селекты
const activeSelect: ISelectData = {
    name: 'active',
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

// __ Удаляем Ткань
const deleteTextile = async (textile: ICuttingTextile) => {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'confirm'

    modalInfoText.value = ['Запись будет удалена.', 'Продолжить?']
    const answer        = await appModalAsyncMultilineTS.value!.show()

    if (!answer) {
        return
    }

    const result = await cuttingStore.deleteCuttingTextile(textile.code_1c)

    if (!checkCRUD(result)) {
        await showError()
        return
    } else {
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'

        modalInfoText.value = result.payload
        await appModalAsyncMultilineTS.value!.show()
        entitiesRender.value = entitiesRender.value.filter(item => item.code_1c !== textile.code_1c)
        return
    }
}

// __ Обнуляем фильтры
const resetFilters = () => {
    code_1cFilter.value     = ''
    nameFilter.value        = ''
    layersFilter.value      = ''
    widthFilter.value       = ''
    widthWorkFilter.value   = ''
    descriptionFilter.value = ''
}

// __ Получение данных
const getEntities = async () => {
    entities = await cuttingStore.getCuttingTextiles()
    entities = entities
        .map(textile => ({
            ...textile,
            description: textile.description ?? '',
        }))
        .sort((a, b) => a.name.localeCompare(b.name))
    // .filter(textile => textile.code_1c !== 0)
}

// __ Формирование данных для рендера
const getEntitiesRender = () => {
    entitiesRender.value = entities
}

// __ Фильтрация
watchEffect(() => {
    const nameSearch        = nameFilter.value.toLowerCase()
    const code1cSearch      = code_1cFilter.value.toLowerCase()
    const descriptionSearch = descriptionFilter.value.toLowerCase()
    const layersSearch      = layersFilter.value.toLowerCase()
    const widthSearch       = widthFilter.value.toLowerCase()
    const widthWorkSearch   = widthWorkFilter.value.toLowerCase()

    entitiesRender.value = entities
        .filter(entity => entity.name.toLowerCase().includes(nameSearch))
        .filter(entity => entity.code_1c.toLowerCase().includes(code1cSearch))
        .filter(entity => entity.description!.toLowerCase().includes(descriptionSearch))
        .filter(entity => entity.layers.toString().toLowerCase().includes(layersSearch))
        .filter(entity => entity.width.toString().toLowerCase().includes(widthSearch))
        .filter(entity => entity.width_work.toString().toLowerCase().includes(widthWorkSearch))
        .filter(entity => {
            if (activeFilter.value === 0) return true
            else if (activeFilter.value === 1) return entity.active
            else if (activeFilter.value === 2) return !entity.active
        })
})

onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getEntities()
            console.log('cutting_textiles: ', entities)

            getEntitiesRender()
        },
        undefined
        // false,
    )

    isLoading.value = false
})


</script>

<style scoped>
</style>
