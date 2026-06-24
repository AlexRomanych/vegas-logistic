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
import { reactive } from 'vue'
import type { IRenderData } from '@/types'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'

interface IProps {
    fieldsWidth: Record<string, string>
}

const props = defineProps<IProps>()

// __ Объект отображения данных
const DEFAULT_HEIGHT   = 'h-[20px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'stone'
const DEFAULT_TYPE     = 'stone'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'center'

const render: IRenderData = reactive({
    checker:          {
        id:             () => 'id-checker',
        header:         ['', ''],
        width:          props.fieldsWidth.checker,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍?...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => '✓'
    },
    id:          {
        id:             () => 'id-search',
        header:         ['', ''],
        width:          props.fieldsWidth.id,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍id...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'ID'
    },
    position:    {
        id:             () => 'position-search',
        header:         ['', ''],
        width:          props.fieldsWidth.position,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍№ п/п...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => '#',
    },
    size:        {
        id:             () => 'size-search',
        header:         ['', ''],
        width:          props.fieldsWidth.size,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Размер...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Размер'
    },
    cover:       {
        id:             () => 'cover-search',
        header:         ['', ''],
        width:          props.fieldsWidth.cover,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Чехол...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Чехол'
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
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Кол-во...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Шт.'
    },
    time:      {
        id:             () => 'time-search',
        header:         ['', ''],
        width:          props.fieldsWidth.time,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Время...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Время'
    },
    detail:   {
        id:             () => 'detail-search',
        header:         ['', ''],
        width:          props.fieldsWidth.detail,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Элемент...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Эл.'
    },
    table_1:   {
        id:             () => 'table-1-search',
        header:         ['', ''],
        width:          props.fieldsWidth.table,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Стол 1...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => '1'
    },
    table_2:   {
        id:             () => 'table-2-search',
        header:         ['', ''],
        width:          props.fieldsWidth.table,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Стол 2...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => '2'
    },
    table_3:   {
        id:             () => 'table-3-search',
        header:         ['', ''],
        width:          props.fieldsWidth.table,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Стол 3...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => '3'
    },
    table_0:   {
        id:             () => 'table-0-search',
        header:         ['', ''],
        width:          props.fieldsWidth.table,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Стол ??...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => '??'
    },
    machine:   {
        id:             () => 'machine-search',
        header:         ['', ''],
        width:          props.fieldsWidth.machine,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍ШМ ??...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'ШМ'
    },
    kdch:     {
        id:             () => 'kdch-search',
        header:         ['', ''],
        width:          props.fieldsWidth.kdch,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍КДЧ...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'КДЧ'
    },
    tkch:     {
        id:             () => 'tkch-search',
        header:         ['', ''],
        width:          props.fieldsWidth.tkch,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍ТКЧ...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'ТКЧ'
    },
    kant:     {
        id:             () => 'kant-search',
        header:         ['', ''],
        width:          props.fieldsWidth.kant,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Кант...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Кант'
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
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Ткань'
    },
    cut:     {
        id:             () => 'cut-search',
        header:         ['', ''],
        width:          props.fieldsWidth.cut,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Крой...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Крой'
    },
    angle:     {
        id:             () => 'angle-search',
        header:         ['', ''],
        width:          props.fieldsWidth.angle,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Угол...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Угол'
    },
    layers:     {
        id:             () => 'layers-search',
        header:         ['', ''],
        width:          props.fieldsWidth.layers,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Настилы...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Настилы'
    },
    expense:     {
        id:             () => 'expense-search',
        header:         ['', ''],
        width:          props.fieldsWidth.expense,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Расход...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Расход'
    },
    fabric_roll:     {
        id:             () => 'fabric-roll-search',
        header:         ['', ''],
        width:          props.fieldsWidth.fabric_roll,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Рулон ПС...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Рулон'
    },
    fabric_roll_at:     {
        id:             () => 'fabric-roll-at-search',
        header:         ['', ''],
        width:          props.fieldsWidth.fabric_roll_at,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Отметка Рулон ПС...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Отм. рул.'
    },
    defects:     {
        id:             () => 'defects-search',
        header:         ['', ''],
        width:          props.fieldsWidth.defects,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Брак...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Брак'
    },
    defects_reason:     {
        id:             () => 'defects_reason-search',
        header:         ['', ''],
        width:          props.fieldsWidth.defects_reason,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Причины брака...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Причина брака'
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
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Состав',
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
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Прим. 1',
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
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Прим. 2',
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
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Прим. 3',
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
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Завершено'
    },
    false_reason:  {
        id:             () => 'false-reason-search',
        header:         ['', ''],
        width:          props.fieldsWidth.false_reason,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Причина...',
        data:           (/*cuttingLine: ICuttingTaskLine*/) => 'Причина',
    },
})


</script>

<style scoped>

</style>
