<template>

    <div class="flex">

        <!-- __ Collapsed -->
        <AppLabelTSWrapper :render-object="render.collapsed"/>

        <!-- __ id -->
        <AppLabelTSWrapper :render-object="render.id"/>

        <!-- __ position -->
        <AppLabelTSWrapper :render-object="render.position"/>

        <!-- __ Клиент -->
        <AppLabelTSWrapper :render-object="render.client"/>

        <!-- __ № Заявки -->
        <AppLabelTSWrapper :render-object="render.order_no"/>

        <!-- __ Статус  -->
        <AppLabelTSWrapper :render-object="render.status"/>

        <!-- __ Дата загрузки  -->
        <AppLabelTSWrapper :render-object="render.load_at"/>

        <!-- __ Прогресс общий -->
        <AppLabelTSWrapper :render-object="render.progressTotal"/>

        <!-- __ Комментарий  -->
        <AppLabelTSWrapper :render-object="render.comment"/>

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
const DEFAULT_HEIGHT   = 'h-[30px]'
const HEADER_TYPE      = 'indigo'
const DATA_TYPE        = 'indigo'
const DEFAULT_TYPE     = 'indigo'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'mini'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'center'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const COLLAPSED_WIDTH    = 'w-[40px]'


const render: IRenderData = reactive({
    collapsed:     {
        id:             () => 'collapsed-search',
        header:         ['', ''],
        width:          props.fieldsWidth.collapsed,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍collapsed...',
        data:           (/*sewingTask: ISewingTask*/) => '',
        // data:           (/*sewingTask: ISewingTask*/) => props.sewingTask.collapsed ? '▲' : '▼'

    },
    id:            {
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
        data:           (/*sewingTask: ISewingTask*/) => 'ID'
    },
    position:      {
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
        data:           (/*sewingTask: ISewingTask*/) => '#',
    },
    client:        {
        id:             () => 'client-search',
        header:         ['', ''],
        width:          props.fieldsWidth.client,
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
        data:           (/*sewingTask: ISewingTask*/) => 'Клиент',
    },
    order_no:      {
        id:             () => 'order-no-search',
        header:         ['', ''],
        width:          props.fieldsWidth.order_no,
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
        data:           (/*sewingTask: ISewingTask*/) => '№',
    },
    status:        {
        id:             () => 'status-search',
        header:         ['', ''],
        width:          props.fieldsWidth.status,
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
        data:           (/*sewingTask: ISewingTask*/) => 'Статус'
    },
    load_at:       {
        id:             () => 'progress-total-search',
        header:         ['', ''],
        width:          props.fieldsWidth.load_at,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Дата...',
        data:           (/*sewingTask: ISewingTask*/) => 'Дата загрузки'
    },
    progressTotal: {
        id:             () => 'progress-total-search',
        header:         ['Процесс выполнения от', 'общего времени СЗ'],
        width:          props.fieldsWidth.progressTotal,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Дата...',
        data:           (/*sewingTask: ISewingTask*/) => 'Прогресс',
    },
    comment:       {
        id:             () => 'comment-search',
        header:         ['', ''],
        width:          props.fieldsWidth.comment,
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
        data:           (/*sewingTask: ISewingTask*/) => 'Комментарий к СЗ',
    },

})


</script>

<style scoped>

</style>
