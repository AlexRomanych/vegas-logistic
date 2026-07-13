<template>
    <div>
        <div class="flex">

            <!-- __ id -->
            <AppLabelTSWrapper :render-object="render.id" header/>

            <!-- __ Начало -->
            <AppLabelTSWrapper :render-object="render.start_at" header/>

            <!-- __ Окончание -->
            <AppLabelTSWrapper :render-object="render.finish_at" header/>

            <!-- __ Продолжительность -->
            <AppLabelTSWrapper :render-object="render.duration" header/>

            <!-- __ Событие -->
            <AppLabelTSWrapper :render-object="render.event" header/>

        </div>
    </div>

    <!-- __ Данные -->
    <div v-if="!collapsed">
        <div v-for="entity of entitiesRender" :key="entity.id">
            <div class="flex ">

                <!-- __ id -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.id"/>

                <!-- __ Начало -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.start_at"/>

                <!-- __ Окончание -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.finish_at"/>

                <!-- __ Продолжительность -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.duration"/>

                <!-- __ Событие -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.event"/>

            </div>
        </div>
    </div>


</template>

<script lang="ts" setup>
import { computed,  reactive, ref } from 'vue'

import type {  IRenderData, IBlockDay, ICellEvent } from '@/types'

import { formatTimeInFullFormat, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'

// __ Унифицируем Интерфейс
type IEntity = ICellEvent

interface IProps {
    blockDay: IBlockDay
    canEdit?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    canEdit: true,
})


// __ Данные для отображения
const entitiesRender = computed(() => props.blockDay.cell_events)

const collapsed = ref(false)

// __ Объект отображения данных
const DEFAULT_HEIGHT   = 'h-[25px]'
const HEADER_TYPE      = 'indigo'
const DATA_TYPE        = 'success'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'

const render: IRenderData = reactive({
    id         : {
        id            : () => 'id-search',
        header        : () => 'ID',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (entity: IEntity) => entity.id.toString(),
    },
    start_at         : {
        id            : () => 'start-at-search',
        header        : () => 'Начало',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (entity: IEntity) => formatTimeInFullFormat(entity.start_at),
    },
    finish_at         : {
        id            : () => 'finish-at-search',
        header        : () => 'Окончание',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (entity: IEntity) => formatTimeInFullFormat(entity.finish_at),
    },
    duration         : {
        id            : () => 'duration-search',
        header        : () => 'Длительность',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (entity: IEntity) => getDuration(entity),
    },
    event         : {
        id            : () => 'event-search',
        header        : () => 'Событие',
        width         : 'w-[450px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍id...',
        data          : (entity: IEntity) => entity.event,
    },
})

// __ Получаем продолжительность
const getDuration = (cellEvent: ICellEvent) => {
    const millis = new Date(cellEvent.finish_at).getTime() - new Date(cellEvent.start_at).getTime()
    return formatTimeWithLeadingZeros(millis / 1000,)
}

</script>

<style scoped>

</style>
