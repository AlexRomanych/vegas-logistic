<template>
    <div :class="[width]" class="min flex flex-col bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-2xl">

        <div v-if="title" class="px-4 py-3 border-b border-slate-700 bg-slate-800/50 text-center">
            <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ title }}</h3>
        </div>

        <div class="relative h-[250px] bg-slate-900/50">

            <div class="absolute top-1/2 left-0 w-full h-[50px] -translate-y-1/2
                        border-y border-blue-500/30 bg-blue-500/5 pointer-events-none z-10">
            </div>

            <div
                ref="scrollContainer"
                @scroll="handleScroll"
                class="wheel-scroll h-full overflow-y-auto custom-scrollbar-hidden snap-y snap-mandatory"
            >
                <div class="h-[100px]"></div>

                <div
                    v-for="item in items"
                    :key="item.id"
                    class="h-[50px] flex items-center justify-center px-4 snap-center transition-all duration-300"
                    :class="[
                        localSelectedId === item.id
                            ? 'text-blue-400 font-bold scale-110'
                            : 'text-slate-500 opacity-40 grayscale'
                    ]"
                >
                    <span class="text-sm truncate">{{ item.name }}</span>
                </div>

                <div class="h-[100px]"></div>
            </div>

            <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-20 shadow-overlay"></div>
        </div>

        <div class="p-3 bg-slate-800 border-t border-slate-700">
            <AppInputButton
                id="wheel-confirm"
                title="Выбрать эту схему"
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
    width: 'w-[300px]',
    title: 'Выбор схемы'
})

const emit = defineEmits(['update:modelValue', 'confirm'])

const scrollContainer = ref<HTMLElement | null>(null)
const localSelectedId = ref<number | string | null>(props.modelValue || null)

const ITEM_HEIGHT = 50 // Высота одного элемента в px

const handleScroll = () => {
    if (!scrollContainer.value) return

    // Считаем, какой элемент сейчас в центре
    const scrollTop = scrollContainer.value.scrollTop
    const index = Math.round(scrollTop / ITEM_HEIGHT)

    if (props.items[index]) {
        localSelectedId.value = props.items[index].id
    }
}

const handleConfirm = () => {
    if (localSelectedId.value !== null) {
        const selectedItem = props.items.find(i => i.id === localSelectedId.value)
        emit('update:modelValue', localSelectedId.value)
        emit('confirm', selectedItem)
    }
}

// При монтировании прокручиваем к текущему значению
onMounted(() => {
    if (props.modelValue && scrollContainer.value) {
        const index = props.items.findIndex(i => i.id === props.modelValue)
        if (index !== -1) {
            scrollContainer.value.scrollTop = index * ITEM_HEIGHT
        }
    }
})
</script>

<style scoped>
/* Прячем дефолтный скроллбар */
.custom-scrollbar-hidden::-webkit-scrollbar {
    display: none;
}
.custom-scrollbar-hidden {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Эффект snap-scroll: список "примагничивается" к элементам */
.snap-y {
    scroll-snap-type: y mandatory;
}
.snap-center {
    scroll-snap-align: center;
}

/* Градиентное затухание через маску или наложение */
.shadow-overlay {
    background: linear-gradient(
        to bottom,
        rgba(30, 41, 59, 1) 0%,
        rgba(30, 41, 59, 0) 25%,
        rgba(30, 41, 59, 0) 75%,
        rgba(30, 41, 59, 1) 100%
    );
}

/* Можно использовать маску для более чистого эффекта прозрачности */
.wheel-scroll {
    mask-image: linear-gradient(
        to bottom,
        transparent 0%,
        black 40%,
        black 60%,
        transparent 100%
    );
}
</style>
