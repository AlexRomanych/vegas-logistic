<template>
    <div class="print-container">
        <!-- Кнопка, которая не пойдет на печать -->
        <button class="no-print print-btn" @click="handlePrint">Распечатать сменное задание</button>

        <header class="report-header mb-2">
            <h1>Сменное задание участка <span class="font-semibold">Пошива</span> от <span
                class="font-semibold">{{ formatDateInFullFormat(metaData.action_at) }}</span></h1>
            <h1>Сменное задание: <span class="font-semibold">{{ metaData.task_title }}</span></h1>
            <h1>Группа ШМ: <span class="font-semibold">{{ metaData.sewing_group }}</span></h1>
            <!--<p>Дата печати: <span class="font-semibold">{{ new Date().toLocaleDateString() }}</span></p>-->
            <p>Дата печати: <span class="font-semibold">{{ formatDateInFullFormat(new Date().toDateString()) }}</span></p>
        </header>

        <table class="production-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Размер</th>
                <th>Модель чехла</th>
                <th>шт.</th>
                <!--<th>Время</th>-->
                <th>ШМ</th>
                <th>Ткань</th>
                <th>ТКЧ</th>
                <th>Кант</th>
                <th>КДЧ</th>
                <th>Состав</th>
                <th>Прим. 1</th>
                <th>Прим. 2</th>
                <th>Прим. 3</th>
                <th>Заявка</th>
                <!--<th>Отметка</th>-->
            </tr>
            </thead>

            <tbody v-for="(subgroup, idx) of printData" :key="idx" class="group-section">
            <template v-if="subgroup.lines.length !== 0">
                <tr class="group-header">
                    <td colspan="14">{{ subgroup.subgroupOrderTitle }}: {{ subgroup.subgroupName }} -
                        Всего: {{ subgroup.amount.total }} шт. ({{ formatTimeWithLeadingZeros(subgroup.time.total) }}) /
                        Выполнено: {{ subgroup.amount.done }} шт. ({{ formatTimeWithLeadingZeros(subgroup.time.done) }}) /
                        Не выполнено: {{ subgroup.amount.incomplete }} шт. ({{ formatTimeWithLeadingZeros(subgroup.time.incomplete) }})
                    </td>
                    <!--<td colspan="14">{{ subgroup.subgroupOrderTitle }}: {{ subgroup.subgroupName }}</td>-->
                </tr>
                <tr v-for="(line, index) of subgroup.lines" :key="line.id">
                    <td class="data-td">{{ index + 1 }}</td>
                    <td class="data-td">{{ getCoverSizeString(line) }}</td>
                    <td>{{ getSewingTaskModelCoverName(line) }}</td>
                    <td class="data-td">{{ line.amount }}</td>
                    <!--<td class="time-cell">{{ time(line) }}</td>-->
                    <td class="data-td">{{ sewingMachine(line) }}</td>
                    <td class="data-td">{{ line.order_line.textile ?? '' }}</td>
                    <td class="data-td">{{ line.order_line.model.main.tkch ?? '' }}</td>
                    <td class="data-td">{{ line.order_line.model.main.kant ?? '' }}</td>
                    <td class="data-td">{{ line.order_line.model.main.kdch ?? '' }}</td>
                    <td>{{ line.order_line.composition ?? '' }}</td>
                    <td>{{ line.order_line.describe_1 ?? '' }}</td>
                    <td>{{ line.order_line.describe_2 ?? '' }}</td>
                    <td>{{ line.order_line.describe_3 ?? '' }}</td>
                    <td>{{ subgroup.subgroupOrderTitle ?? '' }}</td>

                    <!--<td class="empty-cell"></td>-->
                </tr>
            </template>
            </tbody>
        </table>
    </div>
</template>


<script lang="ts" setup>
import type { ISewingTaskLine, ISewingTaskLinesSubgroup } from '@/types'

import { onMounted, ref } from 'vue'

import { getCoverSizeString, getSewingLineMachineType, getSewingTaskModelCoverName, /*getTimeString*/ } from '@/app/helpers/manufacture/helpers_sewing.ts'
import { formatDateInFullFormat, formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

import { TASK_TO_PRINT_KEY, TASK_TO_PRINT_META_KEY } from '@/app/constants/common.ts'
import { SEWING_MACHINES } from '@/app/constants/sewing.ts'

const printData = ref<ISewingTaskLinesSubgroup[]>([]) // массив строк для печати
const metaData  = ref<Record<string, string>>({})
// const time = (line: ISewingTaskLine) => getTimeString(line, true).replaceAll('.', '')

const sewingMachine = (sewingLine: ISewingTaskLine) => {
    const machine = getSewingLineMachineType(sewingLine)

    switch (machine) {
        case SEWING_MACHINES.UNIVERSAL:
            return 'У'
        case SEWING_MACHINES.AUTO:
            return 'А'
        case SEWING_MACHINES.SOLID_HARD:
            return 'ГС'
        case SEWING_MACHINES.SOLID_LITE:
            return 'ГП'
    }

    return '??'
}

const handlePrint = () => {
    window.print()
}

onMounted(() => {
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
