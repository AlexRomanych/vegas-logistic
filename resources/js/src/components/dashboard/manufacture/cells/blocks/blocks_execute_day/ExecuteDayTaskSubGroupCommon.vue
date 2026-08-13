<template>
    <div class="flex">

        <!-- __ ∑ -->
        <AppLabelTS
            :type="subgroup.subgroupType"
            align="center"
            rounded="4"
            text="∑"
            text-size="normal"
            width="w-[30px]"
        />

        <!-- __ Название SubCategory -->
        <AppLabelTS
            :text="subgroup.subgroupName"
            :type="subgroup.subgroupType"
            rounded="4"
            text-size="normal"
            width="w-[367px]"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :text="subgroup.amount.total.toFixed(0)"
            :type="subgroup.subgroupType"
            rounded="4"
            text-size="mini"
            width="w-[40px]"
            align="center"
        />

        <!-- __ Время -->
        <AppLabelTS
            :text="formatTimeWithLeadingZeros(subgroup.time.total, 'hour')"
            :type="subgroup.subgroupType"
            align="center"
            rounded="4"
            text-size="mini"
            width="w-[100px]"
        />

        <!-- __ Площадь -->
        <AppLabelTS
            :text="subgroup.square.total.toFixed(3)"
            :type="subgroup.subgroupType"
            align="center"
            rounded="4"
            text-size="mini"
            width="w-[70px]"
        />

        <!-- __ Заглушка -->
        <AppLabelTS
            text=""
            :type="subgroup.subgroupType"
            rounded="4"
            text-size="mini"
            width="w-[40px]"
            align="center"
        />

        <!-- __ Всего, шт. -->
        <AppLabelTS
            :text="`Σ = ${subgroup.amount.total.toFixed(0)} шт. (${formatTimeWithLeadingZeros(subgroup.time.total, 'hour')})`"
            :type="subgroup.amount.total === 0 ? 'danger' : 'stone'"
            :width="FIELDS_AMOUNT_TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
        />

        <!-- __ Выполнено, шт. -->
        <AppLabelTS
            :text="`✓ = ${subgroup.amount.done.toFixed(0)} шт. (${formatTimeWithLeadingZeros(subgroup.time.done, 'hour')})`"
            :width="FIELDS_AMOUNT_TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="success"
        />

        <!-- __ Не Выполнено, шт. -->
        <AppLabelTS
            :text="`✗ = ${subgroup.amount.incomplete.toFixed(0)} шт. (${formatTimeWithLeadingZeros(subgroup.time.incomplete, 'hour')})`"
            :width="FIELDS_AMOUNT_TIME_WIDTH"
            align="center"
            rounded="4"
            text-size="mini"
            type="danger"
        />

    </div>

</template>

<script lang="ts" setup>

import type { IBlockTaskLinesSubgroup, } from '@/types'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

interface IProps {
    subgroup: IBlockTaskLinesSubgroup
}

/*const props =*/
defineProps<IProps>()

const FIELDS_AMOUNT_TIME_WIDTH = 'w-[174px]'

</script>

<style scoped>

</style>
