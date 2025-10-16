<template>

    <!-- __ Добавляем информацию о рулонах -->
    <div class="flex">

<!--        <AppLabelMultiLine-->
<!--            :text="['Номер', 'рулона']"-->
<!--            align="center"-->
<!--            width="w-[80px]"-->
<!--        />-->


        <!-- __ № п/п-->
        <AppLabelMultiLine
            :text="['№', 'п/п']"
            align="center"
            type="primary"
            width="w-[50px]"
            text-size="mini"
        />

        <!-- __ ПС -->
        <AppLabelMultiLine
            :text="['Полотно', 'стеганное']"
            align="center"
            type="primary"
            width="w-[300px]"
            text-size="mini"
        />

        <!-- __ Буфер -->
        <AppLabelMultiLine
            :text="['Буфер', 'ПС, м.п.']"
            align="center"
            width="w-[80px]"
            text-size="mini"
        />

        <!-- __ Средняя длина одного рулона ткани -->
        <AppLabelMultiLine
            :text="['Ср. дл. рул.', 'ткани, м.п.']"
            align="center"
            width="w-[80px]"
            text-size="mini"
        />

        <!-- __ Кол-во рулонов ткани в ПС -->
        <AppLabelMultiLine
            :text="['Кол-во рул.', 'в ПС, шт.']"
            align="center"
            width="w-[80px]"
            text-size="mini"
        />

        <!-- __ Длина ткани в ПС -->
        <AppLabelMultiLine
            :text="['Ср. дл. рул.', 'ПС, м.п.']"
            align="center"
            width="w-[80px]"
            text-size="mini"
        />

        <!-- __ Количество рулонов -->
        <AppLabelMultiLine
            :text="['Кол-во,', 'рул.']"
            align="center"
            type="primary"
            width="w-[80px]"
            text-size="mini"
        />

        <!-- __ Длина ткани -->
        <AppLabelMultiLine
            :text="['Ср. длина', 'ткани, м.п.']"
            align="center"
            type="primary"
            width="w-[80px]"
            text-size="mini"
        />

        <!-- __ Длина ПС -->
        <AppLabelMultiLine
            :text="['Длина ПС,', 'м.п.']"
            align="center"
            width="w-[80px]"
            text-size="mini"
        />

        <!-- __ Труд-ты -->
        <AppLabelMultiLine
            :text="['Труд-ты', '']"
            align="center"
            width="w-[90px]"
            text-size="mini"
        />

        <!-- __ Комментарий -->
        <AppLabelMultiLine
            :text="['Комментарий', '']"
            align="center"
            type="primary"
            width="w-[300px]"
            text-size="mini"
        />

    </div>

</template>

<script setup lang="ts">
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'

</script>


<style scoped>

</style>
