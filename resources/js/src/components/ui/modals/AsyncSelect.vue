<template>
    <div :class="[width]" class="flex flex-col bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-2xl"
         @mousedown="initAudio"
         @touchstart="initAudio"
    >

        <div v-if="title" class="px-4 py-3 border-b border-slate-700 bg-slate-800/50 text-center">
            <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ title }}</h3>
        </div>

        <div class="relative flex h-[300px] bg-slate-900/50">

            <div class="z-30 flex flex-col justify-center px-1 bg-slate-800/30 border-r border-slate-700/50">
                <div
                    v-for="letter in alphabet"
                    :key="letter"
                    @click="scrollToLetter(letter)"
                    class="text-[9px] font-bold text-slate-500 hover:text-blue-400 cursor-pointer py-0.5 px-1 transition-colors text-center"
                >
                    {{ letter }}
                </div>
            </div>

            <div class="relative flex-1 overflow-hidden">

                <div class="absolute top-1/2 left-0 w-full h-[50px] -translate-y-1/2
                            border-y border-blue-500/30 bg-blue-500/5 pointer-events-none z-10">
                </div>

                <div
                    ref="scrollContainer"
                    @scroll="handleScroll"
                    class="wheel-scroll h-full overflow-y-auto custom-scrollbar-hidden snap-y snap-mandatory scroll-smooth"
                >
                    <div class="h-[125px]"></div>

                    <div
                        v-for="item in items"
                        :key="item.id"
                        :data-letter="item.name.charAt(0).toUpperCase()"
                        class="h-[50px] flex flex-col items-center justify-center px-4 snap-center transition-all duration-300"
                        :class="[
                            localSelectedId === item.id
                                ? 'text-blue-400 font-bold scale-110'
                                : 'text-slate-500 opacity-30'
                        ]"
                    >
                        <span class="text-sm truncate w-full text-center">{{ item.name }}</span>
                        <span v-if="localSelectedId === item.id && item.description" class="text-[9px] font-normal opacity-60">
                            {{ item.description }}
                        </span>
                    </div>

                    <div class="h-[125px]"></div>
                </div>

                <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-20 shadow-overlay"></div>
            </div>
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

<script lang="ts" setup generic="T extends { id: number | string, name: string, description?: string }">
import { ref, computed, onMounted } from 'vue'
import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

interface IProps {
    items: T[]
    modelValue?: number | string | null
    title?: string
    width?: string
}

const props = withDefaults(defineProps<IProps>(), {
    width: 'w-[350px]',
    title: 'Выбор технологической схемы'
})

const emit = defineEmits(['update:modelValue', 'confirm'])

const scrollContainer = ref<HTMLElement | null>(null)
const localSelectedId = ref<number | string | null>(props.modelValue || null)
const ITEM_HEIGHT = 30

// Генерируем уникальный список первых букв для навигации
const alphabet = computed(() => {
    const letters = props.items.map(item => item.name.charAt(0).toUpperCase())
    return [...new Set(letters)].sort()
})

// const handleScroll = () => {
//     if (!scrollContainer.value) return
//     const scrollTop = scrollContainer.value.scrollTop
//     const index = Math.round(scrollTop / ITEM_HEIGHT)
//     if (props.items[index]) {
//         localSelectedId.value = props.items[index].id
//     }
// }

const scrollToLetter = (letter: string) => {
    if (!scrollContainer.value) return
    const index = props.items.findIndex(item => item.name.toUpperCase().startsWith(letter))
    if (index !== -1) {
        scrollContainer.value.scrollTop = index * ITEM_HEIGHT
    }
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
            scrollContainer.value.scrollTop = index * ITEM_HEIGHT
        }
    }

    for (let i = 0; i < 100; i++) {
        playClickSound()
    }
})







// __ Настройка звука
// const audioCtx = ref<AudioContext | null>(null)
//
// const playClickSound = () => {
//     if (!audioCtx.value) return
//
//     // Создаем осциллятор для короткого "тика"
//     const osc = audioCtx.value.createOscillator()
//     const gain = audioCtx.value.createGain()
//
//     osc.type = 'sine'
//     osc.frequency.setValueAtTime(150, audioCtx.value.currentTime) // Низкий щелчок
//     osc.frequency.exponentialRampToValueAtTime(40, audioCtx.value.currentTime + 0.05)
//
//     gain.gain.setValueAtTime(0.1, audioCtx.value.currentTime)
//     gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.value.currentTime + 0.05)
//
//     osc.connect(gain)
//     gain.connect(audioCtx.value.destination)
//
//     osc.start()
//     osc.stop(audioCtx.value.currentTime + 0.05)
// }
//
// // __ Инициализация аудио при первом взаимодействии (требование браузеров)
// const initAudio = () => {
//     if (!audioCtx.value) {
//         audioCtx.value = new (window.AudioContext || (window as any).webkitAudioContext)()
//     }
// }





const audioCtx = ref<AudioContext | null>(null);
//
// const initAudio = async () => {
//     if (!audioCtx.value) {
//         audioCtx.value = new (window.AudioContext || (window as any).webkitAudioContext)();
//     }
//     // Если контекст "спит", будим его
//     if (audioCtx.value.state === 'suspended') {
//         await audioCtx.value.resume();
//     }
// };
//
// const playClickSound = () => {
//     if (!audioCtx.value || audioCtx.value.state !== 'running') return;
//
//     const osc = audioCtx.value.createOscillator();
//     const gain = audioCtx.value.createGain();
//
//     // Тип 'square' или 'sawtooth' дает более резкий, заметный звук
//     osc.type = 'sine';
//
//     // Делаем звук выше (800Hz -> 100Hz), чтобы его точно было слышно
//     osc.frequency.setValueAtTime(800, audioCtx.value.currentTime);
//     osc.frequency.exponentialRampToValueAtTime(100, audioCtx.value.currentTime + 0.04);
//
//     // Чуть добавим громкости (0.2 вместо 0.1)
//     gain.gain.setValueAtTime(0.2, audioCtx.value.currentTime);
//     gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.value.currentTime + 0.04);
//
//     osc.connect(gain);
//     gain.connect(audioCtx.value.destination);
//
//     osc.start();
//     osc.stop(audioCtx.value.currentTime + 0.04);
// };








const isAudioReady = ref(false)

const playClickSound = () => {
    if (!audioCtx.value || audioCtx.value.state !== 'running') {
        // Если звук не готов, пробуем разбудить еще раз
        initAudio()
        return
    }

    const osc = audioCtx.value.createOscillator()
    const gain = audioCtx.value.createGain()

    // 'square' дает характерный "игровой" щелчок, 'triangle' - чуть мягче
    osc.type = 'triangle'

    // Частота чуть выше
    osc.frequency.setValueAtTime(1000, audioCtx.value.currentTime)
    osc.frequency.exponentialRampToValueAtTime(10, audioCtx.value.currentTime + 0.03)

    gain.gain.setValueAtTime(0.3, audioCtx.value.currentTime)
    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.value.currentTime + 0.03)

    osc.connect(gain)
    gain.connect(audioCtx.value.destination)

    osc.start()
    osc.stop(audioCtx.value.currentTime + 0.03)
}

const initAudio = async () => {
    if (isAudioReady.value) return

    // Создаем контекст
    if (!audioCtx.value) {
        audioCtx.value = new (window.AudioContext || (window as any).webkitAudioContext)()
    }

    // Ключевой момент: пробуждаем контекст
    if (audioCtx.value.state === 'suspended') {
        await audioCtx.value.resume()
    }

    isAudioReady.value = true
    console.log('Audio Context Ready:', audioCtx.value.state) // Проверь в консоли!

    // Пробный писк при активации (чтобы сразу понять, что заработало)
    playClickSound()
}


















// В handleScroll обязательно оставляем вызов
const handleScroll = () => {
    if (!scrollContainer.value) return;
    const scrollTop = scrollContainer.value.scrollTop;
    const index = Math.round(scrollTop / ITEM_HEIGHT);

    if (props.items[index] && localSelectedId.value !== props.items[index].id) {
        localSelectedId.value = props.items[index].id;
        playClickSound();
    }
};





// const handleScroll = () => {
//     if (!scrollContainer.value) return
//     const scrollTop = scrollContainer.value.scrollTop
//     const index = Math.round(scrollTop / ITEM_HEIGHT)
//
//     if (props.items[index] && localSelectedId.value !== props.items[index].id) {
//         localSelectedId.value = props.items[index].id
//         playClickSound() // Проигрываем звук при смене элемента
//     }
// }













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
        rgba(15, 23, 42, 0) 20%,
        rgba(15, 23, 42, 0) 80%,
        rgb(15, 23, 42) 100%
    );
}

.wheel-scroll {
    mask-image: linear-gradient(
        to bottom,
        transparent 0%,
        black 30%,
        black 70%,
        transparent 100%
    );
}

/* Добавляем плавность прокрутки при клике на буквы */
.scroll-smooth {
    scroll-behavior: smooth;
}
</style>
