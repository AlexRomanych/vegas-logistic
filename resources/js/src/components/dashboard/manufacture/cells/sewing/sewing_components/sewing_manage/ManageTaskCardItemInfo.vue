<template>

    <Teleport to="body">

        <div v-if="showModal"
             class="dark-container">

            <div :class="[width, height, borderColor, 'modal-container']">

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

                <div class="w-full pl-8 border-b border-slate-800/50">
                    <h3 class="text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold mb-1">
                        Детализация
                    </h3>
                    <h2 class="text-left text-white text-lg font-bold">
                        Детализация записи
                    </h2>
                </div>

                <div class="p-4 max-h-[60vh] overflow-y-auto custom-scrollbar space-y-1">

                    <div v-for="(item, index) in shortData" :key="index"
                         class="flex items-center px-4 py-2 rounded-lg hover:bg-[#161e2d] transition-colors group"
                    >
                        <span class="w-24 shrink-0 text-slate-500 text-[11px] uppercase font-semibold tracking-tight">
                            {{ item.label }}
                        </span>
                        <span class="text-slate-200 text-sm font-medium group-hover:text-blue-400 transition-colors">
                            {{ item.value }}
                        </span>
                    </div>

                    <div v-for="(item, index) in longData" :key="index">
                        <div class="mt-4 px-4 py-3 bg-[#161e2d] rounded-xl border border-slate-800/50">
                            <div class="text-slate-500 text-[10px] uppercase font-bold tracking-widest mb-2">
                                {{ item.label }}
                            </div>
                            <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-wrap">
                                {{ item.value }}
                            </p>
                        </div>
                    </div>

                </div>

                <div class="w-full h-full flex justify-end">
                    <div class="m-1 p-1">
                        <AppInputButton
                            id="confirm"
                            :type="type"
                            title="Закрыть"
                            @buttonClick="select(false)"
                        />
                    </div>
                </div>

            </div>
        </div>

    </Teleport>
</template>

<script lang="ts" setup>
import { computed, ref, watch, } from 'vue'

import type { IColorTypes } from '@/app/constants/colorsClasses.js'

import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import type { ISewingTaskOrderLine } from '@/types'

interface IProps {
    orderLine: ISewingTaskOrderLine | null,
    type?: IColorTypes,
    width?: string,
    height?: string,
}

const props = withDefaults(defineProps<IProps>(), {
    type:   'primary',
    width:  'min-w-[500px] max-w-[500px]',
    height: 'min-h-[200px]',
})

// const emits = defineEmits<{
//     (e: 'select'): void
// }>()


const showModal = ref(false)           // реактивность видимости модального окна

let resolvePromise: ((value: boolean) => void) | null
const show = () => {
    showModal.value = true

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

defineExpose({
    show,
})

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

const shortData = computed(() => [
    { label: 'Размер', value: props.orderLine?.size },
    { label: 'Модель', value: props.orderLine?.model.main.name_report },
    { label: 'Количество', value: props.orderLine?.amount.toString() },
    { label: 'Ткань', value: props.orderLine?.textile },
])

const longData = computed(() => [
    { label: 'Состав', value: props.orderLine?.composition },
    { label: 'Примечание 1', value: props.orderLine?.describe_1 },
    { label: 'Примечание 2', value: props.orderLine?.describe_2 },
    { label: 'Примечание 3', value: props.orderLine?.describe_3 },
])


</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-700 rounded-full;
}

/* Красивый перенос длинных слов, если они встретятся */
p {
    word-break: break-word;
}

.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between border-l-8
}

.close-cross-container {
    @apply flex justify-end w-full h-full
}

.text-container {
    @apply flex items-end
}

.text-data {
    @apply border-2 border-slate-800 w-full h-full text-white
}

/*
.close-button-container {
    @apply w-full h-full flex justify-end
}
*/
</style>



