<template>
    <div :class="width" class="select-none m-0.5">
        <div v-if="label" class="flex justify-between items-end mb-1.5 px-1">
            <span class="text-slate-500 uppercase tracking-widest text-[10px] font-bold">
                {{ label }}
            </span>
            <span
                :style="{ color: statusColor }"
                :class="{ 'animate-pulse scale-110': isCritical }"
                class="text-[10px] font-black uppercase transition-all duration-300"
            >
                {{ deviationStatus }}
            </span>
        </div>

        <div
            :class="[height, width, 'bg-slate-900 border border-slate-700/50 rounded-[3px] overflow-hidden relative flex items-center shadow-inner']"
        >
            <div class="absolute left-1/2 top-0 bottom-0 w-px bg-white/20 z-20"></div>

            <div class="absolute inset-0 z-30 flex items-center justify-center pointer-events-none">
                <span class="text-white text-[10px] font-black uppercase tracking-tighter drop-shadow-md">
                   {{ props.deviation > 0 ? '+' : '' }}{{ Math.round(props.deviation) }}%
                </span>
            </div>

            <div class="relative w-full h-full flex">
                <div class="w-1/2 h-full flex justify-end">
                    <div
                        v-if="clampedDeviation < 0"
                        :class="{
                            'animated-stripes': animated,
                            'critical-pulse': isCritical
                        }"
                        :style="{
                            width: Math.abs(clampedDeviation) + '%',
                            backgroundColor: statusColor,
                            boxShadow: `0 0 15px ${statusColor}${isCritical ? '99' : '66'}`
                        }"
                        class="h-full transition-all duration-700 ease-out relative"
                    >
                        <div class="absolute inset-0 bg-white/10 h-[35%]"></div>
                    </div>
                </div>

                <div class="w-1/2 h-full flex justify-start">
                    <div
                        v-if="clampedDeviation > 0"
                        :class="{
                            'animated-stripes': animated,
                            'critical-pulse': isCritical
                        }"
                        :style="{
                            width: clampedDeviation + '%',
                            backgroundColor: statusColor,
                            boxShadow: `0 0 15px ${statusColor}${isCritical ? '99' : '66'}`
                        }"
                        class="h-full transition-all duration-700 ease-out relative"
                    >
                        <div class="absolute inset-0 bg-white/10 h-[35%]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

interface IProps {
    deviation: number
    label?: string
    height?: string
    width?: string
    animated?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    deviation: 0,
    label:    '',
    height:   'h-[24px]',
    width:    'w-[200px]',
    animated: true
})

const clampedDeviation = computed(() => Math.max(-100, Math.min(100, props.deviation)))

// Флаг критического состояния (больше 80% в любую сторону)
const isCritical = computed(() => Math.abs(clampedDeviation.value) >= 80)

const statusColor = computed(() => {
    const d = clampedDeviation.value
    if (d === 0) return '#64748b'
    if (d > 0) return d > 80 ? '#10b981' : '#34d399'
    return d < -80 ? '#ef4444' : '#f97316'
})

const deviationStatus = computed(() => {
    const d = clampedDeviation.value
    if (d === 0) return 'В графике'
    if (d > 0) return d > 80 ? 'Рекорд' : 'Опережение'
    return d < -80 ? 'Критично' : 'Отставание'
})
</script>

<style scoped>
.animated-stripes {
    background-image: linear-gradient(
        45deg,
        rgba(255, 255, 255, 0.15) 25%,
        transparent 25%,
        transparent 50%,
        rgba(255, 255, 255, 0.15) 50%,
        rgba(255, 255, 255, 0.15) 75%,
        transparent 75%,
        transparent
    );
    background-size: 1rem 1rem;
    animation: progress-stripes 1.5s linear infinite;
}

/* Новая анимация пульсации для критических зон */
.critical-pulse {
    animation:
        progress-stripes 1.5s linear infinite,
        danger-glow 1.5s ease-in-out infinite alternate;
}

@keyframes progress-stripes {
    from { background-position: 1rem 0; }
    to { background-position: 0 0; }
}

@keyframes danger-glow {
    from {
        opacity: 0.8;
        filter: brightness(1);
    }
    to {
        opacity: 1;
        filter: brightness(1.4);
    }
}
</style>
