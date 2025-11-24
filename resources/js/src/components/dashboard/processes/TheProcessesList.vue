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
                    <!-- __ + Бизнес-процесс -->
                    <router-link :to="{ name: 'business.processes' }">
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
        <div v-if="businessProcessesRender.length">
            <div class="pt-1 pb-1 bg-slate-200 border-2 rounded-lg border-slate-700 p-1 mb-1 max-w-fit">
                <div v-for="process of businessProcessesRender" :key="process.id">
                    <div class="flex">

                        <!-- __ id -->
                        <AppLabelTS
                            v-if="render.id.show"
                            :align="render.id.dataAlign"
                            :text="render.id.data ? render.id.data(process) : ''"
                            :text-size="render.id.dataTextSize"
                            :type="typeof render.id.type === 'function' ? render.id.type(process, false) : render.id.type"
                            :width="render.id.width"
                        />

                        <!-- __ Название -->
                        <AppLabelTS
                            v-if="render.name.show"
                            :align="render.name.dataAlign"
                            :text="render.name.data ? render.name.data(process) : ''"
                            :text-size="render.name.dataTextSize"
                            :type="typeof render.name.type === 'function' ? render.name.type(process, false) : render.name.type"
                            :width="render.name.width"
                        />

                        <!-- __ Active -->
                        <AppLabelTS
                            v-if="render.active.show"
                            :align="render.active.dataAlign"
                            :text="render.active.data ? render.active.data(process) : ''"
                            :text-size="render.active.dataTextSize"
                            :type="typeof render.active.type === 'function' ? render.active.type(process, false) : render.active.type"
                            :width="render.active.width"
                        />

                        <!-- __ Описание -->
                        <AppLabelTS
                            v-if="render.description.show"
                            :align="render.description.dataAlign"
                            :text="render.description.data ? render.description.data(process) : ''"
                            :text-size="render.description.dataTextSize"
                            :type="typeof render.description.type === 'function' ? render.description.type(process, false) : render.description.type"
                            :width="render.description.width"
                            class="truncate"
                        />

                        <!-- __ Комментарий -->
                        <AppLabelTS
                            v-if="render.comment.show"
                            :align="render.comment.dataAlign"
                            :text="render.comment.data ? render.comment.data(process) : ''"
                            :text-size="render.comment.dataTextSize"
                            :type="typeof render.comment.type === 'function' ? render.comment.type(process, false) : render.comment.type"
                            :width="render.comment.width"
                            class="truncate"
                        />

                        <!-- __ Удалить -->
                        <AppLabelTS
                            v-if="false"
                            align="center"
                            text="🗑️"
                            text-size="mini"
                            type="danger"
                            width="w-[30px]"
                            @click="console.log('Delete Business Process')"
                        />

                        <!-- __ Редактировать -->
                        <router-link :to="{ name: 'business.processes.list', /*params: { id: client.id }*/ }">
                            <AppLabelTS
                                v-if="true"
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
import type { IRenderData, ISelectData, ISelectDataItem, IBusinessProcessList } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

import { onMounted, reactive, ref, watchEffect } from 'vue'

// import { useRouter } from 'vue-router'

import { useBusinessProcessesStore } from '@/stores/BusinessProcessesStore'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'
import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'


const businessProcessesStore = useBusinessProcessesStore()

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
const businessProcesses = ref<IBusinessProcessList[]>([])
const businessProcessesRender = ref<IBusinessProcessList[]>([])

// __ Фильтры
const idFilter = ref('')
const nameFilter = ref('')
const descriptionFilter = ref('')
const commentFilter = ref('')
const activeFilter = ref(0)


const DEFAULT_HEADER_TYPE = 'primary'
const DEFAULT_DATA_TYPE = 'stone'
const INACTIVE_DATA_TYPE = 'dark'
const HEADER_ALIGN = 'center'
const DATA_ALIGN = 'left'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE = 'mini'
const FILTER_TEXT_SIZE = 'mini'

const getType = (process: IBusinessProcessList | null, isHeader: boolean = true) => {
    if (isHeader) return DEFAULT_HEADER_TYPE
    if (!process) return DEFAULT_HEADER_TYPE
    return process.active ? DEFAULT_DATA_TYPE : INACTIVE_DATA_TYPE
}

// __ Подготавливаем рендер
const render: IRenderData = reactive({
    id: {
        header: ['id', ''],
        width: 'w-[50px]',
        show: true,
        type: (process = null, isHeader: boolean = true) => getType(process, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (process) => process.id.toString(),
    },
    name: {
        header: ['Название', 'бизнес-процесса'],
        width: 'w-[200px]',
        show: true,
        type: (process = null, isHeader: boolean = true) => getType(process, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (process) => process.name,
    },
    active: {
        header: ['Актуальность', 'процесса'],
        width: 'w-[100px]',
        show: true,
        type: (process = null, isHeader: boolean = true) => {
            if (isHeader) return DEFAULT_HEADER_TYPE
            return process.active ? 'success' : 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (process) => process.active ? '✓' : '✗',
    },
    description: {
        header: ['Описание', 'бизнес-процесса'],
        width: 'w-[250px]',
        show: true,
        type: (process = null, isHeader: boolean = true) => getType(process, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (process) => process.description ?? ''
    },
    comment: {
        header: ['Комментарий', 'к бизнес-процессу'],
        width: 'w-[250px]',
        show: true,
        type: (client = null, isHeader: boolean = true) => getType(client, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (process) => process.comment ?? ''
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

// __ Обрабатываем селекты
const filterByActive = (value: ISelectDataItem) => {
    activeFilter.value = value.id
}


// __ Обнуляем фильтры
const resetFilters = () => {
    idFilter.value = ''
    nameFilter.value = ''
    descriptionFilter.value = ''
    commentFilter.value = ''
    activeFilter.value = 0
}

// __ Получаем данные
const getBusinessProcesses = async () => {
    businessProcesses.value = await businessProcessesStore.getBusinessProcesses()
    businessProcesses.value = businessProcesses.value.map(businessProcess => {
        businessProcess.description = businessProcess.description ?? ''
        businessProcess.comment = businessProcess.comment ?? ''
        return {
            ...businessProcess,
            can_edit: false,        // Добавляем возможность редактирования
        }
    })
}

// __ Подготавливаем данные для отображения
const getProcessesRender = () => businessProcessesRender.value = businessProcesses.value

// __ Удаляем клиента
const deleteProcess = async (process: IBusinessProcessList) => {
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
    businessProcessesRender.value = businessProcesses.value
        .filter(process => process.id.toString().includes(idFilter.value.toLowerCase()))
        .filter(process => process.name.toLowerCase().includes(nameFilter.value.toLowerCase()))
        .filter(process => process.description!.toLowerCase().includes(descriptionFilter.value.toLowerCase()))
        .filter(process => process.comment!.toLowerCase().includes(commentFilter.value.toLowerCase()))
        .filter(process => {
            if (activeFilter.value === 0) return true
            else if (activeFilter.value === 1) return process.active
            else if (activeFilter.value === 2) return !process.active
        })
})


onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getBusinessProcesses()
            getProcessesRender()      // Подготавливаем данные для отображения

            console.log('businessProcesses: ', businessProcesses.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})


</script>

<style scoped></style>
