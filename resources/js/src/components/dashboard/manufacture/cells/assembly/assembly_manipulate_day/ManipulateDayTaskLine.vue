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
            :title="TITLE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.position"
            align="center"
            rounded="4"
            @dblclick.exact="showLineMenu(groupLine.order_line)"
            @click.ctrl="showLineInfo(groupLine.order_line)"
            @click.alt="showSpecification(groupLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(groupLine.order_line)"
        />

        <!-- __ Размер -->
        <AppLabelTS
            :height="lineHeight"
            :text="getOrderLineSize(groupLine.order_line)"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.size"
            align="center"
            rounded="4"
            @dblclick.exact="showLineMenu(groupLine.order_line)"
            @click.ctrl="showLineInfo(groupLine.order_line)"
            @click.alt="showSpecification(groupLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(groupLine.order_line)"
        />

        <!-- __ Название Модели -->
        <AppLabelTS
            :height="lineHeight"
            :text="groupLine.order_line.model.name_report"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.name"
            rounded="4"
            @dblclick.exact="showLineMenu(groupLine.order_line)"
            @click.ctrl="showLineInfo(groupLine.order_line)"
            @click.alt="showSpecification(groupLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(groupLine.order_line)"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :height="lineHeight"
            :text="groupLine.order_line.amount.toString()"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.amount"
            align="center"
            rounded="4"
            @dblclick.exact="showLineMenu(groupLine.order_line)"
            @click.ctrl="showLineInfo(groupLine.order_line)"
            @click.alt="showSpecification(groupLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(groupLine.order_line)"
        />

        <!-- __ Трудозатраты -->
        <AppLabelMultiLineTS
            :height="lineHeight"
            :text="time"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="time === '00с' ? 'danger' : groupLine.order_line_attr.render_type"
            :width="fieldWidths.time"
            align="center"
            rounded="4"
            @dblclick.exact="showLineMenu(groupLine.order_line)"
            @click.ctrl="showLineInfo(groupLine.order_line)"
            @click.alt="showSpecification(groupLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(groupLine.order_line)"
        />

        <!-- __ Название Заявки, показываем только для Объединения -->
        <AppLabelMultiLineTS
            v-if="showOrderTitle"
            :height="lineHeight"
            :text="groupLine.order_line.order_title!"
            :title="TITLE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.order_title"
            align="center"
            rounded="4"
            text-size="micro"
            @dblclick.exact="showLineMenu(groupLine.order_line)"
            @click.ctrl="showLineInfo(groupLine.order_line)"
            @click.alt="showSpecification(groupLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(groupLine.order_line)"
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
            :text="
                groupLine.order_line_attr.finished_at ?
                    formatTimeInFullFormat(groupLine.order_line_attr.finished_at) :
                    groupLine.order_line_attr.false_at ?
                        formatTimeInFullFormat(groupLine.order_line_attr.false_at) :
                        ''
            "
            :text-size="LINE_TEXT_SIZE"
            :type="groupLine.order_line_attr.render_type"
            :width="fieldWidths.timeLabel"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!--&lt;!&ndash; __ Причина не выполнения &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :height="lineHeight"-->
        <!--    :text-size="LINE_TEXT_SIZE"-->
        <!--    :type="groupLine.order_line_attr.render_type"-->
        <!--    :width="fieldWidths.false_reason"-->
        <!--    align="left"-->
        <!--    class="truncate"-->
        <!--    rounded="4"-->
        <!--    text=""-->
        <!--/>-->


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

    <!-- __ Модальное Меню -->
    <AppModalMenuTS
        ref="appModalMenuTS"
        :menu="modalMenu"
        :type="modalMenuType"
    />

    <!-- __ Модальное окно для информации о записи -->
    <OrderItemInfo
        ref="orderItemInfo"
        :order-line="orderLine"
    />

    <!-- __ Карточка Спецификации -->
    <CardSpecification
        ref="cardSpecification"
        :construct="modelConstruct"
    />

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />

</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'

import type { IAssemblyTaskOrderLine, IColorTypes, IMatrixManufactureGroupLine, IModalAsyncMenu, IModelConstruct } from '@/types'

import { useModelsStore } from '@/stores/ModelsStore.ts'

import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'
import { getCheckClass, getOrderLineSize } from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppModalMenuTS from '@/components/ui/modals/AppModalAsyncMenuTS.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'

import CommentEdit from '@/components/dashboard/manufacture/cells/blocks/common/CommentEdit.vue'
import OrderItemInfo from '@/components/dashboard/manufacture/cells/assembly/common/OrderItemInfo.vue'
import CardSpecification from '@/components/dashboard/models/components/CardSpecification.vue'


interface IProps {
    groupLine: IMatrixManufactureGroupLine
    fieldWidths: Record<string, string>
    index?: number
    ordering?: 'index' | 'position',
    showOrderTitle?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    index         : 0,
    ordering      : 'position',
    showOrderTitle: false,
})


// const emits = defineEmits<{
//     (e: 'showDocument', payload: number): void
//     (e: 'changeDescription', payload: string): void
// }>()

const modelsStore = useModelsStore()

// const LINE_HEIGHT    = 'h-[25px]'
// const LINE_TYPE          = 'dark'
const LINE_TEXT_SIZE     = 'mini'
const MATERIAL_TEXT_SIZE = 'micro'

// const TITLE = 'Ctrl + Click - Инфо о Заявке, Shift + Click - Карточка Модели, Alt + Click - Спецификация'
const TITLE = `Ctrl + Click - Инфо о Заявке,
Shift + Click - Карточка Модели,
Alt + Click - Спецификация`

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

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

// __ Показываем сообщение об ошибке
async function showError(error: string | string[] | null = null) {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'

    let renderError = ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    if (typeof error === 'string' && error.length > 0) {
        renderError = [error]
    } else if (Array.isArray(error) && error.length > 0) {
        renderError = error
    }

    modalInfoText.value = renderError
    await appModalAsyncMultilineTS.value!.show()
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Спецификации                   !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Карточка Спецификаций
const cardSpecification = ref<InstanceType<typeof CardSpecification> | null>(null)
const modelConstruct    = ref<IModelConstruct | null>(null)

// __ Показываем спецификацию
const showSpecification = async (code_1c: string | null | undefined) => {
    if (!code_1c) {
        return
    }

    const construct = await modelsStore.getConstructByCode1c(code_1c)
    if (!construct) {
        await showError([
            `Спецификация с кодом: ${code_1c}`,
            'не найдена!'
        ])
        return
    }

    modelConstruct.value = construct
    await cardSpecification.value?.show()
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---          Инфа о Строке Заявки                 !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Тип для модального окна информации о записи в Заявке
const orderLine     = ref<IAssemblyTaskOrderLine | null>(null)
const orderItemInfo = ref<InstanceType<typeof OrderItemInfo> | null>(null)

// __ Показать информацию о записи
const showLineInfo = async (inOrderLine: IAssemblyTaskOrderLine) => {
    orderLine.value = inOrderLine
    await orderItemInfo.value!.show()
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---              Карточка Модели                  !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Тип для Карточки Модели

// __ Показать информацию о записи
const showModelCard = (orderLine: IAssemblyTaskOrderLine) => {
    console.log('showModelCard: ', orderLine)
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                   Меню                        !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Тип для модального Меню
const modalMenuType  = ref<IColorTypes>('primary')
const modalMenu      = ref<IModalAsyncMenu>({ data: [] })
const appModalMenuTS = ref<InstanceType<typeof AppModalMenuTS> | null>(null)

// __ Показываем меню по двойному клику
const showLineMenu = async (orderLine: IAssemblyTaskOrderLine) => {

    // __ Показываем модальное меню и обрабатываем результаты
    const CANCEL_ID = 10

    modalMenuType.value = 'primary'
    modalMenu.value     = {
        data: [
            { id: 1, title: 'Информация о строке Заказа' },
            { id: 2, title: 'Карточка Модели' },
            { id: 3, title: 'Спецификация Модели' },
            { id: CANCEL_ID, title: 'Отмена' },
        ],
    }

    // let result = { menuItem: CANCEL_ID, value: false } as IModalResponse

    // __ Показываем модальное меню
    const result = await appModalMenuTS.value!.show()

    // __ 'Отмена'
    if (!result.value || result.menuItem === CANCEL_ID) {
        return
    }

    // __ 'Информация о строке Заказа'
    if (result.menuItem === 1) {
        await showLineInfo(orderLine)
        return
    }

    // __ 'Спецификация Модели'
    if (result.menuItem === 3) {
        await showSpecification(orderLine.construct_code_1c)
        return
    }
}


</script>

<style scoped>

</style>
