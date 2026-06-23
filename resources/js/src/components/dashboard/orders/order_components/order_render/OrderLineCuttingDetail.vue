<template>
    <!-- __ Данные -->
    <div v-for="(cuttingLine, idx) of line.cutting_lines" :key="idx">
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


            <!-- __ Название Детальки Раскроя -->
            <AppLabelTSWrapper :arg="cuttingLine" :render-object="render.name"/>

            <!-- __ Количество Деталек Раскроя -->
            <AppLabelTSWrapper :arg="cuttingLine" :render-object="render.amount"/>

            <!-- __ Стол Деталек Раскроя -->
            <AppLabelTSWrapper :arg="cuttingLine" :render-object="render.table"/>

            <!-- __ Ткань Деталек Раскроя -->
            <AppLabelTSWrapper :arg="cuttingLine" :render-object="render.fabric"/>

            <!-- __ Крой Деталек Раскроя -->
            <AppLabelTSWrapper :arg="cuttingLine" :render-object="render.cut"/>

            <!-- __ Угол Деталек Раскроя -->
            <AppLabelTSWrapper :arg="cuttingLine" :render-object="render.angel"/>

            <!-- __ Расход Деталек Раскроя -->
            <AppLabelTSWrapper :arg="cuttingLine" :render-object="render.expense"/>

        </div>
    </div>

</template>

<script lang="ts" setup>
import type { IRenderData, IRenderOrderLine, IRenderOrderLineCuttingLine } from '@/types'
import { reactive } from 'vue'
import { DETAILS } from '@/app/constants/cutting.ts'
import { getTableTitle } from '@/app/helpers/manufacture/helpers_cutting.ts'
import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'

// import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    line: IRenderOrderLine
}

const props = defineProps<IProps>()

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

// __ Получаем тип
const getType = (cuttingLine: IRenderOrderLineCuttingLine) => {
    if (!cuttingLine) return DEFAULT_TYPE
    switch (cuttingLine.detail) {
        case DETAILS.PANEL.NAME: return 'info'
        case DETAILS.PANEL_UP.NAME: return 'indigo'
        case DETAILS.PANEL_DOWN.NAME: return 'primary'
        case DETAILS.SIDE.NAME: return 'orange'
    }

    return DEFAULT_TYPE
}

// __ Получаем название детальки
const getName = (cuttingLine: IRenderOrderLineCuttingLine): string => {
    const detail = Object.values(DETAILS).find(value => value.NAME === cuttingLine.detail)
    return detail ? `${detail.TITLE} ${props.line.model.name_report}` : props.line.model.name_report
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
    name     : {
        header        : 'Название Детали',
        width         : 'w-[358px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (cuttingLine: IRenderOrderLineCuttingLine) => getType(cuttingLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (cuttingLine: IRenderOrderLineCuttingLine) => getName(cuttingLine),
    },
    amount     : {
        header        : 'Количество',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (cuttingLine: IRenderOrderLineCuttingLine) => getType(cuttingLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (cuttingLine: IRenderOrderLineCuttingLine) => cuttingLine.amount.toString(),
    },
    table     : {
        header        : 'Стол',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (cuttingLine: IRenderOrderLineCuttingLine) => getType(cuttingLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (cuttingLine: IRenderOrderLineCuttingLine) => getTableTitle(cuttingLine.table),
    },
    fabric     : {
        header        : 'Ткань',
        width         : 'w-[248px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (cuttingLine: IRenderOrderLineCuttingLine) => getType(cuttingLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (cuttingLine: IRenderOrderLineCuttingLine) => cuttingLine.fabric_construct[0]
    },
    cut     : {
        header        : 'Крой',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (cuttingLine: IRenderOrderLineCuttingLine) => getType(cuttingLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (cuttingLine: IRenderOrderLineCuttingLine) => `${cuttingLine.cut_width.toString()}x${cuttingLine.cut_length.toString()}`,
    },
    angel     : {
        header        : 'Угол',
        width         : 'w-[70px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (cuttingLine: IRenderOrderLineCuttingLine) => getType(cuttingLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (cuttingLine: IRenderOrderLineCuttingLine) => cuttingLine.angle ?? '',
    },
    expense  : {
        header        : 'Расход',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (cuttingLine: IRenderOrderLineCuttingLine) => getType(cuttingLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (cuttingLine: IRenderOrderLineCuttingLine) => cuttingLine.expense.toFixed(4),
    },

})

</script>

<style scoped>

</style>
