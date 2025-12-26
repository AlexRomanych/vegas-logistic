<template>
    <div v-if="!isLoading" class="ml-2 mt-2">
        <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit">
            <div>
                <div class="flex ml-0.5">

                    <!-- __ Collapsed -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.collapsed" @click="toggleCollapsed"/>
                    </div>

                    <!-- __ id -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.id"/>
                        <AppInputTextTSWrapper v-model="idFilter" :render-object="render.id"/>
                    </div>

                    <!-- __ Клиент -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.client"/>
                        <AppInputTextTSWrapper v-model="clientFilter" :render-object="render.client"/>
                    </div>

                    <!-- __ Номер Заявки -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.orderNoStr"/>
                        <AppInputTextTSWrapper v-model="orderNoStrFilter" :render-object="render.orderNoStr"/>
                    </div>

                    <!-- __ Тип элементов (изделий) -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.elementsType"/>
                        <AppInputTextTSWrapper v-model="elementsTypeFilter" :render-object="render.elementsType"/>
                    </div>

                    <!-- __ Общее количество элементов (изделий) -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.orderAmount"/>
                        <!--<AppInputTextTSWrapper v-model="orderAmountFilter" :render-object="render.orderAmount"/>-->
                    </div>

                    <!-- __ Период, к которому относится Заявка -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.orderPeriod"/>
                        <AppInputTextTSWrapper
                            v-model="orderPeriodFilter"
                            :render-object="render.orderPeriod"
                            @input="handleOrderPeriodDate"
                        />
                    </div>

                    <!-- __ Active -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.orderActive"/>

                        <!-- __ Фильтр: Active -->
                        <AppSelectSimpleTS
                            v-if="render.orderActive.show"
                            :select-data="orderActiveSelect"
                            :text-size="render.orderActive.headerTextSize"
                            :type="
                                orderActiveFilter === 0
                                ? 'primary'
                                : orderActiveFilter === 1
                                    ? 'success'
                                    : 'danger'
                            "
                            :width="render.orderActive.width"
                            align="center"
                            class="mt-[8px]"
                            height="h-[30px]"
                            @change="filterByOrderActive"
                        />
                    </div>

                    <!-- __ Прогнозный -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.isForecast"/>

                        <!-- __ Фильтр: Active -->
                        <AppSelectSimpleTS
                            v-if="render.isForecast.show"
                            :select-data="orderForecastSelect"
                            :text-size="render.isForecast.headerTextSize"
                            :type="
                                orderForecastFilter === 0
                                ? 'primary'
                                : orderForecastFilter === 1
                                    ? 'success'
                                    : 'danger'
                            "
                            :width="render.isForecast.width"
                            align="center"
                            class="mt-[8px]"
                            height="h-[30px]"
                            @change="filterByOrderForecast"
                        />
                    </div>

                    <!-- __ Отображаемый в Планах -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.isShown"/>

                        <!-- __ Фильтр: Active -->
                        <AppSelectSimpleTS
                            v-if="render.isShown.show"
                            :select-data="orderShownSelect"
                            :text-size="render.isShown.headerTextSize"
                            :type="
                                orderShownFilter === 0
                                ? 'primary'
                                : orderShownFilter === 1
                                    ? 'success'
                                    : 'danger'
                            "
                            :width="render.isShown.width"
                            align="center"
                            class="mt-[8px]"
                            height="h-[30px]"
                            @change="filterByOrderShown"
                        />
                    </div>

                    <!-- __ Дата загрузки на складе Вегас -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.loadAt"/>
                        <AppInputTextTSWrapper
                            v-model="loadAtFilter"
                            :render-object="render.loadAt"
                            @input="handleLoadAtDate"
                        />
                    </div>

                    <!-- __ Дата разагрузки на складе клиента -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.unloadAt"/>
                        <AppInputTextTSWrapper
                            v-model="unloadAtFilter"
                            :render-object="render.unloadAt"
                            @input="handleUnloadAtDate"
                        />
                    </div>

                    <!-- __ Комментарий из 1С -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.comment_1c"/>
                        <AppInputTextTSWrapper v-model="comment1CFilter" :render-object="render.comment_1c"/>
                    </div>

                    <!-- __ Описание -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.description"/>
                        <AppInputTextTSWrapper v-model="descriptionFilter" :render-object="render.description"/>
                    </div>

                </div>
            </div>
        </div>

        <!-- __ Данные -->
        <div v-for="order of ordersRender" :key="order.id" class="ml-2 max-w-fit">

            <div v-if="!order.collapsed">
                <TheDividerLine/>
                <div class="min-h-3 bg-red-50 rounded-[4px]"></div>
            </div>

            <div :class="!order.collapsed ? 'bg-green-100 rounded-[4px]' : ''">

                <div class="flex ">

                    <!-- __ Collapsed -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.collapsed"
                                       @click="render.collapsed.click!(order)"/>

                    <!-- __ ID -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.id"/>

                    <!-- __ Клиент -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.client"/>

                    <!-- __ Номер Заявки -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.orderNoStr"/>

                    <!-- __ Тип элементов (изделий) -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.elementsType"/>

                    <!-- __ Общее количество элементов (изделий) -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.orderAmount"/>

                    <!-- __ Период, к которому относится Заявка -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.orderPeriod"/>

                    <!-- __ Active -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.orderActive"/>

                    <!-- __ Прогнозный -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.isForecast"/>

                    <!-- __ Отображаемый в Планах -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.isShown"/>

                    <!-- __ Дата загрузки на складе Вегас -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.loadAt"/>

                    <!-- __ Дата разагрузки на складе клиента -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.unloadAt"/>

                    <!-- __ Комментарий из 1С -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.comment_1c"/>

                    <!-- __ Описание -->
                    <AppLabelTSWrapper :arg="order" :render-object="render.description"/>

                </div>

                <!-- __ Сами данные по абонентскому соглашению -->
                <div v-if="!order.collapsed">
                    <OrderLines :order-lines="order.lines"/>
                    <div class="min-h-3"></div>
                    <TheDividerLine/>
                </div>
            </div>
        </div>

    </div>

</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, watchEffect } from 'vue'

import type { IDataInputObj, IRenderData, IRenderOrder, IRenderOrderLine, ISelectData, ISelectDataItem } from '@/types'

import { useOrdersStore } from '@/stores/OrdersStore.ts'

import { formatDateIntl, getDateFromDateTimeString, validateInputDateHelper } from '@/app/helpers/helpers_date.js'

import AppLabelMultilineTSWrapper from '@/components/dashboard/orders/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
import AppInputTextTSWrapper from '@/components/dashboard/orders/components/AppInputTextTSWrapper.vue'
import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'
import OrderLines from '@/components/dashboard/orders/order_components/order_render/OrderLines.vue'
import TheDividerLine from '@/components/ui/dividers/TheDividerLine.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

const isLoading = ref(false)

const ordersStore = useOrdersStore()

// __ Глобальный Collapse
const collapseAll = ref(true)

// __ Определяем переменные
const orders = ref<IRenderOrder[]>([])
const ordersRender = ref<IRenderOrder[]>([])

// __ Возможность редактирования
// TODO: Реализовать через систему ролей
const canEdit = ref(false)

// __ Объект отображения данных
// const DEFAULT_WIDTH = 'w-[100px]'
const DEFAULT_WIDTH_BOOL = 'w-[70px]'
const DEFAULT_WIDTH_DATE = 'w-[100px]'
const DEFAULT_HEIGHT = 'h-[30px]'
const HEADER_TYPE = 'primary'
const DATA_TYPE = 'primary'
const DEFAULT_TYPE = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE = 'mini'
const HEADER_ALIGN = 'center'
const DATA_ALIGN = 'left'
const DATA_ALIGN_DEFAULT = 'center'

const render: IRenderData = reactive({
    collapsed: {
        header: ['▲', '▼'],
        width: 'w-[30px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => 'warning',
        dataType: () => DATA_TYPE,
        type: () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (order: IRenderOrder) => order.collapsed ? '▲' : '▼',
        click: (order: IRenderOrder) => order.collapsed = !order.collapsed
    },
    id: {
        id: () => 'id-search',
        header: ['ID', ''],
        width: 'w-[50px]',
        height: DEFAULT_HEIGHT,
        show: false,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        placeholder: '🔍id...',
        data: (order: IRenderOrder) => order.id.toString()
    },
    client: {
        id: () => 'client-search',
        header: ['Клиент', ''],
        width: 'w-[164px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        placeholder: '🔍Клиент...',
        data: (order: IRenderOrder) => order.client.short_name,
        color: (order: IRenderOrder) => order.order_type.color,
        title: (order: IRenderOrder) => order.order_type.display_name
    },
    orderNoStr: {
        id: () => 'order-no-search',
        header: ['№', 'Заявки'],
        width: 'w-[60px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        placeholder: '🔍№...',
        data: (order: IRenderOrder) => order.order_no_str,
        color: (order: IRenderOrder) => order.order_type.color,
        title: (order: IRenderOrder) => order.order_type.display_name
    },
    elementsType: {
        id: () => 'elements-type-search',
        header: ['Тип', 'изделий'],
        width: 'w-[200px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (order: IRenderOrder) => {
            if (!order) return DEFAULT_TYPE
            return order.elements_type_render.toLowerCase().includes('матрасы')
                ? 'success' : order.elements_type_render.toLowerCase().includes('аксессуары')
                    ? 'info' : 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        placeholder: '🔍Тип изделий...',
        data: (order: IRenderOrder) => order.elements_type_render
    },
    orderAmount: {
        id: () => 'order-amount-search',
        header: ['Общее', 'кол-во'],
        width: 'w-[50px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        placeholder: '🔍Кол-во...',
        data: (order: IRenderOrder) => order.lines.reduce((acc: number, line: IRenderOrderLine) => acc + line.amount, 0).toString()
    },
    orderPeriod: {
        id: () => 'order-period-search',
        header: ['Период', 'заявки'],
        width: DEFAULT_WIDTH_DATE,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        placeholder: '🔍01.мм.гггг...',
        data: (order: IRenderOrder) => formatDateIntl(order.order_period, false, false)
    },
    orderActive: {
        header: ['Актуаль-', 'ная'],
        width: DEFAULT_WIDTH_BOOL,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (order: IRenderOrder) => order.active ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN_DEFAULT,
        data: (order: IRenderOrder) => order.active ? '✓' : '✗'
    },
    isForecast: {
        header: ['Прогноз-', 'ная'],
        width: DEFAULT_WIDTH_BOOL,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (order: IRenderOrder) => order.is_forecast ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN_DEFAULT,
        data: (order: IRenderOrder) => order.is_forecast ? '✓' : '✗'
    },
    isShown: {
        header: ['Отобра-', 'жаемая '],
        width: DEFAULT_WIDTH_BOOL,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (order: IRenderOrder) => order.shown ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN_DEFAULT,
        data: (order: IRenderOrder) => order.shown ? '✓' : '✗'
    },
    loadAt: {
        id: () => 'load-at-search',
        header: ['Дата', 'загрузки'],
        width: DEFAULT_WIDTH_DATE,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        placeholder: '🔍дд.мм.гггг...',
        data: (order: IRenderOrder) => formatDateIntl(order.load_at)
    },
    unloadAt: {
        id: () => 'unload-at-search',
        header: ['Дата', 'разагрузки'],
        width: DEFAULT_WIDTH_DATE,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        placeholder: '🔍дд.мм.гггг...',
        data: (order: IRenderOrder) => formatDateIntl(order.unload_at)
    },
    comment_1c: {
        id: () => 'comment-1c-search',
        header: ['Комментарий из 1С', ''],
        width: 'w-[250px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        placeholder: '🔍Комментарий из 1С...',
        data: (order: IRenderOrder) => order.comment_1c ?? ''
    },
    description: {  // __ Описание Заявки
        id: () => 'description-search',
        header: ['Описание (доп. информация)', 'заявки'],
        width: 'w-[250px]',
        height: DEFAULT_HEIGHT,
        show: false,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        placeholder: '🔍Описание...',
        data: (order: IRenderOrder) => order.description ?? ''
    },

})

// __ Фильтры
const idFilter = ref('')
const clientFilter = ref('')
const orderNoStrFilter = ref('')
const elementsTypeFilter = ref('')
const comment1CFilter = ref('')
const descriptionFilter = ref('')
const orderPeriodFilter = ref('')
const loadAtFilter = ref('')
const unloadAtFilter = ref('')
const orderActiveFilter = ref(0)
const orderForecastFilter = ref(0)
const orderShownFilter = ref(0)


// __ Обработчики ввода дат
const handleOrderPeriodDateObj: IDataInputObj = {   // Объект для манипуляции с вводом и выводом даты
    newValue: '',
    oldValue: '',
}
const handleOrderPeriodDate = (event: Event) => {
    const target = event.target as HTMLInputElement
    handleOrderPeriodDateObj.newValue = target.value
    validateInputDateHelper(handleOrderPeriodDateObj)  // вся логика изменения объекта будет внутри функции
    orderPeriodFilter.value = handleOrderPeriodDateObj.newValue
}

const handleLoadAtDateObj: IDataInputObj = {   // Объект для манипуляции с вводом и выводом даты
    newValue: '',
    oldValue: '',
}
const handleLoadAtDate = (event: Event) => {
    const target = event.target as HTMLInputElement
    handleLoadAtDateObj.newValue = target.value

    const copy = {...handleLoadAtDateObj}

    validateInputDateHelper(handleLoadAtDateObj)  // вся логика изменения объекта будет внутри функции
    loadAtFilter.value = handleLoadAtDateObj.newValue

    console.log('before: ', copy)
    console.log('after: ', handleLoadAtDateObj)
}

const handleUnloadAtDateObj: IDataInputObj = {   // Объект для манипуляции с вводом и выводом даты
    newValue: '',
    oldValue: '',
}
const handleUnloadAtDate = (event: Event) => {
    const target = event.target as HTMLInputElement
    handleUnloadAtDateObj.newValue = target.value
    validateInputDateHelper(handleUnloadAtDateObj)  // вся логика изменения объекта будет внутри функции
    unloadAtFilter.value = handleUnloadAtDateObj.newValue
}

// __ Подготавливаем селекты
const orderActiveSelect: ISelectData = {
    name: 'order-active',
    data: [
        {id: 0, name: 'Все', selected: true, disabled: false},
        {id: 1, name: '✓', selected: false, disabled: false},
        {id: 2, name: '✗', selected: false, disabled: false},
    ],
}
const orderForecastSelect: ISelectData = {
    name: 'order-forecast',
    data: [
        {id: 0, name: 'Все', selected: true, disabled: false},
        {id: 1, name: '✓', selected: false, disabled: false},
        {id: 2, name: '✗', selected: false, disabled: false},
    ],
}
const orderShownSelect: ISelectData = {
    name: 'order-shown',
    data: [
        {id: 0, name: 'Все', selected: true, disabled: false},
        {id: 1, name: '✓', selected: false, disabled: false},
        {id: 2, name: '✗', selected: false, disabled: false},
    ],
}


// __ Обрабатываем селекты
const filterByOrderActive = (value: ISelectDataItem) => {
    orderActiveFilter.value = value.id
}
const filterByOrderForecast = (value: ISelectDataItem) => {
    orderForecastFilter.value = value.id
}
const filterByOrderShown = (value: ISelectDataItem) => {
    orderShownFilter.value = value.id
}

// __ Collapse/Expand all
const toggleCollapsed = () => {
    collapseAll.value = !collapseAll.value
    ordersRender.value.forEach(order => order.collapsed = collapseAll.value)
}

// __ Получаем данные
const getOrders = async () => {
    const tempOrders = await ordersStore.getOrders()
    orders.value = tempOrders
    orders.value = tempOrders.map((order: IRenderOrder) => ({
        ...order,
        collapsed: collapseAll.value,
        description: order.description ?? ''
    }))
}

// __ Формируем отображение Заявок
const getOrdersRender = () => {
    // ordersRender.value = orders.value
    ordersRender.value = orders.value.sort((a, b) => a.no_1c.localeCompare(b.no_1c))
}


// __ Реализация фильтров
watchEffect(() => {
    ordersRender.value = orders.value
        .filter(order => order.id.toString().toLowerCase().includes(idFilter.value.toLowerCase()))
        .filter(order => order.client.short_name.toLowerCase().includes(clientFilter.value.toLowerCase()))
        .filter(order => order.order_no_str.toLowerCase().includes(orderNoStrFilter.value.toLowerCase()))
        .filter(order => order.elements_type_render.toLowerCase().includes(elementsTypeFilter.value.toLowerCase()))
        .filter(order => order.comment_1c.toLowerCase().includes(comment1CFilter.value.toLowerCase()))
        .filter(order => order.description!.toLowerCase().includes(comment1CFilter.value.toLowerCase()))
        .filter(order => getDateFromDateTimeString(order.load_at).includes(loadAtFilter.value))
        .filter(order => getDateFromDateTimeString(order.unload_at).includes(unloadAtFilter.value))
        .filter(order => getDateFromDateTimeString(order.order_period).includes(orderPeriodFilter.value))
        .filter(order => {
            if (orderActiveFilter.value === 0) return true
            else if (orderActiveFilter.value === 1) return order.active
            else if (orderActiveFilter.value === 2) return !order.active
        })
        .filter(order => {
            if (orderForecastFilter.value === 0) return true
            else if (orderForecastFilter.value === 1) return order.is_forecast
            else if (orderForecastFilter.value === 2) return !order.is_forecast
        })
        .filter(order => {
            if (orderShownFilter.value === 0) return true
            else if (orderShownFilter.value === 1) return order.shown
            else if (orderShownFilter.value === 2) return !order.shown
        })
    return
})


onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getOrders()
            getOrdersRender()

            // console.log('orders: ', orders.value)
            console.log('ordersRender: ', ordersRender.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})

</script>

<style scoped>

</style>
