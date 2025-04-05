<template>

    <!--attract: Добавляем информацию о рулонах -->
    <div class="flex">

<!--        <AppLabelMultiLine-->
<!--            :text="['Номер', 'рулона']"-->
<!--            align="center"-->
<!--            width="w-[80px]"-->
<!--        />-->

        <AppLabelMultiLine
            :text="['Полотно', 'стеганное']"
            align="center"
            type="primary"
            width="w-[300px]"
        />

        <AppLabelMultiLine
            :text="['Ср. длина', 'ткани (м.п.)']"
            align="center"
            width="w-[100px]"
        />

        <AppLabelMultiLine
            :text="['Кол-во,', '(рул.)']"
            align="center"
            type="primary"
            width="w-[70px]"
        />

        <AppLabelMultiLine
            :text="['Кол-во,', '(м.п.)']"
            align="center"
            type="primary"
            width="w-[70px]"
        />

        <AppLabelMultiLine
            :text="['Труд-ты', '(мин.)']"
            align="center"
            width="w-[80px]"
        />

        <AppLabelMultiLine
            :text="['Комментарий', '']"
            align="center"
            type="primary"
            width="w-[300px]"
        />

    </div>

</template>

<script setup>
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'

</script>


<style scoped>

</style>
