<template>

    <div>
        <div class="flex">

            <!-- __ Collapsed -->
            <AppLabelTS
                v-if="render.collapsed.show"
                :width="render.collapsed.width"
                align="center"
                text=""
                text-size="normal"
                type="light"
            />

            <AppLabelTS
                v-if="render.collapsed.show"
                :width="render.collapsed.width"
                align="center"
                text=""
                text-size="normal"
                type="light"
            />

            <!-- __ Collapsed -->
            <!--<AppLabelTSWrapper :render-object="render.collapsed" header @click="toggleCollapsed"/>-->

            <!-- __ Тип изделия -->
            <AppLabelTSWrapper :render-object="render.modelType" header/>

            <!-- __ Размер -->
            <AppLabelTSWrapper :render-object="render.modelSize" header/>

            <!-- __ Название Модели -->
            <AppLabelTSWrapper :render-object="render.modelName" header/>

            <!-- __ Количество -->
            <AppLabelTSWrapper :render-object="render.modelAmount" header/>

            <!-- __ Состава изделия -->
            <AppLabelTSWrapper :render-object="render.composition" header/>

            <!-- __ Примечание 1 -->
            <AppLabelTSWrapper :render-object="render.describe_1" header/>

            <!-- __ Примечание 2 -->
            <AppLabelTSWrapper :render-object="render.describe_2" header/>

            <!-- __ Примечание 3 -->
            <AppLabelTSWrapper :render-object="render.describe_3" header/>

            <!--&lt;!&ndash; __ Тип Отходов &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.wasterType" header/>-->

            <!--&lt;!&ndash; __ Тип Загрузки контейнера &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.loadType" header/>-->

            <!--&lt;!&ndash; __ Объем контейнера &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.tankVolume" header/>-->

            <!--&lt;!&ndash; __ Active &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.active" header/>-->

            <!--&lt;!&ndash; __ Количество контейнеров &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.tanksAmount" header/>-->

            <!--&lt;!&ndash; __ График(и) &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.routes" header/>-->

            <!--&lt;!&ndash; __ Частота (периодичность) вывоза &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.generalFrequency" header/>-->

            <!--&lt;!&ndash; __ Актуальность периода вывоза &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.generalRemovalActive" header/>-->

            <!--&lt;!&ndash; __ Начало вывоза &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.generalRemovalStart" header/>-->

            <!--&lt;!&ndash; __ Окончание вывоза &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.generalRemovalEnd" header/>-->

            <!--&lt;!&ndash; __ Актуальность паузы вывоза &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.generalPauseActive" header/>-->

            <!--&lt;!&ndash; __ Начало приостановки вывоза &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.generalPauseStart" header/>-->

            <!--&lt;!&ndash; __ Окончание приостановки вывоза &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.generalPauseEnd" header/>-->

            <!--&lt;!&ndash; __ Месячная логика вывоза (Active) &ndash;&gt;-->
            <!--<AppLabelTSWrapper :render-object="render.generalMonthLogicActive" header/>-->

        </div>
    </div>

    <!-- __ Данные -->
    <div v-for="orderLine of orderLinesRender" :key="orderLine.id">
        <div class="flex ">

            <!-- __ Collapsed -->
            <AppLabelTS
                v-if="render.collapsed.show"
                :width="render.collapsed.width"
                align="center"
                text=""
                text-size="normal"
                type="light"
            />

            <AppLabelTS
                v-if="render.collapsed.show"
                :width="render.collapsed.width"
                align="center"
                text=""
                text-size="normal"
                type="light"
            />

            <!-- __ Collapsed -->
            <!--<AppLabelTSWrapper :arg="orderLine" :render-object="render.collapsed" @click="render.collapsed.click!(orderLine)"/>-->

            <!-- __ Тип изделия -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelType"/>

            <!-- __ Размер -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelSize"/>

            <!-- __ Название -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelName"/>

            <!-- __ Количество -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.modelAmount"/>

            <!-- __ Состава изделия -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.composition"/>

            <!-- __ Примечание 1 -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.describe_1"/>

            <!-- __ Примечание 2 -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.describe_2"/>

            <!-- __ Примечание 3 -->
            <AppLabelTSWrapper :arg="orderLine" :render-object="render.describe_3"/>


            <!--&lt;!&ndash; __ Тип Отходов &ndash;&gt;-->
            <!--<AppLabelTSWrapper :arg="dotLogistic" :render-object="render.wasterType"/>-->

            <!--&lt;!&ndash; __ Тип Загрузки контейнера &ndash;&gt;-->
            <!--<AppLabelTSWrapper :arg="dotLogistic" :render-object="render.loadType"/>-->

            <!--&lt;!&ndash; __ Объем контейнера &ndash;&gt;-->
            <!--<AppLabelTSWrapper :arg="dotLogistic" :render-object="render.tankVolume"/>-->

            <!--&lt;!&ndash; __ Active &ndash;&gt;-->
            <!--<AppLabelTSWrapper :arg="dotLogistic" :render-object="render.active"/>-->

            <!--&lt;!&ndash; __ Количество контейнеров &ndash;&gt;-->
            <!--<AppLabelTSWrapper :arg="dotLogistic" :render-object="render.tanksAmount"/>-->

            <!--&lt;!&ndash; __ График(и) &ndash;&gt;-->
            <!--<AppLabelTSWrapper :arg="dotLogistic" :render-object="render.routes"/>-->

            <!--&lt;!&ndash; __ Частота (периодичность) вывоза &ndash;&gt;-->
            <!--<AppLabelTSWrapper :arg="dotLogistic" :render-object="render.generalFrequency"/>-->

            <!--&lt;!&ndash; __ Актуальность периода вывоза &ndash;&gt;-->
            <!--<AppLabelTSWrapper :arg="dotLogistic" :render-object="render.generalRemovalActive"/>-->

            <!--&lt;!&ndash; __ Начало Вывоза &ndash;&gt;-->
            <!--<AppInputDateTS-->
            <!--    v-if="render.generalRemovalStart.show"-->
            <!--    :id="render.generalRemovalStart.id ? render.generalRemovalStart.id(dotLogistic) : ''"-->
            <!--    v-model="dotLogistic.removal.removal_start"-->
            <!--    :clearable="true"-->
            <!--    :disabled="!canEdit || !dotLogistic.removal.removal_active"-->
            <!--    :width="render.generalRemovalStart.width"-->
            <!--    class="input-date"-->
            <!--/>-->

            <!--&lt;!&ndash; __ Окончание вывоза &ndash;&gt;-->
            <!--<AppInputDateTS-->
            <!--    v-if="render.generalRemovalEnd.show"-->
            <!--    :id="render.generalRemovalEnd.id ? render.generalRemovalEnd.id(dotLogistic) : ''"-->
            <!--    v-model="dotLogistic.removal.removal_end"-->
            <!--    :clearable="true"-->
            <!--    :disabled="!canEdit || !dotLogistic.removal.removal_active"-->
            <!--    :width="render.generalRemovalEnd.width"-->
            <!--    class="input-date"-->
            <!--/>-->

            <!--&lt;!&ndash; __ Актуальность паузы вывоза &ndash;&gt;-->
            <!--<AppLabelTSWrapper :arg="dotLogistic" :render-object="render.generalRemovalActive"/>-->

            <!--&lt;!&ndash; __ Начало приостановки вывоза &ndash;&gt;-->
            <!--<AppInputDateTS-->
            <!--    v-if="render.generalPauseStart.show"-->
            <!--    :id="render.generalPauseStart.id ? render.generalPauseStart.id(dotLogistic) : ''"-->
            <!--    v-model="dotLogistic.removal.removal_pause_start"-->
            <!--    :clearable="true"-->
            <!--    :disabled="!canEdit || !dotLogistic.removal.removal_pause_active"-->
            <!--    :width="render.generalPauseStart.width"-->
            <!--    class="input-date"-->
            <!--/>-->

            <!--&lt;!&ndash; __ Окончание приостановки вывоза &ndash;&gt;-->
            <!--<AppInputDateTS-->
            <!--    v-if="render.generalPauseEnd.show"-->
            <!--    :id="render.generalPauseEnd.id ? render.generalPauseEnd.id(dotLogistic) : ''"-->
            <!--    v-model="dotLogistic.removal.removal_pause_end"-->
            <!--    :clearable="true"-->
            <!--    :disabled="!canEdit || !dotLogistic.removal.removal_pause_active"-->
            <!--    :width="render.generalPauseEnd.width"-->
            <!--    class="input-date"-->
            <!--/>-->

            <!--&lt;!&ndash; __ Месячная логика вывоза (Active) &ndash;&gt;-->
            <!--<AppLabelTSWrapper :arg="dotLogistic" :render-object="render.generalMonthLogicActive"/>-->

        </div>


    </div>

</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue'

import type { IRenderData, IRenderOrderLine } from '@/types'

// import { useClientsStore } from '@/stores/ClientsStore.ts'

import { formatDateIntl } from '@/app/helpers/helpers_date.js'

import AppInputDateTS from '@/components/ui/inputs/AppInputDateTS.vue'
import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { log_FullPath } from '@/app/helpers/helpers.ts'

interface IProps {
    orderLines: IRenderOrderLine[]
}

const props = defineProps<IProps>()


// __ Определяем переменные
// const getDotLogisticRender = (dotLogistic: IDotLogistic[]) => dotLogistic.map((dotLogisticEntity: IDotLogistic) => ({ ...dotLogisticEntity, collapsed: true }))
// const dotLogisticRender = ref<IDotLogistic[]>(getDotLogisticRender(props.dotsLogistic))
const orderLinesRender = ref<IRenderOrderLine[]>(props.orderLines)


// __ Возможность редактирования
// TODO: Реализовать через систему ролей
const canEdit = ref(false)


// __ Объект отображения данных
const DEFAULT_WIDTH = 'w-[100px]'
const DEFAULT_WIDTH_DATE = 'w-[140px]'
const DEFAULT_HEIGHT = 'h-[30px]'
// const DEFAULT_FREQUENCY_TEMPLATE_HEIGHT = DEFAULT_HEIGHT
// const DEFAULT_FREQUENCY_TEMPLATE_HEIGHT = 'h-[20px]'
// const WIDTH = 'w-[300px]'
const HEADER_TYPE = 'indigo'
// const HEADER_TYPE_FREQUENCY_TEMPLATE = 'indigo'
const DATA_TYPE = 'primary'
// const DATA_TYPE_FREQUENCY_TEMPLATE = 'stone'
const DEFAULT_TYPE = 'dark'
const HEADER_TEXT_SIZE = 'micro'
const DATA_TEXT_SIZE = 'micro'
const HEADER_ALIGN = 'center'
const DATA_ALIGN_DEFAULT = 'center'
const DATA_ALIGN = 'left'
// const COORDINATES_PRECISION = 7

const render: IRenderData = reactive({
    collapsed: {
        header: '▲',
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
        data: (orderLine: IRenderOrderLine) => '▲',
        // data: (orderLine: IRenderOrderLine) => orderLine.collapsed ? '▲' : '▼',
        // data: (dotLogistic: IDotLogistic) => console.log(dotLogistic),
        click: (orderLine: IRenderOrderLine) => console.log(orderLine)
        // click: (orderLine: IRenderOrderLine) => dotLogistic.collapsed = !dotLogistic.collapsed
        // click: (dotLogistic: IDotLogistic) => console.log(dotLogistic)
    },


    modelType: {
        header: 'Тип изделия',
        width: 'w-[110px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: 'center',
        dataAlign: 'center',
        data: (orderLine: IRenderOrderLine) => orderLine.model.model_type
    },
    modelSize: {
        header: 'Размер',
        width: 'w-[80px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: 'center',
        dataAlign: 'center',
        data: (orderLine: IRenderOrderLine) => orderLine.size
    },
    modelName: {
        header: 'Модель',
        width: 'w-[200px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: 'center',
        dataAlign: DATA_ALIGN,
        data: (orderLine: IRenderOrderLine) => orderLine.model.name_report
    },
    modelAmount: {
        header: 'Кол-во',
        width: 'w-[50px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: 'center',
        dataAlign: 'center',
        data: (orderLine: IRenderOrderLine) => orderLine.amount.toString()
    },
    composition: {
        header: 'Комментарий',
        width: 'w-[248px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: 'center',
        dataAlign: DATA_ALIGN,
        data: (orderLine: IRenderOrderLine) => orderLine.composition
    },
    describe_1: {
        header: 'Примечание 1',
        width: 'w-[174px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: 'center',
        dataAlign: DATA_ALIGN,
        data: (orderLine: IRenderOrderLine) => orderLine.describe_1
    },
    describe_2: {
        header: 'Примечание 2',
        width: 'w-[175px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: 'center',
        dataAlign: DATA_ALIGN,
        data: (orderLine: IRenderOrderLine) => orderLine.describe_2
    },
    describe_3: {
        header: 'Примечание 3',
        width: 'w-[175px]',
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: 'center',
        dataAlign: DATA_ALIGN,
        data: (orderLine: IRenderOrderLine) => orderLine.describe_3
    },


    wasterType: {
        header: 'Тип отходов',
        width: DEFAULT_WIDTH,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: 'center',
        dataAlign: DATA_ALIGN_DEFAULT,
        data: (dotLogistic: IDotLogistic) => dotLogistic.tank_type.waster_type.display_name
    },
    loadType: {
        header: 'Тип загрузки',
        width: DEFAULT_WIDTH,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN_DEFAULT,
        data: (dotLogistic: IDotLogistic) => dotLogistic.tank_type.load_type.display_name
    },
    tankVolume: {
        header: 'Объем контейнера',
        width: DEFAULT_WIDTH,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN_DEFAULT,
        data: (dotLogistic: IDotLogistic) => dotLogistic.tank_type.tank_volume.display_name
    },
    active: {
        header: 'Действ-щий',
        width: DEFAULT_WIDTH,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (dotLogistic) => dotLogistic.common.active ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN_DEFAULT,
        data: (dotLogistic: IDotLogistic) => dotLogistic.common.active ? '✓' : '✗'
    },

    // __ Общее кол-во контейнеров (Если логика периодичности простая /1 запись/ - показываем ее, иначе - показываем инфу про смешанную частоту)
    tanksAmount: {
        header: 'Кол-во контейнеров',
        width: DEFAULT_WIDTH,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (dotLogistic) => dotLogistic.frequency_entities.length === 0 ? 'danger' : dotLogistic.frequency_entities.length > 1 ? 'warning' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN_DEFAULT,
        data: (dotLogistic: IDotLogistic) => {
            let result = ''
            dotLogistic.frequency_entities.forEach(frequencyEntity => result += `${frequencyEntity.tanks_amount.toString()}/`)
            if (!result) return 'н/д'
            return result.slice(0, -1)
        }

    },
    // __ График(и) вывоза
    routes: {
        header: 'График(и)',
        width: DEFAULT_WIDTH,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (dotLogistic) => 'danger', //DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN_DEFAULT,
        data: (dotLogistic: IDotLogistic) => 'TODO!!!'
    },

    // __ Общая Периодичность /частота/ (Если логика периодичности простая /1 запись/ - показываем ее, иначе - показываем инфу про смешанную частоту)
    generalFrequency: {
        header: 'Периодичность вывоза',
        width: DEFAULT_WIDTH,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (dotLogistic) => dotLogistic.frequency_entities.length === 0 ? 'danger' : dotLogistic.frequency_entities.length > 1 ? 'warning' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (dotLogistic: IDotLogistic) => {
            if (!dotLogistic.frequency_entities) return 'н/д'
            if (dotLogistic.frequency_entities.length > 1) return 'смешанная'
            return dotLogistic.frequency_entities[0].frequency_template.display_name
        }
    },

    // __ Логика периодичности (Актуальность периодов) - Учет приостановки вывоза
    generalRemovalActive: {
        header: 'Учет приостановки',
        width: DEFAULT_WIDTH,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (dotLogistic: IDotLogistic) => dotLogistic.removal.removal_active ? 'danger' : 'success',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (dotLogistic: IDotLogistic) => dotLogistic.removal.removal_active ? '✓' : '✗',
        click: (dotLogistic: IDotLogistic) => {
            if (!canEdit.value) return
            dotLogistic.removal.removal_active = !dotLogistic.removal.removal_active
        }
    },
    // __ Начало вывоза
    generalRemovalStart: {
        id: (dotLogistic: IDotLogistic) => 'general-removal-start-' + dotLogistic.id.toString(),
        header: 'Начало вывоза',
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
        data: (dotLogistic: IDotLogistic) => formatDateIntl(dotLogistic.removal.removal_start)
    },
    // __ Окончание вывоза
    generalRemovalEnd: {
        id: (dotLogistic: IDotLogistic) => 'general-removal-end-' + dotLogistic.id.toString(),
        header: 'Окончание вывоза',
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
        data: (dotLogistic: IDotLogistic) => formatDateIntl(dotLogistic.removal.removal_end)
    },

    // __ Учет паузы приостановки шаблона вывоза
    generalPauseActive: {
        header: 'Учет паузы вывоза',
        width: DEFAULT_WIDTH,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (dotLogistic: IDotLogistic) => dotLogistic.removal.removal_pause_active ? 'danger' : 'success',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (dotLogistic: IDotLogistic) => dotLogistic.removal.removal_pause_active ? '✓' : '✗',
        click: (dotLogistic: IDotLogistic) => {
            if (!canEdit.value) return
            dotLogistic.removal.removal_pause_active = !dotLogistic.removal.removal_pause_active
        }
    },
    // __ Начало приостановки вывоза
    generalPauseStart: {
        id: (dotLogistic: IDotLogistic) => 'general-pause-start-' + dotLogistic.id.toString(),
        header: 'Начало приостановки',
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
        data: (dotLogistic: IDotLogistic) => formatDateIntl(dotLogistic.removal.removal_pause_start)
    },
    // __ Окончание приостановки вывоза
    generalPauseEnd: {
        id: (dotLogistic: IDotLogistic) => 'general-pause-end-' + dotLogistic.id.toString(),
        header: 'Окончание приостановки',
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
        data: (dotLogistic: IDotLogistic) => formatDateIntl(dotLogistic.removal.removal_pause_end)
    },


    // __ Общая логика вывоза по месяцам (checkbox)
    generalMonthLogicActive: {
        header: 'Месячная логика',
        width: DEFAULT_WIDTH,
        height: DEFAULT_HEIGHT,
        show: true,
        headerType: () => HEADER_TYPE,
        dataType: () => DATA_TYPE,
        type: (dotLogistic: IDotLogistic) => dotLogistic.frequency_entities.some(entity => entity.month_logic) ? 'danger' : 'success',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (dotLogistic: IDotLogistic) => dotLogistic.frequency_entities.some(entity => entity.month_logic) ? '✓' : '✗'
    },

})


// __ Collapse/Expand all
const toggleCollapsed = () => {
    // dotLogisticRender.value.forEach(dotLogistic => dotLogistic.collapsed = !dotLogistic.collapsed)
    if (render.collapsed.header === '▲') {
        dotLogisticRender.value.forEach(dotLogistic => dotLogistic.collapsed = false)
        render.collapsed.header = '▼'
    } else {
        dotLogisticRender.value.forEach(dotLogistic => dotLogistic.collapsed = true)
        render.collapsed.header = '▲'
    }
}


</script>

<style scoped>
/*@reference "@css/app.css";*/

.input-date {
    @apply mr-0.5 ml-0.5 mt-[2px]
}
</style>
