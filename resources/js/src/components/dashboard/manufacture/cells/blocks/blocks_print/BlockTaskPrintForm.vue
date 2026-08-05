<template>
    <div class="print-container" :style="{ '--print-font-size': `${fontSize}px` }">

        <!-- Панель управления (Кнопка печати + Кнопки шрифта) -->
        <div class="no-print controls-panel">
            <button class="print-btn" @click="handlePrint">Распечатать сменное задание</button>

            <div class="zoom-controls">
                <span>Шрифт печати: <b>{{ fontSize }}px</b></span>
                <button class="zoom-btn" @click="changeFontSize(-1)">-</button>
                <button class="zoom-btn" @click="changeFontSize(1)">+</button>
                <button class="zoom-btn reset-btn" @click="fontSize = DEFAULT_FONT_SIZE; counter = 1">Сброс</button>
            </div>
        </div>

        <!-- Кнопка, которая не пойдет на печать -->
        <!--<button class="no-print print-btn" @click="handlePrint">Распечатать сменное задание</button>-->

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
                    <!--<td class="data-td">{{ formatTimeInFullFormat(line.finished_at) }}</td>-->
                    <td class="data-td"></td>
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

import { formatDateInFullFormat, /*formatTimeInFullFormat*, formatTimeWithLeadingZeros*/ } from '@/app/helpers/helpers_date'

import { TASK_TO_PRINT_KEY, TASK_TO_PRINT_META_KEY } from '@/app/constants/common.ts'

const printData = ref<IBlockTaskLinesSubgroup[]>([]) // массив строк для печати
const metaData  = ref<Record<string, string>>({})
// const time = (line: ICuttingTaskLine) => getTimeString(line, true).replaceAll('.', '')

let counter = 1

// Состояние размера шрифта для печати (по умолчанию 9px)
const DEFAULT_FONT_SIZE = 15

const fontSize = ref(DEFAULT_FONT_SIZE)

// Функция изменения размера
const changeFontSize = (delta: number) => {
    counter = 1
    const newSize = fontSize.value + delta
    if (newSize >= 10 && newSize <= 20) {
        fontSize.value = newSize
    }
}


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


/* Стили для панели управления кнопками */
.controls-panel {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.zoom-controls {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f1f2f6;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 14px;
}

.zoom-btn {
    width: 28px;
    height: 28px;
    background: #ffffff;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.zoom-btn:hover {
    background: #e4e4e4;
}

.reset-btn {
    width: auto;
    padding: 0 8px;
    font-weight: normal;
    font-size: 12px;
}

/* Стили для экрана */
.print-container {
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    --print-font-size: 15px; /* <--- Добавь эту строку */
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
/* МАГИЯ ПЕЧАТИ */
@media print {
    .no-print {
        display: none !important;
    }

    .print-container {
        padding: 0;
    }

    /* Использование переменной размера шрифта */
    .production-table {
        font-size: var(--print-font-size, 15px) !important;
    }

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
