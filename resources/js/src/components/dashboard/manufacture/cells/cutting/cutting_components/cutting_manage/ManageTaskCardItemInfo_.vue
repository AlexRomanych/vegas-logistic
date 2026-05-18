<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/90 backdrop-blur-sm p-4">
        <div class="relative w-full max-w-lg bg-[#1e293b] rounded-[2rem] p-8 shadow-2xl border border-slate-700/50">

            <button
                @click="$emit('close')"
                class="absolute -top-4 -right-4 w-10 h-10 bg-[#3b82f6] hover:bg-blue-500 text-white rounded-xl flex items-center justify-center shadow-lg transition-colors font-bold text-xl"
            >
                x
            </button>

            <div class="text-center mb-8">
                <h3 class="text-slate-500 uppercase tracking-widest text-xs font-bold mb-4">
                    Информация о пользователе
                </h3>
                <h2 class="text-white text-2xl font-bold leading-tight">
                    Карточка сотрудника
                </h2>
            </div>

            <div class="space-y-4">

                <div class="bg-[#161e2d] rounded-[1.5rem] p-6 border border-slate-800 flex flex-col items-center justify-center">
                    <span class="text-slate-400 text-sm mb-1">ФИО</span>
                    <div class="text-[#3b82f6] text-3xl font-bold">
                        Иван Иванов
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-[#161e2d] rounded-[1.2rem] p-5 border border-slate-800 flex flex-col items-center">
                        <span class="text-slate-500 text-[10px] uppercase font-bold tracking-tighter mb-2">Возраст</span>
                        <div class="text-white text-3xl font-bold">46</div>
                        <span class="text-slate-500 text-xs mt-1">лет</span>
                    </div>

                    <div class="bg-[#161e2d] rounded-[1.2rem] p-5 border border-slate-800 flex flex-col items-center">
                        <span class="text-slate-500 text-[10px] uppercase font-bold tracking-tighter mb-2">Статус</span>
                        <div class="text-green-400 text-3xl font-bold flex items-center">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                            Активен
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-10">
                <button
                    @click="$emit('close')"
                    class="w-full py-4 bg-[#3b82f6] hover:bg-blue-500 text-white font-bold rounded-2xl transition-all shadow-lg shadow-blue-500/20 active:scale-[0.98]"
                >
                    Закрыть
                </button>
            </div>

        </div>
    </div>
</template>

<script setup lang="ts">
defineEmits(['close']);
</script>

<style scoped>
/* Дополнительные стили для имитации вашего UI */
.bg-slate-900\/90 {
    background-color: rgba(15, 23, 42, 0.9);
}
</style>
