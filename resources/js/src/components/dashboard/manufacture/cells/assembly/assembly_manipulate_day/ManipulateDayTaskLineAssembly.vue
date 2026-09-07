<template>

    <div class="flex items-center">

        <template v-if="showDetails">
            <!-- __ Collapsed -->
            <div class="ml-[-2px] mr-[2px]">
                <AppLabelTS
                    :text="assemblyLine.sectorCollapsed ? '▲' : '▼'"
                    align="center"
                    class="cursor-pointer"
                    rounded="4"
                    text-size="micro"
                    type="warning"
                    width="w-[30px]"
                    @click="emits('toggleSectors')"
                />
            </div>
        </template>
        <template v-else>
            <!-- __ Plug -->
            <div class="ml-[-2px] mr-[2px]">
                <AppLabelTS
                    rounded="4"
                    text=""
                    type="light"
                    width="w-[30px]"
                />
            </div>
        </template>


        <div
            :class="[checkClass]"
            class="w-[30px] h-[30px] rounded flex items-center justify-center transition-all mr-[2px]"
        >
            <span :class="checkClass" class="text-[12px] font-semibold text-white">
                {{ checkSymbol }}
            </span>
        </div>

        <!-- __ Позиция -->
        <AppLabelTS
            :height="lineHeight"
            :text="ordering === 'position' ? assemblyLine.order_line.id.toString() : index.toString()"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="checkType"
            :width="fieldWidths.position"
            align="center"
            rounded="4"
            @dblclick.exact="showLineMenu(assemblyLine)"
            @click.ctrl="showLineInfo(assemblyLine.order_line)"
            @click.alt="showSpecification(assemblyLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(assemblyLine.order_line)"
        />

        <!-- __ Размер -->
        <AppLabelTS
            :height="lineHeight"
            :text="getOrderLineSize(assemblyLine.order_line)"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="checkType"
            :width="fieldWidths.size"
            align="center"
            rounded="4"
            @dblclick.exact="showLineMenu(assemblyLine)"
            @click.ctrl="showLineInfo(assemblyLine.order_line)"
            @click.alt="showSpecification(assemblyLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(assemblyLine.order_line)"
        />

        <!-- __ Название Модели -->
        <AppLabelTS
            :height="lineHeight"
            :text="assemblyLine.order_line.model.name_report"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="checkType"
            :width="fieldWidths.name"
            rounded="4"
            @dblclick.exact="showLineMenu(assemblyLine)"
            @click.ctrl="showLineInfo(assemblyLine.order_line)"
            @click.alt="showSpecification(assemblyLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(assemblyLine.order_line)"
        />

        <!-- __ Количество -->
        <AppLabelTS
            :height="lineHeight"
            :text="assemblyLine.amount.toString()"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="checkType"
            :width="fieldWidths.amount"
            align="center"
            rounded="4"
            @dblclick.exact="showLineMenu(assemblyLine)"
            @click.ctrl="showLineInfo(assemblyLine.order_line)"
            @click.alt="showSpecification(assemblyLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(assemblyLine.order_line)"
        />

        <!-- __ Трудозатраты -->
        <AppLabelMultiLineTS
            :height="lineHeight"
            :text="time"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="time === '00с' ? 'danger' : checkType"
            :width="fieldWidths.time"
            align="center"
            rounded="4"
            @dblclick.exact="showLineMenu(assemblyLine)"
            @click.ctrl="showLineInfo(assemblyLine.order_line)"
            @click.alt="showSpecification(assemblyLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(assemblyLine.order_line)"
        />

        <!-- __ Сборочная Линия -->
        <AppLabelMultiLineTS
            :height="lineHeight"
            :text="assemblyLineTitle"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="checkType"
            :width="fieldWidths.manuf_line"
            align="center"
            rounded="4"
            @dblclick.exact="showLineMenu(assemblyLine)"
            @click.ctrl="showLineInfo(assemblyLine.order_line)"
            @click.alt="showSpecification(assemblyLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(assemblyLine.order_line)"
        />

        <!-- __ finished_at -->
        <AppLabelTS
            :height="lineHeight"
            :text="
                assemblyLine.finished_at ?
                    formatTimeInFullFormat(assemblyLine.finished_at) :
                    assemblyLine.false_at ?
                        formatTimeInFullFormat(assemblyLine.false_at) :
                        ''
            "
            :text-size="LINE_TEXT_SIZE"
            :type="checkType"
            :width="fieldWidths.timeLabel"
            align="center"
            class="truncate"
            rounded="4"
        />

        <!-- __ Причина не выполнения -->
        <AppLabelTS
            :height="lineHeight"
            :text="assemblyLine.false_reason ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="checkType"
            :width="fieldWidths.false_reason"
            align="left"
            class="truncate"
            rounded="4"
        />

        <!-- __ Описание -->
        <AppLabelTS
            :height="lineHeight"
            :text="assemblyLine.description ?? ''"
            :text-size="LINE_TEXT_SIZE"
            :type="assemblyLine.description ? 'warning' : checkType"
            :width="fieldWidths.description"
            align="left"
            class="truncate cursor-pointer"
            rounded="4"
            title="Double Click - Изменить Комментарий"
            @dblclick="changeDescription"
        />

        <!-- __ Название Заявки -->
        <AppLabelMultiLineTS
            v-if="showOrderTitle"
            :height="lineHeight"
            :text="assemblyLine.order_line.order_title!"
            :text-size="LINE_TEXT_SIZE"
            :title="TITLE"
            :type="checkType"
            :width="fieldWidths.order"
            align="center"
            rounded="4"
            @dblclick.exact="showLineMenu(assemblyLine)"
            @click.ctrl="showLineInfo(assemblyLine.order_line)"
            @click.alt="showSpecification(assemblyLine.order_line.construct_code_1c)"
            @click.shift="showModelCard(assemblyLine.order_line)"
        />

    </div>

    <!-- __ Участки -->
    <template v-if="showDetails">
        <template v-if="!assemblyLine.sectorCollapsed">
            <template v-for="sector of assemblyLine.sector_lines" :key="sector.id">
                <ManipulateDayTaskLineAssemblySector
                    :field-widths="fieldWidths"
                    :sector="sector"
                />
            </template>
        </template>
    </template>

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

import type {
    IAssemblySector,
    IAssemblySectorKeys,
    IAssemblyTaskLine,
    IAssemblyTaskOrderLine,
    IColorTypes,
    IModalAsyncMenu,
    IModelConstruct
} from '@/types'

import { useModelsStore } from '@/stores/ModelsStore.ts'

import { formatTimeInFullFormat } from '@/app/helpers/helpers_date'
import { getOrderLineSize, getSectorByName, isCommon, isTaskLineDone, isTaskLineFalse } from '@/app/helpers/manufacture/helpers_assembly.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppModalMenuTS from '@/components/ui/modals/AppModalAsyncMenuTS.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'

import CommentEdit from '@/components/dashboard/manufacture/cells/blocks/common/CommentEdit.vue'
import OrderItemInfo from '@/components/dashboard/manufacture/cells/assembly/common/OrderItemInfo.vue'
import CardSpecification from '@/components/dashboard/models/components/CardSpecification.vue'
import ManipulateDayTaskLineAssemblySector
    from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulateDayTaskLineAssemblySector.vue'


interface IProps {
    assemblyLine: IAssemblyTaskLine
    fieldWidths: Record<string, string>
    index?: number
    ordering?: 'index' | 'position',
    showOrderTitle?: boolean
    sector: IAssemblySector
    showDetails?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    index         : 0,
    ordering      : 'position',
    showOrderTitle: true,
    showDetails   : false
})

const emits = defineEmits<{
    (e: 'changeDescription', payload: string): void
    (e: 'toggleSectors'): void
    // (e: 'showDocument', payload: number): void
}>()

const modelsStore = useModelsStore()

const LINE_TEXT_SIZE = 'mini'

const TITLE = 'Ctrl + Click - Инфо о Заявке,\nShift + Click - Карточка Модели,\nAlt + Click - Спецификация'

// __ Получаем Линию Сборки
const assemblyLineTitle = computed(() => {
    const sector = getSectorByName(props.assemblyLine.assembly_line as IAssemblySectorKeys)
    return sector?.TITLE ?? ''
})

// __ Получаем символ завершенности
const checkSymbol = computed(() => {
    if (isTaskLineDone(props.assemblyLine)) {
        return '✓'
    }
    if (isTaskLineFalse(props.assemblyLine)) {
        return '✘'
    }
    return ''
})

// __ Получаем класс завершенности
const checkClass = computed(() => {
    if (isTaskLineDone(props.assemblyLine)) {
        return 'bg-green-500'
    }
    if (isTaskLineFalse(props.assemblyLine)) {
        return 'bg-red-500'
    }
    return 'bg-slate-400'
})

// __ Получаем тип завершенности
const checkType = computed(() => {
    if (isTaskLineDone(props.assemblyLine)) {
        return 'success'
    }
    if (isTaskLineFalse(props.assemblyLine)) {
        return 'danger'
    }
    return 'dark'
})

// __ Возвращаем высоту строки в зависимости от количества ПС
const lineHeight = computed(() => 'h-[30px]')

// __ Получаем трудозатраты
const time = computed(() => '00с')
// const time = computed(() => getTimeString(props.assemblyLine, true).replaceAll('.', ''))


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
// !!! ---                Комментарий                    !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Тип для модального окна изменения Комментария
const comment     = ref('')
const commentEdit = ref<InstanceType<typeof CommentEdit> | null>(null)

// __ Меняем Комментарий
const changeDescription = async () => {
    comment.value = props.assemblyLine.description ?? '' // __ Устанавливаем комментарий

    const answer = await commentEdit.value!.show()
    if (answer) {
        const newComment = commentEdit.value!.comment.trim()
        emits('changeDescription', newComment)
    }
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                   Меню                        !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// __ Тип для модального Меню
const modalMenuType  = ref<IColorTypes>('primary')
const modalMenu      = ref<IModalAsyncMenu>({ data: [] })
const appModalMenuTS = ref<InstanceType<typeof AppModalMenuTS> | null>(null)

// __ Показываем меню по двойному клику
const showLineMenu = async (assemblyLine: IAssemblyTaskLine) => {

    // __ Показываем модальное меню и обрабатываем результаты
    const CANCEL_ID = 10

    modalMenuType.value = 'primary'
    modalMenu.value     = {
        data: [
            { id: 1, title: 'Информация о строке Заказа' },
            { id: 2, title: 'Карточка Модели' },
            { id: 3, title: 'Спецификация Модели' },
            { id: 4, title: 'Изменить / Добавить Комментарий' },
            { id: CANCEL_ID, title: 'Отмена' },
        ],
    }

    // __ Показываем модальное меню
    const result = await appModalMenuTS.value!.show()

    // __ 'Отмена'
    if (!result.value || result.menuItem === CANCEL_ID) {
        return
    }

    // __ 'Информация о строке Заказа'
    if (result.menuItem === 1) {
        await showLineInfo(assemblyLine.order_line)
        return
    }

    // __ 'Спецификация Модели'
    if (result.menuItem === 3) {
        await showSpecification(assemblyLine.order_line.construct_code_1c)
        return
    }

    // __ 'Изменить / Добавить Комментарий'
    if (result.menuItem === 4) {
        await changeDescription()
        return
    }
}


</script>

<style scoped>

</style>
<script lang="ts" setup>
</script>
