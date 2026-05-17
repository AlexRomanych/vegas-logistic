<template>
    <template v-if="!isLoading">
        <div class="print-container">
            <!-- Кнопка, которая не пойдет на печать -->
            <button class="no-print print-btn" @click="handlePrint">Распечатать сменное задание</button>

            <header class="report-header mb-2">
                <h1>Заявка <span class="font-semibold">{{ `${order?.client.short_name} №${order?.order_no_str} ` }}</span>, загрузка на складе: <span
                    class="font-semibold">{{ formatDateInFullFormat(order?.load_at) }}</span></h1>
                <h1>Общее количество: <span class="font-semibold">{{ totalAmount }}</span> элементов</h1>
                <h1>Тип изделий: <span class="font-semibold">{{ order?.elements_type_render }}</span></h1>
                <h1>Комментарий из 1С: <span class="font-semibold">{{ order?.comment_1c }}</span></h1>
                <p>Дата печати: <span class="font-semibold">{{ formatDateInFullFormat(new Date().toDateString()) }}</span></p>
            </header>

            <table class="production-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Тип</th>
                    <th>Размер</th>
                    <th>Модель</th>
                    <th>Кол-во</th>
                    <th>Ткань</th>
                    <th>Состав</th>
                    <th>Прим. 1</th>
                    <th>Прим. 2</th>
                    <th>Прим. 3</th>
                </tr>
                </thead>

                <tbody>

                <tr v-for="(line, index) of order?.lines" :key="line.id">
                    <td class="data-td">{{ index + 1 }}</td>
                    <td class="data-td">{{ line.model.model_type }}</td>
                    <td class="data-td">{{ getSizeString(line) }}</td>
                    <td>{{ line.model.name_report }}</td>
                    <td class="data-td">{{ line.amount }}</td>
                    <td class="data-td">{{ line.textile ?? '' }}</td>
                    <td class="data-td">{{ line.composition ?? '' }}</td>
                    <td class="data-td">{{ line.describe_1 ?? '' }}</td>
                    <td class="data-td">{{ line.describe_2 ?? '' }}</td>
                    <td class="data-td">{{ line.describe_3 ?? '' }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </template>
</template>


<script lang="ts" setup>
import type { IRenderOrder, IRenderOrderLine } from '@/types'

import { computed, onMounted, ref } from 'vue'

import { formatDateInFullFormat } from '@/app/helpers/helpers_date'

import { useRoute, useRouter } from 'vue-router'
import { useOrdersStore } from '@/stores/OrdersStore.ts'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'


const route  = useRoute()
const router = useRouter()                 // Определяем роутер

const ordersStore = useOrdersStore()

// __ Подготавливаем переменные
const order     = ref<IRenderOrder | null>(null)
const isLoading = ref(false)
const paramId   = ref<number>(-1)

// __ Общее количество
const totalAmount = computed(() => order.value?.lines.reduce((acc, line) => acc + line.amount, 0) ?? 0)

// __ Форматируем строку размера
const getSizeString = (line: IRenderOrderLine) => {
    return line.size
}

// __ Отправка данных на печать
const handlePrint = () => {
    window.print()
}


// __ Подготавливаем данные
onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await router.isReady().then(() => {
                paramId.value = parseInt(route.params.id as unknown as string, 10)
            })
            order.value = await ordersStore.getOrderById(paramId.value)

            console.log('order.value: ', order.value)
        },
        undefined,
        // false,
    )


    isLoading.value = false
})


</script>


<style scoped>

/* Стили для экрана */
.print-container {
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

.print-btn {
    margin-bottom: 20px;
    padding: 10px 20px;
    background: #4a90e2;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.production-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}

.production-table th {
    background: #4a69bd;
    color: white;
    padding: 8px;
    border: 1px solid #ddd;
    text-align: center;
}

.production-table td {
    padding: 4px 7px;
    border: 1px solid #ccc;
}

/*
.group-header {
    background: #f1f2f6;
    font-weight: bold;
    font-style: italic;
}

.time-cell {
    background: #ff4757;
    color: white;
    font-weight: bold;
    text-align: center;
}
*/

/*.empty-cell {
    width: 80px; !* Место для подписи ручкой *!
}*/



.data-td {
    text-align: center;
}


/* МАГИЯ ПЕЧАТИ */
@media print {
    .no-print {
        display: none !important;
    }

    .print-container {
        padding: 0;
    }

    .production-table th {
        background: #eee !important;
        color: black !important;
    }

    .time-cell {
        background: transparent !important;
        color: black !important;
        border: 2px solid red !important; /* Чтобы выделить время без заливки */
    }

    tr {
        page-break-inside: avoid; /* Чтобы строки не разрывались между страницами */
    }

    @page {
        margin: 1cm;
        size: portrait; /* Альбомная ориентация, так как таблица широкая */
        /*size: landscape;*/
        /* Альбомная ориентация, так как таблица широкая */
    }
}
</style>
