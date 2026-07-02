<template>


    <div class="flex h-screen w-full bg-slate-900 p-4 gap-4 overflow-hidden">

        <div v-for="panel in ['left', 'right']" :key="panel"
             class="flex flex-col flex-1 bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">

            <div class="flex-none bg-slate-700 p-3 shadow-md">
                <div class="flex justify-between text-slate-300 font-bold px-2">
                    <span class="w-1/3">Наименование</span>
                    <span class="w-1/3 text-center">Кол-во</span>
                    <span class="w-1/3 text-right">Статус</span>
                </div>
            </div>

            <div class="flex-grow overflow-y-auto custom-scrollbar p-2">
                <div v-for="n in 50" :key="n"
                     class="flex justify-between p-3 mb-2 bg-slate-750 hover:bg-slate-600 rounded border border-slate-700 text-slate-200 transition-colors">
                    <span class="w-1/3">Задача #{{ n }}</span>
                    <span class="w-1/3 text-center">{{ Math.floor(Math.random() * 100) }}</span>
                    <span class="w-1/3 text-right text-green-400">В работе</span>
                </div>
            </div>

            <div class="flex-none bg-slate-700 p-3 border-t border-slate-600">
                <div class="flex justify-between text-white font-semibold px-2">
                    <span>Всего позиций:</span>
                    <span>50 шт.</span>
                </div>
            </div>

        </div>

    </div>


</template>

<script lang="ts" setup>

</script>

<style scoped>
/* Чтобы панели были именно 50/50 и не схлопывались */
.flex-1 {
    flex: 1 1 0;
    min-width: 0;
}

/* Кастомный скроллбар, как мы делали ранее */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    @apply bg-slate-800;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-600 rounded-full;
}
</style>
