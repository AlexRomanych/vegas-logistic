<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal" class="dark-container" @click.self="select(false)">

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

                    <div class="w-full pl-8 border-b border-slate-800/50 pb-4">
                        <h3 class="text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold mb-1">
                            Редактирование / добавление
                        </h3>
                        <h2 class="text-left text-white text-lg font-bold">
                            Изменение/добавление комментария
                        </h2>
                    </div>

                    <div class="p-6 flex flex-col space-y-6 overflow-y-auto custom-scrollbar">

                        <!--<div class="flex flex-col space-y-2">-->
                        <!--    <label class="text-slate-500 text-[11px] uppercase font-semibold tracking-tight">-->
                        <!--       Название Схемы-->
                        <!--    </label>-->
                        <!--    <input-->
                        <!--        v-model="formData.title"-->
                        <!--        type="text"-->
                        <!--        placeholder="Введите текст..."-->
                        <!--        class="bg-slate-900 text-slate-200 text-sm border border-slate-700 rounded-lg px-4 py-2-->
                        <!--               focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"-->
                        <!--    />-->
                        <!--</div>-->

                        <div class="flex flex-col space-y-2">
                            <label class="text-slate-500 text-[11px] uppercase font-semibold tracking-tight">
                                {{ label}}
                            </label>
                            <div class="relative group">
                                <textarea
                                    v-model="formData.comment"
                                    rows="8"
                                    placeholder="Добавьте комментарий..."
                                    class="w-full bg-[#161e2d] text-blue-400 text-sm font-mono leading-relaxed
                                           border border-slate-800/50 rounded-xl px-4 py-3
                                           focus:ring-1 focus:ring-blue-500 outline-none transition-all
                                           custom-scrollbar resize-none whitespace-pre-wrap"
                                ></textarea>
                                <div class="absolute right-3 bottom-3 opacity-20 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                                </div>
                            </div>
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
                            :type="type"
                            title="Сохранить"
                            @buttonClick="select(true)"
                        />
                    </div>

                </div>
            </div>

        </Transition>
    </Teleport>
</template>

<script lang="ts" setup>
import { computed, nextTick, reactive, ref } from 'vue'
import type { IColorTypes } from '@/types'

import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

interface IProps {
    type?: IColorTypes,
    width?: string,
    height?: string,
    comment: string | null,
    label?: string,
}

const props = withDefaults(defineProps<IProps>(), {
    type:   'primary',
    width:  'min-w-[700px] max-w-[700px]',
    height: 'min-h-[400px]',
    label:  'Комментарий',
})

const showModal = ref(false)

const formData = reactive({
    comment: '',
    // title: '',
    // content: ''
})

let resolvePromise: ((value: boolean) => void) | null

const show = async () => {

    await nextTick()

    formData.comment = props.comment ?? ''

    showModal.value = true

    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: boolean) => {
    if (resolvePromise) {
        resolvePromise(value)
        showModal.value = false
        resolvePromise = null
    }
}

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

defineExpose({
    show,
    get comment() { return formData.comment },
})
</script>

<style scoped>
/* Копируем твои стили скроллбара и анимаций */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-700 rounded-full;
}

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
