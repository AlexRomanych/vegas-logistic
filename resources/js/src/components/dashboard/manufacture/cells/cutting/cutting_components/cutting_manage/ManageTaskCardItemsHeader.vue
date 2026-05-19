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
            text="Размер"
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
            text="Модель"
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
            text="Тр-ты"
            @click="emits('sortByField', 'time')"
        />

        <!-- __ УШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortUniversal)"
            :width="renderData.machine.width"
            class="field"
            text="У"
            @click="emits('sortByField', 'universal')"
        />

        <!-- __ АШМ -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortAuto)"
            :width="renderData.machine.width"
            class="field"
            text="А"
            @click="emits('sortByField', 'auto')"
        />

        <!-- __ Глухие сложные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortSolidHard)"
            :width="renderData.machine.width"
            class="field"
            text="ГС"
            @click="emits('sortByField', 'solid_hard')"
        />

        <!-- __ Глухие простые -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getPositionBySort(sortSolidLite)"
            :width="renderData.machine.width"
            class="field"
            text="ГП"
            @click="emits('sortByField', 'solid_lite')"
        />

        <!-- __ Неопознанные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="DEFAULT_TYPE"
            :width="renderData.machine.width"
            class="field"
            text="Н"
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
            text="Ткань"
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
            text="ТКЧ"
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
            text="Кант"
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


interface IProps {
    renderData: IRenderCuttingLineData
    panel: ICuttingLinesPanel
    activePanel: ICuttingLinesPanel
    showComments?: boolean,
    showDetails?: boolean,
    sortPosition?: ICuttingTaskCardSort
    sortName?: ICuttingTaskCardSort
    sortUniversal?: ICuttingTaskCardSort
    sortAuto?: ICuttingTaskCardSort
    sortSolidHard?: ICuttingTaskCardSort
    sortSolidLite?: ICuttingTaskCardSort
    sortTextile?: ICuttingTaskCardSort
    sortKant?: ICuttingTaskCardSort
    sortTkch?: ICuttingTaskCardSort
    sortAmount?: ICuttingTaskCardSort
    sortTime?: ICuttingTaskCardSort
    sortSize?: ICuttingTaskCardSort
}

const props = withDefaults(defineProps<IProps>(), {
    showComments:  false,
    showDetails:   false,
    sortPosition:  'none',
    sortName:      'none',
    sortUniversal: 'none',
    sortAuto:      'none',
    sortSolidHard: 'none',
    sortSolidLite: 'none',
    sortTextile:   'none',
    sortKant:      'none',
    sortTkch:      'none',
    sortAmount:    'none',
    sortTime:      'none',
    sortSize:      'none',
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

</script>

<style scoped>
.field {
    @apply cursor-pointer;
}
</style>
