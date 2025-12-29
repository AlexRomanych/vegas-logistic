<template>
    <div class="w-full max-w-xl mx-auto p-4">

        <div
            v-if="!selectedFile && !isUploading"
            class="flex items-center justify-center w-full"
            @dragover.prevent
            @dragenter.prevent
            @drop.prevent="onFileDrop"
        >
            <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 hover:bg-slate-100 hover:border-indigo-400 transition-all cursor-pointer group">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-10 h-10 mb-3 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="mb-2 text-sm text-slate-700 font-semibold text-center px-4">
                        Перетащите Excel сюда или кликните
                    </p>
                </div>

                <input type="file" class="hidden" accept=".xlsx, .xls" @change="onFileSelect" />
            </label>
        </div>

    </div>
</template>

<script setup>
import { ref } from 'vue';

const selectedFile = ref(null);

// 1. Метод для выбора через клик (стандартный input)
const onFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) selectedFile.value = file;
};

// 2. Метод для обработки перетаскивания (Drop)
const onFileDrop = (event) => {
    // Получаем файл из объекта события перетаскивания
    const file = event.dataTransfer.files[0];

    if (file) {
        // Проверяем, что это Excel, прежде чем принять
        const isExcel = file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
            file.name.endsWith('.xlsx') ||
            file.name.endsWith('.xls');

        if (isExcel) {
            selectedFile.value = file;
        } else {
            alert('Пожалуйста, выберите файл в формате Excel');
        }
    }
};
</script>
