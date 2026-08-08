<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal" class="dark-container" @click.self="close">

                <div :class="[width, 'modal-container overflow-hidden']">

                    <!-- Кнопка закрытия (крестик) -->
                    <div class="close-cross-container">
                        <div class="m-1 p-1">
                            <AppInputButton
                                id="close"
                                :type="type"
                                height="w-5"
                                title="x"
                                width="w-[30px]"
                                @buttonClick="close"
                            />
                        </div>
                    </div>

                    <!-- Шапка модалки -->
                    <div class="w-full pl-8 border-b border-slate-800/50 pb-4">
                        <h3 class="text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold mb-1">
                            Аналитика по оборудованию
                        </h3>
                        <h2 class="text-left text-white font-bold text-lg">
                            Сводное количество изделий по типам ШМ (шт.)
                        </h2>
                    </div>

                    <!-- Контент: Кросс-таблица со скроллом -->
                    <div :class="[height, 'overflow-y-auto custom-scrollbar w-full bg-slate-900/20']">
                        <table class="w-full text-left border-collapse table-fixed">
                            <thead>
                            <tr class="border-b border-slate-800/40 bg-slate-950/40 sticky top-0 z-10 backdrop-blur-sm">
                                <th class="pl-8 py-3 text-slate-400 uppercase tracking-wider text-[11px] font-semibold min-w-[250px]">
                                    Сменное Задание
                                </th>
                                <!-- Динамические столбцы для каждого типа машин -->
                                <th
                                    v-for="machine in activeMachines"
                                    :key="machine"
                                    class="px-4 py-3 text-right text-slate-400 tracking-wider uppercase text-[11px] font-semibold w-[80px]"
                                >
                                    <span :class="getMachineBadgeClass(machine)">
                                        {{ formatMachineName(machine) }}
                                    </span>
                                </th>
                                <!-- Последний столбец - сумма по строке task -->
                                <th class="pr-8 py-3 text-right text-slate-400 tracking-wider uppercase text-[11px] font-semibold w-[140px]">
                                    Всего
                                </th>
                            </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-800/30">
                            <tr
                                v-for="task in pivotData.rows"
                                :key="task.taskId"
                                class="hover:bg-slate-800/20 transition-colors group"
                            >
                                <!-- Название Task -->
                                <td class="pl-8 py-3 text-slate-300 text-sm leading-relaxed truncate group-hover:text-white transition-colors">
                                    {{ task.position }}. {{ task.clientName }} №{{ task.orderNo }}
                                </td>

                                <!-- Пересечение: сумма изделий по machine внутри task -->
                                <td
                                    v-for="machine in activeMachines"
                                    :key="machine"
                                    class="px-4 py-3 text-right font-mono font-semibold text-sm text-slate-400"
                                >
                                    {{ task.amounts[machine] > 0 ? formatInteger(task.amounts[machine]) : '' }}
                                </td>

                                <!-- Итог по строке (Task) -->
                                <td class="pr-8 py-3 text-right font-mono text-sm text-blue-400 font-medium">
                                    {{ formatInteger(task.taskTotal) }}
                                </td>
                            </tr>

                            <!-- Если список пуст -->
                            <tr v-if="pivotData.rows.length === 0">
                                <td :colspan="activeMachines.length + 2" class="text-center py-12 text-slate-500 text-sm italic">
                                    Нет доступных технологических задач для анализа
                                </td>
                            </tr>
                            </tbody>

                            <!-- Последняя строка - общая сумма по всем tasks -->
                            <tfoot v-if="pivotData.rows.length > 0" class="border-t-2 border-slate-700 bg-slate-950/40 font-semibold sticky bottom-0 z-10 backdrop-blur-sm">
                            <tr>
                                <td class="pl-8 py-3 text-sm text-slate-300 uppercase tracking-wider">
                                    Итого по всем СЗ:
                                </td>
                                <td
                                    v-for="machine in activeMachines"
                                    :key="machine"
                                    class="px-4 py-3 text-right font-mono text-sm text-emerald-400"
                                >
                                    {{ formatInteger(pivotData.columnsTotal[machine] || 0) }}
                                </td>
                                <td class="pr-8 py-3 text-right font-mono text-sm text-amber-400">
                                    {{ formatInteger(pivotData.grandTotal) }}
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Подвал модалки -->
                    <div class="w-full flex justify-between items-center p-4 bg-slate-800/50 rounded-b-xl border-t border-slate-800/30">
                        <span class="pl-4 text-[11px] text-slate-500 font-mono uppercase tracking-wider">
                            Всего Сменных Заданий в обработке: {{ pivotData.rows.length }}
                        </span>

                        <AppInputButton
                            id="confirm"
                            :type="type"
                            title="Закрыть"
                            @buttonClick="close"
                        />
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script lang="ts" setup>
import { computed, ref, nextTick } from 'vue'
import type { ICuttingTask, ITextileMachineKeys, IColorTypes } from '@/types'
import { getCuttingLineMachineType } from '@/app/helpers/manufacture/helpers_cutting.ts'
import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import { AUTO, AVERAGE, SOLID_HARD, SOLID_LITE, UNDEFINED, UNIVERSAL } from '@/app/constants/textile_common.ts'

interface IProps {
    type?: IColorTypes
    width?: string
    height?: string
}

withDefaults(defineProps<IProps>(), {
    type: 'primary',
    width: 'min-w-[1000px] max-w-[1000px]',
    height: 'max-h-[800px]',
})

const activeMachines: ITextileMachineKeys[] = [
    UNIVERSAL,
    AUTO,
    SOLID_HARD,
    SOLID_LITE,
    AVERAGE,
    UNDEFINED
]

interface IPivotRow {
    taskId: number
    position: number
    clientName: string
    orderNo: number
    amounts: Record<ITextileMachineKeys, number>
    taskTotal: number
}

const showModal = ref(false)
const tasksContext = ref<ICuttingTask[]>([])
let resolvePromise: ((value: boolean) => void) | null

const pivotData = computed(() => {
    const rows: IPivotRow[] = []
    const columnsTotal = {} as Record<ITextileMachineKeys, number>
    activeMachines.forEach(m => { columnsTotal[m] = 0 })

    let grandTotal = 0

    tasksContext.value.forEach(task => {
        const taskAmounts = {} as Record<ITextileMachineKeys, number>
        activeMachines.forEach(m => { taskAmounts[m] = 0 })

        let taskTotal = 0

        // Используем Map/Set, чтобы собрать уникальные order_line внутри текущего Task
        // и правильно определить для них привязанную швейную машину.
        const uniqueOrderLinesMap = new Map<number, { amount: number, machine: ITextileMachineKeys }>()

        task.cutting_lines.forEach(line => {
            const oLine = line.order_line
            if (!oLine) return

            // Если эту строку заказа мы в рамках таска еще не обсчитывали
            if (!uniqueOrderLinesMap.has(oLine.id)) {
                const machine = getCuttingLineMachineType(line) || UNDEFINED
                uniqueOrderLinesMap.set(oLine.id, {
                    amount: oLine.amount || 0,
                    machine
                })
            }
        })

        // Теперь суммируем штуки по уникальным строкам заказа
        uniqueOrderLinesMap.forEach((data) => {
            if (data.machine in taskAmounts) {
                taskAmounts[data.machine] += data.amount
                taskTotal += data.amount

                columnsTotal[data.machine] += data.amount
                grandTotal += data.amount
            }
        })

        rows.push({
            taskId: task.id,
            position: task.position,
            clientName: task.order.client?.short_name || 'Неизвестен',
            orderNo: task.order.order_no_num,
            amounts: taskAmounts,
            taskTotal
        })
    })

    return {
        rows,
        columnsTotal,
        grandTotal
    }
})

const show = async (inTasks: ICuttingTask[]) => {
    await nextTick()
    tasksContext.value = inTasks
    showModal.value    = true
    return new Promise<boolean>((resolve) => {
        resolvePromise = resolve
    })
}

const close = () => {
    if (resolvePromise) {
        resolvePromise(true)
        showModal.value = false
        resolvePromise  = null
    }
}

// Форматирование для целых чисел (штук), без плавающей точки
const formatInteger = (num: number): string => {
    return new Intl.NumberFormat('ru-RU', {
        maximumFractionDigits: 0
    }).format(num)
}

const formatMachineName = (machine: ITextileMachineKeys | null): string => {
    if (!machine) return 'н/д'
    const names: Record<string, string> = {
        [UNIVERSAL]:  'УШМ',
        [AUTO]:       'АШМ',
        [SOLID_HARD]: 'ГС',
        [SOLID_LITE]: 'ГП',
        [AVERAGE]:    'Средняя',
        [UNDEFINED]:  'Н/Д'
    }
    return names[machine] || machine
}

const getMachineBadgeClass = (machine: ITextileMachineKeys | null): string => {
    const base = 'px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-tight border '
    if (!machine || machine === UNDEFINED) {
        return base + 'bg-slate-900/60 border-slate-800 text-slate-500'
    }
    const styles: Record<string, string> = {
        [UNIVERSAL]:  'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
        [AUTO]:       'bg-blue-500/10 border-blue-500/20 text-blue-400',
        [SOLID_HARD]: 'bg-amber-500/10 border-amber-500/20 text-amber-400',
        [SOLID_LITE]: 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400',
        [AVERAGE]:    'bg-purple-500/10 border-purple-500/20 text-purple-400'
    }
    return styles[machine] || (base + 'bg-slate-800 border-slate-700 text-slate-300')
}

defineExpose({ show })
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-700 rounded-full;
}

.dark-container {
    @apply z-[999] bg-slate-900/80 fixed w-screen h-screen top-0 left-0 flex justify-center items-center backdrop-blur-sm
}

.modal-container {
    @apply bg-slate-800 rounded-xl flex flex-col border-l-8 border-blue-500 shadow-2xl
}

.close-cross-container {
    @apply flex justify-end w-full
}

.modal-enter-active,
.modal-leave-active {
    transition: all 0.5s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(1.10);
}
</style>
