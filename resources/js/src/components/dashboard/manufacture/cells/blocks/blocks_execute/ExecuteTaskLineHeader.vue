<template>

    <div class="flex">

        <!-- __ Checker -->
        <AppLabelTSWrapper :render-object="render.checker"/>

        <!-- __ id -->
        <AppLabelTSWrapper :render-object="render.id"/>

        <!-- __ position -->
        <AppLabelTSWrapper :render-object="render.position"/>

        <!-- __ Модель (Блок) -->
        <AppLabelTSWrapper :render-object="render.name"/>

        <!-- __ Количество -->
        <AppLabelTSWrapper :render-object="render.amount"/>

        <!-- __ Площадь -->
        <AppLabelTSWrapper :render-object="render.square"/>

        <!-- __ Трудозатраты -->
        <AppLabelTSWrapper :render-object="render.time"/>

        <!-- __ Линия 1 -->
        <AppLabelTSWrapper :render-object="render.line_1"/>

        <!-- __ Линия 2 -->
        <AppLabelTSWrapper :render-object="render.line_2"/>

        <!-- __ Линия ?? -->
        <AppLabelTSWrapper :render-object="render.line_0"/>

        <!-- __ КДБ -->
        <AppLabelTSWrapper :render-object="render.kdb"/>

        <!-- __ Описание -->
        <AppLabelTSWrapper :render-object="render.description"/>

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
        data:           (/*blockLine: IBlockTaskLine*/) => '✓'
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
        data:           (/*blockLine: IBlockTaskLine*/) => 'ID'
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
        data:           (/*blockLine: IBlockTaskLine*/) => '#',
    },
    name:       {
        id:             () => 'name-search',
        header:         ['', ''],
        width:          props.fieldsWidth.name,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Блок...',
        data:           (/*blockLine: IBlockTaskLine*/) => 'Блок'
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
        data:           (/*blockLine: IBlockTaskLine*/) => 'шт.'
    },
    square:      {
        id:             () => 'square-search',
        header:         ['', ''],
        width:          props.fieldsWidth.square,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍S...',
        data:           (/*blockLine: IBlockTaskLine*/) => 'S, m2'
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
        data:           (/*blockLine: IBlockTaskLine*/) => 'Время'
    },
    line_1:   {
        id:             () => 'line-1-search',
        header:         ['', ''],
        width:          props.fieldsWidth.line,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Линия 1...',
        data:           (/*blockLine: IBlockTaskLine*/) => '1'
    },
    line_2:   {
        id:             () => 'line-2-search',
        header:         ['', ''],
        width:          props.fieldsWidth.line,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Линия 2...',
        data:           (/*blockLine: IBlockTaskLine*/) => '2'
    },
    line_0:   {
        id:             () => 'line-0-search',
        header:         ['', ''],
        width:          props.fieldsWidth.line,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Линия ??...',
        data:           (/*blockLine: IBlockTaskLine*/) => '??'
    },
    description:     {
        id:             () => 'description-search',
        header:         ['', ''],
        width:          props.fieldsWidth.description,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Комментарий...',
        data:           (/*blockLine: IBlockTaskLine*/) => 'Комментарий'
    },
    kdb:     {
        id:             () => 'kdb-search',
        header:         ['', ''],
        width:          props.fieldsWidth.kdb,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍КДБ...',
        data:           (/*blockLine: IBlockTaskLine*/) => 'КДБ'
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
        data:           (/*blockLine: IBlockTaskLine*/) => 'Завершено'
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
        data:           (/*blockLine: IBlockTaskLine*/) => 'Причина',
    },
})


</script>

<style scoped>

</style>
