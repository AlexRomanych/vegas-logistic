<template>
    <template v-for="sector of assemblyLine.sector_lines" :key="sector.id">

        <div class="flex">

            <!-- __ Checker -->
            <AppLabelTSWrapper :render-object="render.checker"/>

            <!-- __ id -->
            <AppLabelTSWrapper :render-object="render.id"/>

            <!-- __ Участок -->
            <AppLabelTSWrapper :arg="sector" :render-object="render.sector"/>

            <!-- __ Размер детальки -->
            <AppLabelTSWrapper :arg="sector" :render-object="render.size"/>

            <!-- __ Код материала из 1С -->
            <AppLabelTSWrapper :arg="sector" :render-object="render.code_1c"/>

            <!-- __ Название материала из 1С -->
            <AppLabelTSWrapper :arg="sector" :render-object="render.name"/>

            <!-- __ Количество Деталек -->
            <AppLabelTSWrapper :arg="sector" :render-object="render.amount"/>

            <!-- __ Трудозатраты -->
            <AppLabelTSWrapper :arg="sector" :render-object="render.time"/>

            <!-- __ Расход -->
            <AppLabelTSWrapper :arg="sector" :render-object="render.expense"/>

            <!-- __ Описание -->
            <AppLabelTSWrapper
                :arg="sector"
                :render-object="render.description"
                @dblclick="changeDescription(sector)"
            />

            <!-- __ Время выполнения -->
            <AppLabelTSWrapper :render-object="render.finished_at"/>

            <!-- __ Причина невыполнения -->
            <AppLabelTSWrapper :render-object="render.false_reason"/>

        </div>
    </template>

    <!-- __ Модальное окно для изменения/добавления комментария -->
    <CommentEdit
        ref="commentEdit"
        :comment="comment"
        label="Комментарий к строке"
    />

</template>

<script lang="ts" setup>
import { computed, reactive, ref } from 'vue'

import type { IColorTypes, IRenderData, IAssemblyTaskLine, IAssemblyTaskLineSector, IAssemblySectorKeys } from '@/types'

import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'
import { getSectorAmount, getSectorByName, getSectorExpense, getSectorSize, getSectorTime } from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import CommentEdit from '@/components/dashboard/manufacture/cells/assembly/common/CommentEdit.vue'

// import AppLabelMultilineTSWrapper
//     from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'


interface IProps {
    assemblyLine: IAssemblyTaskLine
    fieldsWidth: Record<string, string>
}

const props = defineProps<IProps>()

// console.log('props.assemblyLine: ', props.assemblyLine)

const emits = defineEmits<{
    (e: 'changeDescription', sector: IAssemblyTaskLineSector, description: string): void
}>()

// __ Объект отображения данных
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const COLLAPSED_WIDTH    = 'w-[30px]'
const DEFAULT_HEIGHT   = 'h-[20px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'

// __ Получаем название Участка
const getSectorName = (assemblySector: IAssemblyTaskLineSector) => {
    if (!assemblySector) return DEFAULT_TYPE
    const sector = getSectorByName(assemblySector.sector as IAssemblySectorKeys)
    return sector ? sector.TITLE : ''
}

// __ Получаем тип Участка
const getSectorType = (assemblySector: IAssemblyTaskLineSector) => {
    if (assemblySector?.finished_at) return 'success'
    if (assemblySector?.false_at) return 'danger'
    return DEFAULT_TYPE

    // if (!assemblySector) return DEFAULT_TYPE
    // const sector = getSectorByName(assemblySector.sector as IAssemblySectorKeys)
    // return sector ? sector.TYPE : DEFAULT_TYPE
}

const render: IRenderData = reactive({
    checker     : {
        id            : () => 'id-checker',
        header        : ['', ''],
        width         : props.fieldsWidth.checker,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍?...',
        data          : (assemblySector: IAssemblyTaskLineSector) => assemblySector?.finished_at ? '✓' : '✗',
    },
    id          : {
        id            : () => 'id-search',
        header        : ['', ''],
        width         : props.fieldsWidth.id,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (assemblySector: IAssemblyTaskLineSector) => assemblySector.id.toString(),
    },
    sector      : {
        id            : () => 'sector-search',
        header        : ['', ''],
        width         : props.fieldsWidth.sector,
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Участок...',
        data          : (assemblySector: IAssemblyTaskLineSector) => getSectorName(assemblySector),
    },
    size        : {
        id            : () => 'size-search',
        header        : ['', ''],
        width         : props.fieldsWidth.size,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Размер...',
        data          : (assemblySector: IAssemblyTaskLineSector) => getSectorSize(assemblySector),
    },
    code_1c        : {
        id            : () => 'code-1c-search',
        header        : ['', ''],
        width         : props.fieldsWidth.code_1c,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Код 1С...',
        data          : (assemblySector: IAssemblyTaskLineSector) => assemblySector.material_code_1c,
    },
    name        : {
        id            : () => 'name-search',
        header        : ['', ''],
        width         : props.fieldsWidth.name,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Материал...',
        data          : (assemblySector: IAssemblyTaskLineSector) => assemblySector.material_name,
    },
    amount      : {
        id            : () => 'amount-search',
        header        : ['', ''],
        width         : props.fieldsWidth.amount,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => getSectorAmount(assemblySector) === 0 ? 'danger' : getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Кол-во...',
        data          : (assemblySector: IAssemblyTaskLineSector) => getSectorAmount(assemblySector).toFixed(0)
    },
    expense      : {
        id            : () => 'expense-search',
        header        : ['', ''],
        width         : props.fieldsWidth.expense,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Расход...',
        data          : (assemblySector: IAssemblyTaskLineSector) => getSectorExpense(assemblySector).toFixed(3)
    },
    time        : {
        id            : () => 'time-search',
        header        : ['', ''],
        width         : props.fieldsWidth.time,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => getSectorTime(assemblySector) === 0 ? 'danger' : getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Время...',
        data          : (assemblySector: IAssemblyTaskLineSector) => getSectorTime(assemblySector).toFixed(0)
    },
    description : {
        id            : () => 'description-search',
        header        : ['', ''],
        width         : props.fieldsWidth.description,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => assemblySector?.description ? 'warning' : getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Примечание...',
        data          : (assemblySector: IAssemblyTaskLineSector) => assemblySector?.description ?? '',
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
        data          : (assemblySector: IAssemblyTaskLineSector) =>
            assemblySector?.finished_at ?
                formatTimeInFullFormat(assemblySector?.finished_at) :
                assemblySector?.false_at ?
                    formatTimeInFullFormat(assemblySector?.false_at) : '',
    },
    false_reason: {
        id            : () => 'false-reason-search',
        header        : ['', ''],
        width         : props.fieldsWidth.false_reason,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (assemblySector: IAssemblyTaskLineSector) => assemblySector?.false_reason ? 'danger' : getSectorType(assemblySector),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Причина...',
        data          : (assemblySector: IAssemblyTaskLineSector) => assemblySector?.false_reason ?? '',
        class         : 'truncate',
    },
})


// __ Тип подсветки для выполненного элемента
const finishedAtType = computed<IColorTypes>(() => props.assemblyLine.finished_at ? 'success' : 'danger')


// __ Тип для модального окна изменения Комментария
const comment     = ref('')
const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null)

// __ Меняем Комментарий
const changeDescription = async (sector: IAssemblyTaskLineSector) => {
    comment.value = sector.description ?? '' // __ Устанавливаем комментарий

    const answer = await commentEdit.value!.show()
    if (answer) {
        const newComment = commentEdit.value!.comment.trim()
        emits('changeDescription', sector, newComment)
    }
}

</script>

<style scoped>

</style>
