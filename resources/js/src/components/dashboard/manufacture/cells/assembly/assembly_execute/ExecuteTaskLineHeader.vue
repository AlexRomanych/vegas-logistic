<template>

    <div class="flex">
        <!-- __ Checker -->
        <AppLabelTSWrapper :render-object="render.checker"/>

        <!-- __ id -->
        <AppLabelTSWrapper :render-object="render.id"/>

        <!-- __ Участок -->
        <AppLabelTSWrapper :render-object="render.sector"/>

        <!-- __ Размер детальки -->
        <AppLabelTSWrapper :render-object="render.size"/>

        <!-- __ Код материала из 1С -->
        <AppLabelTSWrapper :render-object="render.code_1c"/>

        <!-- __ Название материала из 1С -->
        <AppLabelTSWrapper :render-object="render.name"/>

        <!-- __ Количество Деталек -->
        <AppLabelTSWrapper :render-object="render.amount"/>

        <!-- __ Трудозатраты -->
        <AppLabelTSWrapper :render-object="render.time"/>

        <!-- __ Расход -->
        <AppLabelTSWrapper :render-object="render.expense"/>

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
    sector:          {
        sector:             () => 'sector-search',
        header:         ['', ''],
        width:          props.fieldsWidth.sector,
        height:         DEFAULT_HEIGHT,
        show:           false,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Участок...',
        data:           (/*blockLine: IBlockTaskLine*/) => 'Участок'
    },
    size:          {
        size:             () => 'size-search',
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
        placeholder:    '🔍Участок...',
        data:           (/*blockLine: IBlockTaskLine*/) => 'Размер'
    },
    code_1c:       {
        id:             () => 'code-1c-search',
        header:         ['', ''],
        width:          props.fieldsWidth.code_1c,
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
        data:           (/*blockLine: IBlockTaskLine*/) => 'Код 1С'
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
        placeholder:    '🔍Материал...',
        data:           (/*blockLine: IBlockTaskLine*/) => 'Материал'
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
    expense:      {
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
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Кол-во...',
        data:           (/*blockLine: IBlockTaskLine*/) => 'Расход'
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
