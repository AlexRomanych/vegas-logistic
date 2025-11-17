<template>
    <div class="w-[130px] rounded-lg">
        <VueDatePicker
            v-model="date"
            :action-row="{
                showPreview: false,
                showNow: true,
                selectBtnLabel: 'Выбор',
                cancelBtnLabel: 'Отмена',
                nowBtnLabel: 'Текущая',}"
            :enable-time-picker="false"
            :formats="{input: 'dd.MM.yyyy г.'}"
            :input-attrs="{
                clearable: false,
                /*hideInputIcon: true,*/
                id: id
            }"
            :locale="ru"
            :timePicker="false"
            class="custom-datepicker"
            dark
            @update:model-value="console.log(date)"
        />
    </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import { VueDatePicker } from '@vuepic/vue-datepicker'
import { ru } from 'date-fns/locale'

import '@vuepic/vue-datepicker/dist/main.css'

interface IProps {
    id?: string
}

const props = withDefaults(defineProps<IProps>(), {
    id: () => 'date-picker-' + Math.random().toString(36).substring(2, 9)
})

const date = ref<Date>(new Date())



// --dp-cell-padding - отступ между ячейками календаря
// --dp-cell-border-radius - скругление краев ячеек календаря выбранной даты
// --dp-cell-padding - расстояние дней недели
// --dp-background-color #212169 #002B36 #62748E
// --dp-border-radius 9px
</script>

<style>
.custom-datepicker {

    --dp-background-color: #002B36;
    --dp-border-radius: 9px; /* Немного меньше радиус */
    --dp-menu-border-radius: 8px;
    --dp-font-family: 'Inter', sans-serif;
    --dp-primary-color: #4f46e5;

    /* --- ИЗМЕНЕНИЯ ДЛЯ УМЕНЬШЕНИЯ РАЗМЕРА --- */
    --dp-font-size: 0.725rem; /* Уменьшаем размер шрифта (14px) */
    /* Уменьшаем вертикальный отступ для уменьшения высоты */
    --dp-input-padding: 28px 30px 8px 10px;


    /* --- КОНЕЦ ИЗМЕНЕНИЙ --- */
}

.dp__theme_dark {
    --dp-background-color: #002B36;
}

</style>
