<template>

    <!-- __ Отрисовываем только те строки, у которых есть чехол -->
    <div class="flex">
        <!--<div class="flex">-->

        <!-- __ № п/п -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="blockLine.position.toString()"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.position.width"
            class="field"
        />

        <!-- __ Название модели -->
        <AppLabelTS
            :rounded="DEFAULT_ROUNDED"
            :text="blockLine.block.name"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="short ? renderData.modelShort.width : renderData.model.width"
            class="field"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text="blockLine.amount.toString()"
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

        <!-- __ Линия 1 -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForLine(BLOCK_MANUF_LINES.LINE_1)"
            :width="renderData.line.width"
            class="field"
            text="1"
        />

        <!-- __ Линия 2 -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForLine(BLOCK_MANUF_LINES.LINE_2)"
            :width="renderData.line.width"
            class="field"
            text="2"
        />

        <!-- __ Неопознанные -->
        <AppLabelTS
            :align="DEFAULT_ALIGN"
            :rounded="DEFAULT_ROUNDED"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getTypeForLine(BLOCK_MANUF_LINES.LINE_0)"
            :width="renderData.line.width"
            class="field"
            text="??"
        />

        <!-- __ Номер заявки -->
        <AppLabelTS
            v-if="showDetails"
            :align="'left'"
            :rounded="DEFAULT_ROUNDED"
            :text="blockLine.groupAttr ?? ''"
            :text-size="DEFAULT_TEXT_SIZE"
            :type="getType"
            :width="renderData.order.width"
            class="field"
        />

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue'

import type { IBlockManufLine, IBlockManufLinesPanel, IBlockTaskLine } from '@/types'
import type {
    IRenderBlockLineData
} from '@/components/dashboard/manufacture/cells/blocks/blocks_manage/ManageTaskCard.vue'

import { BLOCK_MANUF_LINES } from '@/app/constants/blocks.ts'


import { storeToRefs } from 'pinia'
import { useBlocksStore } from '@/stores/BlocksStore.ts'
import {
    getTimeString
} from '@/app/helpers/manufacture/helpers_blocks.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'


interface IProps {
    blockLine: IBlockTaskLine
    panel: IBlockManufLinesPanel
    renderData: IRenderBlockLineData
    showDetails?: boolean,
    short?: boolean
}


const props = withDefaults(defineProps<IProps>(), {
    showDetails: false,
    short      : false,
})

// console.log('props.blockLine: ', props.blockLine)

// __ Данные из Хранилища
const blockStore = useBlocksStore()

const { globalManageTaskCardActiveBlockLine } = storeToRefs(blockStore)

const DEFAULT_ALIGN     = 'center'
const DEFAULT_TEXT_SIZE = 'micro'
const DEFAULT_ROUNDED   = '4'
const ACCENT_TYPE       = 'success'
const ACTIVE_TYPE       = 'primary'
// const DEFAULT_TYPE      = 'primary'


// __ Тип подсветки для основного элемента
// __ Определяем, основная Линия или нет для подсветки
const getType = computed(() => {
    if (props.panel !== props.blockLine.block.collection.manuf_line) {
        if (props.blockLine.block.collection.manuf_line === BLOCK_MANUF_LINES.LINE_1) {
            return 'indigo'
        } else if (props.blockLine.block.collection.manuf_line === BLOCK_MANUF_LINES.LINE_2) {
            return 'orange'
        }
    }

    return props.blockLine === globalManageTaskCardActiveBlockLine.value ? ACTIVE_TYPE : 'dark'
})

// __ Получаем тип стегальной машины
const line = computed(() => props.blockLine.manuf_line)

// __ Тип подсветки для стегальной машины элемента
const getTypeForLine = (blockLineTarget: IBlockManufLine) => {

    // !!! Порядок важен !!!
    if (line.value === blockLineTarget) {
        if (blockLineTarget === BLOCK_MANUF_LINES.LINE_0) {
            return 'danger'
        }

        return ACCENT_TYPE
    }

    // __ Посвечиваем доступные столы
    if (blockLineTarget === props.blockLine.block.collection.manuf_line ||
        blockLineTarget === props.blockLine.block.collection.manuf_line_alt) {
        if (blockLineTarget.toString() === BLOCK_MANUF_LINES.LINE_1.toString()) {
            return 'indigo'
        } else if (blockLineTarget.toString() === BLOCK_MANUF_LINES.LINE_2.toString()) {
            return 'orange'
        }
    }

    return props.blockLine === globalManageTaskCardActiveBlockLine.value ? ACTIVE_TYPE : 'dark'
}

// __ Получаем трудозатраты
const getTime = computed(() => getTimeString(props.blockLine, true).replaceAll('.', ''))


</script>

<style scoped>
.describe {
    @apply truncate;
}

.field {
    @apply cursor-pointer;
}

</style>
