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
                            id="active"
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

                        <!-- __ Фильтр: Прогнозный -->
                        <AppSelectSimpleTS
                            v-if="render.isForecast.show"
                            id="is-forecast"
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

                        <!-- __ Фильтр: Отображаемый в Планах -->
                        <AppSelectSimpleTS
                            v-if="render.isShown.show"
                            id="is-shown"
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

                    <!-- __ Дата разгрузки на складе клиента -->
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

                    <!-- __ Наличие СЗ Раскроя -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.has_cutting_task"/>
                    </div>

                    <!-- __ Наличие СЗ Пошива -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.has_sewing_task"/>
                    </div>

                    <div>
                        <div class="flex">
                            <!-- __ Добавить заявку -->
                            <div>
                                <AppLabelMultilineTSWrapper :render-object="render.order_service"
                                                            @click="$router.push({name: 'orders.average.add'})"/>
                            </div>

                            <!-- __ Выбор дат -->
                            <CellDatesSelectMiniTS
                                :period="renderPeriod"
                                @apply="loadOrders"
                            />
                        </div>

                        <!-- __ Табы отображения -->
                        <div class="flex items-end mt-1">
                            <div v-for="tab of tabs" :key="tab.id">
                                <AppLabelTS
                                    :text="tab.title"
                                    :type="activeTabIndex == tab.id ? 'primary' : tab.tabType"
                                    align="center"
                                    rounded="4"
                                    text-size="mini"
                                    width="w-[93px]"
                                    @click="activeTabIndex = tab.id"
                                />
                            </div>


                        </div>
                    </div>


                </div>
            </div>
        </div>

        <!-- __ Данные -->
        <template v-if="tabs && tabs.length !== 0">
            <!-- __ Список -->
            <template v-if="activeTabIndex === LIST_TAB_ID">
                <div v-for="order of tabs[activeTabIndex].renderData" :key="order.id" class="ml-2 max-w-fit">
                    <OrderBody
                        :order="order"
                        :render="render"
                        @delete-order="deleteOrder"
                        @print-order="printOrder"
                        @delete-order-line="deleteOrderLine"
                    />
                </div>
            </template>
            <!-- __ Группировка по клиентам -->
            <template v-else-if="activeTabIndex === CLIENTS_TAB_ID">
                <div v-for="[itemData, orders] of tabs[activeTabIndex].renderData" :key="itemData.client" class="ml-2 max-w-fit">
                    <div :class="[!itemData.collapsed ? 'mb-3' : '']">
                        <div class="flex">
                            <!-- __ Collapsed -->
                            <AppLabelTS
                                :text="itemData.collapsed ? '▲' : '▼'"
                                align="center"
                                class="cursor-pointer"
                                rounded="4"
                                text-size="mini"
                                type="info"
                                width="w-[30px]"
                                @click="itemData.collapsed = !itemData.collapsed"
                            />
                            <!-- __ Клиент -->
                            <AppLabelTS
                                :text="itemData.client + ` (${orders.length} шт.)`"
                                align="left"
                                class="cursor-pointer"
                                rounded="4"
                                text-size="mini"
                                type="indigo"
                                width="w-[590px]"
                                @click="itemData.collapsed = !itemData.collapsed"
                            />
                        </div>
                        <template v-if="!itemData.collapsed">
                            <div v-for="order of orders" :key="order.id" class="max-w-fit">
                                <OrderBody
                                    :order="order"
                                    :render="render"
                                    @delete-order="deleteOrder"
                                    @print-order="printOrder"
                                    @delete-order-line="deleteOrderLine"
                                />
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <!-- __ Группировка по датам -->
            <template v-else-if="activeTabIndex === DATES_TAB_ID">
                <div v-for="[itemData, orders] of tabs[activeTabIndex].renderData" :key="itemData.load_at" class="ml-2 max-w-fit">
                    <div :class="[!itemData.collapsed ? 'mb-3' : '']">
                        <div class="flex">
                            <!-- __ Collapsed -->
                            <AppLabelTS
                                :text="itemData.collapsed ? '▲' : '▼'"
                                align="center"
                                class="cursor-pointer"
                                rounded="4"
                                text-size="mini"
                                type="info"
                                width="w-[30px]"
                                @click="itemData.collapsed = !itemData.collapsed"
                            />
                            <!-- __ Даты -->
                            <AppLabelTS
                                :text="formatDateIntl(itemData.load_at) + ` (${orders.length} шт.)`"
                                align="left"
                                class="cursor-pointer"
                                rounded="4"
                                text-size="mini"
                                type="indigo"
                                width="w-[590px]"
                                @click="itemData.collapsed = !itemData.collapsed"
                            />
                        </div>
                        <template v-if="!itemData.collapsed">
                            <div v-for="order of orders" :key="order.id" class="max-w-fit">
                                <OrderBody
                                    :order="order"
                                    :render="render"
                                    @delete-order="deleteOrder"
                                    @print-order="printOrder"
                                    @delete-order-line="deleteOrderLine"
                                />
                            </div>
                        </template>
                    </div>
                </div>
            </template>

        </template>

    </div>


    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />


</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, watchEffect } from 'vue'

import type {
    IColorTypes,
    IDataInputObj, IPeriod,
    IRenderData,
    IRenderOrder,
    IRenderOrderLine,
    ISelectData,
    ISelectDataItem,
} from '@/types'

import { useOrdersStore } from '@/stores/OrdersStore.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import { formatDateIntl, getDateFromDateTimeString, validateInputDateHelper } from '@/app/helpers/helpers_date.js'

import AppLabelMultilineTSWrapper from '@/components/dashboard/orders/components/AppLabelMultilineTSWrapper.vue'
import AppInputTextTSWrapper from '@/components/dashboard/orders/components/AppInputTextTSWrapper.vue'
import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import CellDatesSelectMiniTS from '@/components/dashboard/orders/components/CellDatesSelectMiniTS.vue'
// import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
// import OrderLines from '@/components/dashboard/orders/order_components/order_render/OrderLines.vue'
// import TheDividerLine from '@/components/ui/dividers/TheDividerLine.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import { useRouter } from 'vue-router'
import OrderBody from '@/components/dashboard/orders/order_components/order_render/OrderBody.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

const router = useRouter()                 // Определяем роутер

const isLoading = ref(false)

const ordersStore = useOrdersStore()


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

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

    modalInfoText.value = renderError
    await appModalAsyncMultiline.value!.show()
}

// __ Глобальный Collapse
const collapseAll = ref(true)

// __ Определяем переменные
const orders       = ref<IRenderOrder[]>([])
const renderPeriod = ref<IPeriod | null>(null)
// const ordersRender = ref<IRenderOrder[]>([])

// __ Возможность редактирования
// TODO: Реализовать через систему ролей
// const canEdit = ref(false)

// __ Объект отображения данных
// const DEFAULT_WIDTH = 'w-[100px]'
const DEFAULT_WIDTH_BOOL = 'w-[70px]'
const DEFAULT_WIDTH_DATE = 'w-[100px]'
const DEFAULT_WIDTH_TASK = 'w-[70px]'
const DEFAULT_HEIGHT     = 'h-[30px]'
const HEADER_TYPE        = 'primary'
const DATA_TYPE          = 'primary'
const DEFAULT_TYPE       = 'dark'
const HEADER_TEXT_SIZE   = 'mini'
const DATA_TEXT_SIZE     = 'mini'
const HEADER_ALIGN       = 'center'
const DATA_ALIGN         = 'left'
const DATA_ALIGN_DEFAULT = 'center'

const render: IRenderData = reactive({
    collapsed    : {
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
        data          : (order: IRenderOrder) => order.collapsed ? '▲' : '▼',
        click         : (order: IRenderOrder) => order.collapsed = !order.collapsed,
    },
    id           : {
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
        data          : (order: IRenderOrder) => order.id.toString(),
    },
    client       : {
        id            : () => 'client-search',
        header        : ['Клиент', ''],
        width         : 'w-[164px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Клиент...',
        data          : (order: IRenderOrder) => order.client.short_name,
        color         : (order: IRenderOrder) => order.order_type.color,
        title         : (order: IRenderOrder) => order.order_type.display_name,
    },
    orderNoStr   : {
        id            : () => 'order-no-search',
        header        : ['№', 'Заявки'],
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
        placeholder   : '🔍№...',
        data          : (order: IRenderOrder) => order.order_no_str,
        color         : (order: IRenderOrder) => order.order_type.color,
        title         : (order: IRenderOrder) => order.order_type.display_name,
    },
    elementsType : {
        id            : () => 'elements-type-search',
        header        : ['Тип', 'изделий'],
        width         : 'w-[200px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (order: IRenderOrder) => {
            if (!order) return DEFAULT_TYPE
            return order.elements_type_render.toLowerCase().includes('матрасы')
                ? 'success' : order.elements_type_render.toLowerCase().includes('аксессуары')
                    ? 'info' : 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Тип изделий...',
        data          : (order: IRenderOrder) => order.elements_type_render,
    },
    orderAmount  : {
        id            : () => 'order-amount-search',
        header        : ['Общее', 'кол-во'],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Кол-во...',
        data          : (order: IRenderOrder) => order.lines.reduce((acc: number, line: IRenderOrderLine) => acc + line.amount, 0).toString(),
    },
    orderPeriod  : {
        id            : () => 'order-period-search',
        header        : ['Период', 'заявки'],
        width         : DEFAULT_WIDTH_DATE,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍01.мм.гггг...',
        data          : (order: IRenderOrder) => formatDateIntl(order.order_period, false, false),
    },
    orderActive  : {
        id            : () => 'order-active',
        header        : ['Актуаль-', 'ная'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (order: IRenderOrder) => order.active ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN_DEFAULT,
        data          : (order: IRenderOrder) => order.active ? '✓' : '✗',
    },
    isForecast   : {
        id            : () => 'is-forecast',
        header        : ['Раскры-', 'тая'],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (order: IRenderOrder) => !order.is_forecast ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN_DEFAULT,
        data          : (order: IRenderOrder) => !order.is_forecast ? '✓' : '✗',
    },
    isShown      : {
        id            : () => 'is-shown',
        header        : ['Отобра-', 'жаемая '],
        width         : DEFAULT_WIDTH_BOOL,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (order: IRenderOrder) => order.shown ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN_DEFAULT,
        data          : (order: IRenderOrder) => order.shown ? '✓' : '✗',
    },
    loadAt       : {
        id            : () => 'load-at-search',
        header        : ['Дата', 'загрузки'],
        width         : DEFAULT_WIDTH_DATE,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍дд.мм.гггг...',
        data          : (order: IRenderOrder) => formatDateIntl(order.load_at),
    },
    unloadAt     : {
        id            : () => 'unload-at-search',
        header        : ['Дата', 'разгрузки'],
        width         : DEFAULT_WIDTH_DATE,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍дд.мм.гггг...',
        data          : (order: IRenderOrder) => formatDateIntl(order.unload_at),
    },
    comment_1c   : {
        id            : () => 'comment-1c-search',
        header        : ['Комментарий из 1С', ''],
        width         : 'w-[250px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Комментарий из 1С...',
        data          : (order: IRenderOrder) => order.comment_1c ?? '',
    },
    description  : {  // __ Описание Заявки
        id            : () => 'description-search',
        header        : ['Описание (доп. информация)', 'заявки'],
        width         : 'w-[250px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Описание...',
        data          : (order: IRenderOrder) => order.description ?? '',
    },
    order_service: {
        id            : () => 'order-add-search',
        header        : ['Добавить', 'заявку'],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'orange',
        dataType      : () => DATA_TYPE,
        type          : () => 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : 'large',
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Добавить прогнозную заявку...',
        class         : 'cursor-pointer',
        data          : (/*order: IRenderOrder*/) => '🗑️',
    },
    order_print  : {
        id            : () => 'order-print-search',
        header        : ['Печать', ''],
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'dark',
        dataType      : () => DATA_TYPE,
        type          : () => 'info',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : 'large',
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Распечатать заявку...',
        class         : 'cursor-pointer',
        data          : (/*order: IRenderOrder*/) => '📄',
    },
    has_cutting_task   : {
        id            : () => 'comment-1c-search',
        header        : ['СЗ', 'Раскрой'],
        width         : DEFAULT_WIDTH_TASK,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (order: IRenderOrder) => order.has_tasks.cutting_task ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍СЗ Раскроя...',
        data          : (order: IRenderOrder) => order.has_tasks.cutting_task ? '✓' : '✗',
    },
    has_sewing_task   : {
        id            : () => 'comment-1c-search',
        header        : ['СЗ', 'Пошив'],
        width         : DEFAULT_WIDTH_TASK,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (order: IRenderOrder) => order.has_tasks.sewing_task ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍СЗ Раскроя...',
        data          : (order: IRenderOrder) => order.has_tasks.sewing_task ? '✓' : '✗',
    },
})

// __ Фильтры
const idFilter            = ref('')
const clientFilter        = ref('')
const orderNoStrFilter    = ref('')
const elementsTypeFilter  = ref('')
const comment1CFilter     = ref('')
const descriptionFilter   = ref('')
const orderPeriodFilter   = ref('')
const loadAtFilter        = ref('')
const unloadAtFilter      = ref('')
const orderActiveFilter   = ref(0)
const orderForecastFilter = ref(0)
const orderShownFilter    = ref(0)


// __ Обработчики ввода дат
const handleOrderPeriodDateObj: IDataInputObj = {   // Объект для манипуляции с вводом и выводом даты
    newValue: '',
    oldValue: '',
}

const handleOrderPeriodDate = (event: Event) => {
    const target                      = event.target as HTMLInputElement
    handleOrderPeriodDateObj.newValue = target.value
    validateInputDateHelper(handleOrderPeriodDateObj)  // вся логика изменения объекта будет внутри функции
    orderPeriodFilter.value = handleOrderPeriodDateObj.newValue
}

const handleLoadAtDateObj: IDataInputObj = {   // Объект для манипуляции с вводом и выводом даты
    newValue: '',
    oldValue: '',
}

const handleLoadAtDate = (event: Event) => {
    const target                 = event.target as HTMLInputElement
    handleLoadAtDateObj.newValue = target.value

    const copy = { ...handleLoadAtDateObj }

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
    const target                   = event.target as HTMLInputElement
    handleUnloadAtDateObj.newValue = target.value
    validateInputDateHelper(handleUnloadAtDateObj)  // вся логика изменения объекта будет внутри функции
    unloadAtFilter.value = handleUnloadAtDateObj.newValue
}

// __ Подготавливаем селекты
const orderActiveSelect: ISelectData   = {
    name: 'order-active',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: '✓', selected: false, disabled: false },
        { id: 2, name: '✗', selected: false, disabled: false },
    ],
}
const orderForecastSelect: ISelectData = {
    name: 'order-forecast',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: '✓', selected: false, disabled: false },
        { id: 2, name: '✗', selected: false, disabled: false },
    ],
}
const orderShownSelect: ISelectData    = {
    name: 'order-shown',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: '✓', selected: false, disabled: false },
        { id: 2, name: '✗', selected: false, disabled: false },
    ],
}


// __ Обрабатываем селекты
const filterByOrderActive   = (value: ISelectDataItem) => {
    orderActiveFilter.value = value.id
}
const filterByOrderForecast = (value: ISelectDataItem) => {
    orderForecastFilter.value = value.id
}
const filterByOrderShown    = (value: ISelectDataItem) => {
    orderShownFilter.value = value.id
}

// __ Collapse/Expand all
const toggleCollapsed = () => {
    collapseAll.value = !collapseAll.value
    if (activeTabIndex.value === LIST_TAB_ID) {
        tabs.value[activeTabIndex.value].renderData.forEach((order: IRenderOrder) => order.collapsed = collapseAll.value)
    } else if (activeTabIndex.value === CLIENTS_TAB_ID || activeTabIndex.value === DATES_TAB_ID) {
        tabs.value[activeTabIndex.value].renderData.forEach(item => item[0].collapsed = collapseAll.value)
    }

}

// __ Получаем данные
const getOrders = async (period: IPeriod | null = null) => {
    const tempOrders = await ordersStore.getOrders(period)
    orders.value     = tempOrders
    orders.value     = tempOrders.map((order: IRenderOrder) => ({
        ...order,
        collapsed  : collapseAll.value,
        description: order.description ?? '',
        comment_1c : order.comment_1c ?? '',
    }))
}

// __ Формируем отображение Заявок
// const getOrdersRender = () => {
//     // ordersRender.value = orders.value
//     // ordersRender.value = orders.value.sort((a, b) => a.no_1c.localeCompare(b.no_1c))
//     ordersRender.value = orders.value.sort((a, b) => (new Date(a.load_at!)).getTime() - (new Date(b.load_at!)).getTime())
// }


// __ Печать заявки
const printOrder = async (order: IRenderOrder) => {
    // __ Получаем объект с путем и параметрами
    const routeData = router.resolve({
        name  : 'orders.print',
        params: { id: order.id }
        // query: { orderId: id }
    })

    // __ Открываем новое окно через стандартный JS
    window.open(routeData.href, '_blank')
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---    Табы для группировки отображения           !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
const LIST_TAB_ID    = 0
const CLIENTS_TAB_ID = 1
const DATES_TAB_ID   = 2


interface ITab {
    id: number
    title: string
    tabType: IColorTypes
    active: boolean
    renderData: any
}

const tabs           = ref<ITab[]>([])
const activeTabIndex = ref(LIST_TAB_ID)

// __ Простое отображение
const setListPlainTab = () => {
    return orders.value.sort((a, b) => (new Date(a.load_at!)).getTime() - (new Date(b.load_at!)).getTime())
}

// __ Группировка по клиентам
const getGroupByClientsData = () => {
    const groupedData            = Object.groupBy(orders.value, order => order.client.short_name)
    const groupedDataArray       = Object.entries(groupedData).sort((a, b) => a[0].localeCompare(b[0]))
    const groupedDataArrayMapped = groupedDataArray.map((item: any) => [{
        client   : item[0],
        collapsed: true
    }, item[1].sort((a, b) => a.order_no_str.localeCompare(b.order_no_str))])
    // console.log(groupedDataArrayMapped)
    return groupedDataArrayMapped
}

// __ Группировка по датам
const getGroupByDatesData = () => {
    const groupedData            = Object.groupBy(orders.value, order => order.load_at ?? '')
    const groupedDataArray       = Object.entries(groupedData).sort((a, b) => a[0].localeCompare(b[0]))
    const groupedDataArrayMapped = groupedDataArray.map((item: any) => [
        { load_at: item[0], collapsed: true },
        item[1]
            .sort((a, b) => a.client.short_name.localeCompare(b.client.short_name))
            .sort((a, b) => a.order_no_str.localeCompare(b.order_no_str))
    ])
    // console.log(groupedDataArrayMapped)
    return groupedDataArrayMapped
}

const setTabs = () => {
    const listTab: ITab     = {
        id        : LIST_TAB_ID,
        tabType   : 'stone',
        title     : 'Список',
        active    : true,
        renderData: setListPlainTab()
    }
    tabs.value[LIST_TAB_ID] = listTab

    const clientsTab: ITab     = {
        id        : CLIENTS_TAB_ID,
        title     : 'Клиенты',
        tabType   : 'orange',
        active    : true,
        renderData: getGroupByClientsData()
    }
    tabs.value[CLIENTS_TAB_ID] = clientsTab

    const datesTab: ITab     = {
        id        : DATES_TAB_ID,
        title     : 'Даты',
        tabType   : 'success',
        active    : true,
        renderData: getGroupByDatesData()
    }
    tabs.value[DATES_TAB_ID] = datesTab

}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---              Удаление Заявки                  !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Проверка наличия СЗ
const hasOrderTasks = (order: IRenderOrder) => {
    let hasTask = false
    Object.values(order.has_tasks).forEach(val => hasTask ||= val )
    return hasTask
}


// __ Удаление заявки
const deleteOrder = async (order: IRenderOrder) => {
    if (hasOrderTasks(order)) {
        return
    }

    modalInfoType.value = 'danger'
    modalInfoMode.value = 'confirm'
    modalInfoText.value = [
        `Заявка ${order.client.short_name} №${order.order_no_str}`,
        'будет удалена!',
        'Продолжить?',
    ]
    const answer        = await appModalAsyncMultiline.value!.show()

    if (answer) {
        const result = await ordersStore.deleteOrders(order.id)

        if (checkCRUD(result)) {
            // __ Удаляем из массивов
            orders.value                          = orders.value.filter(item => item.id !== order.id)
            // ordersRender.value = ordersRender.value.filter(item => item.id !== order.id)
            tabs.value[LIST_TAB_ID].renderData    = setListPlainTab()
            tabs.value[CLIENTS_TAB_ID].renderData = getGroupByClientsData()
            tabs.value[DATES_TAB_ID].renderData   = getGroupByDatesData()

        } else {
            await showError()
            return
        }
    }
}

// __ Удаление записи (orderLine: IRenderOrderLine) в заявке
// __ Реализовано в карточке заявки
const deleteOrderLine = async (orderLine: IRenderOrderLine) => {

    modalInfoType.value = 'danger'
    modalInfoMode.value = 'confirm'
    modalInfoText.value = [
        `Запись ${orderLine.size} ${orderLine.model.name_report} ${orderLine.amount}`,
        'будет удалена!',
        'Продолжить?',
    ]

    const answer = await appModalAsyncMultiline.value!.show()

    if (answer) {
        // const result = await ordersStore.deleteOrderLine(orderLine.id)

        // if (checkCRUD(result)) {
        //     // __ Удаляем из массивов
        //     orders.value       = orders.value.filter(item => item.id !== order.id)
        //     ordersRender.value = ordersRender.value.filter(item => item.id !== order.id)
        // } else {
        //     await showError()
        //     return
        // }
    }
}


// __ Реализация фильтров
watchEffect(() => {
    if (!tabs.value || !tabs.value.length) {
        return
    }

    const filterOrders = (inOrders: IRenderOrder[]) => {
        return inOrders
            .filter(order => order.id.toString().toLowerCase().includes(idFilter.value.toLowerCase()))
            .filter(order => order.client.short_name.toLowerCase().includes(clientFilter.value.toLowerCase()))
            .filter(order => order.order_no_str.toLowerCase().includes(orderNoStrFilter.value.toLowerCase()))
            .filter(order => order.elements_type_render.toLowerCase().includes(elementsTypeFilter.value.toLowerCase()))
            .filter(order => order.comment_1c?.toLowerCase().includes(comment1CFilter.value.toLowerCase()))
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
    }

    if (activeTabIndex.value === LIST_TAB_ID) {
        tabs.value[activeTabIndex.value].renderData = filterOrders(orders.value)
    } else if (activeTabIndex.value === CLIENTS_TAB_ID || activeTabIndex.value === DATES_TAB_ID) {
        tabs.value[activeTabIndex.value].renderData = activeTabIndex.value === CLIENTS_TAB_ID
            ? getGroupByClientsData()
            : getGroupByDatesData()
        tabs.value[activeTabIndex.value].renderData.forEach((item: any) => item[1] = filterOrders(item[1]))
        tabs.value[activeTabIndex.value].renderData.forEach((item: any) => item[0].collapsed = !item[1].length)

    }
})


// __ Загрузка Заявок
const loadOrders = async (period: IPeriod | null = null) => {
    renderPeriod.value = period

    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getOrders(period)
            console.log('orders: ', orders.value)

            setTabs()
            // console.log('tabs: ', tabs.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
}


onMounted(async () => {
    await loadOrders()
})

</script>

<style scoped>

</style>
