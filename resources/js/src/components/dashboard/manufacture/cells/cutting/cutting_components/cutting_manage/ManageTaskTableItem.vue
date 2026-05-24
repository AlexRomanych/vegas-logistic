<template>

    <!-- __ Отрисовываем только те строки, у которых есть чехол -->
    <div v-if="modelCover" class="flex">
        <!--<div class="flex">-->

        <!-- __ № п/п -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.position.toString()"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.position.width"
            class="field"
        />

        <!-- __ Размер модели -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="getSize"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="short ? renderData.sizeShort.width : renderData.size.width"
            class="field"
        />

        <!-- __ Название модели -->
        <AppLabelTS
            :rounded="DEFAULT_ROUNDED"
            :text="coverName"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="short ? renderData.modelShort.width : renderData.model.width"
            class="field"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.amount.toString()"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.amount.width"
            class="field"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="getTime"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTime === '00с' ? 'danger' : getType"
            :width="renderData.time.width"
        />

        <template v-if="!short">
            <!-- __ Стол 1 -->
            <AppLabelTS
                :align="DEFAULT_ALIGN"
                :rounded="DEFAULT_ROUNDED"
                :text-size="DEFAULT_TEXT_SIZE"
                :type="getTypeForTable(CUTTING_TABLES.TABLE_1)"
                :width="renderData.table.width"
                class="field"
                text="1"
            />

            <!-- __ Стол 2 -->
            <AppLabelTS
                :align="DEFAULT_ALIGN"
                :rounded="DEFAULT_ROUNDED"
                :text-size="DEFAULT_TEXT_SIZE"
                :type="getTypeForTable(CUTTING_TABLES.TABLE_2)"
                :width="renderData.table.width"
                class="field"
                text="2"
            />

            <!-- __ Стол 3 -->
            <AppLabelTS
                :align="DEFAULT_ALIGN"
                :rounded="DEFAULT_ROUNDED"
                :text-size="DEFAULT_TEXT_SIZE"
                :type="getTypeForTable(CUTTING_TABLES.TABLE_3)"
                :width="renderData.table.width"
                class="field"
                text="3"
            />

            <!-- __ Неопознанные -->
            <AppLabelTS
                :align="DEFAULT_ALIGN"
                :rounded="DEFAULT_ROUNDED"
                :text-size="DEFAULT_TEXT_SIZE"
                :type="getTypeForTable(CUTTING_TABLES.UNKNOWN)"
                :width="renderData.table.width"
                class="field"
                text="??"
            />
        </template>

        <!-- __ ШМ Пошива -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="sewingMachine"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.machine.width"
            class="field"
        />

        <!-- __ Элемент (Деталь) -->
        <template v-if="!short">
            <AppLabelTS
                :align="DEFAULT_ALIGN"
                :rounded="DEFAULT_ROUNDED"
                :text="cuttingLine.is_side ? DETAIL_SIDE_TITLE_SHORT : DETAIL_PANEL_TITLE"
                :text-size="DEFAULT_TEXT_SIZE"
                :type="cuttingLine.is_side ? 'warning' : 'indigo'"
                :width="renderData.detail.width"
                class="field"
            />
        </template>
        <template v-else>
            <AppLabelTS
                :align="DEFAULT_ALIGN"
                :rounded="DEFAULT_ROUNDED"
                :text="cuttingLine.is_side ? DETAIL_SIDE_TITLE_SHORT.charAt(0) : DETAIL_PANEL_TITLE.charAt(0)"
                :text-size="DEFAULT_TEXT_SIZE"
                :type="cuttingLine.is_side ? 'warning' : 'indigo'"
                :width="renderData.detailShort.width"
                class="field"
            />
        </template>

        <!-- __ Ткань -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.textile ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.textile.width"
            class="field"
        />

        <!-- __ Типовая конструкция чехла (ТКЧ) -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.model.main.tkch ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.tkch.width"
            class="field"
        />

        <!-- __ Кант -->
        <AppLabelTS
            v-if="showDetails"
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.model.main.kant ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.kant.width"
            class="field"
        />

        <!-- __ Состав -->
        <AppLabelTS
            v-if="showComments"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.composition ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

        <!-- __ Примечание 1 -->
        <AppLabelTS
            v-if="showComments"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.describe_1 ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

        <!-- __ Примечание 2 -->
        <AppLabelTS
            v-if="showComments"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.describe_2 ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

        <!-- __ Примечание 3 -->
        <AppLabelTS
            v-if="showComments"
            :rounded="DEFAULT_ROUNDED"
            :text="cuttingLine.order_line.describe_3 ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.describe.width"
            class="describe field"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { ICuttingTableKeys, ICuttingTaskLine } from '@/types'
import type {
    IRenderCuttingLineData
} from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_manage/ManageTaskCard.vue'

import { CUTTING_MACHINES, CUTTING_TABLES, DETAIL_PANEL_TITLE, DETAIL_SIDE_TITLE_SHORT } from '@/app/constants/cutting.ts'


import { storeToRefs } from 'pinia'
import { useCuttingStore } from '@/stores/CuttingStore.ts'
import {
    getCoverSizeString,
    getCuttingTaskModelCover,
    getCuttingTaskModelCoverName,
    getTimeString
} from '@/app/helpers/manufacture/helpers_cutting.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { getSewingMachineTitle } from '@/app/helpers/manufacture/helpers_textile.ts'


interface IProps {
    cuttingLine: ICuttingTaskLine
    renderData: IRenderCuttingLineData
    showComments?: boolean,
    showDetails?: boolean,
    short?: boolean
}


const props = withDefaults(defineProps<IProps>(), {
    showComments: false,
    showDetails : false,
    short       : false,
})

// console.log('props.cuttingLine: ', props.cuttingLine)

// __ Данные из Хранилища
const cuttingStore = useCuttingStore()

const { globalManageTaskCardActiveCuttingLine } = storeToRefs(cuttingStore)

const DEFAULT_ALIGN     = 'center'
const DEFAULT_TEXT_SIZE = 'micro'
const DEFAULT_ROUNDED   = '4'
const ACCENT_TYPE       = 'success'
const ACTIVE_TYPE       = 'primary'
// const DEFAULT_TYPE      = 'primary'


// __ Получаем чехол модели
// const modelCover = null
const modelCover = computed(() => getCuttingTaskModelCover(props.cuttingLine))

// __ Получаем название модели
const coverName = computed(() => getCuttingTaskModelCoverName(props.cuttingLine))

// __ Тип подсветки для основного элемента
const getType = computed(() =>
    props.cuttingLine === globalManageTaskCardActiveCuttingLine.value ? ACTIVE_TYPE : 'dark')

// __ Получаем тип стегальной машины
const table = computed(() => props.cuttingLine.table)

// __ Тип подсветки для стегальной машины элемента
const getTypeForTable = (cuttingTableTarget: ICuttingTableKeys) => {

    // !!! Порядок важен !!!
    if (table.value === cuttingTableTarget) {
        if (cuttingTableTarget === CUTTING_MACHINES.UNKNOWN) {
            return 'danger'
        }

        return ACCENT_TYPE
    }

    return props.cuttingLine === globalManageTaskCardActiveCuttingLine.value ? ACTIVE_TYPE : 'dark'
}

// __ Получаем трудозатраты
const getTime = computed(() => getTimeString(props.cuttingLine, true).replaceAll('.', ''))

// __ Получаем размер чехла (Высота из размеров чехла модели)
const getSize = computed(() => getCoverSizeString(props.cuttingLine))


const sewingMachine = computed(() => getSewingMachineTitle(props.cuttingLine))

// console.log('activeCuttingLine: ', globalManageTaskCardActiveCuttingLine.value)


</script>

<style scoped>
.describe {
    @apply truncate;
}

.field {
    @apply cursor-pointer;
}

</style>
