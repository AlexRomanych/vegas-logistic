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

            <!-- __ Удаление/Создание СЗ -->
            <AppLabelMultiLineTS
                :text="actionText"
                :type="actionType"
                :width="BUTTON_WIDTH"
                align="center"
                height-limit="h-[50px]"
                rounded="4"
                text-size="mini"
                @click="actionTask"
            />

            <!-- __ Добавление СЗ -->
            <AppLabelMultiLineTS
                :text="['Добавить', 'сменное задание']"
                :width="BUTTON_WIDTH"
                align="center"
                height-limit="h-[50px]"
                rounded="4"
                text-size="mini"
                type="success"
                @click="addTask"
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
                    :with-deleting="canEditBlocksPermissionsRights"
                    @delete-task="deleteTask(blockTask)"
                    @add-block-line="addBlockLine(blockTask, $event)"
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

    <!-- __ Модальное окно для Добавления СЗ -->
    <AddBlocksTaskAsync
        ref="addBlocksTaskAsync"
        :max-date="order.load_at || new Date()"
    />

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        align="center"
        ok-word="Понятно"
    />

</template>

<script lang="ts" setup>
import { onMounted, ref, computed } from 'vue'

import type {
    IColorTypes,
    IRenderOrder,
    IBlockTask,
    IRenderOrderLine, IBlockTaskChangeKeys, IBlock,
} from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'
import { useUserStore } from '@/stores/UserStore'
// import { useOrdersStore } from '@/stores/OrdersStore.ts'

import { RENDER_ORDER_LINE_MODEL_DRAFT } from '@/app/constants/orders.ts'

import { isTaskStatusCreated } from '@/app/helpers/manufacture/helpers_blocks.ts'

import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { useLoading } from 'vue-loading-overlay'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
// import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

import ExecuteTaskHeader
    from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteTaskHeader.vue'
import ExecuteTask
    from '@/components/dashboard/manufacture/cells/blocks/blocks_execute/ExecuteTask.vue'
import OrderLines from '@/components/dashboard/orders/order_components/order_render/OrderLines.vue'
import AddBlocksTaskAsync from '@/components/dashboard/orders/order_components/order_card/tasks/AddBlocksTaskAsync.vue'



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
const userStore   = useUserStore()
// const ordersStore  = useOrdersStore()

const canEditBlocksPermissionsRights = computed(() => {
    return userStore.canEditBlocksPermissionsRole()
})

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
const canDelete  = computed(() => blockTasks.value?.length !== 0 && blockTasks.value.every(task => isTaskStatusCreated(task)))
const actionText = computed(() => blockTasks.value?.length !== 0 ? ['Удалить', 'сменное задание'] : ['Создать', 'сменное задание'])
const actionType = computed(() => blockTasks.value?.length !== 0 ? canDelete.value ? 'danger' : 'dark' : 'success')

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
// const PROGRESS_WIDTH  = 'w-[264px]'

const blockTaskFieldsWidth = {
    collapsed    : COLLAPSED_WIDTH,
    id           : 'w-[30px]',
    position     : 'w-[30px]',
    client       : 'w-[190px]',
    order_no     : 'w-[50px]',
    status       : 'w-[140px]',
    progressTotal: 'w-[308px]',
    load_at      : 'w-[228px]',
    comment      : 'w-[738px]',
}

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

// __ Удаляем/Создаем СЗ
const actionTask = async () => {

    let result
    if (blockTasks.value?.length !== 0) {

        if (!canDelete.value) {
            return
        }

        // __ Удаляем СЗ
        modalInfoType.value = 'danger'
        modalInfoMode.value = 'confirm'
        modalInfoText.value = [
            'Сменное задание будет удалено.',
            'Продолжить?',
        ]

        const answer = await appModalAsyncMultilineTS.value!.show()
        if (!answer) {
            return
        }

        result = await blocksStore.deleteBlockTasksByOrderId(props.id)

        blockTasks.value = []

    } else {

        // __ Создаем СЗ
        modalInfoType.value = 'primary'
        modalInfoMode.value = 'confirm'
        modalInfoText.value = [
            'Сменное задание будет создано.',
            'Продолжить?',
        ]

        const answer = await appModalAsyncMultilineTS.value!.show()
        if (!answer) {
            return
        }

        result = await blocksStore.createBlockTasksByOrderId(props.id)
        await getTasks()

    }

    if (checkCRUD(result)) {
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'
        modalInfoText.value = [result.payload]
        await appModalAsyncMultilineTS.value!.show()
    } else {
        await showError()
    }
}


// __ Удаляем конкретное СЗ
const deleteTask = async (blockTask: IBlockTask) => {

    // __ Удаляем СЗ
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'confirm'
    modalInfoText.value = [
        'Сменное задание будет удалено.',
        'Продолжить?',
    ]

    const answer = await appModalAsyncMultilineTS.value!.show()
    if (!answer) {
        return
    }

    const result = await blocksStore.deleteBlockTask(blockTask.id)

    if (checkCRUD(result)) {
        blockTasks.value = blockTasks.value.filter(task => task.id !== blockTask.id)

        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'
        modalInfoText.value = [result.payload]
        await appModalAsyncMultilineTS.value!.show()
    } else {
        await showError()
    }

    //
    // await getTasks()
    // console.log(blockTask)
}



// __ Тип для модального окна Добавления СЗ
const addBlocksTaskAsync = ref<InstanceType<typeof AddBlocksTaskAsync> | null>(null)

// __ Добавляем СЗ
const addTask = async () => {
    const answer = await addBlocksTaskAsync.value!.show()
    if (answer) {
        const taskData: {action_at: string, change: IBlockTaskChangeKeys, comment: string | null} = addBlocksTaskAsync.value!.taskData
        // console.log('taskData: ', taskData)
        const result = await blocksStore.addBlockTasksByOrderId(props.order.id, taskData.action_at, taskData.change, taskData.comment)

        if (checkCRUD(result)) {
            modalInfoType.value = 'success'
            modalInfoMode.value = 'inform'
            modalInfoText.value = ['Сменное Задание', 'успешно добавлено']
            await appModalAsyncMultilineTS.value!.show()

            // result.collapsed = true
            blockTasks.value.push(result)
            // console.log('result: ', result)
            // await getTasks()
        } else {
            await showError()
        }
    }
}

// __ Добавляем Строку СЗ
const addBlockLine = async (blockTask: IBlockTask, lineData: {block: IBlock, amount: number}) => {
    const result = await blocksStore.addBlockTaskLine(blockTask.id, lineData.block.code_1c, lineData.amount)
    if (checkCRUD(result)) {
        blockTask.collapsed = false
        blockTask.block_lines.push(result)
        // console.log('result: ', result)
        // await getTasks()
    } else {
        await showError()
    }
    // console.log('result: ', result)
}

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
