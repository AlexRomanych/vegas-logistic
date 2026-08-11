<template>
    <!-- __ Данные -->
    <div v-for="assemblyLine of line.assembly_lines" :key="assemblyLine.id">

        <div v-for="sector of assemblyLine.sectors" :key="sector.id">

            <div class="flex">

                <!--&lt;!&ndash; __ Collapsed &ndash;&gt;-->
                <!--<AppLabelTS-->
                <!--    v-if="render.collapsed.show"-->
                <!--    :width="render.collapsed.width"-->
                <!--    align="center"-->
                <!--    text=""-->
                <!--    text-size="normal"-->
                <!--    type="light"-->
                <!--/>-->

                <!-- __ Участок -->
                <AppLabelTSWrapper :arg="sector" :render-object="render.sector"/>

                <!-- __ Размер детальки -->
                <AppLabelTSWrapper :arg="sector" :render-object="render.size"/>

                <!-- __ Название материала из 1С -->
                <AppLabelTSWrapper :arg="sector" :render-object="render.name"/>

                <!-- __ Количество Деталек -->
                <AppLabelTSWrapper :arg="sector" :render-object="render.amount"/>

                <!-- __ Код материала из 1С -->
                <AppLabelTSWrapper :arg="sector" :render-object="render.code_1c"/>

                <!-- __ Расход -->
                <AppLabelTSWrapper :arg="sector" :render-object="render.expense"/>

            </div>

        </div>
    </div>

</template>

<script lang="ts" setup>
import type { IAssemblySectorKeys, IRenderData, IRenderOrderLine, IRenderOrderLineAssemblyLineSector } from '@/types'
import { reactive } from 'vue'

import {
    getSectorAmount,
    getSectorByName,
    getSectorExpense, getSectorSize
} from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'

// import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    line: IRenderOrderLine
}

/*const props =*/ defineProps<IProps>()

// __ Объект отображения данных
// const DEFAULT_WIDTH      = 'w-[100px]'
// const DEFAULT_WIDTH_DATE = 'w-[140px]'
const DEFAULT_HEIGHT   = 'h-[30px]'
const HEADER_TYPE      = 'indigo'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'micro'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
// const DATA_ALIGN_DEFAULT = 'center'
const DATA_ALIGN       = 'left'

// __ Получаем тип Участка
const getSectorType = (assemblySector: IRenderOrderLineAssemblyLineSector) => {
    if (!assemblySector) return DEFAULT_TYPE
    const sector = getSectorByName(assemblySector.sector as IAssemblySectorKeys)
    return sector ? sector.TYPE : DEFAULT_TYPE
}

// __ Получаем название Участка
const getSectorName = (assemblySector: IRenderOrderLineAssemblyLineSector) => {
    if (!assemblySector) return DEFAULT_TYPE
    const sector = getSectorByName(assemblySector.sector as IAssemblySectorKeys)
    return sector ? sector.TITLE : ''
}


const render: IRenderData = reactive({
    collapsed: {
        header        : '▲',
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : (/*orderLine: IRenderOrderLine*/) => '▲',
        // click         : (material: IRenderOrderLineMaterial) => '',
    },
    sector     : {
        header        : 'Название Участка',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IRenderOrderLineAssemblyLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (assemblySector: IRenderOrderLineAssemblyLineSector) => getSectorName(assemblySector),
    },
    size     : {
        header        : 'Размер Детали',
        width         : 'w-[80px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IRenderOrderLineAssemblyLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (assemblySector: IRenderOrderLineAssemblyLineSector) => getSectorSize(assemblySector),
    },
    code_1c     : {
        header        : 'Код Материала',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IRenderOrderLineAssemblyLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (assemblySector: IRenderOrderLineAssemblyLineSector) => assemblySector.material_code_1c,
    },
    name     : {
        header        : 'Название Материала',
        width         : 'w-[200px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IRenderOrderLineAssemblyLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (assemblySector: IRenderOrderLineAssemblyLineSector) => assemblySector.material_name,
    },
    amount   : {
        header        : 'Количество',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IRenderOrderLineAssemblyLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (assemblySector: IRenderOrderLineAssemblyLineSector) => getSectorAmount(assemblySector).toFixed(0)
    },
    expense  : {
        header        : 'Расход',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IRenderOrderLineAssemblyLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (assemblySector: IRenderOrderLineAssemblyLineSector) => getSectorExpense(assemblySector).toFixed(3)
    },

})

</script>

<style scoped>

</style>
