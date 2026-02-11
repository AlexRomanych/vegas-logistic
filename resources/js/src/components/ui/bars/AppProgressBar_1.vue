<template>
    <div class="w-full select-none">
        <div v-if="label" class="flex justify-between items-end mb-1.5 px-1">
      <span class="text-slate-500 uppercase tracking-widest text-[10px] font-bold">
        {{ label }}
      </span>
        </div>

        <div
            :class="[
        height,
        'w-full bg-slate-900 border border-slate-700/50 rounded-lg overflow-hidden relative shadow-inner flex items-center'
      ]"
        >
            <div class="absolute inset-0 z-10 flex items-center justify-center pointer-events-none">
        <span class="text-[10px] font-black uppercase tracking-tighter transition-colors duration-500"
              :class="clampedProgress > 55 ? 'text-white' : 'text-slate-500'">
           {{ internalText || `${Math.round(clampedProgress)}%` }}
        </span>
            </div>

            <div
                class="h-full transition-all duration-500 ease-out relative"
                :class="[
          animated ? 'animated-stripes' : '',
          progressColor
        ]"
                :style="{
          width: clampedProgress + '%',
          boxShadow: clampedProgress > 0 ? `0 0 20px ${glowColor}` : 'none'
        }"
            >
                <div class="absolute inset-0 bg-white/5 h-[40%]"></div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

interface IProps {
    progress: number
    label?: string
    internalText?: string // Свой текст вместо процентов
    height?: string
    animated?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    progress: 0,
    label: '',
    internalText: '',
    height: 'h-10', // Сделал чуть выше, чтобы текст влез
    animated: true
})

const clampedProgress = computed(() => Math.max(0, Math.min(100, props.progress)))

const progressColor = computed(() => {
    if (clampedProgress.value >= 100) return 'bg-emerald-500'
    return 'bg-blue-600'
})

const glowColor = computed(() => {
    return clampedProgress.value >= 100 ? 'rgba(16, 185, 129, 0.4)' : 'rgba(37, 99, 235, 0.3)'
})
</script>

<style scoped>
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
    background-size: 1rem 1rem;
    animation: progress-stripes 1.5s linear infinite;
}

@keyframes progress-stripes {
    from { background-position: 1rem 0; }
    to { background-position: 0 0; }
}
</style>
