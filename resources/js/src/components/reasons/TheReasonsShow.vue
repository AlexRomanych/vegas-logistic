<template>
<!--<Suspense>-->
    <!--    <template #default>-->
    <div class="ml-2 mt-2">
        <div
            class="sticky top-0 flex pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit"
        >

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
                    id="name-search"
                    v-model.trim="nameFilter"
                    :width="render.name.width"
                    placeholder="Название причины..."
                    text-size="mini"
                    type="primary"
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
                    id="name-search"
                    v-model.trim="displayNameFilter"
                    :width="render.displayName.width"
                    placeholder="Отображаемое название причины..."
                    text-size="mini"
                    type="primary"
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

                <div v-for="reasonsCategory in cellGroup.reason_categories" :key="reasonsCategory.id">

                    <div v-for="reason in reasonsCategory.reasons" :key="reason.id">


                        <div class="flex">

                            <!-- __ id Стрелка -->
                            <!--                    <AppLabel-->
                            <!--                        v-if="render.plug.show"-->
                            <!--                        :align="render.plug.dataAlign"-->
                            <!--                        :text="render.plug.data(frequencyTemplate)"-->
                            <!--                        :text-size="render.plug.dataTextSize"-->
                            <!--                        :title="render.plug.title"-->
                            <!--                        :type="render.plug.type"-->
                            <!--                        :width="render.plug.width"-->
                            <!--                        class="header-item cursor-pointer"-->
                            <!--                        @click="toggleCollapse(frequencyTemplate)"-->
                            <!--                    />-->

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

                            <!-- __ Редактирование записи -->
<!--                            <router-link-->
<!--                                v-if="reason.id !== 0"-->
<!--                                :to="{ name: 'clients', params: { id: reason.id } }"-->
<!--                            >-->
<!--                                <AppLabel-->
<!--                                    align="center"-->
<!--                                    text="Редактировать"-->
<!--                                    text-size="mini"-->
<!--                                    title="Редактировать запись"-->
<!--                                    type="warning"-->
<!--                                    width="w-[100px]"-->
<!--                                />-->
<!--                            </router-link>-->

                            <!-- __ Удаление записи -->
                            <!--                    <AppLabel-->
                            <!--                        v-if="frequencyTemplate.id !== 0"-->
                            <!--                        align="center"-->
                            <!--                        text="Удалить"-->
                            <!--                        text-size="small"-->
                            <!--                        title="Удалить запись"-->
                            <!--                        type="danger"-->
                            <!--                        width="w-[100px]"-->
                            <!--                        @click="deleteFrequencyTemplate(frequencyTemplate)"-->
                            <!--                    />-->

                        </div>


                    </div>


                </div>

            </div>

        </div>
        <div v-else class="flex justify-center w-full>">
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

    <AppCallout
        v-if="calloutShow && confirmClick"
        :text="calloutMessage"
        :type="calloutType"
        @toggleShow="calloutHandler"
    />

    <AppModalAsync ref="appModalAsync" :text="modalText" mode="confirm" type="danger"/>
<!--    </template>-->
<!--    <template #fallback>-->
<!--        <div>Загрузка причин...</div>-->
<!--    </template>-->
<!--</Suspense>-->
</template>


<script lang="ts" setup>

import type { ICellsGroupReasons, IRenderDataItem, IRenderData, IReason } from '@/app/types/index.ts'

// @ts-ignore
import { useReasonStore } from '@/stores/ReasonsStore.ts'

import { ref, onMounted } from 'vue'

import { catchErrorHandler } from '@/app/helpers/helpers_checks.ts'

import AppLabelMultiline from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppInputText from '@/components/ui/inputs/AppInputText.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppModalAsync from '@/components/ui/modals/AppModalAsync.vue'
import AppSelectSimple from '@/components/ui/selects/AppSelectSimple.vue'

const reasonStore = useReasonStore()

// __ Получаем список причин в иерархии ПЯ --> Категории причин --> Список причин категории
const reasons = ref<ICellsGroupReasons[] | []>([])

const getReasons = async () => {
    try {
        reasons.value = await reasonStore.getReasons()
        console.log('reasons', reasons.value)
    } catch (e: unknown) {
        catchErrorHandler('Ошибка получения причин выполнения: ', e)
    }
}

// __ Определяем объект вывода шаблона
const render: IRenderData = {
    id: {
        header: ['ID', ''],
        width: 'w-[50px]',
        show: true,
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
        width: 'w-[350px]',
        show: true,
        title: 'Название причины',
        headerTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'left',
        type: 'primary',
        data: (reason: IReason) => reason.name,
    },
    displayName: {
        header: ['Отображаемое название', 'причины'],
        width: 'w-[350px]',
        show: true,
        title: 'Отображаемое название причины',
        headerTextSize: 'small',
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
        width: 'w-[150px]',
        show: true,
        title: 'Описание',
        headerTextSize: 'small',
        dataTextSize: 'mini',
        headerAlign: 'center',
        dataAlign: 'left',
        type: 'primary',
        data: (reason: IReason) => reason.description,
    },
}


// __ Для работы callout
const calloutShow = ref(false) // состояние окна
const confirmClick = ref(false) // определяем для вывода этого callout
const calloutMessage = ref('') // определяем показываемое сообщение
const calloutType = ref('danger') // определяем тип callout

// для того чтобы оставить анимацию в callout
const calloutHandler = (delay = 5000) => {
    setInterval(() => (confirmClick.value = false), delay)
}
// __ End Callout

const appModalAsync = ref(null) // Получаем ссылку на модальное окно
const modalText = ref('Вы уверены?')


// __ Селект для фильтра статуса
const statusSelectData = {
    name: 'status',
    data: [
        {id: 1, name: 'Все', selected: true, disabled: false},
        {id: 2, name: 'Активный', selected: false, disabled: false},
        {id: 3, name: 'Архив', selected: false, disabled: false},
    ],
}


//line--------------------------------------------------
// __ Фильтры
const nameFilter = ref('')
const displayNameFilter = ref('')
const descriptionFilter = ref('')
const statusFilter = ref(1)
const typeFilter = ref(1)

//line--------------------------------------------------
// __ Меняем статус
const filterByStatus = (status: { id: number }) => {
    statusFilter.value = status.id
}


onMounted(() => {
    getReasons()
})


</script>

<style scoped>

</style>
