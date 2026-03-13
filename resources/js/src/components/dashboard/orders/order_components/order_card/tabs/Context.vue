<template>
    <div class="m-2 h-[calc(100vh-200px)] flex flex-col overflow-hidden">
        <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar">

            <OrderLines
                :order-lines="order.lines"
                :show-collapsed="false"
                showDelete
                @delete-line="deleteLine"
            />

        </div>
    </div>
</template>

<script setup lang="ts">
import type { IRenderOrder } from '@/types'
import OrderLines from '@/components/dashboard/orders/order_components/order_render/OrderLines.vue'

interface IProps {
    order: IRenderOrder
    id: number
}

const props = defineProps<IProps>()

const emits = defineEmits<{
    (e: 'delete-line', id: number): void
}>()

// const deleteLine = (orderLineId: number) => console.log(orderLineId)
const deleteLine = (orderLineId: number) => emits('delete-line', orderLineId)


</script>

<style scoped>
    /* Добавим стилизацию скроллбара, чтобы он не выглядел стандартным в синем интерфейсе */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        @apply bg-blue-950/20;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        @apply bg-blue-600/50 rounded-full hover:bg-blue-500 transition-colors;
    }

    /* Для Firefox */
    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: rgba(37, 99, 235, 0.5) transparent;
    }
</style>
