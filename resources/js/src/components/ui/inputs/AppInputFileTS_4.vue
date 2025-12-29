<template>
    <div class="w-full max-w-xl mx-auto p-4">

        <div
            v-if="!selectedFile && !isUploading"
            class="relative"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="onFileDrop"
        >
            <label
                :class="[
            'flex flex-col items-center justify-center w-full h-64 border-2 border-dashed rounded-2xl transition-all duration-300 cursor-pointer',
            isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-slate-300 bg-slate-50 hover:bg-slate-100'
        ]"
            >
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg
                        :class="['w-10 h-10 mb-3 transition-colors', isDragging ? 'text-indigo-600' : 'text-slate-400']"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"/>
                    </svg>
                    <p class="mb-2 text-sm text-slate-700 font-semibold">
                        {{ isDragging ? 'Отпустите файл здесь' : 'Перетащите Excel сюда или кликните' }}
                    </p>
                </div>
                <input accept=".xlsx, .xls" class="hidden" type="file" @change="onFileSelect"/>
            </label>
        </div>

        <div v-if="selectedFile && !isUploading"
             class="bg-white border-2 border-indigo-100 rounded-2xl p-6 shadow-xl shadow-indigo-50/50">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-600 rounded-xl text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base font-bold text-slate-900 truncate">
                        {{ selectedFile.name }}
                    </p>
                    <p class="text-xs text-slate-500 font-medium tracking-wide">
                        {{ (selectedFile.size / 1024).toFixed(1) }} KB • Готов к импорту
                    </p>
                </div>
                <button class="p-2 hover:bg-slate-100 rounded-full text-slate-400 hover:text-red-500 transition-all"
                        @click="cancelSelection">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                </button>
            </div>

            <button
                class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-4 rounded-xl transition-all flex items-center justify-center space-x-2 active:scale-[0.98]"
                @click="isUploading = true"
            >
                <span>Начать импорт</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M13 5l7 7-7 7M5 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"/>
                </svg>
            </button>
        </div>

    </div>
</template>

<script setup>
import { ref } from 'vue'

const selectedFile = ref(null)
const isDragging = ref(false) // Для визуального эффекта при наведении
const isUploading = ref(false)
const progress = ref(0)

// Обработка перетаскивания (Drop)
const onFileDrop = (e) => {
    isDragging.value = false
    const file = e.dataTransfer.files[0]
    if (file) validateAndSetFile(file)
}

// Обработка выбора через клик (Change)
const onFileSelect = (e) => {
    const file = e.target.files[0]
    if (file) validateAndSetFile(file)
}

// Общая валидация
const validateAndSetFile = (file) => {
    const name = file.name.toLowerCase()
    if (name.endsWith('.xlsx') || name.endsWith('.xls')) {
        selectedFile.value = file
    } else {
        alert('Файл должен быть в формате Excel (.xlsx или .xls)')
    }
}

const cancelSelection = () => {
    selectedFile.value = null
}
</script>
