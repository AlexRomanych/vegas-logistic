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
        data:           (/*sewingLine: ISewingTaskLine*/) => '✓'
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
        data:           (/*sewingLine: ISewingTaskLine*/) => 'ID'
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
        data:           (/*sewingLine: ISewingTaskLine*/) => '#',
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
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Размер'
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
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Чехол'
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
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Шт.'
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
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Время'
    },
    universal:   {
        id:             () => 'universal-search',
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
        dataAlign:      DATA_ALIGN,
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
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
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
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
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
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
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
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍ТКЧ...',
        data:           (/*sewingLine: ISewingTaskLine*/) => 'ТКЧ'
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
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Кант...',
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Кант'
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
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Ткань...',
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Ткань'
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
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Состав',
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
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Прим. 1',
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
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Прим. 2',
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
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Прим. 3',
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
        data:           (/*sewingLine: ISewingTaskLine*/) => 'Завершено'
    },
})


</script>

<style scoped>

</style>
