<template>
    <div class="flex">

        <!-- __ ⚙️ -->
        <AppLabelTS
            :type="subgroup.subgroupType"
            align="center"
            rounded="4"
            text="⚙️"
            text-size="mini"
            width="w-[30px]"
        />

        <!-- __ Название SubCategory -->
        <AppLabelTS
            :text="subgroup.subgroupName"
            :type="subgroup.subgroupType"
            rounded="4"
            text-size="micro"
            width="w-[410px]"
        />

        <!-- __ Время -->
        <AppLabelTS
            :text="formatTimeWithLeadingZeros(subgroup.time.total, 'min')"
            :type="subgroup.subgroupType"
            align="center"
            rounded="4"
            text-size="mini"
            width="w-[100px]"
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

</script>

<style scoped>

</style>
