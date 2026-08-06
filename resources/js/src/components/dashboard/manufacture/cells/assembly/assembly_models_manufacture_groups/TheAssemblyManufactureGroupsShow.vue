<template>
    <div
        v-if="!isLoading"
        class="mx-2 mt-2"
    >
        <div class="sticky top-0 p-1 mb-1 bg-slate-200 border-2 rounded-lg border-slate-300 max-w-fit">
            <div class="mx-0.5">
                <div class="flex">

                    <!-- __ id -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.id"/>
                        <AppInputTextTSWrapper
                            v-model="idFilter"
                            :render-object="render.id"
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

                    <!-- __ Номер Группы -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.group_number"/>
                        <AppInputTextTSWrapper
                            v-model="groupNumberFilter"
                            :render-object="render.group_number"
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
                        <AppInputTextTSWrapper
                            v-model="descriptionFilter"
                            :render-object="render.description"
                        />
                    </div>

                    <div>
                        <!-- __ + Ткань -->
                        <router-link :to="{ name: 'manufacture.cell.assembly.model.manufacture.groups.create' }">
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
                v-for="group of entitiesRender"
                :key="group.id"
                class="max-w-fit"
            >
                <div class="flex">

                    <!-- __ id -->
                    <AppLabelTSWrapper
                        :arg="group"
                        :render-object="render.id"
                        class="cursor-pointer"
                    />

                    <!-- __ Название -->
                    <AppLabelTSWrapper
                        :arg="group"
                        :render-object="render.name"
                    />

                    <!-- __ Номер Группы -->
                    <AppLabelTSWrapper
                        :arg="group"
                        :render-object="render.group_number"
                    />

                    <!-- __ Active -->
                    <AppLabelTSWrapper
                        :arg="group"
                        :render-object="render.active"
                    />

                    <!-- __ Описание -->
                    <AppLabelTSWrapper
                        :arg="group"
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
                        @click="deleteGroup(group)"
                    />

                    <!-- __ Редактировать -->
                    <router-link
                        :to="{ name: 'manufacture.cell.assembly.model.manufacture.groups.edit', params: { id: group.id } }">
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
import type { IAssemblyModelManufactureGroup, IColorTypes, IRenderData, ISelectData, ISelectDataItem } from '@/types'
import { onMounted, reactive, ref, watchEffect } from 'vue'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'

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


const assemblyStore = useAssemblyStore()

const isLoading = ref(false)

const CAN_EDIT   = true
const CAN_DELETE = false

let entities: IAssemblyModelManufactureGroup[] = []
const entitiesRender                           = ref<IAssemblyModelManufactureGroup[]>([])
// let entities: ICuttingTextile[] = []

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
    id          : {
        id            : () => 'id-search',
        header        : ['ID', ''],
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍ID...',
        data          : (group: IAssemblyModelManufactureGroup) => group.id.toString(),
    },
    name        : {
        id            : () => 'name-search',
        header        : ['Название', 'Группы'],
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
        data          : (group: IAssemblyModelManufactureGroup) => group.name,
    },
    group_number: {
        id            : () => 'group-number-search',
        header        : ['Номер', 'Группы'],
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
        placeholder   : '🔍№...',
        data          : (group: IAssemblyModelManufactureGroup) => group.group_number.toString(),
    },
    active      : {
        id            : () => 'active-search',
        header        : ['Актуаль-', 'ность'],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (group: IAssemblyModelManufactureGroup) => group.active ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Название...',
        data          : (group: IAssemblyModelManufactureGroup) => group.active ? '✓' : '✗',
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
        data          : (group: IAssemblyModelManufactureGroup) => group.description ?? ''
    },
})

// __ Фильтры
const nameFilter        = ref('')
const idFilter          = ref('')
const descriptionFilter = ref('')
const groupNumberFilter = ref('')
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

// __ Удаляем Группу
const deleteGroup = async (group: IAssemblyModelManufactureGroup) => {
    if (!CAN_DELETE) {
        return
    }
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'confirm'

    modalInfoText.value = ['Запись будет удалена.', 'Продолжить?']
    const answer        = await appModalAsyncMultilineTS.value!.show()

    if (!answer) {
        return
    }

    const result = await assemblyStore.deleteModelManufactureGroup(group.id)

    if (!checkCRUD(result)) {
        await showError()
        return
    } else {
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'

        modalInfoText.value = result.payload
        await appModalAsyncMultilineTS.value!.show()
        entitiesRender.value = entitiesRender.value.filter(item => item.id !== group.id)
        return
    }
}

// __ Обнуляем фильтры
const resetFilters = () => {
    idFilter.value          = ''
    nameFilter.value        = ''
    groupNumberFilter.value = ''
    descriptionFilter.value = ''
}

// __ Получение данных
const getEntities = async () => {
    entities = await assemblyStore.getModelManufactureGroups()
    entities = entities
        .map(group => ({
            ...group,
            description: group.description ?? '',
        }))
        .sort((a, b) => a.group_number - b.group_number)
}

// __ Формирование данных для рендера
const getEntitiesRender = () => {
    entitiesRender.value = entities
}

// __ Фильтрация
watchEffect(() => {
    const nameSearch        = nameFilter.value.toLowerCase()
    const idSearch          = idFilter.value.toLowerCase()
    const groupNumberSearch = groupNumberFilter.value.toLowerCase()
    const descriptionSearch = descriptionFilter.value.toLowerCase()

    entitiesRender.value = entities
        .filter(entity => entity.name.toLowerCase().includes(nameSearch))
        .filter(entity => entity.id.toString().toLowerCase().includes(idSearch))
        .filter(entity => entity.group_number.toString().toLowerCase().includes(groupNumberSearch))
        .filter(entity => entity.description!.toLowerCase().includes(descriptionSearch))
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
            console.log('model_manuf_groups: ', entities)

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
