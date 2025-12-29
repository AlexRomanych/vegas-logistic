<script setup>
import { ref } from 'vue';

const selectedFile = ref(null);
const isUploading = ref(false);
const progress = ref(0);
const statusMessage = ref('');

// 1. Обработка выбора (но не загрузка!)
const onFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) selectedFile.value = file;
};

// 2. Сброс выбора
const cancelSelection = () => {
    selectedFile.value = null;
};

// 3. Запуск загрузки
const startUpload = async () => {
    isUploading.value = true;
    // ... здесь логика axios.post, которую мы писали ранее ...
    // После завершения не забудьте вызвать listenToSocketProgress()
};
</script>

<template>
    <div class="w-full max-w-xl mx-auto p-4">

        <div v-if="!selectedFile && !isUploading" class="flex items-center justify-center w-full">
            <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 hover:bg-slate-100 hover:border-indigo-400 transition-all cursor-pointer group">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-10 h-10 mb-3 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                    <p class="mb-2 text-sm text-slate-700 font-semibold text-center">Перетащите Excel сюда</p>
                </div>
                <input type="file" class="hidden" accept=".xlsx, .xls" @change="onFileSelect" />
            </label>
        </div>

        <div v-if="selectedFile && !isUploading" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-green-100 rounded-lg text-green-600">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M14.7 2.3H6.3c-1.1 0-2 .9-2 2v15.4c0 1.1.9 2 2 2h11.4c1.1 0 2-.9 2-2V7L14.7 2.3zM16 19H8v-2h8v2zm0-4H8v-2h8v2zm-3-5.5V3.5l3.5 3.5H13z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-900 truncate">{{ selectedFile.name }}</p>
                    <p class="text-xs text-slate-500">{{ (selectedFile.size / 1024 / 1024).toFixed(2) }} MB</p>
                </div>
                <button @click="cancelSelection" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <button
                @click="startUpload"
                class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-indigo-200 flex items-center justify-center space-x-2"
            >
                <span>Начать импорт</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
            </button>
        </div>

        <div v-if="isUploading" class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex justify-between items-end mb-4">
                <span class="text-sm font-bold text-slate-800">{{ statusMessage }}</span>
                <span class="text-2xl font-black text-indigo-600">{{ progress }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                <div class="h-full bg-indigo-600 transition-all duration-500" :style="{ width: progress + '%' }"></div>
            </div>
        </div>

    </div>
</template>
