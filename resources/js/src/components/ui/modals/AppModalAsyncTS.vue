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

                <div class="text-container">
                    <div class="text-data">
                        <span>{{ showText }}</span>
                    </div>
                </div>


                <div class="w-full h-full flex justify-end">

                    <div v-if="mode === 'confirm'"
                         class="m-1 p-1">
                        <AppInputButton
                            id="confirm"
                            :type="type"
                            title="Да"
                            @buttonClick="select(true)"
                        />
                    </div>

                    <div
                        class="m-1 p-1">
                        <AppInputButton
                            id="confirm"
                            :title="mode === 'confirm' ? 'Отмена' : 'Закрыть'"
                            :type="type"
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

interface IProps {
    type?: IColorTypes,
    width?: string,
    height?: string,
    text?: string,
    mode?: 'inform' | 'confirm'
}

const props = withDefaults(defineProps<IProps>(), {
    type: 'primary',
    width: 'min-w-[500px]',
    height: 'min-h-[300px]',
    text: 'This is a Modal Window',
    mode: 'inform'
})

// const emits = defineEmits<{
//     (e: 'select'): void
// }>()

const showModal = ref(false)           // реактивность видимости модального окна
const showText = ref(props.text)              // реактивность текста сообщения в модальном окне

let resolvePromise: ((value: boolean) => void) | null
const show = (msg = showText.value) => {
    showModal.value = true
    showText.value = msg
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

defineExpose({
    show,
})

// Следим за отображением текста в модальном окне
watch(() => props.text, (value) => {
    showText.value = value
})

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

</script>

<style scoped>

.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between items-center border-l-8
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
