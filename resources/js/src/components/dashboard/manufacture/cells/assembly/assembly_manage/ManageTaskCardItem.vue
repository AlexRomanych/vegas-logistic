<template>
    <div class="flex">

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
            class="field"
            align="center"
        />

        <!-- __ Размер -->
        <AppLabelTS
            :rounded="DEFAULT_ROUNDED"
            :text="getOrderLineSize(assemblyLine)"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.size.width"
            class="field"
            align="center"
        />

        <!-- __ Название Модели -->
        <AppLabelTS
            :rounded="DEFAULT_ROUNDED"
            :text="assemblyLine.order_line.model.name_report"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.model.width"
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
            :type="getTypeForAssemblyLine(ASSEMBLY_LINE_LAMIT)"
            :width="renderData.line.width"
            class="field"
            text="Л"
        />

        <!-- __ Столы -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForAssemblyLine(ASSEMBLY_LINE_TABLE)"
            :width="renderData.line.width"
            class="field"
            text="С"
        />

    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IAssemblyLineKeys, IAssemblyTaskLine } from '@/types'
import type { IRenderAssemblyLineData } from '@/components/dashboard/manufacture/cells/assembly/assembly_manage/ManageTaskCard.vue'

import { ASSEMBLY_LINE_LAMIT, ASSEMBLY_LINE_TABLE, ASSEMBLY_LINES } from '@/app/constants/assembly.ts'


import { storeToRefs } from 'pinia'
import { useAssemblyStore } from '@/stores/AssemblyStore.ts'
import {
    getOrderLineSize,
    getTimeString
} from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


interface IProps {
    assemblyLine: IAssemblyTaskLine
    renderData: IRenderAssemblyLineData
    showComments?: boolean,
    showDetails?: boolean,
}


const props = withDefaults(defineProps<IProps>(), {
    showComments: false,
    showDetails : false,
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


// __ Тип подсветки для основного элемента
const getType = computed(() =>
    props.assemblyLine === globalManageTaskCardActiveAssemblyLine.value ? ACTIVE_TYPE : 'dark')

// __ Получаем Производственной Линии
const manufLine = computed(() => props.assemblyLine.assembly_line)

// __ Тип подсветки для Производственной Линии
const getTypeForAssemblyLine = (assemblyLineTarget: IAssemblyLineKeys) => {
    // !!! Порядок важен !!!
    if (manufLine.value === assemblyLineTarget) {
        if (assemblyLineTarget === ASSEMBLY_LINES.ASSEMBLY_LINE_UNDEFINED) {
            return 'danger'
        }

        return ACCENT_TYPE
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
