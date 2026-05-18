<template>
    <div
        class="max-h-[800px] bg-slate-950 p-8 text-slate-300 relative overflow-hidden flex flex-col"
        @contextmenu.prevent="openContextMenu"
    >
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Task Manager Pro</h1>
                <p class="text-sm text-slate-500">Выбрано задач: {{ selectedIds.size }}</p>
            </div>
            <div class="flex gap-3">
                <button
                    @click="completeSelected"
                    :disabled="selectedIds.size === 0"
                    class="bg-blue-600 hover:bg-blue-500 disabled:bg-slate-800 disabled:text-slate-600 px-6 py-2 rounded-xl font-bold transition-all active:scale-95"
                >
                    Завершить выбранные
                </button>
            </div>
        </div>

        <div
            ref="scrollContainer"
            class="flex-1 overflow-y-auto border border-slate-800 rounded-lg bg-slate-900 select-none custom-scroll relative"
        >
            <div
                v-for="(task, index) in tasks"
                :key="task.id"
                :data-task-id="task.id"
                @mousedown="startSelection(index, $event)"
                @mouseenter="updateSelection(index)"
                class="h-14 flex items-center px-4 border-b border-slate-800/50 transition-colors relative"
                :class="[
                  selectedIds.has(task.id) ? 'bg-blue-600/20 text-blue-100' : 'hover:bg-slate-800/50',
                  task.completed ? 'opacity-40' : ''
                ]"
            >
                <span class="mr-4 text-xs font-mono text-slate-600 w-6">{{ index + 1 }}</span>

                <div class="flex items-center gap-3 flex-1">
                    <div
                        class="w-5 h-5 border rounded flex items-center justify-center transition-colors"
                        :class="[
                          selectedIds.has(task.id) ? 'bg-blue-500 border-blue-500' : 'border-slate-600',
                          task.completed ? 'bg-green-600 border-green-600' : ''
                        ]"
                    >
                        <span v-if="task.completed" class="text-[10px] text-white">✔</span>
                    </div>
                    <span :class="['font-medium', task.completed ? 'line-through' : '']">
                        {{ task.title }}
                    </span>
                </div>

                <div
                    v-if="selectedIds.has(task.id)"
                    class="absolute inset-0 border-l-2 border-r-2 border-blue-500 pointer-events-none animate-select"
                ></div>
            </div>
        </div>

        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="showMenu"
                    ref="menuRef"
                    :style="{ top: `${menuPosition.y}px`, left: `${menuPosition.x}px` }"
                    class="fixed z-[100] w-64 bg-slate-800/95 border border-slate-700 shadow-2xl rounded-xl overflow-hidden py-1 backdrop-blur-md"
                    @click.stop
                >
                    <div class="px-4 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-700/50 mb-1">
                        Действия ({{ selectedIds.size }})
                    </div>
                    <button
                        @click="handleMenuAction('complete')"
                        class="w-full flex items-center px-4 py-2 text-sm hover:bg-blue-600 hover:text-white transition-colors"
                    >
                        <span class="mr-3">✅</span> Завершить задачи
                    </button>
                    <button
                        @click="handleMenuAction('reset')"
                        class="w-full flex items-center px-4 py-2 text-sm hover:bg-slate-700 transition-colors"
                    >
                        <span class="mr-3">⏳</span> Сбросить статус
                    </button>
                    <div class="h-[1px] bg-slate-700 my-1"></div>
                    <button
                        @click="handleMenuAction('delete')"
                        class="w-full flex items-center px-4 py-2 text-sm text-red-400 hover:bg-red-600 hover:text-white transition-colors"
                    >
                        <span class="mr-3">🗑️</span> Удалить из списка
                    </button>
                </div>
            </Transition>
        </Teleport>

        <div class="mt-4 text-xs text-slate-600 flex gap-6">
            <span>🖱️ Drag для выделения</span>
            <span>⌨️ Ctrl + Click для выбора вразнобой</span>
            <span>⌨️ Shift + Click для диапазонов</span>
            <span>🖱️ ПКМ для меню</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'

// --- Инициализация данных ---
const tasks = ref(Array.from({ length: 40 }, (_, i) => ({
    id: Date.now() + i,
    title: `Подготовить отчет по модулю ${String.fromCharCode(65 + i % 26)}${i}`,
    completed: false
})))

// --- Состояние выделения ---
const scrollContainer = ref(null)
const selectedIds = ref(new Set())
const isDragging = ref(false)
const startIndex = ref(null)
const lastClickedIndex = ref(null)

// --- Состояние меню ---
const showMenu = ref(false)
const menuPosition = ref({ x: 0, y: 0 })
const menuRef = ref(null)

// --- Логика автоскролла ---
let scrollInterval = null
const startAutoScroll = (direction) => {
    if (scrollInterval) return
    scrollInterval = setInterval(() => {
        if (scrollContainer.value) {
            scrollContainer.value.scrollTop += direction * 12
        }
    }, 16)
}
const stopAutoScroll = () => {
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
        if (selectedIds.value.has(tasks.value[index].id)) {
            selectedIds.value.delete(tasks.value[index].id)
        } else {
            selectedIds.value.add(tasks.value[index].id)
        }
    } else {
        selectedIds.value.clear()
        selectedIds.value.add(tasks.value[index].id)
    }
    lastClickedIndex.value = index
}

const updateSelection = (currentIndex) => {
    if (!isDragging.value) return
    applyRangeSelection(startIndex.value, currentIndex, window.event.ctrlKey || window.event.metaKey)
}

const applyRangeSelection = (startIdx, endIdx, isCumulative) => {
    const start = Math.min(startIdx, endIdx)
    const end = Math.max(startIdx, endIdx)

    if (!isCumulative) selectedIds.value.clear()

    for (let i = start; i <= end; i++) {
        selectedIds.value.add(tasks.value[i].id)
    }
}

// --- Обработка мыши и скролла ---
const handleMouseMove = (event) => {
    if (!isDragging.value || !scrollContainer.value) return

    const rect = scrollContainer.value.getBoundingClientRect()
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

    let x = event.clientX
    let y = event.clientY
    const menuWidth = menuRef.value?.offsetWidth || 250
    const menuHeight = menuRef.value?.offsetHeight || 180

    if (x + menuWidth > window.innerWidth) x -= menuWidth
    if (y + menuHeight > window.innerHeight) y -= menuHeight

    menuPosition.value = { x, y }
}

// --- Действия ---
const handleMenuAction = (action) => {
    if (action === 'complete') completeSelected()
    if (action === 'reset') {
        tasks.value.forEach(t => selectedIds.value.has(t.id) && (t.completed = false))
    }
    if (action === 'delete') {
        tasks.value = tasks.value.filter(t => !selectedIds.value.has(t.id))
        selectedIds.value.clear()
    }
    showMenu.value = false
}

const completeSelected = () => {
    tasks.value.forEach(t => {
        if (selectedIds.value.has(t.id)) t.completed = true
    })
    showMenu.value = false
}

const stopGlobalSelection = () => {
    isDragging.value = false
    stopAutoScroll()
}

// --- Жизненный цикл ---
onMounted(() => {
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
    background: #334155;
    border-radius: 10px;
}
.custom-scroll::-webkit-scrollbar-thumb:hover {
    background: #475569;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.15s ease, transform 0.15s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: translateY(-5px) scale(0.98);
}

/* Отключаем стандартное выделение текста во время работы */
.select-none {
    user-select: none;
    -webkit-user-select: none;
}


@keyframes select-blink {
    0% { opacity: 0; transform: scaleX(0.98); }
    50% { opacity: 1; background-color: rgba(59, 130, 246, 0.3); }
    100% { opacity: 1; transform: scaleX(1); }
}

.animate-select {
    animation: select-blink 0.4s ease-out forwards;
}
</style>
