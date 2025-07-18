<template>
    <div class="ml-2 mt-2">
        <div
            class="sticky top-0 flex pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit"
        >
            <!-- __ Заглушка Группы ПЯ-->
            <AppLabelMultiline
                v-if="render.plugCellsGroup.show"
                :align="render.plugCellsGroup.headerAlign"
                :text="render.plugCellsGroup.header"
                :text-size="render.plugCellsGroup.headerTextSize"
                :title="render.plugCellsGroup.title"
                :type="render.plugCellsGroup.type"
                :width="render.plugCellsGroup.width"
                class="header-item cursor-pointer"
                @click="toggleCollapseCellsGroup(reasons)"
            />

            <!-- __ Заглушка Категории причин -->
            <AppLabelMultiline
                v-if="render.plugCategoryReason.show"
                :align="render.plugCategoryReason.headerAlign"
                :text="render.plugCategoryReason.header"
                :text-size="render.plugCategoryReason.headerTextSize"
                :title="render.plugCategoryReason.title"
                :type="render.plugCategoryReason.type"
                :width="render.plugCategoryReason.width"
                class="header-item cursor-pointer"
                @click="toggleCollapseCategoryReasons(reasons)"

            />

            <!-- __ id Причины -->
            <AppLabelMultiline
                v-if="render.id.show"
                :align="render.id.headerAlign"
                :text="render.id.header"
                :text-size="render.id.headerTextSize"
                :title="render.id.title"
                :type="render.id.type"
                :width="render.id.width"
                class="header-item"
            />

            <div>

                <!-- __ Название Причины -->
                <AppLabelMultiline
                    v-if="render.name.show"
                    :align="render.name.headerAlign"
                    :text="render.name.header"
                    :text-size="render.name.headerTextSize"
                    :title="render.name.title"
                    :type="render.name.type"
                    :width="render.name.width"
                    class="header-item"
                />

                <!-- __ Фильтр: Название Причины -->
                <AppInputText
                    v-if="render.name.show"
                    id="name-search"
                    v-model.trim="nameFilter"
                    :placeholder="render.name.placeholder"
                    :text-size="render.name.filterTextSize"
                    :type="render.name.type"
                    :width="render.name.width"
                />

            </div>

            <!-- __ Отображаемое название Причины -->
            <div>
                <AppLabelMultiline
                    v-if="render.displayName.show"
                    :align="render.displayName.headerAlign"
                    :text="render.displayName.header"
                    :text-size="render.displayName.headerTextSize"
                    :title="render.displayName.title"
                    :type="render.displayName.type"
                    :width="render.displayName.width"
                    class="header-item"
                />

                <!-- __ Фильтр: Причины -->
                <AppInputText
                    v-if="render.displayName.show"
                    id="display-name-search"
                    v-model.trim="displayNameFilter"
                    :placeholder="render.displayName.placeholder"
                    :text-size="render.displayName.filterTextSize"
                    :type="render.displayName.type"
                    :width="render.displayName.width"
                />
            </div>

            <div>
                <!-- __ Актуальность Шаблона периодичности вывоза отходов -->
                <AppLabelMultiline
                    v-if="render.active.show"
                    :align="render.active.headerAlign"
                    :text="render.active.header"
                    :text-size="render.active.headerTextSize"
                    :title="render.active.title"
                    :type="render.active.type"
                    :width="render.active.width"
                    class="header-item"
                />

                <!-- Верстка  -->
                <div class="mt-2"></div>

                <!-- __ Фильтр: Статус -->
                <AppSelectSimple
                    :select-data="statusSelectData"
                    :width="render.active.width"
                    height="h-[26px]"
                    text-size="mini"
                    type="primary"
                    @change="filterByStatus"
                />
            </div>

            <div>
                <!-- __ Описание Шаблона периодичности вывоза отходов -->
                <AppLabelMultiline
                    v-if="render.description.show"
                    :align="render.description.headerAlign"
                    :text="render.description.header"
                    :text-size="render.description.headerTextSize"
                    :title="render.description.title"
                    :type="render.description.type"
                    :width="render.description.width"
                    class="header-item"
                />

                <!-- __ Фильтр: Описание Шаблона периодичности вывоза отходов -->
                <AppInputText
                    id="name-search"
                    v-model.trim="descriptionFilter"
                    :width="render.description.width"
                    placeholder="Описание..."
                    text-size="mini"
                    type="primary"
                />
            </div>


            <!--            &lt;!&ndash; __ Кнопка добавления &ndash;&gt;-->
            <!--            <router-link :to="{ name: 'frequency.template.add' }">-->
            <!--                <AppLabelMultiline-->
            <!--                    :text="['Создать', 'новый тип']"-->
            <!--                    :width="'w-[100px]'"-->
            <!--                    align="center"-->
            <!--                    class="border-2 rounded-lg border-green-800 cursor-pointer"-->
            <!--                    text-size="small"-->
            <!--                    type="success"-->
            <!--                />-->
            <!--            </router-link>-->

            <!--            &lt;!&ndash; __ Кнопка показа детализации &ndash;&gt;-->
            <!--            <AppLabelMultiline-->
            <!--                :text="showDetailsText"-->
            <!--                :width="'w-[100px]'"-->
            <!--                align="center"-->
            <!--                class="border-2 rounded-lg border-blue-800 cursor-pointer"-->
            <!--                text-size="small"-->
            <!--                type="info"-->
            <!--                @click="toggleShowDetails()"-->
            <!--            />-->
        </div>

        <!-- __ Сами данные -->
        <div v-if="reasons.length" class="ml-1.5 max-w-fit">

            <div v-for="cellGroup in reasons" :key="cellGroup.id">

                <div class="flex">

                    <!-- __ Стрелки развертывания -->
                    <AppLabel
                        v-if="render.plugCellsGroup.show"
                        :align="render.plugCellsGroup.dataAlign"
                        :text="render.plugCellsGroup.data?.(cellGroup.collapsed)"
                        :text-size="render.plugCellsGroup.dataTextSize"
                        :title="render.plugCellsGroup.title"
                        :type="render.plugCellsGroup.type"
                        :width="render.plugCellsGroup.width"
                        class="header-item cursor-pointer"
                        @click="toggleCollapse(cellGroup)"
                    />

                    <!-- __ Название производственной ячейки -->
                    <AppLabel
                        v-if="render.cellGroupName.show"
                        :align="render.cellGroupName.dataAlign"
                        :text="render.cellGroupName.data?.(cellGroup)"
                        :text-size="render.cellGroupName.dataTextSize"
                        :title="render.cellGroupName.title"
                        :type="render.cellGroupName.type"
                        :width="render.cellGroupName.width"
                        class="header-item cursor-pointer"
                        @click="toggleCollapse(cellGroup)"
                    />

                </div>

                <!-- __ Разворот Группы ПЯ-->
                <div v-if="!cellGroup.collapsed">

                    <div v-for="reasonsCategory in cellGroup.reason_categories" :key="reasonsCategory.id">

                        <div class="flex">

                            <!-- __ Выравнивание -->
                            <div :class="[render.plugCellsGroup.width, 'ml-1']"></div>

                            <!-- __ Стрелки развертывания -->
                            <AppLabel
                                v-if="render.plugCategoryReason.show"
                                :align="render.plugCategoryReason.dataAlign"
                                :text="render.plugCategoryReason.data?.(reasonsCategory.collapsed)"
                                :text-size="render.plugCategoryReason.dataTextSize"
                                :title="render.plugCategoryReason.title"
                                :type="render.plugCategoryReason.type"
                                :width="render.plugCategoryReason.width"
                                class="header-item cursor-pointer"
                                @click="toggleCollapse(reasonsCategory)"
                            />

                            <!-- __ Название категории причин -->
                            <AppLabel
                                v-if="render.reasonCategory.show"
                                :align="render.reasonCategory.dataAlign"
                                :text="render.reasonCategory.data?.(reasonsCategory)"
                                :text-size="render.reasonCategory.dataTextSize"
                                :title="render.reasonCategory.title"
                                :type="render.reasonCategory.type"
                                :width="render.reasonCategory.width"
                                class="header-item cursor-pointer"
                                @click="toggleCollapse(reasonsCategory)"
                            />


                            <!-- __ Добавить запись -->
                            <router-link
                                :to="{ name: 'reasons.create' , query: {category_id: reasonsCategory.id} }"
                            >
                                <AppLabel
                                    align="center"
                                    class="cursor-pointer"
                                    text="➕️"
                                    type="warning"
                                    width="w-[30px]"
                                />
                            </router-link>


                        </div>

                        <!-- __ Разворот Категории причин-->
                        <div v-if="!reasonsCategory.collapsed">

                            <div v-for="reason in reasonsCategory.reasons" :key="reason.id">

                                <div class="flex">

                                    <!-- __ Выравнивание -->
                                    <div :class="[render.plugCellsGroup.width, 'ml-1']"></div>
                                    <div :class="[render.plugCategoryReason.width, 'ml-1']"></div>

                                    <!-- __ id Причины -->
                                    <AppLabel
                                        v-if="render.id.show"
                                        :align="render.id.dataAlign"
                                        :text="render.id.data?.(reason)"
                                        :text-size="render.id.dataTextSize"
                                        :title="render.id.title"
                                        :type="render.id.type"
                                        :width="render.id.width"
                                        class="header-item"
                                    />

                                    <!-- __ Название Причины -->
                                    <AppLabel
                                        v-if="render.name.show"
                                        :align="render.name.dataAlign"
                                        :text="render.name.data?.(reason)"
                                        :text-size="render.name.dataTextSize"
                                        :title="render.name.title"
                                        :type="render.name.type"
                                        :width="render.name.width"
                                        class="header-item cursor-pointer"
                                    />

                                    <!-- __ Отображаемое название Причины -->
                                    <AppLabel
                                        v-if="render.displayName.show"
                                        :align="render.displayName.dataAlign"
                                        :text="render.displayName.data?.(reason)"
                                        :text-size="render.displayName.dataTextSize"
                                        :title="render.displayName.title"
                                        :type="render.displayName.type"
                                        :width="render.displayName.width"
                                        class="header-item"
                                    />

                                    <!-- __ Актуальность Причины -->
                                    <AppLabel
                                        v-if="render.active.show"
                                        :align="render.active.dataAlign"
                                        :text="render.active.data?.(reason)"
                                        :text-size="render.active.dataTextSize"
                                        :title="render.active.title"
                                        :type="render.active.type"
                                        :width="render.active.width"
                                        class="header-item"
                                    />

                                    <!-- __ Описание Причины -->
                                    <AppLabel
                                        v-if="render.description.show"
                                        :align="render.description.dataAlign"
                                        :text="render.description.data?.(reason)"
                                        :text-size="render.description.dataTextSize"
                                        :title="render.description.data?.(reason)"
                                        :type="render.description.type"
                                        :width="render.description.width"
                                        class="header-item"
                                    />

                                    <!-- __ Редактировать -->
                                    <router-link
                                        v-if="reason.id !== 0"
                                        :to="{ name: 'reasons.edit', params: { id: reason.id } }"
                                    >
                                        <AppLabel
                                            align="center"
                                            class="cursor-pointer"
                                            text="✏️"
                                            type="warning"
                                            width="w-[30px]"
                                        />
                                    </router-link>

                                    <!-- __ Удалить -->
                                    <AppLabel
                                        align="center"
                                        class="cursor-pointer"
                                        text="🗑️️"
                                        type="danger"
                                        width="w-[30px]"
                                        @click="deleteReason(reason)"
                                    />

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        <div v-else class="flex justify-center w-full>">
            <div v-if="!isLoading">
                <AppLabel
                    align="center"
                    height="min-h-[50px]"
                    text="Нет данных"
                    text-size="small"
                    type="info"
                    width="w-[300px]"
                />
            </div>
        </div>

    </div>

    <AppCallout
        :show="calloutShow"
        :text="calloutMessage"
        :type="calloutType"
        @toggleShow="calloutClose"
    />

    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="confirm"
    />

</template>


<script lang="ts" setup>

import type { ICellsGroupReasons, IRenderData, IReason, IReasonCategory } from '@/app/types/index.ts'

import { ref, onMounted, watch } from 'vue'

import { useReasonStore } from '@/stores/ReasonsStore.ts'

import { checkApiAnswer } from '@/app/helpers/helpers_checks.ts'

//@ts-ignore
import { deepCopy } from '@/app/helpers/helpers_lib.js'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppInputText from '@/components/ui/inputs/AppInputText.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppSelectSimple from '@/components/ui/selects/AppSelectSimple.vue'
import AppLabelMultiline from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
// import AppModalAsync from '@/components/ui/modals/AppModalAsync.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'
// import 'vue-loading-overlay/dist/css/index.css'                  // для loader
// import { LOADER_SETTINGS } from '@/app/constants/common.ts'      // для loader


const isLoading = ref(true)
// __ End Loader

const reasonsStore = useReasonStore()


//line--------------------------------------------------


// __ Фильтры
const nameFilter = ref('')
const displayNameFilter = ref('')
const descriptionFilter = ref('')
const statusFilter = ref(1)

//line--------------------------------------------------



// __ Получаем список причин в иерархии ПЯ --> Категории причин --> Список причин категории
const reasons = ref<ICellsGroupReasons[]>([])
let reasonsSource: ICellsGroupReasons[] = []    // Исходный список причин

const getReasons = async () => {


    isLoading.value = true     // Меняем маячок загрузки на true
    const loadingService = useLoading()
    reasons.value = await loaderHandler(
        loadingService,
        () => reasonsStore.getReasons()
    )
    isLoading.value = false     // Меняем маячок загрузки на false

    // Добавляем в каждую группу причин категорий свойство collapsed + Сортируем причины по алфавиту
    reasons.value.forEach((cellGroup) => {
        cellGroup.reason_categories.forEach(reasonCategory => {
            reasonCategory.reasons = reasonCategory.reasons.sort((a, b) => a.name.localeCompare(b.name))
            reasonCategory.collapsed = true
        })
        cellGroup.collapsed = true
    })

    // Сортируем категории причины по id
    reasons.value.forEach(cellGroup => {
        cellGroup.reason_categories = cellGroup.reason_categories.sort((a, b) => a.id - b.id)
    })

    // Сортируем категории причин по id
    reasons.value = reasons.value.sort((a, b) => a.id - b.id)

    // Делаем копию исходного списка
    reasonsSource = deepCopy(reasons.value)

    // Сброс фильтров
    nameFilter.value = ''
    displayNameFilter.value = ''
    descriptionFilter.value = ''
    statusFilter.value = 1


    console.log('reasons', reasons.value)
    console.log('reasonsSource', reasonsSource)

    // __ Тут старый код до вынесения его в отдельный хелпер
    // try {
    //     const $loading = useLoading({...LOADER_SETTINGS})
    //     const loader = $loading.show()
    //     reasons.value = await reasonsStore.getReasons()
    //
    //     // Добавляем в каждую группу причин категорий свойство collapsed + Сортируем причины по алфавиту
    //     reasons.value.forEach((cellGroup) => {
    //         cellGroup.reason_categories.forEach(reasonCategory => {
    //             reasonCategory.reasons = reasonCategory.reasons.sort((a, b) => a.name.localeCompare(b.name))
    //             reasonCategory.collapsed = true
    //         })
    //         cellGroup.collapsed = true
    //     })
    //
    //     // Сортируем категории причины по id
    //     reasons.value.forEach(cellGroup => {
    //         cellGroup.reason_categories = cellGroup.reason_categories.sort((a, b) => a.id - b.id)
    //     })
    //
    //     // Сортируем категории причин по id
    //     reasons.value = reasons.value.sort((a, b) => a.id - b.id)
    //
    //     console.log('reasons', reasons.value)
    // } catch (e: unknown) {
    //     catchErrorHandler('Ошибка получения причин выполнения: ', e)
    // } finally {
    //     if (loader) loader.hide()   // Прячем лоадер
    //     isLoading.value = false     // Меняем маячок загрузки на false
    // }
}

// __ Определяем объект вывода шаблона
const render: IRenderData = {
    plugCellsGroup: {
        header: ['▲', '▼'],
        width: 'w-[30px]',
        show: true,
        title: '',
        headerTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'center',
        type: 'stone',
        data: (collapsed) => collapsed ? '▲' : '▼',     // Передаем состояние группировки
    },
    plugCategoryReason: {
        header: ['▲', '▼'],
        width: 'w-[30px]',
        show: true,
        title: '',
        headerTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'center',
        type: 'warning',
        data: (collapsed) => collapsed ? '▲' : '▼',     // Передаем состояние группировки
    },
    id: {
        header: ['ID', ''],
        width: 'w-[50px]',
        show: false,
        title: 'Идентификатор записи',
        headerTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'center',
        type: 'dark',
        data: (reason: IReason) => reason.id.toString(),
    },
    name: {
        header: ['Название', 'причины'],
        width: 'w-[500px]',
        show: true,
        title: 'Название причины',
        placeholder: 'Название причины...',
        headerTextSize: 'small',
        filterTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'left',
        type: 'primary',
        data: (reason: IReason) => reason.name,
    },
    displayName: {
        header: ['Отображаемое название', 'причины'],
        width: 'w-[350px]',
        show: false,
        title: 'Отображаемое название причины',
        placeholder: 'Отображаемое название причины...',
        headerTextSize: 'small',
        filterTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'left',
        type: 'primary',
        data: (reason: IReason) => reason.display_name,
    },
    active: {
        header: ['Актив.', ''],
        width: 'w-[80px]',
        show: true,
        title: 'Актуальность',
        headerTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'center',
        type: 'primary',
        data: (reason: IReason) => (reason.active ? '✅' : '❌'),
    },
    description: {
        header: ['Описание', ''],
        width: 'w-[400px]',
        show: true,
        title: 'Описание',
        headerTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'left',
        type: 'primary',
        data: (reason: IReason) => reason.description,
    },
    cellGroupName: {
        header: ['Название', 'ПЯ'],
        width: 'w-[300px]',
        show: true,
        title: 'Название производственной ячейки',
        headerTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'left',
        type: 'stone',
        data: (cellGroup: ICellsGroupReasons) => cellGroup.name + ' | ' + cellGroup.reason_categories.length.toString(),
    },
    reasonCategory: {
        header: ['Название', 'категории'],
        width: 'w-[300px]',
        show: true,
        title: 'Название категории причин',
        headerTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'left',
        type: 'warning',
        data: (reasonCategory: IReasonCategory) => reasonCategory.name + ' | ' + reasonCategory.reasons.length.toString(),
    },
}

// __ Селект для фильтра статуса
const statusSelectData = {
    name: 'status',
    data: [
        {id: 1, name: 'Все', selected: true, disabled: false},
        {id: 2, name: 'Активный', selected: false, disabled: false},
        {id: 3, name: 'Архив', selected: false, disabled: false},
    ],
}


// __ Для работы callout
const calloutShow = ref(false) // состояние окна
const calloutMessage = ref('') // определяем показываемое сообщение
const calloutType = ref('danger') // определяем тип callout

// для того чтобы оставить анимацию в callout
const calloutClose = (delay = 5000) => {
    setInterval(() => (calloutShow.value = false), delay)
}
// __ End Callout

// __ Модальное окно
const appModalAsync = ref(null) // Получаем ссылку на модальное окно
const modalText = ref<string | string[]>()
const modalType = ref<string>('danger')


// __ Функция для разворачивания/сворачивания
// Один элемент (группа или ПЯ)
const toggleCollapse = <T extends { collapsed: boolean }>(target: T): void => {
    target.collapsed = !target.collapsed
}

// Для Всех элементов Группы ПЯ
let collapseCellsGroup = true
const toggleCollapseCellsGroup = <T extends {
    collapsed: boolean,
    reason_categories: IReasonCategory[]
}>(target: T[]): void => {
    collapseCellsGroup = !collapseCellsGroup
    target.forEach(item => {
        item.reason_categories.forEach(reasonCategory => {
            reasonCategory.collapsed = true
        })
        item.collapsed = collapseCellsGroup
    })
}

// Для Всех элементов Категории причин
let collapseReasonsCategory = true
const toggleCollapseCategoryReasons = <T extends {
    collapsed: boolean,
    reason_categories: IReasonCategory[]
}>(target: T[]): void => {
    collapseReasonsCategory = !collapseReasonsCategory
    target.forEach(item => {
        item.reason_categories.forEach(reasonCategory => {
            reasonCategory.collapsed = collapseReasonsCategory
        })
        item.collapsed = false
    })
}
// __ End  Разворачивания/Сворачивания

// __ Меняем статус active
const filterByStatus = (status: { id: number }) => {
    statusFilter.value = status.id
}


// __ Удаление записи
const deleteReason = async (reason: IReason) => {

    modalText.value = ['Запись будет удалена', 'Продолжить?']
    modalType.value = 'danger'

    //@ts-ignore
    const answer = await appModalAsync.value.show()             // показываем модалку и ждем ответ
    if (answer) {

        const loadingService = useLoading()
        const result = await loaderHandler(
            loadingService,
            () => reasonsStore.deleteReason(reason.id),
        )

        // console.log('result: ', result)
        // console.log('checkApiAnswer: ', checkApiAnswer(result))

        if (checkApiAnswer(result).code === 0) {
            calloutType.value = 'success'
            calloutMessage.value = 'Запись удалена'
        } else {
            calloutType.value = 'danger'
            calloutMessage.value = 'Упс, что-то пошло не так...'
        }

        calloutShow.value = true
        calloutClose()

        await getReasons()  // Обновляемся

    }
}


//__ Реализация фильтра
watch(
    [
        () => nameFilter.value,
        () => displayNameFilter.value,
        () => statusFilter.value,
        () => descriptionFilter.value,
    ],
    ([
         newNameFilter,
         newDisplayNameFilter,
         newStatusFilter,
         newDescriptionFilter
     ]) => {

        // Если все фильтры пустые, то выводим все
        // обходим ситуацию когда все фильтры пустые, но были применены ранее
        // и поэтому отображаются не все записи
        if (newNameFilter === ''
            && newDisplayNameFilter === ''
            && newStatusFilter === 1
            && newDescriptionFilter === '') {
            reasons.value = reasonsSource
        } else {

            const cellsGroupsArr: ICellsGroupReasons[] = []
            let reasonsCategoriesArr: IReasonCategory[] = []
            let reasonsArr: IReason[] = []

            for (const cellGroup of reasonsSource) {

                reasonsCategoriesArr = []
                for (const reasonCategory of cellGroup.reason_categories) {

                    reasonsArr = []
                    for (const reason of reasonCategory.reasons) {
                        if (
                            // Текстовые поля
                            reason.name.toLowerCase().includes(newNameFilter.toLowerCase())
                            && reason.display_name.toLowerCase().includes(newDisplayNameFilter.toLowerCase())
                            && reason.description.toLowerCase().includes(newDescriptionFilter.toLowerCase())
                        ) {

                            // Статус: Active
                            if (newStatusFilter === 2) {
                                if (!reason.active) continue
                            } else if (newStatusFilter === 3) {
                                if (reason.active) continue
                            }

                            reasonsArr.push(reason)
                        }
                    }

                    if (reasonsArr.length > 0) {
                        reasonsCategoriesArr.push({
                            ...reasonCategory,
                            reasons: reasonsArr
                        })
                    }
                }

                if (reasonsCategoriesArr.length > 0) {
                    cellsGroupsArr.push({
                        ...cellGroup,
                        reason_categories: reasonsCategoriesArr
                    })
                }

            }

            reasons.value = cellsGroupsArr
        }

    },
    { deep: true }
)


onMounted(() => {
    getReasons()
})


</script>

<style scoped>

</style>
