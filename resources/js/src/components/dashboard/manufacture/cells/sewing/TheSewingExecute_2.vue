<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const tasks = ref(Array.from({ length: 50 }, (_, i) => ({
    id: i + 1,
    title: `Задача №${i + 1}`,
    completed: false
})))

const scrollContainer = ref(null)
const selectedIds = ref(new Set())
const isDragging = ref(false)
const startIndex = ref(null)
const lastClickedIndex = ref(null) // Для логики Shift

// --- Логика Автоскролла ---
let scrollInterval = null

const startAutoScroll = (direction) => {
    if (scrollInterval) return
    scrollInterval = setInterval(() => {
        if (scrollContainer.value) {
            scrollContainer.value.scrollTop += direction * 10
        }
    }, 16) // ~60fps
}

const stopAutoScroll = () => {
    clearInterval(scrollInterval)
    scrollInterval = null
}

// --- Основные методы ---
const startSelection = (index, event) => {
    isDragging.value = true
    startIndex.value = index

    if (event.shiftKey && lastClickedIndex.value !== null) {
        // Логика Shift: выделяем диапазон от последней активной ячейки
        updateSelection(index, true)
    } else if (event.ctrlKey || event.metaKey) {
        // Логика Ctrl: инвертируем выбор конкретного элемента
        if (selectedIds.value.has(tasks.value[index].id)) {
            selectedIds.value.delete(tasks.value[index].id)
        } else {
            selectedIds.value.add(tasks.value[index].id)
        }
    } else {
        // Обычный клик: сброс и новое выделение
        selectedIds.value.clear()
        selectedIds.value.add(tasks.value[index].id)
    }
    lastClickedIndex.value = index
}

const updateSelection = (currentIndex, isShift = false) => {
    if (!isDragging.value && !isShift) return

    const start = Math.min(startIndex.value, currentIndex)
    const end = Math.max(startIndex.value, currentIndex)

    // Если не зажат Ctrl, очищаем старое перед отрисовкой нового диапазона
    if (!window.event?.ctrlKey && !window.event?.metaKey && !isShift) {
        selectedIds.value.clear()
    }

    for (let i = start; i <= end; i++) {
        selectedIds.value.add(tasks.value[i].id)
    }
}

const handleMouseMove = (event) => {
    if (!isDragging.value || !scrollContainer.value) return

    const rect = scrollContainer.value.getBoundingClientRect()
    const offset = 40 // зона срабатывания у краев

    if (event.clientY > rect.bottom - offset) {
        startAutoScroll(1) // Скролл вниз
    } else if (event.clientY < rect.top + offset) {
        startAutoScroll(-1) // Скролл вверх
    } else {
        stopAutoScroll()
    }
}

const stopSelection = () => {
    isDragging.value = false
    stopAutoScroll()
}

onMounted(() => {
    window.addEventListener('mouseup', stopSelection)
    window.addEventListener('mousemove', handleMouseMove)
})

onUnmounted(() => {
    window.removeEventListener('mouseup', stopSelection)
    window.removeEventListener('mousemove', handleMouseMove)
})
</script>

<template>
    <div class="flex flex-col h-screen bg-slate-950 p-8 text-slate-300">
        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold text-white">Excel-style Tasks</h1>
            <button
                @click="tasks.forEach(t => selectedIds.has(t.id) && (t.completed = true))"
                class="bg-green-600 hover:bg-green-500 px-6 py-2 rounded-xl text-white font-bold transition-all active:scale-95"
            >
                Завершить выбранные
            </button>
        </div>

        <div
            ref="scrollContainer"
            class="flex-1 overflow-y-auto border border-slate-800 rounded-lg bg-slate-900 select-none custom-scroll"
        >
            <div
                v-for="(task, index) in tasks"
                :key="task.id"
                @mousedown="startSelection(index, $event)"
                @mouseenter="updateSelection(index)"
                class="h-12 flex items-center px-4 border-b border-slate-800/50 transition-colors"
                :class="[
          selectedIds.has(task.id) ? 'bg-blue-600/30 text-blue-200' : 'hover:bg-slate-800/50',
          task.completed ? 'opacity-30 line-through' : ''
        ]"
            >
                <span class="mr-4 text-xs text-slate-600 w-6">{{ index + 1 }}</span>
                <span class="font-medium">{{ task.title }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 8px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
.custom-scroll::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>
