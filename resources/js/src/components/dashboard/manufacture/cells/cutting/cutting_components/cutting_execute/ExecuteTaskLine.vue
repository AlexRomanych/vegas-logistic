<template>

    <div class="flex">

        <!-- __ Checker -->
        <AppLabelTSWrapper :render-object="render.checker"/>

        <!-- __ id -->
        <AppLabelTSWrapper :render-object="render.id"/>

        <!-- __ position -->
        <AppLabelTSWrapper :render-object="render.position"/>

        <!-- __ Размер  -->
        <AppLabelTSWrapper :render-object="render.size"/>

        <!-- __ Модель (Чехол) -->
        <AppLabelTSWrapper :render-object="render.cover"/>

        <!-- __ Количество -->
        <AppLabelTSWrapper :render-object="render.amount"/>

        <!-- __ Трудозатраты -->
        <AppLabelTSWrapper :render-object="render.time"/>

        <!-- __ Стол 1 -->
        <AppLabelTSWrapper :render-object="render.table_1"/>

        <!-- __ Стол 2 -->
        <AppLabelTSWrapper :render-object="render.table_2"/>

        <!-- __ Стол 3 -->
        <AppLabelTSWrapper :render-object="render.table_3"/>

        <!-- __ Стол ?? -->
        <AppLabelTSWrapper :render-object="render.table_0"/>

        <!-- __ Деталька -->
        <AppLabelTSWrapper :render-object="render.detail"/>

        <!-- __ ШМ -->
        <AppLabelTSWrapper :render-object="render.machine"/>

        <!-- __ Ткань -->
        <AppLabelTSWrapper :render-object="render.textile"/>

        <!-- __ КДЧ -->
        <AppLabelTSWrapper :render-object="render.kdch"/>

        <!-- __ Крой -->
        <AppLabelTSWrapper :render-object="render.cut"/>

        <!-- __ Угол -->
        <AppLabelTSWrapper :render-object="render.angle"/>

        <!-- __ Настилы -->
        <AppLabelTSWrapper :render-object="render.layers"/>

        <!-- __ Расход -->
        <AppLabelTSWrapper :render-object="render.expense"/>

        <!-- __ Рулон ПС -->
        <AppLabelTSWrapper :render-object="render.fabric_roll"/>

        <!-- __ Отметка Рулона ПС -->
        <AppLabelTSWrapper :render-object="render.fabric_roll_at"/>

        <!-- __ Брак -->
        <AppLabelTSWrapper :render-object="render.defects"/>

        <!-- __ Причина Брака -->
        <AppLabelTSWrapper :render-object="render.defects_reason"/>

        <!-- __ ТКЧ -->
        <AppLabelTSWrapper :render-object="render.tkch"/>

        <!-- __ Кант -->
        <AppLabelTSWrapper :render-object="render.kant"/>

        <!-- __ Состав -->
        <AppLabelTSWrapper :render-object="render.composition"/>

        <!-- __ Примечание 1 -->
        <AppLabelTSWrapper :render-object="render.describe_1"/>

        <!-- __ Примечание 2 -->
        <AppLabelTSWrapper :render-object="render.describe_2"/>

        <!-- __ Примечание 3 -->
        <AppLabelTSWrapper :render-object="render.describe_3"/>

        <!-- __ Время выполнения -->
        <AppLabelTSWrapper :render-object="render.finished_at"/>

        <!-- __ Причина невыполнения -->
        <AppLabelTSWrapper :render-object="render.false_reason"/>

    </div>


</template>

<script lang="ts" setup>
import { computed, reactive } from 'vue'

import type { IColorTypes, IRenderData, ICuttingTaskLine, ICuttingTableKeys } from '@/types'

import {
    getCoverSizeString,
    getCuttingTaskModelCoverName, getDetailTitle, getDetailType,
    getTimeString
} from '@/app/helpers/manufacture/helpers_cutting.ts'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'
import { CUTTING_TABLES } from '@/app/constants/cutting.ts'
import { getKDCH, getSewingMachineTitle } from '@/app/helpers/manufacture/helpers_textile.ts'
// import AppLabelMultilineTSWrapper
//     from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'


interface IProps {
    cuttingLine: ICuttingTaskLine
    fieldsWidth: Record<string, string>
}

const props = defineProps<IProps>()

// __ Объект отображения данных
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const COLLAPSED_WIDTH    = 'w-[30px]'
const DEFAULT_HEIGHT   = 'h-[20px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const ACCENT_TYPE      = 'success'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'

const render: IRenderData = reactive({
    checker       : {
        id            : () => 'id-checker',
        header        : ['', ''],
        width         : props.fieldsWidth.checker,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍?...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.finished_at ? '✓' : '✗'
    },
    id            : {
        id            : () => 'id-search',
        header        : ['', ''],
        width         : props.fieldsWidth.id,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.id.toString()
    },
    position      : {
        id            : () => 'position-search',
        header        : ['', ''],
        width         : props.fieldsWidth.position,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍№ п/п...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.position.toString(),
    },
    size          : {
        id            : () => 'size-search',
        header        : ['', ''],
        width         : props.fieldsWidth.size,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Размер...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => getSize.value
    },
    cover         : {
        id            : () => 'cover-search',
        header        : ['', ''],
        width         : props.fieldsWidth.cover,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Чехол...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => getCuttingTaskModelCoverName(props.cuttingLine, false)
    },
    amount        : {
        id            : () => 'amount-search',
        header        : ['', ''],
        width         : props.fieldsWidth.amount,
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
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.amount.toString()
    },
    time          : {
        id            : () => 'time-search',
        header        : ['', ''],
        width         : props.fieldsWidth.time,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => getTime.value === '00с' ? 'danger' : getType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Время...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => getTime.value
    },
    detail        : {
        id            : () => 'detail-search',
        header        : ['', ''],
        width         : props.fieldsWidth.detail,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => getDetailType(props.cuttingLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Элемент...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => getDetailTitle(props.cuttingLine, false, true)
    },
    table_1       : {
        id            : () => 'table-1-search',
        header        : ['', ''],
        width         : props.fieldsWidth.table,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => getTypeForTable(CUTTING_TABLES.TABLE_1),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Стол 1...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine?.table === CUTTING_TABLES.TABLE_1 ? '1' : '',
    },
    table_2       : {
        id            : () => 'table-2-search',
        header        : ['', ''],
        width         : props.fieldsWidth.table,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => getTypeForTable(CUTTING_TABLES.TABLE_2),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Стол 2...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine?.table === CUTTING_TABLES.TABLE_2 ? '2' : '',
    },
    table_3       : {
        id            : () => 'table-3-search',
        header        : ['', ''],
        width         : props.fieldsWidth.table,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => getTypeForTable(CUTTING_TABLES.TABLE_3),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Стол 3...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine?.table === CUTTING_TABLES.TABLE_3 ? '3' : '',
    },
    table_0       : {
        id            : () => 'table-0-search',
        header        : ['', ''],
        width         : props.fieldsWidth.table,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => getTypeForTable(CUTTING_TABLES.TABLE_0),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Стол ??...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine?.table === CUTTING_TABLES.TABLE_0 ? '??' : '',
    },
    machine       : {
        id            : () => 'machine-search',
        header        : ['', ''],
        width         : props.fieldsWidth.machine,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍ШМ ??...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => getSewingMachineTitle(props.cuttingLine)
    },
    kdch          : {
        id            : () => 'kdch-search',
        header        : ['', ''],
        width         : props.fieldsWidth.kdch,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍КДЧ...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => getKDCH(props.cuttingLine)
    },
    tkch          : {
        id            : () => 'tkch-search',
        header        : ['', ''],
        width         : props.fieldsWidth.tkch,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍ТКЧ...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.order_line.model.main.tkch ?? ''
    },
    kant          : {
        id            : () => 'kant-search',
        header        : ['', ''],
        width         : props.fieldsWidth.kant,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Кант...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.order_line.model.main.kant ?? ''
    },
    textile       : {
        id            : () => 'textile-search',
        header        : ['', ''],
        width         : props.fieldsWidth.textile,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Ткань...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.fabric_construct?.[0],
        // data:           (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.order_line.textile ?? ''
    },
    cut           : {
        id            : () => 'cut-search',
        header        : ['', ''],
        width         : props.fieldsWidth.cut,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => props.cuttingLine.cut_width * props.cuttingLine.cut_length !== 0 ? DEFAULT_TYPE : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Крой...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.cut_width * props.cuttingLine.cut_length !== 0 ? `${props.cuttingLine.cut_width}x${props.cuttingLine.cut_length}` : '??',
    },
    angle         : {
        id            : () => 'angle-search',
        header        : ['', ''],
        width         : props.fieldsWidth.angle,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => props.cuttingLine.angle ? DEFAULT_TYPE : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Угол...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.angle ?? '??'
    },
    layers        : {
        id            : () => 'layers-search',
        header        : ['', ''],
        width         : props.fieldsWidth.layers,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Настилы...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => '??'
    },
    expense       : {
        id            : () => 'expense-search',
        header        : ['', ''],
        width         : props.fieldsWidth.expense,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => props.cuttingLine.expense !== 0 ? DEFAULT_TYPE : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Расход...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.expense.toFixed(3)
    },
    fabric_roll   : {
        id            : () => 'fabric-roll-search',
        header        : ['', ''],
        width         : props.fieldsWidth.fabric_roll,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Рулон ПС...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => '??'
    },
    fabric_roll_at: {
        id            : () => 'fabric-roll-at-search',
        header        : ['', ''],
        width         : props.fieldsWidth.fabric_roll_at,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Отметка Рулон ПС...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => '??'
    },
    defects       : {
        id            : () => 'defects-search',
        header        : ['', ''],
        width         : props.fieldsWidth.defects,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Брак...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => '??'
    },
    defects_reason: {
        id            : () => 'defects_reason-search',
        header        : ['', ''],
        width         : props.fieldsWidth.defects_reason,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Причины брака...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => '??'
    },
    composition   : {
        id            : () => 'composition-search',
        header        : ['', ''],
        width         : props.fieldsWidth.composition,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Состав...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.order_line.composition ?? '',
        class         : 'truncate',
    },
    describe_1    : {
        id            : () => 'describe-1-search',
        header        : ['', ''],
        width         : props.fieldsWidth.describe_1,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Примечание 1...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.order_line.describe_1 ?? '',
        class         : 'truncate',
    },
    describe_2    : {
        id            : () => 'describe-2-search',
        header        : ['', ''],
        width         : props.fieldsWidth.describe_2,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Примечание 2...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.order_line.describe_2 ?? '',
        class         : 'truncate',
    },
    describe_3    : {
        id            : () => 'describe-3-search',
        header        : ['', ''],
        width         : props.fieldsWidth.describe_3,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Примечание 3...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.order_line.describe_3 ?? '',
        class         : 'truncate',
    },
    finished_at   : {
        id            : () => 'finished-at-search',
        header        : ['', ''],
        width         : props.fieldsWidth.finished_at,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Время...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) =>
            props.cuttingLine.finished_at ?
                formatTimeInFullFormat(props.cuttingLine.finished_at) :
                props.cuttingLine.false_at ?
                    formatTimeInFullFormat(props.cuttingLine.false_at) : '',
    },
    false_reason  : {
        id            : () => 'false-reason-search',
        header        : ['', ''],
        width         : props.fieldsWidth.false_reason,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => props.cuttingLine.false_reason ? 'danger' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Причина...',
        data          : (/*cuttingLine: ICuttingTaskLine*/) => props.cuttingLine.false_reason ?? '',
        class         : 'truncate',
    },
})

// __ Тип подсветки для стегальной машины элемента
const getTypeForTable = (cuttingTableTarget: ICuttingTableKeys) => {

    // !!! Порядок важен !!!

    // if (machineType.value === CUTTING_MACHINES.AVERAGE) {
    //     // if (cuttingMachineTarget === CUTTING_MACHINES.UNKNOWN) {
    //     //     return 'danger'
    //     // }
    //     return ACCENT_TYPE
    // }

    if (table.value === cuttingTableTarget) {
        if (cuttingTableTarget === CUTTING_TABLES.TABLE_0) {
            return 'danger'
        }
        return ACCENT_TYPE
    }

    return DEFAULT_TYPE
}


// __ Получаем тип стегальной машины
const table = computed(() => props.cuttingLine.table)

// __ Получаем трудозатраты
const getTime = computed(() => getTimeString(props.cuttingLine, true).replaceAll('.', ''))

// __ Получаем размер чехла (Высота из размеров чехла модели)
const getSize = computed(() => getCoverSizeString(props.cuttingLine))

// __ Тип подсветки для основного элемента
const getType = computed<IColorTypes>(() =>
        DEFAULT_TYPE
    // props.cuttingLine === globalManageTaskCardActiveCuttingLine.value ? ACTIVE_TYPE : 'dark'
)

// __ Тип подсветки для выполненного элемента
const finishedAtType = computed<IColorTypes>(() => props.cuttingLine.finished_at ? 'success' : 'danger')

</script>

<style scoped>

</style>
