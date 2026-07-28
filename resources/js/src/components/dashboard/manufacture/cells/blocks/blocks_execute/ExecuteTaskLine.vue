<template>

    <div class="flex">

        <!-- __ Checker -->
        <AppLabelTSWrapper :render-object="render.checker"/>

        <!-- __ id -->
        <AppLabelTSWrapper :render-object="render.id"/>

        <!-- __ position -->
        <AppLabelTSWrapper :render-object="render.position"/>

        <!-- __ Модель (Блок) -->
        <AppLabelTSWrapper :render-object="render.name"/>

        <!-- __ Количество -->
        <AppLabelTSWrapper :render-object="render.amount"/>

        <!-- __ Площадь -->
        <AppLabelTSWrapper :render-object="render.square"/>

        <!-- __ Трудозатраты -->
        <AppLabelTSWrapper :render-object="render.time"/>

        <!-- __ Линия 1 -->
        <AppLabelTSWrapper :render-object="render.line_1"/>

        <!-- __ Линия 2 -->
        <AppLabelTSWrapper :render-object="render.line_2"/>

        <!-- __ Линия ?? -->
        <AppLabelTSWrapper :render-object="render.line_0"/>

        <!-- __ КДБ -->
        <AppLabelTSWrapper :render-object="render.kdb"/>

        <!-- __ Описание -->
        <AppLabelTSWrapper
            :render-object="render.description"
            @dblclick="changeDescription"
        />

        <!-- __ Время выполнения -->
        <AppLabelTSWrapper :render-object="render.finished_at"/>

        <!-- __ Причина невыполнения -->
        <AppLabelTSWrapper :render-object="render.false_reason"/>

    </div>

    <!-- __ Модальное окно для изменения/добавления комментария -->
    <CommentEdit
        ref="commentEdit"
        :comment="comment"
        label="Комментарий к Блоку"
    />

</template>

<script lang="ts" setup>
import { computed, reactive, ref } from 'vue'

import type { IColorTypes, IRenderData, IBlockTaskLine, IBlockManufLine } from '@/types'

import { BLOCK_MANUF_LINES } from '@/app/constants/blocks.ts'

import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import { getBlockTaskLineSquare, getTimeString } from '@/app/helpers/manufacture/helpers_blocks.ts'
import CommentEdit from '@/components/dashboard/manufacture/cells/blocks/common/CommentEdit.vue'
// import AppLabelMultilineTSWrapper
//     from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'


interface IProps {
    blockLine: IBlockTaskLine
    fieldsWidth: Record<string, string>
}

const props = defineProps<IProps>()

const emits = defineEmits<{
    (e: 'changeDescription', payload: string): void
}>()

// __ Объект отображения данных
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const COLLAPSED_WIDTH    = 'w-[30px]'
const DEFAULT_HEIGHT   = 'h-[20px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const ACCENT_TYPE      = 'success'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'

const render: IRenderData = reactive({
    checker     : {
        id            : () => 'id-checker',
        header        : ['', ''],
        width         : props.fieldsWidth.checker,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍?...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine.finished_at ? '✓' : '✗'
    },
    id          : {
        id            : () => 'id-search',
        header        : ['', ''],
        width         : props.fieldsWidth.id,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine.id.toString()
    },
    position    : {
        id            : () => 'position-search',
        header        : ['', ''],
        width         : props.fieldsWidth.position,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍№ п/п...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine.position.toString(),
    },
    name        : {
        id            : () => 'name-search',
        header        : ['', ''],
        width         : props.fieldsWidth.name,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Чехол...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine.block.name
    },
    amount      : {
        id            : () => 'amount-search',
        header        : ['', ''],
        width         : props.fieldsWidth.amount,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Кол-во...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine.amount.toString()
    },
    square      : {
        id            : () => 'square-search',
        header        : ['', ''],
        width         : props.fieldsWidth.square,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍S...',
        data          : (/*blockLine: IBlockTaskLine*/) => getBlockTaskLineSquare(props.blockLine).toFixed(2)
    },
    time        : {
        id            : () => 'time-search',
        header        : ['', ''],
        width         : props.fieldsWidth.time,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => getTime.value === '00с' ? 'danger' : getType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Время...',
        data          : (/*blockLine: IBlockTaskLine*/) => getTime.value
    },
    line_1      : {
        id            : () => 'line-1-search',
        header        : ['', ''],
        width         : props.fieldsWidth.line,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => getTypeForLine(BLOCK_MANUF_LINES.LINE_1),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Линия 1...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine?.manuf_line === BLOCK_MANUF_LINES.LINE_1 ? '1' : '',
    },
    line_2      : {
        id            : () => 'line-2-search',
        header        : ['', ''],
        width         : props.fieldsWidth.line,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => getTypeForLine(BLOCK_MANUF_LINES.LINE_2),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Линия 2...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine?.manuf_line === BLOCK_MANUF_LINES.LINE_2 ? '2' : '',
    },
    line_0      : {
        id            : () => 'line-0-search',
        header        : ['', ''],
        width         : props.fieldsWidth.line,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => getTypeForLine(BLOCK_MANUF_LINES.LINE_0),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Линия ??...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine?.manuf_line === BLOCK_MANUF_LINES.LINE_0 ? '??' : '',
    },
    kdb         : {
        id            : () => 'kdb-search',
        header        : ['', ''],
        width         : props.fieldsWidth.kdb,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍КДБ...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine.block.collection.kdb ? props.blockLine.block.collection.kdb.kdb : ''
    },
    description : {
        id            : () => 'description-search',
        header        : ['', ''],
        width         : props.fieldsWidth.description,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Примечание...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine.description ?? '',
        title         : 'Double Click - Изменить комментарий',
        class         : 'truncate'
    },
    finished_at : {
        id            : () => 'finished-at-search',
        header        : ['', ''],
        width         : props.fieldsWidth.finished_at,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => finishedAtType.value,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Время...',
        data          : (/*blockLine: IBlockTaskLine*/) =>
            props.blockLine.finished_at ?
                formatTimeInFullFormat(props.blockLine.finished_at) :
                props.blockLine.false_at ?
                    formatTimeInFullFormat(props.blockLine.false_at) : '',
    },
    false_reason: {
        id            : () => 'false-reason-search',
        header        : ['', ''],
        width         : props.fieldsWidth.false_reason,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => props.blockLine.false_reason ? 'danger' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Причина...',
        data          : (/*blockLine: IBlockTaskLine*/) => props.blockLine.false_reason ?? '',
        class         : 'truncate',
    },
})

// __ Тип подсветки для стегальной машины элемента
const getTypeForLine = (blockTableTarget: IBlockManufLine) => {

    // !!! Порядок важен !!!

    if (line.value === blockTableTarget) {
        if (blockTableTarget === BLOCK_MANUF_LINES.LINE_0) {
            return 'danger'
        }
        return ACCENT_TYPE
    }

    return DEFAULT_TYPE
}


// __ Получаем трудозатраты
const getTime = computed(() => getTimeString(props.blockLine, true).replaceAll('.', ''))


// __ Получаем тип стегальной машины
const line = computed(() => props.blockLine.manuf_line)

// __ Тип подсветки для основного элемента
const getType = computed<IColorTypes>(() =>
        DEFAULT_TYPE
    // props.blockLine === globalManageTaskCardActiveBlockLine.value ? ACTIVE_TYPE : 'dark'
)

// __ Тип подсветки для выполненного элемента
const finishedAtType = computed<IColorTypes>(() => props.blockLine.finished_at ? 'success' : 'danger')


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
