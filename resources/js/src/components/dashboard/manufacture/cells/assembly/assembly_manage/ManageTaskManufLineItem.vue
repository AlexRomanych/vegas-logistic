<template>

    <div class="flex">
        <!--<div class="flex">-->

        <!-- __ № п/п -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="assemblyLine.position.toString()"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.position.width"
            class="field"
        />

        <!-- __ Тип модели -->
        <AppLabelTS
            :rounded="DEFAULT_ROUNDED"
            :text="assemblyLine.order_line.model.model_type"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.modelType.width"
            align="center"
            class="field"
        />

        <!-- __ Размер -->
        <AppLabelTS
            :rounded="DEFAULT_ROUNDED"
            :text="getOrderLineSize(assemblyLine)"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.size.width"
            align="center"
            class="field"
        />

        <!-- __ Название модели -->
        <AppLabelTS
            :rounded="DEFAULT_ROUNDED"
            :text="assemblyLine.order_line.model.name_report"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="short ? renderData.modelShort.width : renderData.model.width"
            class="field"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="assemblyLine.amount.toString()"
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

        <!-- __ Ламит -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForLine(ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT)"
            :width="renderData.line.width"
            class="field"
            text="Л"
        />

        <!-- __ Столы -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForLine(ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE)"
            :width="renderData.line.width"
            class="field"
            text="С"
        />

        <!-- __ Номер заявки -->
        <AppLabelTS
            v-if="showDetails"
            :align="'left'"
            :rounded="DEFAULT_ROUNDED"
            :text="assemblyLine.groupAttr ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.order.width"
            class="field"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IAssemblyLineKeys, IAssemblyManufLinesPanel, IAssemblyTaskLine } from '@/types'
import type {
    IRenderAssemblyLineData
} from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskCard.vue'

import { ASSEMBLY_LINES } from '@/app/constants/assembly.ts'


import { storeToRefs } from 'pinia'
import { useAssemblyStore } from '@/stores/AssemblyStore.ts'
import {
    getTimeString
} from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import { getOrderLineSize } from '@/app/helpers/manufacture/helpers_assembly.ts'


interface IProps {
    assemblyLine: IAssemblyTaskLine
    panel: IAssemblyManufLinesPanel
    renderData: IRenderAssemblyLineData
    showDetails?: boolean,
    short?: boolean
}


const props = withDefaults(defineProps<IProps>(), {
    showDetails: false,
    short      : false,
})

// console.log('props.assemblyLine: ', props.assemblyLine)

// __ Данные из Хранилища
const assemblyStore = useAssemblyStore()

const { globalManageTaskCardActiveAssemblyLine } = storeToRefs(assemblyStore)

const DEFAULT_ALIGN     = 'center'
const DEFAULT_TEXT_SIZE = 'micro'
const DEFAULT_ROUNDED   = '4'
const ACCENT_TYPE       = 'success'
const ACTIVE_TYPE       = 'primary'
// const DEFAULT_TYPE      = 'primary'

// __ Получаем Сборочную Линию
const line = computed(() => props.assemblyLine.order_line.model.assembly_line)

// __ Тип подсветки для основного элемента
// __ Определяем, основная Линия или нет для подсветки
const getType = computed(() => {
    if (props.panel !== line.value) {
        if (line.value === ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT) {
            return 'indigo'
        } else if (line.value === ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE) {
            return 'orange'
        }
    }

    return props.assemblyLine === globalManageTaskCardActiveAssemblyLine.value ? ACTIVE_TYPE : 'dark'
})

// __ Тип подсветки для стегальной машины элемента
const getTypeForLine = (assemblyLineTarget: IAssemblyLineKeys) => {

    // !!! Порядок важен !!!
    if (line.value === assemblyLineTarget) {
        if (assemblyLineTarget === ASSEMBLY_LINES.ASSEMBLY_LINE_UNDEFINED) {
            return 'danger'
        }

        return ACCENT_TYPE
    }

    // __ Посвечиваем доступные столы
    if (assemblyLineTarget === line.value) {
        if (assemblyLineTarget.toString() === ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT) {
            return 'indigo'
        } else if (assemblyLineTarget.toString() === ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE) {
            return 'orange'
        }
    }

    return props.assemblyLine === globalManageTaskCardActiveAssemblyLine.value ? ACTIVE_TYPE : 'dark'
}

// __ Получаем трудозатраты
const getTime = computed(() => getTimeString(props.assemblyLine, true).replaceAll('.', ''))


</script>

<style scoped>
.describe {
    @apply truncate;
}

.field {
    @apply cursor-pointer;
}

</style>
