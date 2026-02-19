<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal" class="dark-container" @click.self="select(false)">
                <div :class="[width, height, borderColor, 'modal-container ']">

                    <div class="close-cross-container">
                        <div class="m-1 p-1">
                            <AppInputButton
                                id="close"
                                :type="type"
                                height="w-5"
                                title="x"
                                width="w-[30px]"
                                @buttonClick="select(false)"
                            />
                        </div>
                    </div>

                    <!--<div :class="[width, height]" class="flex flex-col bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-xl">-->

                    <div v-if="title" class="px-4 pb-3 border-b border-slate-700 bg-slate-800/50">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ title }}</h3>
                    </div>

                    <div class="p-2 bg-slate-800/80 border-b border-slate-700/50">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                class="w-full bg-slate-900 border border-slate-700 rounded-lg py-2 pl-9 pr-4 text-sm text-slate-200 focus:outline-none focus:border-blue-500 transition-colors"
                                placeholder="Быстрый поиск..."
                                type="text"
                            >
                            <span class="absolute left-3 top-2.5 text-slate-500 text-sm">🔍</span>
                            <button
                                v-if="searchQuery"
                                class="absolute right-3 top-2.5 text-slate-500 hover:text-slate-300"
                                @click="searchQuery = ''"
                            >✕
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-0.5 bg-slate-900/30">
                        <div
                            v-for="item in filteredItems"
                            :key="item.id"
                            :class="[
                                localSelectedId === item.id
                                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20'
                                    : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'
                            ]"
                            class="group flex items-center justify-between px-4 py-1 rounded-lg cursor-pointer transition-all duration-200"
                            @click="localSelectedId = item.id"
                        >
                            <div class="flex flex-col">
                                <span class="text-sm font-medium">{{
                                        func ? func(item.surname, item.name, item.patronymic) : item.name
                                    }} </span>
                                <span v-if="item.description"
                                      :class="localSelectedId === item.id ? 'text-blue-100' : 'text-slate-500'"
                                      class="text-[10px] uppercase opacity-70 font-semibold"
                                >
                                    {{ item.description }}
                                </span>
                            </div>

                            <div
                                :class="localSelectedId === item.id ? 'border-white' : 'border-slate-600 group-hover:border-slate-500'"
                                class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-colors">
                                <div v-if="localSelectedId === item.id" class="w-2 h-2 rounded-full bg-white"></div>
                            </div>
                        </div>

                        <div v-if="filteredItems.length === 0"
                             class="flex flex-col items-center justify-center h-full opacity-30 py-10">
                            <span class="text-sm">{{ items.length === 0 ? 'Список пуст' : 'Ничего не найдено' }}</span>
                        </div>
                    </div>


                    <div class="w-full flex justify-end gap-2 p-4 bg-slate-800/50 rounded-b-xl">
                        <AppInputButton
                            id="cancel"
                            title="Отмена"
                            type="stone"
                            @buttonClick="select(false)"
                        />
                        <AppInputButton
                            id="confirm"
                            title="Выбрать"
                            type="primary"
                            @buttonClick="handleConfirm"
                        />

                        <!--<AppInputButton-->
                        <!--    id="confirm"-->
                        <!--    :disabled="!localSelectedId"-->
                        <!--    :type="localSelectedId ? 'primary' : 'stone'"-->
                        <!--    title="Выбрать"-->
                        <!--    @buttonClick="handleConfirm"-->
                        <!--/>-->
                    </div>


                </div>
            </div>

        </Transition>
    </Teleport>

</template>

<script generic="T extends {
    id: number | string,
    name: string,
    surname?: string,
    patronymic?: string,
    description?: string | null
}" lang="ts" setup>
import { ref, watch, computed, nextTick } from 'vue'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'
import { getColorClassByType } from '@/app/helpers/helpers.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

interface IProps {
    items: T[]
    modelValue?: number | string | null
    title?: string
    width?: string
    height?: string
    type?: IColorTypes,
    func?: null | ((...args: any[]) => string)
}

const props = withDefaults(defineProps<IProps>(), {
    type:   'primary',
    width:  'min-w-[700px] max-w-[700px]',
    height: 'min-h-[400px] max-h-[600px]',
    title:  '',
    func:   null
})


const emit = defineEmits(['update:modelValue', 'confirm'])

const showModal = ref(false)

let resolvePromise: ((value: boolean) => void) | null

const show = async (initValue: number | string | null) => {
    await nextTick()

    localSelectedId.value = initValue

    showModal.value = true

    // console.log('props.items: ', props.items)
    // console.log('initValue: ', initValue)
    // console.log('modelValue: ', props.modelValue)
    // console.log('localSelectedId: ', localSelectedId.value)

    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: boolean) => {
    if (resolvePromise) {
        resolvePromise(value)
        showModal.value = false
        resolvePromise  = null
    }
}

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

defineExpose({
    show,
    get selected() {
        return props.items.find(i => i.id === localSelectedId.value)
    },
    // get description() { return formData.content }
})


// __ Состояние поиска
const searchQuery = ref('')

// __ Локальное состояние выделения
const localSelectedId = ref<number | string | null>(props.modelValue || null)

// __ Вычисляемый отфильтрованный список
const filteredItems = computed(() => {
    if (!searchQuery.value.trim()) return props.items

    const query = searchQuery.value.toLowerCase()
    return props.items
        .filter(item =>
            (props.func ?
                props.func(item.surname, item.name, item.patronymic).toLowerCase().includes(query) :
                item.name.toLowerCase().includes(query))
            || (item.description && item.description.toLowerCase().includes(query))
        )
})


const handleConfirm = () => {
    if (localSelectedId.value !== null) {
        const selectedItem = props.items.find(i => i.id === localSelectedId.value)
        emit('update:modelValue', localSelectedId.value)
        emit('confirm', selectedItem)
    }

    select(true)
}

// __ Синхронизация, если modelValue придет извне
watch(() => props.modelValue, (newVal) => {
    localSelectedId.value = newVal === 0 ? 0 : newVal || null
})

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


/* Копируем твои стили скроллбара и анимаций */
/*
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-700 rounded-full;
}
*/

.dark-container {
    @apply z-[999] bg-slate-500 fixed w-screen h-screen top-0 left-0 flex justify-center items-center backdrop-blur-sm
    /*@apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center*/
}

.modal-container {
    @apply bg-slate-800 rounded-xl flex flex-col border-l-8 shadow-2xl
}

.close-cross-container {
    @apply flex justify-end w-full
}


/* Состояние появления и исчезновения */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.5s ease;
}

/* Стартовое состояние при появлении / Финальное при исчезновении */
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(1.10); /* Легкое увеличение для эффекта приближения */
}

/*.modal-enter-active, .modal-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from, .modal-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
}*/


</style>
