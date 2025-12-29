<template>
    <div class="w-full max-w-xl mx-auto p-4">
        <div v-if="!isUploading" class="flex items-center justify-center w-full">
            <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 cursor-pointer group">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <div class="p-4 bg-indigo-100 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                    <p class="mb-2 text-sm text-slate-700 font-semibold text-center px-4">
                        Кликните для выбора Excel или перетащите файл
                    </p>
                    <p class="text-xs text-slate-500">XLSX, XLS (до 500 000 строк)</p>
                </div>
                <input type="file" class="hidden" accept=".xlsx, .xls" @change="handleFileChange" />
            </label>
        </div>

        <div v-else class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm transition-all duration-500">
            <div class="flex justify-between items-end mb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Импорт данных</h3>
                    <p class="text-sm text-slate-500">{{ statusMessage }}</p>
                </div>
                <span class="text-2xl font-black text-indigo-600">{{ progress }}%</span>
            </div>

            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                <div
                    class="h-full bg-indigo-600 transition-all duration-500 ease-out shadow-[0_0_15px_rgba(79,70,229,0.4)]"
                    :style="{ width: progress + '%' }"
                ></div>
            </div>

            <div class="mt-4 flex items-center justify-center space-x-2 animate-pulse" v-if="progress < 100">
                <div class="w-2 h-2 bg-indigo-400 rounded-full"></div>
                <div class="w-2 h-2 bg-indigo-400 rounded-full"></div>
                <div class="w-2 h-2 bg-indigo-400 rounded-full"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const file = ref(null);
const isUploading = ref(false);
const progress = ref(0);
const statusMessage = ref('');

// Функция для обработки выбора файла
const handleFileChange = (event) => {
    file.value = event.target.files[0];
    if (file.value) {
        uploadFile();
    }
};

const uploadFile = async () => {
    isUploading.value = true;
    progress.value = 0;
    statusMessage.value = 'Загрузка файла на сервер...';

    const formData = new FormData();
    formData.append('file', file.value);

    try {
        // Отправляем файл в Laravel
        await axios.post('/api/import/upload', formData, {
            onUploadProgress: (progressEvent) => {
                // Это прогресс именно загрузки файла в PHP
                progress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            }
        });

        statusMessage.value = 'Файл принят. Rust начинает обработку...';

        // ВАЖНО: После этого прогресс будет обновляться через Laravel Echo (WebSockets)
        listenToSocketProgress();

    } catch (error) {
        statusMessage.value = 'Ошибка при загрузке';
        isUploading.value = false;
    }
};

const listenToSocketProgress = () => {
    // Подключаемся к каналу, который мы обсуждали ранее
    window.Echo.private(`users.${userId}`)
        .listen('.import.progress', (e) => {
            progress.value = e.percent; // Rust шлет нам проценты
            statusMessage.value = e.message;

            if (e.percent === 100) {
                isUploading.value = false;
                statusMessage.value = 'Импорт успешно завершен!';
            }
        });
};
</script>


<style scoped>
</style>
