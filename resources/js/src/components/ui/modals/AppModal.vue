<template>
    <Teleport to="body">
        <div v-if="showModal" class="dark-container">
            <div :class="[width, height, borderColor, 'modal-container']">
                <div class="close-cross-container">
                    <div class="m-1 p-1">
                        <AppInputButton
                            id="close"
                            :type="type"
                            height="w-5"
                            title="x"
                            width="w-[30px]"
                            @buttonClick="closeModal(false)"
                        />
                    </div>
                </div>

                <div class="text-container">
                    <div class="text-data">
                        <span>{{ text }}</span>
                    </div>
                </div>

                <div class="w-full h-full flex justify-end">
                    <div v-if="mode === 'confirm'" class="m-1 p-1">
                        <AppInputButton
                            id="confirm"
                            :type="type"
                            title="Да"
                            @buttonClick="closeModal(true)"
                        />
                    </div>

                    <div class="m-1 p-1">
                        <AppInputButton
                            id="confirm"
                            :type="type"
                            :title="mode === 'confirm' ? 'Отмена' : 'Закрыть'"
                            @buttonClick="closeModal(false)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, ref, watch, watchEffect } from 'vue'

import { colorsList } from '@/app/constants/colorsClasses.js'
import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'

const props = defineProps({
    width: {
        type: String,
        required: false,
        default: 'min-w-[500px]',
    },
    height: {
        type: String,
        required: false,
        default: 'min-h-[300px]',
    },
    type: {
        type: String,
        required: false,
        default: 'primary',
        validator: (type) => colorsList.includes(type),
    },
    text: {
        type: String,
        required: false,
        default: 'This is a Modal Window',
    },
    show: {
        type: Boolean,
        required: false,
        default: false,
    },
    mode: {
        type: String,
        required: false,
        default: 'inform',
        validator: (mode) => ['inform', 'confirm'].includes(mode),
    },
})

const emit = defineEmits(['closeModal'])

const showModal = ref(props.show)

// Следим за отображением модального окна
watch(
    () => props.show,
    (value) => {
        showModal.value = value
        // console.log('watch showModal', value)
    },
)

// закрываем модальное окно и возвращаем подтверждение
const closeModal = (confirm = false) => {
    showModal.value = false
    emit('closeModal', confirm)
}

const borderColor = computed(() => getColorClassByType(props.type, 'border'))
</script>

<style scoped>
.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center;
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between items-center border-l-8;
}

.close-cross-container {
    @apply flex justify-end w-full h-full;
}

.text-container {
    @apply flex items-end;
}

.text-data {
    @apply border-2 border-slate-800 w-full h-full text-white;
}

.close-button-container {
    @apply w-full h-full flex justify-end;
}
</style>
