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

        <!-- __ Название Блока -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="'Модель' + getSortIcon(sortName)"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortName)"
            :width="short ? renderData.modelShort.width : renderData.model.width"
            class="field"
            title="Click - Сортировка по Названию Блока"
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

        <!-- __ Площадь -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortSquare)"
            :width="renderData.square.width"
            class="field"
            text="S, m2"
            title="Click - Сортировка по Площади"
            @click="emits('sortByField', 'square')"
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

        <!-- __ Линия 1 -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortLine_1)"
            :width="renderData.line.width"
            class="field"
            text="1"
            @click="emits('sortByField', 'line_1')"
        />

        <!-- __ Линия 2 -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortLine_2)"
            :width="renderData.line.width"
            class="field"
            text="2"
            @click="emits('sortByField', 'line_2')"
        />

        <!-- __ Неопознанные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortLine_0)"
            :width="renderData.line.width"
            class="field"
            text="??"
            @click="emits('sortByField', 'line_0')"
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
import type { IBlockManufLinesPanel, IBlockTaskCardSort } from '@/types'
import type {
    IRenderBlockLineData
} from '@/components/dashboard/manufacture/cells/blocks/blocks_manage/ManageTaskCard.vue'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


interface IProps {
    renderData: IRenderBlockLineData
    panel: IBlockManufLinesPanel
    activePanel: IBlockManufLinesPanel
    showDetails?: boolean
    short?: boolean
    sortPosition?: IBlockTaskCardSort
    sortName?: IBlockTaskCardSort
    sortLine_1?: IBlockTaskCardSort
    sortLine_2?: IBlockTaskCardSort
    sortLine_0?: IBlockTaskCardSort
    sortTextile?: IBlockTaskCardSort
    sortOrder?: IBlockTaskCardSort
    sortAmount?: IBlockTaskCardSort
    sortSquare?: IBlockTaskCardSort
    sortTime?: IBlockTaskCardSort
    sortDetail?: IBlockTaskCardSort
    sortMachine?: IBlockTaskCardSort
}

const props = withDefaults(defineProps<IProps>(), {
    showDetails : false,
    short       : false,
    sortPosition: 'none',
    sortName    : 'none',
    sortLine_1  : 'none',
    sortLine_2  : 'none',
    sortLine_0  : 'none',
    sortAmount  : 'none',
    sortSquare  : 'none',
    sortTime    : 'none',
    sortOrder   : 'none',
})


const emits = defineEmits<{
    (e: 'sortByField', field: string): void
}>()

const DEFAULT_ALIGN     = 'center'
const DEFAULT_TEXT_SIZE = 'micro'
const DEFAULT_ROUNDED   = '4'
const DEFAULT_TYPE      = 'primary'
const SORT_BY_ASC_TYPE  = 'info'
const SORT_BY_DESC_TYPE = 'indigo'


// __ Получаем тип в зависимости от направления сортировки
const getPositionBySort = (sort: IBlockTaskCardSort) => {
    if (props.panel !== props.activePanel) return DEFAULT_TYPE
    if (sort === 'none') return DEFAULT_TYPE
    if (sort === 'asc') return SORT_BY_ASC_TYPE
    if (sort === 'desc') return SORT_BY_DESC_TYPE
}

// __ Получаем иконку направления сортировки
const getSortIcon = (sort: IBlockTaskCardSort) => {
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
