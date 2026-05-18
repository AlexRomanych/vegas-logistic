<template>
    <div
        class="max-h-[800px] bg-gray-50 p-8 text-slate-700 relative overflow-hidden flex flex-col font-sans"
        @contextmenu.prevent="openContextMenu"
    >
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Task Manager Pro</h1>
                <p class="text-sm text-slate-400 font-medium">Выбрано задач: {{ selectedIds.size }}</p>
            </div>
            <div class="flex gap-3">
                <button
                    @click="completeSelected"
                    :disabled="selectedIds.size === 0"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white disabled:bg-gray-200 disabled:text-gray-400 px-6 py-2 rounded-xl font-bold transition-all active:scale-95 shadow-sm shadow-indigo-200"
                >
                    Завершить выбранные
                </button>
            </div>
        </div>

        <div
            ref="scrollContainer"
            class="flex-1 overflow-y-auto border border-gray-200 rounded-2xl bg-white select-none custom-scroll relative shadow-sm"
        >
            <div
                v-for="(task, index) in tasks"
                :key="task.id"
                :data-task-id="task.id"
                @mousedown="startSelection(index, $event)"
                @mouseenter="updateSelection(index)"
                class="h-14 flex items-center px-6 border-b border-gray-100 transition-colors relative"
                :class="[
                  selectedIds.has(task.id) ? 'bg-indigo-50 text-indigo-900' : 'hover:bg-gray-50',
                  task.completed ? 'opacity-50' : ''
                ]"
            >
                <span class="mr-4 text-xs font-bold text-slate-300 w-6">{{ index + 1 }}</span>

                <div class="flex items-center gap-4 flex-1">
                    <div
                        class="w-5 h-5 border-2 rounded-md flex items-center justify-center transition-all"
                        :class="[
                          selectedIds.has(task.id) ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300 bg-white',
                          task.completed ? 'bg-emerald-500 border-emerald-500' : ''
                        ]"
                    >
                        <span v-if="task.completed" class="text-[10px] text-white">✔</span>
                    </div>
                    <span :class="['font-semibold text-sm transition-all', task.completed ? 'line-through text-slate-400' : 'text-slate-700']">
                        {{ task.title }}
                    </span>
                </div>

                <div
                    v-if="selectedIds.has(task.id)"
                    class="absolute inset-y-0 left-0 w-1 bg-indigo-500 pointer-events-none"
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
                    <div class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-gray-50 mb-1">
                        Действия ({{ selectedIds.size }})
                    </div>
                    <button
                        @click="handleMenuAction('complete')"
                        class="w-full flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-indigo-600 hover:text-white transition-colors"
                    >
                        <span class="mr-3 text-lg">✅</span> Завершить
                    </button>
                    <button
                        @click="handleMenuAction('reset')"
                        class="w-full flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-gray-100 transition-colors"
                    >
                        <span class="mr-3 text-lg">⏳</span> Сбросить статус
                    </button>
                    <div class="h-[1px] bg-gray-100 my-1"></div>
                    <button
                        @click="handleMenuAction('delete')"
                        class="w-full flex items-center px-4 py-2.5 text-sm text-rose-500 hover:bg-rose-500 hover:text-white transition-colors"
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
}

/* Измененная анимация выделения для светлой темы */
@keyframes select-blink-light {
    0% { background-color: rgba(79, 70, 229, 0.05); }
    100% { background-color: rgba(79, 70, 229, 0.1); }
}

.animate-select {
    animation: select-blink-light 0.3s ease-out forwards;
}
</style>
