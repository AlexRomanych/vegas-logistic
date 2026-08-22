<template>
    <div class="flex">
        <!-- __ Collapse (Plug) -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.plug"
        />

        <!-- __ id -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.id"
            class="cursor-pointer"
        />

        <!-- __ № -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.position"
            class="cursor-pointer"
        />

        <!-- __ Клиент -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.client"
            class="cursor-pointer"
        />

        <!-- __ Номер Заявки -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.orderNoStr"
            class="cursor-pointer"
        />

        <!-- __ Количество -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.taskAmount"
            class="cursor-pointer"
        />

        <!-- __ Актуальность -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.taskActive"
            class="cursor-pointer"
        />

        <!-- __ Прогнозная -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.isForecast"
            class="cursor-pointer"
        />

        <!-- __ Загрузка на складе -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.loadAt"
            class="cursor-pointer"
        />

        <!-- __ Разгрузка на складе -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.unloadAt"
            class="cursor-pointer"
        />

        <!-- __ Комментарий -->
        <AppLabelTSWrapper
            :arg="item"
            :render-object="render.description"
            class="cursor-pointer"
        />

        <!-- __ Данные по каждому участку -->
        <div v-for="dataItem of percentsRender" :key="dataItem.name" class="flex">
            <AppLabelTS
                :color="dataItem.color"
                :text="dataItem.title"
                align="center"
                rounded="4"
                text-size="micro"
                :width="render.sector.width"
            />
        </div>


    </div>
</template>

<script lang="ts" setup>
import {computed} from 'vue'
import type { IAssemblyTask, IRenderData, } from '@/types'
import { ASSEMBLY_TASK_DRAFT } from '@/app/constants/assembly.ts'
import { getDataArray } from '@/app/helpers/manufacture/helpers_assembly.ts'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/assembly/components/AppLabelTSWrapper.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    render: IRenderData
    item?: IAssemblyTask
    index?: number
}


const props = withDefaults(defineProps<IProps>(), {
    item : () => ASSEMBLY_TASK_DRAFT,
    index: 0,
})

const percentsRender = computed(() => getDataArray(props.item))

// console.log('props.item: ', props.item)

</script>

<style scoped>

</style>
