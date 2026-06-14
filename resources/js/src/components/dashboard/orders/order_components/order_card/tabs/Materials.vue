<template>
    <div v-if="!isLoading" class="my-2">
        <div class="m-2 h-[calc(100vh-200px)] flex flex-col overflow-hidden">
            <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar">

                <OrderLines
                    :order-lines="orderRender ? orderRender.lines : []"
                    :show-collapsed="false"
                    :show-materials="true"
                />

            </div>
        </div>
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

import type { IColorTypes, IRenderOrder, ISewingTask } from '@/types'

import { useOrdersStore } from '@/stores/OrdersStore.ts'

import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { useLoading } from 'vue-loading-overlay'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import OrderLines from '@/components/dashboard/orders/order_components/order_render/OrderLines.vue'

// import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    order: IRenderOrder
    id: number
}

const props = defineProps<IProps>()

const ordersStore = useOrdersStore()

const DEBUG     = true
const isLoading = ref(false)


// __ Объявляем переменные
const sewingTasks = ref<ISewingTask[]>([])
const orderRender = ref<IRenderOrder | null>(null)

// __ Вычисляемые свойства
const actionText = computed(() => sewingTasks.value.length !== 0 ? 'Удалить сменное задание' : 'Создать сменное задание')
const actionType = computed(() => sewingTasks.value.length !== 0 ? 'danger' : 'success')


// __ Ширина полей для вывода СЗ
const COLLAPSED_WIDTH = 'w-[30px]'
const PROGRESS_WIDTH  = 'w-[266px]'

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
const getOrdersWithMaterials = async () => {
    const loadedOrders = await ordersStore.getOrdersWithMaterials([406, 414])
    // const loadedOrders = await ordersStore.getOrdersWithMaterials([props.id])
    orderRender.value  = loadedOrders[0] ?? []
    if (orderRender.value) {
        orderRender.value.lines = orderRender.value.lines.map(line => {
            return {
                ...line,
                collapsed_materials: true,
                materials: line.materials?.toSorted((a, b) => a.name.localeCompare(b.name))
            }
        })
    }

}

// const getTasks = async () => {
//     return
//     const tasks: ISewingTask[] = await sewingStore.getSewingTasksByOrderId(props.id)
//     sewingTasks.value          = tasks
//         .map(task => ({ ...task, collapsed: true }))
// }


// __ Удаляем/добавляем СЗ
const actionTask = async () => {

    let result
    if (sewingTasks.value.length !== 0) {

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

        // result = await sewingStore.deleteSewingTasksByOrderId(props.id)

        sewingTasks.value = []

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

        // result = await sewingStore.addSewingTasksByOrderId(props.id)
        // await getTasks()

    }

    if (checkCRUD(result)) {
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'
        // modalInfoText.value = [result.payload]
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

            // await getTasks()
            // if (DEBUG) console.log('sewingTasks: ', sewingTasks.value)

            await getOrdersWithMaterials()
            if (DEBUG) console.log('orderRender: ', orderRender.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})


</script>

<style scoped>

</style>
