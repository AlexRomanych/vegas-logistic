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
                :width="render.sector.width"
                align="center"
                class="cursor-pointer"
                rounded="4"
                text-size="micro"
                @dblclick="goToSector(dataItem)"
            />
        </div>


    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import type { IAssemblyTask, IRenderData, IStats, ITotalsDataType, } from '@/types'
import { ASSEMBLY_TASK_DRAFT, DATA_TYPE_NOTHING, REDIRECT_KEY } from '@/app/constants/assembly.ts'
import { getDataArray } from '@/app/helpers/manufacture/helpers_assembly.ts'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/assembly/components/AppLabelTSWrapper.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    render: IRenderData
    item?: IAssemblyTask
    index?: number
    dataType?: ITotalsDataType,
}


const props = withDefaults(defineProps<IProps>(), {
    item : () => ASSEMBLY_TASK_DRAFT,
    index: 0,
    dataType  : DATA_TYPE_NOTHING,
})

const router = useRouter()

const percentsRender = computed(() => getDataArray(props.item, props.dataType))

// console.log('props.item: ', props.item)

// __ Переход на нужный Участок и нужное СЗ
const goToSector = (dataItem: IStats) => {
    localStorage.setItem(REDIRECT_KEY, JSON.stringify({
        task_id  : props.item.id,
        sector_id: dataItem.id,
    }))

    router.push({
        name  : 'manufacture.cell.assembly.manipulate.day',
        params: {
            date: props.item.action_at.split(' ')[0],
        },
    })

    // console.log('dataItem: ', dataItem)
}

</script>

<style scoped>

</style>
