<template>
    <div class="print-container">
        <!-- Кнопка, которая не пойдет на печать -->
        <button class="no-print print-btn" @click="handlePrint">Распечатать сменное задание</button>

        <header class="report-header mb-2">
            <div class="flex w-full justify-center gap-3 text-[24px]">

                <h1>Сменное задание Линии №<span class="font-semibold">{{ metaData.block_group }}</span></h1>
                <h1><span class="font-semibold">{{ `${formatDateInFullFormat(metaData.action_at)} (смена ${metaData.change})` }}</span></h1>
            </div>

            <!--<h1>Сменное задание: <span class="font-semibold">{{ metaData.task_title }}</span></h1>-->
            <!--<h1>Группа ШМ: <span class="font-semibold">{{ metaData.cutting_group }}</span></h1>-->
            <!--&lt;!&ndash;<p>Дата печати: <span class="font-semibold">{{ new Date().toLocaleDateString() }}</span></p>&ndash;&gt;-->
            <!--<p>Дата печати: <span class="font-semibold">{{ formatDateInFullFormat(new Date().toDateString()) }}</span></p>-->
        </header>

        <table class="production-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Наименование блока</th>
                <th>Количество, шт.</th>
                <th>Заявка</th>
                <th>Готовность</th>
                <th>Комментарий</th>
                <!--<th>Время</th>-->
            </tr>
            </thead>

            <tbody v-for="(subgroup, idx) of printData" :key="idx" class="group-section">
            <template v-if="subgroup.lines.length !== 0">
                <!--<tr class="group-header">-->
                <!--    <td colspan="14">{{ subgroup.subgroupOrderTitle }}: {{ subgroup.subgroupName }} - -->
                <!--        Всего: {{ subgroup.amount.total }} шт. ({{ formatTimeWithLeadingZeros(subgroup.time.total) }}) /-->
                <!--        Выполнено: {{ subgroup.amount.done }} шт. ({{ formatTimeWithLeadingZeros(subgroup.time.done) }}) /-->
                <!--        Не выполнено: {{ subgroup.amount.incomplete }} шт. ({{ formatTimeWithLeadingZeros(subgroup.time.incomplete) }})-->
                <!--    </td>-->
                <!--    &lt;!&ndash;<td colspan="14">{{ subgroup.subgroupOrderTitle }}: {{ subgroup.subgroupName }}</td>&ndash;&gt;-->
                <!--</tr>-->
                <tr v-for="(line, /*index*/) of subgroup.lines" :key="line.id">
                    <td class="data-td">{{ counter++ }}</td>
                    <td>{{ line.block.name }}</td>
                    <td class="data-td">{{ line.amount }}</td>
                    <td class="data-td">{{ line.groupAttr }}</td>
                    <td class="data-td">{{ formatTimeInFullFormat(line.finished_at) }}</td>
                    <td>{{ line.description }}</td>
                    <!--<td>{{ getCuttingTaskModelCoverName(line) }}</td>-->
                    <!--&lt;!&ndash;<td class="time-cell">{{ time(line) }}</td>&ndash;&gt;-->
                    <!--<td class="data-td">{{ line.order_line.textile ?? '' }}</td>-->
                    <!--<td class="data-td">{{ cuttingMachine(line) }}</td>-->
                    <!--<td class="data-td">{{ line.order_line.model.main.tkch ?? '' }}</td>-->
                    <!--<td class="data-td">{{ line.order_line.model.main.kant ?? '' }}</td>-->
                    <!--<td class="data-td">{{ line.order_line.model.main.kdch ?? '' }}</td>-->
                    <!--<td>{{ line.order_line.composition ?? '' }}</td>-->
                    <!--<td>{{ line.order_line.describe_1 ?? '' }}</td>-->
                    <!--<td>{{ line.order_line.describe_2 ?? '' }}</td>-->
                    <!--<td>{{ line.order_line.describe_3 ?? '' }}</td>-->
                    <!--<td>{{ subgroup.subgroupOrderTitle ?? '' }}</td>-->

                    <!--<td class="empty-cell"></td>-->
                </tr>
            </template>
            </tbody>
        </table>
    </div>
</template>


<script lang="ts" setup>
import type { IBlockTaskLinesSubgroup } from '@/types'

import { onMounted, ref } from 'vue'

import { formatDateInFullFormat, formatTimeInFullFormat, /*formatTimeWithLeadingZeros*/ } from '@/app/helpers/helpers_date'

import { TASK_TO_PRINT_KEY, TASK_TO_PRINT_META_KEY } from '@/app/constants/common.ts'

const printData = ref<IBlockTaskLinesSubgroup[]>([]) // массив строк для печати
const metaData  = ref<Record<string, string>>({})
// const time = (line: ICuttingTaskLine) => getTimeString(line, true).replaceAll('.', '')

let counter = 1

const handlePrint = () => {
    window.print()
}

onMounted(() => {
    counter = 1

    const data = localStorage.getItem(TASK_TO_PRINT_KEY)
    if (data) {
        printData.value = JSON.parse(data)              // Загружаем в Store или локальную переменную
        localStorage.removeItem(TASK_TO_PRINT_KEY)      // СРАЗУ УДАЛЯЕМ, чтобы не висело мертвым грузом
    }

    const meta = localStorage.getItem(TASK_TO_PRINT_META_KEY)
    if (meta) {
        metaData.value = JSON.parse(meta)
        localStorage.removeItem(TASK_TO_PRINT_META_KEY)
    }

    console.log('printData: ', printData.value)
    console.log('metaData: ', metaData.value)

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

.empty-cell {
    width: 80px; /* Место для подписи ручкой */
}

.data-td {
    text-align: center;
}


/* МАГИЯ ПЕЧАТИ */
/* МАГИЯ ПЕЧАТИ */
@media print {
    .no-print {
        display: none !important;
    }

    .print-container {
        padding: 0;
    }

    /* Уменьшаем шрифт всей таблицы при печати (например, до 9px или 8.5px) */
    .production-table {
        font-size: 9px !important;
    }

    /* Если нужно уменьшить отступы в ячейках, чтобы строки были компактнее */
    .production-table th,
    .production-table td {
        padding: 2px 4px !important;
    }

    .production-table th {
        background: #eee !important;
        color: black !important;
    }

    .time-cell {
        background: transparent !important;
        color: black !important;
        border: 2px solid red !important;
    }

    tr {
        page-break-inside: avoid;
    }

    @page {
        margin: 1cm;
        size: portrait;
    }
}
</style>
