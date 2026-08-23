<template>

    <div class="flex items-center">
        <div
            :class="[getCheckClass(groupLine.order_line_attr.render_type)]"
            class="w-[25px] h-[30px] rounded flex items-center justify-center transition-all mr-[2px]"
        >
            <span :class="getCheckClass(groupLine.order_line_attr.render_type)" class="text-[12px] font-semibold text-white">
                {{ checkSymbol }}
            </span>
        </div>

        <!--<div :class="fieldWidths.space"></div>-->

        <!-- __ Позиция -->
        <AppLabelTS
            :height="lineHeight"
            :text="ordering === 'position' ? groupLine.order_line.id.toString() : index.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.position"
            align="center"
            rounded="4"
        />

        <!-- __ Размер -->
        <AppLabelTS
            :height="lineHeight"
            :text="getOrderLineSize(groupLine.order_line)"
            :text-size="LINE_TEXT_SIZE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.size"
            align="center"
            rounded="4"
        />

        <!-- __ Название Модели -->
        <AppLabelTS
            :height="lineHeight"
            :text="groupLine.order_line.model.name_report"
            :text-size="LINE_TEXT_SIZE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.name"
            rounded="4"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :height="lineHeight"
            :text="groupLine.order_line.amount.toString()"
            :text-size="LINE_TEXT_SIZE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.amount"
            align="center"
            rounded="4"
        />

        <!-- __ Трудозатраты -->
        <AppLabelMultiLineTS
            :height="lineHeight"
            :text="time"
            :text-size="LINE_TEXT_SIZE"
            :type="time === '00с' ? 'danger' : groupLine.order_line_attr.render_type"
            :width="fieldWidths.time"
            align="center"
            rounded="4"
        />

        <!-- __ Сами детальки -->
        <div v-for="(detail, index) in groupLine.materials_array" :key="index">
            <template v-if="detail">
                <AppLabelMultiLineTS
                    :height="lineHeight"
                    :text="groupLine.materials_attr[index]?.title"
                    :text-size="MATERIAL_TEXT_SIZE"
                    :type="groupLine.materials_attr[index]?.render_type"
                    :width="fieldWidths.material"
                    align="center"
                    rounded="4"
                />
            </template>
            <template v-else>
                <AppLabelTS
                    :height="lineHeight"
                    :text-size="LINE_TEXT_SIZE"
                    :width="fieldWidths.material"
                    align="center"
                    rounded="4"
                    text=""
                    type="light"
                />
            </template>
        </div>

        <!-- __ finished_at -->
        <AppLabelTS
            :height="lineHeight"
            :text-size="LINE_TEXT_SIZE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.timeLabel"
            align="center"
            class="truncate"
            rounded="4"
            :text="
                groupLine.order_line_attr.finished_at ?
                    formatTimeInFullFormat(groupLine.order_line_attr.finished_at) :
                    groupLine.order_line_attr.false_at ?
                        formatTimeInFullFormat(groupLine.order_line_attr.false_at) :
                        ''
            "
        />

        <!-- __ Причина не выполнения -->
        <AppLabelTS
            :height="lineHeight"
            :text-size="LINE_TEXT_SIZE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.false_reason"
            align="left"
            class="truncate"
            rounded="4"
            text=""
        />


        <!-- __ Производственная Линия -->
        <!--<AppLabelTS-->
        <!--    :height="lineHeight"-->
        <!--    :text="groupLine.manuf_line"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="getCheckType(groupLine)"-->
        <!--    :width="fieldWidths.manuf_line"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--/>-->

        <!-- __ КДБ -->
        <!--<AppLabelTS-->
        <!--    :class="kdbId ? 'cursor-pointer' : ''"-->
        <!--    :height="lineHeight"-->
        <!--    :text="kdb"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="kdbId ? 'indigo' : 'dark'"-->
        <!--    :width="fieldWidths.kdb"-->
        <!--    align="center"-->
        <!--    rounded="4"-->
        <!--    @click="showDoc"-->
        <!--/>-->

        <!--&lt;!&ndash; __ finished_at &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :height="lineHeight"-->
        <!--    :text="-->
        <!--        groupLine.finished_at ?-->
        <!--            formatTimeInFullFormat(groupLine.finished_at) :-->
        <!--            groupLine.false_at ?-->
        <!--                formatTimeInFullFormat(groupLine.false_at) :-->
        <!--                ''-->
        <!--    "-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="getCheckType(groupLine)"-->
        <!--    :width="fieldWidths.timeLabel"-->
        <!--    align="center"-->
        <!--    class="truncate"-->
        <!--    rounded="4"-->
        <!--/>-->

        <!-- __ Описание -->
        <!--<AppLabelTS-->
        <!--    :height="lineHeight"-->
        <!--    :text="groupLine.description ?? ''"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="groupLine.description ? 'warning' : getCheckType(groupLine)"-->
        <!--    :width="fieldWidths.description"-->
        <!--    align="left"-->
        <!--    class="truncate cursor-pointer"-->
        <!--    rounded="4"-->
        <!--    title="Double Click - Изменить Комментарий"-->
        <!--    @dblclick="changeDescription"-->
        <!--/>-->

        <!-- __ Заявка -->
        <!--<AppLabelTS-->
        <!--    :height="lineHeight"-->
        <!--    :text="groupLine.groupAttr"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="getCheckType(groupLine)"-->
        <!--    :width="fieldWidths.order"-->
        <!--    align="left"-->
        <!--    rounded="4"-->
        <!--/>-->

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

import type { IMatrixManufactureGroupLine } from '@/types'

import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'
import { getCheckClass, getOrderLineSize } from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import CommentEdit from '@/components/dashboard/manufacture/cells/blocks/common/CommentEdit.vue'

interface IProps {
    groupLine: IMatrixManufactureGroupLine
    fieldWidths: Record<string, string>
    index?: number
    ordering?: 'index' | 'position'
}

const props = withDefaults(defineProps<IProps>(), {
    index   : 0,
    ordering: 'position'
})


// const emits = defineEmits<{
//     (e: 'showDocument', payload: number): void
//     (e: 'changeDescription', payload: string): void
// }>()

// const LINE_HEIGHT    = 'h-[25px]'
// const LINE_TYPE          = 'dark'
const LINE_TEXT_SIZE     = 'mini'
const MATERIAL_TEXT_SIZE = 'micro'


// __ Получаем символ завершенности
const checkSymbol = computed(() => {
    if (props.groupLine.order_line_attr.total === props.groupLine.order_line_attr.done) {
        return '✓'
    }
    if (props.groupLine.order_line_attr.total === props.groupLine.order_line_attr.incomplete) {
        return '✘'
    }
    return ''
})

// __ Возвращаем высоту строки в зависимости от количества ПС
const lineHeight = computed(() => 'h-[30px]')

// __ Получаем трудозатраты
const time = computed(() => '00с')
// const time = computed(() => getTimeString(props.groupLine, true).replaceAll('.', ''))

// __ Тип для модального окна изменения Комментария
const comment     = ref('')
const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null)

// __ Меняем Комментарий
// const changeDescription = async () => {
//     comment.value = props.groupLine.description ?? '' // __ Устанавливаем комментарий
//
//     const answer = await commentEdit.value!.show()
//     if (answer) {
//         const newComment = commentEdit.value!.comment.trim()
//         emits('changeDescription', newComment)
//     }
// }

</script>

<style scoped>

</style>
