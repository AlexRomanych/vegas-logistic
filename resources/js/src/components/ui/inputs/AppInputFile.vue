<template>
    <div :class="width" class="flex flex-col m-1">
        <!-- todo доделать и дооформить AppInputFile-->

        <!--<label v-if="label" :class="['input-label', textColor]" :for="id">{{ label }}</label>-->
        <label v-if="label" :for="id" class="input-label">{{ label }}</label>
        <input
            :id="id"
            :class="['app-input', width]"
            :disabled="disabled"
            type="file"
            @input="selectFile"
        />
        <div><span class="input-label">JSON (*.json)</span></div>
        <div v-if="errors">
            <div v-for="(err, index) in errors" :key="index">
                <span :class="['input-error', textColor]">
                    {{ err.$message }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'

import { colorsClasses, colorsList } from '@/app/constants/colorsClasses.js'
import { getColorClassByType } from '@/app/helpers/helpers.js'
import AppButton from '@/components/ui/buttons/AppButton.vue'

const props = defineProps({
    id: {
        required: true,
    },
    type: {
        type: String,
        required: false,
        default: 'primary',
        validator: (type) => colorsList.includes(type),
    },
    label: {
        type: String,
        required: false,
        default: '',
    },
    disabled: {
        type: Boolean,
        required: false,
        default: false,
    },
    width: {
        type: String,
        required: false,
        default: 'w-[400px]',
    },
    errors: {
        type: Array,
        required: false,
        default: null,
    },
})

const emit = defineEmits(['selectFile'])

// const file = ref(null)

// Сохраняем ссылку на файл
// const selectFile = (e) => file.value = e.target.files[0]
const selectFile = (e) => emit('selectFile', e.target.files[0])
// const selectFile = (e) => {
//     console.log(e.target.files[0])
//
//     // const file = e.target.files[0]
//     // const url = URL.createObjectURL(file)
//     // console.log('RESULT', url)
// }

// const currentColorIndex = 600       // задаем основной индекс палитры tailwinds
// const currentColor = computed(() => getColorClassByType(props.type)).value + currentColorIndex
//
// let textColor = 'text' + currentColor
// textColor = textColor.replace(currentColorIndex.toString(), (currentColorIndex + 200).toString())
</script>

<style scoped>
.app-input {
    /*@apply p-1 border rounded-lg focus:outline-none focus:ring-2;*/
    @apply block
    text-lg text-slate-500 font-semibold
    border-2 rounded-lg border-slate-500
    hover:bg-slate-200
    file:py-4
    file:px-4
    file:rounded-l-lg file:border-0 file:border-slate-500
    file:text-lg file:font-semibold
    file:bg-slate-500 file:text-white
    hover:file:bg-slate-400;
}

.input-error {
    @apply text-sm ml-2 font-semibold;
}

.input-label {
    @apply text-sm font-semibold ml-2 mb-0.5 mt-2 text-slate-600;
}
</style>
