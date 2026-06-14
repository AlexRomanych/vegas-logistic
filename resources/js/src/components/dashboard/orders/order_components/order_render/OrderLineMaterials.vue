<template>
    <!-- __ Данные -->
    <div v-for="(material, idx) of line.materials" :key="idx">
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

            <!-- __ Код из 1С -->
            <AppLabelTSWrapper :arg="material" :render-object="render.code_1c"/>

            <!-- __ Название материала -->
            <AppLabelTSWrapper :arg="material" :render-object="render.name"/>

            <!-- __ Ед. измерения -->
            <AppLabelTSWrapper :arg="material" :render-object="render.unit"/>

            <!-- __ Расход -->
            <AppLabelTSWrapper :arg="material" :render-object="render.expense"/>

        </div>
    </div>

</template>

<script lang="ts" setup>
import type { IRenderData, IRenderOrderLine, IRenderOrderLineMaterial } from '@/types'
import { reactive } from 'vue'
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

const getType = (material: IRenderOrderLineMaterial) => {
    if (!material) return DEFAULT_TYPE
    if (material.name.includes('БП ') || material.name.includes('(V) ')) return 'indigo'
    if (material.name.includes('ПС ')) return 'orange'
    return DEFAULT_TYPE
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
    code_1c  : {
        header        : 'Код из 1С',
        width         : 'w-[70px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (material: IRenderOrderLineMaterial) => getType(material),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (material: IRenderOrderLineMaterial) => material.code_1c
    },
    name     : {
        header        : 'Название материала',
        width         : 'w-[284px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (material: IRenderOrderLineMaterial) => getType(material),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (material: IRenderOrderLineMaterial) => material.name
    },
    unit     : {
        header        : 'Ед. изм.',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (material: IRenderOrderLineMaterial) => getType(material),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (material: IRenderOrderLineMaterial) => material.unit
    },
    expense  : {
        header        : 'Расход',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (material: IRenderOrderLineMaterial) => getType(material),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (material: IRenderOrderLineMaterial) => ((material.expense_per_pic + material.rest_per_pic) * props.line.amount).toFixed(3)
    },

})

</script>

<style scoped>

</style>
