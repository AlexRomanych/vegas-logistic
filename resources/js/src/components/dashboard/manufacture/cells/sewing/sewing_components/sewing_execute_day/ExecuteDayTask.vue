<template>
    <div
        class="container bg-slate-100 p-1 text-slate-600 relative overflow-hidden flex flex-col font-sans"
        @contextmenu.prevent="openContextMenu"
    >
        <div class="flex justify-between items-center mb-2">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                    {{
                        `${sewingTask.position}. ${sewingTask.order.client.short_name} №${sewingTask.order.order_no_num}`
                    }}
                </h1>
                <p class="text-sm text-slate-400 font-medium">Выбрано элементов: {{ selectedIds.size }}</p>
            </div>

            <div class="flex gap-0.5">

                <!-- __ Выполнено -->
                <AppLabelTS
                    :disabled="selectedIds.size === 0"
                    :type="selectedIds.size === 0 ? 'dark' : 'success'"
                    :width="MENU_WIDTH"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text="✓ Выполнено"
                    text-size="small"
                    @click="completeSelected"
                />

                <!-- __ Не Выполнено -->
                <AppLabelTS
                    :disabled="selectedIds.size === 0"
                    :type="selectedIds.size === 0 ? 'dark' : 'danger'"
                    :width="MENU_WIDTH"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text="✘ Не выполнено"
                    text-size="small"
                    @click="unCompleteSelected"
                />

                <!-- __ Сброс отметки -->
                <AppLabelTS
                    :disabled="selectedIds.size === 0"
                    :type="selectedIds.size === 0 ? 'dark' : 'stone'"
                    :width="MENU_WIDTH"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text="↺ Сбросить"
                    text-size="small"
                    @click="resetStatus"
                />

                <!-- __ Разбить количество -->
                <AppLabelTS
                    :disabled="selectedIds.size === 0"
                    :type="selectedIds.size === 0 ? 'dark' : 'indigo'"
                    :width="MENU_WIDTH"
                    align="center"
                    class="menu-button"
                    rounded="4"
                    text="⛏ Разбить"
                    text-size="small"
                    @click="unCompleteSelected"
                />

                <!--<button-->
                <!--    :disabled="selectedIds.size === 0"-->
                <!--    class="bg-indigo-600 hover:bg-indigo-700 text-white disabled:bg-gray-200 disabled:text-gray-400 px-6 py-2 rounded-xl font-bold transition-all active:scale-95 shadow-sm shadow-indigo-200"-->
                <!--    @click="completeSelected"-->
                <!--&gt;-->
                <!--    Завершить выбранные-->
                <!--</button>-->
            </div>

        </div>

        <div
            ref="scrollContainer"
            class="py-2 flex-1 overflow-y-auto border border-slate-200 rounded-xl bg-white select-none custom-scroll relative shadow-sm"
        >

            <div v-for="(sewingLine, index) of  sewingLines" :key="sewingLine.id"
                 :class="[
                  selectedIds.has(sewingLine.id) ? 'bg-slate-300 text-slate-900' : 'hover:bg-gray-50',
                  sewingLine.completed ? '' : '',
                ]"
                 :data-task-id="sewingLine.id"
                 class="h-[30px] flex items-center px-6 border-b border-gray-100 transition-colors relative"
                 @mousedown="startSelection(index, $event)"
                 @mouseenter="updateSelection(index)"
            >

                <!-- __ Cтрока СЗ -->
                <ExecuteDayTaskLine
                    :sewing-line="sewingLine"
                />

                <!--class="absolute inset-y-0 left-0 w-1 bg-slate-500 pointer-events-none"-->
                <div
                    v-if="selectedIds.has(sewingLine.id)"

                    class="absolute inset-0 border-l-4 border-r-4 border-slate-500 pointer-events-none animate-select"

                ></div>
            </div>
        </div>

        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="showMenu"
                    ref="menuRef"
                    :style="{ top: `${menuPosition.y}px`, left: `${menuPosition.x}px` }"
                    class="fixed z-[100] w-64 bg-white border border-gray-200 shadow-xl rounded-2xl overflow-hidden py-1.5 backdrop-blur-xl"
                    @click.stop
                >
                    <div
                        class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-gray-50 mb-1">
                        Действия ({{ selectedIds.size }})
                    </div>
                    <button
                        class="w-full flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-indigo-600 hover:text-white transition-colors"
                        @click="handleMenuAction('complete')"
                    >
                        <span class="mr-3 text-lg">✅</span> Завершить
                    </button>
                    <button
                        class="w-full flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-gray-100 transition-colors"
                        @click="handleMenuAction('reset')"
                    >
                        <span class="mr-3 text-lg">⏳</span> Сбросить статус
                    </button>
                    <div class="h-[1px] bg-gray-100 my-1"></div>
                    <button
                        class="w-full flex items-center px-4 py-2.5 text-sm text-rose-500 hover:bg-rose-500 hover:text-white transition-colors"
                        @click="handleMenuAction('delete')"
                    >
                        <span class="mr-3 text-lg">🗑️</span> Удалить
                    </button>
                </div>
            </Transition>
        </Teleport>

        <div class="mt-4 text-[11px] font-bold text-slate-400 flex gap-6 px-2">
            <span class="flex items-center gap-1.5"><span class="bg-gray-200 px-1 rounded text-[10px]">DRAG</span> Выделение</span>
            <span class="flex items-center gap-1.5"><span class="bg-gray-200 px-1 rounded text-[10px]">CTRL</span> Выбор вразнобой</span>
            <span class="flex items-center gap-1.5"><span class="bg-gray-200 px-1 rounded text-[10px]">SHIFT</span> Диапазон</span>
        </div>
    </div>

    <!-- __ Модальное окно для добавления причины не выполнения -->
    <ExecuteDayFalseReason
        ref="executeDayFalseReason"
        :false-reason="falseReason"
        label="Причина не выполнения"
    />

</template>

<script lang="ts" setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'

import type { ISewingTask, ISewingTaskLine } from '@/types'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import {  useRoute, useRouter } from 'vue-router'
import ExecuteDayFalseReason
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute_day/ExecuteDayFalseReason.vue'
import ExecuteDayTaskLine
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute_day/ExecuteDayTaskLine.vue'

interface IProps {
    sewingTask: ISewingTask
}

const props = defineProps<IProps>()
console.log('sewingTask: ', props.sewingTask)

const emits = defineEmits<{
    (e: 'setFinishStatus', payload: number[]): void
    (e: 'setFalseStatus', payload: number[], falseReason: string): void
    (e: 'resetStatus', payload: number[]): void
}>()


const MENU_WIDTH = 'w-[130px]'

const router = useRouter()
const route  = useRoute()


// __ Тип для модального окна изменения Комментария
const falseReason           = ref('')
const executeDayFalseReason = ref<InstanceType<typeof ExecuteDayFalseReason> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

// --- Инициализация данных ---
// const tasks = ref(Array.from({ length: 40 }, (_, i) => ({
//     id:        Date.now() + i,
//     title:     `Подготовить отчет по модулю ${String.fromCharCode(65 + i % 26)}${i}`,
//     completed: false
// })))

// __ Формируем объект выполнения
const sewingLines = ref<ISewingTaskLine[]>([])

const getSewingLines = () => {
    sewingLines.value = props.sewingTask.sewing_lines
        .map(line => ({ ...line, completed: false }))
        .sort((a, b) => a.position - b.position)
}


// --- Состояние выделения ---
const scrollContainer  = ref(null)
const selectedIds      = ref(new Set<number>())
const isDragging       = ref(false)
const startIndex       = ref(null)
const lastClickedIndex = ref(null)

// --- Состояние меню ---
const showMenu     = ref(false)
const menuPosition = ref({ x: 0, y: 0 })
const menuRef      = ref(null)

// --- Логика автоскролла ---
let scrollInterval    = null
const startAutoScroll = (direction) => {
    if (scrollInterval) return
    scrollInterval = setInterval(() => {
        if (scrollContainer.value) {
            scrollContainer.value.scrollTop += direction * 12
        }
    }, 16)
}
const stopAutoScroll  = () => {
    clearInterval(scrollInterval)
    scrollInterval = null
}

// --- Методы выделения ---
const startSelection = (index, event) => {
    // Если клик правой кнопкой — игнорируем здесь (обработает contextmenu)
    if (event.button === 2) return

    isDragging.value = true
    startIndex.value = index

    if (event.shiftKey && lastClickedIndex.value !== null) {
        applyRangeSelection(lastClickedIndex.value, index, event.ctrlKey)
    } else if (event.ctrlKey || event.metaKey) {
        if (selectedIds.value.has(sewingLines.value[index].id)) {
            selectedIds.value.delete(sewingLines.value[index].id)
        } else {
            selectedIds.value.add(sewingLines.value[index].id)
        }
    } else {
        selectedIds.value.clear()
        selectedIds.value.add(sewingLines.value[index].id)
    }
    lastClickedIndex.value = index
}

const updateSelection = (currentIndex) => {
    if (!isDragging.value) return
    applyRangeSelection(startIndex.value, currentIndex, window.event.ctrlKey || window.event.metaKey)
}

const applyRangeSelection = (startIdx, endIdx, isCumulative) => {
    const start = Math.min(startIdx, endIdx)
    const end   = Math.max(startIdx, endIdx)

    if (!isCumulative) selectedIds.value.clear()

    for (let i = start; i <= end; i++) {
        selectedIds.value.add(sewingLines.value[i].id)
    }
}

// --- Обработка мыши и скролла ---
const handleMouseMove = (event) => {
    if (!isDragging.value || !scrollContainer.value) return

    const rect      = scrollContainer.value.getBoundingClientRect()
    const threshold = 50

    if (event.clientY > rect.bottom - threshold) {
        startAutoScroll(1)
    } else if (event.clientY < rect.top + threshold) {
        startAutoScroll(-1)
    } else {
        stopAutoScroll()
    }
}

// --- Контекстное меню ---
const openContextMenu = async (event) => {
    const target = event.target.closest('[data-task-id]')
    if (target) {
        const id = Number(target.dataset.taskId)
        if (!selectedIds.value.has(id)) {
            selectedIds.value.clear()
            selectedIds.value.add(id)
        }
    }

    showMenu.value = true
    await nextTick()

    let x            = event.clientX
    let y            = event.clientY
    const menuWidth  = menuRef.value?.offsetWidth || 250
    const menuHeight = menuRef.value?.offsetHeight || 180

    if (x + menuWidth > window.innerWidth) x -= menuWidth
    if (y + menuHeight > window.innerHeight) y -= menuHeight

    menuPosition.value = { x, y }
}

// --- Действия ---
const handleMenuAction = (action) => {
    if (action === 'complete') completeSelected()
    if (action === 'reset') {
        sewingLines.value.forEach(t => selectedIds.value.has(t.id) && (t.completed = false))
    }
    if (action === 'delete') {
        sewingLines.value = sewingLines.value.filter(t => !selectedIds.value.has(t.id))
        selectedIds.value.clear()
    }
    showMenu.value = false
}


// __ Выполнено
const completeSelected = () => {
    const ids: number[] = []

    // __ Выбираем только не завершенные задачи
    sewingLines.value.forEach(t => {
        if (selectedIds.value.has(t.id)) {
            if (!t.finished_at) {
                ids.push(t.id)
            }
        }
    })

    if (!ids.length) {
        return
    }

    emits('setFinishStatus', ids)
    showMenu.value = false
}


// __ Не Выполнено
const unCompleteSelected = async () => {
    const ids: number[] = []

    // __ Выбираем только задачи с нулевым статусом
    sewingLines.value.forEach(t => {
        if (selectedIds.value.has(t.id)) {
            if (!t.finished_at && !t.false_at) {
                ids.push(t.id)
            }
        }
    })

    if (!ids.length) {
        return
    }

    const answer = await executeDayFalseReason.value?.show()
    if (answer) {
        falseReason.value = executeDayFalseReason.value?.falseReason ?? ''

        if (!falseReason.value) {
            return
        }

        emits('setFalseStatus', ids, falseReason.value)
        showMenu.value = false

    }
}

// __ Не Выполнено
const resetStatus = async () => {
    const ids: number[] = []

    // __ Выбираем только задачи с нулевым статусом
    sewingLines.value.forEach(t => {
        if (selectedIds.value.has(t.id)) {
            if (t.finished_at || t.false_at) {
                ids.push(t.id)
            }
        }
    })

    if (!ids.length) {
        return
    }

    emits('resetStatus', ids)
    showMenu.value = false
}


// const unCompleteSelected = () => {
//     sewingLines.value.forEach(t => {
//         if (selectedIds.value.has(t.id)) {
//             t.finished_at = null
//         }
//         // if (selectedIds.value.has(t.id)) t.completed = true
//     })
//     showMenu.value = false
// }


const stopGlobalSelection = () => {
    isDragging.value = false
    stopAutoScroll()
}

watch(() => props.sewingTask, () => {
    selectedIds.value.clear()   // __ Очистка выделения
    getSewingLines()
}, { deep: true, immediate: true })

// --- Жизненный цикл ---
onMounted(async () => {
    // await router.isReady().then(() => {
    //     const meta = route.meta /*as unknown as string*/
    //     // editMode.value = route.meta.mode === 'edit' // определяем режим работы формы (редактирование или создание)
    //     console.log('meta: ', meta)
    //     route.meta.title = '123'
    // })

    // getSewingLines()    // __ Инициализация данных

    window.addEventListener('mouseup', stopGlobalSelection)
    window.addEventListener('mousemove', handleMouseMove)
    window.addEventListener('click', () => (showMenu.value = false))
})

onUnmounted(() => {
    window.removeEventListener('mouseup', stopGlobalSelection)
    window.removeEventListener('mousemove', handleMouseMove)
})


</script>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    width: 6px;
}

.custom-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scroll::-webkit-scrollbar-thumb {
    background: #e2e8f0; /* Светлый скролл */
    border-radius: 10px;
}

.custom-scroll::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.1s ease, transform 0.1s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: translateY(-8px) scale(0.95);
}

.select-none {
    user-select: none;
    -webkit-user-select: none;
}

/* Измененная анимация выделения для светлой темы */
@keyframes select-blink-light {
    0% {
        background-color: rgba(79, 70, 229, 0.05);
    }
    100% {
        background-color: rgba(79, 70, 229, 0.1);
    }
}

.animate-select {
    animation: select-blink-light 0.3s ease-out forwards;
}


.container {
    /*
    overflow: auto; !* Включаем скролл для всего контейнера *!
    position: relative;
    */

    /* Высота: из всей высоты экрана вычитаем сумму хедера и футера */
    height: calc(100vh - var(--header-height) - var(--footer-height) - 140px);
    /* Ширина: из всей ширины вычитаем ширину сайдбара */
    /*
    width: calc(100vw - var(--sidebar-width) - 15px);

    @apply border-2 border-gray-300 rounded-md p-1;
    */
}

@keyframes select-blink {
    0% {
        opacity: 0;
        transform: scaleX(0.98);
    }
    50% {
        opacity: 1;
        background-color: rgba(0, 0, 0, 0.3);
    }
    100% {
        opacity: 1;
        transform: scaleX(1);
    }
}

.animate-select {
    animation: select-blink 0.4s ease-out forwards;
}

.menu-button {
    @apply cursor-pointer shadow-[0_0_15px_rgba(79,70,229,0.4)];
}

</style>
