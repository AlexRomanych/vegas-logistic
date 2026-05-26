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
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortSize)"
            :width="renderData.size.width"
            class="field"
            :text="'Размер' + getSortIcon(sortSize)"
            @click="emits('sortBySize')"
        />

        <!-- __ Название модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortName)"
            :width="renderData.model.width"
            class="field"
            :text="'Модель' + getSortIcon(sortName)"
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

        <!-- __ Стол 1 -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortTable_1)"
            :width="renderData.table.width"
            class="field"
            text="1"
            @click="emits('sortByField', CUTTING_TABLES.TABLE_1)"
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
            @click="emits('sortByField', CUTTING_TABLES.TABLE_2)"
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
            @click="emits('sortByField', CUTTING_TABLES.TABLE_3)"
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
            @click="emits('sortByField', CUTTING_TABLES.TABLE_0)"
        />

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
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortDetail)"
            :width="renderData.detail.width"
            class="field"
            :text="'Элемент' + getSortIcon(sortDetail)"
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

        <!-- __ Типовая конструкция чехла (ТКЧ) -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortTkch)"
            :width="renderData.tkch.width"
            class="field"
            :text="'ТКЧ' + getSortIcon(sortTkch)"
            @click="emits('sortByField', 'tkch')"
        />

        <!-- __ Кант -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortKant)"
            :width="renderData.kant.width"
            class="field"
            :text="'Кант' + getSortIcon(sortKant)"
            @click="emits('sortByField', 'kant')"
        />

        <!-- __ Состав -->
        <AppLabelTS
            v-if="showComments"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.describe.width"
            class="field"
            text="Состав"
        />

        <!-- __ Примечание 1 -->
        <AppLabelTS
            v-if="showComments"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.describe.width"
            class="field"
            text="Прим. 1"
        />

        <!-- __ Примечание 2 -->
        <AppLabelTS
            v-if="showComments"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.describe.width"
            class="field"
            text="Прим. 2"
        />

        <!-- __ Примечание 3 -->
        <AppLabelTS
            v-if="showComments"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.describe.width"
            class="field"
            text="Прим. 3"
        />

    </div>

</template>

<script lang="ts" setup>
import type { ICuttingLinesPanel, ICuttingTaskCardSort } from '@/types'
import type {
    IRenderCuttingLineData
} from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskCard.vue'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { CUTTING_TABLES } from '@/app/constants/cutting.ts'


interface IProps {
    renderData: IRenderCuttingLineData
    panel: ICuttingLinesPanel
    activePanel: ICuttingLinesPanel
    showComments?: boolean,
    showDetails?: boolean,
    sortPosition?: ICuttingTaskCardSort
    sortName?: ICuttingTaskCardSort
    sortTextile?: ICuttingTaskCardSort
    sortKant?: ICuttingTaskCardSort
    sortTkch?: ICuttingTaskCardSort
    sortAmount?: ICuttingTaskCardSort
    sortTime?: ICuttingTaskCardSort
    sortSize?: ICuttingTaskCardSort
    sortTable_1?: ICuttingTaskCardSort
    sortTable_2?: ICuttingTaskCardSort
    sortTable_3?: ICuttingTaskCardSort
    sortTable_0?: ICuttingTaskCardSort
    sortDetail?: ICuttingTaskCardSort
    sortMachine?: ICuttingTaskCardSort
}

const props = withDefaults(defineProps<IProps>(), {
    showComments: false,
    showDetails : false,
    sortPosition: 'none',
    sortName    : 'none',
    sortTextile : 'none',
    sortKant    : 'none',
    sortTkch    : 'none',
    sortAmount  : 'none',
    sortTime    : 'none',
    sortSize    : 'none',
    sortTable_1 : 'none',
    sortTable_2 : 'none',
    sortTable_3 : 'none',
    sortTable_0 : 'none',
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
