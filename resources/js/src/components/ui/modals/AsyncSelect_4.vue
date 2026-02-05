<template>
    <div :class="[width]" class="flex flex-col bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-2xl select-none">

        <div v-if="title" class="px-4 py-3 border-b border-slate-700 bg-slate-800/50 text-center">
            <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ title }}</h3>
        </div>

        <div class="relative h-[450px] bg-slate-900/50 group">

            <div
                @click="stepScroll(-1)"
                class="absolute top-2 left-1/2 -translate-x-1/2 z-30 p-2 cursor-pointer text-slate-500 hover:text-blue-400 transition-colors opacity-0 group-hover:opacity-100"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
            </div>

            <div class="absolute top-1/2 left-0 w-full h-[50px] -translate-y-1/2
                        border-y border-blue-500/40 bg-blue-500/5 pointer-events-none z-10 shadow-[0_0_15px_rgba(59,130,246,0.1)]">
            </div>

            <div
                ref="scrollContainer"
                @scroll="handleScroll"
                @mousedown="initAudio"
                class="wheel-scroll h-full overflow-y-auto custom-scrollbar-hidden snap-y snap-mandatory scroll-smooth"
            >
                <div class="h-[200px]"></div>

                <div
                    v-for="item in items"
                    :key="item.id"
                    class="h-[50px] flex items-center justify-center px-6 snap-center transition-all duration-300 cursor-pointer"
                    @click="scrollToItem(item.id)"
                >
                    <span
                        class="text-sm truncate transition-all duration-300"
                        :class="[
                            localSelectedId === item.id
                                ? 'text-blue-400 font-bold scale-125'
                                : 'text-slate-500 opacity-30 hover:opacity-60'
                        ]"
                    >
                        {{ item.name }}
                    </span>
                </div>

                <div class="h-[200px]"></div>
            </div>

            <div
                @click="stepScroll(1)"
                class="absolute bottom-2 left-1/2 -translate-x-1/2 z-30 p-2 cursor-pointer text-slate-500 hover:text-blue-400 transition-colors opacity-0 group-hover:opacity-100"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-20 shadow-overlay"></div>
        </div>

        <div class="p-3 bg-slate-800 border-t border-slate-700">
            <AppInputButton
                id="wheel-confirm"
                title="Подтвердить выбор"
                type="primary"
                width="w-full"
                @buttonClick="handleConfirm"
            />
        </div>
    </div>
</template>

<script lang="ts" setup generic="T extends { id: number | string, name: string }">
import { ref, onMounted } from 'vue'
import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

interface IProps {
    items: T[]
    modelValue?: number | string | null
    title?: string
    width?: string
}

const props = withDefaults(defineProps<IProps>(), {
    width: 'w-[350px]',
    title: 'Выбор схемы'
})

const emit = defineEmits(['update:modelValue', 'confirm'])

const scrollContainer = ref<HTMLElement | null>(null)
const localSelectedId = ref<number | string | null>(props.modelValue || null)

const ITEM_HEIGHT = 50

// --- Логика звука ---
const audioCtx = ref<AudioContext | null>(null)
const initAudio = () => {
    if (!audioCtx.value) {
        audioCtx.value = new (window.AudioContext || (window as any).webkitAudioContext)()
    }
}

const playClick = () => {
    if (!audioCtx.value) return
    if (audioCtx.value.state === 'suspended') audioCtx.value.resume()

    const osc = audioCtx.value.createOscillator()
    const gain = audioCtx.value.createGain()
    osc.type = 'triangle'
    osc.frequency.setValueAtTime(800, audioCtx.value.currentTime)
    osc.frequency.exponentialRampToValueAtTime(100, audioCtx.value.currentTime + 0.03)
    gain.gain.setValueAtTime(0.1, audioCtx.value.currentTime)
    gain.gain.linearRampToValueAtTime(0, audioCtx.value.currentTime + 0.03)
    osc.connect(gain)
    gain.connect(audioCtx.value.destination)
    osc.start()
    osc.stop(audioCtx.value.currentTime + 0.03)
}

// --- Скролл и позиционирование ---
const handleScroll = () => {
    if (!scrollContainer.value) return
    const scrollTop = scrollContainer.value.scrollTop
    const index = Math.round(scrollTop / ITEM_HEIGHT)

    if (props.items[index] && localSelectedId.value !== props.items[index].id) {
        localSelectedId.value = props.items[index].id
        playClick()
    }
}

const scrollToItem = (id: number | string) => {
    const index = props.items.findIndex(i => i.id === id)
    if (index !== -1 && scrollContainer.value) {
        scrollContainer.value.scrollTo({
            top: index * ITEM_HEIGHT,
            behavior: 'smooth'
        })
    }
}

const stepScroll = (direction: number) => {
    if (!scrollContainer.value) return
    const currentScroll = scrollContainer.value.scrollTop
    scrollContainer.value.scrollTo({
        top: currentScroll + (direction * ITEM_HEIGHT),
        behavior: 'smooth'
    })
}

const handleConfirm = () => {
    if (localSelectedId.value !== null) {
        const selectedItem = props.items.find(i => i.id === localSelectedId.value)
        emit('update:modelValue', localSelectedId.value)
        emit('confirm', selectedItem)
    }
}

onMounted(() => {
    if (props.modelValue && scrollContainer.value) {
        const index = props.items.findIndex(i => i.id === props.modelValue)
        if (index !== -1) {
            // Без smooth при загрузке, чтобы не было прыжка
            scrollContainer.value.scrollTop = index * ITEM_HEIGHT
        }
    }
})
</script>

<style scoped>
.custom-scrollbar-hidden::-webkit-scrollbar { display: none; }
.custom-scrollbar-hidden { -ms-overflow-style: none; scrollbar-width: none; }

.snap-y { scroll-snap-type: y mandatory; }
.snap-center { scroll-snap-align: center; }

.shadow-overlay {
    background: linear-gradient(
        to bottom,
        rgb(15, 23, 42) 0%,
        rgba(15, 23, 42, 0) 15%,
        rgba(15, 23, 42, 0) 85%,
        rgb(15, 23, 42) 100%
    );
}

.wheel-scroll {
    mask-image: linear-gradient(
        to bottom,
        transparent 0%,
        black 20%,
        black 80%,
        transparent 100%
    );
}

.scroll-smooth {
    scroll-behavior: smooth;
}
</style>
