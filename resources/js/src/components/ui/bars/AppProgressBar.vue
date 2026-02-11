<template>
    <div class="w-full select-none">

        <div v-if="label" class="flex justify-between items-end mb-1.5 px-1">
          <span class="text-slate-500 uppercase tracking-widest text-[10px] font-bold">
            {{ label }}
          </span>
        </div>

        <div
            :class="[height, 'w-full bg-slate-900 border border-slate-700/50 rounded-lg overflow-hidden relative flex items-center shadow-inner']">

            <div class="absolute inset-0 z-10 flex items-center justify-center pointer-events-none">
                <span :class="clampedProgress > 40 ? 'text-white' : 'text-slate-400'"
                      class="text-[10px] font-black uppercase tracking-tighter drop-shadow-md transition-colors duration-300">
                   {{ statusText }}
                </span>
            </div>

            <div
                :class="{ 'animated-stripes': animated }"
                :style="{
                  width: clampedProgress + '%',
                  backgroundColor: statusColor,
                  boxShadow: `0 0 15px ${statusColor}66`
                }"
                class="h-full transition-all duration-700 ease-out relative"
            >
                <div class="absolute inset-0 bg-white/10 h-[35%]"></div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

interface IProps {
    progress: number
    label?: string
    height?: string
    animated?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    progress: 0,
    label:    '',
    height:   'h-10',
    animated: true
})

const clampedProgress = computed(() => Math.max(0, Math.min(100, props.progress)))

// __ 1. Логика цвета в зависимости от %
const statusColor = computed(() => {
    const p = clampedProgress.value
    if (p < 30) return '#3b82f6'    // Blue-500 (Начало)
    if (p < 70) return '#06b6d4'    // Cyan-500 (В процессе)
    if (p < 90) return '#eab308'    // Yellow-500 (Почти готово / Внимание)
    return '#10b981'                // Emerald-500 (Завершено)
})

// __ 2. Логика текста внутри
const statusText = computed(() => {
    const p = clampedProgress.value
    if (p === 0) return 'Начало'
    if (p < 30) return `Старт: ${Math.round(p)}%`
    if (p < 80) return `Выполнение: ${Math.round(p)}%`
    if (p < 90) return `Почти готово: ${Math.round(p)}%`
    if (p < 100) return `Финишная прямая: ${Math.round(p)}%`
    return 'Выполнено'
})
</script>

<style scoped>

/* __ Плавные полоски для динамики */
.animated-stripes {
    background-image: linear-gradient(
        45deg,
        rgba(255, 255, 255, 0.1) 25%,
        transparent 25%,
        transparent 50%,
        rgba(255, 255, 255, 0.1) 50%,
        rgba(255, 255, 255, 0.1) 75%,
        transparent 75%,
        transparent
    );
    background-size: 1.5rem 1.5rem;
    animation: progress-stripes 2s linear infinite;
}

@keyframes progress-stripes {
    from {
        background-position: 1.5rem 0;
    }
    to {
        background-position: 0 0;
    }
}
</style>
