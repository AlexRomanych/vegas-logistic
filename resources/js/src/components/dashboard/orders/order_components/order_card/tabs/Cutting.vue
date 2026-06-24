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
                    align="center"
                    class="start-group cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    width="w-[250px]"
                    @click="activeTabPosition = tab.position"
                />
            </div>

            <!-- __ Удаление/добавление СЗ -->
            <AppLabelTS
                :text="actionText"
                :type="actionType"
                align="center"
                height="h-[50px]"
                rounded="4"
                text-size="mini"
                width="w-[250px]"
                @click="actionTask"
            />
        </div>

        <template v-if="activeTabPosition === 1">
            <!-- __ Шапка СЗ -->
            <ExecuteTaskHeader
                :client-show="false"
                :fields-width="cuttingTaskFieldsWidth"
                :order-info="false"
            />

            <!-- __ Сами СЗ -->
            <div v-for="cuttingTask of cuttingTasks" :key="cuttingTask.id">
                <ExecuteTask
                    :client-show="false"
                    :cutting-task="cuttingTask"
                    :fields-width="cuttingTaskFieldsWidth"
                    :order-info="false"
                />
            </div>
        </template>
        <template v-else-if="activeTabPosition === 2">
            <OrderLines
                :order-lines="orderWithCuttingTask ? orderWithCuttingTask.lines : []"
                :show-collapsed="false"
                :show-cutting-details="true"
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

import type { IColorTypes, IRenderOrder, ICuttingTask, IRenderOrderCuttingTask } from '@/types'

import { useCuttingStore } from '@/stores/CuttingStore.ts'
import { useOrdersStore } from '@/stores/OrdersStore.ts'

import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { useLoading } from 'vue-loading-overlay'

import ExecuteTaskHeader
    from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute/ExecuteTaskHeader.vue'
import ExecuteTask
    from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_execute/ExecuteTask.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import OrderLines from '@/components/dashboard/orders/order_components/order_render/OrderLines.vue'
import { DETAILS } from '@/app/constants/cutting.ts'


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

const cuttingStore = useCuttingStore()
const ordersStore  = useOrdersStore()

const DEBUG     = true
const isLoading = ref(false)


// __ Объявляем переменные
const cuttingTasks         = ref<ICuttingTask[]>([])
const orderWithCuttingTask = ref<IRenderOrderCuttingTask | null>(null)

// __ Вычисляемые свойства
const actionText = computed(() => cuttingTasks.value?.length !== 0 ? 'Удалить сменное задание' : 'Создать сменное задание')
const actionType = computed(() => cuttingTasks.value?.length !== 0 ? 'danger' : 'success')

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

const cuttingTaskFieldsWidth = {
    collapsed    : COLLAPSED_WIDTH,
    id           : 'w-[30px]',
    position     : 'w-[30px]',
    client       : 'w-[190px]',
    order_no     : 'w-[50px]',
    status       : 'w-[140px]',
    progressTotal: PROGRESS_WIDTH,
    load_at      : 'w-[228px]',
    comment      : 'w-[1059px]',
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


// __ Получаем СЗ с сервера
const getTasks = async () => {
    const [tasks, orderWithTask]: [ICuttingTask[], IRenderOrderCuttingTask] = await Promise.all([
        cuttingStore.getCuttingTasksByOrderId(props.id),
        ordersStore.getOrdersWithCuttingTaskLines(props.id)
    ])

    // const tasks: ICuttingTask[]                    = await cuttingStore.getCuttingTasksByOrderId(props.id)
    // const orderWithTask: IRenderOrderCuttingTask[] = await cuttingStore.getCuttingTasksByOrderId(props.id)

    cuttingTasks.value = tasks.map(task => ({ ...task, collapsed: true}))

    const ORDER_OF_DETAILS = [
        DETAILS.PANEL.NAME,      // 'panel'
        DETAILS.PANEL_UP.NAME,   // 'panel_up'
        DETAILS.SIDE.NAME,        // 'side'
        DETAILS.PANEL_DOWN.NAME // 'panel_down'
    ]

    if (orderWithTask) {
        orderWithCuttingTask.value = {
            ...orderWithTask,
            lines: orderWithTask.lines.map(line => {
                return {
                    ...line,
                    collapsed_cutting_details: true,
                    cutting_lines            : line.cutting_lines?.toSorted((a, b) => {
                        // 2. Находим индексы текущих элементов в нашем эталоне
                        const indexA = ORDER_OF_DETAILS.indexOf(a.detail)
                        const indexB = ORDER_OF_DETAILS.indexOf(b.detail)

                        // 3. Сравниваем индексы (числа) обычным вычитанием
                        return indexA - indexB
                    })
                }
            })
        }
    }
}

// __ Удаляем/добавляем СЗ
const actionTask = async () => {

    let result
    if (cuttingTasks.value?.length !== 0) {

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

        result = await cuttingStore.deleteCuttingTasksByOrderId(props.id)

        cuttingTasks.value = []

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

        result = await cuttingStore.addCuttingTasksByOrderId(props.id)
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


onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getTasks()
            if (DEBUG) console.log('cuttingTask: ', cuttingTasks.value)

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
