<template>
    <div v-if="!isLoading" class="ml-2 mt-2">

        <div class="sticky top-0 p-1 mb-1 bg-blue-200 border-2 rounded-lg border-blue-700 max-w-fit">

            <div class="flex">

                <div>
                    <!-- __ id -->
                    <AppLabelMultiLineTS
                        v-if="render.id.show"
                        :align="render.id.headerAlign"
                        :text="render.id.header"
                        :text-size="render.id.headerTextSize"
                        :type="typeof render.id.type === 'function' ? render.id.type() : render.id.type"
                        :width="render.id.width"
                    />

                    <!-- __ Фильтр: id -->
                    <AppInputTextTS
                        v-if="render.id.show"
                        id="id-search"
                        v-model:text-value.trim="idFilter"
                        :text-size="render.id.filterTextSize"
                        :type="typeof render.id.type === 'function' ? render.id.type() : render.id.type"
                        :width="render.id.width"
                        placeholder="🔍id..."
                    />
                </div>

                <div>
                    <!-- __ Название -->
                    <AppLabelMultiLineTS
                        v-if="render.name.show"
                        :align="render.name.headerAlign"
                        :text="render.name.header"
                        :text-size="render.name.headerTextSize"
                        :type="typeof render.name.type === 'function' ? render.name.type() : render.name.type"
                        :width="render.name.width"
                    />

                    <!-- __ Фильтр: Название -->
                    <AppInputTextTS
                        v-if="render.name.show"
                        id="name-search"
                        v-model:text-value.trim="nameFilter"
                        :text-size="render.name.filterTextSize"
                        :type="typeof render.name.type === 'function' ? render.name.type() : render.name.type"
                        :width="render.name.width"
                        placeholder="🔍Название..."
                    />
                </div>

                <div>
                    <!-- __ Дополнительное название -->
                    <AppLabelMultiLineTS
                        v-if="render.addName.show"
                        :align="render.addName.headerAlign"
                        :text="render.addName.header"
                        :text-size="render.addName.headerTextSize"
                        :type="typeof render.addName.type === 'function' ? render.addName.type() : render.addName.type"
                        :width="render.addName.width"
                    />

                    <!-- __ Фильтр: Дополнительное название -->
                    <AppInputTextTS
                        v-if="render.addName.show"
                        id="id-add-name-search"
                        v-model:text-value.trim="addNameFilter"
                        :text-size="render.addName.filterTextSize"
                        :type="typeof render.addName.type === 'function' ? render.addName.type() : render.addName.type"
                        :width="render.addName.width"
                        placeholder="🔍Доп. название..."
                    />
                </div>

                <div>
                    <!-- __ Сокращенное название -->
                    <AppLabelMultiLineTS
                        v-if="render.shortName.show"
                        :align="render.shortName.headerAlign"
                        :text="render.shortName.header"
                        :text-size="render.shortName.headerTextSize"
                        :type="typeof render.shortName.type === 'function' ? render.shortName.type() : render.shortName.type"
                        :width="render.shortName.width"
                    />

                    <!-- __ Фильтр: Сокращенное название -->
                    <AppInputTextTS
                        v-if="render.shortName.show"
                        id="id-short-name-search"
                        v-model:text-value.trim="shortNameFilter"
                        :text-size="render.shortName.filterTextSize"
                        :type="typeof render.shortName.type === 'function' ? render.shortName.type() : render.shortName.type"
                        :width="render.shortName.width"
                        placeholder="🔍Сокр. название..."
                    />
                </div>

                <div>
                    <!-- __ Код из 1С -->
                    <AppLabelMultiLineTS
                        v-if="render.code1c.show"
                        :align="render.code1c.headerAlign"
                        :text="render.code1c.header"
                        :text-size="render.code1c.headerTextSize"
                        :type="typeof render.code1c.type === 'function' ? render.code1c.type() : render.code1c.type"
                        :width="render.code1c.width"
                    />

                    <!-- __ Фильтр: Сокращенное название -->
                    <AppInputTextTS
                        v-if="render.code1c.show"
                        id="id-code-1c-search"
                        v-model:text-value.trim="code1cFilter"
                        :text-size="render.code1c.filterTextSize"
                        :type="typeof render.code1c.type === 'function' ? render.code1c.type() : render.code1c.type"
                        :width="render.code1c.width"
                        placeholder="🔍Код из 1С..."
                    />
                </div>

                <div>
                    <!-- __ Active -->
                    <AppLabelMultiLineTS
                        v-if="render.active.show"
                        :align="render.active.headerAlign"
                        :text="render.active.header"
                        :text-size="render.active.headerTextSize"
                        :type="typeof render.active.type === 'function' ? render.active.type() : render.active.type"
                        :width="render.active.width"
                    />

                    <!-- __ Фильтр: Active -->
                    <AppSelectSimpleTS
                        v-if="render.active.show"
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
                        class="mt-[7px]"
                        height="h-[26px]"
                        @change="filterByActive"
                    />

                </div>

                <div>
                    <!-- __ Регион -->
                    <AppLabelMultiLineTS
                        v-if="render.region.show"
                        :align="render.region.headerAlign"
                        :text="render.region.header"
                        :text-size="render.region.headerTextSize"
                        :type="typeof render.region.type === 'function' ? render.region.type() : render.region.type"
                        :width="render.region.width"
                    />

                    <!-- __ Фильтр: Регион -->
                    <AppSelectSimpleTS
                        v-if="render.region.show"
                        :select-data="regionSelect"
                        :text-size="render.region.headerTextSize"
                        :type="typeof render.region.type === 'function' ? render.region.type() : render.region.type"
                        :width="render.region.width"
                        align="center"
                        class="mt-[7px]"
                        height="h-[26px]"
                        @change="filterByRegion"
                    />
                </div>

                <div>
                    <!-- __ Описание -->
                    <AppLabelMultiLineTS
                        v-if="render.description.show"
                        :align="render.description.headerAlign"
                        :text="render.description.header"
                        :text-size="render.description.headerTextSize"
                        :type="typeof render.description.type === 'function' ? render.description.type() : render.description.type"
                        :width="render.description.width"
                    />

                    <!-- __ Фильтр: Описание -->
                    <AppInputTextTS
                        v-if="render.description.show"
                        id="description-search"
                        v-model:text-value.trim="descriptionFilter"
                        :text-size="render.description.filterTextSize"
                        :type="typeof render.description.type === 'function' ? render.description.type() : render.description.type"
                        :width="render.description.width"
                        placeholder="🔍Описание..."
                    />
                </div>

                <div>
                    <!-- __ Комментарий -->
                    <AppLabelMultiLineTS
                        v-if="render.comment.show"
                        :align="render.comment.headerAlign"
                        :text="render.comment.header"
                        :text-size="render.comment.headerTextSize"
                        :type="typeof render.comment.type === 'function' ? render.comment.type() : render.comment.type"
                        :width="render.comment.width"
                    />

                    <!-- __ Фильтр: Комментарий -->
                    <AppInputTextTS
                        v-if="render.comment.show"
                        id="comment-search"
                        v-model:text-value.trim="commentFilter"
                        :text-size="render.comment.filterTextSize"
                        :type="typeof render.comment.type === 'function' ? render.comment.type() : render.comment.type"
                        :width="render.comment.width"
                        placeholder="🔍Комментарий..."
                    />
                </div>

                <div>
                    <!-- __ + Клиент -->
                    <router-link :to="{ name: 'clients.create' }">
                        <AppLabelMultiLineTS
                            :text="['➕', '']"
                            align="center"
                            class="cursor-pointer"
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

        <!-- __ Сами данные -->
        <div v-if="clientsRender.length">
            <div class="pt-1 pb-1 bg-slate-200 border-2 rounded-lg border-slate-700 p-1 mb-1 max-w-fit">
                <div v-for="client in clientsRender" :key="client.id">
                    <div class="flex">

                        <!-- __ id -->
                        <AppLabelTS
                            v-if="render.id.show"
                            :align="render.id.dataAlign"
                            :text="render.id.data ? render.id.data(client) : ''"
                            :text-size="render.id.dataTextSize"
                            :type="typeof render.id.type === 'function' ? render.id.type(client, false) : render.id.type"
                            :width="render.id.width"
                        />

                        <!-- __ Название -->
                        <AppLabelTS
                            v-if="render.name.show"
                            :align="render.name.dataAlign"
                            :text="render.name.data ? render.name.data(client) : ''"
                            :text-size="render.name.dataTextSize"
                            :type="typeof render.name.type === 'function' ? render.name.type(client, false) : render.name.type"
                            :width="render.name.width"
                        />

                        <!-- __ Дополнительное название -->
                        <AppLabelTS
                            v-if="render.addName.show"
                            :align="render.addName.dataAlign"
                            :text="render.addName.data ? render.addName.data(client) : ''"
                            :text-size="render.addName.dataTextSize"
                            :type="typeof render.addName.type === 'function' ? render.addName.type(client, false) : render.addName.type"
                            :width="render.addName.width"
                        />

                        <!-- __ Сокращенное название -->
                        <AppLabelTS
                            v-if="render.shortName.show"
                            :align="render.shortName.dataAlign"
                            :text="render.shortName.data ? render.shortName.data(client) : ''"
                            :text-size="render.shortName.dataTextSize"
                            :type="typeof render.shortName.type === 'function' ? render.shortName.type(client, false) : render.shortName.type"
                            :width="render.shortName.width"
                        />

                        <!-- __ Код из 1С -->
                        <AppLabelTS
                            v-if="render.code1c.show"
                            :align="render.code1c.dataAlign"
                            :text="render.code1c.data ? render.code1c.data(client) : ''"
                            :text-size="render.code1c.dataTextSize"
                            :type="typeof render.code1c.type === 'function' ? render.code1c.type(client, false) : render.code1c.type"
                            :width="render.code1c.width"
                        />

                        <!-- __ Active -->
                        <AppLabelTS
                            v-if="render.active.show"
                            :align="render.active.dataAlign"
                            :text="render.active.data ? render.active.data(client) : ''"
                            :text-size="render.active.dataTextSize"
                            :type="typeof render.active.type === 'function' ? render.active.type(client, false) : render.active.type"
                            :width="render.active.width"
                        />

                        <!-- __ Регион -->
                        <AppLabelTS
                            v-if="render.region.show"
                            :align="render.region.dataAlign"
                            :text="render.region.data ? render.region.data(client) : ''"
                            :text-size="render.region.dataTextSize"
                            :type="typeof render.region.type === 'function' ? render.region.type(client, false) : render.region.type"
                            :width="render.region.width"
                        />

                        <!-- __ Описание -->
                        <AppLabelTS
                            v-if="render.description.show"
                            :align="render.description.dataAlign"
                            :text="render.description.data ? render.description.data(client) : ''"
                            :text-size="render.description.dataTextSize"
                            :type="typeof render.description.type === 'function' ? render.description.type(client, false) : render.description.type"
                            :width="render.description.width"
                            class="truncate"
                        />

                        <!-- __ Комментарий -->
                        <AppLabelTS
                            v-if="render.comment.show"
                            :align="render.comment.dataAlign"
                            :text="render.comment.data ? render.comment.data(client) : ''"
                            :text-size="render.comment.dataTextSize"
                            :type="typeof render.comment.type === 'function' ? render.comment.type(client, false) : render.comment.type"
                            :width="render.comment.width"
                            class="truncate"
                        />

                        <!-- __ Удалить -->
                        <AppLabelTS
                            v-if="client.can_edit"
                            align="center"
                            text="🗑️"
                            text-size="mini"
                            type="danger"
                            width="w-[30px]"
                            @click="deleteClient(client)"
                        />

                        <!-- __ Редактировать -->
                        <router-link :to="{ name: 'clients.edit', params: { id: client.id } }">
                            <AppLabelTS
                                v-if="client.can_edit"
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
        </div>

        <div v-else>
            <AppLabelTS
                text="Нет данных"
                type="info"
            />
        </div>
    </div>

    <AppModalAsyncMultilineTS
        ref="appModalAsyncTS"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />

    <AppCallout
        :show="modalSimpleShow"
        :text="modalSimpleText"
        :type="modalSimpleType"
    />

</template>


<script lang="ts" setup>
import type { IClient, IRenderData, ISelectData, ISelectDataItem } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

import { onMounted, reactive, ref, watchEffect } from 'vue'

// import { useRouter } from 'vue-router'

import { useClientsStore } from '@/stores/ClientsStore'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'
import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'

const clientsStore = useClientsStore()

const isLoading = ref(false)

// __ Тип для модального окна
const modalType = ref<IColorTypes>('danger')
const modalText = ref<string[]>([])
const modalMode = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncTS = ref<any>(null)         // Получаем ссылку на модальное окно с асинхронной функцией

// __ Простое модальное окно для вывода ошибок и предупреждений
const modalSimpleType = ref('danger')
const modalSimpleText = ref('')
const modalSimpleShow = ref(false)
// const modalSimpleClose = (delay = 5000) => setTimeout(() => modalSimpleShow.value = false, delay) // закрываем модалку


// __ Подготавливаем переменные
const clients = ref<IClient[]>([])
const clientsRender = ref<IClient[]>([])

// __ Фильтры
const idFilter = ref('')
const nameFilter = ref('')
const addNameFilter = ref('')
const shortNameFilter = ref('')
const code1cFilter = ref('')
const descriptionFilter = ref('')
const commentFilter = ref('')
const activeFilter = ref(0)
const regionFilter = ref(0)


const DEFAULT_HEADER_TYPE = 'primary'
const DEFAULT_DATA_TYPE = 'stone'
const INACTIVE_DATA_TYPE = 'dark'
const HEADER_ALIGN = 'center'
const DATA_ALIGN = 'left'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE = 'mini'
const FILTER_TEXT_SIZE = 'mini'

const getType = (client: IClient | null, isHeader: boolean = true) => {
    if (isHeader) return DEFAULT_HEADER_TYPE
    if (!client) return DEFAULT_HEADER_TYPE
    return client.active ? DEFAULT_DATA_TYPE : INACTIVE_DATA_TYPE
}

// __ Подготавливаем рендер
const render: IRenderData = reactive({
    id: {
        header: ['id', ''],
        width: 'w-[50px]',
        show: true,
        headerType: DEFAULT_HEADER_TYPE,
        type: (client = null, isHeader: boolean = true) => getType(client, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (client) => client.id.toString(),
    },
    name: {
        header: ['Название', 'клиента'],
        width: 'w-[200px]',
        show: true,
        headerType: DEFAULT_HEADER_TYPE,
        type: (client = null, isHeader: boolean = true) => getType(client, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (client) => client.name,
    },
    addName: {
        header: ['Доп. название', 'клиента'],
        width: 'w-[150px]',
        show: true,
        headerType: DEFAULT_HEADER_TYPE,
        type: (client = null, isHeader: boolean = true) => getType(client, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (client) => client.add_name,
    },
    shortName: {
        header: ['Сокр. название', 'клиента'],
        width: 'w-[150px]',
        show: true,
        headerType: DEFAULT_HEADER_TYPE,
        type: (client = null, isHeader: boolean = true) => getType(client, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (client) => client.short_name,
    },
    code1c: {
        header: ['Код', 'из 1С'],
        width: 'w-[150px]',
        show: true,
        headerType: DEFAULT_HEADER_TYPE,
        type: (client = null, isHeader: boolean = true) => getType(client, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (client) => client.code_1c ?? '',
    },
    active: {
        header: ['Актуальность', 'клиента'],
        width: 'w-[100px]',
        show: true,
        headerType: DEFAULT_HEADER_TYPE,
        type: (client = null, isHeader: boolean = true) => {
            if (isHeader) return DEFAULT_HEADER_TYPE
            return client.active ? 'success' : 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (client) => client.active ? '✓' : '✗',
    },
    region: {
        header: ['Регион', 'клиента'],
        width: 'w-[100px]',
        show: true,
        headerType: DEFAULT_HEADER_TYPE,
        type: (client = null, isHeader: boolean = true) => getType(client, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (client) => client.region === 'west' ? 'Запад' : 'Восток',
    },
    description: {
        header: ['Описание', 'клиента'],
        width: 'w-[250px]',
        show: true,
        headerType: DEFAULT_HEADER_TYPE,
        type: (client = null, isHeader: boolean = true) => getType(client, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (client) => client.description ?? ''
    },
    comment: {
        header: ['Комментарий', 'к клиенту'],
        width: 'w-[250px]',
        show: true,
        headerType: DEFAULT_HEADER_TYPE,
        type: (client = null, isHeader: boolean = true) => getType(client, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (client) => client.comment ?? ''
    },
})

// __ Подготавливаем селекты
const activeSelect: ISelectData = {
    name: 'active',
    data: [
        {id: 0, name: 'Все', selected: true, disabled: false},
        {id: 1, name: '✓', selected: false, disabled: false},
        {id: 2, name: '✗', selected: false, disabled: false},
    ],
}

const regionSelect: ISelectData = {
    name: 'region',
    data: [
        {id: 0, name: 'Все', selected: true, disabled: false},
        {id: 1, name: 'Восток', selected: false, disabled: false},
        {id: 2, name: 'Запад', selected: false, disabled: false},
    ],
}

// __ Обрабатываем селекты
const filterByActive = (value: ISelectDataItem) => {
    activeFilter.value = value.id
}

const filterByRegion = (value: ISelectDataItem) => {
    regionFilter.value = value.id
}


// __ Обнуляем фильтры
const resetFilters = () => {
    idFilter.value = ''
    nameFilter.value = ''
    addNameFilter.value = ''
    shortNameFilter.value = ''
    descriptionFilter.value = ''
    commentFilter.value = ''
    activeFilter.value = 0
    regionFilter.value = 0
}

// __ Получаем данные
const getClients = async () => {
    clients.value = (await clientsStore.getClients() as IClient[]).sort((a, b) => a.id - b.id)
    clients.value = clients.value.map(client => {
        client.description = client.description ?? ''
        client.comment = client.comment ?? ''
        client.code_1c = client.code_1c ?? ''
        return {...client, can_edit: client.id !== 0}
    })   // Добавляем возможность редактирования
}

// __ Подготавливаем данные для отображения
const getClientsRender = () => clientsRender.value = clients.value

// __ Удаляем клиента
const deleteClient = async (client: IClient) => {
    // TODO: Тут логика прав доступа

    return  // Warn! Не разрешаем удалять клиента

    // modalText.value = ['Данные будут удалены.', 'Продолжить?']
    // modalType.value = 'danger'
    // modalMode.value = 'confirm'
    //
    // const result = appModalAsyncTS.value.show()             // показываем модалку и ждем ответ
    // if (result) {
    //     resetFilters()
    //     await clientsStore.deleteClient(client.id)
    //     await getClients()      // Получаем список клиентов
    //     getClientsRender()      // Подготавливаем данные для отображения
    // }
}

// const router = useRouter()                 // Определяем роутер
// const edit = (client: IClient) => {
//     router.push({
//         name: 'clients.edit',
//         params: {id: client.id}
//     })
// }

// const createClient = () => {
//     router.push({
//         name: 'clients.create',
//     })
// }

// __ Реализация фильтров
watchEffect(() => {
    clientsRender.value = clients.value
        .filter(client => client.id.toString().includes(idFilter.value.toLowerCase()))
        .filter(client => client.name.toLowerCase().includes(nameFilter.value.toLowerCase()))
        .filter(client => client.add_name.toLowerCase().includes(addNameFilter.value.toLowerCase()))
        .filter(client => client.short_name.toLowerCase().includes(shortNameFilter.value.toLowerCase()))
        .filter(client => client.code_1c!.toLowerCase().includes(code1cFilter.value.toLowerCase()))
        .filter(client => client.description!.toLowerCase().includes(descriptionFilter.value.toLowerCase()))
        .filter(client => client.comment!.toLowerCase().includes(commentFilter.value.toLowerCase()))
        .filter(client => {
            if (activeFilter.value === 0) return true
            else if (activeFilter.value === 1) return client.active
            else if (activeFilter.value === 2) return !client.active
        })
        .filter(client => {
            if (regionFilter.value === 0) return true
            else if (regionFilter.value === 1) return client.region === 'east'
            else if (regionFilter.value === 2) return client.region === 'west'
        })
})


onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getClients()      // Получаем список клиентов
            getClientsRender()      // Подготавливаем данные для отображения
            // console.log('clients: ', clients.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})


</script>

<style scoped></style>
