<template>
    <!-- __ Данные -->
    <div v-for="(blockLine, idx) of line.block_lines" :key="idx">
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


            <!-- __ Название Блока -->
            <AppLabelTSWrapper :arg="blockLine" :render-object="render.name"/>

            <!-- __ Блоков -->
            <AppLabelTSWrapper :arg="blockLine" :render-object="render.amount"/>

            <!-- __ Производственная Линия Блоков -->
            <AppLabelTSWrapper :arg="blockLine" :render-object="render.line"/>

        </div>
    </div>

</template>

<script lang="ts" setup>
import { reactive } from 'vue'
import type { IColorTypes, IRenderData, IRenderOrderLine, IRenderOrderLineBlockLine } from '@/types'
import { BLOCK_MANUF_LINES, LINE_1_NAME, LINE_2_NAME } from '@/app/constants/blocks.ts'
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
const DEFAULT_TYPE     = 'stone'
const HEADER_TEXT_SIZE = 'micro'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
// const DATA_ALIGN_DEFAULT = 'center'
const DATA_ALIGN       = 'left'


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
        header        : 'Название Блока',
        width         : 'w-[358px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockLine: IRenderOrderLineBlockLine) => getType(blockLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (blockLine: IRenderOrderLineBlockLine) => blockLine.block_name,
    },
    amount   : {
        header        : 'Количество',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockLine: IRenderOrderLineBlockLine) => getType(blockLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (blockLine: IRenderOrderLineBlockLine) => blockLine.amount.toString(),
    },
    line     : {
        header        : 'Линия',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (blockLine: IRenderOrderLineBlockLine) => getType(blockLine),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (blockLine: IRenderOrderLineBlockLine) => blockLine.manuf_line === BLOCK_MANUF_LINES.LINE_1 ? LINE_1_NAME : LINE_2_NAME,
    },

})

const getType = (blockLine: IRenderOrderLineBlockLine): IColorTypes => {
    // if (false) console.log(blockLine)
    return DEFAULT_TYPE
}

</script>

<style scoped>

</style>
