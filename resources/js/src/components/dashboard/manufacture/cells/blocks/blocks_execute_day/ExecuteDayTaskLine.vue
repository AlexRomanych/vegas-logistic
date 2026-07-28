<template>


    <div class="flex items-center">
        <div
            :class="[getCheckClass(blockLine)]"
            class="w-[25px] h-[30px] rounded flex items-center justify-center transition-all"
        >
            <span :class="getCheckClass(blockLine)" class="text-[12px] font-semibold text-white">
                {{ getCheckSymbol(blockLine) }}
            </span>
        </div>

        <div :class="fieldWidths.space"></div>

        <!-- __ Позиция -->
        <AppLabelTS
            :height="lineHeight"
            :text="ordering === 'position' ? blockLine.position.toString() : index.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(blockLine)"
            :width="fieldWidths.position"
            align="center"
            rounded="4"
        />

        <!-- __ Название Блока -->
        <AppLabelTS
            :height="lineHeight"
            :text="blockLine.block.name"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(blockLine)"
            :width="fieldWidths.name"
            rounded="4"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :height="lineHeight"
            :text="blockLine.amount.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(blockLine)"
            :width="fieldWidths.amount"
            align="center"
            rounded="4"
        />

        <!-- __ Трудозатраты -->
        <AppLabelTS
            :height="lineHeight"
            :text="time"
            :text-size="LINE_TEXT_SIZE"
            :type="time === '00с' ? 'danger' : getCheckType(blockLine)"
            :width="fieldWidths.time"
            align="center"
            rounded="4"
        />

        <!-- __ Площадь -->
        <AppLabelTS
            :height="lineHeight"
            :text="square"
            :text-size="LINE_TEXT_SIZE"
            :type="time === '00с' ? 'danger' : getCheckType(blockLine)"
            :width="fieldWidths.square"
            align="center"
            rounded="4"
        />

        <!-- __ Производственная Линия -->
        <AppLabelTS
            :height="lineHeight"
            :text="blockLine.manuf_line"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(blockLine)"
            :width="fieldWidths.manuf_line"
            align="center"
            rounded="4"
        />

        <!-- __ КДБ -->
        <AppLabelTS
            :class="kdbId ? 'cursor-pointer' : ''"
            :height="lineHeight"
            :text="kdb"
            :text-size="LINE_TEXT_SIZE"
            :type="kdbId ? 'indigo' : 'dark'"
            :width="fieldWidths.kdb"
            align="center"
            rounded="4"
            @click="showDoc"
        />

        <!-- __ finished_at -->
        <AppLabelTS
            :height="lineHeight"
            :text="
                blockLine.finished_at ?
                    formatTimeInFullFormat(blockLine.finished_at) :
                    blockLine.false_at ?
                        formatTimeInFullFormat(blockLine.false_at) :
                        ''
            "
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(blockLine)"
            :width="fieldWidths.timeLabel"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Причина не выполнения -->
        <AppLabelTS
            :height="lineHeight"
            :text="blockLine.false_reason ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(blockLine)"
            :width="fieldWidths.false_reason"
            align="left"
            class="truncate"
            rounded="4"
        />

        <!-- __ Описание -->
        <AppLabelTS
            :height="lineHeight"
            :text="blockLine.description ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(blockLine)"
            :width="fieldWidths.description"
            align="left"
            class="truncate cursor-pointer"
            rounded="4"
            title="Double Click - Изменить Комментарий"
            @dblclick="changeDescription"
        />

        <!-- __ Заявка -->
        <AppLabelTS
            :height="lineHeight"
            :text="blockLine.groupAttr"
            :text-size="LINE_TEXT_SIZE"
            :type="getCheckType(blockLine)"
            :width="fieldWidths.order"
            align="left"
            rounded="4"
        />

    </div>

    <!-- __ Модальное окно для изменения/добавления комментария -->
    <CommentEdit
        ref="commentEdit"
        :comment="comment"
        label="Комментарий к Блоку"
    />

</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'

import type { IColorTypes, IBlockTaskLine } from '@/types'

import {
    getBlockTaskLineSquare,
    getTimeString
} from '@/app/helpers/manufacture/helpers_blocks.ts'
import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import CommentEdit from '@/components/dashboard/manufacture/cells/blocks/common/CommentEdit.vue'

interface IProps {
    blockLine: IBlockTaskLine
    fieldWidths: Record<string, string>
    index?: number
    ordering?: 'index' | 'position'
}

const props = withDefaults(defineProps<IProps>(), {
    index   : 0,
    ordering: 'position'
})


const emits = defineEmits<{
    (e: 'showDocument', payload: number): void
    (e: 'changeDescription', payload: string): void
}>()

// const LINE_HEIGHT    = 'h-[25px]'
const LINE_TYPE      = 'dark'
const LINE_TEXT_SIZE = 'mini'

// __ Получаем символ завершенности
const getCheckSymbol = (blockLine: IBlockTaskLine) => {
    if (!blockLine.finished_at && !blockLine.false_at) {
        return ''
    }

    if (blockLine.finished_at) {
        return '✓'
    }

    return '✘'
}


// __ Получаем класс завершенности
const getCheckClass = (blockLine: IBlockTaskLine) => {
    if (!blockLine.finished_at && !blockLine.false_at) {
        return 'bg-slate-400'
    }

    if (blockLine.finished_at) {
        return 'bg-green-500'
    }

    return 'bg-red-500'
}


// __ Получаем тип завершенности
const getCheckType = (blockLine: IBlockTaskLine): IColorTypes => {
    if (!blockLine.finished_at && !blockLine.false_at) {
        return LINE_TYPE
    }

    if (blockLine.finished_at) {
        return 'success'
    }

    return 'danger'
}

// __ Возвращаем высоту строки в зависимости от количества ПС
const lineHeight = computed(() => {
    return 'h-[30px]'
    // switch (fabric.value.length) {
    //     case 1:
    //
    //     case 2:
    //         return 'h-[30px]'
    //     case 3:
    //         return 'h-[45px]'
    //     default:
    //         return 'h-[30px]'
    // }
})

// __ Получаем трудозатраты
const time = computed(() => getTimeString(props.blockLine, true).replaceAll('.', ''))

// __ Получаем Площадь
const square = computed(() => getBlockTaskLineSquare(props.blockLine).toFixed(3))
// const square = computed(() => (props.blockLine.block.length * props.blockLine.block.width * props.blockLine.amount / 100 / 100).toFixed(3))

// __ Получаем КДБ
const kdbId = computed(() => props.blockLine.block.collection.kdb?.id)
const kdb   = computed(() => kdbId.value ? props.blockLine.block.collection.kdb?.kdb + '🔍' : '')

const showDoc = () => {
    if (!kdbId.value) {
        return
    }
    emits('showDocument', kdbId.value)
}

// __ Тип для модального окна изменения Комментария
const comment     = ref('')
const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null)

// __ Меняем Комментарий
const changeDescription = async () => {
    comment.value = props.blockLine.description ?? '' // __ Устанавливаем комментарий

    const answer = await commentEdit.value!.show()
    if (answer) {
        const newComment = commentEdit.value!.comment.trim()
        emits('changeDescription', newComment)
    }
}

</script>

<style scoped>

</style>
