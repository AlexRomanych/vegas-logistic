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
            @click="emits('sortByField', 'position')"
        />

        <!-- __ Размер модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="'Размер' + getSortIcon(sortSize)"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortSize)"
            :width="short ? renderData.sizeShort.width : renderData.size.width"
            class="field"
            @click="emits('sortBySize')"
        />

        <!-- __ Название модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="'Модель' + getSortIcon(sortName)"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortName)"
            :width="short ? renderData.modelShort.width : renderData.model.width"
            class="field"
            @click="emits('sortByField', 'name_report')"
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
            @click="emits('sortByField', 'amount')"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortTime)"
            :width="renderData.time.width"
            class="field"
            :text="'Тр-ты' + getSortIcon(sortTime)"
            @click="emits('sortByField', 'time')"
        />

        <template v-if="!short">
            <!-- __ Стол 1 -->
            <AppLabelTS
                :align="DEFAULT_ALIGN"
                :rounded="DEFAULT_ROUNDED"
                :text-size="DEFAULT_TEXT_SIZE"
                :type="getPositionBySort(sortTable_1)"
                :width="renderData.table.width"
                class="field"
                text="1"
                @click="emits('sortByField', 'table_1')"
            />

            <!-- __ Стол 2 -->
            <AppLabelTS
                :align="DEFAULT_ALIGN"
                :rounded="DEFAULT_ROUNDED"
                :text-size="DEFAULT_TEXT_SIZE"
                :type="getPositionBySort(sortTable_2)"
                :width="renderData.table.width"
                class="field"
                text="2"
                @click="emits('sortByField', 'table_2')"
            />

            <!-- __ Стол 3 -->
            <AppLabelTS
                :align="DEFAULT_ALIGN"
                :rounded="DEFAULT_ROUNDED"
                :text-size="DEFAULT_TEXT_SIZE"
                :type="getPositionBySort(sortTable_3)"
                :width="renderData.table.width"
                class="field"
                text="3"
                @click="emits('sortByField', 'table_3')"
            />

            <!-- __ Неопознанные -->
            <AppLabelTS
                :align="DEFAULT_ALIGN"
                :rounded="DEFAULT_ROUNDED"
                :text-size="DEFAULT_TEXT_SIZE"
                :type="getPositionBySort(sortTable_0)"
                :width="renderData.table.width"
                class="field"
                text="??"
                @click="emits('sortByField', 'table_0')"
            />
        </template>

        <!-- __ ШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortMachine)"
            :width="renderData.machine.width"
            class="field"
            text="ШМ"
            @click="emits('sortByField', 'machine')"
        />

        <!-- __ Элемент (Деталь) -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="short ? 'Эл.' : 'Элемент'"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortDetail)"
            :width="short ? renderData.detailShort.width : renderData.detail.width"
            class="field"
            @click="emits('sortByField', 'detail')"
        />

        <!-- __ Ткань -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortTextile)"
            :width="renderData.textile.width"
            class="field"
            :text="'Ткань' + getSortIcon(sortTextile)"
            @click="emits('sortByField', 'textile')"
        />

        <!-- __ Номер заявки -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortOrder)"
            :width="renderData.order.width"
            class="field"
            :text="'Заявка' + getSortIcon(sortOrder)"
            @click="emits('sortByField', 'order')"
        />

    </div>

</template>

<script lang="ts" setup>
import type { ICuttingTablePanel, ICuttingTaskCardSort } from '@/types'
import type {
    IRenderCuttingLineData
} from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskCard.vue'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


interface IProps {
    renderData: IRenderCuttingLineData
    panel: ICuttingTablePanel
    activePanel: ICuttingTablePanel
    showDetails?: boolean
    short?: boolean
    sortPosition?: ICuttingTaskCardSort
    sortName?: ICuttingTaskCardSort
    sortTable_1?: ICuttingTaskCardSort
    sortTable_2?: ICuttingTaskCardSort
    sortTable_3?: ICuttingTaskCardSort
    sortTable_0?: ICuttingTaskCardSort
    sortTextile?: ICuttingTaskCardSort
    sortOrder?: ICuttingTaskCardSort
    sortAmount?: ICuttingTaskCardSort
    sortTime?: ICuttingTaskCardSort
    sortSize?: ICuttingTaskCardSort
    sortDetail?: ICuttingTaskCardSort
    sortMachine?: ICuttingTaskCardSort
}

const props = withDefaults(defineProps<IProps>(), {
    showDetails : false,
    short       : false,
    sortPosition: 'none',
    sortName    : 'none',
    sortTable_1 : 'none',
    sortTable_2 : 'none',
    sortTable_3 : 'none',
    sortTable_0 : 'none',
    sortTextile : 'none',
    sortAmount  : 'none',
    sortTime    : 'none',
    sortSize    : 'none',
    sortOrder   : 'none',
    sortDetail  : 'none',
    sortMachine : 'none',
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
const getPositionBySort = (sort: ICuttingTaskCardSort) => {
    if (props.panel !== props.activePanel) return DEFAULT_TYPE
    if (sort === 'none') return DEFAULT_TYPE
    if (sort === 'asc') return SORT_BY_ASC_TYPE
    if (sort === 'desc') return SORT_BY_DESC_TYPE
}

// __ Получаем иконку направления сортировки
const getSortIcon = (sort: ICuttingTaskCardSort) => {
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
