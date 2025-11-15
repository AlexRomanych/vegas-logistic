<template>
    <div v-if="!isLoading" class="ml-2 mt-2">

        <div class="sticky top-0 p-1 mb-1 bg-blue-200 border-2 rounded-lg border-blue-700 max-w-fit">

            <div class="flex">

                <div>

                    <div>
                        <!-- __ Пользователь -->
                        <AppLabelMultiLineTS
                            v-if="render.user.show"
                            :align="render.user.headerAlign"
                            :text="render.user.header"
                            :text-size="render.user.headerTextSize"
                            :type="typeof render.user.type === 'function' ? render.user.type() : render.user.type"
                            :width="render.user.width"
                        />

                        <!-- __ Фильтр: Пользователь -->
                        <AppInputTextTS
                            v-if="render.user.show"
                            id="user-search"
                            v-model:text-value.trim="userFilter"
                            :text-size="render.user.filterTextSize"
                            :type="typeof render.user.type === 'function' ? render.user.type() : render.user.type"
                            :width="render.user.width"
                            placeholder="🔍User..."
                        />
                    </div>
                </div>

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
                    <!-- __ Дата лога -->
                    <AppLabelMultiLineTS
                        v-if="render.logAt.show"
                        :align="render.logAt.headerAlign"
                        :text="render.logAt.header"
                        :text-size="render.logAt.headerTextSize"
                        :type="typeof render.logAt.type === 'function' ? render.logAt.type() : render.logAt.type"
                        :width="render.logAt.width"
                    />

                    <!-- __ Фильтр: Дата лога -->
                    <AppInputTextTS
                        v-if="render.logAt.show"
                        id="log-at-search"
                        v-model:text-value.trim="logAtFilter"
                        :text-size="render.logAt.filterTextSize"
                        :type="typeof render.logAt.type === 'function' ? render.logAt.type() : render.logAt.type"
                        :width="render.logAt.width"
                        placeholder="🔍дд.мм.гггг..."
                        @input="handleLogAtInput"
                    />
                </div>

                <div>
                    <!-- __ Номер рулона -->
                    <AppLabelMultiLineTS
                        v-if="render.rollNumber.show"
                        :align="render.rollNumber.headerAlign"
                        :text="render.rollNumber.header"
                        :text-size="render.rollNumber.headerTextSize"
                        :type="typeof render.rollNumber.type === 'function' ? render.rollNumber.type() : render.rollNumber.type"
                        :width="render.rollNumber.width"
                    />

                    <!-- __ Фильтр: Номер рулона -->
                    <AppInputTextTS
                        v-if="render.rollNumber.show"
                        id="roll-number-search"
                        v-model:text-value.trim="rollNumberFilter"
                        :text-size="render.rollNumber.filterTextSize"
                        :type="typeof render.rollNumber.type === 'function' ? render.rollNumber.type() : render.rollNumber.type"
                        :width="render.rollNumber.width"
                        placeholder="🔍№ рул..."
                    />
                </div>

                <div>
                    <!-- __ Событие -->
                    <AppLabelMultiLineTS
                        v-if="render.event.show"
                        :align="render.event.headerAlign"
                        :text="render.event.header"
                        :text-size="render.event.headerTextSize"
                        :type="typeof render.event.type === 'function' ? render.event.type() : render.event.type"
                        :width="render.event.width"
                    />

                    <!-- __ Фильтр: Событие -->
                    <AppInputTextTS
                        v-if="render.event.show"
                        id="event-search"
                        v-model:text-value.trim="eventFilter"
                        :text-size="render.event.filterTextSize"
                        :type="typeof render.event.type === 'function' ? render.event.type() : render.event.type"
                        :width="render.event.width"
                        placeholder="🔍Событие..."
                    />
                </div>

                <div>
                    <!-- __ Статус до -->
                    <AppLabelMultiLineTS
                        v-if="render.statusBefore.show"
                        :align="render.statusBefore.headerAlign"
                        :text="render.statusBefore.header"
                        :text-size="render.statusBefore.headerTextSize"
                        :type="typeof render.statusBefore.type === 'function' ? render.statusBefore.type() : render.statusBefore.type"
                        :width="render.statusBefore.width"
                    />

                    <!-- __ Фильтр: Статус до -->
                    <AppSelectSimpleTS
                        v-if="render.statusBefore.show"
                        :select-data="statusBeforeSelect"
                        :text-size="render.statusBefore.headerTextSize"
                        :type="getBeforeStatusType()"
                        :width="render.statusBefore.width"
                        align="center"
                        class="mt-[7px]"
                        height="h-[26px]"
                        @change="filterByStatusBefore"
                    />

                </div>

                <div>
                    <!-- __ Статус после -->
                    <AppLabelMultiLineTS
                        v-if="render.statusAfter.show"
                        :align="render.statusAfter.headerAlign"
                        :text="render.statusAfter.header"
                        :text-size="render.statusAfter.headerTextSize"
                        :type="typeof render.statusAfter.type === 'function' ? render.statusAfter.type() : render.statusAfter.type"
                        :width="render.statusAfter.width"
                    />

                    <!-- __ Фильтр: Статус после -->
                    <AppSelectSimpleTS
                        v-if="render.statusAfter.show"
                        :select-data="statusAfterSelect"
                        :text-size="render.statusAfter.headerTextSize"
                        :type="getAfterStatusType()"
                        :width="render.statusAfter.width"
                        align="center"
                        class="mt-[7px]"
                        height="h-[26px]"
                        @change="filterByStatusAfter"
                    />
                </div>

                <div>
                    <!-- __ Ответственный -->
                    <AppLabelMultiLineTS
                        v-if="render.responsible.show"
                        :align="render.responsible.headerAlign"
                        :text="render.responsible.header"
                        :text-size="render.responsible.headerTextSize"
                        :type="typeof render.responsible.type === 'function' ? render.responsible.type() : render.responsible.type"
                        :width="render.responsible.width"
                    />

                    <!-- __ Фильтр: Ответственный -->
                    <AppInputTextTS
                        v-if="render.responsible.show"
                        id="responsible-search"
                        v-model:text-value.trim="responsibleFilter"
                        :text-size="render.responsible.filterTextSize"
                        :type="typeof render.responsible.type === 'function' ? render.responsible.type() : render.responsible.type"
                        :width="render.responsible.width"
                        placeholder="🔍Ответст..."
                    />
                </div>

                <div>
                    <!-- __ Причина -->
                    <AppLabelMultiLineTS
                        v-if="render.reason.show"
                        :align="render.reason.headerAlign"
                        :text="render.reason.header"
                        :text-size="render.reason.headerTextSize"
                        :type="typeof render.reason.type === 'function' ? render.reason.type() : render.reason.type"
                        :width="render.reason.width"
                    />

                    <!-- __ Фильтр: Причина -->
                    <AppInputTextTS
                        v-if="render.reason.show"
                        id="reason-search"
                        v-model:text-value.trim="reasonFilter"
                        :text-size="render.reason.filterTextSize"
                        :type="typeof render.reason.type === 'function' ? render.reason.type() : render.reason.type"
                        :width="render.reason.width"
                        placeholder="🔍Причина..."
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
                    <!-- __ ip адрес -->
                    <AppLabelMultiLineTS
                        v-if="render.ip.show"
                        :align="render.ip.headerAlign"
                        :text="render.ip.header"
                        :text-size="render.ip.headerTextSize"
                        :type="typeof render.ip.type === 'function' ? render.ip.type() : render.ip.type"
                        :width="render.ip.width"
                    />

                    <!-- __ Фильтр: ip адрес -->
                    <AppInputTextTS
                        v-if="render.ip.show"
                        id="ip-search"
                        v-model:text-value.trim="ipFilter"
                        :text-size="render.ip.filterTextSize"
                        :type="typeof render.ip.type === 'function' ? render.ip.type() : render.ip.type"
                        :width="render.ip.width"
                        placeholder="🔍ip..."
                    />
                </div>
            </div>

        </div>

        <!-- __ Сами данные -->
        <div v-if="logsRender.length">
            <div class="pt-1 pb-1 bg-slate-200 border-2 rounded-lg border-slate-700 p-1 mb-1 max-w-fit">
                <div v-for="log in logsRender" :key="log.id">

                    <div class="flex">

                        <!-- __ Пользователь -->
                        <AppLabelTS
                            v-if="render.user.show"
                            :align="render.user.dataAlign"
                            :text="render.user.data ? render.user.data(log) : ''"
                            :text-size="render.user.dataTextSize"
                            :type="typeof render.user.type === 'function' ? render.user.type(log, false) : render.user.type"
                            :width="render.user.width"
                        />

                        <!-- __ id -->
                        <AppLabelTS
                            v-if="render.id.show"
                            :align="render.id.dataAlign"
                            :text="render.id.data ? render.id.data(log) : ''"
                            :text-size="render.id.dataTextSize"
                            :type="typeof render.id.type === 'function' ? render.id.type(log, false) : render.id.type"
                            :width="render.id.width"
                        />

                        <!-- __ Дата Лога -->
                        <AppLabelTS
                            v-if="render.logAt.show"
                            :align="render.logAt.dataAlign"
                            :text="render.logAt.data ? render.logAt.data(log) : ''"
                            :text-size="render.logAt.dataTextSize"
                            :type="typeof render.logAt.type === 'function' ? render.logAt.type(log, false) : render.logAt.type"
                            :width="render.logAt.width"
                        />

                        <!-- __ Номер рулона -->
                        <AppLabelTS
                            v-if="render.rollNumber.show"
                            :align="render.rollNumber.dataAlign"
                            :text="render.rollNumber.data ? render.rollNumber.data(log) : ''"
                            :text-size="render.rollNumber.dataTextSize"
                            :type="typeof render.rollNumber.type === 'function' ? render.rollNumber.type(log, false) : render.rollNumber.type"
                            :width="render.rollNumber.width"
                        />

                        <!-- __ Событие -->
                        <AppLabelTS
                            v-if="render.event.show"
                            :align="render.event.dataAlign"
                            :text="render.event.data ? render.event.data(log) : ''"
                            :text-size="render.event.dataTextSize"
                            :type="typeof render.event.type === 'function' ? render.event.type(log, false) : render.event.type"
                            :width="render.event.width"
                        />

                        <!-- __ Статус до -->
                        <AppLabelTS
                            v-if="render.statusBefore.show"
                            :align="render.statusBefore.dataAlign"
                            :text="render.statusBefore.data ? render.statusBefore.data(log) : ''"
                            :text-size="render.statusBefore.dataTextSize"
                            :type="typeof render.statusBefore.type === 'function' ? render.statusBefore.type(log, false) : render.statusBefore.type"
                            :width="render.statusBefore.width"
                        />

                        <!-- __ Статус после -->
                        <AppLabelTS
                            v-if="render.statusAfter.show"
                            :align="render.statusAfter.dataAlign"
                            :text="render.statusAfter.data ? render.statusAfter.data(log) : ''"
                            :text-size="render.statusAfter.dataTextSize"
                            :type="typeof render.statusAfter.type === 'function' ? render.statusAfter.type(log, false) : render.statusAfter.type"
                            :width="render.statusAfter.width"
                        />

                        <!-- __ Ответственный -->
                        <AppLabelTS
                            v-if="render.responsible.show"
                            :align="render.responsible.dataAlign"
                            :text="render.responsible.data ? render.responsible.data(log) : ''"
                            :text-size="render.responsible.dataTextSize"
                            :type="typeof render.responsible.type === 'function' ? render.responsible.type(log, false) : render.responsible.type"
                            :width="render.responsible.width"
                        />

                        <!-- __ Причина -->
                        <AppLabelTS
                            v-if="render.reason.show"
                            :align="render.reason.dataAlign"
                            :text="render.reason.data ? render.reason.data(log) : ''"
                            :text-size="render.reason.dataTextSize"
                            :type="typeof render.reason.type === 'function' ? render.reason.type(log, false) : render.reason.type"
                            :width="render.reason.width"
                        />

                        <!-- __ Описание -->
                        <AppLabelTS
                            v-if="render.description.show"
                            :align="render.description.dataAlign"
                            :text="render.description.data ? render.description.data(log) : ''"
                            :text-size="render.description.dataTextSize"
                            :type="typeof render.description.type === 'function' ? render.description.type(log, false) : render.description.type"
                            :width="render.description.width"
                        />

                        <!-- __ ip -->
                        <AppLabelTS
                            v-if="render.ip.show"
                            :align="render.ip.dataAlign"
                            :text="render.ip.data ? render.ip.data(log) : ''"
                            :text-size="render.ip.dataTextSize"
                            :type="typeof render.ip.type === 'function' ? render.ip.type(log, false) : render.ip.type"
                            :width="render.ip.width"
                        />

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

</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, watchEffect } from 'vue'

import type { IFabricTaskRollLog, IPeriod, IRenderData, ISelectData, ISelectDataItem } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

import { useLogsStore } from '@/stores/LogsStore.ts'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'


// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'

import {
    formatDateAndTimeInShortFormat,
    getDateFromDateTimeString,
    validateInputDateHelper
} from '@/app/helpers/helpers_date'

import { getRollExecStatusByCode } from '@/app/helpers/manufacture/helpers_fabric'
import { getFormatFIO } from '@/app/helpers/workers/helpers_workers'
import { FABRIC_ROLL_STATUS } from '@/app/constants/fabrics.ts'


const isLoading = ref(true)

const logsStore = useLogsStore()

// __ Подготавливаем переменные
const logs = ref<IFabricTaskRollLog[]>([])
const logsRender = ref<IFabricTaskRollLog[]>([])

// __ Фильтры
const userFilter = ref('')
const idFilter = ref('')
const rollNumberFilter = ref('')
const logAtFilter = ref('')
const eventFilter = ref('')
const reasonFilter = ref('')
const responsibleFilter = ref('')
const descriptionFilter = ref('')
const ipFilter = ref('')
const statusBeforeFilter = ref(-100)
const statusAfterFilter = ref(-100)

// __ Задаем умолчания
const DEFAULT_HEADER_TYPE = 'primary'
const HEADER_ALIGN = 'center'
const DATA_ALIGN = 'left'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE = 'micro'
const FILTER_TEXT_SIZE = 'mini'

const getType = (log: IFabricTaskRollLog | null, isHeader: boolean = true, renderRollStatuses: boolean = false, statusField: string = 'status_before'): IColorTypes => {
    if (isHeader) return DEFAULT_HEADER_TYPE
    if (renderRollStatuses) {
        if (!log || !log.hasOwnProperty(statusField)) return 'dark'
        return getRollExecStatusByCode(log[statusField as keyof IFabricTaskRollLog] as number).TYPE
    }
    return 'dark'
}

// __ Подготавливаем рендер
const render: IRenderData = reactive({
    user: {
        header: ['Пользователь', ''],
        width: 'w-[100px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (log) => getFormatFIO(log.user),
    },
    id: {
        header: ['id', ''],
        width: 'w-[50px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (log) => log.id.toString(),
    },
    rollNumber: {
        header: ['Номер', 'рулона'],
        width: 'w-[70px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (log) => log.fabric_task_roll.id.toString(),
    },
    logAt: {
        header: ['Дата и', 'время лога'],
        width: 'w-[100px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (log) => formatDateAndTimeInShortFormat(log.log_at, false),
    },
    event: {
        header: ['Событие', ''],
        width: 'w-[250px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (log) => log.event,
    },
    statusBefore: {
        header: ['Статус', 'до'],
        width: 'w-[100px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader, true, 'status_before'),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (log) => getRollExecStatusByCode(log.status_before)?.TITLE as string,
    },
    statusAfter: {
        header: ['Статус', 'после'],
        width: 'w-[100px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader, true, 'status_after'),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (log) => getRollExecStatusByCode(log.status_after)?.TITLE as string,
    },
    responsible: {
        header: ['Ответственный', ''],
        width: 'w-[100px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (log) => getFormatFIO(log.responsible),
    },
    reason: {
        header: ['Причина', ''],
        width: 'w-[300px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (log) => log.reason,
    },
    description: {
        header: ['Описание', ''],
        width: 'w-[300px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        data: (log) => log.description,
    },
    ip: {
        header: ['ip', ''],
        width: 'w-[80px]',
        show: true,
        type: (log = null, isHeader: boolean = true) => getType(log, isHeader),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        filterTextSize: FILTER_TEXT_SIZE,
        headerAlign: HEADER_ALIGN,
        dataAlign: 'center',
        data: (log) => log.ip,
    },
})


// __ Получаем список логов
const period: IPeriod = {start: '2024-01-01', end: '2024-12-31'}
const getLogs = async () => {
    const tempLogs: IFabricTaskRollLog[] = await logsStore.getLogsFabricsExecuteRollsByPeriod(/*period*/)
    logs.value = tempLogs
        .map(log => {
            log.description = log.description ?? ''
            log.reason = log.reason ?? ''
            log.ip = log.ip ?? ''
            log.log_at_date = new Date(log.log_at)
            return log
        })
        .sort((a, b) => a.log_at_date!.getTime() - b.log_at_date!.getTime())
}

// __ Получаем рендер
const getLogsRender = () => logsRender.value = logs.value


// __ Обработчик ввода даты лога
const handleLogAtInputObj = {   // Объект для манипуляции с вводом и выводом даты
    newValue: '',
    oldValue: '',
}
const handleLogAtInput = (event: Event) => {
    const target = event.target as HTMLInputElement
    handleLogAtInputObj.newValue = target.value
    validateInputDateHelper(handleLogAtInputObj)  // вся логика изменения объекта будет внутри функции
    logAtFilter.value = handleLogAtInputObj.newValue
}

// __ Подготавливаем селекты
const statusBeforeSelect: ISelectData = {
    name: 'status_before',
    data: [
        {id: -100, name: 'Все', selected: true, disabled: false},
        {id: FABRIC_ROLL_STATUS.UNKNOWN.CODE, name: FABRIC_ROLL_STATUS.UNKNOWN.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.CREATED.CODE, name: FABRIC_ROLL_STATUS.CREATED.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.RUNNING.CODE, name: FABRIC_ROLL_STATUS.RUNNING.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.PAUSED.CODE, name: FABRIC_ROLL_STATUS.PAUSED.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.DONE.CODE, name: FABRIC_ROLL_STATUS.DONE.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.FALSE.CODE, name: FABRIC_ROLL_STATUS.FALSE.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.MOVED.CODE, name: FABRIC_ROLL_STATUS.MOVED.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.CANCELLED.CODE, name: FABRIC_ROLL_STATUS.CANCELLED.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.CLOSED.CODE, name: FABRIC_ROLL_STATUS.CLOSED.TITLE, selected: false, disabled: false},
    ],
}

const statusAfterSelect: ISelectData = {
    name: 'status_after',
    data: [
        {id: -100, name: 'Все', selected: true, disabled: false},
        // {id: FABRIC_ROLL_STATUS.UNKNOWN.CODE, name: FABRIC_ROLL_STATUS.UNKNOWN.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.CREATED.CODE, name: FABRIC_ROLL_STATUS.CREATED.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.RUNNING.CODE, name: FABRIC_ROLL_STATUS.RUNNING.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.PAUSED.CODE, name: FABRIC_ROLL_STATUS.PAUSED.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.DONE.CODE, name: FABRIC_ROLL_STATUS.DONE.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.FALSE.CODE, name: FABRIC_ROLL_STATUS.FALSE.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.MOVED.CODE, name: FABRIC_ROLL_STATUS.MOVED.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.CANCELLED.CODE, name: FABRIC_ROLL_STATUS.CANCELLED.TITLE, selected: false, disabled: false},
        {id: FABRIC_ROLL_STATUS.CLOSED.CODE, name: FABRIC_ROLL_STATUS.CLOSED.TITLE, selected: false, disabled: false},
    ],
}

// __ Обрабатываем селекты
const filterByStatusBefore = (value: ISelectDataItem) => {
    statusBeforeFilter.value = value.id
}

const filterByStatusAfter = (value: ISelectDataItem) => {
    statusAfterFilter.value = value.id
}

// __ Получаем тип статуса (раскраски) для фильтра
const getBeforeStatusType = () => {
    if (statusBeforeFilter.value === -100) {
        return 'primary'
    }
    return getRollExecStatusByCode(statusBeforeFilter.value)?.TYPE as string
}

const getAfterStatusType = () => {
    if (statusAfterFilter.value === -100) {
        return 'primary'
    }
    return getRollExecStatusByCode(statusAfterFilter.value)?.TYPE as string
}

// __ Обнуляем фильтры
const resetFilters = () => {
    userFilter.value = ''
    idFilter.value = ''
    rollNumberFilter.value = ''
    logAtFilter.value = ''
    eventFilter.value = ''
    reasonFilter.value = ''
    responsibleFilter.value = ''
    descriptionFilter.value = ''
    ipFilter.value = ''
    statusBeforeFilter.value = -100
    statusAfterFilter.value = -100
}

// __ Реализация фильтров
watchEffect(() => {
    logsRender.value = logs.value
        .filter(log => log.id.toString().includes(idFilter.value.toLowerCase()))
        .filter(log => getFormatFIO(log.user).toLowerCase().includes(userFilter.value.toLowerCase()))
        .filter(log => getDateFromDateTimeString(log.log_at).includes(logAtFilter.value))
        .filter(log => log.fabric_task_roll.id.toString().includes(rollNumberFilter.value.toLowerCase()))
        .filter(log => log.event.includes(eventFilter.value.toLowerCase()))
        .filter(log => getFormatFIO(log.responsible).toLowerCase().includes(responsibleFilter.value.toLowerCase()))
        .filter(log => log.reason?.includes(reasonFilter.value.toLowerCase()))
        .filter(log => log.description?.includes(descriptionFilter.value.toLowerCase()))
        .filter(log => log.ip?.includes(ipFilter.value.toLowerCase()))
        .filter(log => {
            if (statusBeforeFilter.value === -100) return true
            return log.status_before === statusBeforeFilter.value
        })
        .filter(log => {
            if (statusAfterFilter.value === -100) return true
            return log.status_after === statusAfterFilter.value
        })
})


onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getLogs()             // Получаем список логов
            getLogsRender()

            console.log('logsRender:', logsRender.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})
</script>

<style scoped>

</style>
