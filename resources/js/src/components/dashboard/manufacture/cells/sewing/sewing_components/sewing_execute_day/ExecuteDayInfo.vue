<template>

    <!-- __ id -->
    <div class="flex">
        <AppLabelTSWrapper :render-object="render.id" header/>
        <AppLabelTSWrapper :arg="sewingDay" :render-object="render.id"/>
    </div>

    <!-- __ Дата производства -->
    <div class="flex">
        <AppLabelTSWrapper :render-object="render.manufacture_date" header/>
        <AppLabelTSWrapper :arg="sewingDay" :render-object="render.manufacture_date"/>
    </div>

    <!-- __ Смена производства -->
    <div class="flex">
        <AppLabelTSWrapper :render-object="render.manufacture_change" header/>
        <AppLabelTSWrapper :arg="sewingDay" :render-object="render.manufacture_change"/>
    </div>

    <!-- __ Количество заявок -->
    <div class="flex">
        <AppLabelTSWrapper :render-object="render.tasks_amount" header/>
        <AppLabelTSWrapper :arg="sewingDay" :render-object="render.tasks_amount"/>
    </div>
    <!-- __ Количество сотрудников -->
    <div class="flex">
        <AppLabelTSWrapper :render-object="render.workers_amount" header/>
        <AppLabelTSWrapper :arg="sewingDay" :render-object="render.workers_amount"/>
    </div>


</template>

<script lang="ts" setup>

import type { IRenderData, ISewingDay, } from '@/types'
import { reactive } from 'vue'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import { formatDateInFullFormat } from '@/app/helpers/helpers_date'

interface IProps {
    sewingDay: ISewingDay | null
}

type IEntity = ISewingDay

const props = defineProps<IProps>()


// __ Объект отображения данных
const DEFAULT_HEIGHT     = 'h-[25px]'
const DEFAULT_WIDTH      = 'w-[200px]'
const HEADER_TYPE        = 'indigo'
const DATA_TYPE          = 'success'
const DEFAULT_TYPE       = 'dark'
const HEADER_TEXT_SIZE   = 'mini'
const DATA_TEXT_SIZE     = 'mini'
const HEADER_ALIGN       = 'center'
const DATA_ALIGN         = 'center'

const render: IRenderData = reactive({
    id: {
        header:         () => 'ID дня',
        width:          DEFAULT_WIDTH,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        data:           (entity: IEntity) => entity?.id.toString(),
    },

    manufacture_date: {
        header:         () => 'Дата производства',
        width:          DEFAULT_WIDTH,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        data:           (entity: IEntity) => entity && formatDateInFullFormat(entity.action_at)
    },
    manufacture_change: {
        header:         () => 'Смена производства',
        width:          DEFAULT_WIDTH,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        data:           (entity: IEntity) => entity?.change.toString()
    },
    tasks_amount: {
        header:         () => 'Количество заявок',
        width:          DEFAULT_WIDTH,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        data:           (entity: IEntity) => entity?.sewing_tasks.length.toString()
    },
    workers_amount: {
        header:         () => 'Количество сотрудников',
        width:          DEFAULT_WIDTH,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        data:           (entity: IEntity) => entity?.workers.length.toString()
    },

})


</script>

<style scoped>

</style>
