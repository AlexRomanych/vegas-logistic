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

        <!-- __ УШМ -->
        <AppLabelTSWrapper :render-object="render.universal"/>

        <!-- __ АШМ -->
        <AppLabelTSWrapper :render-object="render.auto"/>

        <!-- __ ГС -->
        <AppLabelTSWrapper :render-object="render.solid_hard"/>

        <!-- __ ГП -->
        <AppLabelTSWrapper :render-object="render.solid_lite"/>

        <!-- __ ТКЧ -->
        <AppLabelTSWrapper :render-object="render.tkch"/>

        <!-- __ Кант -->
        <AppLabelTSWrapper :render-object="render.kant"/>

        <!-- __ Ткань -->
        <AppLabelTSWrapper :render-object="render.textile"/>

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

import type { IColorTypes, IRenderData, ISewingMachineKeys, ISewingTaskLine } from '@/types'

import {
    getCoverSizeString,
    getSewingLineMachineType,
    getSewingTaskModelCoverName,
    getTimeString
} from '@/app/helpers/manufacture/helpers_sewing.ts'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'
import { SEWING_MACHINES } from '@/app/constants/sewing.ts'
// import AppLabelMultilineTSWrapper
//     from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'


interface IProps {
    sewingLine: ISewingTaskLine
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
    checker:          {
        id:             () => 'id-checker',
        header:         ['', ''],
        width:          props.fieldsWidth.checker,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍?...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.finished_at ? '✓' : '✗'
    },
    id:          {
        id:             () => 'id-search',
        header:         ['', ''],
        width:          props.fieldsWidth.id,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍id...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.id.toString()
    },
    position:    {
        id:             () => 'position-search',
        header:         ['', ''],
        width:          props.fieldsWidth.position,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍№ п/п...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.position.toString(),
    },
    size:        {
        id:             () => 'size-search',
        header:         ['', ''],
        width:          props.fieldsWidth.size,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Размер...',
        data:           (/*sewingLine: ISewingTaskLine*/) => getSize.value
    },
    cover:       {
        id:             () => 'cover-search',
        header:         ['', ''],
        width:          props.fieldsWidth.cover,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Чехол...',
        data:           (/*sewingLine: ISewingTaskLine*/) => getSewingTaskModelCoverName(props.sewingLine)
    },
    amount:      {
        id:             () => 'amount-search',
        header:         ['', ''],
        width:          props.fieldsWidth.amount,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Кол-во...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.amount.toString()
    },
    time:      {
        id:             () => 'time-search',
        header:         ['', ''],
        width:          props.fieldsWidth.time,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => getTime.value === '00с' ? 'danger' : getType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Время...',
        data:           (/*sewingLine: ISewingTaskLine*/) => getTime.value
    },
    universal:   {
        id:             () => 'universal-search',
        header:         ['', ''],
        width:          props.fieldsWidth.machine,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => getTypeForMachine(SEWING_MACHINES.UNIVERSAL),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍УШМ...',
        data:           (/*sewingLine: ISewingTaskLine*/) => 'У'
    },
    auto:        {
        id:             () => 'auto-search',
        header:         ['', ''],
        width:          props.fieldsWidth.machine,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => getTypeForMachine(SEWING_MACHINES.AUTO),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍АШМ...',
        data:           (/*sewingLine: ISewingTaskLine*/) => 'А'
    },
    solid_hard:  {
        id:             () => 'solid-hard-search',
        header:         ['', ''],
        width:          props.fieldsWidth.machine,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => getTypeForMachine(SEWING_MACHINES.SOLID_HARD),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍ГС...',
        data:           (/*sewingLine: ISewingTaskLine*/) => 'ГС'
    },
    solid_lite:  {
        id:             () => 'solid-lite-search',
        header:         ['', ''],
        width:          props.fieldsWidth.machine,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => getTypeForMachine(SEWING_MACHINES.SOLID_LITE),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍ГП...',
        data:           (/*sewingLine: ISewingTaskLine*/) => 'ГП'
    },
    tkch:     {
        id:             () => 'tkch-search',
        header:         ['', ''],
        width:          props.fieldsWidth.tkch,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍ТКЧ...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.order_line.model.main.tkch ?? ''
    },
    kant:     {
        id:             () => 'kant-search',
        header:         ['', ''],
        width:          props.fieldsWidth.kant,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Кант...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.order_line.model.main.kant ?? ''
    },
    textile:     {
        id:             () => 'textile-search',
        header:         ['', ''],
        width:          props.fieldsWidth.textile,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Ткань...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.order_line.textile ?? ''
    },
    composition: {
        id:             () => 'composition-search',
        header:         ['', ''],
        width:          props.fieldsWidth.composition,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Состав...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.order_line.composition ?? '',
        class:          'truncate',
    },
    describe_1:  {
        id:             () => 'describe-1-search',
        header:         ['', ''],
        width:          props.fieldsWidth.describe_1,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Примечание 1...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.order_line.describe_1 ?? '',
        class:          'truncate',
    },
    describe_2:  {
        id:             () => 'describe-2-search',
        header:         ['', ''],
        width:          props.fieldsWidth.describe_2,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Примечание 2...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.order_line.describe_2 ?? '',
        class:          'truncate',
    },
    describe_3:  {
        id:             () => 'describe-3-search',
        header:         ['', ''],
        width:          props.fieldsWidth.describe_3,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Примечание 3...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.order_line.describe_3 ?? '',
        class:          'truncate',
    },
    finished_at: {
        id:             () => 'finished-at-search',
        header:         ['', ''],
        width:          props.fieldsWidth.finished_at,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Время...',
        data:           (/*sewingLine: ISewingTaskLine*/) =>
                            props.sewingLine.finished_at ?
                                formatTimeInFullFormat(props.sewingLine.finished_at) :
                                props.sewingLine.false_at ?
                                    formatTimeInFullFormat(props.sewingLine.false_at) : '',
    },
    false_reason:  {
        id:             () => 'false-reason-search',
        header:         ['', ''],
        width:          props.fieldsWidth.false_reason,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => props.sewingLine.false_reason ? 'danger' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Причина...',
        data:           (/*sewingLine: ISewingTaskLine*/) => props.sewingLine.false_reason ?? '',
        class:          'truncate',
    },
})

// __ Тип подсветки для стегальной машины элемента
const getTypeForMachine = (sewingMachineTarget: ISewingMachineKeys) => {

    // !!! Порядок важен !!!

    if (machineType.value === SEWING_MACHINES.AVERAGE) {
        // if (sewingMachineTarget === SEWING_MACHINES.UNKNOWN) {
        //     return 'danger'
        // }
        return ACCENT_TYPE
    }

    if (machineType.value === sewingMachineTarget) {
        // if (sewingMachineTarget === SEWING_MACHINES.UNKNOWN) {
        //     return 'danger'
        // }
        return ACCENT_TYPE
    }

    return DEFAULT_TYPE
}

// __ Получаем тип стегальной машины
const machineType = computed(() => getSewingLineMachineType(props.sewingLine))

// __ Получаем трудозатраты
const getTime = computed(() => getTimeString(props.sewingLine, true).replaceAll('.', ''))

// __ Получаем размер чехла (Высота из размеров чехла модели)
const getSize = computed(() => getCoverSizeString(props.sewingLine))

// __ Тип подсветки для основного элемента
const getType = computed<IColorTypes>(() =>
    DEFAULT_TYPE
    // props.sewingLine === globalManageTaskCardActiveSewingLine.value ? ACTIVE_TYPE : 'dark'
)

// __ Тип подсветки для выполненного элемента
const finishedAtType = computed<IColorTypes>(() => props.sewingLine.finished_at ? 'success' : 'danger')

</script>

<style scoped>

</style>
