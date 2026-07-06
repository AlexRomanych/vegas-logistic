<template>
    <div v-if="!isLoading" class="my-2">

        <div class="flex mb-4 uppercase cursor-pointer">
            <!-- __ Табы -->
            <div
                v-for="tab of tabs"
                :key="tab.position"
            >
                <!-- __ Таб: TODO: !!! Доделать крестики и галочки на выполненных задачах !!!   -->
                <AppLabelMultiLineTS
                    v-if="tab.show"
                    :text="tab.label"
                    :type="activeTabPosition === tab.position ? tab.typeActive : tab.type"
                    :width="BUTTON_WIDTH"
                    align="center"
                    class="start-group cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    @click="activeTabPosition = tab.position"
                />
            </div>

            <!-- __ Удаление/добавление СЗ -->
            <AppLabelTS
                :text="actionText"
                :type="actionType"
                :width="BUTTON_WIDTH"
                align="center"
                height="h-[50px]"
                rounded="4"
                text-size="mini"
                @click="actionTask"
            />

        </div>

        <template v-if="activeTabPosition === 1">
            <!-- __ Шапка СЗ -->
            <ExecuteTaskHeader
                :client-show="false"
                :fields-width="blockTaskFieldsWidth"
                :order-info="false"
            />

            <!-- __ Сами СЗ -->
            <div v-for="blockTask of blockTasks" :key="blockTask.id">
                <ExecuteTask
                    :block-task="blockTask"
                    :client-show="false"
                    :fields-width="blockTaskFieldsWidth"
                    :order-info="false"
                />
            </div>
        </template>
        <template v-else-if="activeTabPosition === 2">
            <OrderLines
                :order-lines="orderLines"
                :show-blocks="true"
                :show-collapsed="false"
            />
        </template>

    </div>

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />

</template>

<script lang="ts" setup>
import { onMounted, ref, computed } from 'vue'

import type {
    IColorTypes,
    IRenderOrder,
    IBlockTask,
    IRenderOrderLine,
} from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'
// import { useOrdersStore } from '@/stores/OrdersStore.ts'

import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { useLoading } from 'vue-loading-overlay'

import { RENDER_ORDER_LINE_MODEL_DRAFT } from '@/app/constants/orders.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import ExecuteTaskHeader
    from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteTaskHeader.vue'
import ExecuteTask
    from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteTask.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import OrderLines from '@/components/dashboard/orders/order_components/order_render/OrderLines.vue'


interface IProps {
    order: IRenderOrder
    id: number
}

interface ITab {
    show: boolean
    label: string[]
    position: number
    type: IColorTypes
    typeActive: IColorTypes
}

const props = defineProps<IProps>()

const blocksStore = useBlocksStore()
// const ordersStore  = useOrdersStore()

const DEBUG     = true
const isLoading = ref(false)

// __ Объявляем константы
const BUTTON_WIDTH = 'w-[200px]'


// __ Объявляем переменные
const blockTasks = ref<IBlockTask[]>([])
const orderLines = ref<IRenderOrderLine[]>([])
// const orderWithBlockTask = ref<IRenderOrderBlockTask | null>(null)

// __ Вычисляемые свойства
// const hasTask    = computed(() => blockTasks.value?.length !== 0)
const actionText = computed(() => blockTasks.value?.length !== 0 ? 'Удалить сменное задание' : 'Создать сменное задание')
const actionType = computed(() => blockTasks.value?.length !== 0 ? 'danger' : 'success')

// __ Табы
const tabs              = ref<ITab[]>([])
const activeTabPosition = ref(1)

const setTabs = () => {
    tabs.value = []
    tabs.value.push({
        show      : true,
        label     : ['Сменное', 'задание'],
        position  : 1,
        type      : 'stone',
        typeActive: 'primary',
    })
    tabs.value.push({
        show      : true,
        label     : ['Содержимое', 'сменного задания'],
        position  : 2,
        type      : 'dark',
        typeActive: 'primary',
    })
}


// __ Ширина полей для вывода СЗ
const COLLAPSED_WIDTH = 'w-[30px]'
const PROGRESS_WIDTH  = 'w-[264px]'

const blockTaskFieldsWidth = {
    collapsed    : COLLAPSED_WIDTH,
    id           : 'w-[30px]',
    position     : 'w-[30px]',
    client       : 'w-[190px]',
    order_no     : 'w-[50px]',
    status       : 'w-[140px]',
    progressTotal: PROGRESS_WIDTH,
    load_at      : 'w-[228px]',
    comment      : 'w-[334px]',
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

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
    await appModalAsyncMultiline.value!.show()
}


// __ Формируем список OrderLines, для которых были созданы СЗ Блоков
const getOrderLines = () => {
    const orderLinesMap = new Map<number, IRenderOrderLine>()
    blockTasks.value.forEach(task => {
        task.block_lines.forEach(blockLine => {
            blockLine.order_lines.forEach(orderLine => {

                const existingOrderLine = orderLinesMap.get(orderLine.id)

                if (existingOrderLine) {
                    if (!existingOrderLine.block_lines) {
                        existingOrderLine.block_lines = []
                    }

                    // __ Убираем дубликаты блоков
                    const existingBlock = existingOrderLine.block_lines.find(block => block.block_code_1c === blockLine.block.code_1c)
                    if (!existingBlock) {
                        existingOrderLine.block_lines!.push(
                            {
                                block_code_1c: blockLine.block.code_1c,
                                block_name   : blockLine.block.name,
                                amount       : orderLine.amount,
                                manuf_line   : blockLine.manuf_line,
                            }
                        )
                    }

                    orderLinesMap.set(orderLine.id, existingOrderLine)
                } else {
                    orderLinesMap.set(orderLine.id, {
                        size            : `${orderLine.dims.width}x${orderLine.dims.length}x${orderLine.dims.height}`,
                        textile         : orderLine.textile,
                        amount          : orderLine.amount,
                        composition     : orderLine.composition ?? '',
                        describe_1      : orderLine.describe_1 ?? '',
                        describe_2      : orderLine.describe_2 ?? '',
                        describe_3      : orderLine.describe_3 ?? '',
                        id              : orderLine.id,
                        spec_name       : null,
                        spec_name_add   : null,
                        collapsed_blocks: true,
                        model           : {
                            ...RENDER_ORDER_LINE_MODEL_DRAFT,
                            name_report: orderLine.model_name,
                            code_1c    : orderLine.model_code_1c,
                        },
                        block_lines     : [
                            {
                                block_code_1c: blockLine.block.code_1c,
                                block_name   : blockLine.block.name,
                                amount       : orderLine.amount,
                                manuf_line   : blockLine.manuf_line,
                            }
                        ],
                    })
                }
            })
        })
    })

    orderLines.value = Array.from(orderLinesMap.values())
}


// __ Получаем СЗ с сервера
const getTasks = async () => {
    const tasks: IBlockTask[] = await blocksStore.getBlockTasksByOrderId(props.id)

    blockTasks.value = tasks.map(task => {
        return {
            ...task,
            collapsed: true,
        }
    })

    return tasks
}

// __ Удаляем/добавляем СЗ
const actionTask = async () => {

    let result
    if (blockTasks.value?.length !== 0) {

        // __ Удаляем СЗ
        modalInfoType.value = 'danger'
        modalInfoMode.value = 'confirm'
        modalInfoText.value = [
            'Сменное задание будет удалено.',
            'Продолжить?',
        ]

        const answer = await appModalAsyncMultiline.value!.show()
        if (!answer) {
            return
        }

        result = await blocksStore.deleteBlockTasksByOrderId(props.id)

        blockTasks.value = []

    } else {

        // __ Добавляем СЗ
        modalInfoType.value = 'primary'
        modalInfoMode.value = 'confirm'
        modalInfoText.value = [
            'Сменное задание будет добавлено.',
            'Продолжить?',
        ]

        const answer = await appModalAsyncMultiline.value!.show()
        if (!answer) {
            return
        }

        result = await blocksStore.addBlockTasksByOrderId(props.id)
        await getTasks()

    }

    if (checkCRUD(result)) {
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'
        modalInfoText.value = [result.payload]
        await appModalAsyncMultiline.value!.show()
    } else {
        await showError()
    }
}


// __ Пересчет размеров Деталек
// const calculateTaskCut = async () => {
//     if (!hasTask.value) {
//         return
//     }
//
//     // __ Пересчитываем детали Кроя
//     modalInfoType.value = 'primary'
//     modalInfoMode.value = 'confirm'
//     modalInfoText.value = [
//         'Детали Кроя будут пересчитаны.',
//         'Продолжить?',
//     ]
//
//     const answer = await appModalAsyncMultiline.value!.show()
//     if (!answer) {
//         return
//     }
//
//     const result = await blocksStore.calcBlockTasksCutByOrderId(props.id)
//     await getTasks()
//
//
//     if (checkCRUD(result)) {
//         modalInfoType.value = 'success'
//         modalInfoMode.value = 'inform'
//         modalInfoText.value = [result.payload]
//         await appModalAsyncMultiline.value!.show()
//     } else {
//         await showError()
//     }
// }


onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getTasks()
            if (DEBUG) console.log('blockTask: ', blockTasks.value)

            getOrderLines()

            setTabs()
        },
        undefined,
        // false,
    )

    isLoading.value = false
})


</script>

<style scoped>

</style>
