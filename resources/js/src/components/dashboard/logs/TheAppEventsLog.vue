<template>
    <div v-if="!isLoading" class="ml-2 mt-2">
        <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit">
            <div>
                <div class="flex ml-0.5">

                    <!-- __ id -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.id"/>
                        <AppInputTextTSWrapper v-model="idFilter" :render-object="render.id"/>
                    </div>

                    <!-- __ Дата и время -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.created_at"/>
                        <AppInputTextTSWrapper v-model="createdAtFilter" :render-object="render.created_at"/>
                    </div>

                    <!-- __ Тип сообщения -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.level"/>
                        <AppInputTextTSWrapper v-model="levelFilter" :render-object="render.level"/>
                    </div>

                    <!-- __ Модуль -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.target"/>
                        <AppInputTextTSWrapper v-model="targetFilter" :render-object="render.target"/>
                    </div>

                    <!-- __ Сообщение -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.message"/>
                        <AppInputTextTSWrapper v-model="messageFilter" :render-object="render.message"/>
                    </div>

                    <!-- __ Контекст -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.context"/>
                        <AppInputTextTSWrapper v-model="contextFilter" :render-object="render.context"/>
                    </div>

                    <!-- __ Выбор дат -->
                    <CellDatesSelectMiniTS
                        :period="renderPeriod"
                        @apply="loadEventLogs"
                    />

                </div>
            </div>
        </div>

        <!-- __ Данные -->
        <div v-for="eventLog of entitiesRender" :key="eventLog.id" class="ml-2">
            <div class="flex" @click="showContext(eventLog)">
                <!-- __ ID -->
                <AppLabelTSWrapper :arg="eventLog" :render-object="render.id"/>

                <!-- __ Дата и Время -->
                <AppLabelTSWrapper :arg="eventLog" :render-object="render.created_at"/>

                <!-- __ Уровень -->
                <AppLabelTSWrapper :arg="eventLog" :render-object="render.level"/>

                <!-- __ Модуль -->
                <AppLabelTSWrapper :arg="eventLog" :render-object="render.target"/>

                <!-- __ Сообщение -->
                <AppLabelTSWrapper :arg="eventLog" :render-object="render.message"/>

                <!-- __ Контекст -->
                <AppLabelTSWrapper :arg="eventLog" :render-object="render.context"/>

            </div>
        </div>
    </div>

    <!-- __ Модальное окно для информации о Контексте __ -->
    <LogContextInfo
        ref="logContextInfo"
        :event-log="eventLogInfo"
    />

</template>

<script lang="ts" setup>
import type { IEventLog, IPeriod, IRenderData } from '@/types'

import { onMounted, reactive, ref, watchEffect } from 'vue'

import { useLogsStore } from '@/stores/LogsStore.ts'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import { formatDateAndTimeInShortFormat } from '@/app/helpers/helpers_date'
import { getEventLogObjectByLevel, getModuleTitle } from '@/app/helpers/helpers_logs.ts'

import AppLabelMultilineTSWrapper from '@/components/dashboard/logs/components/AppLabelMultilineTSWrapper.vue'
import AppInputTextTSWrapper from '@/components/dashboard/logs/components/AppInputTextTSWrapper.vue'
import CellDatesSelectMiniTS from '@/components/dashboard/logs/components/CellDatesSelectMiniTS.vue'
import LogContextInfo from '@/components/dashboard/logs/components/LogContextInfo.vue'
import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'


const logsStore = useLogsStore()

const isLoading = ref(false)

// __ Определяем переменные
let entities: IEventLog[] = []
const entitiesRender      = ref<IEventLog[]>([])
const renderPeriod        = ref<IPeriod | null>(null)

// __ Фильтры
const idFilter        = ref('')
const createdAtFilter = ref('')
const levelFilter     = ref('')
const targetFilter    = ref('')
const messageFilter   = ref('')
const contextFilter   = ref('')

// __ Объект отображения данных
const DEFAULT_HEIGHT     = 'h-[30px]'
const HEADER_TYPE        = 'primary'
const DATA_TYPE          = 'primary'
const DEFAULT_TYPE       = 'dark'
const HEADER_TEXT_SIZE   = 'mini'
const DATA_TEXT_SIZE     = 'mini'
const HEADER_ALIGN       = 'center'
const DATA_ALIGN         = 'left'
const DATA_ALIGN_DEFAULT = 'center'

// __ Тип раскрашки
const getType = (event: IEventLog) => {
    if (!event) return DEFAULT_TYPE
    const eventObj = getEventLogObjectByLevel(event.level)
    return eventObj ? eventObj.TYPE : DEFAULT_TYPE
}

// __ Уровень
const getLevel = (event: IEventLog) => {
    const eventObj = getEventLogObjectByLevel(event.level)
    return eventObj ? eventObj.TITLE : ''
}


const render: IRenderData = reactive({
    id        : {
        id            : () => 'id-search',
        header        : ['ID', ''],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (event: IEventLog) => getType(event),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN_DEFAULT,
        placeholder   : '🔍id...',
        data          : (event: IEventLog) => event.id.toString(),
    },
    level     : {
        id            : () => 'level-search',
        header        : ['Тип', 'записи'],
        width         : 'w-[150px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (event: IEventLog) => getType(event),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN_DEFAULT,
        placeholder   : '🔍Тип...',
        data          : (event: IEventLog) => getLevel(event),
    },
    created_at: {
        id            : () => 'created-at-search',
        header        : ['Дата и', 'время'],
        width         : 'w-[150px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (event: IEventLog) => getType(event),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN_DEFAULT,
        placeholder   : '🔍Дата...',
        data          : (event: IEventLog) => formatDateAndTimeInShortFormat(event.created_at),
    },
    target    : {
        id            : () => 'target-search',
        header        : ['Модуль', ''],
        width         : 'w-[150px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (event: IEventLog) => getType(event),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN_DEFAULT,
        placeholder   : '🔍Модуль...',
        data          : (event: IEventLog) => getModuleTitle(event.target),
    },
    message   : {
        id            : () => 'message-search',
        header        : ['Текст сообщения', ''],
        width         : 'w-[300px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (event: IEventLog) => getType(event),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN_DEFAULT,
        placeholder   : '🔍Сообщение...',
        data          : (event: IEventLog) => event.message,
    },
    context   : {
        id            : () => 'context-search',
        header        : ['Контекст', ''],
        width         : 'w-[600px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (event: IEventLog) => getType(event),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Контекст...',
        data          : (event: IEventLog) => JSON.stringify(event.context),
    },
})

// __ Тип для модального окна информации о записи в Логе
const eventLogInfo   = ref<IEventLog | null>(null)
const logContextInfo = ref<InstanceType<typeof LogContextInfo> | null>(null)

// __ Показать Контекст
const showContext = async (eventLog: IEventLog) => {
    if (eventLog.context) {
        eventLogInfo.value = eventLog
        await logContextInfo.value!.show() // показываем модалку и ждем ответ
    }
}

// __ Получаем Логи за Период
const getEntities = async () => {
    entities = await logsStore.getLogsAppEvents(renderPeriod.value)
}

// __ Создаем объект Рендера
const getEntitiesRender = () => {
    entitiesRender.value = entities
}

// __ Загрузка Заявок
const loadEventLogs = async (period: IPeriod | null = null) => {
    renderPeriod.value = period

    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getEntities()
            console.log('EventLogs: ', entities)

            getEntitiesRender()

        },
        undefined,
        // false,
    )

    isLoading.value = false
}

watchEffect(() => {
    const idFilterSearch        = idFilter.value.toLowerCase()
    const createdAtFilterSearch = createdAtFilter.value.toLowerCase()
    const levelFilterSearch     = levelFilter.value.toLowerCase()
    const targetFilterSearch    = targetFilter.value.toLowerCase()
    const messageFilterSearch   = messageFilter.value.toLowerCase()
    const contextFilterSearch   = contextFilter.value.toLowerCase()

    entitiesRender.value = entities
        .filter(eventLog => eventLog.id.toString().includes(idFilterSearch))
        .filter(eventLog => formatDateAndTimeInShortFormat(eventLog.created_at).includes(createdAtFilterSearch))
        .filter(eventLog => getLevel(eventLog).toLowerCase().includes(levelFilterSearch))
        .filter(eventLog => getModuleTitle(eventLog.target).toLowerCase().includes(targetFilterSearch))
        .filter(eventLog => eventLog.message.toLowerCase().includes(messageFilterSearch))
        .filter(eventLog => JSON.stringify(eventLog.context).toLowerCase().includes(contextFilterSearch))
})

onMounted(async () => await loadEventLogs())

</script>

<style scoped>

</style>
