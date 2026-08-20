<template>
    <div class="flex py-[3px]">

        <!-- __ № п/п -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortPosition)"
            :width="renderData.position.width"
            class="field"
            text="#"
            title="Click - Сортировка по Порядку"
            @click="emits('sortByField', 'position')"
        />

        <!-- __ Тип модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortType)"
            :width="renderData.modelType.width"
            class="field"
            :text="'Тип' + getSortIcon(sortType)"
            title="Click - Сортировка по Типу Модели"
            @click="emits('sortByField', 'model_type')"
        />

        <!-- __ Размер модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="'Размер' + getSortIcon(sortSize)"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortSize)"
            :width="renderData.size.width"
            class="field"
            title="Click - Сортировка по Размеру Модели"
            @click="emits('sortBySize')"
        />

        <!-- __ Название Модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="'Модель' + getSortIcon(sortName)"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortName)"
            :width="short ? renderData.modelShort.width : renderData.model.width"
            class="field"
            title="Click - Сортировка по Названию Модели"
            @click="emits('sortByField', 'name')"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortAmount)"
            :width="renderData.amount.width"
            class="field"
            text="шт."
            title="Click - Сортировка по Количеству"
            @click="emits('sortByField', 'amount')"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="'Тр-ты' + getSortIcon(sortTime)"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortTime)"
            :width="renderData.time.width"
            class="field"
            @click="emits('sortByField', 'time')"
        />

        <!-- __ Ламит -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortLamit)"
            :width="renderData.line.width"
            class="field"
            text="Л"
            @click="emits('sortByField', ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT)"
        />

        <!-- __ Столы -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortTable)"
            :width="renderData.line.width"
            class="field"
            text="С"
            @click="emits('sortByField', ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE)"
        />

        <!-- __ Номер заявки -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="'Заявка' + getSortIcon(sortOrder)"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortOrder)"
            :width="renderData.order.width"
            class="field"
            @click="emits('sortByField', 'order')"
        />

    </div>

</template>

<script lang="ts" setup>
import type { IAssemblyManufLinesPanel, IAssemblyTaskCardSort } from '@/types'
import type {
    IRenderAssemblyLineData
} from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskCard.vue'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { ASSEMBLY_LINES } from '@/app/constants/assembly.ts'


interface IProps {
    renderData: IRenderAssemblyLineData
    panel: IAssemblyManufLinesPanel
    activePanel: IAssemblyManufLinesPanel
    showDetails?: boolean
    short?: boolean
    sortPosition?: IAssemblyTaskCardSort
    sortName?: IAssemblyTaskCardSort
    sortType?: IAssemblyTaskCardSort
    sortSize?: IAssemblyTaskCardSort
    sortLamit?: IAssemblyTaskCardSort
    sortTable?: IAssemblyTaskCardSort
    sortTextile?: IAssemblyTaskCardSort
    sortOrder?: IAssemblyTaskCardSort
    sortAmount?: IAssemblyTaskCardSort
    sortTime?: IAssemblyTaskCardSort
}

const props = withDefaults(defineProps<IProps>(), {
    showDetails : false,
    short       : false,
    sortPosition: 'none',
    sortName    : 'none',
    sortType    : 'none',
    sortSize    : 'none',
    sortLamit  : 'none',
    sortTable  : 'none',
    sortAmount  : 'none',
    sortTime    : 'none',
    sortOrder   : 'none',
})


const emits = defineEmits<{
    (e: 'sortByField', field: string): void
    (e: 'sortBySize'): void
}>()

const DEFAULT_ALIGN     = 'center'
const DEFAULT_TEXT_SIZE = 'micro'
const DEFAULT_ROUNDED   = '4'
const DEFAULT_TYPE      = 'primary'
const SORT_BY_ASC_TYPE  = 'info'
const SORT_BY_DESC_TYPE = 'indigo'


// __ Получаем тип в зависимости от направления сортировки
const getPositionBySort = (sort: IAssemblyTaskCardSort) => {
    if (props.panel !== props.activePanel) return DEFAULT_TYPE
    if (sort === 'none') return DEFAULT_TYPE
    if (sort === 'asc') return SORT_BY_ASC_TYPE
    if (sort === 'desc') return SORT_BY_DESC_TYPE
}

// __ Получаем иконку направления сортировки
const getSortIcon = (sort: IAssemblyTaskCardSort) => {
    if (props.panel !== props.activePanel) return ''
    if (sort === 'none') return ''
    if (sort === 'asc') return '▲'
    if (sort === 'desc') return '▼'
}


</script>

<style scoped>
.field {
    @apply cursor-pointer;
}
</style>
