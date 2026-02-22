<template>
    <div :class="width" class="select-none m-0.5">
        <div v-if="label" class="flex justify-between items-end mb-1.5 px-1">
            <span class="text-slate-500 uppercase tracking-widest text-[10px] font-bold">
                {{ label }}
            </span>
            <span :style="{ color: statusColor }" class="text-[10px] font-black uppercase">
                {{ deviationText }}
            </span>
        </div>

        <div
            :class="[height, width, 'bg-slate-900 border border-slate-700/50 rounded-[3px] overflow-hidden relative flex items-center shadow-inner']"
        >
            <div class="absolute left-1/2 top-0 bottom-0 w-px bg-slate-500/50 z-20"></div>

            <div class="absolute inset-0 z-30 flex items-center justify-center pointer-events-none">

                <div :class="textSizeClass" class="text-white text-center font-black tracking-tighter drop-shadow-md">
                    <div>{{ text }}</div>
                    <div>{{ Math.round(deviation) }}%</div>
                </div>

                <!--<span class="text-white text-[10px] font-black uppercase tracking-tighter drop-shadow-md">-->
                <!--   {{ Math.abs(clampedDeviation) }}%-->
                <!--</span>-->
            </div>

            <div class="relative w-full h-full flex">
                <div class="w-1/2 h-full flex justify-end">
                    <div
                        v-if="clampedDeviation < 0"
                        :class="{ 'animated-stripes': animated }"
                        :style="{
                            width: Math.abs(clampedDeviation) * 2 + '%',
                            backgroundColor: '#ef4444',
                            boxShadow: `0 0 15px #ef444466`
                        }"
                        class="h-full transition-all duration-700 ease-out relative"
                    >
                        <div class="absolute inset-0 bg-white/10 h-[35%]"></div>
                    </div>
                </div>

                <div class="w-1/2 h-full flex justify-start">
                    <div
                        v-if="clampedDeviation > 0"
                        :class="{ 'animated-stripes': animated }"
                        :style="{
                            width: clampedDeviation * 2 + '%',
                            backgroundColor: '#10b981',
                            boxShadow: `0 0 15px #10b98166`
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
import type { IFontsType } from '@/app/constants/fontSizes.ts'
import { getFontSizeClass } from '@/app/helpers/helpers.ts'

interface IProps {
    deviation: number // от -50 до 50 (где 0 - в графике)
    text?: string
    label?: string
    height?: string
    width?: string
    animated?: boolean
    textSize?: IFontsType
}

const props = withDefaults(defineProps<IProps>(), {
    deviation: 0,
    text:      '',
    label:     '',
    height:    'h-[30px]',
    width:     'w-[150px]', // Чуть шире для наглядности отклонения
    animated:  true,
    textSize:  'mini'
})

const textSizeClass = computed(() => getFontSizeClass(props.textSize))

// Ограничиваем отклонение, чтобы бар не вылез за границы (макс 50% в одну сторону)
const clampedDeviation = computed(() => Math.max(-50, Math.min(50, props.deviation)))

const statusColor = computed(() => {
    if (clampedDeviation.value < 0) return '#ef4444' // Red (Отставание)
    if (clampedDeviation.value > 0) return '#10b981' // Emerald (Опережение)
    return '#64748b' // Slate (В графике)
})

const deviationText = computed(() => {
    const d = clampedDeviation.value
    if (d === 0) return 'В графике'
    return d > 0 ? 'Опережение' : 'Отставание'
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
