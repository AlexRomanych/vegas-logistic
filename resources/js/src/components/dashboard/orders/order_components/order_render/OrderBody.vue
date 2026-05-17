<template>

    <div v-if="!order.collapsed">
        <TheDividerLine/>
        <div class="min-h-3 bg-red-50 rounded-[4px]"></div>
    </div>

    <div :class="!order.collapsed ? 'bg-green-100 rounded-[4px]' : ''">

        <!-- __ Переход по двойному клику на карточку -->
        <div class="flex" @dblclick="$router.push({name: 'orders.card', params: {id: order.id}})">

            <!-- __ Collapsed -->
            <AppLabelTSWrapper :arg="order" :render-object="render.collapsed"
                               @click="render.collapsed.click!(order)"/>

            <!-- __ ID -->
            <AppLabelTSWrapper :arg="order" :render-object="render.id"/>

            <!-- __ Клиент -->
            <AppLabelTSWrapper :arg="order" :render-object="render.client"/>

            <!-- __ Номер Заявки -->
            <AppLabelTSWrapper :arg="order" :render-object="render.orderNoStr"/>

            <!-- __ Тип элементов (изделий) -->
            <AppLabelTSWrapper :arg="order" :render-object="render.elementsType"/>

            <!-- __ Общее количество элементов (изделий) -->
            <AppLabelTSWrapper :arg="order" :render-object="render.orderAmount"/>

            <!-- __ Период, к которому относится Заявка -->
            <AppLabelTSWrapper :arg="order" :render-object="render.orderPeriod"/>

            <!-- __ Active -->
            <AppLabelTSWrapper :arg="order" :render-object="render.orderActive"/>

            <!-- __ Прогнозный -->
            <AppLabelTSWrapper :arg="order" :render-object="render.isForecast"/>

            <!-- __ Отображаемый в Планах -->
            <AppLabelTSWrapper :arg="order" :render-object="render.isShown"/>

            <!-- __ Дата загрузки на складе Вегас -->
            <AppLabelTSWrapper :arg="order" :render-object="render.loadAt"/>

            <!-- __ Дата разгрузки на складе клиента -->
            <AppLabelTSWrapper :arg="order" :render-object="render.unloadAt"/>

            <!-- __ Комментарий из 1С -->
            <AppLabelTSWrapper :arg="order" :render-object="render.comment_1c"/>

            <!-- __ Описание -->
            <AppLabelTSWrapper :arg="order" :render-object="render.description"/>

            <!-- __ Распечатать -->
            <AppLabelTSWrapper :arg="order" :render-object="render.order_print" @click="emits('printOrder', order)"/>

            <!-- __ Удалить -->
            <AppLabelTSWrapper :arg="order" :render-object="render.order_service" @click="emits('deleteOrder', order)"/>

        </div>

        <!-- __ Сами данные по Содержимому Заявки -->
        <div v-if="!order.collapsed">
            <OrderLines
                :order-lines="order.lines"
                @delete-order-line="deleteOrderLine"
            />
            <div class="min-h-3"></div>
            <TheDividerLine/>
        </div>
    </div>

</template>

<script lang="ts" setup>
import type { IRenderData, IRenderOrder, IRenderOrderLine } from '@/types'

import TheDividerLine from '@/components/ui/dividers/TheDividerLine.vue'
import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
import OrderLines from '@/components/dashboard/orders/order_components/order_render/OrderLines.vue'

interface IProps {
    order: IRenderOrder
    render: IRenderData
}

const props = defineProps<IProps>()

const emits = defineEmits<{
    (e: 'printOrder', payload: IRenderOrder): void,
    (e: 'deleteOrder', payload: IRenderOrder): void,
    (e: 'deleteOrderLine', payload: IRenderOrderLine): void,
}>()

const deleteOrderLine = (orderLine: IRenderOrderLine) => {
    emits('deleteOrderLine', orderLine)
}

</script>

<style scoped>

</style>
