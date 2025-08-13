<template>

    <!--attract: Добавляем информацию о рулонах -->
    <div class="flex">

<!--        <AppLabelMultiLine-->
<!--            :text="['Номер', 'рулона']"-->
<!--            align="center"-->
<!--            width="w-[80px]"-->
<!--        />-->

        <AppLabelMultiLine
            :text="['№', 'п/п']"
            align="center"
            type="primary"
            width="w-[50px]"
            text-size="mini"
        />

        <AppLabelMultiLine
            :text="['Полотно', 'стеганное']"
            align="center"
            type="primary"
            width="w-[300px]"
            text-size="mini"
        />

        <AppLabelMultiLine
            :text="['Буфер', 'ПС, м.п.']"
            align="center"
            width="w-[80px]"
            text-size="mini"
        />

        <AppLabelMultiLine
            :text="['Ср. длина', 'ткани, м.п.']"
            align="center"
            width="w-[80px]"
            text-size="mini"
        />

        <AppLabelMultiLine
            :text="['Ср. длина', 'ПС, м.п.']"
            align="center"
            width="w-[80px]"
            text-size="mini"
        />

        <AppLabelMultiLine
            :text="['Кол-во,', 'рул.']"
            align="center"
            type="primary"
            width="w-[80px]"
            text-size="mini"
        />

        <AppLabelMultiLine
            :text="['Длина', 'ткани, м.п.']"
            align="center"
            type="primary"
            width="w-[80px]"
            text-size="mini"
        />

        <AppLabelMultiLine
            :text="['Длина ПС,', 'м.п.']"
            align="center"
            width="w-[80px]"
            text-size="mini"
        />

        <AppLabelMultiLine
            :text="['Труд-ты', '']"
            align="center"
            width="w-[90px]"
            text-size="mini"
        />

        <AppLabelMultiLine
            :text="['Комментарий', '']"
            align="center"
            type="primary"
            width="w-[300px]"
            text-size="mini"
        />

    </div>

</template>

<script setup>
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'

</script>


<style scoped>

</style>
