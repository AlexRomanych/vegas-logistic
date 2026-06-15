<template>

    <!-- __ Данные по Группам -->
    <div v-for="materialGroup of materialGroups" :key="materialGroup.code_1c">
        <div class="flex">

            <!-- __ Collapsed Category -->
            <AppLabelTSWrapper :arg="materialGroup" :render-object="render.collapsed" @click="render.collapsed.click!(materialGroup)"/>

            <!-- __ Код из 1С -->
            <AppLabelTSWrapper
                :arg="materialGroup"
                :render-object="{...render.code_1c, type: 'primary'}"
                @click="render.collapsed.click!(materialGroup)"
            />

            <!-- __ Название Группы материалов -->
            <AppLabelTSWrapper
                :arg="materialGroup"
                :render-object="{...render.name, width: 'w-[764px]', type: 'primary'}"
                @click="render.collapsed.click!(materialGroup)"
            />
        </div>

        <!-- __ Категории материалов -->
        <div v-if="!materialGroup.collapsed">
            <div v-for="materialCategory of materialGroup.categories" :key="materialCategory.code_1c">
                <div class="flex">

                    <!-- __ Plug -->
                    <AppLabelTS
                        v-if="render.collapsed.show"
                        :width="render.collapsed.width"
                        align="center"
                        rounded="4"
                        text=""
                        text-size="normal"
                        type="light"
                    />

                    <!-- __ Collapsed Materials -->
                    <AppLabelTSWrapper :arg="materialCategory" :render-object="render.collapsed" @click="render.collapsed.click!(materialCategory)"/>

                    <!-- __ Код из 1С -->
                    <AppLabelTSWrapper :arg="materialCategory" :render-object="render.code_1c" @click="render.collapsed.click!(materialCategory)"/>

                    <!-- __ Название Категории материалов -->
                    <AppLabelTSWrapper :arg="materialCategory" :render-object="{...render.name, width: 'w-[730px]'}"
                                       @click="render.collapsed.click!(materialCategory)"/>
                </div>

                <!-- __ Сами материалы -->
                <div v-if="!materialCategory.collapsed">
                    <div v-for="material of materialCategory.materials" :key="material.code_1c">
                        <div class="flex">

                            <!-- __ Plug -->
                            <template v-for="i of [1, 2]" :key="i">
                                <AppLabelTS
                                    v-if="render.collapsed.show"
                                    :width="render.collapsed.width"
                                    align="center"
                                    rounded="4"
                                    text=""
                                    text-size="normal"
                                    type="light"
                                />
                            </template>

                            <!-- __ Collapsed Orders -->
                            <AppLabelTSWrapper :arg="material" :render-object="render.collapsed" @click="render.collapsed.click!(material)"/>

                            <!-- __ Код из 1С -->
                            <AppLabelTSWrapper :arg="material" :render-object="render.code_1c" @click="render.collapsed.click!(material)"/>

                            <!-- __ Название Материала -->
                            <AppLabelTSWrapper :arg="material" :render-object="{...render.name, width:'w-[538px]'}" @click="render.collapsed.click!(material)"/>

                            <!-- __ Единица измерения -->
                            <AppLabelTSWrapper :arg="material" :render-object="render.unit" @click="render.collapsed.click!(material)"/>

                            <!-- __ Общий Расход по материалу -->
                            <AppLabelTSWrapper
                                :arg="material"
                                :render-object="{...render.materialTotal, type: 'orange'}"
                                @click="render.collapsed.click!(material)"
                            />

                        </div>

                        <!-- __ Заказы -->
                        <div v-if="!material.collapsed">
                            <div v-for="order of material.orders" :key="order.order_id">
                                <div class="flex">

                                    <!-- __ Plug -->
                                    <template v-for="j of [1, 2, 3]" :key="j">
                                        <AppLabelTS
                                            v-if="render.collapsed.show"
                                            :width="render.collapsed.width"
                                            align="center"
                                            rounded="4"
                                            text=""
                                            text-size="normal"
                                            type="light"
                                        />
                                    </template>

                                    <!-- __ Collapsed OrderLines -->
                                    <AppLabelTSWrapper :arg="order" :render-object="render.collapsed" @click="render.collapsed.click!(order)"/>

                                    <!-- __ Название Клиента -->
                                    <AppLabelTSWrapper :arg="order" :render-object="render.client" @click="render.collapsed.click!(order)"/>

                                    <!-- __ Номер заявки -->
                                    <!--<AppLabelTSWrapper :arg="order" :render-object="render.no" @click="render.collapsed.click!(order)"/>-->

                                    <!-- __ Общее Количество изделий в Заявке -->
                                    <AppLabelTSWrapper :arg="order" :render-object="render.orderAmount" @click="render.collapsed.click!(order)"/>

                                    <!-- __ Общий Расход в Заявке -->
                                    <AppLabelTSWrapper :arg="order" :render-object="render.orderTotal" @click="render.collapsed.click!(order)"/>
                                </div>

                                <!-- __ Строки Заказа OrderLines -->
                                <div v-if="!order.collapsed">
                                    <div v-for="line of order.order_lines" :key="line.line_id">
                                        <div class="flex">

                                            <!-- __ Plug -->
                                            <template v-for="s of [1, 2, 3, 4]" :key="s">
                                                <AppLabelTS
                                                    v-if="render.collapsed.show"
                                                    :width="render.collapsed.width"
                                                    align="center"
                                                    rounded="4"
                                                    text=""
                                                    text-size="normal"
                                                    type="light"
                                                />
                                            </template>

                                            <!-- __ Размер -->
                                            <AppLabelTSWrapper :arg="line" :render-object="render.size"/>

                                            <!-- __ Код модели из 1С -->
                                            <AppLabelTSWrapper :arg="line" :render-object="render.modelCode_1c"/>

                                            <!-- __ Название Модели из 1С -->
                                            <AppLabelTSWrapper :arg="line" :render-object="render.modelName"/>

                                            <!-- __ Кол-во -->
                                            <AppLabelTSWrapper :arg="line" :render-object="render.modelAmount"/>

                                            <!-- __ Расход -->
                                            <AppLabelTSWrapper :arg="line" :render-object="render.modelTotal"/>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script lang="ts" setup>
import type { IMaterialRender, IMaterialRenderCommon, IMaterialRenderGroup, IMaterialRenderOrder, IMaterialRenderOrderLine, IRenderData } from '@/types'

import { reactive } from 'vue'

import { getPrettyOrderNumber } from '@/app/helpers/helpers_order'

import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


interface IProps {
    materialGroups: IMaterialRenderGroup[]
}

/*const props =*/ defineProps<IProps>()

// __ Объект отображения данных
const TOTAL_WIDTH      = 'w-[100px]'
const DEFAULT_HEIGHT   = 'h-[30px]'
const HEADER_TYPE      = 'indigo'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'indigo'
const ORDER_TYPE       = 'stone'
const LINE_TYPE        = 'dark'
const HEADER_TEXT_SIZE = 'micro'
const DATA_TEXT_SIZE   = 'mini'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'
// const DEFAULT_WIDTH_DATE = 'w-[140px]'
// const DATA_ALIGN_DEFAULT = 'center'


const render: IRenderData = reactive({
    collapsed    : {
        header        : '▲▼',
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : (entity: IMaterialRenderCommon) => entity.collapsed ? '▲' : '▼',
        click         : (entity: IMaterialRenderCommon) => entity.collapsed = !entity.collapsed
    },
    code_1c      : {
        header        : 'Код Группы',
        width         : 'w-[90px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (entity: IMaterialRenderCommon) => entity.code_1c,
    },
    name         : {
        header        : 'Название Группы',
        width         : 'w-[450px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (entity: IMaterialRenderCommon) => entity.name,
    },
    unit         : {
        header        : 'Единица измерения',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (entity: IMaterialRender) => entity.unit,
    },
    materialTotal: {
        header        : 'Всего по материалу',
        width         : TOTAL_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (entity: IMaterialRender) => entity.material_total.toFixed(3),
    },
    client       : {
        header        : 'Клиент',
        width         : 'w-[598px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => ORDER_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (order: IMaterialRenderOrder) => `${order.client_name} №${getPrettyOrderNumber(order.order_no)}`,
    },
    no           : {
        header        : '№',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => ORDER_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (order: IMaterialRenderOrder) => getPrettyOrderNumber(order.order_no),
    },
    orderAmount  : {
        header        : 'Кол-во изд. в Заявке',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => ORDER_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (order: IMaterialRenderOrder) => order.order_amount.toString(),
    },
    orderTotal   : {
        header        : 'Общий расход в Заявке',
        width         : TOTAL_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => ORDER_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (order: IMaterialRenderOrder) => order.order_total.toFixed(3),
    },
    size         : {
        header        : 'Размер',
        width         : 'w-[100px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => LINE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (line: IMaterialRenderOrderLine) => `${line.size.width}x${line.size.length}x${line.size.height}`,
    },
    modelCode_1c : {
        header        : 'Название модели',
        width         : 'w-[90px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => LINE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (line: IMaterialRenderOrderLine) => line.model_code_1c,
    },
    modelName    : {
        header        : 'Название модели',
        width         : 'w-[400px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => LINE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : DATA_ALIGN,
        data          : (line: IMaterialRenderOrderLine) => line.model_name,
    },
    modelAmount  : {
        header        : 'Количество',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => LINE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (line: IMaterialRenderOrderLine) => line.amount.toString(),
    },
    modelTotal   : {
        header        : 'Всего',
        width         : TOTAL_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => LINE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : 'center',
        dataAlign     : 'center',
        data          : (line: IMaterialRenderOrderLine) => line.total.toFixed(3),
    },

})
</script>

<style scoped>

</style>
