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



    <!--<div class="flex items-center justify-center w-full max-w-xl mx-auto">-->
    <!--    <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 cursor-pointer group">-->
    <!--        <div class="flex flex-col items-center justify-center pt-5 pb-6">-->
    <!--            <div class="p-4 bg-indigo-100 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">-->
    <!--                <svg class="w-8 h-8 text-indigo-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">-->
    <!--                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>-->
    <!--                </svg>-->
    <!--            </div>-->
    <!--            <p class="mb-2 text-sm text-slate-700 font-semibold">Нажмите для загрузки или перетащите файл</p>-->
    <!--            <p class="text-xs text-slate-500">Excel (XLSX, XLS) до 50МБ</p>-->
    <!--        </div>-->
    <!--        <input id="dropzone-file" type="file" class="hidden" accept=".xlsx, .xls" />-->
    <!--    </label>-->
    <!--</div>-->
</template>

<script setup>
// import { computed, ref } from 'vue'

import { colorsClasses, colorsList } from '@/app/constants/colorsClasses.js'
// import { getColorClassByType } from '@/app/helpers/helpers.js'
// import AppButton from '@/components/ui/buttons/AppButton.vue'

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
        default: 'w-[800px]',
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
