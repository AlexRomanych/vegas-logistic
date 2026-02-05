<template>
    <div :class="[width, height]" class="flex flex-col bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-xl">

        <div v-if="title" class="px-4 py-3 border-b border-slate-700 bg-slate-800/50">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ title }}</h3>
        </div>

        <div class="p-2 bg-slate-800/80 border-b border-slate-700/50">
            <div class="relative">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Быстрый поиск..."
                    class="w-full bg-slate-900 border border-slate-700 rounded-lg py-2 pl-9 pr-4 text-sm text-slate-200 focus:outline-none focus:border-blue-500 transition-colors"
                >
                <span class="absolute left-3 top-2.5 text-slate-500 text-sm">🔍</span>
                <button
                    v-if="searchQuery"
                    @click="searchQuery = ''"
                    class="absolute right-3 top-2.5 text-slate-500 hover:text-slate-300"
                >✕</button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1 bg-slate-900/30">
            <div
                v-for="item in filteredItems"
                :key="item.id"
                @click="localSelectedId = item.id"
                class="group flex items-center justify-between px-4 py-2.5 rounded-lg cursor-pointer transition-all duration-200"
                :class="[
                    localSelectedId === item.id
                        ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20'
                        : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'
                ]"
            >
                <div class="flex flex-col">
                    <span class="text-sm font-medium">{{ item.name }}</span>
                    <span v-if="item.description"
                          class="text-[10px] uppercase opacity-70 font-semibold"
                          :class="localSelectedId === item.id ? 'text-blue-100' : 'text-slate-500'"
                    >
                        {{ item.description }}
                    </span>
                </div>

                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-colors"
                     :class="localSelectedId === item.id ? 'border-white' : 'border-slate-600 group-hover:border-slate-500'">
                    <div v-if="localSelectedId === item.id" class="w-2 h-2 rounded-full bg-white"></div>
                </div>
            </div>

            <div v-if="filteredItems.length === 0" class="flex flex-col items-center justify-center h-full opacity-30 py-10">
                <span class="text-sm">{{ items.length === 0 ? 'Список пуст' : 'Ничего не найдено' }}</span>
            </div>
        </div>

        <div class="p-3 bg-slate-800 border-t border-slate-700 flex justify-end">
            <AppInputButton
                id="select-item"
                title="Выбрать"
                :type="localSelectedId ? 'primary' : 'stone'"
                :disabled="!localSelectedId"
                width="w-full"
                @buttonClick="handleConfirm"
            />
        </div>
    </div>
</template>

<script lang="ts" setup generic="T extends { id: number | string, name: string, description?: string }">
import { ref, watch, computed } from 'vue'
import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

interface IProps {
    items: T[]
    modelValue?: number | string | null
    title?: string
    width?: string
    height?: string
}

const props = withDefaults(defineProps<IProps>(), {
    width: 'w-full',
    height: 'h-[400px]',
    title: ''
})

const emit = defineEmits(['update:modelValue', 'confirm'])

// Состояние поиска
const searchQuery = ref('')

// Локальное состояние выделения
const localSelectedId = ref<number | string | null>(props.modelValue || null)

// Вычисляемый отфильтрованный список
const filteredItems = computed(() => {
    if (!searchQuery.value.trim()) return props.items

    const query = searchQuery.value.toLowerCase()
    return props.items.filter(item =>
        item.name.toLowerCase().includes(query) ||
        (item.description && item.description.toLowerCase().includes(query))
    )
})

// Синхронизация, если modelValue придет извне
watch(() => props.modelValue, (newVal) => {
    localSelectedId.value = newVal || null
})

const handleConfirm = () => {
    if (localSelectedId.value !== null) {
        const selectedItem = props.items.find(i => i.id === localSelectedId.value)
        emit('update:modelValue', localSelectedId.value)
        emit('confirm', selectedItem)
    }
}
</script>

<style scoped>
/* Скроллбар и эффекты те же, что и были */
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    @apply bg-transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-700 rounded-full hover:bg-slate-600 transition-colors;
}

.active-scale:active {
    transform: scale(0.98);
}
</style>
