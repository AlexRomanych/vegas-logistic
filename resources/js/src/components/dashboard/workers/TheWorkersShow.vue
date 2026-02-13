<template>
    <div v-if="!isLoading" class="ml-2 mt-2">
        <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit">
            <div>
                <div class="flex ml-0.5">

                    <!-- __ id -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.id" @click="render.id.click"/>
                        <AppInputTextTSWrapper v-model="idFilter" :render-object="render.id"/>
                    </div>

                    <!-- __ Фамилия -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.surname" @click="render.surname.click"/>
                        <AppInputTextTSWrapper v-model="surnameFilter" :render-object="render.surname"/>
                    </div>

                    <!-- __ Имя -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.name" @click="render.name.click"/>
                        <AppInputTextTSWrapper v-model="nameFilter" :render-object="render.name"/>
                    </div>

                    <!-- __ Отчество -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.patronymic"
                                                    @click="render.patronymic.click"/>
                        <AppInputTextTSWrapper v-model="patronymicFilter" :render-object="render.patronymic"/>
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
                        <!-- __ + Сущность -->
                        <router-link :to="{ name: 'worker.create' }">
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
                                height="h-[26px]"
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
        <div v-for="entity of entitiesRender" :key="entity.id" class="ml-2 max-w-fit">
            <div class="flex ">

                <!-- __ id -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.id"/>

                <!-- __ Фамилия -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.surname"/>

                <!-- __ Имя -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.name"/>

                <!-- __ Отчество -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.patronymic"/>

                <!-- __ Active -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.active"/>

                <!-- __ Цвет (Picker) -->
                <AppRGBPickerModalTS
                    v-if="render.color.show"
                    v-model="entity.color!"
                    @confirm="saveEntityColor($event, entity)"
                />

                <!-- __ Описание -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.description"/>

                <!-- __ Удалить -->
                <AppLabelTS
                    v-if="CAN_DELETE"
                    align="center"
                    text="🗑️"
                    text-size="mini"
                    type="danger"
                    width="w-[30px]"
                    @click="deleteOperation(entity)"
                />

                <!-- __ Редактировать -->
                <router-link
                    :to="{ name: 'worker.edit', params: { id: entity.id } }">
                    <AppLabelTS
                        v-if="CAN_EDIT"
                        align="center"
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
import { computed, onMounted, reactive, ref, /*watchEffect*/ } from 'vue'

import type {
    IWorker, IRenderData, ISelectData, ISelectDataItem,
} from '@/types'

import { useWorkersStore } from '@/stores/WorkersStore.ts'

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


// __ Унифицируем Интерфейс
type IEntity = IWorker


const isLoading = ref(false)

const workersStore = useWorkersStore()

const DEBUG = true

// __ Права изменения
const CAN_EDIT   = true
const CAN_DELETE = true

// __ Определяем переменные
const entities = ref<IEntity[]>([])
// const entitiesRender = ref<IEntity[]>([])

// __ Фильтры
const idFilter          = ref('')
const surnameFilter     = ref('')
const nameFilter        = ref('')
const patronymicFilter  = ref('')
const descriptionFilter = ref('')
const activeFilter      = ref(0)

// __ Сортировка
const idSort         = ref<boolean | null>(null)
const nameSort       = ref<boolean | null>(null)
const surnameSort    = ref<boolean | null>(null)
const patronymicSort = ref<boolean | null>(null)

// __ Подготавливаем селекты
const activeSelect: ISelectData = {
    name: 'worker-active',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: '✓', selected: false, disabled: false },
        { id: 2, name: '✗', selected: false, disabled: false },
    ],
}


// __ Объект отображения данных
const DEFAULT_WIDTH_BOOL = 'w-[70px]'
const DEFAULT_HEIGHT     = 'h-[30px]'
const HEADER_TYPE        = 'primary'
const DATA_TYPE          = 'primary'
const DEFAULT_TYPE       = 'dark'
const HEADER_TEXT_SIZE   = 'mini'
const DATA_TEXT_SIZE     = 'micro'
const HEADER_ALIGN       = 'center'
const DATA_ALIGN         = 'left'
// const DEFAULT_WIDTH = 'w-[100px]'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const DEFAULT_WIDTH_DATE = 'w-[100px]'
// const DATA_ALIGN_DEFAULT = 'center'

const render: IRenderData = reactive({
    id:          {
        id:         () => 'id-search',
        header: () => computed(() => {
            const word = 'ID'
            if (idSort.value === null) return [word, '']
            if (idSort.value) return [word + ' ▲', '']
            return [word + ' ▼', '']
        }).value,
        width:      'w-[50px]',
        height:     DEFAULT_HEIGHT,
        show:       true,
        headerType: () => HEADER_TYPE,
        dataType:   () => DATA_TYPE,
        type:       () => DEFAULT_TYPE,
        // color:          (entity: IEntity) => entity.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍id...',
        data:           (entity: IEntity) => entity.id.toString(),
        click:          () => {
            surnameSort.value    = null
            nameSort.value       = null
            patronymicSort.value = null
            idSort.value === null ? idSort.value = true : idSort.value = !idSort.value
        }

    },
    surname:     {
        id:     () => 'surname-search',
        header: () => computed(() => {
            const word = 'Фамилия'
            if (surnameSort.value === null) return [word, '']
            if (surnameSort.value) return [word + ' ▲', '']
            return [word + ' ▼', '']
        }).value,
        width:          'w-[200px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Фамилия...',
        data:           (entity: IEntity) => entity.surname,
        click:          () => {
            idSort.value         = null
            nameSort.value       = null
            patronymicSort.value = null
            surnameSort.value === null ? surnameSort.value = true : surnameSort.value = !surnameSort.value
        }
    },
    name:        {
        id:             () => 'name-search',
        header: () => computed(() => {
            const word = 'Имя'
            if (nameSort.value === null) return [word, '']
            if (nameSort.value) return [word + ' ▲', '']
            return [word + ' ▼', '']
        }).value,
        width:          'w-[100px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Имя...',
        data:           (entity: IEntity) => entity.name,
        click:          () => {
            idSort.value         = null
            surnameSort.value    = null
            patronymicSort.value = null
            nameSort.value === null ? nameSort.value = true : nameSort.value = !nameSort.value
        }
    },
    patronymic:  {
        id:             () => 'patronymic-search',
        header: () => computed(() => {
            const word = 'Отчество'
            if (patronymicSort.value === null) return [word, '']
            if (patronymicSort.value) return [word + ' ▲', '']
            return [word + ' ▼', '']
        }).value,
        width:          'w-[100px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Отчество...',
        data:           (entity: IEntity) => entity.patronymic ?? '',
        click:          () => {
            idSort.value      = null
            surnameSort.value = null
            nameSort.value    = null
            patronymicSort.value === null ? patronymicSort.value = true : patronymicSort.value = !patronymicSort.value
        }
    },
    active:      {
        id:             () => 'active-search',
        header:         ['Актуаль-', 'ность'],
        width:          DEFAULT_WIDTH_BOOL,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (entity: IEntity) => entity.active ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Active...',
        data:           (entity: IEntity) => entity.active ? '✓' : '✗'
    },
    color:       {
        id:             () => 'color-search',
        header:         ['Цвет', 'ярлычка'],
        width:          'w-[100px]',
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Цвет...',
        data:           (entity: IEntity) => entity.color ?? '',
        class:          'cursor-pointer'
    },
    description: {
        id:             () => 'description-search',
        header:         ['Описание', ''],
        width:          'w-[450px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Описание...',
        data:           (entity: IEntity) => entity.description ?? ''
    },
})


// __ Обрабатываем селекты
const filterByActive = (value: ISelectDataItem) => {
    activeFilter.value = value.id
}

// __ Обнуляем фильтры
const resetFilters = () => {
    idFilter.value          = ''
    surnameFilter.value     = ''
    patronymicFilter.value  = ''
    nameFilter.value        = ''
    descriptionFilter.value = ''
    activeFilter.value      = 0
}


// __ Получаем данные
const getData = async () => {
    entities.value = await workersStore.getWorkers()
    entities.value = entities.value
        .filter(entity => entity.id)
        .sort((a, b) => a.surname.localeCompare(b.surname))
        .map(entities => ({ ...entities, description: entities.description ?? '', can_edit: true }))

    surnameSort.value = true // __ Сортировка по умолчанию по фамилии по возрастанию
}


// __ Объект отображения данных (Фильтры + Сортировка)
const entitiesRender = computed<IEntity[]>(() => {
    const entitiesCopy = [...entities.value]
        .filter(entity => entity.id.toString().toLowerCase().includes(idFilter.value.toLowerCase()))
        .filter(entity => entity.surname.toLowerCase().includes(surnameFilter.value.toLowerCase()))
        .filter(entity => entity.name.toLowerCase().includes(nameFilter.value.toLowerCase()))
        .filter(entity => (entity.patronymic ?? '').toLowerCase().includes(patronymicFilter.value.toLowerCase()))
        .filter(entity => entity.description!.toLowerCase().includes(descriptionFilter.value.toLowerCase()))
        .filter(entity => {
            if (activeFilter.value === 0) return true
            else if (activeFilter.value === 1) return entity.active
            else if (activeFilter.value === 2) return !entity.active
        })

    if (idSort.value !== null) {
        if (idSort.value) {
            entitiesCopy.sort((a, b) => a.id - b.id)
        } else {
            entitiesCopy.sort((a, b) => b.id - a.id)
        }
        return entitiesCopy
    }

    if (surnameSort.value !== null) {
        if (surnameSort.value) {
            entitiesCopy.sort((a, b) => a.surname.localeCompare(b.surname))
        } else {
            entitiesCopy.sort((a, b) => b.surname.localeCompare(a.surname))
        }
        return entitiesCopy
    }

    if (nameSort.value !== null) {
        if (nameSort.value) {
            entitiesCopy.sort((a, b) => a.name.localeCompare(b.name))
        } else {
            entitiesCopy.sort((a, b) => b.name.localeCompare(a.name))
        }
        return entitiesCopy
    }

    if (patronymicSort.value !== null) {
        if (patronymicSort.value) {
            entitiesCopy.sort((a, b) => (a.patronymic ?? '').localeCompare(b.patronymic ?? ''))
        } else {
            entitiesCopy.sort((a, b) => (b.patronymic ?? '').localeCompare(a.patronymic ?? ''))
        }
        return entitiesCopy
    }

    return entitiesCopy
})


// __ Удаляем типовую операцию
const deleteOperation = async (entity: IEntity) => {
    return
}

// __ Сохраняем данные по цвету
const saveEntityColor = async (event: string, entity: IEntity) => {
    return
    // await sewingStore.patchSewingOperationColor(sewingOperation.id, event)
}

// __ Формируем отображение данных
// const getDataRender = () => {
//     // entitiesRender.value = entities.value
// }

// __ Реализация фильтров
// watchEffect(() => {
    // entitiesRender.value = entities.value
    //     .filter(entity => entity.id.toString().toLowerCase().includes(idFilter.value.toLowerCase()))
    //     .filter(entity => entity.surname.toLowerCase().includes(surnameFilter.value.toLowerCase()))
    //     .filter(entity => entity.name.toLowerCase().includes(nameFilter.value.toLowerCase()))
    //     .filter(entity => (entity.patronymic ?? '').toLowerCase().includes(patronymicFilter.value.toLowerCase()))
    //     .filter(entity => entity.description!.toLowerCase().includes(descriptionFilter.value.toLowerCase()))
    //     .filter(entity => {
    //         if (activeFilter.value === 0) return true
    //         else if (activeFilter.value === 1) return entity.active
    //         else if (activeFilter.value === 2) return !entity.active
    //     })
// })

// // __ Реализация Сортировки
// watchEffect(() => {
//     if (idSort.value !== null) {
//         if (idSort.value) {
//             entitiesRender.value.sort((a, b) => a.id - b.id)
//         } else {
//             entitiesRender.value.sort((a, b) => b.id - a.id)
//         }
//         return
//     }
//
//     if (surnameSort.value !== null) {
//         if (surnameSort.value) {
//             entitiesRender.value.sort((a, b) => a.surname.localeCompare(b.surname))
//         } else {
//             entitiesRender.value.sort((a, b) => b.surname.localeCompare(a.surname))
//         }
//         return
//     }
//
//     if (nameSort.value !== null) {
//         if (nameSort.value) {
//             entitiesRender.value.sort((a, b) => a.name.localeCompare(b.name))
//         } else {
//             entitiesRender.value.sort((a, b) => b.name.localeCompare(a.name))
//         }
//         return
//     }
//
//     if (patronymicSort.value !== null) {
//         if (patronymicSort.value) {
//             entitiesRender.value.sort((a, b) => (a.patronymic ?? '').localeCompare(b.patronymic ?? ''))
//         } else {
//             entitiesRender.value.sort((a, b) => (b.patronymic ?? '').localeCompare(a.patronymic ?? ''))
//         }
//         return
//     }
// })

onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getData()
            // getDataRender()
            if (DEBUG) console.log('workers: ', entitiesRender.value)

        },
        undefined,
        // false,
    )

    isLoading.value = false
})

</script>

<style scoped>

</style>
