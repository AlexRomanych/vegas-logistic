<template>
    <div class="p-6 bg-slate-900 min-h-screen text-slate-200">
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-xl font-bold">Список задач</h2>
            <button
                @click="completeSelectedTasks"
                :disabled="selectedIds.size === 0"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-700 rounded-lg transition-colors"
            >
                Завершить выбранные ({{ selectedIds.size }})
            </button>
        </div>

        <div
            class="space-y-1 select-none"
            @mouseleave="isDragging = false"
        >
            <div
                v-for="(task, index) in tasks"
                :key="task.id"
                @mousedown="startSelection(index)"
                @mouseenter="updateSelection(index)"
                :class="[
          'p-3 border rounded-md transition-all cursor-pointer',
          selectedIds.has(task.id)
            ? 'bg-blue-500/20 border-blue-500 shadow-[inset_0_0_10px_rgba(59,130,246,0.2)]'
            : 'bg-slate-800 border-slate-700 hover:border-slate-500',
          task.completed ? 'opacity-50 line-through' : ''
        ]"
            >
                <div class="flex items-center gap-3">
                    <div :class="['w-4 h-4 border rounded', selectedIds.has(task.id) ? 'bg-blue-500 border-blue-500' : 'border-slate-500']"></div>
                    {{ task.title }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const tasks = ref([
    { id: 1, title: 'Подготовить отчет', completed: false },
    { id: 2, title: 'Собрать данные по API', completed: false },
    { id: 3, title: 'Верстка модального окна', completed: false },
    { id: 4, title: 'Написать тесты', completed: false },
    { id: 5, title: 'Code Review', completed: false },
    { id: 6, title: 'Deploy на прод', completed: false },
])

const selectedIds = ref(new Set())
const isDragging = ref(false)
const startIndex = ref(null)

// Начало выделения
const startSelection = (index) => {
    isDragging.value = true
    startIndex.value = index
    selectedIds.value.clear()
    selectedIds.value.add(tasks.value[index].id)
}

// Протяжка мыши
const updateSelection = (currentIndex) => {
    if (!isDragging.value) return

    const start = Math.min(startIndex.value, currentIndex)
    const end = Math.max(startIndex.value, currentIndex)

    // В Excel выделение заменяет предыдущее при перетаскивании
    selectedIds.value.clear()
    for (let i = start; i <= end; i++) {
        selectedIds.value.add(tasks.value[i].id)
    }
}

// Завершение выделения (глобальное)
const stopSelection = () => {
    isDragging.value = false
}

// Экшн по кнопке
const completeSelectedTasks = () => {
    tasks.value.forEach(task => {
        if (selectedIds.value.has(task.id)) {
            task.completed = true
        }
    })
    selectedIds.value.clear()
}

onMounted(() => {
    window.addEventListener('mouseup', stopSelection)
})

onUnmounted(() => {
    window.removeEventListener('mouseup', stopSelection)
})
</script>

<style scoped>
.select-none {
    user-select: none; /* Запрещаем выделение текста браузером при протяжке */
}
</style>
